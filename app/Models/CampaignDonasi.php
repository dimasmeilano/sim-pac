<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToOrganization;

class CampaignDonasi extends Model
{
    use BelongsToOrganization; // Gembok Multi-Tenant Aktif!

    protected $guarded = [];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function transaksis()
    {
        return $this->hasMany(TransaksiDonasi::class);
    }
}
