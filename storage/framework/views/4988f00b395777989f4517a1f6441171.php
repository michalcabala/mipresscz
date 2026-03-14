<div class="mason-brick mason-hero relative overflow-hidden rounded-xl bg-gray-900 text-white"
     <?php if($media_id ?? null): ?>
         style="background-image: url(''); background-size: cover; background-position: center;"
     <?php endif; ?>>
    <div class="relative z-10 px-8 py-16 text-<?php echo e($alignment ?? 'left'); ?>">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($heading ?? null): ?>
            <h1 class="text-4xl font-bold mb-4"><?php echo e($heading); ?></h1>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($subheading ?? null): ?>
            <p class="text-xl mb-8 opacity-90"><?php echo e($subheading); ?></p>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($button_label ?? null) && ($button_url ?? null)): ?>
            <a href="<?php echo e($button_url); ?>" class="inline-block bg-white text-gray-900 font-semibold px-6 py-3 rounded-lg hover:bg-gray-100 transition">
                <?php echo e($button_label); ?>

            </a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($media_id ?? null): ?>
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
<?php $component->withAttributes(['class' => 'w-full h-full object-cover opacity-40']); ?>
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
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>

    //
</div>
<?php /**PATH C:\laragon\www\mipresscz\resources\views/mason/hero.blade.php ENDPATH**/ ?>