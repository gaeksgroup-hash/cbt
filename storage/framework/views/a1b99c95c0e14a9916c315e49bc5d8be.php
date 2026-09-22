<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo $__env->yieldContent('title', 'GAEKS CBT'); ?> — Computer-Based Testing Platform</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <?php if(file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot'))): ?>
        <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <?php else: ?>
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            brand: {
                                primary: '#012E34',
                                secondary: '#0F4C5C',
                                accent: '#0891B2',
                                surface: '#FFFFFF',
                                bg: '#F8FAFC',
                                dark: '#0B1F24',
                            }
                        }
                    }
                }
            }
        </script>
        <style>
            body { font-family: 'Inter', sans-serif; }
        </style>
    <?php endif; ?>
</head>
<body class="min-h-full flex flex-col bg-[#F8FAFC] text-slate-800 antialiased">
    <header class="border-b border-slate-200 bg-white sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-3">
                    <a href="<?php echo e(route('landing')); ?>" class="flex items-center space-x-3 focus:outline-none focus:ring-2 focus:ring-[#0891B2] rounded-md">
                        <div class="w-10 h-10 rounded-lg bg-[#012E34] flex items-center justify-center text-white font-bold text-lg tracking-wider shadow-sm">
                            G
                        </div>
                        <div>
                            <span class="text-lg font-bold text-[#012E34] tracking-tight block leading-tight">GAEKS CBT</span>
                            <span class="text-xs text-slate-500 font-medium block leading-none">Assessment Platform</span>
                        </div>
                    </a>
                </div>

                <nav class="flex items-center space-x-4">
                    <a href="<?php echo e(route('landing')); ?>" class="text-sm font-medium text-slate-600 hover:text-[#012E34] transition-colors">Platform</a>
                    <a href="<?php echo e(route('sak.landing')); ?>" class="text-sm font-medium text-slate-600 hover:text-[#012E34] transition-colors">Program SAK</a>
                    <a href="<?php echo e(route('health')); ?>" target="_blank" class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 transition-colors">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                        System OK
                    </a>
                </nav>
            </div>
        </div>
    </header>

    <main class="flex-1">
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <footer class="border-t border-slate-200 bg-white py-8 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row justify-between items-center space-y-4 sm:space-y-0 text-xs text-slate-500">
            <p>&copy; <?php echo e(date('Y')); ?> GAEKS GROUP. Seluruh hak cipta dilindungi undang-undang.</p>
            <div class="flex items-center space-x-6">
                <span class="text-slate-400">cbt.gaeks.com</span>
                <span class="inline-block w-1 h-1 rounded-full bg-slate-300"></span>
                <span>Production Standard Architecture</span>
            </div>
        </div>
    </footer>
</body>
</html>
<?php /**PATH /workspaces/cbt/resources/views/layouts/app.blade.php ENDPATH**/ ?>