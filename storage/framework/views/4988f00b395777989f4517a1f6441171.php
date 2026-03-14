<?php
    $bg = $background ?? 'gradient';
    $isFullscreen = filter_var($fullscreen ?? true, FILTER_VALIDATE_BOOLEAN);
    $align = $alignment ?? 'left';
    $heightClass = $isFullscreen ? 'min-h-screen' : 'py-24 sm:py-32 lg:py-40';
    $textAlign = match($align) {
        'center' => 'text-center mx-auto',
        'right' => 'ml-auto text-right',
        default => '',
    };
?>
<section class="mason-brick mason-hero relative overflow-hidden <?php echo e($heightClass); ?> <?php echo e($isFullscreen ? 'flex items-center' : ''); ?>">
    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bg === 'image' && ($media_id ?? null)): ?>
        <div class="absolute inset-0 z-0">
            <?php if (isset($component)) { $__componentOriginal2d62a2f0e3650962aee0f8158be82357 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2d62a2f0e3650962aee0f8158be82357 = $attributes; } ?>
<?php $component = Awcodes\Curator\View\Components\Glider::resolve(['media' => $media_id] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('curator-glider'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Awcodes\Curator\View\Components\Glider::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-full h-full object-cover']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2d62a2f0e3650962aee0f8158be82357)): ?>
<?php $attributes = $__attributesOriginal2d62a2f0e3650962aee0f8158be82357; ?>
<?php unset($__attributesOriginal2d62a2f0e3650962aee0f8158be82357); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2d62a2f0e3650962aee0f8158be82357)): ?>
<?php $component = $__componentOriginal2d62a2f0e3650962aee0f8158be82357; ?>
<?php unset($__componentOriginal2d62a2f0e3650962aee0f8158be82357); ?>
<?php endif; ?>
            <div class="absolute inset-0 bg-gray-950/70"></div>
        </div>
    <?php elseif($bg === 'dark'): ?>
        <div class="absolute inset-0 bg-gray-950"></div>
    <?php else: ?>
        <div class="absolute inset-0 bg-gradient-to-br from-gray-950 via-blue-950 to-gray-950"></div>
        <div class="absolute inset-0 opacity-30" style="background-image: radial-gradient(circle at 20% 50%, #1d4ed8 0%, transparent 60%), radial-gradient(circle at 80% 20%, #1e40af 0%, transparent 50%)"></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    
    <div class="absolute inset-0 opacity-5" style="background-image: linear-gradient(rgba(255,255,255,.1) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.1) 1px, transparent 1px); background-size: 60px 60px"></div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 <?php echo e($isFullscreen ? '' : ''); ?> w-full">
        <div class="max-w-3xl <?php echo e($textAlign); ?>">
            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($eyebrow ?? null): ?>
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-blue-500/30 bg-blue-500/10 text-blue-300 text-sm font-mono mb-8">
                <span class="w-2 h-2 rounded-full bg-blue-400 animate-pulse"></span>
                <?php echo e($eyebrow); ?>

            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($heading ?? null): ?>
            <h1 class="text-5xl sm:text-6xl lg:text-7xl font-bold text-white leading-tight tracking-tight mb-6">
                <?php echo e($heading); ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($heading_highlight ?? null): ?>
                <br><span class="bg-gradient-to-r from-blue-400 to-blue-300 bg-clip-text text-transparent"><?php echo e($heading_highlight); ?></span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </h1>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($subheading ?? null): ?>
            <p class="text-lg sm:text-xl text-gray-300 leading-relaxed max-w-2xl mb-10"><?php echo e($subheading); ?></p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($button_label ?? null) || ($secondary_label ?? null)): ?>
            <div class="flex flex-wrap gap-4 <?php echo e($align === 'center' ? 'justify-center' : ($align === 'right' ? 'justify-end' : '')); ?>">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($button_label ?? null): ?>
                <a href="<?php echo e($button_url ?? '#'); ?>"
                   class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-500 text-white font-semibold px-6 py-3 rounded-xl transition-colors shadow-lg shadow-blue-900/40">
                    <?php echo e($button_label); ?>

                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                </a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($secondary_label ?? null): ?>
                <a href="<?php echo e($secondary_url ?? '#'); ?>" <?php if(str_starts_with($secondary_url ?? '', 'http')): ?> target="_blank" rel="noopener" <?php endif; ?>
                   class="inline-flex items-center gap-2 border border-gray-600 hover:border-gray-400 text-gray-300 hover:text-white font-semibold px-6 py-3 rounded-xl transition-colors">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($secondary_icon ?? null) === 'github'): ?>
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 .5C5.65.5.5 5.65.5 12c0 5.1 3.3 9.42 7.88 10.95.58.1.79-.25.79-.56v-2c-3.2.69-3.87-1.54-3.87-1.54-.53-1.34-1.3-1.7-1.3-1.7-1.06-.72.08-.71.08-.71 1.17.08 1.79 1.2 1.79 1.2 1.04 1.78 2.72 1.27 3.38.97.1-.75.41-1.27.74-1.56-2.55-.29-5.24-1.28-5.24-5.7 0-1.26.45-2.29 1.18-3.1-.12-.29-.51-1.47.11-3.06 0 0 .97-.31 3.17 1.18a11.05 11.05 0 0 1 5.78 0c2.2-1.49 3.17-1.18 3.17-1.18.62 1.59.23 2.77.11 3.06.74.81 1.18 1.84 1.18 3.1 0 4.43-2.7 5.41-5.27 5.69.42.36.79 1.07.79 2.16v3.2c0 .31.21.67.8.56A11.512 11.512 0 0 0 23.5 12C23.5 5.65 18.35.5 12 .5z"/></svg>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php echo e($secondary_label); ?>

                </a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($badges ?? [])): ?>
        <div class="absolute right-8 top-1/2 -translate-y-1/2 hidden xl:flex flex-col gap-3 opacity-60">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $badges; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $badge): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
            <div class="flex items-center gap-3 bg-gray-900/60 border border-gray-700/50 rounded-xl px-4 py-3 backdrop-blur-sm">
                <span class="font-mono text-xs text-blue-400">// <?php echo e($badge['label'] ?? ''); ?></span>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    
    <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 60" fill="none" xmlns="http://www.w3.org/2000/svg" class="block w-full text-white dark:text-gray-950" preserveAspectRatio="none">
            <path d="M0 60L1440 60L1440 30C1200 5 960 0 720 15C480 30 240 50 0 30L0 60Z" fill="currentColor"/>
        </svg>
    </div>
</section>
<?php /**PATH C:\laragon\www\mipresscz\resources\views/mason/hero.blade.php ENDPATH**/ ?>