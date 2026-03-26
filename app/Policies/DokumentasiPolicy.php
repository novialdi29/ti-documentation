<?php

namespace App\Policies;

use App\Models\Dokumentasi;
use App\Models\User;

class DokumentasiPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'teknisi', 'verifikator']);
    }

    public function view(User $user, Dokumentasi $dokumentasi): bool
    {
        if ($user->hasAnyRole(['admin', 'verifikator'])) {
            return true;
        }

        return $user->hasRole('teknisi') && $dokumentasi->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'teknisi']);
    }

    public function update(User $user, Dokumentasi $dokumentasi): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        return $user->hasRole('teknisi') && $dokumentasi->user_id === $user->id;
    }

    public function delete(User $user, Dokumentasi $dokumentasi): bool
    {
        return $user->hasRole('admin');
    }

    public function restore(User $user, Dokumentasi $dokumentasi): bool
    {
        return $user->hasRole('admin');
    }

    public function forceDelete(User $user, Dokumentasi $dokumentasi): bool
    {
        return $user->hasRole('admin');
    }

    public function approve(User $user, Dokumentasi $dokumentasi): bool
    {
        return $user->hasAnyRole(['admin', 'verifikator']) && $dokumentasi->status === 'pending';
    }

    public function reject(User $user, Dokumentasi $dokumentasi): bool
    {
        return $user->hasAnyRole(['admin', 'verifikator']) && $dokumentasi->status === 'pending';
    }
}
