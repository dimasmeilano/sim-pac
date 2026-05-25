<?php

namespace App\Console\Commands;

use App\Models\Kegiatan;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('kegiatan:update-status')]
#[Description('Command description')]
class UpdateKegiatanStatus extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $kegiatan = Kegiatan::where('status', '!=', 'batal')->get();
        $count = 0;

        foreach ($kegiatan as $item) {
            $oldStatus = $item->status;
            $item->updateStatusByTime();

            if ($oldStatus != $item->status) {
                $count++;
                $this->info("Kegiatan '{$item->nama}' berubah dari {$oldStatus} menjadi {$item->status}");
            }
        }

        $this->info("Selesai. {$count} kegiatan berubah status.");
    }
}
