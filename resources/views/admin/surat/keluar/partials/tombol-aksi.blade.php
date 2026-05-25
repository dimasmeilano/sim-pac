@if ($suratKeluar->status == 'menunggu_ttd')
    <form action="{{ route('surat.keluar.ttd', $suratKeluar) }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-success btn-block"
            onclick="return confirm('Anda yakin ingin menandatangani surat ini?')">
            <i class="fas fa-signature"></i> Tanda Tangani Surat
        </button>
    </form>
@endif

@if ($suratKeluar->status == 'selesai')
    <a href="{{ route('surat.keluar.download', $suratKeluar) }}" class="btn btn-primary btn-block">
        <i class="fas fa-download"></i> Download PDF
    </a>
@endif

@if ($suratKeluar->status_validasi == 'draft' && auth()->user()->id == $suratKeluar->created_by)
    <div class="mt-3">
        <p class="text-muted mb-1"><small>Surat masih <strong>Draft</strong>.</small></p>
        <form action="{{ route('surat.keluar.ajukan-validasi', $suratKeluar) }}" method="POST">
            @csrf
            <div class="form-group">
                <select name="divalidasi_oleh" class="form-control form-control-sm" required>
                    <option value="">- Pilih Wakil -</option>
                    @php
                        $currentUser = auth()->user();
                        $wakilRole = null;
                        foreach ($currentUser->getRoleNames() as $role) {
                            if (str_contains($role, 'wakil')) {
                                $wakilRole = $role;
                                break;
                            }
                        }
                        $otherWakil = App\Models\User::role($wakilRole)->where('id', '!=', $currentUser->id)->get();
                    @endphp
                    @foreach ($otherWakil as $wakil)
                        <option value="{{ $wakil->id }}">{{ $wakil->name }}
                            ({{ $wakil->getRoleNames()->implode(', ') }})</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary btn-block btn-sm">
                <i class="fas fa-paper-plane"></i> Ajukan Validasi
            </button>
        </form>
    </div>
@endif

@if ($suratKeluar->status_validasi == 'menunggu_validasi_wakil' && auth()->user()->id == $suratKeluar->divalidasi_oleh)
    <div class="mt-3 border-top pt-2">
        <form action="{{ route('surat.keluar.validasi-wakil', $suratKeluar) }}" method="POST">
            @csrf
            <div class="form-group mb-2">
                <select name="status" class="form-control form-control-sm" required>
                    <option value="disetujui">✅ Disetujui</option>
                    <option value="ditolak">❌ Ditolak</option>
                </select>
            </div>
            <div class="form-group mb-2">
                <textarea name="catatan" class="form-control form-control-sm" rows="2" placeholder="Catatan (wajib jika ditolak)"></textarea>
            </div>
            <button type="submit" class="btn btn-warning btn-block btn-sm">Proses Validasi</button>
        </form>
    </div>
@endif

@if ($suratKeluar->status_validasi == 'menunggu_ttd_sekretaris' && auth()->user()->hasRole('sekretaris_pac'))
    <form action="{{ route('surat.keluar.ttd-sekretaris', $suratKeluar) }}" method="POST" class="mt-2">
        @csrf
        <button type="submit" class="btn btn-success btn-block btn-sm">
            <i class="fas fa-signature"></i> TTD Sekretaris
        </button>
    </form>
@endif

@if ($suratKeluar->status_validasi == 'menunggu_ttd_ketua' && auth()->user()->hasRole('ketua_pac'))
    <form action="{{ route('surat.keluar.ttd-ketua', $suratKeluar) }}" method="POST" class="mt-2">
        @csrf
        <button type="submit" class="btn btn-success btn-block btn-sm">
            <i class="fas fa-signature"></i> TTD Ketua
        </button>
    </form>
@endif
