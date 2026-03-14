<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="index, follow">

    <title><?php echo $__env->yieldContent('title', config('app.name', 'miPress')); ?></title>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(strlen(trim((string) View::yieldContent('description')))): ?>
    <meta name="description" content="<?php echo $__env->yieldContent('description'); ?>">
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($canonicalUrl)): ?>
    <link rel="canonical" href="<?php echo e($canonicalUrl); ?>">
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($hreflangLinks) && $hreflangLinks->count() > 1): ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $hreflangLinks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $locale => $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
    <link rel="alternate" hreflang="<?php echo e($locale); ?>" href="<?php echo e($link['url']); ?>">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    <link rel="alternate" hreflang="x-default" href="<?php echo e($hreflangLinks->first()['url']); ?>">
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php echo $__env->yieldContent('meta'); ?>

    
    <script>
        (function () {
            var t = localStorage.getItem('mipress-theme');
            if (t === 'dark' || (!t && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

    <script>tailwind = { config: { darkMode: 'class' } };</script>
    <script src="https://cdn.tailwindcss.com"></script>

    <?php echo $__env->yieldContent('head'); ?>
</head>
<body class="flex flex-col min-h-full bg-white dark:bg-gray-950 text-gray-900 dark:text-gray-100 antialiased">

    <?php echo $__env->make('template::partials.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <main class="flex-1">
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <?php echo $__env->make('template::partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <script>
        function miPressToggleTheme() {
            var html = document.documentElement;
            if (html.classList.contains('dark')) {
                html.classList.remove('dark');
                localStorage.setItem('mipress-theme', 'light');
            } else {
                html.classList.add('dark');
                localStorage.setItem('mipress-theme', 'dark');
            }
        }

        function miPressOpenMenu() {
            var overlay = document.getElementById('mobile-nav-overlay');
            if (overlay) {
                overlay.classList.remove('hidden');
                requestAnimationFrame(function () {
                    overlay.classList.add('mp-menu-open');
                });
                document.body.style.overflow = 'hidden';
            }
        }

        function miPressCloseMenu() {
            var overlay = document.getElementById('mobile-nav-overlay');
            if (overlay) {
                overlay.classList.remove('mp-menu-open');
                setTimeout(function () { overlay.classList.add('hidden'); }, 300);
                document.body.style.overflow = '';
            }
        }

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') { miPressCloseMenu(); }
        });
    </script>

    <style>
        #mobile-nav-overlay {
            opacity: 0;
            transform: translateY(-12px);
            transition: opacity 0.25s ease, transform 0.25s ease;
        }
        #mobile-nav-overlay.mp-menu-open {
            opacity: 1;
            transform: translateY(0);
        }
    </style>

    <?php echo $__env->yieldContent('scripts'); ?>

</body>
</html>
<?php /**PATH C:\laragon\www\mipresscz\resources\views/templates/default/layouts/app.blade.php ENDPATH**/ ?>