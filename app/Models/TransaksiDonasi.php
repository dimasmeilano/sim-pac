<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiDonasi extends Model
{
    protected $guarded = [];

    public function campaign()
    {
        return $this->belongsTo(CampaignDonasi::class, 'campaign_donasi_id');
    }

    public function alumni()
    {
        return $this->belongsTo(Alumni::class);
    }

    public function verifikator()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
