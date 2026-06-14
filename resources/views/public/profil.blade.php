@extends('layouts.public')

@section('title', 'Profil Organisasi')

@section('content')
    <div class="container mt-5 mb-5">

        <div class="text-center mb-5">
            <h1 class="font-serif font-weight-bold text-dark">Profil Organisasi</h1>
            <p class="text-muted lead">Mengenal lebih dekat Pimpinan Anak Cabang IPNU IPPNU.</p>
        </div>

        <div class="card shadow-sm border-0 rounded-lg">
            <div class="card-header bg-white p-0 border-bottom-0">
                <ul class="nav nav-tabs nav-justified" id="profilTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active font-weight-bold py-3 text-success" id="sejarah-tab" data-toggle="tab"
                            href="#sejarah" role="tab"><i class="fas fa-history mr-2"></i>Sejarah Singkat</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold py-3 text-success" id="visimisi-tab" data-toggle="tab"
                            href="#visimisi" role="tab"><i class="fas fa-bullseye mr-2"></i>Visi & Misi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold py-3 text-success" id="lambang-tab" data-toggle="tab"
                            href="#lambang" role="tab"><i class="fas fa-certificate mr-2"></i>Makna Lambang</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold py-3 text-success" id="struktur-tab" data-toggle="tab"
                            href="#struktur" role="tab"><i class="fas fa-sitemap mr-2"></i>Struktur BPH</a>
                    </li>
                </ul>
            </div>

            <div class="card-body p-4 p-md-5">
                <div class="tab-content" id="profilTabContent">

                    <div class="tab-pane fade show active" id="sejarah" role="tabpanel">
                        <h3 class="font-serif mb-4 text-dark border-bottom pb-2">Sejarah Singkat PAC</h3>
                        <div class="text-justify text-muted" style="line-height: 1.8; font-size: 16px;">
                            {!! $profil->sejarah_singkat ?? '<p>Data sejarah belum ditambahkan.</p>' !!}
                        </div>
                    </div>

                    <div class="tab-pane fade" id="visimisi" role="tabpanel">
                        <h3 class="font-serif mb-4 text-dark border-bottom pb-2">Visi & Misi</h3>
                        <div class="text-justify text-muted" style="line-height: 1.8; font-size: 16px;">
                            {!! $profil->visi_misi ?? '<p>Data visi & misi belum ditambahkan.</p>' !!}
                        </div>
                    </div>

                    <div class="tab-pane fade" id="lambang" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <h4 class="font-serif text-success border-bottom pb-2 mb-4">
                                    <i class="fas fa-certificate mr-2"></i>Filosofi Lambang IPNU
                                </h4>
                                <div class="text-center mb-4">
                                    {{-- Ganti nama file 'logo-ipnu.png' dengan nama file logo Anda di folder public --}}
                                    <img src="{{ asset('images/logo-ipnu.png') }}" alt="Logo IPNU" class="img-fluid"
                                        style="max-height: 180px;">
                                </div>
                                <div class="text-justify text-muted" style="line-height: 1.8; font-size: 15px;">
                                    {!! $profil->makna_lambang_ipnu ?? '<p>Data makna lambang IPNU belum ditambahkan.</p>' !!}
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <h4 class="font-serif text-success border-bottom pb-2 mb-4">
                                    <i class="fas fa-certificate mr-2"></i>Filosofi Lambang IPPNU
                                </h4>
                                <div class="text-center mb-4">
                                    {{-- Ganti nama file 'logo-ippnu.png' dengan nama file logo Anda di folder public --}}
                                    <img src="{{ asset('images/logo-ippnu.png') }}" alt="Logo IPPNU" class="img-fluid"
                                        style="max-height: 180px;">
                                </div>
                                <div class="text-justify text-muted" style="line-height: 1.8; font-size: 15px;">
                                    {!! $profil->makna_lambang_ippnu ?? '<p>Data makna lambang IPPNU belum ditambahkan.</p>' !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="struktur" role="tabpanel">
                        <div class="alert alert-success mb-4">
                            <i class="fas fa-info-circle mr-2"></i> Berikut adalah susunan Badan Pengurus Harian (BPH)
                            Pimpinan Anak Cabang.
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <div class="card bg-light border-0">
                                    <div class="card-header bg-success text-white font-weight-bold text-center">
                                        BPH PAC IPNU (PUTRA)
                                    </div>
                                    <ul class="list-group list-group-flush text-dark">
                                        @if ($pac_ipnu)
                                            <li class="list-group-item d-flex justify-content-between"><span>Ketua</span>
                                                <strong>{{ $pac_ipnu->ketua->name ?? 'Belum Diatur' }}</strong>
                                            </li>
                                            @if ($pac_ipnu->wakil_ketua_1_id)
                                                <li class="list-group-item d-flex justify-content-between text-muted"><span
                                                        class="pl-3">↳ Wakil Ketua 1</span>
                                                    <strong>{{ $pac_ipnu->wakilKetua1->name ?? '' }}</strong>
                                                </li>
                                            @endif
                                            @if ($pac_ipnu->wakil_ketua_2_id)
                                                <li class="list-group-item d-flex justify-content-between text-muted"><span
                                                        class="pl-3">↳ Wakil Ketua 2</span>
                                                    <strong>{{ $pac_ipnu->wakilKetua2->name ?? '' }}</strong>
                                                </li>
                                            @endif
                                            @if ($pac_ipnu->wakil_ketua_3_id)
                                                <li class="list-group-item d-flex justify-content-between text-muted"><span
                                                        class="pl-3">↳ Wakil Ketua 3</span>
                                                    <strong>{{ $pac_ipnu->wakilKetua3->name ?? '' }}</strong>
                                                </li>
                                            @endif
                                            @if ($pac_ipnu->wakil_ketua_4_id)
                                                <li class="list-group-item d-flex justify-content-between text-muted"><span
                                                        class="pl-3">↳ Wakil Ketua 4</span>
                                                    <strong>{{ $pac_ipnu->wakilKetua4->name ?? '' }}</strong>
                                                </li>
                                            @endif
                                            @if ($pac_ipnu->wakil_ketua_5_id)
                                                <li class="list-group-item d-flex justify-content-between text-muted"><span
                                                        class="pl-3">↳ Wakil Ketua 5</span>
                                                    <strong>{{ $pac_ipnu->wakilKetua5->name ?? '' }}</strong>
                                                </li>
                                            @endif
                                            <li class="list-group-item d-flex justify-content-between">
                                                <span>Sekretaris</span>
                                                <strong>{{ $pac_ipnu->sekretaris->name ?? 'Belum Diatur' }}</strong>
                                            </li>
                                            @if ($pac_ipnu->wakil_sekretaris_1_id)
                                                <li class="list-group-item d-flex justify-content-between text-muted"><span
                                                        class="pl-3">↳ Wakil Sekretaris 1</span>
                                                    <strong>{{ $pac_ipnu->wakilSekretaris1->name ?? '' }}</strong>
                                                </li>
                                            @endif
                                            @if ($pac_ipnu->wakil_sekretaris_2_id)
                                                <li class="list-group-item d-flex justify-content-between text-muted"><span
                                                        class="pl-3">↳ Wakil Sekretaris 2</span>
                                                    <strong>{{ $pac_ipnu->wakilSekretaris2->name ?? '' }}</strong>
                                                </li>
                                            @endif
                                            @if ($pac_ipnu->wakil_sekretaris_3_id)
                                                <li class="list-group-item d-flex justify-content-between text-muted"><span
                                                        class="pl-3">↳ Wakil Sekretaris 3</span>
                                                    <strong>{{ $pac_ipnu->wakilSekretaris3->name ?? '' }}</strong>
                                                </li>
                                            @endif
                                            @if ($pac_ipnu->wakil_sekretaris_4_id)
                                                <li class="list-group-item d-flex justify-content-between text-muted"><span
                                                        class="pl-3">↳ Wakil Sekretaris 4</span>
                                                    <strong>{{ $pac_ipnu->wakilSekretaris4->name ?? '' }}</strong>
                                                </li>
                                            @endif
                                            @if ($pac_ipnu->wakil_sekretaris_5_id)
                                                <li class="list-group-item d-flex justify-content-between text-muted"><span
                                                        class="pl-3">↳ Wakil Sekretaris 5</span>
                                                    <strong>{{ $pac_ipnu->wakilSekretaris5->name ?? '' }}</strong>
                                                </li>
                                            @endif
                                            <li class="list-group-item d-flex justify-content-between">
                                                <span>Bendahara</span>
                                                <strong>{{ $pac_ipnu->bendahara->name ?? 'Belum Diatur' }}</strong>
                                            </li>
                                            @if ($pac_ipnu->wakil_bendahara_1_id)
                                                <li class="list-group-item d-flex justify-content-between text-muted"><span
                                                        class="pl-3">↳ Wakil Bendahara 1</span>
                                                    <strong>{{ $pac_ipnu->wakilBendahara1->name ?? '' }}</strong>
                                                </li>
                                            @endif
                                            @if ($pac_ipnu->wakil_bendahara_2_id)
                                                <li class="list-group-item d-flex justify-content-between text-muted"><span
                                                        class="pl-3">↳ Wakil Bendahara 2</span>
                                                    <strong>{{ $pac_ipnu->wakilBendahara2->name ?? '' }}</strong>
                                                </li>
                                            @endif
                                            @if ($pac_ipnu->wakil_bendahara_3_id)
                                                <li class="list-group-item d-flex justify-content-between text-muted"><span
                                                        class="pl-3">↳ Wakil Bendahara 3</span>
                                                    <strong>{{ $pac_ipnu->wakilBendahara3->name ?? '' }}</strong>
                                                </li>
                                            @endif
                                            {{-- Tambahkan Waka 1-5 di sini jika diperlukan --}}
                                        @else
                                            <li class="list-group-item text-center text-muted">Data Kepengurusan Belum
                                                Tersedia</li>
                                        @endif
                                    </ul>
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <div class="card bg-light border-0">
                                    <div class="card-header bg-success text-white font-weight-bold text-center">
                                        BPH PAC IPPNU (PUTRI)
                                    </div>
                                    <ul class="list-group list-group-flush text-dark">
                                        @if ($pac_ippnu)
                                            <li class="list-group-item d-flex justify-content-between"><span>Ketua</span>
                                                <strong>{{ $pac_ippnu->ketua->name ?? 'Belum Diatur' }}</strong>
                                            </li>
                                            @if ($pac_ippnu->wakil_ketua_1_id)
                                                <li class="list-group-item d-flex justify-content-between text-muted"><span
                                                        class="pl-3">↳ Wakil Ketua 1</span>
                                                    <strong>{{ $pac_ippnu->wakilKetua1->name ?? '' }}</strong>
                                                </li>
                                            @endif
                                            @if ($pac_ippnu->wakil_ketua_2_id)
                                                <li class="list-group-item d-flex justify-content-between text-muted"><span
                                                        class="pl-3">↳ Wakil Ketua 2</span>
                                                    <strong>{{ $pac_ippnu->wakilKetua2->name ?? '' }}</strong>
                                                </li>
                                            @endif
                                            @if ($pac_ippnu->wakil_ketua_3_id)
                                                <li class="list-group-item d-flex justify-content-between text-muted"><span
                                                        class="pl-3">↳ Wakil Ketua 3</span>
                                                    <strong>{{ $pac_ippnu->wakilKetua3->name ?? '' }}</strong>
                                                </li>
                                            @endif
                                            @if ($pac_ippnu->wakil_ketua_4_id)
                                                <li class="list-group-item d-flex justify-content-between text-muted"><span
                                                        class="pl-3">↳ Wakil Ketua 4</span>
                                                    <strong>{{ $pac_ippnu->wakilKetua4->name ?? '' }}</strong>
                                                </li>
                                            @endif
                                            @if ($pac_ippnu->wakil_ketua_5_id)
                                                <li class="list-group-item d-flex justify-content-between text-muted"><span
                                                        class="pl-3">↳ Wakil Ketua 5</span>
                                                    <strong>{{ $pac_ippnu->wakilKetua5->name ?? '' }}</strong>
                                                </li>
                                            @endif
                                            <li class="list-group-item d-flex justify-content-between">
                                                <span>Sekretaris</span>
                                                <strong>{{ $pac_ippnu->sekretaris->name ?? 'Belum Diatur' }}</strong>
                                            </li>
                                            @if ($pac_ippnu->wakil_sekretaris_1_id)
                                                <li class="list-group-item d-flex justify-content-between text-muted"><span
                                                        class="pl-3">↳ Wakil Sekretaris 1</span>
                                                    <strong>{{ $pac_ippnu->wakilSekretaris1->name ?? '' }}</strong>
                                                </li>
                                            @endif
                                            @if ($pac_ippnu->wakil_sekretaris_2_id)
                                                <li class="list-group-item d-flex justify-content-between text-muted"><span
                                                        class="pl-3">↳ Wakil Sekretaris 2</span>
                                                    <strong>{{ $pac_ippnu->wakilSekretaris2->name ?? '' }}</strong>
                                                </li>
                                            @endif
                                            @if ($pac_ippnu->wakil_sekretaris_3_id)
                                                <li class="list-group-item d-flex justify-content-between text-muted"><span
                                                        class="pl-3">↳ Wakil Sekretaris 3</span>
                                                    <strong>{{ $pac_ippnu->wakilSekretaris3->name ?? '' }}</strong>
                                                </li>
                                            @endif
                                            @if ($pac_ippnu->wakil_sekretaris_4_id)
                                                <li class="list-group-item d-flex justify-content-between text-muted"><span
                                                        class="pl-3">↳ Wakil Sekretaris 4</span>
                                                    <strong>{{ $pac_ippnu->wakilSekretaris4->name ?? '' }}</strong>
                                                </li>
                                            @endif
                                            @if ($pac_ippnu->wakil_sekretaris_5_id)
                                                <li class="list-group-item d-flex justify-content-between text-muted"><span
                                                        class="pl-3">↳ Wakil Sekretaris 5</span>
                                                    <strong>{{ $pac_ippnu->wakilSekretaris5->name ?? '' }}</strong>
                                                </li>
                                            @endif
                                            <li class="list-group-item d-flex justify-content-between">
                                                <span>Bendahara</span>
                                                <strong>{{ $pac_ippnu->bendahara->name ?? 'Belum Diatur' }}</strong>
                                            </li>
                                            @if ($pac_ippnu->wakil_bendahara_1_id)
                                                <li class="list-group-item d-flex justify-content-between text-muted"><span
                                                        class="pl-3">↳ Wakil Bendahara 1</span>
                                                    <strong>{{ $pac_ippnu->wakilBendahara1->name ?? '' }}</strong>
                                                </li>
                                            @endif
                                            @if ($pac_ippnu->wakil_bendahara_2_id)
                                                <li class="list-group-item d-flex justify-content-between text-muted"><span
                                                        class="pl-3">↳ Wakil Bendahara 2</span>
                                                    <strong>{{ $pac_ippnu->wakilBendahara2->name ?? '' }}</strong>
                                                </li>
                                            @endif
                                            @if ($pac_ippnu->wakil_bendahara_3_id)
                                                <li class="list-group-item d-flex justify-content-between text-muted"><span
                                                        class="pl-3">↳ Wakil Bendahara 3</span>
                                                    <strong>{{ $pac_ippnu->wakilBendahara3->name ?? '' }}</strong>
                                                </li>
                                            @endif
                                        @else
                                            <li class="list-group-item text-center text-muted">Data Kepengurusan Belum
                                                Tersedia</li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <style>
        /* Styling khusus tab menu agar tidak kaku */
        .nav-tabs .nav-link {
            border: none;
            color: #6c757d;
            border-bottom: 3px solid transparent;
        }

        .nav-tabs .nav-link.active {
            color: #006b3f !important;
            border-bottom: 3px solid #006b3f;
            background: transparent;
        }

        .nav-tabs .nav-link:hover {
            border-bottom: 3px solid #c3e6cb;
        }
    </style>
@endsection
