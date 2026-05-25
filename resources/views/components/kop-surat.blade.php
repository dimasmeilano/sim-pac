@props(['organization', 'jenis' => 'bersama'])

<div style="text-align: center; margin-bottom: 20px;">
    @php
        $kopFile = null;
        if ($jenis == 'ipnu' && $organization->kop_ipnu) {
            $kopFile = $organization->kop_ipnu;
        } elseif ($jenis == 'ippnu' && $organization->kop_ippnu) {
            $kopFile = $organization->kop_ippnu;
        } elseif ($organization->kop_bersama) {
            $kopFile = $organization->kop_bersama;
        }
    @endphp

    @if ($kopFile)
        <img src="{{ asset('storage/' . $kopFile) }}" alt="Kop Surat" style="width: 100%; max-width: 800px;">
    @else
        <!-- Fallback HTML -->
        <div style="font-family: 'Times New Roman', serif;">
            <h2>PIMPINAN {{ strtoupper($organization->type == 'pac' ? 'ANAK CABANG' : 'RANTING') }}</h2>
            <h3>
                @if ($jenis == 'ipnu')
                    IKATAN PELAJAR NAHDLATUL ULAMA
                @elseif($jenis == 'ippnu')
                    IKATAN PELAJAR PUTRI NAHDLATUL ULAMA
                @else
                    IKATAN PELAJAR NAHDLATUL ULAMA - IPPNU
                @endif
            </h3>
            <h3>{{ strtoupper($organization->name) }}</h3>
        </div>
    @endif
</div>
