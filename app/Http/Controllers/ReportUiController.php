<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\BpjsKlaimAlokasiKomponen;
use App\Models\BpjsKlaimBulanan;
use App\Models\ClinicDatabaseConnection;
use App\Models\ClinicProfile;
use App\Models\MasterAdministrasiPasien;
use App\Models\MasterKategoriPengeluaran;
use App\Models\MasterKomponenTransaksi;
use App\Models\MasterLayanan;
use App\Models\Pengeluaran;
use App\Models\RekapPasien;
use App\Models\RekapPasienUpdate;
use App\Models\TransaksiPasien;
use App\Models\TransaksiPasienAdministrasi;
use App\Models\TransaksiPasienKomponen;
use App\Models\User;
use App\Services\ClinicDatabaseManager;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class ReportUiController extends Controller
{
    public function dashboard(Request $request): View
    {
        $selectedMonth = now()->startOfMonth();
        $today = now()->startOfDay();
        $loggedUser = $request->user();
        $clinicContext = $this->clinicContext($request, true);
        $selectedClinicId = $clinicContext['selectedClinicId'];
        $dashboardGreeting = $this->dashboardGreeting(
            $loggedUser,
            $clinicContext['selectedClinic'],
            $clinicContext['viewingAllClinics']
        );

        $masterLayanan = MasterLayanan::query()
            ->active()
            ->orderBy('urutan_laporan')
            ->orderBy('nama_layanan')
            ->get();

        $monthTransactionsQuery = TransaksiPasien::query()
            ->with(['masterLayanan', 'clinicProfile'])
            ->whereYear('tanggal', $selectedMonth->year)
            ->whereMonth('tanggal', $selectedMonth->month);
        $this->scopeQueryToClinic($monthTransactionsQuery, $selectedClinicId);
        $monthTransactions = $monthTransactionsQuery->get();

        $monthExpensesQuery = Pengeluaran::query()
            ->with(['masterKategoriPengeluaran', 'clinicProfile'])
            ->forBulan($selectedMonth->month, $selectedMonth->year);
        $this->scopeQueryToClinic($monthExpensesQuery, $selectedClinicId);
        $monthExpenses = $monthExpensesQuery->get();

        $todayTransactions = $monthTransactions->filter(
            fn (TransaksiPasien $transaction) => $transaction->tanggal?->toDateString() === $today->toDateString()
        );

        $todayExpenses = $monthExpenses->filter(
            fn (Pengeluaran $expense) => $expense->tanggal?->toDateString() === $today->toDateString()
        );

        $monthlyRevenue = (float) $monthTransactions->sum('jumlah_rp');
        $monthlyExpenseTotal = (float) $monthExpenses->sum('jumlah_rp');
        $monthlyNet = $monthlyRevenue - $monthlyExpenseTotal;
        $todayRevenue = (float) $todayTransactions->sum('jumlah_rp');
        $todayExpenseTotal = (float) $todayExpenses->sum('jumlah_rp');
        $averageTransaction = $monthTransactions->count() > 0
            ? $monthlyRevenue / $monthTransactions->count()
            : 0.0;

        $unmappedTransactions = $monthTransactions->filter(
            fn (TransaksiPasien $transaction) => ! $transaction->masterLayanan
        )->count();

        $mappedPercentage = $monthTransactions->count() > 0
            ? (($monthTransactions->count() - $unmappedTransactions) / $monthTransactions->count()) * 100
            : 100.0;

        $penjaminMix = $monthTransactions
            ->groupBy(fn (TransaksiPasien $transaction) => filled($transaction->penjamin)
                ? trim((string) $transaction->penjamin)
                : 'Tanpa Penjamin')
            ->map(function (Collection $items, string $label) use ($monthlyRevenue) {
                $amount = (float) $items->sum('jumlah_rp');

                return [
                    'label' => $label,
                    'count' => $items->count(),
                    'amount' => $amount,
                    'share' => $monthlyRevenue > 0 ? ($amount / $monthlyRevenue) * 100 : 0.0,
                ];
            })
            ->sortByDesc('amount')
            ->values()
            ->take(5);

        $topLayanan = $this->monthlyLayananRows($monthTransactions, $masterLayanan)
            ->filter(fn (array $row) => (float) $row['debet'] > 0)
            ->sortByDesc('debet')
            ->values()
            ->take(6)
            ->map(function (array $row, int $index) use ($monthlyRevenue) {
                $palette = ['blue', 'emerald', 'amber', 'sky', 'violet', 'slate'];
                $amount = (float) $row['debet'];

                return [
                    'kode' => $row['kode'],
                    'nama' => $row['keterangan'],
                    'amount' => $amount,
                    'share' => $monthlyRevenue > 0 ? ($amount / $monthlyRevenue) * 100 : 0.0,
                    'tone' => $palette[$index % count($palette)],
                ];
            });

        $monthlyTrend = collect(range(5, 0))
            ->map(function (int $offset) use ($selectedMonth, $selectedClinicId) {
                $month = $selectedMonth->copy()->subMonths($offset);
                $revenueQuery = TransaksiPasien::query()
                    ->whereYear('tanggal', $month->year)
                    ->whereMonth('tanggal', $month->month);
                $this->scopeQueryToClinic($revenueQuery, $selectedClinicId);
                $revenue = (float) $revenueQuery->sum('jumlah_rp');

                $expenseQuery = Pengeluaran::query()
                    ->forBulan($month->month, $month->year);
                $this->scopeQueryToClinic($expenseQuery, $selectedClinicId);
                $expense = (float) $expenseQuery->sum('jumlah_rp');

                return [
                    'label' => strtoupper($month->locale('id')->translatedFormat('M')),
                    'period' => $month->locale('id')->translatedFormat('F Y'),
                    'revenue' => $revenue,
                    'expense' => $expense,
                    'net' => $revenue - $expense,
                ];
            })
            ->values();

        $trendPeak = max(1, (float) $monthlyTrend
            ->flatMap(fn (array $row) => [$row['revenue'], $row['expense']])
            ->max());

        $monthlyTrend = $monthlyTrend->map(function (array $row) use ($trendPeak) {
            $row['revenue_ratio'] = min(100, ($row['revenue'] / $trendPeak) * 100);
            $row['expense_ratio'] = min(100, ($row['expense'] / $trendPeak) * 100);

            return $row;
        });

        $recentTransactionsQuery = TransaksiPasien::query()
            ->with(['masterLayanan', 'clinicProfile'])
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->limit(5);
        $this->scopeQueryToClinic($recentTransactionsQuery, $selectedClinicId);
        $recentTransactions = $recentTransactionsQuery->get();

        $recentExpensesQuery = Pengeluaran::query()
            ->with(['masterKategoriPengeluaran', 'clinicProfile'])
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->limit(5);
        $this->scopeQueryToClinic($recentExpensesQuery, $selectedClinicId);
        $recentExpenses = $recentExpensesQuery->get();

        $operationalRouteParams = $loggedUser?->isMaster() && $selectedClinicId
            ? ['clinic_id' => $selectedClinicId]
            : [];
        $reportRouteParams = $loggedUser?->isMaster()
            ? ($clinicContext['viewingAllClinics']
                ? ['clinic_id' => 'all']
                : $operationalRouteParams)
            : [];

        $quickActions = collect([
            [
                'label' => 'Transaksi Pasien',
                'description' => 'Tarik data SIMRS per tanggal dan simpan transaksi pasien lokal.',
                'route' => route('transaksi-pasien', $operationalRouteParams),
                'tone' => 'blue',
            ],
            [
                'label' => 'Input Pengeluaran',
                'description' => 'Catat kredit operasional dan atur kategori pengeluaran.',
                'route' => route('input-pengeluaran', $operationalRouteParams),
                'tone' => 'emerald',
            ],
            [
                'label' => 'Rekap Bulanan',
                'description' => 'Pastikan total layanan dan komponen transaksi tetap balance.',
                'route' => route('rekap-bulanan', $reportRouteParams),
                'tone' => 'amber',
            ],
            [
                'label' => 'Rekap Tahunan',
                'description' => 'Lihat performa debet dan kredit dalam tampilan per bulan.',
                'route' => route('rekap-tahunan', $reportRouteParams),
                'tone' => 'violet',
            ],
        ]);

        return view('dashboard', [
            'todayLabel' => $today->locale('id')->translatedFormat('l, d F Y'),
            'monthLabel' => $this->periodLabel($selectedMonth),
            'currentMonthName' => $selectedMonth->locale('id')->translatedFormat('F Y'),
            'monthlyRevenue' => $monthlyRevenue,
            'monthlyExpenseTotal' => $monthlyExpenseTotal,
            'monthlyNet' => $monthlyNet,
            'monthlyTransactionCount' => $monthTransactions->count(),
            'todayRevenue' => $todayRevenue,
            'todayTransactionCount' => $todayTransactions->count(),
            'todayExpenseTotal' => $todayExpenseTotal,
            'todayExpenseCount' => $todayExpenses->count(),
            'averageTransaction' => $averageTransaction,
            'activeServiceCount' => $masterLayanan->count(),
            'activeExpenseCategoryCount' => MasterKategoriPengeluaran::query()->active()->count(),
            'unmappedTransactionCount' => $unmappedTransactions,
            'mappedPercentage' => $mappedPercentage,
            'penjaminCount' => $penjaminMix->count(),
            'penjaminMix' => $penjaminMix,
            'topLayanan' => $topLayanan,
            'monthlyTrend' => $monthlyTrend,
            'recentTransactions' => $recentTransactions,
            'recentExpenses' => $recentExpenses,
            'quickActions' => $quickActions,
            'dashboardGreeting' => $dashboardGreeting,
            'clinicOptions' => $clinicContext['clinicOptions'],
            'showClinicFilter' => $clinicContext['showClinicFilter'],
            'selectedClinicFilter' => $clinicContext['viewingAllClinics']
                ? 'all'
                : (string) ($selectedClinicId ?: ''),
            'selectedClinicLabel' => $clinicContext['selectedClinicLabel'],
            'viewingAllClinics' => $clinicContext['viewingAllClinics'],
        ]);
    }

    public function transaksiPasien(Request $request): View
    {
        $selectedDate = $this->normalizeSelectedDate($request->string('tanggal')->toString());
        $selectedDateRange = $this->normalizeSelectedDateRange(
            $request->string('tanggal_awal')->toString(),
            $request->string('tanggal_akhir')->toString(),
            $selectedDate
        );
        $selectedStartDate = $selectedDateRange['start'];
        $selectedEndDate = $selectedDateRange['end'];
        $selectedDate = $selectedEndDate;
        $dataMonth = $this->normalizeSelectedMonth($request->string('data_bulan')->toString() ?: $selectedDate);
        $selectedPenjamin = trim($request->string('data_penjamin')->toString());
        $selectedLocalStatus = $this->normalizeLocalStatusFilter($request->string('local_status')->toString());
        $preferredTab = $request->string('active_tab')->toString();
        $user = $request->user();
        $isMasterView = $user?->isMaster() ?? false;
        $clinicContext = $this->clinicContext($request, $isMasterView);
        $selectedClinicId = $clinicContext['selectedClinicId'];
        $showTransaksiTab = ! $isMasterView;
        $showDataTransaksiTab = true;
        $showRekapPasienTab = $isMasterView;
        $komponenTransaksiMasters = $this->activeMasterKomponenTransaksi();
        $administrasiPasienMasters = $this->activeMasterAdministrasiPasien();
        $komponenTransaksiMasters->each(function (MasterKomponenTransaksi $item): void {
            $item->setAttribute('prefill_keys', $this->komponenPrefillKeys($item));
        });
        $administrasiPasienMasters->each(function (MasterAdministrasiPasien $item): void {
            $item->setAttribute('prefill_keys', $this->administrasiPrefillKeys($item));
        });

        $savedTransactions = collect();
        $visitRows = collect();

        if ($showTransaksiTab && $selectedClinicId) {
            $savedTransactions = TransaksiPasien::query()
                ->with(['masterLayanan', 'komponenTransaksi', 'administrasiTransaksi'])
                ->where('clinic_profile_id', $selectedClinicId)
                ->whereDate('tanggal', '>=', $selectedStartDate)
                ->whereDate('tanggal', '<=', $selectedEndDate)
                ->get()
                ->keyBy('simrs_no_rawat');

            $visitRows = $this->simrsVisitRows($selectedStartDate, $selectedClinicId, $selectedEndDate)
                ->filter(function (array $row) use ($savedTransactions, $selectedLocalStatus) {
                    if ($selectedLocalStatus === 'saved') {
                        return $savedTransactions->has($row['simrs_no_rawat']);
                    }

                    if ($selectedLocalStatus === 'unsaved') {
                        return ! $savedTransactions->has($row['simrs_no_rawat']);
                    }

                    return true;
                })
                ->map(fn (array $row) => array_merge($row, [
                    'clinic_profile_id' => $selectedClinicId,
                    'map_key' => $selectedClinicId . '::' . $row['simrs_no_rawat'],
                ]))
                ->values();
        }

        $savedTransactionQuery = TransaksiPasien::query()
            ->with(['masterLayanan', 'clinicProfile', 'komponenTransaksi', 'administrasiTransaksi'])
            ->whereYear('tanggal', $dataMonth->year)
            ->whereMonth('tanggal', $dataMonth->month);
        $this->scopeQueryToClinic($savedTransactionQuery, $selectedClinicId);

        if (filled($selectedPenjamin)) {
            $savedTransactionQuery->where('penjamin', $selectedPenjamin);
        }

        $savedTransactionList = $savedTransactionQuery
            ->orderByDesc('tanggal')
            ->orderBy('nama_pasien')
            ->get();

        $savedTransactionDataset = $savedTransactions
            ->values()
            ->concat($savedTransactionList)
            ->unique('id')
            ->keyBy(fn (TransaksiPasien $item) => $item->clinic_profile_id . '::' . $item->simrs_no_rawat);

        $penjaminOptionsQuery = TransaksiPasien::query()
            ->whereYear('tanggal', $dataMonth->year)
            ->whereMonth('tanggal', $dataMonth->month)
            ->whereNotNull('penjamin')
            ->where('penjamin', '!=', '')
            ->orderBy('penjamin')
            ->distinct();
        $this->scopeQueryToClinic($penjaminOptionsQuery, $selectedClinicId);
        $penjaminOptions = $penjaminOptionsQuery->pluck('penjamin');

        $rekapPasienQuery = RekapPasien::query()
            ->with(['masterLayanan', 'clinicProfile'])
            ->forBulan($dataMonth->month, $dataMonth->year)
            ->orderByDesc('tanggal')
            ->orderBy('nama_pasien');
        $this->scopeQueryToClinic($rekapPasienQuery, $selectedClinicId);
        $rekapPasienList = $showRekapPasienTab
            ? $rekapPasienQuery->get()
            : collect();

        $existingRekapKeys = $rekapPasienList->isNotEmpty()
            ? TransaksiPasien::query()
                ->select('clinic_profile_id', 'simrs_no_rawat')
                ->when($selectedClinicId, fn ($query) => $query->where('clinic_profile_id', $selectedClinicId))
                ->whereIn('simrs_no_rawat', $rekapPasienList->pluck('no_rawat')->filter()->unique()->values())
                ->get()
                ->mapWithKeys(fn (TransaksiPasien $item) => [
                    $item->clinic_profile_id . '::' . $item->simrs_no_rawat => true,
                ])
            : collect();

        $lastRekapUpdate = $selectedClinicId
            ? RekapPasienUpdate::query()
                ->with('user')
                ->where('clinic_profile_id', $selectedClinicId)
                ->first()
            : null;

        $allowedTabs = collect()
            ->when($showTransaksiTab, fn ($tabs) => $tabs->push('panel-transaksi-pasien'))
            ->when($showDataTransaksiTab, fn ($tabs) => $tabs->push('panel-data-transaksi'))
            ->when($showRekapPasienTab, fn ($tabs) => $tabs->push('panel-rekap-pasien'))
            ->values()
            ->all();
        $resolvedPreferredTab = in_array($preferredTab, $allowedTabs, true)
            ? $preferredTab
            : ($allowedTabs[0] ?? null);

        return view('pages.input-transaksi-pasien', [
            'selectedDate' => $selectedDate,
            'selectedStartDate' => $selectedStartDate,
            'selectedEndDate' => $selectedEndDate,
            'selectedDataMonth' => $dataMonth->format('Y-m'),
            'selectedPenjamin' => $selectedPenjamin,
            'selectedLocalStatus' => $selectedLocalStatus,
            'penjaminOptions' => $penjaminOptions,
            'preferredTab' => $resolvedPreferredTab,
            'visitRows' => $visitRows,
            'savedTransactions' => $savedTransactions,
            'savedTransactionList' => $savedTransactionList,
            'rekapPasienList' => $rekapPasienList,
            'existingRekapKeys' => $existingRekapKeys,
            'lastRekapUpdate' => $lastRekapUpdate,
            'showTransaksiTab' => $showTransaksiTab,
            'showDataTransaksiTab' => $showDataTransaksiTab,
            'showRekapPasienTab' => $showRekapPasienTab,
            'showUpdateRekapButton' => $showTransaksiTab,
            'komponenTransaksiMasters' => $komponenTransaksiMasters,
            'administrasiPasienMasters' => $administrasiPasienMasters,
            'viewingAllClinics' => $clinicContext['viewingAllClinics'],
            'savedTransactionData' => $savedTransactionDataset->map(function (TransaksiPasien $item) {
                return [
                    'id' => $item->id,
                    'map_key' => $item->clinic_profile_id . '::' . $item->simrs_no_rawat,
                    'clinic_profile_id' => $item->clinic_profile_id,
                    'user_id' => $item->user_id,
                    'simrs_no_rawat' => $item->simrs_no_rawat,
                    'simrs_no_reg' => $item->simrs_no_reg,
                    'tanggal' => optional($item->tanggal)->format('Y-m-d'),
                    'bulan' => $item->bulan,
                    'harian' => $item->harian,
                    'layanan_medis' => $item->layanan_medis,
                    'layanan_label' => $item->masterLayanan?->nama_layanan ?: $item->layanan_medis,
                    'dokter' => $item->dokter,
                    'penjamin' => $item->penjamin,
                    'no_rm' => $item->no_rm,
                    'nama_pasien' => $item->nama_pasien,
                    'jk' => $item->jk,
                    'statis' => $item->statis,
                    'genap' => $item->genap,
                    'status_pasien' => $item->status_pasien,
                    'alamat' => $item->alamat,
                    'lab' => $item->lab,
                    'icd' => $item->icd,
                    'diagnosa' => $item->diagnosa,
                    'farmasi' => $item->farmasi,
                    'jml_hari' => (int) $item->jml_hari,
                    'jml_visit' => (int) $item->jml_visit,
                    'jumlah_rp' => (float) $item->jumlah_rp,
                    'jumlah_kredit' => (float) $item->jumlah_kredit,
                    'petugas_admin' => $item->petugas_admin,
                    'keterangan' => $item->keterangan,
                    'komponen_transaksi' => $this->serializeKomponenTransaksi($item),
                    'administrasi_transaksi' => $this->serializeAdministrasiTransaksi($item),
                    'meta' => [
                        'penjamin' => $item->penjamin,
                    ],
                ];
            })->all(),
            'loggedInAdminName' => $this->resolveLoggedInAdminName($request->user()),
            'loggedInAdminRole' => $request->user()?->pegawaiProfile?->jabatan ?: 'Petugas Admin',
            'clinicOptions' => $clinicContext['clinicOptions'],
            'showClinicFilter' => $clinicContext['showClinicFilter'],
            'selectedClinicId' => $selectedClinicId,
            'selectedClinicLabel' => $clinicContext['selectedClinicLabel'],
        ]);
    }

    public function profileKlinik(Request $request): View
    {
        $clinicContext = $this->clinicContext($request, false);
        $user = $request->user();
        $isCreateMode = $user?->isMaster() && $request->string('mode')->toString() === 'create';
        $clinicProfile = $isCreateMode ? null : $clinicContext['selectedClinic'];

        return view('pages.profile-klinik', [
            'clinicProfile' => $clinicProfile,
            'clinicLogoPreviewDataUri' => $this->clinicLogoDataUri($clinicProfile?->logo_path),
            'clinicOptions' => $clinicContext['clinicOptions'],
            'showClinicFilter' => $clinicContext['showClinicFilter'],
            'selectedClinicId' => $clinicContext['selectedClinicId'],
            'selectedClinicLabel' => $clinicContext['selectedClinicLabel'],
            'isCreateMode' => $isCreateMode,
        ]);
    }

    public function saveProfileKlinik(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user?->isMaster()) {
            $profileId = $request->integer('profile_id');
            $isCreateMode = $request->string('profile_mode')->toString() === 'create';

            if ($isCreateMode) {
                $clinicProfile = ClinicProfile::query()->create(
                    $this->validatedClinicProfile($request, null, true, null)
                );
            } else {
                $clinicProfile = $profileId > 0
                    ? ClinicProfile::query()->findOrFail($profileId)
                    : $this->clinicContext($request, false)['selectedClinic'];

                abort_if(! $clinicProfile, 404);

                $clinicProfile->update(
                    $this->validatedClinicProfile($request, $clinicProfile->id, true, $clinicProfile)
                );
            }
        } else {
            abort_if(! $user?->clinic_profile_id, 403, 'Akun ini belum terhubung ke klinik.');

            $clinicProfile = ClinicProfile::query()->findOrFail($user->clinic_profile_id);
            $clinicProfile->update(
                $this->validatedClinicProfile($request, $clinicProfile->id, false, $clinicProfile)
            );
        }

        return redirect()
            ->route('profile-klinik', ['clinic_id' => $clinicProfile->id])
            ->with('success', 'Profil klinik berhasil diperbarui.');
    }

    public function inputPengeluaran(Request $request): View
    {
        $selectedMonth = $this->normalizeSelectedMonth($request->string('bulan')->toString());
        $selectedCategory = trim($request->string('kategori')->toString());
        $preferredTab = $request->string('active_tab')->toString();
        $clinicContext = $this->clinicContext($request, true);
        $selectedClinicId = $clinicContext['selectedClinicId'];

        $categoryOptions = MasterKategoriPengeluaran::query()
            ->active()
            ->orderBy('urutan_laporan')
            ->orderBy('nama_kategori')
            ->get();

        $expensesQuery = Pengeluaran::query()
            ->with(['masterKategoriPengeluaran', 'clinicProfile'])
            ->forBulan($selectedMonth->month, $selectedMonth->year);

        $this->scopeQueryToClinic($expensesQuery, $selectedClinicId);

        if (filled($selectedCategory)) {
            $expensesQuery->where('kategori_pengeluaran', $selectedCategory);
        }

        $expenses = $expensesQuery
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->get();

        return view('pages.input-pengeluaran', [
            'selectedMonth' => $selectedMonth->format('Y-m'),
            'selectedCategory' => $selectedCategory,
            'categoryOptions' => $categoryOptions,
            'expenses' => $expenses,
            'expenseData' => $expenses->keyBy('id')->map(function (Pengeluaran $expense) {
                return [
                    'id' => $expense->id,
                    'clinic_profile_id' => $expense->clinic_profile_id,
                    'clinic_label' => $expense->clinicProfile?->nama_pendek ?: $expense->clinicProfile?->nama_klinik,
                    'user_id' => $expense->user_id,
                    'master_kategori_pengeluaran_id' => $expense->master_kategori_pengeluaran_id,
                    'tanggal' => optional($expense->tanggal)->format('Y-m-d'),
                    'kategori_pengeluaran' => $expense->kategori_pengeluaran,
                    'deskripsi' => $expense->deskripsi,
                    'jumlah_rp' => (float) $expense->jumlah_rp,
                    'petugas_admin' => $expense->petugas_admin,
                    'keterangan' => $expense->keterangan,
                ];
            })->all(),
            'preferredTab' => in_array($preferredTab, ['panel-input-pengeluaran', 'panel-data-pengeluaran'], true)
                ? $preferredTab
                : null,
            'totalPengeluaran' => (float) $expenses->sum('jumlah_rp'),
            'loggedInAdminName' => $this->resolveLoggedInAdminName($request->user()),
            'loggedInAdminRole' => $request->user()?->pegawaiProfile?->jabatan ?: 'Petugas Admin',
            'clinicOptions' => $clinicContext['clinicOptions'],
            'showClinicFilter' => $clinicContext['showClinicFilter'],
            'selectedClinicId' => $selectedClinicId,
            'selectedClinicLabel' => $clinicContext['selectedClinicLabel'],
            'selectedClinicFilter' => $clinicContext['viewingAllClinics']
                ? 'all'
                : (string) ($selectedClinicId ?: ''),
            'viewingAllClinics' => $clinicContext['viewingAllClinics'],
        ]);
    }

    public function inputKlaimBpjs(Request $request): View
    {
        $selectedMonth = $this->normalizeSelectedMonth($request->string('bulan')->toString());
        $clinicContext = $this->clinicContext($request, true);
        $selectedClinicId = $clinicContext['selectedClinicId'];
        $selectedClaimAmount = (float) old('total_klaim', 0);
        $selectedClaimDate = old('tanggal_terima');
        $selectedNote = old('keterangan');

        $claimsQuery = BpjsKlaimBulanan::query()
            ->with(['clinicProfile', 'user', 'masterKomponenSelisih', 'alokasiKomponen.masterKomponen'])
            ->forBulan($selectedMonth->month, $selectedMonth->year)
            ->orderByDesc('updated_at')
            ->orderByDesc('id');
        $this->scopeQueryToClinic($claimsQuery, $selectedClinicId);
        $claims = $claimsQuery->get();

        $existingClaim = $selectedClinicId
            ? $claims->firstWhere('clinic_profile_id', $selectedClinicId)
            : null;

        if (! old()) {
            $selectedClaimAmount = (float) ($existingClaim?->total_klaim ?? 0);
            $selectedClaimDate = optional($existingClaim?->tanggal_terima)->format('Y-m-d');
            $selectedNote = $existingClaim?->keterangan;
        }

        $preview = $selectedClinicId
            ? $this->buildBpjsKlaimSummary($selectedClinicId, $selectedMonth, $selectedClaimAmount)
            : $this->emptyBpjsKlaimSummary($selectedClaimAmount);

        return view('pages.input-klaim-bpjs', [
            'selectedMonth' => $selectedMonth->format('Y-m'),
            'selectedClaimDate' => $selectedClaimDate ?: $selectedMonth->copy()->endOfMonth()->format('Y-m-d'),
            'selectedClaimAmount' => $selectedClaimAmount,
            'selectedNote' => $selectedNote,
            'claims' => $claims,
            'existingClaim' => $existingClaim,
            'preview' => $preview,
            'clinicOptions' => $clinicContext['clinicOptions'],
            'showClinicFilter' => $clinicContext['showClinicFilter'],
            'selectedClinicId' => $selectedClinicId,
            'selectedClinicLabel' => $clinicContext['selectedClinicLabel'],
            'selectedClinicFilter' => $clinicContext['viewingAllClinics']
                ? 'all'
                : (string) ($selectedClinicId ?: ''),
            'viewingAllClinics' => $clinicContext['viewingAllClinics'],
            'canUpdateClaim' => $request->user()?->canEditOperationalData() ?? false,
            'loggedInAdminName' => $this->resolveLoggedInAdminName($request->user()),
        ]);
    }

    public function storeKlaimBpjs(Request $request): RedirectResponse
    {
        $data = $this->validatedBpjsKlaimBulanan($request);

        $alreadyExists = BpjsKlaimBulanan::query()
            ->where('clinic_profile_id', $data['clinic_profile_id'])
            ->where('bulan', $data['bulan'])
            ->where('tahun', $data['tahun'])
            ->exists();

        if ($alreadyExists) {
            return redirect()
                ->route('input-klaim-bpjs', [
                    'bulan' => Carbon::create($data['tahun'], $data['bulan'], 1)->format('Y-m'),
                    'clinic_id' => $data['clinic_profile_id'],
                ])
                ->withInput()
                ->with('error', 'Klaim BPJS untuk periode ini sudah ada. Dalam satu bulan, satu klinik hanya boleh punya satu data klaim.');
        }

        $alokasiSync = $data['_alokasi_sync'] ?? [];
        unset($data['_alokasi_sync']);

        $klaim = BpjsKlaimBulanan::query()->create($data);
        $this->syncBpjsKlaimAlokasi($klaim, $alokasiSync);

        return redirect()
            ->route('input-klaim-bpjs', [
                'bulan' => Carbon::create($data['tahun'], $data['bulan'], 1)->format('Y-m'),
                'clinic_id' => $data['clinic_profile_id'],
            ])
            ->with('success', 'Rekap klaim BPJS berhasil disimpan.');
    }

    public function updateKlaimBpjs(
        Request $request,
        BpjsKlaimBulanan $bpjsKlaimBulanan
    ): RedirectResponse {
        $this->ensureOperationalRecordAccess($request->user(), $bpjsKlaimBulanan->clinic_profile_id);

        $data = $this->validatedBpjsKlaimBulanan($request, $bpjsKlaimBulanan);
        $alokasiSync = $data['_alokasi_sync'] ?? [];
        unset($data['_alokasi_sync']);

        $bpjsKlaimBulanan->update($data);
        $this->syncBpjsKlaimAlokasi($bpjsKlaimBulanan, $alokasiSync);

        return redirect()
            ->route('input-klaim-bpjs', [
                'bulan' => Carbon::create($data['tahun'], $data['bulan'], 1)->format('Y-m'),
                'clinic_id' => $data['clinic_profile_id'],
            ])
            ->with('success', 'Rekap klaim BPJS berhasil diperbarui.');
    }

    public function destroyKlaimBpjs(
        Request $request,
        BpjsKlaimBulanan $bpjsKlaimBulanan
    ): RedirectResponse {
        $this->ensureOperationalRecordAccess($request->user(), $bpjsKlaimBulanan->clinic_profile_id);

        $redirectMonth = Carbon::create($bpjsKlaimBulanan->tahun, $bpjsKlaimBulanan->bulan, 1)->format('Y-m');
        $clinicId = $bpjsKlaimBulanan->clinic_profile_id;
        $returnClinicFilter = strtolower(trim((string) $request->input('return_clinic_filter', '')));

        $bpjsKlaimBulanan->delete();

        $routeParams = ['bulan' => $redirectMonth];

        if ($request->user()?->isMaster() && $returnClinicFilter === 'all') {
            $routeParams['clinic_id'] = 'all';
        } else {
            $routeParams['clinic_id'] = $clinicId;
        }

        return redirect()
            ->route('input-klaim-bpjs', $routeParams)
            ->with('success', 'Rekap klaim BPJS berhasil dihapus.');
    }

    public function kodeLayanan(Request $request): View
    {
        $search = trim($request->string('q')->toString());
        $editId = $request->integer('edit');

        $transactionUsageCounts = TransaksiPasien::query()
            ->select('layanan_medis', DB::raw('COUNT(*) as total'))
            ->groupBy('layanan_medis')
            ->pluck('total', 'layanan_medis');
        $rekapUsageCounts = RekapPasien::query()
            ->select('master_layanan_id', DB::raw('COUNT(*) as total'))
            ->whereNotNull('master_layanan_id')
            ->groupBy('master_layanan_id')
            ->pluck('total', 'master_layanan_id');

        $recordsQuery = MasterLayanan::query()
            ->orderBy('urutan_laporan')
            ->orderBy('kode_layanan');

        if (filled($search)) {
            $recordsQuery->where(function ($query) use ($search) {
                $query->where('kode_layanan', 'like', '%' . $search . '%')
                    ->orWhere('nama_layanan', 'like', '%' . $search . '%')
                    ->orWhere('simrs_kd_poli', 'like', '%' . $search . '%')
                    ->orWhere('simrs_nm_poli', 'like', '%' . $search . '%');
            });
        }

        $records = $recordsQuery->get()->map(function (MasterLayanan $item) use ($transactionUsageCounts, $rekapUsageCounts) {
            $usageCount = (int) ($transactionUsageCounts[$item->kode_layanan] ?? 0)
                + (int) ($rekapUsageCounts[$item->id] ?? 0);

            return [
                'id' => $item->id,
                'code' => $item->kode_layanan,
                'name' => $item->nama_layanan,
                'mapping_key' => $item->simrs_kd_poli,
                'mapping_label' => $item->simrs_nm_poli,
                'is_bpjs_claim_target' => (bool) $item->is_bpjs_claim_target,
                'sort_order' => (int) $item->urutan_laporan,
                'is_active' => (bool) $item->is_active,
                'usage_count' => $usageCount,
                'is_deletable' => $usageCount === 0,
            ];
        });

        $editingItem = $editId > 0
            ? MasterLayanan::query()->find($editId)
            : null;

        return view('pages.master-reference', [
            'variant' => 'layanan',
            'pageTitle' => 'Kode Layanan',
            'pageEyebrow' => 'Master Transaksi',
            'pageDescription' => 'Atur kode layanan, nama laporan, dan mapping poli SIMRS dalam satu tempat.',
            'routeIndex' => 'kode-layanan',
            'routeStore' => 'kode-layanan.store',
            'routeUpdate' => 'kode-layanan.update',
            'routeDestroy' => 'kode-layanan.destroy',
            'search' => $search,
            'records' => $records,
            'editingItem' => $editingItem,
            'stats' => [
                'total' => MasterLayanan::query()->count(),
                'active' => MasterLayanan::query()->active()->count(),
                'mapped' => MasterLayanan::query()->whereNotNull('simrs_kd_poli')->where('simrs_kd_poli', '!=', '')->count(),
                'used' => $records->sum('usage_count'),
                'bpjs_target' => MasterLayanan::query()->where('is_bpjs_claim_target', true)->count(),
            ],
        ]);
    }

    public function storeKodeLayanan(Request $request): RedirectResponse
    {
        $masterLayanan = MasterLayanan::query()->create($this->validatedMasterLayanan($request));
        $this->syncBpjsClaimTargetLayanan($masterLayanan);

        return redirect()
            ->route('kode-layanan')
            ->with('success', 'Kode layanan berhasil disimpan.');
    }

    public function updateKodeLayanan(Request $request, MasterLayanan $masterLayanan): RedirectResponse
    {
        $masterLayanan->update($this->validatedMasterLayanan($request, $masterLayanan->id));
        $this->syncBpjsClaimTargetLayanan($masterLayanan);

        return redirect()
            ->route('kode-layanan')
            ->with('success', 'Kode layanan berhasil diperbarui.');
    }

    public function destroyKodeLayanan(Request $request, MasterLayanan $masterLayanan): RedirectResponse
    {
        $usageCount = TransaksiPasien::query()
            ->where('layanan_medis', $masterLayanan->kode_layanan)
            ->count()
            + RekapPasien::query()
                ->where('master_layanan_id', $masterLayanan->id)
                ->count();

        if ($usageCount > 0) {
            return $this->cannotDeleteMasterRedirect(
                $request,
                'kode-layanan',
                'Kode layanan ini sudah dipakai oleh ' . number_format($usageCount, 0, ',', '.') . ' data. Nonaktifkan saja jika sudah tidak ingin dipakai.'
            );
        }

        $masterLayanan->delete();

        return $this->masterDestroySuccessRedirect(
            $request,
            'kode-layanan',
            'Kode layanan berhasil dihapus.'
        );
    }

    public function kodeKomponenTransaksi(Request $request): View
    {
        $search = trim($request->string('q')->toString());
        $editId = $request->integer('edit');

        $usageCounts = TransaksiPasienKomponen::query()
            ->select('master_komponen_transaksi_id', DB::raw('COUNT(*) as total'))
            ->groupBy('master_komponen_transaksi_id')
            ->pluck('total', 'master_komponen_transaksi_id');

        $recordsQuery = MasterKomponenTransaksi::query()
            ->orderBy('urutan_laporan')
            ->orderBy('kode_komponen');

        if (filled($search)) {
            $recordsQuery->where(function ($query) use ($search) {
                $query->where('kode_komponen', 'like', '%' . $search . '%')
                    ->orWhere('nama_komponen', 'like', '%' . $search . '%')
                    ->orWhere('field_key', 'like', '%' . $search . '%');
            });
        }

        $records = $recordsQuery->get()->map(function (MasterKomponenTransaksi $item) use ($usageCounts) {
            $usageCount = (int) ($usageCounts[$item->id] ?? 0);

            return [
                'id' => $item->id,
                'code' => $item->kode_komponen,
                'name' => $item->nama_komponen,
                'mapping_key' => $item->field_key,
                'mapping_label' => null,
                'direction_key' => $item->arah_laporan,
                'direction_label' => $item->arah_laporan === 'kredit' ? 'Kredit' : 'Debet',
                'ikut_alokasi_bpjs' => (bool) $item->ikut_alokasi_bpjs,
                'basis_pajak_obat' => (bool) $item->basis_pajak_obat,
                'peran_sistem' => $item->peran_sistem,
                'peran_sistem_label' => match ($item->peran_sistem) {
                    'bpjs_selisih' => 'Penyesuaian Klaim BPJS',
                    default => 'Normal',
                },
                'sort_order' => (int) $item->urutan_laporan,
                'is_active' => (bool) $item->is_active,
                'usage_count' => $usageCount,
                'is_deletable' => $usageCount === 0,
            ];
        });

        $editingItem = $editId > 0
            ? MasterKomponenTransaksi::query()->find($editId)
            : null;

        return view('pages.master-reference', [
            'variant' => 'komponen',
            'pageTitle' => 'Komponen Transaksi',
            'pageEyebrow' => 'Master Transaksi',
            'pageDescription' => 'Kelola komponen transaksi pasien, termasuk arah laporan debet atau kredit, agar form input dan rekap bulanan memakai struktur yang sama.',
            'routeIndex' => 'kode-komponen-transaksi',
            'routeStore' => 'kode-komponen-transaksi.store',
            'routeUpdate' => 'kode-komponen-transaksi.update',
            'routeDestroy' => 'kode-komponen-transaksi.destroy',
            'search' => $search,
            'records' => $records,
            'editingItem' => $editingItem,
            'stats' => [
                'total' => MasterKomponenTransaksi::query()->count(),
                'active' => MasterKomponenTransaksi::query()->active()->count(),
                'mapped' => MasterKomponenTransaksi::query()->where('ikut_alokasi_bpjs', true)->count(),
                'used' => $usageCounts->sum(),
            ],
        ]);
    }

    public function storeKodeKomponenTransaksi(Request $request): RedirectResponse
    {
        MasterKomponenTransaksi::query()->create($this->validatedMasterKomponenTransaksi($request));

        return redirect()
            ->route('kode-komponen-transaksi')
            ->with('success', 'Komponen transaksi berhasil disimpan.');
    }

    public function updateKodeKomponenTransaksi(
        Request $request,
        MasterKomponenTransaksi $masterKomponenTransaksi
    ): RedirectResponse {
        $masterKomponenTransaksi->update(
            $this->validatedMasterKomponenTransaksi($request, $masterKomponenTransaksi->id, $masterKomponenTransaksi)
        );

        return redirect()
            ->route('kode-komponen-transaksi')
            ->with('success', 'Komponen transaksi berhasil diperbarui.');
    }

    public function destroyKodeKomponenTransaksi(
        Request $request,
        MasterKomponenTransaksi $masterKomponenTransaksi
    ): RedirectResponse {
        $usageCount = TransaksiPasienKomponen::query()
            ->where('master_komponen_transaksi_id', $masterKomponenTransaksi->id)
            ->count();

        if ($usageCount > 0) {
            return $this->cannotDeleteMasterRedirect(
                $request,
                'kode-komponen-transaksi',
                'Komponen transaksi ini sudah dipakai oleh ' . number_format($usageCount, 0, ',', '.') . ' data. Nonaktifkan saja jika sudah tidak ingin dipakai.'
            );
        }

        $masterKomponenTransaksi->delete();

        return $this->masterDestroySuccessRedirect(
            $request,
            'kode-komponen-transaksi',
            'Komponen transaksi berhasil dihapus.'
        );
    }

    public function kodeAdministrasiPasien(Request $request): View
    {
        $search = trim($request->string('q')->toString());
        $editId = $request->integer('edit');

        $usageCounts = TransaksiPasienAdministrasi::query()
            ->select('master_administrasi_pasien_id', DB::raw('COUNT(*) as total'))
            ->groupBy('master_administrasi_pasien_id')
            ->pluck('total', 'master_administrasi_pasien_id');

        $recordsQuery = MasterAdministrasiPasien::query()
            ->orderBy('urutan_laporan')
            ->orderBy('kode_administrasi');

        if (filled($search)) {
            $recordsQuery->where(function ($query) use ($search) {
                $query->where('kode_administrasi', 'like', '%' . $search . '%')
                    ->orWhere('nama_administrasi', 'like', '%' . $search . '%')
                    ->orWhere('field_key', 'like', '%' . $search . '%');
            });
        }

        $records = $recordsQuery->get()->map(function (MasterAdministrasiPasien $item) use ($usageCounts) {
            $usageCount = (int) ($usageCounts[$item->id] ?? 0);

            return [
                'id' => $item->id,
                'code' => $item->kode_administrasi,
                'name' => $item->nama_administrasi,
                'mapping_key' => $item->field_key,
                'mapping_label' => null,
                'direction_key' => $item->arah_laporan,
                'direction_label' => $item->arah_laporan === 'kredit' ? 'Kredit' : 'Debet',
                'sort_order' => (int) $item->urutan_laporan,
                'is_active' => (bool) $item->is_active,
                'usage_count' => $usageCount,
                'is_deletable' => $usageCount === 0,
            ];
        });

        $editingItem = $editId > 0
            ? MasterAdministrasiPasien::query()->find($editId)
            : null;

        return view('pages.master-reference', [
            'variant' => 'administrasi',
            'pageTitle' => 'Administrasi Pasien',
            'pageEyebrow' => 'Master Transaksi',
            'pageDescription' => 'Kelola field administrasi pasien yang tampil dinamis di form transaksi, lengkap dengan arah laporan debet atau kredit.',
            'routeIndex' => 'kode-administrasi-pasien',
            'routeStore' => 'kode-administrasi-pasien.store',
            'routeUpdate' => 'kode-administrasi-pasien.update',
            'routeDestroy' => 'kode-administrasi-pasien.destroy',
            'search' => $search,
            'records' => $records,
            'editingItem' => $editingItem,
            'stats' => [
                'total' => MasterAdministrasiPasien::query()->count(),
                'active' => MasterAdministrasiPasien::query()->active()->count(),
                'mapped' => MasterAdministrasiPasien::query()->whereNotNull('field_key')->where('field_key', '!=', '')->count(),
                'used' => $usageCounts->sum(),
            ],
        ]);
    }

    public function storeKodeAdministrasiPasien(Request $request): RedirectResponse
    {
        MasterAdministrasiPasien::query()->create($this->validatedMasterAdministrasiPasien($request));

        return redirect()
            ->route('kode-administrasi-pasien')
            ->with('success', 'Administrasi pasien berhasil disimpan.');
    }

    public function updateKodeAdministrasiPasien(
        Request $request,
        MasterAdministrasiPasien $masterAdministrasiPasien
    ): RedirectResponse {
        $masterAdministrasiPasien->update(
            $this->validatedMasterAdministrasiPasien($request, $masterAdministrasiPasien->id, $masterAdministrasiPasien)
        );

        return redirect()
            ->route('kode-administrasi-pasien')
            ->with('success', 'Administrasi pasien berhasil diperbarui.');
    }

    public function destroyKodeAdministrasiPasien(
        Request $request,
        MasterAdministrasiPasien $masterAdministrasiPasien
    ): RedirectResponse {
        $usageCount = TransaksiPasienAdministrasi::query()
            ->where('master_administrasi_pasien_id', $masterAdministrasiPasien->id)
            ->count();

        if ($usageCount > 0) {
            return $this->cannotDeleteMasterRedirect(
                $request,
                'kode-administrasi-pasien',
                'Administrasi pasien ini sudah dipakai oleh ' . number_format($usageCount, 0, ',', '.') . ' data. Nonaktifkan saja jika sudah tidak ingin dipakai.'
            );
        }

        $masterAdministrasiPasien->delete();

        return $this->masterDestroySuccessRedirect(
            $request,
            'kode-administrasi-pasien',
            'Administrasi pasien berhasil dihapus.'
        );
    }

    public function kodePengeluaran(Request $request): View
    {
        $search = trim($request->string('q')->toString());
        $editId = $request->integer('edit');

        $usageCounts = Pengeluaran::query()
            ->select('master_kategori_pengeluaran_id', DB::raw('COUNT(*) as total'))
            ->whereNotNull('master_kategori_pengeluaran_id')
            ->groupBy('master_kategori_pengeluaran_id')
            ->pluck('total', 'master_kategori_pengeluaran_id');

        $recordsQuery = MasterKategoriPengeluaran::query()
            ->orderBy('urutan_laporan')
            ->orderBy('kode_kategori');

        if (filled($search)) {
            $recordsQuery->where(function ($query) use ($search) {
                $query->where('kode_kategori', 'like', '%' . $search . '%')
                    ->orWhere('nama_kategori', 'like', '%' . $search . '%');
            });
        }

        $records = $recordsQuery->get()->map(function (MasterKategoriPengeluaran $item) use ($usageCounts) {
            $usageCount = (int) ($usageCounts[$item->id] ?? 0);

            return [
                'id' => $item->id,
                'code' => $item->kode_kategori,
                'name' => $item->nama_kategori,
                'mapping_key' => null,
                'mapping_label' => null,
                'sort_order' => (int) $item->urutan_laporan,
                'is_active' => (bool) $item->is_active,
                'usage_count' => $usageCount,
                'is_deletable' => $usageCount === 0,
            ];
        });

        $editingItem = $editId > 0
            ? MasterKategoriPengeluaran::query()->find($editId)
            : null;

        return view('pages.master-reference', [
            'variant' => 'pengeluaran',
            'pageTitle' => 'Kode Pengeluaran',
            'pageEyebrow' => 'Master Transaksi',
            'pageDescription' => 'Kelola kode kategori pengeluaran untuk dipakai konsisten pada input kredit dan laporan.',
            'routeIndex' => 'kode-pengeluaran',
            'routeStore' => 'kode-pengeluaran.store',
            'routeUpdate' => 'kode-pengeluaran.update',
            'routeDestroy' => 'kode-pengeluaran.destroy',
            'search' => $search,
            'records' => $records,
            'editingItem' => $editingItem,
            'stats' => [
                'total' => MasterKategoriPengeluaran::query()->count(),
                'active' => MasterKategoriPengeluaran::query()->active()->count(),
                'mapped' => MasterKategoriPengeluaran::query()->whereNotNull('kode_kategori')->where('kode_kategori', '!=', '')->count(),
                'used' => $usageCounts->sum(),
            ],
        ]);
    }

    public function storeKodePengeluaran(Request $request): RedirectResponse
    {
        MasterKategoriPengeluaran::query()->create($this->validatedMasterKategoriPengeluaran($request));

        return redirect()
            ->route('kode-pengeluaran')
            ->with('success', 'Kode pengeluaran berhasil disimpan.');
    }

    public function updateKodePengeluaran(Request $request, MasterKategoriPengeluaran $masterKategoriPengeluaran): RedirectResponse
    {
        $masterKategoriPengeluaran->update(
            $this->validatedMasterKategoriPengeluaran($request, $masterKategoriPengeluaran->id)
        );

        return redirect()
            ->route('kode-pengeluaran')
            ->with('success', 'Kode pengeluaran berhasil diperbarui.');
    }

    public function destroyKodePengeluaran(
        Request $request,
        MasterKategoriPengeluaran $masterKategoriPengeluaran
    ): RedirectResponse {
        $usageCount = Pengeluaran::query()
            ->where('master_kategori_pengeluaran_id', $masterKategoriPengeluaran->id)
            ->count();

        if ($usageCount > 0) {
            return $this->cannotDeleteMasterRedirect(
                $request,
                'kode-pengeluaran',
                'Kode pengeluaran ini sudah dipakai oleh ' . number_format($usageCount, 0, ',', '.') . ' data. Nonaktifkan saja jika sudah tidak ingin dipakai.'
            );
        }

        $masterKategoriPengeluaran->delete();

        return $this->masterDestroySuccessRedirect(
            $request,
            'kode-pengeluaran',
            'Kode pengeluaran berhasil dihapus.'
        );
    }

    public function koneksiDbKlinik(Request $request): View
    {
        $search = trim($request->string('q')->toString());
        $editId = $request->integer('edit');

        $recordsQuery = ClinicDatabaseConnection::query()
            ->with('clinicProfile')
            ->where('connection_role', 'simrs')
            ->orderBy('clinic_profile_id');

        if (filled($search)) {
            $recordsQuery->where(function ($query) use ($search) {
                $query->where('server_name', 'like', '%' . $search . '%')
                    ->orWhere('host', 'like', '%' . $search . '%')
                    ->orWhere('zero_tier_ip', 'like', '%' . $search . '%')
                    ->orWhere('database', 'like', '%' . $search . '%')
                    ->orWhere('username', 'like', '%' . $search . '%')
                    ->orWhereHas('clinicProfile', function ($clinicQuery) use ($search) {
                        $clinicQuery->where('kode_klinik', 'like', '%' . $search . '%')
                            ->orWhere('nama_klinik', 'like', '%' . $search . '%')
                            ->orWhere('nama_pendek', 'like', '%' . $search . '%');
                    });
            });
        }

        $records = $recordsQuery->get();
        $editingItem = $editId > 0
            ? ClinicDatabaseConnection::query()->with('clinicProfile')->find($editId)
            : null;

        return view('pages.clinic-db-connections', [
            'search' => $search,
            'records' => $records,
            'editingItem' => $editingItem,
            'clinicOptions' => ClinicProfile::query()
                ->active()
                ->orderBy('nama_klinik')
                ->get(),
            'stats' => [
                'total' => ClinicDatabaseConnection::query()->where('connection_role', 'simrs')->count(),
                'active' => ClinicDatabaseConnection::query()->where('connection_role', 'simrs')->active()->count(),
                'zero_tier' => ClinicDatabaseConnection::query()
                    ->where('connection_role', 'simrs')
                    ->whereNotNull('zero_tier_ip')
                    ->where('zero_tier_ip', '!=', '')
                    ->count(),
                'configured_clinics' => ClinicDatabaseConnection::query()
                    ->where('connection_role', 'simrs')
                    ->distinct('clinic_profile_id')
                    ->count('clinic_profile_id'),
            ],
            'appDbSummary' => $this->appDatabaseSummary(),
        ]);
    }

    public function storeKoneksiDbKlinik(Request $request): RedirectResponse
    {
        ClinicDatabaseConnection::query()->create(
            $this->validatedClinicDatabaseConnection($request)
        );

        return redirect()
            ->route('koneksi-db-klinik')
            ->with('success', 'Koneksi DB klinik berhasil disimpan.');
    }

    public function updateKoneksiDbKlinik(
        Request $request,
        ClinicDatabaseConnection $clinicDatabaseConnection
    ): RedirectResponse {
        $clinicDatabaseConnection->update(
            $this->validatedClinicDatabaseConnection($request, $clinicDatabaseConnection->id, $clinicDatabaseConnection)
        );

        return redirect()
            ->route('koneksi-db-klinik')
            ->with('success', 'Koneksi DB klinik berhasil diperbarui.');
    }

    public function destroyKoneksiDbKlinik(
        Request $request,
        ClinicDatabaseConnection $clinicDatabaseConnection
    ): RedirectResponse {
        $clinicDatabaseConnection->delete();

        return $this->masterDestroySuccessRedirect(
            $request,
            'koneksi-db-klinik',
            'Koneksi DB klinik berhasil dihapus.'
        );
    }

    public function testKoneksiDbKlinik(
        Request $request,
        ClinicDatabaseConnection $clinicDatabaseConnection
    ): RedirectResponse {
        try {
            $connection = app(ClinicDatabaseManager::class)->connectionFromRecord(
                $clinicDatabaseConnection,
                'test_clinic_' . $clinicDatabaseConnection->id
            );

            $connection->getPdo();
            $connection->select('SELECT 1 AS ok');

            $clinicDatabaseConnection->forceFill([
                'last_verified_at' => now(),
            ])->save();

            return back()->with(
                'success',
                'Tes koneksi berhasil untuk '
                . ($clinicDatabaseConnection->clinicProfile?->nama_pendek
                    ?: $clinicDatabaseConnection->clinicProfile?->nama_klinik
                    ?: 'klinik ini')
                . '.'
            );
        } catch (\Throwable $exception) {
            return back()->with(
                'error',
                'Tes koneksi gagal: ' . $this->humanizeClinicConnectionError($exception)
            );
        }
    }

    public function rekapBulanan(Request $request): View
    {
        return view('pages.rekap-bulanan', $this->monthlyReportViewData($request));
    }

    public function downloadRekapBulananPdf(Request $request): Response
    {
        $reportData = $this->monthlyReportViewData($request);
        $pdfData = array_merge(
            $reportData,
            $this->monthlyReportPdfMeta(
                $reportData['selectedClinicProfile'],
                $reportData['viewingAllClinics'],
                $request->user()
            ),
            [
                'pengeluaranPrintRows' => $reportData['pengeluaranRows']
                    ->filter(fn (array $row) => abs((float) ($row['kredit'] ?? 0)) >= 0.01)
                    ->values(),
            ]
        );

        $filename = 'rekap-bulanan-'
            . Str::slug((string) ($pdfData['clinicPdfName'] ?: 'semua-klinik'))
            . '-'
            . $reportData['selectedMonth']
            . '.pdf';

        return Pdf::loadView('pages.rekap-bulanan-pdf', $pdfData)
            ->setPaper('a4', 'portrait')
            ->download($filename);
    }

    public function rekapTahunan(Request $request): View
    {
        $selectedYear = $this->normalizeSelectedYear($request->string('tahun')->toString());
        $selectedPenjaminMode = $this->normalizeRekapPenjaminMode($request->string('penjamin')->toString());
        $clinicContext = $this->clinicContext($request, true);

        $transactionsQuery = TransaksiPasien::query()
            ->with(['komponenTransaksi', 'administrasiTransaksi'])
            ->whereYear('tanggal', $selectedYear);
        $this->scopeQueryToClinic($transactionsQuery, $clinicContext['selectedClinicId']);
        $this->scopeTransactionsByPenjaminMode($transactionsQuery, $selectedPenjaminMode);
        $transactions = $transactionsQuery->get();

        $expensesQuery = Pengeluaran::query()
            ->forTahun($selectedYear);
        $this->scopeQueryToClinic($expensesQuery, $clinicContext['selectedClinicId']);
        $expenses = $expensesQuery->get();

        $masterLayanan = MasterLayanan::query()
            ->active()
            ->orderBy('urutan_laporan')
            ->orderBy('nama_layanan')
            ->get();

        $masterKategori = MasterKategoriPengeluaran::query()
            ->active()
            ->orderBy('urutan_laporan')
            ->orderBy('nama_kategori')
            ->get();

        $bpjsClaimSummary = $selectedPenjaminMode === 'umum'
            ? $this->emptyYearlyBpjsClaimSummary()
            : $this->yearlyBpjsClaimSummary($selectedYear, $clinicContext['selectedClinicId']);
        $bpjsClaimTargetLayanan = $this->bpjsClaimTargetLayanan($masterLayanan);
        $penerimaanBaseRows = $this->yearlyLayananRows($transactions, $masterLayanan);
        $penerimaanRows = $this->mergeYearlyBpjsClaimIntoLayananRows($penerimaanBaseRows, $bpjsClaimSummary, $bpjsClaimTargetLayanan);
        $bpjsClaimRow = $this->yearlyBpjsClaimRow($bpjsClaimSummary);
        $komponenRows = $this->yearlyKomponenRows($transactions, $this->activeMasterKomponenTransaksi());
        $administrasiRows = $this->yearlyAdministrasiRows($transactions, $this->activeMasterAdministrasiPasien());
        $fieldTransaksiBaseRows = $komponenRows->concat($administrasiRows)->values();
        $fieldTransaksiRows = $this->mergeYearlyBpjsClaimIntoFieldRows($fieldTransaksiBaseRows, $bpjsClaimSummary);
        $pengeluaranRows = $this->yearlyPengeluaranRows($expenses, $masterKategori);
        $monthHeaders = $this->yearMonthHeaders();

        $penerimaanMonthlyTotalsVersiKlinik = $this->yearlySideTotals($penerimaanBaseRows, 'debet');
        $bpjsClaimMonthlyTotals = $bpjsClaimSummary['monthly_klaim_totals'];
        $penerimaanMonthlyTotals = $this->yearlySideTotals($penerimaanRows, 'debet');
        $fieldTransaksiMonthlyTotalsVersiKlinikDebet = $this->yearlySideTotals($fieldTransaksiBaseRows, 'debet');
        $fieldTransaksiMonthlyTotalsVersiKlinikKredit = $this->yearlySideTotals($fieldTransaksiBaseRows, 'kredit');
        $fieldTransaksiMonthlyTotalsDebet = $this->yearlySideTotals($fieldTransaksiRows, 'debet');
        $fieldTransaksiMonthlyTotalsKredit = $this->yearlySideTotals($fieldTransaksiRows, 'kredit');
        $pengeluaranMonthlyTotals = $this->yearlySideTotals($pengeluaranRows, 'kredit');
        $saldoMonthlyTotals = collect(range(1, 12))
            ->mapWithKeys(fn (int $month) => [
                $month => (float) ($penerimaanMonthlyTotals[$month] ?? 0) - (float) ($pengeluaranMonthlyTotals[$month] ?? 0),
            ])
            ->all();

        $annualDebitTotal = (float) array_sum($penerimaanMonthlyTotals);
        $annualDebitTotalVersiKlinik = (float) array_sum($penerimaanMonthlyTotalsVersiKlinik);
        $annualBpjsClaimTotal = (float) array_sum($bpjsClaimMonthlyTotals);
        $annualKreditTotal = (float) array_sum($pengeluaranMonthlyTotals);
        $bpjsClaimMergedIntoLayanan = $annualBpjsClaimTotal > 0 && (bool) $bpjsClaimTargetLayanan;

        return view('pages.rekap-tahunan', [
            'selectedYear' => $selectedYear,
            'selectedPenjaminMode' => $selectedPenjaminMode,
            'selectedPenjaminLabel' => match ($selectedPenjaminMode) {
                'bpjs' => 'BPJS',
                'umum' => 'Umum',
                default => 'Semua Penjamin',
            },
            'monthHeaders' => $monthHeaders,
            'penerimaanRows' => $penerimaanRows,
            'bpjsClaimRow' => $bpjsClaimRow,
            'fieldTransaksiRows' => $fieldTransaksiRows,
            'pengeluaranRows' => $pengeluaranRows,
            'penerimaanMonthlyTotals' => $penerimaanMonthlyTotals,
            'penerimaanMonthlyTotalsVersiKlinik' => $penerimaanMonthlyTotalsVersiKlinik,
            'bpjsClaimMonthlyTotals' => $bpjsClaimMonthlyTotals,
            'fieldTransaksiMonthlyTotalsVersiKlinikDebet' => $fieldTransaksiMonthlyTotalsVersiKlinikDebet,
            'fieldTransaksiMonthlyTotalsVersiKlinikKredit' => $fieldTransaksiMonthlyTotalsVersiKlinikKredit,
            'fieldTransaksiMonthlyTotalsDebet' => $fieldTransaksiMonthlyTotalsDebet,
            'fieldTransaksiMonthlyTotalsKredit' => $fieldTransaksiMonthlyTotalsKredit,
            'pengeluaranMonthlyTotals' => $pengeluaranMonthlyTotals,
            'saldoMonthlyTotals' => $saldoMonthlyTotals,
            'annualDebitTotal' => $annualDebitTotal,
            'annualDebitTotalVersiKlinik' => $annualDebitTotalVersiKlinik,
            'annualBpjsClaimTotal' => $annualBpjsClaimTotal,
            'annualKreditTotal' => $annualKreditTotal,
            'annualSaldo' => $annualDebitTotal - $annualKreditTotal,
            'bpjsClaimSummary' => $bpjsClaimSummary,
            'hasBpjsClaimRows' => $annualBpjsClaimTotal > 0,
            'bpjsClaimMergedIntoLayanan' => $bpjsClaimMergedIntoLayanan,
            'bpjsClaimTargetLayanan' => $bpjsClaimTargetLayanan,
            'clinicOptions' => $clinicContext['clinicOptions'],
            'showClinicFilter' => $clinicContext['showClinicFilter'],
            'selectedClinicFilter' => $clinicContext['viewingAllClinics']
                ? 'all'
                : (string) ($clinicContext['selectedClinicId'] ?: ''),
            'selectedClinicLabel' => $clinicContext['selectedClinicLabel'],
            'viewingAllClinics' => $clinicContext['viewingAllClinics'],
        ]);
    }

    public function rekapObatPusat(Request $request): View
    {
        $selectedMonth = $this->normalizeSelectedMonth($request->string('bulan')->toString());
        $clinicContext = $this->clinicContext($request, true);
        $isMasterView = $request->user()?->isMaster() ?? false;

        if ($isMasterView) {
            $clinicContext['selectedClinicId'] = null;
            $clinicContext['selectedClinicLabel'] = 'Semua Klinik';
            $clinicContext['viewingAllClinics'] = true;
            $clinicContext['showClinicFilter'] = false;
        }

        $warnings = collect();
        $rows = collect();
        $clinicSummaryRows = collect();
        $clinicDetailRows = collect();
        $successfulClinicCount = 0;
        $configuredClinicIds = ClinicDatabaseConnection::query()
            ->active()
            ->where('connection_role', 'simrs')
            ->pluck('clinic_profile_id')
            ->map(fn ($id) => (int) $id)
            ->flip();

        if ($clinicContext['viewingAllClinics']) {
            $aggregatedRows = collect();

            foreach ($clinicContext['clinicOptions'] as $clinic) {
                $clinicLabel = $clinic->nama_pendek ?: $clinic->nama_klinik;

                if (! $configuredClinicIds->has((int) $clinic->id)) {
                    $warnings->push('Koneksi SIMRS untuk ' . $clinicLabel . ' belum diatur, jadi data obatnya dilewati.');

                    continue;
                }

                try {
                    $successfulClinicCount++;
                    $clinicRows = $this->simrsRekapObatRows($selectedMonth, (int) $clinic->id);
                    $clinicDetailRows->put((string) $clinic->id, [
                        'clinic_id' => (int) $clinic->id,
                        'clinic_name' => $clinicLabel,
                        'rows' => $clinicRows->values()->all(),
                        'total_items' => $clinicRows->count(),
                        'total_jumlah' => (float) $clinicRows->sum('total_jumlah'),
                        'total_rupiah' => (float) $clinicRows->sum('total_rupiah'),
                    ]);

                    $clinicSummaryRows->push([
                        'clinic_id' => (int) $clinic->id,
                        'clinic_name' => $clinicLabel,
                        'total_items' => $clinicRows->count(),
                        'total_jumlah' => (float) $clinicRows->sum('total_jumlah'),
                        'total_rupiah' => (float) $clinicRows->sum('total_rupiah'),
                    ]);

                    foreach ($clinicRows as $row) {
                        $key = filled($row['kode_brng'])
                            ? strtoupper(trim((string) $row['kode_brng']))
                            : 'item-' . md5((string) $row['nama_brng']);
                        $existing = $aggregatedRows->get($key, [
                            'kode_brng' => $row['kode_brng'],
                            'nama_brng' => $row['nama_brng'],
                            'total_jumlah' => 0.0,
                            'total_rupiah' => 0.0,
                            'clinic_names' => [],
                        ]);

                        $existing['total_jumlah'] += (float) $row['total_jumlah'];
                        $existing['total_rupiah'] += (float) $row['total_rupiah'];
                        $existing['clinic_names'][] = $clinicLabel;

                        $aggregatedRows->put($key, $existing);
                    }
                } catch (\Throwable $exception) {
                    $warnings->push(
                        'Koneksi obat pusat untuk ' . $clinicLabel . ' gagal dibaca: '
                        . $this->humanizeClinicConnectionError($exception)
                    );
                }
            }

            $clinicSummaryRows = $clinicSummaryRows
                ->sortByDesc('total_rupiah')
                ->values();

            $rows = $aggregatedRows
                ->map(function (array $row) {
                    $row['clinic_count'] = count(array_unique($row['clinic_names']));
                    $row['clinic_names'] = array_values(array_unique($row['clinic_names']));

                    return $row;
                })
                ->sortByDesc('total_rupiah')
                ->values();
        } elseif ($clinicContext['selectedClinicId']) {
            try {
                $successfulClinicCount = 1;
                $rows = $this->simrsRekapObatRows($selectedMonth, $clinicContext['selectedClinicId']);
            } catch (\Throwable $exception) {
                $warnings->push(
                    'Data obat untuk '
                    . $clinicContext['selectedClinicLabel']
                    . ' belum bisa dibaca: '
                    . $this->humanizeClinicConnectionError($exception)
                );
            }
        }

        return view('pages.rekap-obat-pusat', [
            'selectedMonth' => $selectedMonth->format('Y-m'),
            'periodLabel' => $this->periodLabel($selectedMonth),
            'rows' => $rows,
            'clinicSummaryRows' => $clinicSummaryRows,
            'clinicDetailRows' => $clinicDetailRows,
            'totalItems' => $rows->count(),
            'totalJumlah' => (float) $rows->sum('total_jumlah'),
            'totalRupiah' => (float) $rows->sum('total_rupiah'),
            'successfulClinicCount' => $successfulClinicCount,
            'warnings' => $warnings,
            'clinicOptions' => $clinicContext['clinicOptions'],
            'showClinicFilter' => $clinicContext['showClinicFilter'],
            'selectedClinicFilter' => $clinicContext['viewingAllClinics']
                ? 'all'
                : (string) ($clinicContext['selectedClinicId'] ?: ''),
            'selectedClinicLabel' => $clinicContext['selectedClinicLabel'],
            'viewingAllClinics' => $clinicContext['viewingAllClinics'],
            'isMasterView' => $isMasterView,
        ]);
    }

    public function rekapPasienPusat(Request $request): View
    {
        $selectedMonth = $this->normalizeSelectedMonth($request->string('bulan')->toString());
        $selectedStatusFilter = $this->normalizeRekapPasienStatusFilter($request->string('status_lanjut')->toString());
        $clinicContext = $this->clinicContext($request, true);
        $isMasterView = $request->user()?->isMaster() ?? false;

        if ($isMasterView) {
            $clinicContext['selectedClinicId'] = null;
            $clinicContext['selectedClinicLabel'] = 'Semua Klinik';
            $clinicContext['viewingAllClinics'] = true;
            $clinicContext['showClinicFilter'] = false;
        }

        $warnings = collect();
        $rows = collect();
        $successfulClinicCount = 0;
        $configuredClinicIds = ClinicDatabaseConnection::query()
            ->active()
            ->where('connection_role', 'simrs')
            ->pluck('clinic_profile_id')
            ->map(fn ($id) => (int) $id)
            ->flip();

        if ($clinicContext['viewingAllClinics']) {
            foreach ($clinicContext['clinicOptions'] as $clinic) {
                $clinicLabel = $clinic->nama_pendek ?: $clinic->nama_klinik;

                if (! $configuredClinicIds->has((int) $clinic->id)) {
                    $warnings->push('Koneksi SIMRS untuk ' . $clinicLabel . ' belum diatur, jadi data pasiennya dilewati.');

                    continue;
                }

                try {
                    $clinicRows = $this->simrsRekapPasienRows($selectedMonth, (int) $clinic->id)
                        ->map(fn (array $row) => array_merge($row, [
                            'clinic_id' => (int) $clinic->id,
                            'clinic_name' => $clinicLabel,
                        ]));
                    $successfulClinicCount++;
                    $rows = $rows->concat($clinicRows);
                } catch (\Throwable $exception) {
                    $warnings->push(
                        'Koneksi pasien pusat untuk ' . $clinicLabel . ' gagal dibaca: '
                        . $this->humanizeClinicConnectionError($exception)
                    );
                }
            }
        } elseif ($clinicContext['selectedClinicId']) {
            try {
                $successfulClinicCount = 1;
                $rows = $this->simrsRekapPasienRows($selectedMonth, $clinicContext['selectedClinicId']);
            } catch (\Throwable $exception) {
                $warnings->push(
                    'Data pasien untuk '
                    . $clinicContext['selectedClinicLabel']
                    . ' belum bisa dibaca: '
                    . $this->humanizeClinicConnectionError($exception)
                );
            }
        }

        $rows = $rows
            ->when($selectedStatusFilter !== 'all', function (Collection $collection) use ($selectedStatusFilter) {
                return $collection->where('status_lanjut_key', $selectedStatusFilter)->values();
            })
            ->sortBy(function (array $row) {
                $timestamp = filled($row['tanggal'] ?? null) ? strtotime((string) $row['tanggal']) : 0;

                return sprintf(
                    '%010d_%s',
                    max(0, 9999999999 - (int) $timestamp),
                    trim((string) ($row['no_rawat'] ?? ''))
                );
            })
            ->values();

        return view('pages.rekap-pasien-pusat', [
            'selectedMonth' => $selectedMonth->format('Y-m'),
            'selectedStatusFilter' => $selectedStatusFilter,
            'selectedStatusLabel' => match ($selectedStatusFilter) {
                'ralan' => 'Rawat Jalan',
                'ranap' => 'Rawat Inap',
                default => 'Semua Rawat',
            },
            'periodLabel' => $this->periodLabel($selectedMonth),
            'rows' => $rows,
            'totalRows' => $rows->count(),
            'totalRawatJalan' => $rows->where('status_lanjut_key', 'ralan')->count(),
            'totalRawatInap' => $rows->where('status_lanjut_key', 'ranap')->count(),
            'totalLakiLaki' => $rows->where('jk_key', 'l')->count(),
            'totalPerempuan' => $rows->where('jk_key', 'p')->count(),
            'totalBpjs' => $rows->filter(fn (array $row) => ($row['jenis_bayar_key'] ?? '') === 'bpjs')->count(),
            'totalUmum' => $rows->filter(fn (array $row) => ($row['jenis_bayar_key'] ?? '') === 'umum')->count(),
            'successfulClinicCount' => $successfulClinicCount,
            'warnings' => $warnings,
            'clinicOptions' => $clinicContext['clinicOptions'],
            'showClinicFilter' => $clinicContext['showClinicFilter'],
            'selectedClinicFilter' => $clinicContext['viewingAllClinics']
                ? 'all'
                : (string) ($clinicContext['selectedClinicId'] ?: ''),
            'selectedClinicLabel' => $clinicContext['selectedClinicLabel'],
            'viewingAllClinics' => $clinicContext['viewingAllClinics'],
            'isMasterView' => $isMasterView,
        ]);
    }

    public function rekapPenyakitPusat(Request $request): View
    {
        $selectedMonth = $this->normalizeSelectedMonth($request->string('bulan')->toString());
        $selectedAgeFilter = $this->normalizeRekapPenyakitAgeFilter($request->string('kelompok_usia')->toString());
        $clinicContext = $this->clinicContext($request, true);
        $isMasterView = $request->user()?->isMaster() ?? false;

        if ($isMasterView) {
            $clinicContext['selectedClinicId'] = null;
            $clinicContext['selectedClinicLabel'] = 'Semua Klinik';
            $clinicContext['viewingAllClinics'] = true;
            $clinicContext['showClinicFilter'] = false;
        }

        $warnings = collect();
        $rows = collect();
        $successfulClinicCount = 0;
        $configuredClinicIds = ClinicDatabaseConnection::query()
            ->active()
            ->where('connection_role', 'simrs')
            ->pluck('clinic_profile_id')
            ->map(fn ($id) => (int) $id)
            ->flip();

        if ($clinicContext['viewingAllClinics']) {
            $aggregatedRows = collect();

            foreach ($clinicContext['clinicOptions'] as $clinic) {
                $clinicLabel = $clinic->nama_pendek ?: $clinic->nama_klinik;

                if (! $configuredClinicIds->has((int) $clinic->id)) {
                    $warnings->push('Koneksi SIMRS untuk ' . $clinicLabel . ' belum diatur, jadi data penyakitnya dilewati.');

                    continue;
                }

                try {
                    $clinicRows = $this->simrsRekapPenyakitRows($selectedMonth, (int) $clinic->id, $selectedAgeFilter);
                    $successfulClinicCount++;

                    foreach ($clinicRows as $row) {
                        $key = filled($row['icd'])
                            ? strtoupper(trim((string) $row['icd']))
                            : 'penyakit-' . md5((string) $row['nama_penyakit']);
                        $existing = $aggregatedRows->get($key, [
                            'icd' => $row['icd'],
                            'nama_penyakit' => $row['nama_penyakit'],
                            'total_kasus' => 0,
                            'total_laki_laki' => 0,
                            'total_perempuan' => 0,
                            'total_anak' => 0,
                            'total_dewasa' => 0,
                            'clinic_names' => [],
                        ]);

                        $existing['total_kasus'] += (int) ($row['total_kasus'] ?? 0);
                        $existing['total_laki_laki'] += (int) ($row['total_laki_laki'] ?? 0);
                        $existing['total_perempuan'] += (int) ($row['total_perempuan'] ?? 0);
                        $existing['total_anak'] += (int) ($row['total_anak'] ?? 0);
                        $existing['total_dewasa'] += (int) ($row['total_dewasa'] ?? 0);
                        $existing['clinic_names'][] = $clinicLabel;

                        $aggregatedRows->put($key, $existing);
                    }
                } catch (\Throwable $exception) {
                    $warnings->push(
                        'Koneksi penyakit pusat untuk ' . $clinicLabel . ' gagal dibaca: '
                        . $this->humanizeClinicConnectionError($exception)
                    );
                }
            }

            $rows = $aggregatedRows
                ->map(function (array $row) {
                    $row['clinic_names'] = array_values(array_unique($row['clinic_names']));
                    $row['clinic_count'] = count($row['clinic_names']);

                    return $row;
                })
                ->sort(function (array $left, array $right) {
                    $byTotalKasus = ($right['total_kasus'] ?? 0) <=> ($left['total_kasus'] ?? 0);

                    if ($byTotalKasus !== 0) {
                        return $byTotalKasus;
                    }

                    return strcasecmp(
                        (string) ($left['nama_penyakit'] ?? ''),
                        (string) ($right['nama_penyakit'] ?? '')
                    );
                })
                ->values();
        } elseif ($clinicContext['selectedClinicId']) {
            try {
                $successfulClinicCount = 1;
                $rows = $this->simrsRekapPenyakitRows(
                    $selectedMonth,
                    $clinicContext['selectedClinicId'],
                    $selectedAgeFilter
                );
            } catch (\Throwable $exception) {
                $warnings->push(
                    'Data penyakit untuk '
                    . $clinicContext['selectedClinicLabel']
                    . ' belum bisa dibaca: '
                    . $this->humanizeClinicConnectionError($exception)
                );
            }
        }

        return view('pages.rekap-penyakit-pusat', [
            'selectedMonth' => $selectedMonth->format('Y-m'),
            'selectedAgeFilter' => $selectedAgeFilter,
            'selectedAgeLabel' => $this->humanizeRekapPenyakitAgeFilter($selectedAgeFilter),
            'periodLabel' => $this->periodLabel($selectedMonth),
            'rows' => $rows,
            'totalDiseases' => $rows->count(),
            'totalCases' => (int) $rows->sum('total_kasus'),
            'totalMale' => (int) $rows->sum('total_laki_laki'),
            'totalFemale' => (int) $rows->sum('total_perempuan'),
            'totalChildren' => (int) $rows->sum('total_anak'),
            'totalAdults' => (int) $rows->sum('total_dewasa'),
            'successfulClinicCount' => $successfulClinicCount,
            'warnings' => $warnings,
            'clinicOptions' => $clinicContext['clinicOptions'],
            'showClinicFilter' => $clinicContext['showClinicFilter'],
            'selectedClinicFilter' => $clinicContext['viewingAllClinics']
                ? 'all'
                : (string) ($clinicContext['selectedClinicId'] ?: ''),
            'selectedClinicLabel' => $clinicContext['selectedClinicLabel'],
            'viewingAllClinics' => $clinicContext['viewingAllClinics'],
            'isMasterView' => $isMasterView,
        ]);
    }

    public function syncRekapPasien(Request $request): RedirectResponse
    {
        $user = $request->user();

        abort_if($user?->isMaster(), 403, 'Role master tidak menggunakan tombol update rekap pasien.');

        $selectedDate = $this->normalizeSelectedDate($request->string('tanggal')->toString());
        $selectedDateRange = $this->normalizeSelectedDateRange(
            $request->string('tanggal_awal')->toString(),
            $request->string('tanggal_akhir')->toString(),
            $selectedDate
        );
        $selectedStartDate = $selectedDateRange['start'];
        $selectedEndDate = $selectedDateRange['end'];
        $selectedDate = $selectedEndDate;
        $selectedLocalStatus = $this->normalizeLocalStatusFilter($request->string('local_status')->toString());
        $selectedPenjamin = trim($request->string('data_penjamin')->toString());
        $clinicProfileId = $this->resolveOperationalClinicId($request);
        $masterLayananByCode = MasterLayanan::query()
            ->active()
            ->get()
            ->keyBy('kode_layanan');

        $savedTransactions = TransaksiPasien::query()
            ->where('clinic_profile_id', $clinicProfileId)
            ->whereDate('tanggal', '>=', $selectedStartDate)
            ->whereDate('tanggal', '<=', $selectedEndDate)
            ->get()
            ->keyBy('simrs_no_rawat');

        $visitRows = $this->simrsVisitRows($selectedStartDate, $clinicProfileId, $selectedEndDate)
            ->filter(function (array $row) use ($savedTransactions, $selectedLocalStatus) {
                if ($selectedLocalStatus === 'saved') {
                    return $savedTransactions->has($row['simrs_no_rawat']);
                }

                if ($selectedLocalStatus === 'unsaved') {
                    return ! $savedTransactions->has($row['simrs_no_rawat']);
                }

                return true;
            })
            ->values();

        $now = now();
        $payload = $visitRows->map(function (array $row) use ($clinicProfileId, $masterLayananByCode, $now) {
            $layananCode = filled($row['layanan_medis'] ?? null)
                ? strtoupper(trim((string) $row['layanan_medis']))
                : null;
            $masterLayanan = $layananCode ? $masterLayananByCode->get($layananCode) : null;
            $tanggal = Carbon::parse($row['tanggal']);

            return [
                'clinic_profile_id' => $clinicProfileId,
                'master_layanan_id' => $masterLayanan?->id,
                'tanggal' => $tanggal->toDateString(),
                'bulan' => $tanggal->month,
                'tahun' => $tanggal->year,
                'no_rawat' => $row['simrs_no_rawat'],
                'no_rm' => $row['no_rm'] ?: null,
                'nama_pasien' => $row['nama_pasien'] ?: null,
                'layanan_medis' => $masterLayanan?->kode_layanan,
                'synced_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        })->all();

        RekapPasien::query()
            ->where('clinic_profile_id', $clinicProfileId)
            ->whereDate('tanggal', '>=', $selectedStartDate)
            ->whereDate('tanggal', '<=', $selectedEndDate)
            ->delete();

        if (! empty($payload)) {
            RekapPasien::query()->upsert(
                $payload,
                ['clinic_profile_id', 'no_rawat'],
                ['master_layanan_id', 'tanggal', 'bulan', 'tahun', 'no_rm', 'nama_pasien', 'layanan_medis', 'synced_at', 'updated_at']
            );
        }

        RekapPasienUpdate::query()->updateOrCreate(
            ['clinic_profile_id' => $clinicProfileId],
            [
                'user_id' => $user?->id,
                'tanggal_data' => $selectedEndDate,
                'total_data' => count($payload),
                'synced_at' => $now,
            ]
        );

        return redirect()
            ->route('transaksi-pasien', [
                'tanggal' => $selectedDate,
                'tanggal_awal' => $selectedStartDate,
                'tanggal_akhir' => $selectedEndDate,
                'data_bulan' => Carbon::parse($selectedDate)->format('Y-m'),
                'data_penjamin' => $selectedPenjamin ?: null,
                'local_status' => $selectedLocalStatus ?: null,
                'clinic_id' => $clinicProfileId,
                'active_tab' => 'panel-transaksi-pasien',
            ])
            ->with('success', 'Rekap pasien berhasil diperbarui dari data transaksi pasien yang tampil.');
    }

    public function storeTransaksiPasien(Request $request): RedirectResponse
    {
        $data = $this->validatedTransaksi($request);
        $redirectClinicId = $this->resolveTransaksiRedirectClinicFilter($request, $data['clinic_profile_id']);
        $komponenSync = $data['_komponen_sync'] ?? [];
        $administrasiSync = $data['_administrasi_sync'] ?? [];
        unset($data['_komponen_sync']);
        unset($data['_administrasi_sync']);

        $transaksiPasien = TransaksiPasien::query()->create($data);
        $this->syncTransaksiKomponen($transaksiPasien, $komponenSync);
        $this->syncTransaksiAdministrasi($transaksiPasien, $administrasiSync);

        return redirect()
            ->route('transaksi-pasien', $this->transaksiPasienRedirectParameters($request, $data, $redirectClinicId))
            ->with('success', 'Transaksi pasien berhasil disimpan.');
    }

    public function updateTransaksiPasien(Request $request, TransaksiPasien $transaksiPasien): RedirectResponse
    {
        $this->ensureOperationalRecordAccess($request->user(), $transaksiPasien->clinic_profile_id);
        $data = $this->validatedTransaksi($request, $transaksiPasien->id);
        $redirectClinicId = $this->resolveTransaksiRedirectClinicFilter($request, $data['clinic_profile_id']);
        $komponenSync = $data['_komponen_sync'] ?? [];
        $administrasiSync = $data['_administrasi_sync'] ?? [];
        unset($data['_komponen_sync']);
        unset($data['_administrasi_sync']);

        $transaksiPasien->update($data);
        $this->syncTransaksiKomponen($transaksiPasien, $komponenSync);
        $this->syncTransaksiAdministrasi($transaksiPasien, $administrasiSync);

        return redirect()
            ->route('transaksi-pasien', $this->transaksiPasienRedirectParameters($request, $data, $redirectClinicId))
            ->with('success', 'Transaksi pasien berhasil diperbarui.');
    }

    public function destroyTransaksiPasien(Request $request, TransaksiPasien $transaksiPasien): RedirectResponse
    {
        $this->ensureOperationalRecordAccess($request->user(), $transaksiPasien->clinic_profile_id);
        $tanggal = optional($transaksiPasien->tanggal)->toDateString() ?: now()->toDateString();
        $clinicId = $this->resolveTransaksiRedirectClinicFilter($request, $transaksiPasien->clinic_profile_id);
        $transaksiPasien->delete();

        return redirect()
            ->route('transaksi-pasien', $this->transaksiPasienRedirectParameters(
                $request,
                ['tanggal' => $tanggal],
                $clinicId
            ))
            ->with('success', 'Transaksi pasien berhasil dihapus.');
    }

    public function storePengeluaran(Request $request): RedirectResponse
    {
        $data = $this->validatedPengeluaran($request);

        Pengeluaran::query()->create($data);

        return redirect()
            ->route('input-pengeluaran', [
                'bulan' => Carbon::parse($data['tanggal'])->format('Y-m'),
                'kategori' => $data['kategori_pengeluaran'] ?: null,
                'clinic_id' => $data['clinic_profile_id'],
                'active_tab' => 'panel-data-pengeluaran',
            ])
            ->with('success', 'Pengeluaran berhasil disimpan.');
    }

    public function updatePengeluaran(Request $request, Pengeluaran $pengeluaran): RedirectResponse
    {
        $this->ensureOperationalRecordAccess($request->user(), $pengeluaran->clinic_profile_id);
        $data = $this->validatedPengeluaran($request);

        $pengeluaran->update($data);

        return redirect()
            ->route('input-pengeluaran', [
                'bulan' => Carbon::parse($data['tanggal'])->format('Y-m'),
                'kategori' => $data['kategori_pengeluaran'] ?: null,
                'clinic_id' => $data['clinic_profile_id'],
                'active_tab' => 'panel-data-pengeluaran',
            ])
            ->with('success', 'Pengeluaran berhasil diperbarui.');
    }

    public function destroyPengeluaran(Request $request, Pengeluaran $pengeluaran): RedirectResponse
    {
        $this->ensureOperationalRecordAccess($request->user(), $pengeluaran->clinic_profile_id);
        $bulan = optional($pengeluaran->tanggal)->format('Y-m') ?: now()->format('Y-m');
        $kategori = $pengeluaran->kategori_pengeluaran;
        $clinicId = $pengeluaran->clinic_profile_id;
        $pengeluaran->delete();

        return redirect()
            ->route('input-pengeluaran', [
                'bulan' => $bulan,
                'kategori' => $kategori ?: null,
                'clinic_id' => $clinicId,
                'active_tab' => 'panel-data-pengeluaran',
            ])
            ->with('success', 'Pengeluaran berhasil dihapus.');
    }

    private function simrsRekapObatRows(Carbon $selectedMonth, ?int $clinicProfileId = null): Collection
    {
        $simrs = $this->resolveSimrsConnection($clinicProfileId);
        $startDate = $selectedMonth->copy()->startOfMonth()->toDateString();
        $endDate = $selectedMonth->copy()->endOfMonth()->toDateString();

        return $simrs->table('detail_pemberian_obat as dp')
            ->leftJoin('databarang as db', 'dp.kode_brng', '=', 'db.kode_brng')
            ->leftJoin('reg_periksa as rp', 'dp.no_rawat', '=', 'rp.no_rawat')
            ->leftJoin('poliklinik as p', 'rp.kd_poli', '=', 'p.kd_poli')
            ->whereBetween('rp.tgl_registrasi', [$startDate, $endDate])
            ->groupBy('dp.kode_brng', 'db.nama_brng')
            ->selectRaw('
                dp.kode_brng,
                COALESCE(db.nama_brng, "") as nama_brng,
                COALESCE(SUM(dp.jml), 0) as total_jumlah,
                COALESCE(SUM(dp.total), 0) as total_rupiah
            ')
            ->get()
            ->map(function ($row) {
                return [
                    'kode_brng' => $row->kode_brng ? trim((string) $row->kode_brng) : '-',
                    'nama_brng' => $row->nama_brng ? trim((string) $row->nama_brng) : 'Tanpa Nama Obat',
                    'total_jumlah' => (float) $row->total_jumlah,
                    'total_rupiah' => (float) $row->total_rupiah,
                ];
            })
            ->sortByDesc('total_rupiah')
            ->values();
    }

    private function simrsRekapPasienRows(Carbon $selectedMonth, ?int $clinicProfileId = null): Collection
    {
        $simrs = $this->resolveSimrsConnection($clinicProfileId);
        $startDate = $selectedMonth->copy()->startOfMonth()->toDateString();
        $endDate = $selectedMonth->copy()->endOfMonth()->toDateString();

        return $simrs->table('reg_periksa as rp')
            ->leftJoin('pasien as ps', 'ps.no_rkm_medis', '=', 'rp.no_rkm_medis')
            ->leftJoin('penjab as pj', 'pj.kd_pj', '=', 'rp.kd_pj')
            ->whereBetween('rp.tgl_registrasi', [$startDate, $endDate])
            ->selectRaw('
                rp.tgl_registrasi,
                rp.no_rawat,
                rp.no_rkm_medis,
                rp.status_lanjut,
                rp.kd_pj,
                COALESCE(ps.jk, "") as jk,
                COALESCE(ps.nm_pasien, "") as nm_pasien,
                COALESCE(pj.png_jawab, "") as penjamin
            ')
            ->orderByDesc('rp.tgl_registrasi')
            ->orderBy('rp.no_rawat')
            ->get()
            ->map(function ($row) {
                $tanggal = filled($row->tgl_registrasi)
                    ? Carbon::parse($row->tgl_registrasi)->toDateString()
                    : null;
                $jenisBayar = $this->classifySimrsJenisBayar(
                    $row->penjamin ?: null,
                    $row->kd_pj ?: null
                );

                return [
                    'tanggal' => $tanggal,
                    'no_rawat' => filled($row->no_rawat) ? trim((string) $row->no_rawat) : '-',
                    'no_rm' => filled($row->no_rkm_medis) ? trim((string) $row->no_rkm_medis) : '-',
                    'nama_pasien' => filled($row->nm_pasien) ? trim((string) $row->nm_pasien) : 'Tanpa Nama',
                    'jk' => $this->humanizeSimrsJenisKelamin($row->jk ?: null),
                    'jk_key' => $this->normalizeSimrsJenisKelaminKey($row->jk ?: null),
                    'status_lanjut' => $this->humanizeSimrsStatusLanjut($row->status_lanjut ?: null),
                    'status_lanjut_key' => $this->normalizeSimrsStatusLanjutKey($row->status_lanjut ?: null),
                    'penjamin' => $jenisBayar['label'],
                    'jenis_bayar_key' => $jenisBayar['key'],
                ];
            })
            ->values();
    }

    private function simrsRekapPenyakitRows(
        Carbon $selectedMonth,
        ?int $clinicProfileId = null,
        string $selectedAgeFilter = 'all'
    ): Collection {
        $simrs = $this->resolveSimrsConnection($clinicProfileId);
        $startDate = $selectedMonth->copy()->startOfMonth()->toDateString();
        $endDate = $selectedMonth->copy()->endOfMonth()->toDateString();

        return $simrs->table('diagnosa_pasien as dp')
            ->join('reg_periksa as rp', 'rp.no_rawat', '=', 'dp.no_rawat')
            ->leftJoin('pasien as ps', 'ps.no_rkm_medis', '=', 'rp.no_rkm_medis')
            ->leftJoin('penyakit as py', 'py.kd_penyakit', '=', 'dp.kd_penyakit')
            ->whereBetween('rp.tgl_registrasi', [$startDate, $endDate])
            ->selectRaw('
                dp.no_rawat,
                dp.kd_penyakit,
                COALESCE(py.nm_penyakit, "") as nm_penyakit,
                COALESCE(ps.jk, "") as jk,
                rp.umurdaftar,
                COALESCE(rp.sttsumur, "") as sttsumur
            ')
            ->distinct()
            ->get()
            ->map(function ($row) {
                $ageGroupKey = $this->determineRekapPenyakitAgeGroup(
                    $row->umurdaftar ?? null,
                    $row->sttsumur ?: null
                );

                return [
                    'icd' => filled($row->kd_penyakit) ? trim((string) $row->kd_penyakit) : '-',
                    'nama_penyakit' => filled($row->nm_penyakit) ? trim((string) $row->nm_penyakit) : 'Tanpa Nama Penyakit',
                    'jk' => strtoupper(trim((string) ($row->jk ?: ''))),
                    'age_group_key' => $ageGroupKey,
                ];
            })
            ->when($selectedAgeFilter !== 'all', function (Collection $collection) use ($selectedAgeFilter) {
                return $collection->where('age_group_key', $selectedAgeFilter)->values();
            })
            ->reduce(function (Collection $carry, array $row) {
                $key = filled($row['icd']) && $row['icd'] !== '-'
                    ? strtoupper(trim((string) $row['icd']))
                    : 'penyakit-' . md5((string) $row['nama_penyakit']);
                $existing = $carry->get($key, [
                    'icd' => $row['icd'],
                    'nama_penyakit' => $row['nama_penyakit'],
                    'total_kasus' => 0,
                    'total_laki_laki' => 0,
                    'total_perempuan' => 0,
                    'total_anak' => 0,
                    'total_dewasa' => 0,
                ]);

                $existing['total_kasus']++;

                if (($row['jk'] ?? '') === 'L') {
                    $existing['total_laki_laki']++;
                } elseif (($row['jk'] ?? '') === 'P') {
                    $existing['total_perempuan']++;
                }

                if (($row['age_group_key'] ?? '') === 'anak') {
                    $existing['total_anak']++;
                } else {
                    $existing['total_dewasa']++;
                }

                $carry->put($key, $existing);

                return $carry;
            }, collect())
            ->sort(function (array $left, array $right) {
                $byTotalKasus = ($right['total_kasus'] ?? 0) <=> ($left['total_kasus'] ?? 0);

                if ($byTotalKasus !== 0) {
                    return $byTotalKasus;
                }

                return strcasecmp(
                    (string) ($left['nama_penyakit'] ?? ''),
                    (string) ($right['nama_penyakit'] ?? '')
                );
            })
            ->values();
    }

    private function simrsVisitRows(
        string $selectedDate,
        ?int $clinicProfileId = null,
        ?string $selectedEndDate = null
    ): Collection
    {
        $selectedDateRange = $this->normalizeSelectedDateRange($selectedDate, $selectedEndDate, $selectedDate);
        $startDate = $selectedDateRange['start'];
        $endDate = $selectedDateRange['end'];
        $simrs = $this->resolveSimrsConnection($clinicProfileId);
        $masterLayanan = MasterLayanan::query()
            ->active()
            ->orderBy('urutan_laporan')
            ->orderBy('nama_layanan')
            ->get();

        $diagnosaSub = $simrs->table('diagnosa_pasien as dp')
            ->leftJoin('penyakit as py', 'py.kd_penyakit', '=', 'dp.kd_penyakit')
            ->selectRaw("
                dp.no_rawat,
                GROUP_CONCAT(DISTINCT dp.kd_penyakit ORDER BY dp.prioritas SEPARATOR ', ') as icd,
                GROUP_CONCAT(DISTINCT py.nm_penyakit ORDER BY dp.prioritas SEPARATOR '; ') as diagnosa
            ")
            ->groupBy('dp.no_rawat');

        $labSub = $simrs->table('periksa_lab as plab')
            ->leftJoin('jns_perawatan_lab as jpl', 'jpl.kd_jenis_prw', '=', 'plab.kd_jenis_prw')
            ->selectRaw("
                plab.no_rawat,
                GROUP_CONCAT(DISTINCT jpl.nm_perawatan SEPARATOR '; ') as lab
            ")
            ->groupBy('plab.no_rawat');

        $farmasiSub = $simrs->table('pemeriksaan_ralan as pr')
            ->selectRaw("
                pr.no_rawat,
                GROUP_CONCAT(NULLIF(TRIM(pr.rtl), '') ORDER BY pr.tgl_perawatan, pr.jam_rawat SEPARATOR '\n\n') as farmasi
            ")
            ->groupBy('pr.no_rawat');

        $obatSub = $simrs->table('detail_pemberian_obat as dpo')
            ->selectRaw("
                dpo.no_rawat,
                COALESCE(SUM(dpo.total), 0) as uang_obat
            ")
            ->groupBy('dpo.no_rawat');

        $rawatDr = $simrs->table('rawat_jl_dr')
            ->selectRaw('no_rawat, COALESCE(biaya_rawat, 0) as biaya_rawat, COALESCE(tarif_tindakandr, 0) as jasa_dokter');

        $rawatPr = $simrs->table('rawat_jl_pr')
            ->selectRaw('no_rawat, COALESCE(biaya_rawat, 0) as biaya_rawat, 0 as jasa_dokter');

        $rawatDrpr = $simrs->table('rawat_jl_drpr')
            ->selectRaw('no_rawat, COALESCE(biaya_rawat, 0) as biaya_rawat, COALESCE(tarif_tindakandr, 0) as jasa_dokter');

        $rawatSub = $simrs->query()
            ->fromSub($rawatDr->unionAll($rawatPr)->unionAll($rawatDrpr), 'rawat_rows')
            ->selectRaw('
                no_rawat,
                COALESCE(SUM(biaya_rawat), 0) as uang_periksa,
                COALESCE(SUM(jasa_dokter), 0) as jasa_dokter,
                COUNT(*) as jml_visit
            ')
            ->groupBy('no_rawat');

        $kamarSub = $simrs->table('kamar_inap')
            ->selectRaw('
                no_rawat,
                COALESCE(SUM(lama), 0) as jml_hari,
                COALESCE(SUM(ttl_biaya), 0) as rawat_inap
            ')
            ->groupBy('no_rawat');

        return $simrs->table('reg_periksa as rp')
            ->leftJoin('pasien as ps', 'ps.no_rkm_medis', '=', 'rp.no_rkm_medis')
            ->leftJoin('dokter as dk', 'dk.kd_dokter', '=', 'rp.kd_dokter')
            ->leftJoin('poliklinik as pl', 'pl.kd_poli', '=', 'rp.kd_poli')
            ->leftJoin('penjab as pj', 'pj.kd_pj', '=', 'rp.kd_pj')
            ->leftJoinSub($diagnosaSub, 'diag', fn ($join) => $join->on('diag.no_rawat', '=', 'rp.no_rawat'))
            ->leftJoinSub($labSub, 'lab_data', fn ($join) => $join->on('lab_data.no_rawat', '=', 'rp.no_rawat'))
            ->leftJoinSub($farmasiSub, 'farmasi_data', fn ($join) => $join->on('farmasi_data.no_rawat', '=', 'rp.no_rawat'))
            ->leftJoinSub($obatSub, 'obat_data', fn ($join) => $join->on('obat_data.no_rawat', '=', 'rp.no_rawat'))
            ->leftJoinSub($rawatSub, 'rawat_data', fn ($join) => $join->on('rawat_data.no_rawat', '=', 'rp.no_rawat'))
            ->leftJoinSub($kamarSub, 'ranap_data', fn ($join) => $join->on('ranap_data.no_rawat', '=', 'rp.no_rawat'))
            ->whereBetween('rp.tgl_registrasi', [$startDate, $endDate])
            ->orderByDesc('rp.tgl_registrasi')
            ->orderBy('rp.jam_reg')
            ->selectRaw('
                rp.no_rawat,
                rp.no_reg,
                rp.kd_poli,
                rp.tgl_registrasi,
                rp.jam_reg,
                rp.no_rkm_medis as no_rm,
                rp.biaya_reg,
                rp.stts_daftar,
                rp.status_lanjut,
                rp.status_bayar,
                dk.nm_dokter as dokter,
                pl.nm_poli as simrs_nama_poli,
                pj.png_jawab as penjamin,
                ps.nm_pasien,
                ps.jk,
                ps.alamat,
                COALESCE(diag.icd, "") as icd,
                COALESCE(diag.diagnosa, "") as diagnosa,
                COALESCE(lab_data.lab, "") as lab,
                COALESCE(farmasi_data.farmasi, "") as farmasi,
                COALESCE(obat_data.uang_obat, 0) as uang_obat,
                COALESCE(rawat_data.uang_periksa, 0) as uang_periksa,
                COALESCE(rawat_data.jasa_dokter, 0) as jasa_dokter,
                COALESCE(rawat_data.jml_visit, 0) as jml_visit,
                COALESCE(ranap_data.jml_hari, 0) as jml_hari,
                COALESCE(ranap_data.rawat_inap, 0) as rawat_inap
            ')
            ->get()
            ->map(function ($row) use ($masterLayanan) {
                $tanggal = Carbon::parse($row->tgl_registrasi);
                $mappedLayanan = $this->resolveMappedLayananFromSimrs(
                    $row->kd_poli,
                    $masterLayanan
                );
                $fallbackKodePoli = $row->kd_poli ? strtoupper(trim((string) $row->kd_poli)) : null;
                $mappedNamaLayanan = $mappedLayanan['nama_layanan'] ?? null;

                return [
                    'simrs_no_rawat' => $row->no_rawat,
                    'simrs_no_reg' => $row->no_reg,
                    'tanggal' => $tanggal->toDateString(),
                    'bulan' => $tanggal->month,
                    'harian' => $tanggal->locale('id')->translatedFormat('l'),
                    'layanan_medis' => $mappedLayanan['kode_layanan']
                        ?? $fallbackKodePoli,
                    'layanan_label' => $mappedNamaLayanan ?: 'Belum Dimapping',
                    'dokter' => $row->dokter,
                    'penjamin' => $row->penjamin,
                    'no_rm' => $row->no_rm,
                    'nama_pasien' => $row->nm_pasien,
                    'jk' => $row->jk,
                    'statis' => $row->stts_daftar,
                    'genap' => '',
                    'status_pasien' => $row->status_lanjut,
                    'alamat' => $row->alamat,
                    'lab' => $row->lab,
                    'icd' => $row->icd,
                    'diagnosa' => $row->diagnosa,
                    'farmasi' => $row->farmasi,
                    'uang_daftar' => (float) $row->biaya_reg,
                    'uang_periksa' => (float) $row->uang_periksa,
                    'uang_obat' => (float) $row->uang_obat,
                    'uang_bersalin' => 0,
                    'jasa_dokter' => (float) $row->jasa_dokter,
                    'jml_hari' => (int) $row->jml_hari,
                    'rawat_inap' => (float) $row->rawat_inap,
                    'jml_visit' => (int) $row->jml_visit,
                    'honor_dr_visit' => 0,
                    'oksigen' => 0,
                    'perlengk_bayi' => 0,
                    'jaspel_nakes' => 0,
                    'bmhp' => 0,
                    'pkl' => 0,
                    'lain_lain' => 0,
                    'jumlah_rp' => collect([
                        (float) $row->biaya_reg,
                        (float) $row->uang_periksa,
                        (float) $row->uang_obat,
                        (float) $row->jasa_dokter,
                        (float) $row->rawat_inap,
                    ])->sum(),
                    'jumlah_kredit' => 0,
                    'petugas_admin' => '',
                    'keterangan' => '',
                    'meta' => [
                        'jam_reg' => $row->jam_reg,
                        'status_bayar' => $row->status_bayar,
                        'penjamin' => $row->penjamin,
                        'kode_layanan' => $mappedLayanan['kode_layanan'] ?? null,
                        'nama_layanan' => $mappedNamaLayanan,
                        'simrs_kd_poli' => $row->kd_poli,
                        'simrs_nama_poli' => $row->simrs_nama_poli,
                    ],
                ];
            })
            ->values();
    }

    private function resolveSimrsConnection(?int $clinicProfileId = null)
    {
        return app(ClinicDatabaseManager::class)->connectionFor($clinicProfileId, 'simrs');
    }

    private function clinicOptionsForUser(?User $user = null): Collection
    {
        $user ??= auth()->user();

        if (! $user) {
            return collect();
        }

        if ($user->isMaster()) {
            return ClinicProfile::query()
                ->active()
                ->orderBy('nama_klinik')
                ->get();
        }

        if (! $user->clinic_profile_id) {
            return collect();
        }

        return ClinicProfile::query()
            ->active()
            ->whereKey($user->clinic_profile_id)
            ->get();
    }

    private function clinicContext(Request $request, bool $allowAll = false, string $parameter = 'clinic_id'): array
    {
        $user = $request->user();
        $clinicOptions = $this->clinicOptionsForUser($user);
        $selectedClinic = null;
        $selectedClinicId = null;
        $viewingAllClinics = false;

        if ($user?->isMaster()) {
            $rawClinic = strtolower(trim((string) $request->query($parameter, '')));

            if ($allowAll && $rawClinic === 'all') {
                $viewingAllClinics = true;
            } else {
                $requestedClinicId = (int) $rawClinic;
                $selectedClinic = $requestedClinicId > 0
                    ? $clinicOptions->firstWhere('id', $requestedClinicId)
                    : null;
                $selectedClinicId = $selectedClinic?->id;
            }
        } else {
            $selectedClinic = $clinicOptions->first();
            $selectedClinicId = $selectedClinic?->id;
        }

        return [
            'clinicOptions' => $clinicOptions,
            'selectedClinic' => $selectedClinic,
            'selectedClinicId' => $selectedClinicId,
            'selectedClinicLabel' => $viewingAllClinics
                ? 'Semua Klinik'
                : ($selectedClinic?->nama_pendek
                    ?: $selectedClinic?->nama_klinik
                    ?: ($user?->isMaster() ? 'Dashboard Pusat' : 'Klinik')),
            'viewingAllClinics' => $viewingAllClinics,
            'showClinicFilter' => $user?->isMaster() && $clinicOptions->isNotEmpty(),
        ];
    }

    private function resolveOperationalClinicId(Request $request): int
    {
        $user = $request->user();
        $clinicOptions = $this->clinicOptionsForUser($user);

        if ($user?->isMaster()) {
            $requestedClinicId = (int) $request->input('clinic_profile_id', $request->query('clinic_id'));
            $selectedClinic = $requestedClinicId > 0
                ? $clinicOptions->firstWhere('id', $requestedClinicId)
                : null;

            abort_if(! $selectedClinic, 422, 'Pilih klinik tujuan terlebih dahulu.');

            return (int) $selectedClinic->id;
        }

        abort_if(! $user?->clinic_profile_id, 403, 'Akun ini belum terhubung ke klinik.');

        return (int) $user->clinic_profile_id;
    }

    private function scopeQueryToClinic($query, ?int $clinicProfileId)
    {
        if ($clinicProfileId) {
            $query->where('clinic_profile_id', $clinicProfileId);
        }

        return $query;
    }

    private function ensureOperationalRecordAccess(?User $user, ?int $clinicProfileId): void
    {
        abort_if(! $user, 403);

        if ($user->isMaster()) {
            return;
        }

        abort_if(
            ! $user->clinic_profile_id || (int) $user->clinic_profile_id !== (int) $clinicProfileId,
            403,
            'Anda tidak punya akses ke data klinik ini.'
        );
    }

    private function resolveTransaksiRedirectClinicFilter(Request $request, ?int $fallbackClinicId): string|int|null
    {
        $user = $request->user();

        if ($user?->isMaster()) {
            $contextClinic = strtolower(trim((string) $request->input('context_clinic_id', $request->query('clinic_id', ''))));

            if ($contextClinic === 'all') {
                return 'all';
            }
        }

        return $fallbackClinicId;
    }

    private function transaksiPasienRedirectParameters(
        Request $request,
        array $data,
        string|int|null $redirectClinicId
    ): array {
        $selectedDate = $this->normalizeSelectedDate(
            $request->input('context_tanggal', $data['tanggal'] ?? now()->toDateString())
        );
        $selectedDateRange = $this->normalizeSelectedDateRange(
            $request->input('context_tanggal_awal'),
            $request->input('context_tanggal_akhir'),
            $selectedDate
        );
        $selectedStartDate = $selectedDateRange['start'];
        $selectedEndDate = $selectedDateRange['end'];
        $selectedDataMonth = $this->normalizeSelectedMonth(
            $request->input('context_data_bulan', Carbon::parse($selectedDate)->format('Y-m'))
        )->format('Y-m');
        $selectedPenjamin = trim((string) $request->input('context_data_penjamin', ''));
        $selectedLocalStatus = $this->normalizeLocalStatusFilter($request->input('context_local_status'));
        $activeTab = trim((string) $request->input('active_tab_context', 'panel-transaksi-pasien'));

        if (! in_array($activeTab, ['panel-transaksi-pasien', 'panel-data-transaksi', 'panel-rekap-pasien'], true)) {
            $activeTab = 'panel-transaksi-pasien';
        }

        return [
            'tanggal' => $selectedEndDate,
            'tanggal_awal' => $selectedStartDate,
            'tanggal_akhir' => $selectedEndDate,
            'data_bulan' => $selectedDataMonth,
            'data_penjamin' => $selectedPenjamin !== '' ? $selectedPenjamin : null,
            'local_status' => $selectedLocalStatus !== '' ? $selectedLocalStatus : null,
            'clinic_id' => $redirectClinicId,
            'active_tab' => $activeTab,
        ];
    }

    private function validatedTransaksi(Request $request, ?int $ignoreId = null): array
    {
        $clinicProfileId = $this->resolveOperationalClinicId($request);
        $masterKomponen = MasterKomponenTransaksi::query()
            ->orderBy('urutan_laporan')
            ->orderBy('kode_komponen')
            ->get();
        $masterAdministrasi = MasterAdministrasiPasien::query()
            ->orderBy('urutan_laporan')
            ->orderBy('kode_administrasi')
            ->get();

        $data = $request->validate([
            'simrs_no_rawat' => [
                'required',
                'string',
                'max:30',
                Rule::unique('transaksi_pasien', 'simrs_no_rawat')
                    ->where(fn ($query) => $query->where('clinic_profile_id', $clinicProfileId))
                    ->ignore($ignoreId),
            ],
            'clinic_profile_id' => ['nullable', 'integer'],
            'simrs_no_reg' => ['nullable', 'string', 'max:20'],
            'tanggal' => ['required', 'date'],
            'bulan' => ['required', 'integer', 'between:1,12'],
            'harian' => ['nullable', 'string', 'max:30'],
            'layanan_medis' => ['nullable', 'string', 'max:255'],
            'dokter' => ['nullable', 'string', 'max:255'],
            'penjamin' => ['nullable', 'string', 'max:255'],
            'no_rm' => ['nullable', 'string', 'max:50'],
            'nama_pasien' => ['nullable', 'string', 'max:255'],
            'jk' => ['nullable', 'string', 'max:20'],
            'statis' => ['nullable', 'string', 'max:100'],
            'genap' => ['nullable', 'string', 'max:100'],
            'status_pasien' => ['nullable', 'string', 'max:100'],
            'alamat' => ['nullable', 'string'],
            'lab' => ['nullable', 'string'],
            'icd' => ['nullable', 'string'],
            'diagnosa' => ['nullable', 'string'],
            'farmasi' => ['nullable', 'string'],
            'komponen_nilai' => ['nullable', 'array'],
            'komponen_nilai.*' => ['nullable', 'numeric', 'min:0'],
            'administrasi_nilai' => ['nullable', 'array'],
            'administrasi_nilai.*' => ['nullable', 'numeric', 'min:0'],
            'jml_hari' => ['nullable', 'integer', 'min:0'],
            'jml_visit' => ['nullable', 'integer', 'min:0'],
            'keterangan' => ['nullable', 'string'],
        ]);

        $tanggal = Carbon::parse($data['tanggal']);
        $data['tanggal'] = $tanggal->toDateString();
        $data['bulan'] = (int) ($data['bulan'] ?? $tanggal->month);
        $data['harian'] = $data['harian'] ?: $tanggal->locale('id')->translatedFormat('l');

        $mappedLayanan = $this->resolveMappedLayananFromStoredValue($data['layanan_medis'] ?? null);
        if ($mappedLayanan) {
            $data['layanan_medis'] = $mappedLayanan['kode_layanan'];
        } elseif (filled($data['layanan_medis'])) {
            $data['layanan_medis'] = strtoupper(trim((string) $data['layanan_medis']));
        }

        $komponenInput = collect($request->input('komponen_nilai', []))
            ->mapWithKeys(fn ($value, $key) => [(int) $key => (float) ($value ?? 0)]);
        $administrasiInput = collect($request->input('administrasi_nilai', []))
            ->mapWithKeys(fn ($value, $key) => [(int) $key => (float) ($value ?? 0)]);
        $komponenSync = [];
        $administrasiSync = [];
        $totalDebet = 0.0;
        $totalKredit = 0.0;

        foreach ($masterKomponen as $komponen) {
            $nominal = (float) ($komponenInput->get($komponen->id, 0) ?? 0);

            $komponenSync[] = [
                'master_komponen_transaksi_id' => (int) $komponen->id,
                'nominal' => $nominal,
            ];

            if ($komponen->arah_laporan === 'kredit') {
                $totalKredit += $nominal;
            } else {
                $totalDebet += $nominal;
            }
        }

        foreach ($masterAdministrasi as $administrasi) {
            $nominal = (float) ($administrasiInput->get($administrasi->id, 0) ?? 0);

            $administrasiSync[] = [
                'master_administrasi_pasien_id' => (int) $administrasi->id,
                'nominal' => $nominal,
            ];

            if ($administrasi->arah_laporan === 'kredit') {
                $totalKredit += $nominal;
            } else {
                $totalDebet += $nominal;
            }
        }

        $data['jml_hari'] = (int) ($data['jml_hari'] ?? 0);
        $data['jml_visit'] = (int) ($data['jml_visit'] ?? 0);
        $data['jumlah_rp'] = $totalDebet;
        $data['jumlah_kredit'] = $totalKredit;
        $data['clinic_profile_id'] = $clinicProfileId;
        $data['user_id'] = $request->user()?->id;
        $data['petugas_admin'] = $this->resolveLoggedInAdminName($request->user());
        $data['_komponen_sync'] = $komponenSync;
        $data['_administrasi_sync'] = $administrasiSync;

        return $data;
    }

    private function validatedPengeluaran(Request $request): array
    {
        $clinicProfileId = $this->resolveOperationalClinicId($request);
        $data = $request->validate([
            'clinic_profile_id' => ['nullable', 'integer'],
            'master_kategori_pengeluaran_id' => ['nullable', 'integer', 'exists:master_kategori_pengeluaran,id'],
            'tanggal' => ['required', 'date'],
            'deskripsi' => ['required', 'string', 'max:255'],
            'jumlah_rp' => ['required', 'numeric', 'min:0'],
            'keterangan' => ['nullable', 'string'],
        ]);

        $tanggal = Carbon::parse($data['tanggal']);
        $kategori = null;

        if (filled($data['master_kategori_pengeluaran_id'])) {
            $kategori = MasterKategoriPengeluaran::query()
                ->whereKey($data['master_kategori_pengeluaran_id'])
                ->value('nama_kategori');
        }

        return [
            'clinic_profile_id' => $clinicProfileId,
            'master_kategori_pengeluaran_id' => $data['master_kategori_pengeluaran_id'] ?? null,
            'user_id' => $request->user()?->id,
            'tanggal' => $tanggal->toDateString(),
            'bulan' => $tanggal->month,
            'tahun' => $tanggal->year,
            'kategori_pengeluaran' => $kategori,
            'deskripsi' => $data['deskripsi'],
            'jumlah_rp' => (float) $data['jumlah_rp'],
            'petugas_admin' => $this->resolveLoggedInAdminName($request->user()),
            'keterangan' => $data['keterangan'] ?? null,
        ];
    }

    private function validatedBpjsKlaimBulanan(
        Request $request,
        ?BpjsKlaimBulanan $existingClaim = null
    ): array {
        $clinicProfileId = $this->resolveOperationalClinicId($request);
        $data = $request->validate([
            'clinic_profile_id' => ['nullable', 'integer'],
            'bulan' => ['required', 'date_format:Y-m'],
            'tanggal_terima' => ['nullable', 'date'],
            'total_klaim' => ['required', 'numeric', 'min:0'],
            'keterangan' => ['nullable', 'string'],
        ]);

        $selectedMonth = $this->normalizeSelectedMonth($data['bulan']);
        $summary = $this->buildBpjsKlaimSummary(
            $clinicProfileId,
            $selectedMonth,
            (float) $data['total_klaim']
        );

        return [
            'clinic_profile_id' => $clinicProfileId,
            'user_id' => $request->user()?->id,
            'master_komponen_selisih_id' => $summary['komponen_selisih']?->id,
            'bulan' => $selectedMonth->month,
            'tahun' => $selectedMonth->year,
            'tanggal_terima' => filled($data['tanggal_terima'] ?? null)
                ? Carbon::parse($data['tanggal_terima'])->toDateString()
                : ($existingClaim?->tanggal_terima?->toDateString() ?: $selectedMonth->copy()->endOfMonth()->toDateString()),
            'total_klaim' => (float) $data['total_klaim'],
            'total_versi_klinik' => $summary['total_versi_klinik'],
            'total_komponen_acuan' => $summary['total_komponen_acuan'],
            'jumlah_komponen_acuan' => $summary['jumlah_komponen_acuan'],
            'selisih_nominal' => $summary['selisih_nominal'],
            'selisih_persen' => $summary['selisih_persen'],
            'selisih_arah' => $summary['selisih_arah'],
            'keterangan' => filled($data['keterangan'] ?? null)
                ? trim((string) $data['keterangan'])
                : null,
            '_alokasi_sync' => $summary['alokasi_rows'],
        ];
    }

    private function buildBpjsKlaimSummary(
        int $clinicProfileId,
        Carbon $selectedMonth,
        float $totalKlaim
    ): array {
        $komponenAcuan = MasterKomponenTransaksi::query()
            ->active()
            ->where('arah_laporan', 'debet')
            ->where('ikut_alokasi_bpjs', true)
            ->whereNull('peran_sistem')
            ->orderBy('urutan_laporan')
            ->orderBy('kode_komponen')
            ->get();

        $componentTotals = TransaksiPasienKomponen::query()
            ->join('transaksi_pasien', 'transaksi_pasien.id', '=', 'transaksi_pasien_komponen.transaksi_pasien_id')
            ->where('transaksi_pasien.clinic_profile_id', $clinicProfileId)
            ->whereYear('transaksi_pasien.tanggal', $selectedMonth->year)
            ->whereMonth('transaksi_pasien.tanggal', $selectedMonth->month)
            ->whereRaw("LOWER(COALESCE(transaksi_pasien.penjamin, '')) LIKE ?", ['%bpjs%'])
            ->whereIn('transaksi_pasien_komponen.master_komponen_transaksi_id', $komponenAcuan->pluck('id'))
            ->groupBy('transaksi_pasien_komponen.master_komponen_transaksi_id')
            ->selectRaw('transaksi_pasien_komponen.master_komponen_transaksi_id, COALESCE(SUM(transaksi_pasien_komponen.nominal), 0) as total_nominal')
            ->pluck('total_nominal', 'transaksi_pasien_komponen.master_komponen_transaksi_id');

        $totalVersiKlinik = (float) TransaksiPasien::query()
            ->where('clinic_profile_id', $clinicProfileId)
            ->whereYear('tanggal', $selectedMonth->year)
            ->whereMonth('tanggal', $selectedMonth->month)
            ->whereRaw("LOWER(COALESCE(penjamin, '')) LIKE ?", ['%bpjs%'])
            ->sum('jumlah_rp');

        $alokasiRows = $komponenAcuan->map(function (MasterKomponenTransaksi $komponen) use ($componentTotals) {
            return [
                'master_komponen_transaksi_id' => (int) $komponen->id,
                'kode_komponen' => $komponen->kode_komponen,
                'nama_komponen' => $komponen->nama_komponen,
                'basis_nominal' => (float) ($componentTotals[$komponen->id] ?? 0),
                'persentase' => 0.0,
                'nominal_alokasi' => 0.0,
                'basis_pajak_obat' => (bool) $komponen->basis_pajak_obat,
                'urutan_laporan' => (int) $komponen->urutan_laporan,
                'is_acuan' => (float) ($componentTotals[$komponen->id] ?? 0) > 0,
            ];
        })->values();

        $totalKomponenAcuan = (float) $alokasiRows->sum('basis_nominal');
        $jumlahKomponenAcuan = (int) $alokasiRows->where('basis_nominal', '>', 0)->count();

        $alokasiRows = $alokasiRows->map(function (array $row) use ($totalKlaim, $totalKomponenAcuan) {
            $persentase = $totalKomponenAcuan > 0
                ? (((float) $row['basis_nominal']) / $totalKomponenAcuan) * 100
                : 0.0;

            $row['persentase'] = $persentase;
            $row['nominal_alokasi'] = $totalKomponenAcuan > 0
                ? ($persentase / 100) * $totalKlaim
                : 0.0;

            return $row;
        })->values();

        $selisihNominal = $totalKlaim - $totalVersiKlinik;
        $selisihArah = abs($selisihNominal) < 0.00001
            ? null
            : ($selisihNominal > 0 ? 'debet' : 'kredit');
        $selisihPersen = $totalVersiKlinik > 0
            ? ($selisihNominal / $totalVersiKlinik) * 100
            : 0.0;

        return [
            'total_klaim' => $totalKlaim,
            'total_versi_klinik' => $totalVersiKlinik,
            'total_komponen_acuan' => $totalKomponenAcuan,
            'jumlah_komponen_acuan' => $jumlahKomponenAcuan,
            'selisih_nominal' => $selisihNominal,
            'selisih_arah' => $selisihArah,
            'selisih_persen' => $selisihPersen,
            'komponen_selisih' => $this->bpjsKomponenSelisih(),
            'alokasi_rows' => $alokasiRows->all(),
        ];
    }

    private function emptyBpjsKlaimSummary(float $totalKlaim = 0): array
    {
        return [
            'total_klaim' => $totalKlaim,
            'total_versi_klinik' => 0.0,
            'total_komponen_acuan' => 0.0,
            'jumlah_komponen_acuan' => 0,
            'selisih_nominal' => 0.0,
            'selisih_arah' => null,
            'selisih_persen' => 0.0,
            'komponen_selisih' => $this->bpjsKomponenSelisih(),
            'alokasi_rows' => [],
        ];
    }

    private function syncBpjsKlaimAlokasi(BpjsKlaimBulanan $klaimBulanan, array $alokasiRows): void
    {
        $payload = collect($alokasiRows)
            ->map(function (array $row) {
                return [
                    'master_komponen_transaksi_id' => $row['master_komponen_transaksi_id'] ?: null,
                    'kode_komponen' => $row['kode_komponen'],
                    'nama_komponen' => $row['nama_komponen'],
                    'basis_nominal' => (float) $row['basis_nominal'],
                    'persentase' => (float) $row['persentase'],
                    'nominal_alokasi' => (float) $row['nominal_alokasi'],
                    'basis_pajak_obat' => (bool) $row['basis_pajak_obat'],
                    'urutan_laporan' => (int) $row['urutan_laporan'],
                ];
            })
            ->values()
            ->all();

        $klaimBulanan->alokasiKomponen()->delete();

        if (! empty($payload)) {
            $klaimBulanan->alokasiKomponen()->createMany($payload);
        }
    }

    private function bpjsKomponenSelisih(): ?MasterKomponenTransaksi
    {
        return MasterKomponenTransaksi::query()
            ->where('peran_sistem', 'bpjs_selisih')
            ->orderByDesc('is_active')
            ->orderBy('urutan_laporan')
            ->orderBy('kode_komponen')
            ->first();
    }

    private function bpjsClaimTargetLayanan(?Collection $masterLayanan = null): ?MasterLayanan
    {
        $masterLayanan ??= MasterLayanan::query()
            ->orderByDesc('is_bpjs_claim_target')
            ->orderBy('urutan_laporan')
            ->orderBy('kode_layanan')
            ->get();

        return $masterLayanan->first(
            fn (MasterLayanan $layanan) => (bool) $layanan->is_bpjs_claim_target
        );
    }

    private function syncBpjsClaimTargetLayanan(MasterLayanan $masterLayanan): void
    {
        if (! $masterLayanan->is_bpjs_claim_target) {
            return;
        }

        MasterLayanan::query()
            ->where('id', '!=', $masterLayanan->id)
            ->where('is_bpjs_claim_target', true)
            ->update(['is_bpjs_claim_target' => false]);
    }

    private function validatedMasterLayanan(Request $request, ?int $ignoreId = null): array
    {
        $data = $request->validate([
            'kode_layanan' => [
                'required',
                'string',
                'max:20',
                Rule::unique('master_layanan', 'kode_layanan')->ignore($ignoreId),
            ],
            'nama_layanan' => ['required', 'string', 'max:255'],
            'simrs_kd_poli' => ['nullable', 'string', 'max:100'],
            'simrs_nm_poli' => ['nullable', 'string', 'max:255'],
            'is_bpjs_claim_target' => ['nullable', 'boolean'],
            'urutan_laporan' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        return [
            'kode_layanan' => strtoupper(trim((string) $data['kode_layanan'])),
            'nama_layanan' => trim((string) $data['nama_layanan']),
            'simrs_kd_poli' => filled($data['simrs_kd_poli'] ?? null)
                ? strtolower(trim((string) $data['simrs_kd_poli']))
                : null,
            'simrs_nm_poli' => filled($data['simrs_nm_poli'] ?? null)
                ? trim((string) $data['simrs_nm_poli'])
                : null,
            'is_bpjs_claim_target' => $request->boolean('is_bpjs_claim_target'),
            'urutan_laporan' => (int) ($data['urutan_laporan'] ?? 0),
            'is_active' => $request->boolean('is_active'),
        ];
    }

    private function validatedMasterKategoriPengeluaran(Request $request, ?int $ignoreId = null): array
    {
        $data = $request->validate([
            'kode_kategori' => [
                'required',
                'string',
                'max:20',
                Rule::unique('master_kategori_pengeluaran', 'kode_kategori')->ignore($ignoreId),
            ],
            'nama_kategori' => ['required', 'string', 'max:255'],
            'urutan_laporan' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        return [
            'kode_kategori' => strtoupper(trim((string) $data['kode_kategori'])),
            'nama_kategori' => trim((string) $data['nama_kategori']),
            'urutan_laporan' => (int) ($data['urutan_laporan'] ?? 0),
            'is_active' => $request->boolean('is_active'),
        ];
    }

    private function validatedMasterKomponenTransaksi(
        Request $request,
        ?int $ignoreId = null,
        ?MasterKomponenTransaksi $existingComponent = null
    ): array {
        $data = $request->validate([
            'kode_komponen' => [
                'required',
                'string',
                'max:20',
                Rule::unique('master_komponen_transaksi', 'kode_komponen')->ignore($ignoreId),
            ],
            'nama_komponen' => ['required', 'string', 'max:255'],
            'arah_laporan' => ['required', Rule::in(['debet', 'kredit'])],
            'ikut_alokasi_bpjs' => ['nullable', 'boolean'],
            'basis_pajak_obat' => ['nullable', 'boolean'],
            'peran_sistem' => ['nullable', Rule::in(['normal', 'bpjs_selisih'])],
            'urutan_laporan' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $peranSistem = trim((string) ($data['peran_sistem'] ?? 'normal'));
        $peranSistem = $peranSistem === '' ? 'normal' : $peranSistem;

        if ($peranSistem === 'bpjs_selisih') {
            $alreadyUsed = MasterKomponenTransaksi::query()
                ->where('peran_sistem', 'bpjs_selisih')
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists();

            if ($alreadyUsed) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'peran_sistem' => 'Komponen penyesuaian klaim BPJS sudah dipilih pada data master lain.',
                ]);
            }
        }

        return [
            'kode_komponen' => strtoupper(trim((string) $data['kode_komponen'])),
            'nama_komponen' => trim((string) $data['nama_komponen']),
            'field_key' => $existingComponent?->field_key
                ?: $this->generateKomponenFieldKey(
                    trim((string) $data['kode_komponen']),
                    $ignoreId,
                    trim((string) $data['nama_komponen']),
                    $peranSistem === 'normal' ? null : $peranSistem
                ),
            'arah_laporan' => strtolower(trim((string) $data['arah_laporan'])),
            'ikut_alokasi_bpjs' => $peranSistem === 'bpjs_selisih'
                ? false
                : $request->boolean('ikut_alokasi_bpjs', true),
            'basis_pajak_obat' => $peranSistem === 'bpjs_selisih'
                ? false
                : $request->boolean('basis_pajak_obat'),
            'peran_sistem' => $peranSistem === 'normal' ? null : $peranSistem,
            'urutan_laporan' => (int) ($data['urutan_laporan'] ?? 0),
            'is_active' => $request->boolean('is_active'),
        ];
    }

    private function validatedMasterAdministrasiPasien(
        Request $request,
        ?int $ignoreId = null,
        ?MasterAdministrasiPasien $existingAdministrasi = null
    ): array {
        $data = $request->validate([
            'kode_administrasi' => [
                'required',
                'string',
                'max:20',
                Rule::unique('master_administrasi_pasien', 'kode_administrasi')->ignore($ignoreId),
            ],
            'nama_administrasi' => ['required', 'string', 'max:255'],
            'arah_laporan' => ['required', Rule::in(['debet', 'kredit'])],
            'urutan_laporan' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        return [
            'kode_administrasi' => strtoupper(trim((string) $data['kode_administrasi'])),
            'nama_administrasi' => trim((string) $data['nama_administrasi']),
            'field_key' => $existingAdministrasi?->field_key
                ?: $this->generateAdministrasiFieldKey(
                    trim((string) $data['kode_administrasi']),
                    $ignoreId,
                    trim((string) $data['nama_administrasi'])
                ),
            'arah_laporan' => strtolower(trim((string) $data['arah_laporan'])),
            'urutan_laporan' => (int) ($data['urutan_laporan'] ?? 0),
            'is_active' => $request->boolean('is_active'),
        ];
    }

    private function generateKomponenFieldKey(
        string $seed,
        ?int $ignoreId = null,
        ?string $name = null,
        ?string $peranSistem = null
    ): string
    {
        $base = $this->canonicalKomponenFieldKey($seed, $name, $peranSistem)
            ?: Str::of($seed)
            ->lower()
            ->replaceMatches('/[^a-z0-9]+/', '_')
            ->trim('_')
            ->toString();

        if ($base === '') {
            $base = 'komponen_transaksi';
        }

        $candidate = $base;
        $suffix = 1;

        while (MasterKomponenTransaksi::query()
            ->where('field_key', $candidate)
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists()) {
            $candidate = $base . '_' . $suffix;
            $suffix++;
        }

        return $candidate;
    }

    private function generateAdministrasiFieldKey(
        string $seed,
        ?int $ignoreId = null,
        ?string $name = null
    ): string
    {
        $base = $this->canonicalAdministrasiFieldKey($seed, $name)
            ?: Str::of($seed)
            ->lower()
            ->replaceMatches('/[^a-z0-9]+/', '_')
            ->trim('_')
            ->toString();

        if ($base === '') {
            $base = 'administrasi_pasien';
        }

        $candidate = $base;
        $suffix = 1;

        while (MasterAdministrasiPasien::query()
            ->where('field_key', $candidate)
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists()) {
            $candidate = $base . '_' . $suffix;
            $suffix++;
        }

        return $candidate;
    }

    private function komponenPrefillKeys(MasterKomponenTransaksi $komponen): array
    {
        return $this->uniqueDynamicPrefillKeys([
            $komponen->field_key,
            $this->canonicalKomponenFieldKey(
                $komponen->kode_komponen,
                $komponen->nama_komponen,
                $komponen->peran_sistem
            ),
        ]);
    }

    private function administrasiPrefillKeys(MasterAdministrasiPasien $administrasi): array
    {
        return $this->uniqueDynamicPrefillKeys([
            $administrasi->field_key,
            $this->canonicalAdministrasiFieldKey(
                $administrasi->kode_administrasi,
                $administrasi->nama_administrasi
            ),
        ]);
    }

    private function canonicalKomponenFieldKey(
        ?string $code = null,
        ?string $name = null,
        ?string $peranSistem = null
    ): ?string {
        if ($peranSistem === 'bpjs_selisih') {
            return null;
        }

        $normalizedName = $this->normalizeDynamicMasterLabel($name);
        $catalog = $this->komponenCanonicalFieldKeyCatalog();

        foreach ($catalog as $fieldKey => $aliases) {
            if (in_array($normalizedName, $aliases, true)) {
                return $fieldKey;
            }
        }

        return match (strtoupper(trim((string) $code))) {
            'D1' => 'uang_daftar',
            'D2' => 'uang_periksa',
            'D3' => 'uang_obat',
            'D4' => 'uang_bersalin',
            'D5' => 'jasa_dokter',
            'D6' => 'rawat_inap',
            'D7' => 'honor_dr_visit',
            'D8' => 'oksigen',
            'D9' => 'perlengk_bayi',
            'D10' => 'jaspel_nakes',
            'D11' => 'bmhp',
            'D12' => 'pkl',
            'D13' => 'lain_lain',
            default => null,
        };
    }

    private function canonicalAdministrasiFieldKey(?string $code = null, ?string $name = null): ?string
    {
        $normalizedName = $this->normalizeDynamicMasterLabel($name);
        $catalog = $this->administrasiCanonicalFieldKeyCatalog();

        foreach ($catalog as $fieldKey => $aliases) {
            if (in_array($normalizedName, $aliases, true)) {
                return $fieldKey;
            }
        }

        return match (strtoupper(trim((string) $code))) {
            'A1' => 'utang_pasien',
            'A2' => 'utang',
            'A3' => 'bayar_utang_pasien',
            'A4' => 'derma_solidaritas',
            'A5' => 'saldo_kredit',
            'A6' => 'saldo',
            default => null,
        };
    }

    private function komponenCanonicalFieldKeyCatalog(): array
    {
        return [
            'uang_daftar' => ['uang daftar', 'biaya daftar', 'biaya pendaftaran', 'pendaftaran'],
            'uang_periksa' => ['uang periksa', 'biaya periksa', 'uang pemeriksaan', 'biaya pemeriksaan'],
            'uang_obat' => ['uang obat', 'biaya obat', 'obat'],
            'uang_bersalin' => ['uang bersalin', 'biaya bersalin', 'persalinan', 'partus'],
            'jasa_dokter' => ['jasa dokter', 'fee dokter', 'honor dokter'],
            'rawat_inap' => ['rawat inap', 'biaya rawat inap'],
            'honor_dr_visit' => ['honor dr visit', 'honor dokter visit', 'honor dr. visit'],
            'oksigen' => ['oksigen', 'biaya oksigen'],
            'perlengk_bayi' => ['perlengkapan bayi', 'perlengk bayi', 'perlengkapan baby'],
            'jaspel_nakes' => ['jaspel nakes', 'jasa pelayanan nakes', 'jasa nakes'],
            'bmhp' => ['bmhp'],
            'pkl' => ['pkl'],
            'lain_lain' => ['lain lain', 'lain-lain', 'lainnya'],
        ];
    }

    private function administrasiCanonicalFieldKeyCatalog(): array
    {
        return [
            'utang_pasien' => ['utang pasien'],
            'utang' => ['utang'],
            'bayar_utang_pasien' => ['bayar utang pasien', 'pelunasan utang pasien'],
            'derma_solidaritas' => ['derma solidaritas', 'derma & solidaritas'],
            'saldo_kredit' => ['saldo kredit'],
            'saldo' => ['saldo'],
        ];
    }

    private function normalizeDynamicMasterLabel(?string $value): string
    {
        return Str::of((string) $value)
            ->lower()
            ->ascii()
            ->replace('&', ' ')
            ->replaceMatches('/[^a-z0-9]+/', ' ')
            ->squish()
            ->toString();
    }

    private function uniqueDynamicPrefillKeys(array $keys): array
    {
        return collect($keys)
            ->filter(fn ($key) => filled($key))
            ->map(fn ($key) => trim((string) $key))
            ->unique()
            ->values()
            ->all();
    }

    private function activeMasterKomponenTransaksi(): Collection
    {
        return MasterKomponenTransaksi::query()
            ->active()
            ->orderBy('urutan_laporan')
            ->orderBy('kode_komponen')
            ->get();
    }

    private function activeMasterAdministrasiPasien(): Collection
    {
        return MasterAdministrasiPasien::query()
            ->active()
            ->orderBy('urutan_laporan')
            ->orderBy('kode_administrasi')
            ->get();
    }

    private function serializeKomponenTransaksi(TransaksiPasien $transaksiPasien): array
    {
        $existing = $transaksiPasien->relationLoaded('komponenTransaksi')
            ? $transaksiPasien->komponenTransaksi
            : $transaksiPasien->komponenTransaksi()->get();

        return $existing
            ->mapWithKeys(fn (TransaksiPasienKomponen $item) => [
                (string) $item->master_komponen_transaksi_id => (float) $item->nominal,
            ])
            ->all();
    }

    private function serializeAdministrasiTransaksi(TransaksiPasien $transaksiPasien): array
    {
        $existing = $transaksiPasien->relationLoaded('administrasiTransaksi')
            ? $transaksiPasien->administrasiTransaksi
            : $transaksiPasien->administrasiTransaksi()->get();

        return $existing
            ->mapWithKeys(fn (TransaksiPasienAdministrasi $item) => [
                (string) $item->master_administrasi_pasien_id => (float) $item->nominal,
            ])
            ->all();
    }

    private function syncTransaksiKomponen(TransaksiPasien $transaksiPasien, array $komponenSync): void
    {
        $payload = collect($komponenSync)
            ->filter(fn (array $item) => (float) ($item['nominal'] ?? 0) > 0)
            ->map(function (array $item) {
                return [
                    'master_komponen_transaksi_id' => (int) $item['master_komponen_transaksi_id'],
                    'nominal' => (float) $item['nominal'],
                ];
            })
            ->values()
            ->all();

        $transaksiPasien->komponenTransaksi()->delete();

        if (! empty($payload)) {
            $transaksiPasien->komponenTransaksi()->createMany($payload);
        }
    }

    private function syncTransaksiAdministrasi(TransaksiPasien $transaksiPasien, array $administrasiSync): void
    {
        $payload = collect($administrasiSync)
            ->filter(fn (array $item) => (float) ($item['nominal'] ?? 0) > 0)
            ->map(function (array $item) {
                return [
                    'master_administrasi_pasien_id' => (int) $item['master_administrasi_pasien_id'],
                    'nominal' => (float) $item['nominal'],
                ];
            })
            ->values()
            ->all();

        $transaksiPasien->administrasiTransaksi()->delete();

        if (! empty($payload)) {
            $transaksiPasien->administrasiTransaksi()->createMany($payload);
        }
    }

    private function validatedClinicDatabaseConnection(
        Request $request,
        ?int $ignoreId = null,
        ?ClinicDatabaseConnection $existingConnection = null
    ): array {
        $data = $request->validate([
            'clinic_profile_id' => [
                'required',
                'integer',
                'exists:clinic_profiles,id',
                Rule::unique('clinic_database_connections', 'clinic_profile_id')
                    ->where(fn ($query) => $query->where('connection_role', 'simrs'))
                    ->ignore($ignoreId),
            ],
            'server_name' => ['nullable', 'string', 'max:255'],
            'driver' => ['required', Rule::in(['mariadb', 'mysql'])],
            'host' => ['required', 'string', 'max:255'],
            'zero_tier_ip' => ['nullable', 'string', 'max:255'],
            'port' => ['required', 'integer', 'between:1,65535'],
            'database' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'max:255'],
            'charset' => ['nullable', 'string', 'max:30'],
            'collation' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $payload = [
            'clinic_profile_id' => (int) $data['clinic_profile_id'],
            'connection_role' => 'simrs',
            'server_name' => filled($data['server_name'] ?? null)
                ? trim((string) $data['server_name'])
                : null,
            'driver' => trim((string) $data['driver']),
            'host' => trim((string) $data['host']),
            'zero_tier_ip' => filled($data['zero_tier_ip'] ?? null)
                ? trim((string) $data['zero_tier_ip'])
                : null,
            'port' => (int) $data['port'],
            'database' => trim((string) $data['database']),
            'username' => trim((string) $data['username']),
            'charset' => filled($data['charset'] ?? null)
                ? trim((string) $data['charset'])
                : 'utf8mb4',
            'collation' => filled($data['collation'] ?? null)
                ? trim((string) $data['collation'])
                : 'utf8mb4_unicode_ci',
            'notes' => filled($data['notes'] ?? null)
                ? trim((string) $data['notes'])
                : null,
            'is_active' => $request->boolean('is_active', true),
            'last_verified_at' => $existingConnection?->last_verified_at,
        ];

        if (filled($data['password'] ?? null)) {
            $payload['password'] = trim((string) $data['password']);
        } elseif (! $existingConnection) {
            $payload['password'] = null;
        }

        return $payload;
    }

    private function validatedClinicProfile(
        Request $request,
        ?int $ignoreId = null,
        bool $allowMasterFields = false,
        ?ClinicProfile $existingProfile = null
    ): array
    {
        $data = $request->validate([
            'kode_klinik' => $allowMasterFields
                ? [
                    'required',
                    'string',
                    'max:40',
                    Rule::unique('clinic_profiles', 'kode_klinik')->ignore($ignoreId),
                ]
                : ['nullable', 'string', 'max:40'],
            'nama_klinik' => ['required', 'string', 'max:255'],
            'nama_pendek' => ['nullable', 'string', 'max:120'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'alamat' => ['nullable', 'string'],
            'kecamatan' => ['nullable', 'string', 'max:120'],
            'kota' => ['nullable', 'string', 'max:120'],
            'provinsi' => ['nullable', 'string', 'max:120'],
            'kode_pos' => ['nullable', 'string', 'max:20'],
            'logo_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'telepon' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'string', 'max:255'],
            'penanggung_jawab' => ['nullable', 'string', 'max:255'],
            'jam_pelayanan' => ['nullable', 'string', 'max:255'],
            'deskripsi_singkat' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $logoPath = $existingProfile?->logo_path;

        if ($request->hasFile('logo_file')) {
            $storedLogo = $request->file('logo_file')->store('clinic-logos', 'public');

            if (filled($logoPath) && $logoPath !== $storedLogo && Storage::disk('public')->exists($logoPath)) {
                Storage::disk('public')->delete($logoPath);
            }

            $logoPath = $storedLogo;
        }

        return [
            'kode_klinik' => $allowMasterFields
                ? strtoupper(trim((string) $data['kode_klinik']))
                : ($existingProfile?->kode_klinik ?: 'KLN' . str_pad((string) ($ignoreId ?: 1), 3, '0', STR_PAD_LEFT)),
            'nama_klinik' => trim((string) $data['nama_klinik']),
            'nama_pendek' => filled($data['nama_pendek'] ?? null)
                ? trim((string) $data['nama_pendek'])
                : null,
            'tagline' => filled($data['tagline'] ?? null)
                ? trim((string) $data['tagline'])
                : null,
            'alamat' => filled($data['alamat'] ?? null)
                ? trim((string) $data['alamat'])
                : null,
            'kecamatan' => filled($data['kecamatan'] ?? null)
                ? trim((string) $data['kecamatan'])
                : null,
            'kota' => filled($data['kota'] ?? null)
                ? trim((string) $data['kota'])
                : null,
            'provinsi' => filled($data['provinsi'] ?? null)
                ? trim((string) $data['provinsi'])
                : null,
            'kode_pos' => filled($data['kode_pos'] ?? null)
                ? trim((string) $data['kode_pos'])
                : null,
            'logo_path' => $logoPath,
            'telepon' => filled($data['telepon'] ?? null)
                ? trim((string) $data['telepon'])
                : null,
            'email' => filled($data['email'] ?? null)
                ? strtolower(trim((string) $data['email']))
                : null,
            'website' => filled($data['website'] ?? null)
                ? trim((string) $data['website'])
                : null,
            'penanggung_jawab' => filled($data['penanggung_jawab'] ?? null)
                ? trim((string) $data['penanggung_jawab'])
                : null,
            'jam_pelayanan' => filled($data['jam_pelayanan'] ?? null)
                ? trim((string) $data['jam_pelayanan'])
                : null,
            'deskripsi_singkat' => filled($data['deskripsi_singkat'] ?? null)
                ? trim((string) $data['deskripsi_singkat'])
                : null,
            'is_active' => $allowMasterFields
                ? $request->boolean('is_active', true)
                : ($existingProfile?->is_active ?? true),
        ];
    }

    private function appDatabaseSummary(): array
    {
        $defaultConnection = config('database.default');
        $connectionConfig = is_string($defaultConnection)
            ? config('database.connections.' . $defaultConnection, [])
            : [];
        $zeroTierHost = (string) env('APP_DB_ZERO_TIER_IP', '');

        return [
            'connection' => is_string($defaultConnection) ? $defaultConnection : 'mariadb',
            'host' => (string) data_get($connectionConfig, 'host', '-'),
            'database' => (string) data_get($connectionConfig, 'database', '-'),
            'port' => (string) data_get($connectionConfig, 'port', '-'),
            'mode' => filled($zeroTierHost) ? 'ZeroTier' : 'Host Biasa',
            'zero_tier_host' => $zeroTierHost,
        ];
    }

    private function humanizeClinicConnectionError(\Throwable $exception): string
    {
        $message = trim($exception->getMessage());
        $normalizedMessage = strtolower($message);

        if (str_contains($normalizedMessage, 'auth_gssapi_client')) {
            return 'Server klinik memakai autentikasi GSSAPI/Kerberos, sedangkan client PHP ini belum mendukung plugin itu. '
                . 'Buat user database khusus aplikasi dengan autentikasi password biasa, lalu pakai user tersebut di koneksi klinik. '
                . 'Pesan asli: ' . $message;
        }

        if (str_contains($normalizedMessage, 'access denied')) {
            return 'Username atau password database ditolak oleh server. Periksa user, password, dan hak akses database. '
                . 'Pesan asli: ' . $message;
        }

        if (str_contains($normalizedMessage, 'connection refused')) {
            return 'Host atau port database menolak koneksi. Periksa IP ZeroTier, port, firewall, dan status service MariaDB/MySQL di server klinik. '
                . 'Pesan asli: ' . $message;
        }

        if (str_contains($normalizedMessage, 'getaddrinfo') || str_contains($normalizedMessage, 'php_network_getaddresses')) {
            return 'Host database tidak bisa diterjemahkan. Periksa penulisan host atau IP ZeroTier yang dipakai. '
                . 'Pesan asli: ' . $message;
        }

        return $message;
    }

    private function normalizeLocalStatusFilter(?string $value): string
    {
        $normalized = strtolower(trim((string) $value));

        return in_array($normalized, ['saved', 'unsaved'], true)
            ? $normalized
            : '';
    }

    private function normalizeRekapPasienStatusFilter(?string $value): string
    {
        $normalized = strtolower(trim((string) $value));

        return in_array($normalized, ['all', 'ralan', 'ranap'], true)
            ? $normalized
            : 'all';
    }

    private function normalizeRekapPenyakitAgeFilter(?string $value): string
    {
        $normalized = strtolower(trim((string) $value));

        return in_array($normalized, ['all', 'anak', 'dewasa'], true)
            ? $normalized
            : 'all';
    }

    private function humanizeRekapPenyakitAgeFilter(string $value): string
    {
        return match ($value) {
            'anak' => 'Anak-anak',
            'dewasa' => 'Dewasa',
            default => 'Semua Usia',
        };
    }

    private function normalizeRekapPenjaminMode(?string $value): string
    {
        $normalized = strtolower(trim((string) $value));

        return in_array($normalized, ['all', 'umum', 'bpjs'], true)
            ? $normalized
            : 'all';
    }

    private function scopeTransactionsByPenjaminMode($query, string $mode)
    {
        if ($mode === 'bpjs') {
            return $query->whereRaw("LOWER(COALESCE(penjamin, '')) LIKE ?", ['%bpjs%']);
        }

        if ($mode === 'umum') {
            return $query->where(function ($builder) {
                $builder->whereNull('penjamin')
                    ->orWhere('penjamin', '')
                    ->orWhereRaw("LOWER(COALESCE(penjamin, '')) NOT LIKE ?", ['%bpjs%']);
            });
        }

        return $query;
    }

    private function monthlyBpjsClaimSummary(Carbon $selectedMonth, ?int $clinicProfileId = null): array
    {
        $claimsQuery = BpjsKlaimBulanan::query()
            ->with(['masterKomponenSelisih'])
            ->forBulan($selectedMonth->month, $selectedMonth->year);
        $this->scopeQueryToClinic($claimsQuery, $clinicProfileId);
        $claims = $claimsQuery->get();

        $totalKlaim = (float) $claims->sum('total_klaim');
        $totalVersiKlinik = (float) $claims->sum('total_versi_klinik');
        $selisihNominal = (float) $claims->sum('selisih_nominal');
        $systemComponent = $claims->pluck('masterKomponenSelisih')->filter()->first()
            ?: $this->bpjsKomponenSelisih();

        return [
            'count' => $claims->count(),
            'claims' => $claims,
            'total_klaim' => $totalKlaim,
            'total_versi_klinik' => $totalVersiKlinik,
            'total_komponen_acuan' => (float) $claims->sum('total_komponen_acuan'),
            'jumlah_komponen_acuan' => (int) $claims->sum('jumlah_komponen_acuan'),
            'selisih_nominal' => $selisihNominal,
            'selisih_arah' => abs($selisihNominal) < 0.00001
                ? null
                : ($selisihNominal > 0 ? 'debet' : 'kredit'),
            'selisih_persen' => $totalVersiKlinik > 0
                ? ($selisihNominal / $totalVersiKlinik) * 100
                : 0.0,
            'komponen_selisih' => $systemComponent,
            'debet' => $totalKlaim,
            'kredit' => 0.0,
        ];
    }

    private function emptyMonthlyBpjsClaimSummary(): array
    {
        return [
            'count' => 0,
            'claims' => collect(),
            'total_klaim' => 0.0,
            'total_versi_klinik' => 0.0,
            'total_komponen_acuan' => 0.0,
            'jumlah_komponen_acuan' => 0,
            'selisih_nominal' => 0.0,
            'selisih_arah' => null,
            'selisih_persen' => 0.0,
            'komponen_selisih' => $this->bpjsKomponenSelisih(),
            'debet' => 0.0,
            'kredit' => 0.0,
        ];
    }

    private function mergeBpjsClaimIntoFieldRows(Collection $fieldRows, array $bpjsClaimSummary): Collection
    {
        $totalKlaim = (float) ($bpjsClaimSummary['total_klaim'] ?? 0);

        if ($totalKlaim <= 0) {
            return $fieldRows->values();
        }

        /** @var MasterKomponenTransaksi|null $component */
        $component = $bpjsClaimSummary['komponen_selisih'] ?? null;
        $componentCode = $component?->kode_komponen;
        $claimDebit = (float) ($bpjsClaimSummary['debet'] ?? 0);
        $claimKredit = (float) ($bpjsClaimSummary['kredit'] ?? 0);

        if (filled($componentCode)) {
            $matched = false;

            $mergedRows = $fieldRows->map(function (array $row) use ($componentCode, $claimDebit, $claimKredit, &$matched) {
                if (($row['kode'] ?? null) !== $componentCode) {
                    return $row;
                }

                $matched = true;
                $row['debet'] = (float) ($row['debet'] ?? 0) + $claimDebit;
                $row['kredit'] = (float) ($row['kredit'] ?? 0) + $claimKredit;
                $row['is_claim_row'] = true;

                return $row;
            });

            if ($matched) {
                return $mergedRows->values();
            }
        }

        return $fieldRows->push([
            'kode' => $component?->kode_komponen ?: 'SYS',
            'keterangan' => $component?->nama_komponen ?: 'Klaim BPJS Bulanan',
            'arah_laporan' => 'debet',
            'debet' => $claimDebit,
            'kredit' => $claimKredit,
            'is_claim_row' => true,
        ])->values();
    }

    private function mergeBpjsClaimIntoLayananRows(
        Collection $layananRows,
        array $bpjsClaimSummary,
        ?MasterLayanan $targetLayanan
    ): Collection {
        $totalKlaim = (float) ($bpjsClaimSummary['total_klaim'] ?? 0);

        if ($totalKlaim <= 0 || ! $targetLayanan) {
            return $layananRows->values();
        }

        return $layananRows->map(function (array $row) use ($targetLayanan, $bpjsClaimSummary) {
            if (($row['kode'] ?? null) !== $targetLayanan->kode_layanan) {
                return $row;
            }

            $row['debet'] = (float) ($row['debet'] ?? 0) + (float) ($bpjsClaimSummary['debet'] ?? 0);
            $row['kredit'] = (float) ($row['kredit'] ?? 0) + (float) ($bpjsClaimSummary['kredit'] ?? 0);
            $row['is_claim_target'] = true;

            return $row;
        })->values();
    }

    private function masterDestroySuccessRedirect(
        Request $request,
        string $route,
        string $message
    ): RedirectResponse {
        return redirect()
            ->route($route, $this->masterListingQuery($request))
            ->with('success', $message);
    }

    private function cannotDeleteMasterRedirect(
        Request $request,
        string $route,
        string $message
    ): RedirectResponse {
        return redirect()
            ->route($route, $this->masterListingQuery($request))
            ->with('error', $message);
    }

    private function masterListingQuery(Request $request): array
    {
        return array_filter([
            'q' => trim($request->string('q')->toString()) ?: null,
        ], fn ($value) => filled($value));
    }

    private function monthlyLayananRows(Collection $transactions, Collection $masterLayanan): Collection
    {
        $groupedTransactions = $transactions->groupBy(
            fn (TransaksiPasien $item) => $this->normalizeLayananKey($item->layanan_medis)
        );

        $consumedKeys = [];
        $rows = collect();

        foreach ($masterLayanan as $layanan) {
            $sum = 0;

            foreach ($this->layananLookupKeys($layanan) as $lookupKey) {
                if ($groupedTransactions->has($lookupKey)) {
                    $sum += (float) $groupedTransactions->get($lookupKey)->sum('jumlah_rp');
                    $consumedKeys[] = $lookupKey;
                }
            }

            $rows->push([
                'kode' => $layanan->kode_layanan,
                'keterangan' => $layanan->nama_layanan,
                'debet' => $sum,
                'kredit' => (float) collect($this->layananLookupKeys($layanan))
                    ->filter(fn ($lookupKey) => $groupedTransactions->has($lookupKey))
                    ->sum(fn ($lookupKey) => (float) $groupedTransactions->get($lookupKey)->sum('jumlah_kredit')),
            ]);
        }

        $leftoverIndex = 1;
        foreach ($groupedTransactions as $normalizedLayanan => $items) {
            if (in_array($normalizedLayanan, $consumedKeys, true) || blank($normalizedLayanan)) {
                continue;
            }

            $rows->push([
                'kode' => 'LX' . $leftoverIndex,
                'keterangan' => $this->resolveMappedLayananFromStoredValue(
                    $items->first()->layanan_medis,
                    $masterLayanan
                )['nama_layanan'] ?? ($items->first()->layanan_medis ?: 'Layanan Lain'),
                'debet' => (float) $items->sum('jumlah_rp'),
                'kredit' => (float) $items->sum('jumlah_kredit'),
            ]);

            $leftoverIndex++;
        }

        return $rows;
    }

    private function monthlyKomponenRows(Collection $transactions, Collection $masterKomponen): Collection
    {
        return $masterKomponen->map(function (MasterKomponenTransaksi $component) use ($transactions) {
            $sum = (float) $transactions->sum(function (TransaksiPasien $transaction) use ($component) {
                $relationValue = $transaction->relationLoaded('komponenTransaksi')
                    ? $transaction->komponenTransaksi->firstWhere('master_komponen_transaksi_id', $component->id)
                    : null;

                if ($relationValue) {
                    return (float) $relationValue->nominal;
                }

                if (filled($component->field_key) && isset($transaction->{$component->field_key})) {
                    return (float) $transaction->{$component->field_key};
                }

                return 0.0;
            });

            return [
                'kode' => $component->kode_komponen,
                'keterangan' => $component->nama_komponen,
                'arah_laporan' => $component->arah_laporan === 'kredit' ? 'kredit' : 'debet',
                'debet' => $component->arah_laporan === 'kredit' ? 0 : $sum,
                'kredit' => $component->arah_laporan === 'kredit' ? $sum : 0,
            ];
        });
    }

    private function monthlyAdministrasiRows(Collection $transactions, Collection $masterAdministrasi): Collection
    {
        return $masterAdministrasi->map(function (MasterAdministrasiPasien $administrasi) use ($transactions) {
            $sum = (float) $transactions->sum(function (TransaksiPasien $transaction) use ($administrasi) {
                $relationValue = $transaction->relationLoaded('administrasiTransaksi')
                    ? $transaction->administrasiTransaksi->firstWhere('master_administrasi_pasien_id', $administrasi->id)
                    : null;

                if ($relationValue) {
                    return (float) $relationValue->nominal;
                }

                return 0.0;
            });

            return [
                'kode' => $administrasi->kode_administrasi,
                'keterangan' => $administrasi->nama_administrasi,
                'arah_laporan' => $administrasi->arah_laporan === 'kredit' ? 'kredit' : 'debet',
                'debet' => $administrasi->arah_laporan === 'kredit' ? 0 : $sum,
                'kredit' => $administrasi->arah_laporan === 'kredit' ? $sum : 0,
            ];
        });
    }

    private function monthlyPengeluaranRows(Collection $expenses, Collection $categories): Collection
    {
        $groupedExpenses = $expenses->groupBy(
            fn (Pengeluaran $expense) => strtolower(trim((string) $expense->kategori_pengeluaran))
        );

        $consumedKeys = [];
        $rows = collect();

        foreach ($categories as $category) {
            $normalized = strtolower(trim((string) $category->nama_kategori));
            $sum = 0;

            if ($groupedExpenses->has($normalized)) {
                $sum = (float) $groupedExpenses->get($normalized)->sum('jumlah_rp');
                $consumedKeys[] = $normalized;
            }

            $rows->push([
                'kode' => $category->kode_kategori,
                'keterangan' => $category->nama_kategori,
                'debet' => 0,
                'kredit' => $sum,
            ]);
        }

        $leftoverIndex = 1;
        foreach ($groupedExpenses as $normalizedCategory => $items) {
            if (in_array($normalizedCategory, $consumedKeys, true) || blank($normalizedCategory)) {
                continue;
            }

            $rows->push([
                'kode' => 'PX' . $leftoverIndex,
                'keterangan' => $items->first()->kategori_pengeluaran ?: 'Kategori Lain',
                'debet' => 0,
                'kredit' => (float) $items->sum('jumlah_rp'),
            ]);

            $leftoverIndex++;
        }

        return $rows;
    }

    private function yearlyLayananRows(Collection $transactions, Collection $masterLayanan): Collection
    {
        $grouped = [];

        foreach ($transactions as $transaction) {
            $resolved = $this->resolveMappedLayananFromStoredValue($transaction->layanan_medis, $masterLayanan);
            $code = $resolved['kode_layanan'] ?? strtoupper(trim((string) $transaction->layanan_medis));
            $month = (int) (optional($transaction->tanggal)->format('n') ?: $transaction->bulan);

            if ($code === '' || $month < 1 || $month > 12) {
                continue;
            }

            $grouped[$code]['keterangan'] = $resolved['nama_layanan'] ?? 'Belum Dimapping';
            $grouped[$code]['months'][$month] = ($grouped[$code]['months'][$month] ?? 0) + (float) $transaction->jumlah_rp;
            $grouped[$code]['credits'][$month] = ($grouped[$code]['credits'][$month] ?? 0) + (float) $transaction->jumlah_kredit;
        }

        $rows = collect();
        $consumedCodes = [];

        foreach ($masterLayanan as $layanan) {
            $consumedCodes[] = $layanan->kode_layanan;
            $monthCells = $this->blankYearMonthCells();
            $rowTotal = 0;

            foreach (range(1, 12) as $month) {
                $value = (float) data_get($grouped, $layanan->kode_layanan . '.months.' . $month, 0);
                $monthCells[$month]['debet'] = $value;
                $monthCells[$month]['kredit'] = (float) data_get($grouped, $layanan->kode_layanan . '.credits.' . $month, 0);
                $rowTotal += $value;
            }

            $rows->push([
                'kode' => $layanan->kode_layanan,
                'keterangan' => $layanan->nama_layanan,
                'months' => $monthCells,
                'total_debet' => $rowTotal,
                'total_kredit' => (float) collect(range(1, 12))
                    ->sum(fn (int $month) => (float) data_get($grouped, $layanan->kode_layanan . '.credits.' . $month, 0)),
            ]);
        }

        foreach ($grouped as $code => $payload) {
            if (in_array($code, $consumedCodes, true)) {
                continue;
            }

            $monthCells = $this->blankYearMonthCells();
            $rowTotal = 0;

            foreach (range(1, 12) as $month) {
                $value = (float) ($payload['months'][$month] ?? 0);
                $monthCells[$month]['debet'] = $value;
                $monthCells[$month]['kredit'] = (float) ($payload['credits'][$month] ?? 0);
                $rowTotal += $value;
            }

            $rows->push([
                'kode' => $code,
                'keterangan' => $payload['keterangan'] ?? 'Belum Dimapping',
                'months' => $monthCells,
                'total_debet' => $rowTotal,
                'total_kredit' => (float) collect(range(1, 12))
                    ->sum(fn (int $month) => (float) ($payload['credits'][$month] ?? 0)),
            ]);
        }

        return $rows;
    }

    private function yearlyKomponenRows(Collection $transactions, Collection $masterKomponen): Collection
    {
        return $masterKomponen->map(function (MasterKomponenTransaksi $component) use ($transactions) {
            $monthCells = $this->blankYearMonthCells();

            foreach ($transactions as $transaction) {
                $month = (int) (optional($transaction->tanggal)->format('n') ?: $transaction->bulan);

                if ($month < 1 || $month > 12) {
                    continue;
                }

                $relationValue = $transaction->relationLoaded('komponenTransaksi')
                    ? $transaction->komponenTransaksi->firstWhere('master_komponen_transaksi_id', $component->id)
                    : null;

                $nominal = 0.0;

                if ($relationValue) {
                    $nominal = (float) $relationValue->nominal;
                } elseif (filled($component->field_key) && isset($transaction->{$component->field_key})) {
                    $nominal = (float) $transaction->{$component->field_key};
                }

                if ($component->arah_laporan === 'kredit') {
                    $monthCells[$month]['kredit'] += $nominal;
                } else {
                    $monthCells[$month]['debet'] += $nominal;
                }
            }

            return [
                'kode' => $component->kode_komponen,
                'keterangan' => $component->nama_komponen,
                'months' => $monthCells,
                'total_debet' => (float) collect(range(1, 12))
                    ->sum(fn (int $month) => (float) $monthCells[$month]['debet']),
                'total_kredit' => (float) collect(range(1, 12))
                    ->sum(fn (int $month) => (float) $monthCells[$month]['kredit']),
            ];
        });
    }

    private function yearlyAdministrasiRows(Collection $transactions, Collection $masterAdministrasi): Collection
    {
        return $masterAdministrasi->map(function (MasterAdministrasiPasien $administrasi) use ($transactions) {
            $monthCells = $this->blankYearMonthCells();

            foreach ($transactions as $transaction) {
                $month = (int) (optional($transaction->tanggal)->format('n') ?: $transaction->bulan);

                if ($month < 1 || $month > 12) {
                    continue;
                }

                $relationValue = $transaction->relationLoaded('administrasiTransaksi')
                    ? $transaction->administrasiTransaksi->firstWhere('master_administrasi_pasien_id', $administrasi->id)
                    : null;

                $nominal = $relationValue ? (float) $relationValue->nominal : 0.0;

                if ($administrasi->arah_laporan === 'kredit') {
                    $monthCells[$month]['kredit'] += $nominal;
                } else {
                    $monthCells[$month]['debet'] += $nominal;
                }
            }

            return [
                'kode' => $administrasi->kode_administrasi,
                'keterangan' => $administrasi->nama_administrasi,
                'months' => $monthCells,
                'total_debet' => (float) collect(range(1, 12))
                    ->sum(fn (int $month) => (float) $monthCells[$month]['debet']),
                'total_kredit' => (float) collect(range(1, 12))
                    ->sum(fn (int $month) => (float) $monthCells[$month]['kredit']),
            ];
        });
    }

    private function yearlyPengeluaranRows(Collection $expenses, Collection $categories): Collection
    {
        $categoriesById = $categories->keyBy('id');
        $categoriesByName = $categories->keyBy(
            fn (MasterKategoriPengeluaran $category) => strtolower(trim((string) $category->nama_kategori))
        );

        $grouped = [];

        foreach ($expenses as $expense) {
            $month = (int) (optional($expense->tanggal)->format('n') ?: $expense->bulan);

            if ($month < 1 || $month > 12) {
                continue;
            }

            $matchedCategory = null;

            if ($expense->master_kategori_pengeluaran_id) {
                $matchedCategory = $categoriesById->get($expense->master_kategori_pengeluaran_id);
            }

            if (! $matchedCategory && filled($expense->kategori_pengeluaran)) {
                $matchedCategory = $categoriesByName->get(strtolower(trim((string) $expense->kategori_pengeluaran)));
            }

            $code = $matchedCategory?->kode_kategori ?: strtoupper(trim((string) ($expense->kategori_pengeluaran ?: 'LAIN')));
            $label = $matchedCategory?->nama_kategori ?: ($expense->kategori_pengeluaran ?: 'Belum Dimapping');

            if ($code === '') {
                continue;
            }

            $grouped[$code]['keterangan'] = $label;
            $grouped[$code]['months'][$month] = ($grouped[$code]['months'][$month] ?? 0) + (float) $expense->jumlah_rp;
        }

        $rows = collect();
        $consumedCodes = [];

        foreach ($categories as $category) {
            $consumedCodes[] = $category->kode_kategori;
            $monthCells = $this->blankYearMonthCells();
            $rowTotal = 0;

            foreach (range(1, 12) as $month) {
                $value = (float) data_get($grouped, $category->kode_kategori . '.months.' . $month, 0);
                $monthCells[$month]['kredit'] = $value;
                $rowTotal += $value;
            }

            $rows->push([
                'kode' => $category->kode_kategori,
                'keterangan' => $category->nama_kategori,
                'months' => $monthCells,
                'total_debet' => 0,
                'total_kredit' => $rowTotal,
            ]);
        }

        foreach ($grouped as $code => $payload) {
            if (in_array($code, $consumedCodes, true)) {
                continue;
            }

            $monthCells = $this->blankYearMonthCells();
            $rowTotal = 0;

            foreach (range(1, 12) as $month) {
                $value = (float) ($payload['months'][$month] ?? 0);
                $monthCells[$month]['kredit'] = $value;
                $rowTotal += $value;
            }

            $rows->push([
                'kode' => $code,
                'keterangan' => $payload['keterangan'] ?? 'Belum Dimapping',
                'months' => $monthCells,
                'total_debet' => 0,
                'total_kredit' => $rowTotal,
            ]);
        }

        return $rows;
    }

    private function yearMonthHeaders(): Collection
    {
        return collect(range(1, 12))->map(function (int $month) {
            return [
                'number' => $month,
                'label' => Carbon::create()->month($month)->locale('id')->translatedFormat('F'),
            ];
        });
    }

    private function yearlySideTotals(Collection $rows, string $side): array
    {
        return collect(range(1, 12))
            ->mapWithKeys(fn (int $month) => [
                $month => (float) $rows->sum(fn (array $row) => (float) data_get($row, 'months.' . $month . '.' . $side, 0)),
            ])
            ->all();
    }

    private function blankYearMonthCells(): array
    {
        return collect(range(1, 12))
            ->mapWithKeys(fn (int $month) => [
                $month => [
                    'debet' => 0.0,
                    'kredit' => 0.0,
                ],
            ])
            ->all();
    }

    private function mergeYearlyBpjsClaimIntoLayananRows(
        Collection $layananRows,
        array $bpjsClaimSummary,
        ?MasterLayanan $targetLayanan
    ): Collection {
        $totalKlaim = (float) ($bpjsClaimSummary['total_klaim'] ?? 0);

        if ($totalKlaim <= 0 || ! $targetLayanan) {
            return $layananRows->values();
        }

        return $layananRows->map(function (array $row) use ($targetLayanan, $bpjsClaimSummary) {
            if (($row['kode'] ?? null) !== $targetLayanan->kode_layanan) {
                return $row;
            }

            foreach (range(1, 12) as $month) {
                $row['months'][$month]['debet'] = (float) ($row['months'][$month]['debet'] ?? 0)
                    + (float) ($bpjsClaimSummary['monthly_klaim_totals'][$month] ?? 0);
            }

            $row['total_debet'] = (float) collect(range(1, 12))
                ->sum(fn (int $month) => (float) ($row['months'][$month]['debet'] ?? 0));
            $row['total_kredit'] = (float) collect(range(1, 12))
                ->sum(fn (int $month) => (float) ($row['months'][$month]['kredit'] ?? 0));
            $row['is_claim_target'] = true;

            return $row;
        })->values();
    }

    private function mergeYearlyBpjsClaimIntoFieldRows(Collection $fieldRows, array $bpjsClaimSummary): Collection
    {
        $totalKlaim = (float) ($bpjsClaimSummary['total_klaim'] ?? 0);

        if ($totalKlaim <= 0) {
            return $fieldRows->values();
        }

        /** @var MasterKomponenTransaksi|null $component */
        $component = $bpjsClaimSummary['komponen_selisih'] ?? null;
        $componentCode = $component?->kode_komponen;
        $claimMonthlyTotals = $bpjsClaimSummary['monthly_klaim_totals'] ?? [];

        if (filled($componentCode)) {
            $matched = false;

            $mergedRows = $fieldRows->map(function (array $row) use ($componentCode, $claimMonthlyTotals, &$matched) {
                if (($row['kode'] ?? null) !== $componentCode) {
                    return $row;
                }

                $matched = true;

                foreach (range(1, 12) as $month) {
                    $row['months'][$month]['debet'] = (float) ($row['months'][$month]['debet'] ?? 0)
                        + (float) ($claimMonthlyTotals[$month] ?? 0);
                }

                $row['total_debet'] = (float) collect(range(1, 12))
                    ->sum(fn (int $month) => (float) ($row['months'][$month]['debet'] ?? 0));
                $row['total_kredit'] = (float) collect(range(1, 12))
                    ->sum(fn (int $month) => (float) ($row['months'][$month]['kredit'] ?? 0));
                $row['is_claim_row'] = true;

                return $row;
            });

            if ($matched) {
                return $mergedRows->values();
            }
        }

        $monthCells = $this->blankYearMonthCells();

        foreach (range(1, 12) as $month) {
            $monthCells[$month]['debet'] = (float) ($claimMonthlyTotals[$month] ?? 0);
        }

        return $fieldRows->push([
            'kode' => $component?->kode_komponen ?: 'SYS',
            'keterangan' => $component?->nama_komponen ?: 'Klaim BPJS Bulanan',
            'months' => $monthCells,
            'total_debet' => $totalKlaim,
            'total_kredit' => 0.0,
            'is_claim_row' => true,
        ])->values();
    }

    private function yearlyBpjsClaimSummary(int $selectedYear, ?int $clinicProfileId = null): array
    {
        $claimsQuery = BpjsKlaimBulanan::query()
            ->with(['masterKomponenSelisih'])
            ->where('tahun', $selectedYear);
        $this->scopeQueryToClinic($claimsQuery, $clinicProfileId);
        $claims = $claimsQuery->get();

        $systemComponent = $claims->pluck('masterKomponenSelisih')->filter()->first()
            ?: $this->bpjsKomponenSelisih();

        $monthlyKlaimTotals = collect(range(1, 12))
            ->mapWithKeys(fn (int $month) => [
                $month => (float) $claims->where('bulan', $month)->sum('total_klaim'),
            ])
            ->all();

        $monthlyVersiKlinikTotals = collect(range(1, 12))
            ->mapWithKeys(fn (int $month) => [
                $month => (float) $claims->where('bulan', $month)->sum('total_versi_klinik'),
            ])
            ->all();

        $monthlySelisihDebet = collect(range(1, 12))
            ->mapWithKeys(fn (int $month) => [
                $month => max(0, (float) $claims->where('bulan', $month)->sum('selisih_nominal')),
            ])
            ->all();

        $monthlySelisihKredit = collect(range(1, 12))
            ->mapWithKeys(fn (int $month) => [
                $month => abs(min(0, (float) $claims->where('bulan', $month)->sum('selisih_nominal'))),
            ])
            ->all();

        $totalVersiKlinik = (float) array_sum($monthlyVersiKlinikTotals);
        $totalSelisih = (float) $claims->sum('selisih_nominal');

        return [
            'count' => $claims->count(),
            'claims' => $claims,
            'komponen_selisih' => $systemComponent,
            'monthly_klaim_totals' => $monthlyKlaimTotals,
            'monthly_versi_klinik_totals' => $monthlyVersiKlinikTotals,
            'monthly_selisih_debet' => $monthlySelisihDebet,
            'monthly_selisih_kredit' => $monthlySelisihKredit,
            'total_klaim' => (float) array_sum($monthlyKlaimTotals),
            'total_versi_klinik' => $totalVersiKlinik,
            'total_komponen_acuan' => (float) $claims->sum('total_komponen_acuan'),
            'jumlah_komponen_acuan' => (int) $claims->sum('jumlah_komponen_acuan'),
            'selisih_nominal' => $totalSelisih,
            'selisih_arah' => abs($totalSelisih) < 0.00001
                ? null
                : ($totalSelisih > 0 ? 'debet' : 'kredit'),
            'selisih_persen' => $totalVersiKlinik > 0
                ? ($totalSelisih / $totalVersiKlinik) * 100
                : 0.0,
        ];
    }

    private function emptyYearlyBpjsClaimSummary(): array
    {
        $blankTotals = collect(range(1, 12))
            ->mapWithKeys(fn (int $month) => [$month => 0.0])
            ->all();

        return [
            'count' => 0,
            'claims' => collect(),
            'komponen_selisih' => $this->bpjsKomponenSelisih(),
            'monthly_klaim_totals' => $blankTotals,
            'monthly_versi_klinik_totals' => $blankTotals,
            'monthly_selisih_debet' => $blankTotals,
            'monthly_selisih_kredit' => $blankTotals,
            'total_klaim' => 0.0,
            'total_versi_klinik' => 0.0,
            'total_komponen_acuan' => 0.0,
            'jumlah_komponen_acuan' => 0,
            'selisih_nominal' => 0.0,
            'selisih_arah' => null,
            'selisih_persen' => 0.0,
        ];
    }

    private function yearlyBpjsClaimRow(array $bpjsClaimSummary): ?array
    {
        $totalKlaim = (float) ($bpjsClaimSummary['total_klaim'] ?? 0);

        if ($totalKlaim <= 0) {
            return null;
        }

        $monthCells = $this->blankYearMonthCells();

        foreach (range(1, 12) as $month) {
            $monthCells[$month]['debet'] = (float) ($bpjsClaimSummary['monthly_klaim_totals'][$month] ?? 0);
        }

        /** @var MasterKomponenTransaksi|null $component */
        $component = $bpjsClaimSummary['komponen_selisih'] ?? null;

        return [
            'kode' => $component?->kode_komponen ?: 'SYS',
            'keterangan' => $component?->nama_komponen ?: 'Klaim BPJS Bulanan',
            'months' => $monthCells,
            'total_debet' => $totalKlaim,
            'total_kredit' => 0.0,
            'is_claim_row' => true,
        ];
    }

    private function resolveMappedLayananFromSimrs(
        ?string $simrsKdPoli,
        ?Collection $masterLayanan = null
    ): ?array
    {
        $masterLayanan ??= MasterLayanan::query()
            ->active()
            ->orderBy('urutan_laporan')
            ->orderBy('nama_layanan')
            ->get();

        $normalizedKdPoli = $this->normalizeLayananKey($simrsKdPoli);

        if ($normalizedKdPoli === '') {
            return null;
        }

        $matched = $masterLayanan->first(
            fn (MasterLayanan $layanan) => $this->normalizeLayananKey($layanan->simrs_kd_poli) === $normalizedKdPoli
        );

        if (! $matched) {
            return null;
        }

        return [
            'kode_layanan' => $matched->kode_layanan,
            'nama_layanan' => $matched->nama_layanan,
        ];
    }

    private function resolveMappedLayananFromStoredValue(
        ?string $storedValue,
        ?Collection $masterLayanan = null
    ): ?array
    {
        $masterLayanan ??= MasterLayanan::query()
            ->active()
            ->orderBy('urutan_laporan')
            ->orderBy('nama_layanan')
            ->get();

        $normalizedValue = $this->normalizeLayananKey($storedValue);

        if ($normalizedValue === '') {
            return null;
        }

        $matched = $masterLayanan->first(function (MasterLayanan $layanan) use ($normalizedValue) {
            return in_array($normalizedValue, $this->layananLookupKeys($layanan), true);
        });

        if (! $matched) {
            return null;
        }

        return [
            'kode_layanan' => $matched->kode_layanan,
            'nama_layanan' => $matched->nama_layanan,
        ];
    }

    private function layananLookupKeys(MasterLayanan $layanan): array
    {
        return collect([
            $layanan->kode_layanan,
            $layanan->nama_layanan,
            $layanan->simrs_kd_poli,
            $layanan->simrs_nm_poli,
        ])
            ->merge($this->layananAliases($layanan->nama_layanan))
            ->map(fn (?string $value) => $this->normalizeLayananKey($value))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function layananAliases(?string $layananName): array
    {
        $normalized = $this->normalizeLayananKey($layananName);

        return match ($normalized) {
            'klinik umum' => ['klinik umum', 'poliklinik umum', 'umum'],
            'partus' => ['partus', 'kia'],
            'curetage' => ['curetage', 'curetase', 'curettage', 'curretage'],
            default => [$normalized],
        };
    }

    private function normalizeLayananKey(?string $layananName): string
    {
        $value = strtolower(trim((string) $layananName));
        $value = str_replace(['/', '-', '_'], ' ', $value);
        $value = preg_replace('/\s+/', ' ', $value) ?: '';

        return $value;
    }

    private function periodLabel(Carbon $selectedMonth): string
    {
        $lastDay = $selectedMonth->copy()->endOfMonth()->day;

        return '1-' . $lastDay . ' ' . $selectedMonth->locale('id')->translatedFormat('F Y');
    }

    private function normalizeSelectedDate(?string $selectedDate): string
    {
        if (blank($selectedDate)) {
            return now()->toDateString();
        }

        try {
            return Carbon::createFromFormat('Y-m-d', $selectedDate)->toDateString();
        } catch (\Throwable) {
            return now()->toDateString();
        }
    }

    private function normalizeSelectedDateRange(
        ?string $startDate,
        ?string $endDate,
        ?string $fallbackDate = null
    ): array {
        $fallback = $this->normalizeSelectedDate($fallbackDate);
        $normalizedStart = $this->normalizeSelectedDate($startDate ?: $fallback);
        $normalizedEnd = $this->normalizeSelectedDate($endDate ?: $fallback);

        if ($normalizedStart > $normalizedEnd) {
            [$normalizedStart, $normalizedEnd] = [$normalizedEnd, $normalizedStart];
        }

        return [
            'start' => $normalizedStart,
            'end' => $normalizedEnd,
        ];
    }

    private function normalizeSelectedMonth(?string $selectedMonth): Carbon
    {
        if (blank($selectedMonth)) {
            return now()->startOfMonth();
        }

        try {
            if (preg_match('/^\d{4}-\d{2}$/', $selectedMonth) === 1) {
                return Carbon::createFromFormat('Y-m', $selectedMonth)->startOfMonth();
            }

            return Carbon::parse($selectedMonth)->startOfMonth();
        } catch (\Throwable) {
            return now()->startOfMonth();
        }
    }

    private function normalizeSimrsStatusLanjutKey(?string $value): string
    {
        $normalized = strtolower(trim((string) $value));

        return match (true) {
            str_contains($normalized, 'ranap') => 'ranap',
            str_contains($normalized, 'ralan') => 'ralan',
            default => 'lainnya',
        };
    }

    private function humanizeSimrsStatusLanjut(?string $value): string
    {
        return match ($this->normalizeSimrsStatusLanjutKey($value)) {
            'ranap' => 'Rawat Inap',
            'ralan' => 'Rawat Jalan',
            default => filled($value) ? trim((string) $value) : 'Belum Diisi',
        };
    }

    private function normalizeSimrsJenisKelaminKey(?string $value): string
    {
        $normalized = strtoupper(trim((string) $value));

        return match ($normalized) {
            'L' => 'l',
            'P' => 'p',
            default => 'lainnya',
        };
    }

    private function humanizeSimrsJenisKelamin(?string $value): string
    {
        return match ($this->normalizeSimrsJenisKelaminKey($value)) {
            'l' => 'Laki-laki',
            'p' => 'Perempuan',
            default => filled($value) ? trim((string) $value) : '-',
        };
    }

    private function determineRekapPenyakitAgeGroup(mixed $umurdaftar, ?string $sttsumur = null): string
    {
        $umur = is_numeric($umurdaftar) ? (float) $umurdaftar : null;
        $unit = strtolower(trim((string) $sttsumur));

        if ($umur === null) {
            return 'dewasa';
        }

        if (in_array($unit, ['th', 'tahun', 'thn', 'taun', 'yr', 'yrs', 'year', 'years'], true)) {
            return $umur < 5 ? 'anak' : 'dewasa';
        }

        if (in_array($unit, ['bln', 'bulan', 'bul', 'mo', 'mos', 'month', 'months'], true)) {
            return 'anak';
        }

        if (in_array($unit, ['hr', 'hari', 'day', 'days'], true)) {
            return 'anak';
        }

        if (in_array($unit, ['mg', 'minggu', 'week', 'weeks'], true)) {
            return 'anak';
        }

        return $umur < 5 ? 'anak' : 'dewasa';
    }

    private function classifySimrsJenisBayar(?string $penjamin, ?string $kodePenjamin = null): array
    {
        $candidate = strtolower(trim((string) ($penjamin ?: $kodePenjamin)));
        $isBpjs = $candidate !== ''
            && (
                str_contains($candidate, 'bpjs')
                || str_contains($candidate, 'jkn')
                || str_contains($candidate, 'kis')
                || str_contains($candidate, 'pbi')
            );

        return [
            'key' => $isBpjs ? 'bpjs' : 'umum',
            'label' => filled($penjamin)
                ? trim((string) $penjamin)
                : ($isBpjs ? 'BPJS' : 'Umum'),
        ];
    }

    private function monthlyReportViewData(Request $request): array
    {
        $selectedMonth = $this->normalizeSelectedMonth($request->string('bulan')->toString());
        $selectedPenjaminMode = $this->normalizeRekapPenjaminMode($request->string('penjamin')->toString());
        $clinicContext = $this->clinicContext($request, true);

        $transactionsQuery = TransaksiPasien::query()
            ->with(['komponenTransaksi', 'administrasiTransaksi'])
            ->whereYear('tanggal', $selectedMonth->year)
            ->whereMonth('tanggal', $selectedMonth->month);
        $this->scopeQueryToClinic($transactionsQuery, $clinicContext['selectedClinicId']);
        $this->scopeTransactionsByPenjaminMode($transactionsQuery, $selectedPenjaminMode);
        $transactions = $transactionsQuery->get();

        $expensesQuery = Pengeluaran::query()
            ->forBulan($selectedMonth->month, $selectedMonth->year);
        $this->scopeQueryToClinic($expensesQuery, $clinicContext['selectedClinicId']);
        $expenses = $expensesQuery->get();

        $bpjsClaimSummary = $selectedPenjaminMode === 'umum'
            ? $this->emptyMonthlyBpjsClaimSummary()
            : $this->monthlyBpjsClaimSummary($selectedMonth, $clinicContext['selectedClinicId']);
        $bpjsClaimTargetLayanan = $this->bpjsClaimTargetLayanan();

        $layananBaseRows = $this->monthlyLayananRows(
            $transactions,
            MasterLayanan::query()->active()->orderBy('urutan_laporan')->get()
        );
        $layananRows = $this->mergeBpjsClaimIntoLayananRows($layananBaseRows, $bpjsClaimSummary, $bpjsClaimTargetLayanan);

        $komponenRows = $this->monthlyKomponenRows($transactions, $this->activeMasterKomponenTransaksi());
        $administrasiRows = $this->monthlyAdministrasiRows($transactions, $this->activeMasterAdministrasiPasien());
        $fieldTransaksiBaseRows = $komponenRows->concat($administrasiRows)->values();
        $fieldTransaksiRows = $this->mergeBpjsClaimIntoFieldRows($fieldTransaksiBaseRows, $bpjsClaimSummary);
        $pengeluaranRows = $this->monthlyPengeluaranRows(
            $expenses,
            MasterKategoriPengeluaran::query()->active()->orderBy('urutan_laporan')->get()
        );

        $totalDebitLayananVersiKlinik = (float) $layananBaseRows->sum('debet');
        $totalKreditLayananVersiKlinik = (float) $layananBaseRows->sum('kredit');
        $totalDebitKomponenVersiKlinik = (float) $fieldTransaksiBaseRows->sum('debet');
        $totalKreditKomponenVersiKlinik = (float) $fieldTransaksiBaseRows->sum('kredit');
        $totalDebitLayanan = (float) $layananRows->sum('debet');
        $totalKreditLayanan = (float) $layananRows->sum('kredit');
        $totalDebitKomponen = (float) $fieldTransaksiRows->sum('debet');
        $totalKreditKomponen = (float) $fieldTransaksiRows->sum('kredit');
        $totalKreditPengeluaran = (float) $pengeluaranRows->sum('kredit');
        $totalKredit = $totalKreditKomponen + $totalKreditPengeluaran;
        $hasBpjsClaimRows = (float) $bpjsClaimSummary['total_klaim'] > 0;
        $bpjsClaimMergedIntoLayanan = $hasBpjsClaimRows
            && $layananRows->contains(fn (array $row): bool => (bool) ($row['is_claim_target'] ?? false));

        if ($hasBpjsClaimRows && ! $bpjsClaimMergedIntoLayanan) {
            $totalDebitLayanan += (float) ($bpjsClaimSummary['debet'] ?? 0);
            $totalKreditLayanan += (float) ($bpjsClaimSummary['kredit'] ?? 0);
        }

        $saldoAkhir = $totalDebitKomponen - $totalKredit;
        $isBalanced = abs($totalDebitLayanan - $totalDebitKomponen) < 0.01
            && abs($totalKreditLayanan - $totalKreditKomponen) < 0.01;

        return [
            'selectedMonth' => $selectedMonth->format('Y-m'),
            'selectedPenjaminMode' => $selectedPenjaminMode,
            'selectedPenjaminLabel' => match ($selectedPenjaminMode) {
                'bpjs' => 'BPJS',
                'umum' => 'Umum',
                default => 'Semua Penjamin',
            },
            'periodLabel' => $this->periodLabel($selectedMonth),
            'layananRows' => $layananRows,
            'komponenRows' => $fieldTransaksiRows,
            'fieldTransaksiBaseRows' => $fieldTransaksiBaseRows,
            'pengeluaranRows' => $pengeluaranRows,
            'totalDebitLayanan' => $totalDebitLayanan,
            'totalKreditLayanan' => $totalKreditLayanan,
            'totalDebitLayananVersiKlinik' => $totalDebitLayananVersiKlinik,
            'totalKreditLayananVersiKlinik' => $totalKreditLayananVersiKlinik,
            'totalDebitKomponen' => $totalDebitKomponen,
            'totalKreditKomponen' => $totalKreditKomponen,
            'totalDebitKomponenVersiKlinik' => $totalDebitKomponenVersiKlinik,
            'totalKreditKomponenVersiKlinik' => $totalKreditKomponenVersiKlinik,
            'totalKreditPengeluaran' => $totalKreditPengeluaran,
            'totalKredit' => $totalKredit,
            'saldoAkhir' => $saldoAkhir,
            'isBalanced' => $isBalanced,
            'hasBpjsClaimRows' => $hasBpjsClaimRows,
            'bpjsClaimMergedIntoLayanan' => $bpjsClaimMergedIntoLayanan,
            'bpjsClaimTargetLayanan' => $bpjsClaimTargetLayanan,
            'bpjsClaimSummary' => $bpjsClaimSummary,
            'clinicOptions' => $clinicContext['clinicOptions'],
            'showClinicFilter' => $clinicContext['showClinicFilter'],
            'selectedClinicFilter' => $clinicContext['viewingAllClinics']
                ? 'all'
                : (string) ($clinicContext['selectedClinicId'] ?: ''),
            'selectedClinicLabel' => $clinicContext['selectedClinicLabel'],
            'selectedClinicProfile' => $clinicContext['selectedClinic'],
            'viewingAllClinics' => $clinicContext['viewingAllClinics'],
        ];
    }

    private function monthlyReportPdfMeta(
        ?ClinicProfile $clinicProfile,
        bool $viewingAllClinics,
        ?User $user
    ): array {
        $locationParts = array_filter([
            filled($clinicProfile?->kecamatan) ? 'Kec. ' . trim((string) $clinicProfile->kecamatan) : null,
            filled($clinicProfile?->kota) ? trim((string) $clinicProfile->kota) : null,
            filled($clinicProfile?->provinsi) ? trim((string) $clinicProfile->provinsi) : null,
        ]);
        $addressParts = array_filter([
            filled($clinicProfile?->alamat) ? trim((string) $clinicProfile->alamat) : null,
            $locationParts !== [] ? implode(', ', $locationParts) : null,
        ]);

        $signerName = filled($clinicProfile?->penanggung_jawab)
            ? trim((string) $clinicProfile->penanggung_jawab)
            : trim((string) ($user?->name ?: 'Administrator'));
        $signatureLocation = filled($clinicProfile?->kecamatan)
            ? trim((string) $clinicProfile->kecamatan)
            : (filled($clinicProfile?->kota)
                ? trim((string) $clinicProfile->kota)
                : '................');

        return [
            'clinicPdfName' => $viewingAllClinics
                ? 'Semua Klinik'
                : trim((string) ($clinicProfile?->nama_klinik ?: $clinicProfile?->nama_pendek ?: config('app.name', 'Klink Report'))),
            'clinicPdfAddress' => $viewingAllClinics
                ? 'Laporan gabungan seluruh klinik aktif.'
                : (count($addressParts) > 0 ? implode(', ', $addressParts) : '-'),
            'clinicPdfLogoDataUri' => $this->clinicLogoDataUri($clinicProfile?->logo_path),
            'reportSignatureLocation' => $viewingAllClinics ? '................' : $signatureLocation,
            'reportSignerTitle' => 'Penanggung Jawab Klinik',
            'reportSignerName' => $signerName !== '' ? $signerName : 'Administrator',
        ];
    }

    private function clinicLogoDataUri(?string $logoPath): ?string
    {
        if (blank($logoPath) || ! Storage::disk('public')->exists($logoPath)) {
            return null;
        }

        $absolutePath = Storage::disk('public')->path($logoPath);

        if (! is_file($absolutePath) || ! is_readable($absolutePath)) {
            return null;
        }

        $mime = mime_content_type($absolutePath);

        if (! is_string($mime) || ! str_starts_with($mime, 'image/')) {
            return null;
        }

        $contents = file_get_contents($absolutePath);

        if ($contents === false) {
            return null;
        }

        return 'data:' . $mime . ';base64,' . base64_encode($contents);
    }

    private function normalizeSelectedYear(?string $selectedYear): int
    {
        $year = (int) $selectedYear;

        if ($year < 2000 || $year > 2100) {
            return now()->year;
        }

        return $year;
    }

    private function resolveLoggedInAdminName(?User $user = null): string
    {
        $user ??= auth()->user();

        if (! $user) {
            return 'Sistem';
        }

        return trim((string) ($user->pegawaiProfile?->jabatan
            ? $user->name . ' · ' . $user->pegawaiProfile->jabatan
            : $user->name));
    }

    private function dashboardGreeting(
        ?User $user = null,
        ?ClinicProfile $clinicProfile = null,
        bool $viewingAllClinics = false
    ): array
    {
        $user ??= auth()->user();
        $clinicProfile ??= $user?->clinicProfile;
        $clinicName = $viewingAllClinics
            ? 'seluruh klinik yang terhubung'
            : ($clinicProfile?->nama_pendek
                ?: $clinicProfile?->nama_klinik
                ?: ($user?->isMaster() ? 'dashboard pusat' : 'klinik'));
        $hour = now()->hour;
        $period = match (true) {
            $hour < 11 => 'Selamat pagi',
            $hour < 15 => 'Selamat siang',
            $hour < 18 => 'Selamat sore',
            default => 'Selamat malam',
        };

        $name = trim((string) ($user?->name ?: 'Tim Klinik'));
        $role = $user?->pegawaiProfile?->jabatan ?: ($user?->role ? ucfirst((string) $user->role) : 'Administrator');

        return [
            'title' => $period . ', ' . $name . '. Semangat untuk hari ini.',
            'body' => 'Semoga kabarnya baik, fokus kerja tetap terjaga, dan seluruh aktivitas di ' . $clinicName . ' hari ini berjalan rapi, tenang, dan lancar.',
            'role' => $role,
        ];
    }
}
