@php
    $formattedMonth = \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->locale('id')->translatedFormat('F Y');
    $clinicInitials = collect(preg_split('/\s+/', trim((string) $clinicPdfName)))
        ->filter()
        ->take(2)
        ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
        ->implode('');
    $bpjsClaimCode = $bpjsClaimSummary['komponen_selisih']?->kode_komponen ?: 'BPJS';
    $bpjsClaimName = $bpjsClaimSummary['komponen_selisih']?->nama_komponen ?: 'Klaim BPJS Bulanan';
    $bpjsClaimCount = (int) ($bpjsClaimSummary['count'] ?? 0);
    $bpjsClaimDebit = (float) ($bpjsClaimSummary['debet'] ?? 0);
    $bpjsClaimKredit = (float) ($bpjsClaimSummary['kredit'] ?? 0);
    $bpjsClaimSelisih = (float) ($bpjsClaimSummary['selisih_nominal'] ?? 0);
    $bpjsClaimDetailLabel = $bpjsClaimCount > 0
        ? $bpjsClaimName . ' - ' . $bpjsClaimCount . ' data klaim'
        : $bpjsClaimName;
    $totalDebitKeseluruhan = $totalDebitKomponen;
    $formatNominal = function ($value) {
        $value = (float) $value;

        if (abs($value) < 0.01) {
            return '-';
        }

        if ($value < 0) {
            return '-Rp ' . number_format(abs($value), 0, ',', '.');
        }

        return 'Rp ' . number_format($value, 0, ',', '.');
    };
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Rekap Bulanan {{ $formattedMonth }}</title>
    <style>
        @page {
            size: legal portrait;
            margin: 18px 20px;
        }

        body {
            color: #1f2937;
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            line-height: 1.24;
            margin: 0;
        }

        .page {
            width: 100%;
        }

        .report-header {
            margin-bottom: 10px;
            padding: 2px 0 10px;
        }

        .report-header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .report-header-table td {
            vertical-align: middle;
        }

        .logo-cell {
    width: 105px;
    padding-right: 14px;
    vertical-align: middle;
}

.logo-box {
    display: flex;
    width: 88px;
    height: 88px;
    align-items: center;
    justify-content: center;
    overflow: hidden;

    background: #ffffff;
    border: 1px solid #dbe3ea;
    border-radius: 12px;
    box-shadow: 0 4px 14px rgba(15, 23, 42, 0.08);
}

.logo-box img {
    max-width: 78px;
    max-height: 78px;
    object-fit: contain;
}

.logo-fallback {
    color: #0f766e;
    font-size: 24px;
    font-weight: 800;
    line-height: 1;
    letter-spacing: 0.06em;
}

.header-copy {
    text-align: center;
    vertical-align: middle;
}

.report-header p {
    margin: 2px 0 0;
    color: #475569;
    font-size: 12px;
    line-height: 1.18;
}

.report-eyebrow {
    margin: 0 0 4px;
    color: #0f766e;
    font-size: 10.5px;
    font-weight: 700;
    line-height: 1.1;
    letter-spacing: 0.16em;
    text-transform: uppercase;
}

.clinic-name {
    margin: 0;
    color: #0f172a;
    font-family: "DejaVu Serif", serif;
    font-size: 15px;
    font-weight: 700;
    line-height: 1.1;
    letter-spacing: 0.035em;
    text-transform: uppercase;
}

.clinic-address {
    margin-top: 2px;
    color: #475569;
    font-size: 12.5px;
    font-weight: 400;
    line-height: 1.18;
    letter-spacing: 0.01em;
}

.report-title {
    display: inline-block;
    margin-top: 8px;
    padding-bottom: 3px;

    color: #0f766e;
    font-size: 14px;
    font-weight: 700;
    line-height: 1.1;
    letter-spacing: 0.055em;
    text-transform: uppercase;

    border-bottom: 1px solid rgba(15, 118, 110, 0.28);
}

