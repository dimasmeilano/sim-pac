<!-- Footer Laporan -->
<div class="laporan-footer"
    style="margin-top: 30px; padding-top: 10px; border-top: 1px solid #ddd; font-size: 11px; color: #666; text-align: center;">
    <p>
        <i class="fas fa-print"></i> Laporan digenerate oleh sistem SIM PAC IPNU-IPPNU<br>
        Dicetak oleh: <strong>{{ Auth::user()->name }}</strong> ({{ Auth::user()->email }})<br>
        Tanggal cetak: <strong>{{ date('d/m/Y H:i:s') }}</strong>
    </p>
</div>
