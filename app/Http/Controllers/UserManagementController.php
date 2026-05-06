<?php

namespace App\Http\Controllers;

use App\Models\ClinicProfile;
use App\Models\PegawaiProfile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim($request->string('q')->toString());
        $editId = $request->integer('edit');

        $usersQuery = User::query()
            ->with(['pegawaiProfile', 'clinicProfile'])
            ->orderByDesc('is_active')
            ->orderBy('role')
            ->orderBy('name');

        if (filled($search)) {
            $usersQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('username', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhereHas('clinicProfile', function ($clinicQuery) use ($search) {
                        $clinicQuery->where('nama_klinik', 'like', '%' . $search . '%')
                            ->orWhere('nama_pendek', 'like', '%' . $search . '%')
                            ->orWhere('kode_klinik', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('pegawaiProfile', function ($pegawaiQuery) use ($search) {
                        $pegawaiQuery->where('jabatan', 'like', '%' . $search . '%')
                            ->orWhere('unit_kerja', 'like', '%' . $search . '%')
                            ->orWhere('nip', 'like', '%' . $search . '%');
                    });
            });
        }

        $users = $usersQuery->get();
        $editingUser = $editId > 0
            ? User::query()->with(['pegawaiProfile', 'clinicProfile'])->find($editId)
            : null;
        $clinicOptions = ClinicProfile::query()
            ->active()
            ->orderBy('nama_klinik')
            ->get();

        return view('pages.manajemen-user', [
            'search' => $search,
            'users' => $users,
            'editingUser' => $editingUser,
            'clinicOptions' => $clinicOptions,
            'stats' => [
                'total' => User::query()->count(),
                'master' => User::query()->where('role', 'master')->count(),
                'admin' => User::query()->where('role', 'admin')->count(),
                'staff' => User::query()->where('role', 'staff')->count(),
                'active' => User::query()->where('is_active', true)->count(),
                'klinik' => $clinicOptions->count(),
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedUserData($request);

        DB::transaction(function () use ($data) {
            $user = User::query()->create($data['user']);
            $user->pegawaiProfile()->create($data['profile']);
        });

        return redirect()
            ->route('manajemen-user')
            ->with('success', 'User baru berhasil ditambahkan.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $this->validatedUserData($request, $user->id);

        DB::transaction(function () use ($user, $data) {
            $user->update($data['user']);
            $user->pegawaiProfile()->updateOrCreate(
                ['user_id' => $user->id],
                $data['profile']
            );
        });

        return redirect()
            ->route('manajemen-user')
            ->with('success', 'User berhasil diperbarui.');
    }

    private function validatedUserData(Request $request, ?int $ignoreId = null): array
    {
        $passwordRule = $ignoreId
            ? ['nullable', 'string', 'min:8', 'max:255']
            : ['required', 'string', 'min:8', 'max:255'];

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => [
                'required',
                'string',
                'max:100',
                Rule::unique('users', 'username')->ignore($ignoreId),
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($ignoreId),
            ],
            'password' => $passwordRule,
            'role' => ['required', 'string', Rule::in(['master', 'admin', 'staff'])],
            'clinic_profile_id' => [
                Rule::requiredIf(fn () => strtolower((string) $request->input('role')) !== 'master'),
                'nullable',
                'integer',
                'exists:clinic_profiles,id',
            ],
            'is_active' => ['nullable', 'boolean'],
            'nip' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('pegawai_profiles', 'nip')->ignore($ignoreId, 'user_id'),
            ],
            'jabatan' => ['nullable', 'string', 'max:255'],
            'unit_kerja' => ['nullable', 'string', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:50'],
            'bio' => ['nullable', 'string'],
        ]);

        $userPayload = [
            'name' => trim((string) $data['name']),
            'username' => strtolower(trim((string) $data['username'])),
            'email' => strtolower(trim((string) $data['email'])),
            'role' => strtolower(trim((string) $data['role'])),
            'clinic_profile_id' => strtolower(trim((string) $data['role'])) === 'master'
                ? null
                : (filled($data['clinic_profile_id'] ?? null)
                    ? (int) $data['clinic_profile_id']
                    : null),
            'is_active' => $request->boolean('is_active'),
        ];

        if (filled($data['password'] ?? null)) {
            $userPayload['password'] = $data['password'];
        }

        return [
            'user' => $userPayload,
            'profile' => [
                'nip' => filled($data['nip'] ?? null) ? trim((string) $data['nip']) : null,
                'jabatan' => filled($data['jabatan'] ?? null) ? trim((string) $data['jabatan']) : null,
                'unit_kerja' => filled($data['unit_kerja'] ?? null) ? trim((string) $data['unit_kerja']) : null,
                'phone_number' => filled($data['phone_number'] ?? null) ? trim((string) $data['phone_number']) : null,
                'bio' => filled($data['bio'] ?? null) ? trim((string) $data['bio']) : null,
            ],
        ];
    }
}