.report-subtitle {
    margin-top: 3px;
    color: #64748b;
    font-size: 12.5px;
    font-weight: 500;
    line-height: 1.15;
}

        .header-separator {
            height: 5px;
            margin-top: 12px;
            background: #0f8a60;
        }

        .report-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .report-table th,
        .report-table td {
            border: 1px solid #94a3b8;
            padding: 5px 7px;
            vertical-align: middle;
        }

        .report-table th {
            background: #f1f5f9;
            font-size: 10px;
            font-weight: 700;
            text-align: left;
            text-transform: uppercase;
            line-height: 1.1;
        }

        .report-table .number {
            text-align: right;
            white-space: nowrap;
        }

        .report-table th.center-head,
        .report-table th.number {
            text-align: center;
        }

        .report-table .date {
            white-space: nowrap;
        }

        .report-table .section-row td {
            background: #f1f5f9;
            font-weight: 700;
            text-transform: uppercase;
        }

        .report-table .subtotal-row td,
        .report-table .total-row td {
            font-weight: 700;
        }

        .report-table .subtotal-row td {
            background: #f8fafc;
        }

        .report-table .total-row td {
            background: #eff6ff;
        }

        .report-table .balance-row td {
            background: #fff4d6;
            color: #9a3412;
            font-weight: 700;
        }

        .signature-wrap {
            width: 100%;
            margin-top: 18px;
        }

        .signature-box {
            width: 280px;
            margin-left: auto;
            text-align: center;
            page-break-inside: avoid;
        }

        .signature-place {
            margin-bottom: 8px;
            font-size: 11px;
        }

        .signature-title {
            font-size: 11px;
        }

        .signature-space {
            height: 60px;
        }

        .signature-name {
            font-weight: 700;
            text-decoration: underline;
        }

        .signature-role {
            margin-top: 2px;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="report-header">
            <table class="report-header-table">
                <tr>
                    <td class="logo-cell">
                        <div class="logo-box">
                            @if ($clinicPdfLogoDataUri)
                                <img src="{{ $clinicPdfLogoDataUri }}" alt="Logo klinik">
                            @else
                                <div class="logo-fallback">{{ $clinicInitials ?: 'KR' }}</div>
                            @endif
                        </div>
                    </td>
                    <td class="header-copy">
                        <div class="report-eyebrow">Laporan Keuangan Klinik</div>
                        <div class="clinic-name">{{ $clinicPdfName }}</div>
                        <div class="clinic-address">{{ $clinicPdfAddress }}</div>
                        <div class="report-title">Laporan Keuangan Bulan {{ $formattedMonth }}</div>
                        <div class="report-subtitle">Periode {{ $periodLabel }} - Mode Penjamin {{ $selectedPenjaminLabel }}</div>
                    </td>
                </tr>
            </table>
            <div class="header-separator"></div>
        </div>

        <table class="report-table">
            <thead>
                <tr>
                    <th style="width: 18%;">Tanggal</th>
                    <th style="width: 38%;">Keterangan</th>
                    <th style="width: 12%;" class="center-head">Kode</th>
                    <th style="width: 16%;" class="number">Debet</th>
                    <th style="width: 16%;" class="number">Kredit</th>
                </tr>
            </thead>
            <tbody>
                <tr class="section-row">
                    <td colspan="5">A. Pendapatan Berdasarkan Layanan</td>
                </tr>
                @foreach ($layananRows as $row)
                    <tr>
                        <td class="date">{{ $periodLabel }}</td>
                        <td>{{ $row['keterangan'] }}</td>
                        <td>{{ $row['kode'] }}</td>
                        <td class="number">{{ $formatNominal($row['debet']) }}</td>
                        <td class="number">{{ $formatNominal($row['kredit']) }}</td>
                    </tr>
                @endforeach
                <tr class="subtotal-row">
                    <td colspan="3">Subtotal</td>
                    <td class="number">{{ $formatNominal($totalDebitLayananVersiKlinik) }}</td>
                    <td class="number">{{ $formatNominal($totalKreditLayananVersiKlinik) }}</td>
                </tr>
                @if ($hasBpjsClaimRows && ! $bpjsClaimMergedIntoLayanan)
                    <tr class="subtotal-row">
                        <td class="date">{{ $periodLabel }}</td>
                        <td>{{ $bpjsClaimDetailLabel }}</td>
                        <td>{{ $bpjsClaimCode }}</td>
                        <td class="number">{{ $formatNominal($bpjsClaimDebit) }}</td>
                        <td class="number">{{ $formatNominal($bpjsClaimKredit) }}</td>
                    </tr>
                @endif
                @if ($hasBpjsClaimRows)
                    <tr class="total-row">
                        <td colspan="3">Total Setelah Klaim BPJS</td>
                        <td class="number">{{ $formatNominal($totalDebitLayanan) }}</td>
                        <td class="number">{{ $formatNominal($totalKreditLayanan) }}</td>
                    </tr>
                    <tr class="balance-row">
                        <td class="date">{{ $periodLabel }}</td>
                        <td>Selisih Klaim vs Versi Klinik</td>
                        <td>INFO</td>
                        <td class="number">{{ $bpjsClaimSelisih > 0 ? $formatNominal($bpjsClaimSelisih) : '-' }}</td>
                        <td class="number">{{ $bpjsClaimSelisih < 0 ? $formatNominal(abs($bpjsClaimSelisih)) : '-' }}</td>
                    </tr>
                @else
                    <tr class="total-row">
                        <td colspan="3">Total Pendapatan Berdasarkan Layanan</td>
                        <td class="number">{{ $formatNominal($totalDebitLayanan) }}</td>
                        <td class="number">{{ $formatNominal($totalKreditLayanan) }}</td>
                    </tr>
                @endif

                <tr class="section-row">
                    <td colspan="5">B. Detail Transaksi</td>
                </tr>
                @foreach ($komponenRows as $row)
                    <tr>
                        <td class="date">{{ $periodLabel }}</td>
                        <td>{{ $row['keterangan'] }}</td>
                        <td>{{ $row['kode'] }}</td>
                        <td class="number">{{ $formatNominal($row['debet']) }}</td>
                        <td class="number">{{ $formatNominal($row['kredit']) }}</td>
                    </tr>
                @endforeach
                <tr class="subtotal-row">
                    <td colspan="3">Subtotal Detail Versi Klinik</td>
                    <td class="number">{{ $formatNominal($totalDebitKomponenVersiKlinik) }}</td>
                    <td class="number">{{ $formatNominal($totalKreditKomponenVersiKlinik) }}</td>
                </tr>
                @if ($hasBpjsClaimRows)
                    <tr class="total-row">
                        <td colspan="3">Total Detail Setelah Klaim BPJS</td>
                        <td class="number">{{ $formatNominal($totalDebitKomponen) }}</td>
                        <td class="number">{{ $formatNominal($totalKreditKomponen) }}</td>
                    </tr>
                @else
                    <tr class="total-row">
                        <td colspan="3">Total Detail Transaksi</td>
                        <td class="number">{{ $formatNominal($totalDebitKomponen) }}</td>
                        <td class="number">{{ $formatNominal($totalKreditKomponen) }}</td>
                    </tr>
                @endif

                <tr class="section-row">
                    <td colspan="5">C. Pengeluaran</td>
                </tr>
                @forelse ($pengeluaranPrintRows as $row)
                    <tr>
                        <td class="date">{{ $periodLabel }}</td>
                        <td>{{ $row['keterangan'] }}</td>
                        <td>{{ $row['kode'] }}</td>
                        <td class="number">-</td>
                        <td class="number">{{ $formatNominal($row['kredit']) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td class="date">{{ $periodLabel }}</td>
                        <td colspan="4">Tidak ada pengeluaran untuk periode ini.</td>
                    </tr>
                @endforelse
                <tr class="subtotal-row">
                    <td colspan="3">Subtotal Pengeluaran</td>
                    <td class="number">-</td>
                    <td class="number">{{ $formatNominal($totalKreditPengeluaran) }}</td>
                </tr>

                <tr class="total-row">
                    <td colspan="3">Total Keseluruhan</td>
                    <td class="number">{{ $formatNominal($totalDebitKeseluruhan) }}</td>
                    <td class="number">{{ $formatNominal($totalKredit) }}</td>
                </tr>
                <tr class="balance-row">
                    <td colspan="3">Saldo Bulanan</td>
                    <td class="number">-</td>
                    <td class="number">{{ $formatNominal($saldoAkhir) }}</td>
                </tr>
            </tbody>
        </table>

        <div class="signature-wrap">
            <div class="signature-box">
                <div class="signature-place">{{ $reportSignatureLocation }}, .......... {{ $formattedMonth }}</div>
                <div class="signature-title">{{ $reportSignerTitle }}</div>
                <div class="signature-space"></div>
                <div class="signature-name">{{ $reportSignerName }}</div>
            </div>
        </div>
    </div>
</body>
</html>
