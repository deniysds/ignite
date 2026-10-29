@extends('layouts.main')

@section('breadcrumbs')
    <div class="flex items-center gap-2 text-xs text-gray-500 mb-4">
        <a href="{{ route('dashboard') }}" class="hover:text-red-600 font-semibold text-gray-700 flex items-center gap-1">
            <i class="ki-filled ki-element-11 text-xs text-red-600"></i> Dashboard
        </a>
        <span>/</span>
        <span class="text-gray-500">Dasbor Eksekutif &amp; Statistik Editorial</span>
    </div>
@endsection

@section('content')
    <div class="grid w-full space-y-6">
        <!-- Banner Selamat Datang & Profil -->
        <div class="kt-card p-6 md:p-8 bg-gradient-to-r from-slate-900 via-slate-800 to-red-950 text-white shadow-md relative overflow-hidden rounded-2xl">
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="space-y-2">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-xs uppercase font-bold tracking-widest text-red-400 bg-red-950/60 border border-red-800/60 px-3 py-1 rounded-full">
                            IGNITE Editorial System
                        </span>
                        @if($isEditorOrAdmin)
                            <span class="text-xs font-semibold bg-amber-500/20 text-amber-300 border border-amber-500/30 px-2.5 py-0.5 rounded-full">
                                Dewan Redaksi / Admin
                            </span>
                        @endif
                        @if($isReviewer)
                            <span class="text-xs font-semibold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 px-2.5 py-0.5 rounded-full">
                                Mitra Bestari (Reviewer)
                            </span>
                        @endif
                        <span class="text-xs font-semibold bg-blue-500/20 text-blue-300 border border-blue-500/30 px-2.5 py-0.5 rounded-full">
                            Penulis (Author)
                        </span>
                    </div>

                    <h1 class="text-2xl md:text-3xl font-extrabold text-white tracking-tight">
                        Selamat Datang, {{ $user->name }}
                    </h1>
                    <p class="text-xs md:text-sm text-slate-300 max-w-2xl leading-relaxed">
                        Platform manajemen penerbitan berkala ilmiah &amp; jurnal genomik medis Yayasan Satriabudi Dharma Setia. Pantau alur naskah, penelaahan sejawat (*peer review*), dan publikasi terbitan secara terpadu.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3 shrink-0">
                    <a href="{{ route('submissions.create.step1') }}" class="kt-btn kt-btn-primary text-white text-xs font-bold px-4 py-2.5 shadow-sm flex items-center gap-2">
                        <i class="ki-filled ki-plus text-white"></i> Ajukan Naskah Baru
                    </a>
                    <a href="{{ route('website.home') }}" target="_blank" class="kt-btn kt-btn-outline bg-white/10 hover:bg-white/20 text-white border-white/20 text-xs px-4 py-2.5 flex items-center gap-2">
                        <i class="ki-filled ki-eye text-white"></i> Portal Publik Jurnal &rarr;
                    </a>
                </div>
            </div>
        </div>

        <!-- Section Statistik Ekosistem Publikasi Ilmiah -->
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                        <i class="ki-filled ki-chart-simple text-red-600"></i> Statistik Ekosistem Publikasi IGNITE
                    </h2>
                    <p class="text-[11px] text-gray-500">Ringkasan statistik agregat portal jurnal, nomor terbitan, naskah ilmiah, dan pembaca.</p>
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                <div class="kt-card p-4 border border-gray-200 bg-white hover:border-red-300 transition shadow-xs flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center font-bold text-base shrink-0">
                        <i class="ki-filled ki-award"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-xl font-black text-gray-900">{{ $overviewMetrics['total_journals'] ?? 0 }}</div>
                        <div class="text-[10px] font-bold text-gray-500 uppercase tracking-wider truncate">Jurnal Aktif</div>
                    </div>
                </div>

                <div class="kt-card p-4 border border-gray-200 bg-white hover:border-blue-300 transition shadow-xs flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-base shrink-0">
                        <i class="ki-filled ki-book-open"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-xl font-black text-gray-900">{{ $overviewMetrics['total_issues'] ?? 0 }}</div>
                        <div class="text-[10px] font-bold text-gray-500 uppercase tracking-wider truncate">Nomor Terbitan</div>
                    </div>
                </div>

                <div class="kt-card p-4 border border-gray-200 bg-white hover:border-emerald-300 transition shadow-xs flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-base shrink-0">
                        <i class="ki-filled ki-crown"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-xl font-black text-emerald-800">{{ $overviewMetrics['published_articles'] ?? 0 }}</div>
                        <div class="text-[10px] font-bold text-gray-500 uppercase tracking-wider truncate">Artikel Terbit</div>
                    </div>
                </div>

                <div class="kt-card p-4 border border-gray-200 bg-white hover:border-indigo-300 transition shadow-xs flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-base shrink-0">
                        <i class="ki-filled ki-document"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-xl font-black text-indigo-900">{{ $overviewMetrics['total_submissions'] ?? 0 }}</div>
                        <div class="text-[10px] font-bold text-gray-500 uppercase tracking-wider truncate">Naskah Masuk</div>
                    </div>
                </div>

                <div class="kt-card p-4 border border-gray-200 bg-white hover:border-purple-300 transition shadow-xs flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold text-base shrink-0">
                        <i class="ki-filled ki-eye"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-xl font-black text-purple-900">{{ number_format($overviewMetrics['total_article_views'] ?? 0) }}</div>
                        <div class="text-[10px] font-bold text-gray-500 uppercase tracking-wider truncate">Tayangan Pembaca</div>
                    </div>
                </div>

                <div class="kt-card p-4 border border-gray-200 bg-white hover:border-teal-300 transition shadow-xs flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center font-bold text-base shrink-0">
                        <i class="ki-filled ki-file-down"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-xl font-black text-teal-900">{{ number_format($overviewMetrics['total_article_downloads'] ?? 0) }}</div>
                        <div class="text-[10px] font-bold text-gray-500 uppercase tracking-wider truncate">Unduhan PDF</div>
                    </div>
                </div>
            </div>
        </div>

        @if($isEditorOrAdmin)
            <!-- Section Dewan Redaksi (Editorial Executive Overview) -->
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
                            <i class="ki-filled ki-chart-line-star text-red-600"></i> Metrik Operasional Dewan Redaksi
                        </h2>
                        <p class="text-xs text-gray-500">Ringkasan status naskah ilmiah aktif dalam alur kerja editorial jurnal.</p>
                    </div>
                    <a href="{{ route('editor.submissions.index') }}" class="text-xs font-bold text-red-600 hover:underline flex items-center gap-1">
                        Buka Semua Submissions &rarr;
                    </a>
                </div>

                <!-- Grid Kartu KPI Redaksi -->
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                    <div class="kt-card p-4 border-s-4 border-blue-500 shadow-xs space-y-1">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-gray-500 block">Menunggu Redaksi</span>
                        <div class="text-2xl font-black text-blue-700">{{ $editorialMetrics['need_assignment'] ?? 0 }}</div>
                        <span class="text-[10px] text-gray-400">Desk Review</span>
                    </div>

                    <div class="kt-card p-4 border-s-4 border-indigo-500 shadow-xs space-y-1">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-gray-500 block">Dalam Telaah</span>
                        <div class="text-2xl font-black text-indigo-700">{{ $editorialMetrics['in_peer_review'] ?? 0 }}</div>
                        <span class="text-[10px] text-gray-400">Peer Review</span>
                    </div>

                    <div class="kt-card p-4 border-s-4 border-orange-500 shadow-xs space-y-1">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-gray-500 block">Perlu Revisi</span>
                        <div class="text-2xl font-black text-orange-700">{{ $editorialMetrics['revision_pending'] ?? 0 }}</div>
                        <span class="text-[10px] text-gray-400">Di Penulis</span>
                    </div>

                    <div class="kt-card p-4 border-s-4 border-sky-500 shadow-xs space-y-1">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-gray-500 block">Revisi Masuk</span>
                        <div class="text-2xl font-black text-sky-700">{{ $editorialMetrics['revision_received'] ?? 0 }}</div>
                        <span class="text-[10px] text-gray-400">Resubmitted</span>
                    </div>

                    <div class="kt-card p-4 border-s-4 border-emerald-500 shadow-xs space-y-1">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-gray-500 block">Siap Terbit</span>
                        <div class="text-2xl font-black text-emerald-700">{{ $editorialMetrics['ready_to_publish'] ?? 0 }}</div>
                        <span class="text-[10px] text-gray-400">Accepted</span>
                    </div>

                    <div class="kt-card p-4 border-s-4 border-green-600 shadow-xs space-y-1">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-gray-500 block">Artikel Terbit</span>
                        <div class="text-2xl font-black text-green-800">{{ $editorialMetrics['published_articles'] ?? 0 }}</div>
                        <span class="text-[10px] text-gray-400">Di Terbitan</span>
                    </div>
                </div>

                <!-- Grid Tabel Action Needed & Pintasan Manajemen -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    <!-- Tabel Action Needed Redaksi (8 Cols) -->
                    <div class="lg:col-span-8">
                        <div class="kt-card h-full shadow-xs">
                            <div class="kt-card-header min-h-14 py-4 border-b border-gray-200 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-red-500 animate-ping"></span>
                                    <h3 class="kt-card-title text-sm font-bold">Naskah Memerlukan Tindakan Cepat (Action Needed)</h3>
                                </div>
                                <span class="text-[11px] text-gray-400">Urutan Prioritas Terkini</span>
                            </div>

                            <div class="kt-card-body p-0">
                                @if($actionNeededSubmissions->isEmpty())
                                    <div class="p-8 text-center space-y-2">
                                        <i class="ki-filled ki-check-circle text-green-500 text-3xl"></i>
                                        <h4 class="text-xs font-bold text-gray-800">Tidak Ada Antrean Naskah Mendesak</h4>
                                        <p class="text-[11px] text-gray-500">Seluruh naskah yang masuk telah diproses atau sedang berada di tangan mitra bestari.</p>
                                    </div>
                                @else
                                    <div class="table-responsive">
                                        <table class="table w-full text-xs text-left">
                                            <thead class="bg-gray-50 text-gray-500 uppercase text-[10px] font-semibold border-b border-gray-150">
                                                <tr>
                                                    <th class="px-4 py-3">Jurnal &amp; Judul Naskah</th>
                                                    <th class="px-3 py-3">Status</th>
                                                    <th class="px-3 py-3">Pembaruan</th>
                                                    <th class="px-4 py-3 text-right">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-100">
                                                @foreach($actionNeededSubmissions as $actSub)
                                                    <tr class="hover:bg-gray-50/70">
                                                        <td class="px-4 py-3 max-w-xs space-y-0.5">
                                                            <span class="text-[10px] font-bold text-red-600 block">{{ $actSub->journal?->short_name ?? $actSub->journal?->name }}</span>
                                                            <a href="{{ route('editor.submissions.show', $actSub->id) }}" class="font-bold text-gray-900 hover:text-red-600 line-clamp-1 block leading-snug">
                                                                {{ $actSub->title }}
                                                            </a>
                                                            <span class="text-[10px] text-gray-400 block truncate">
                                                                {{ $actSub->authors->pluck('name')->join(', ') }}
                                                            </span>
                                                        </td>
                                                        <td class="px-3 py-3">
                                                            @if($actSub->status === 'submitted')
                                                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">Masuk Baru</span>
                                                            @elseif($actSub->status === 'resubmitted')
                                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-sky-100 text-sky-800 border border-sky-200 animate-pulse">Revisi Masuk</span>
                                                            @elseif($actSub->status === 'under_editor_review')
                                                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-50 text-amber-800 border border-amber-200">Desk Review</span>
                                                            @else
                                                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-gray-100 text-gray-700">{{ $actSub->status }}</span>
                                                            @endif
                                                        </td>
                                                        <td class="px-3 py-3 text-[11px] text-gray-500 whitespace-nowrap">
                                                            {{ $actSub->updated_at ? $actSub->updated_at->diffForHumans() : '-' }}
                                                        </td>
                                                        <td class="px-4 py-3 text-right">
                                                            <a href="{{ route('editor.submissions.show', $actSub->id) }}" class="kt-btn kt-btn-xs kt-btn-primary text-white font-semibold px-3 py-1">
                                                                Proses &rarr;
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Pintasan Dewan Redaksi (4 Cols) -->
                    <div class="lg:col-span-4 space-y-4">
                        <div class="kt-card p-5 shadow-xs space-y-4">
                            <h3 class="text-sm font-bold text-gray-900 border-b border-gray-150 pb-2 flex items-center gap-1.5">
                                <i class="ki-filled ki-element-11 text-red-600"></i> Pintasan Kerja Redaksi
                            </h3>

                            <div class="grid grid-cols-1 gap-2.5 text-xs">
                                <a href="{{ route('editor.submissions.index') }}" class="flex items-center justify-between p-3 rounded-xl bg-gray-50 hover:bg-red-50 hover:text-red-700 border border-gray-200 transition">
                                    <div class="flex items-center gap-2.5">
                                        <i class="ki-filled ki-document text-base text-gray-600"></i>
                                        <span class="font-semibold">Kelola Naskah Masuk</span>
                                    </div>
                                    <span class="text-xs text-gray-400">&rarr;</span>
                                </a>

                                <a href="{{ route('issues.index') }}" class="flex items-center justify-between p-3 rounded-xl bg-gray-50 hover:bg-red-50 hover:text-red-700 border border-gray-200 transition">
                                    <div class="flex items-center gap-2.5">
                                        <i class="ki-filled ki-book-open text-base text-gray-600"></i>
                                        <span class="font-semibold">Manajemen Nomor Terbitan</span>
                                    </div>
                                    <span class="text-xs text-gray-400">&rarr;</span>
                                </a>

                                <a href="{{ route('journals.index') }}" class="flex items-center justify-between p-3 rounded-xl bg-gray-50 hover:bg-red-50 hover:text-red-700 border border-gray-200 transition">
                                    <div class="flex items-center gap-2.5">
                                        <i class="ki-filled ki-award text-base text-gray-600"></i>
                                        <span class="font-semibold">Master Jurnal &amp; Redaksi</span>
                                    </div>
                                    <span class="text-xs text-gray-400">&rarr;</span>
                                </a>

                                <a href="{{ route('website.settings') }}" class="flex items-center justify-between p-3 rounded-xl bg-gray-50 hover:bg-red-50 hover:text-red-700 border border-gray-200 transition">
                                    <div class="flex items-center gap-2.5">
                                        <i class="ki-filled ki-setting-2 text-base text-gray-600"></i>
                                        <span class="font-semibold">Pengaturan Profil &amp; CMS</span>
                                    </div>
                                    <span class="text-xs text-gray-400">&rarr;</span>
                                </a>
                            </div>
                        </div>

                        <!-- Statistik Pembaca Publik -->
                        <div class="kt-card p-5 shadow-xs bg-slate-900 text-white space-y-3">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-300">Dampak Pembaca Ilmiah</h4>
                            <div class="grid grid-cols-2 gap-3 pt-1">
                                <div class="space-y-0.5">
                                    <div class="text-xl font-black text-red-400">{{ number_format($editorialMetrics['total_article_views'] ?? 0) }}</div>
                                    <div class="text-[10px] text-slate-400 uppercase tracking-wider font-semibold">Tayangan Artikel</div>
                                </div>
                                <div class="space-y-0.5">
                                    <div class="text-xl font-black text-emerald-400">{{ number_format($editorialMetrics['total_article_downloads'] ?? 0) }}</div>
                                    <div class="text-[10px] text-slate-400 uppercase tracking-wider font-semibold">Unduhan PDF</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if($isReviewer)
            <!-- Section Mitra Bestari (Reviewer Workspace Overview) -->
            <div class="space-y-4 pt-2">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
                            <i class="ki-filled ki-user-edit text-indigo-600"></i> Ruang Kerja Mitra Bestari (Reviewer)
                        </h2>
                        <p class="text-xs text-gray-500">Tugas penelaahan sejawat independen yang dialokasikan kepada Anda.</p>
                    </div>
                    <a href="{{ route('reviewer.assignments.index') }}" class="text-xs font-bold text-indigo-600 hover:underline flex items-center gap-1">
                        Buka Semua Penugasan &rarr;
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="kt-card p-4 border border-amber-200 bg-amber-50/50 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center font-bold text-lg">
                            <i class="ki-filled ki-time"></i>
                        </div>
                        <div>
                            <div class="text-xl font-black text-amber-900">{{ $reviewerMetrics['pending_invitations'] ?? 0 }}</div>
                            <div class="text-[11px] font-semibold text-amber-800 uppercase tracking-wider">Undangan Menunggu Respon</div>
                        </div>
                    </div>

                    <div class="kt-card p-4 border border-indigo-200 bg-indigo-50/50 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-bold text-lg">
                            <i class="ki-filled ki-notepad-edit"></i>
                        </div>
                        <div>
                            <div class="text-xl font-black text-indigo-900">{{ $reviewerMetrics['active_reviews'] ?? 0 }}</div>
                            <div class="text-[11px] font-semibold text-indigo-800 uppercase tracking-wider">Telaah Aktif Berjalan</div>
                        </div>
                    </div>

                    <div class="kt-card p-4 border border-emerald-200 bg-emerald-50/50 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-bold text-lg">
                            <i class="ki-filled ki-check-circle"></i>
                        </div>
                        <div>
                            <div class="text-xl font-black text-emerald-900">{{ $reviewerMetrics['completed_reviews'] ?? 0 }}</div>
                            <div class="text-[11px] font-semibold text-emerald-800 uppercase tracking-wider">Telaah Selesai Dikirim</div>
                        </div>
                    </div>
                </div>

                @if($myReviewAssignments->isNotEmpty())
                    <div class="kt-card shadow-xs overflow-hidden">
                        <div class="kt-card-header min-h-12 py-3 bg-gray-50 border-b border-gray-200">
                            <h3 class="kt-card-title text-xs font-bold text-gray-800">Daftar Tugas Telaah Sejawat Aktif</h3>
                        </div>
                        <div class="p-0 table-responsive">
                            <table class="table w-full text-xs text-left">
                                <thead class="bg-gray-100/70 text-gray-500 uppercase text-[10px]">
                                    <tr>
                                        <th class="px-4 py-2.5">Jurnal &amp; Judul Naskah (Blind)</th>
                                        <th class="px-3 py-2.5">Batas Waktu</th>
                                        <th class="px-3 py-2.5">Status Undangan</th>
                                        <th class="px-4 py-2.5 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($myReviewAssignments as $assign)
                                        @php
                                            $sub = $assign->round?->submission;
                                        @endphp
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 max-w-sm">
                                                <span class="text-[10px] font-bold text-indigo-600 block">{{ $sub?->journal?->short_name }}</span>
                                                <span class="font-bold text-gray-900 block leading-snug">{{ $sub?->title }}</span>
                                            </td>
                                            <td class="px-3 py-3 text-gray-600 whitespace-nowrap">
                                                <i class="ki-filled ki-calendar text-gray-400 text-xs mr-1"></i>
                                                {{ $assign->due_date ? $assign->due_date->format('d M Y') : '-' }}
                                            </td>
                                            <td class="px-3 py-3">
                                                @if($assign->response_status === 'pending')
                                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200 animate-pulse">Menunggu Respon</span>
                                                @elseif($assign->response_status === 'accepted')
                                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">Diterima (Sedang Telaah)</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-right">
                                                <a href="{{ route('reviewer.assignments.show', $assign->id) }}" class="kt-btn kt-btn-xs kt-btn-primary text-white font-semibold">
                                                    Buka Lembar Telaah &rarr;
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <!-- Section Penulis (Author Submissions Overview) -->
        <div class="space-y-4 pt-2">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
                        <i class="ki-filled ki-document text-blue-600"></i> Naskah Saya (Author Workspace)
                    </h2>
                    <p class="text-xs text-gray-500">Status pengajuan naskah ilmiah Anda yang sedang berjalan.</p>
                </div>
                <a href="{{ route('submissions.index') }}" class="text-xs font-bold text-blue-600 hover:underline flex items-center gap-1">
                    Lihat Semua Naskah Saya &rarr;
                </a>
            </div>

            <!-- Grid Metrik Penulis -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="kt-card p-4 border border-gray-200 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gray-100 text-gray-600 flex items-center justify-center font-bold text-lg">
                        <i class="ki-filled ki-pencil"></i>
                    </div>
                    <div>
                        <div class="text-xl font-extrabold text-gray-900">{{ $authorMetrics['drafts'] }}</div>
                        <div class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Draf Belum Diserahkan</div>
                    </div>
                </div>

                <div class="kt-card p-4 border border-blue-200 bg-blue-50/40 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold text-lg">
                        <i class="ki-filled ki-time"></i>
                    </div>
                    <div>
                        <div class="text-xl font-extrabold text-blue-900">{{ $authorMetrics['in_progress'] }}</div>
                        <div class="text-[11px] font-semibold text-blue-800 uppercase tracking-wider">Dalam Peninjauan</div>
                    </div>
                </div>

                <div class="kt-card p-4 border border-orange-200 bg-orange-50/50 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-orange-500 text-white flex items-center justify-center font-bold text-lg">
                        <i class="ki-filled ki-information"></i>
                    </div>
                    <div>
                        <div class="text-xl font-extrabold text-orange-950">{{ $authorMetrics['revision_needed'] }}</div>
                        <div class="text-[11px] font-semibold text-orange-900 uppercase tracking-wider">Perlu Tindakan Revisi</div>
                    </div>
                </div>

                <div class="kt-card p-4 border border-emerald-200 bg-emerald-50/40 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-bold text-lg">
                        <i class="ki-filled ki-crown"></i>
                    </div>
                    <div>
                        <div class="text-xl font-extrabold text-emerald-950">{{ $authorMetrics['accepted_or_pub'] }}</div>
                        <div class="text-[11px] font-semibold text-emerald-900 uppercase tracking-wider">Diterima / Terbit</div>
                    </div>
                </div>
            </div>

            <!-- Tabel Naskah Penulis Terakhir -->
            @if($myRecentSubmissions->isNotEmpty())
                <div class="kt-card shadow-xs overflow-hidden">
                    <div class="kt-card-header min-h-12 py-3 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                        <h3 class="kt-card-title text-xs font-bold text-gray-800">Pengajuan Naskah Terbaru Anda</h3>
                        <a href="{{ route('submissions.create.step1') }}" class="text-xs font-bold text-blue-600 hover:underline">
                            + Ajukan Baru
                        </a>
                    </div>

                    <div class="p-0 table-responsive">
                        <table class="table w-full text-xs text-left">
                            <thead class="bg-gray-100/70 text-gray-500 uppercase text-[10px]">
                                <tr>
                                    <th class="px-4 py-2.5">Jurnal</th>
                                    <th class="px-4 py-2.5">Judul Naskah</th>
                                    <th class="px-3 py-2.5">Status</th>
                                    <th class="px-3 py-2.5">Tanggal Dibuat</th>
                                    <th class="px-4 py-2.5 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($myRecentSubmissions as $mySub)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 font-semibold text-gray-800">{{ $mySub->journal?->short_name }}</td>
                                        <td class="px-4 py-3 max-w-sm truncate text-gray-900 font-medium">{{ $mySub->title }}</td>
                                        <td class="px-3 py-3">
                                            @if($mySub->status === 'draft')
                                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-gray-100 text-gray-700">Draf</span>
                                            @elseif($mySub->status === 'revision_required')
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-orange-100 text-orange-800 border border-orange-200 animate-pulse">Perlu Revisi</span>
                                            @elseif($mySub->status === 'accepted' || $mySub->status === 'published')
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">Diterima/Terbit</span>
                                            @else
                                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-blue-50 text-blue-700">{{ $mySub->status }}</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-3 text-gray-500 whitespace-nowrap">{{ $mySub->created_at->format('d M Y') }}</td>
                                        <td class="px-4 py-3 text-right">
                                            @if($mySub->status === 'revision_required')
                                                <a href="{{ route('submissions.show', $mySub->id) }}" class="kt-btn kt-btn-xs kt-btn-primary text-white font-semibold">
                                                    Unggah Revisi &rarr;
                                                </a>
                                            @elseif($mySub->status === 'draft')
                                                <a href="{{ route('submissions.create.step2', $mySub->id) }}" class="kt-btn kt-btn-xs kt-btn-outline">
                                                    Lanjutkan Draf
                                                </a>
                                            @else
                                                <a href="{{ route('submissions.show', $mySub->id) }}" class="kt-btn kt-btn-xs kt-btn-ghost text-primary">
                                                    Detail
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        <!-- Section Sorotan Artikel Ilmiah Terbitan Terkini -->
        @if($recentPublishedArticles->isNotEmpty())
            <div class="space-y-4 pt-2">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
                            <i class="ki-filled ki-crown text-amber-500"></i> Publikasi Ilmiah Terkini
                        </h2>
                        <p class="text-xs text-gray-500">Naskah yang baru saja terbit resmi dan dapat diakses publik.</p>
                    </div>
                    <a href="{{ route('website.articles.index') }}" target="_blank" class="text-xs font-bold text-red-600 hover:underline flex items-center gap-1">
                        Katalog Artikel Publik &rarr;
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach($recentPublishedArticles as $pubArt)
                        <div class="kt-card p-5 shadow-xs border border-gray-150 flex flex-col justify-between space-y-3 hover:border-red-300 transition">
                            <div class="space-y-1.5">
                                <span class="text-[10px] font-bold text-red-600 uppercase tracking-wider block">
                                    {{ $pubArt->issue?->journal?->short_name }} &bull; Vol. {{ $pubArt->issue?->volume }} No. {{ $pubArt->issue?->number }}
                                </span>
                                <a href="{{ route('website.articles.show', $pubArt->slug) }}" target="_blank" class="font-bold text-xs text-gray-900 hover:text-red-600 line-clamp-2 leading-snug block">
                                    {{ $pubArt->title }}
                                </a>
                                @php
                                    $authors = $pubArt->submission?->authors ?? collect();
                                @endphp
                                @if($authors->isNotEmpty())
                                    <p class="text-[10px] text-gray-500 truncate">
                                        {{ $authors->pluck('name')->join(', ') }}
                                    </p>
                                @endif
                            </div>

                            <div class="pt-2 border-t border-gray-100 flex items-center justify-between text-[11px]">
                                <span class="text-gray-400 font-mono">Hlm. {{ $pubArt->pages ?: '-' }}</span>
                                <div class="flex items-center gap-2 text-gray-500">
                                    <span><i class="ki-filled ki-eye text-xs"></i> {{ $pubArt->views_count }}</span>
                                    <span><i class="ki-filled ki-file-down text-xs"></i> {{ $pubArt->downloads_count }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endsection
