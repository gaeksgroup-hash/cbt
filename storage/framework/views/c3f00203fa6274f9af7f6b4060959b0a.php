<?php $__env->startSection('title', 'Beranda Platform'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
    <div class="text-center max-w-3xl mx-auto mb-16">
        <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-[#012E34]/5 text-[#012E34] border border-[#012E34]/10 mb-4">
            <span class="w-2 h-2 rounded-full bg-[#0891B2] mr-2"></span>
            Platform Foundation Ready
        </div>
        <h1 class="text-3xl sm:text-5xl font-extrabold text-[#012E34] tracking-tight mb-4">
            GAEKS CBT
        </h1>
        <p class="text-lg sm:text-xl text-slate-600 font-medium mb-3">
            Computer-Based Testing Platform
        </p>
        <p class="text-base text-slate-500 max-w-2xl mx-auto leading-relaxed">
            Platform simulasi ujian dan bank soal GAEKS. Dirancang dengan arsitektur deterministik, server-authoritative timer, riwayat belajar berkelanjutan, dan standar keandalan institusional.
        </p>
    </div>

    <div class="max-w-4xl mx-auto">
        <div class="flex items-center justify-between mb-6 pb-2 border-b border-slate-200">
            <h2 class="text-xl font-bold text-[#012E34]">Program Ujian</h2>
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Multi-Program Engine</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 flex flex-col justify-between hover:border-[#0891B2]/50 transition-all">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="px-2.5 py-0.5 rounded text-xs font-bold uppercase tracking-wider bg-[#012E34] text-white">Program Utama</span>
                        <span class="text-xs font-medium text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded">Initialized</span>
                    </div>
                    <h3 class="text-xl font-bold text-[#012E34] mb-2">
                        Sertifikasi Ahli Kepabeanan
                    </h3>
                    <p class="text-sm text-slate-600 mb-4 leading-relaxed">
                        Modul evaluasi mandiri dan simulasi ujian sertifikasi kepabeanan komprehensif mencakup Bab 01 s.d. Bab 11 serta Tryout Akbar SAK.
                    </p>
                    <ul class="text-xs text-slate-500 space-y-1.5 mb-6">
                        <li class="flex items-center"><span class="w-1.5 h-1.5 rounded-full bg-[#0891B2] mr-2"></span>110 Soal Chapter CBT (Bab 01–11)</li>
                        <li class="flex items-center"><span class="w-1.5 h-1.5 rounded-full bg-[#0891B2] mr-2"></span>Tryout Akbar 2 Sesi (Uraian & PG)</li>
                        <li class="flex items-center"><span class="w-1.5 h-1.5 rounded-full bg-[#0891B2] mr-2"></span>Routing Akses via User ID & Token</li>
                    </ul>
                </div>
                <div>
                    <a href="<?php echo e(route('sak.landing')); ?>" class="w-full inline-flex items-center justify-center px-4 py-2.5 rounded-lg text-sm font-semibold text-white bg-[#012E34] hover:bg-[#0F4C5C] transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-[#0891B2]">
                        Buka Program SAK
                        <svg class="w-4 h-4 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>

            <div class="bg-slate-50 rounded-xl border border-dashed border-slate-300 p-6 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="px-2.5 py-0.5 rounded text-xs font-semibold uppercase tracking-wider bg-slate-200 text-slate-600">Extensible Engine</span>
                        <span class="text-xs font-medium text-slate-500">Upcoming</span>
                    </div>
                    <h3 class="text-xl font-bold text-slate-700 mb-2">
                        Future CBT Programs
                    </h3>
                    <p class="text-sm text-slate-500 mb-4 leading-relaxed">
                        Arsitektur platform dirancang reusable untuk memuat berbagai kurikulum, bank soal, dan program sertifikasi logistik maupun manajemen lainnya.
                    </p>
                    <ul class="text-xs text-slate-400 space-y-1.5 mb-6">
                        <li class="flex items-center"><span class="w-1.5 h-1.5 rounded-full bg-slate-300 mr-2"></span>Multi-tenant Program Registry</li>
                        <li class="flex items-center"><span class="w-1.5 h-1.5 rounded-full bg-slate-300 mr-2"></span>Shared Question Bank & Attempt Engine</li>
                        <li class="flex items-center"><span class="w-1.5 h-1.5 rounded-full bg-slate-300 mr-2"></span>Independent Token Routers</li>
                    </ul>
                </div>
                <div>
                    <button type="button" disabled class="w-full inline-flex items-center justify-center px-4 py-2.5 rounded-lg text-sm font-medium text-slate-400 bg-slate-200 cursor-not-allowed">
                        Segera Hadir
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /workspaces/cbt/resources/views/landing.blade.php ENDPATH**/ ?>