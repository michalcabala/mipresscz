<header class="sticky top-0 z-50 border-b border-gray-200/80 dark:border-gray-800/80 backdrop-blur-xl bg-white/90 dark:bg-gray-950/90">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 gap-4">

            
            <a href="<?php echo e(url('/')); ?>" class="flex items-center gap-2.5 shrink-0 group">
                <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-blue-600 text-white text-sm font-bold font-mono group-hover:bg-blue-500 transition-colors">m/</span>
                <span class="font-semibold text-gray-900 dark:text-white tracking-tight hidden sm:inline"><?php echo e(config('app.name', 'miPress')); ?></span>
            </a>

            
            <nav class="hidden lg:flex items-center gap-1 flex-1 justify-center">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($primaryMenu)): ?>
                    <?php echo $__env->make('mipresscz-core::components.nav-menu', [
                        'items' => $primaryMenu,
                        'class' => 'flex items-center gap-1',
                        'itemClass' => 'px-3 py-2 rounded-lg text-sm font-medium transition-colors',
                    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php else: ?>
                    <?php echo $__env->make('template::partials.nav', ['navEntries' => $navEntries], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </nav>

            
            <div class="flex items-center gap-1 shrink-0">
                
                <div class="hidden sm:block mr-1">
                    <?php if (isset($component)) { $__componentOriginalca52de3bb9c3312a4c9c230381dba9e1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalca52de3bb9c3312a4c9c230381dba9e1 = $attributes; } ?>
<?php $component = App\View\Components\LanguageSwitcher::resolve(['entry' => $entry ?? null] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('language-switcher'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\LanguageSwitcher::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalca52de3bb9c3312a4c9c230381dba9e1)): ?>
<?php $attributes = $__attributesOriginalca52de3bb9c3312a4c9c230381dba9e1; ?>
<?php unset($__attributesOriginalca52de3bb9c3312a4c9c230381dba9e1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalca52de3bb9c3312a4c9c230381dba9e1)): ?>
<?php $component = $__componentOriginalca52de3bb9c3312a4c9c230381dba9e1; ?>
<?php unset($__componentOriginalca52de3bb9c3312a4c9c230381dba9e1); ?>
<?php endif; ?>
                </div>

                
                <button
                    onclick="miPressToggleTheme()"
                    type="button"
                    title="<?php echo e(__('Přepnout tmavý / světlý režim')); ?>"
                    class="p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-800 dark:hover:text-white transition-colors"
                >
                    
                    <svg class="w-5 h-5 dark:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                    </svg>
                    
                    <svg class="w-5 h-5 hidden dark:block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0z" />
                    </svg>
                </button>

                
                <button
                    onclick="miPressOpenMenu()"
                    type="button"
                    aria-label="<?php echo e(__('Otevřít navigaci')); ?>"
                    class="lg:hidden p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</header>


<div
    id="mobile-nav-overlay"
    class="hidden fixed inset-0 z-[100] flex flex-col bg-gray-950 lg:hidden"
    role="dialog"
    aria-modal="true"
    aria-label="<?php echo e(__('Mobilní navigace')); ?>"
>
    <div class="flex items-center justify-between px-4 h-16 border-b border-gray-800">
        <a href="<?php echo e(url('/')); ?>" onclick="miPressCloseMenu()" class="flex items-center gap-2.5">
            <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-blue-600 text-white text-sm font-bold font-mono">m/</span>
            <span class="font-semibold text-white"><?php echo e(config('app.name', 'miPress')); ?></span>
        </a>
        <button
            onclick="miPressCloseMenu()"
            type="button"
            aria-label="<?php echo e(__('Zavřít navigaci')); ?>"
            class="p-2 rounded-lg text-gray-400 hover:text-white hover:bg-gray-800 transition-colors"
        >
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <nav class="flex flex-col items-start justify-center flex-1 gap-1 px-6 py-8">
        <a href="<?php echo e(url('/')); ?>" onclick="miPressCloseMenu()"
           class="block text-2xl font-semibold text-white hover:text-blue-400 transition-colors py-3 px-3 rounded-xl w-full"><?php echo e(__('Domů')); ?></a>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($primaryMenu)): ?>
            <?php echo $__env->make('mipresscz-core::components.nav-menu-list', [
                'items' => $primaryMenu,
                'class' => 'w-full space-y-1',
                'childClass' => 'mt-2 ml-3 space-y-1 border-l border-gray-800 pl-4',
                'itemClass' => 'block w-full rounded-xl px-3 py-3 text-2xl font-semibold text-white transition-colors hover:text-blue-400',
                'itemOnclick' => 'miPressCloseMenu()',
            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php else: ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $navEntries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                <a href="<?php echo e(url($item->uri)); ?>" onclick="miPressCloseMenu()"
                   class="block text-2xl font-semibold text-white hover:text-blue-400 transition-colors py-3 px-3 rounded-xl w-full"><?php echo e($item->title); ?></a>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </nav>

    <div class="px-6 pb-8 border-t border-gray-800 pt-6 space-y-4">
        
        <?php if (isset($component)) { $__componentOriginalca52de3bb9c3312a4c9c230381dba9e1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalca52de3bb9c3312a4c9c230381dba9e1 = $attributes; } ?>
<?php $component = App\View\Components\LanguageSwitcher::resolve(['entry' => $entry ?? null] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('language-switcher'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\LanguageSwitcher::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalca52de3bb9c3312a4c9c230381dba9e1)): ?>
<?php $attributes = $__attributesOriginalca52de3bb9c3312a4c9c230381dba9e1; ?>
<?php unset($__attributesOriginalca52de3bb9c3312a4c9c230381dba9e1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalca52de3bb9c3312a4c9c230381dba9e1)): ?>
<?php $component = $__componentOriginalca52de3bb9c3312a4c9c230381dba9e1; ?>
<?php unset($__componentOriginalca52de3bb9c3312a4c9c230381dba9e1); ?>
<?php endif; ?>

        <div class="flex items-center gap-4">
            <a href="<?php echo e(url('/mpcp')); ?>" class="text-sm text-gray-500 hover:text-white transition-colors"><?php echo e(__('Admin')); ?></a>
            <button onclick="miPressToggleTheme()" class="ml-auto text-sm text-gray-500 hover:text-white transition-colors"><?php echo e(__('Přepnout motiv')); ?></button>
        </div>
    </div>
</div>
<?php /**PATH C:\laragon\www\mipresscz\resources\views/templates/default/partials/header.blade.php ENDPATH**/ ?>