<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Kode</th>
                <th>Uraian</th>
                <th class="text-right">Masuk (Rp)</th>
                <th class="text-right">Keluar (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @forelse($transaksi as $item)
                <tr>
                    <td>{{ $no++ }}</td>
                    <td>{{ date('d/m/Y', strtotime($item->tanggal)) }}</td>
                    <td>{{ $item->kode_transaksi }}</td>
                    <td>{{ $item->judul }}</td>
                    <td class="text-right">
                        {{ $item->jenis == 'masuk' ? number_format($item->nominal, 0, ',', '.') : '-' }}</td>
                    <td class="text-right">
                        {{ $item->jenis == 'keluar' ? number_format($item->nominal, 0, ',', '.') : '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="text-right"><strong>TOTAL</strong></td>
                <td class="text-right"><strong>{{ number_format($totalMasuk, 0, ',', '.') }}</strong></td>
                <td class="text-right"><strong>{{ number_format($totalKeluar, 0, ',', '.') }}</strong></td>
            </tr>
            <tr>
                <td colspan="4" class="text-right"><strong>SALDO</strong></td>
                <td colspan="2" class="text-right"><strong>{{ number_format($saldo, 0, ',', '.') }}</strong></td>
            </tr>
        </tfoot>
    </table>
</div>
