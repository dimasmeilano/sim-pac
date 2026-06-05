<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

trait BelongsToOrganization
{
    protected static function bootBelongsToOrganization()
    {
        // Menambahkan Global Scope bernama 'organization'
        // Use call_user_func to avoid static analysis issues when this trait
        // is parsed outside the Eloquent Model context.
        call_user_func([get_called_class(), 'addGlobalScope'], new class implements Scope {
            public function apply(Builder $builder, $model)
            {
                // GANTI Auth::check() MENJADI Auth::hasUser()
                // hasUser() akan mengecek memori tanpa memicu pencarian database baru
                if (Auth::hasUser()) {
                    $user = Auth::user();

                    // 1. Super Admin kebal dari aturan ini
                    if (method_exists($user, 'hasRole') && $user->hasRole('super_admin')) {
                        return;
                    }

                    // 2. Filter otomatis: User hanya bisa melihat data milik organisasinya sendiri.
                    $builder->where('organization_id', $user->organization_id);
                }
            }
        });
    }
}
