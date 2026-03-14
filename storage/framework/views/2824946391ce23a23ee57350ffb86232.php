<?php $__env->startSection('title', ($entry->meta_title ?? null) ?: ($entry->title ?? config('app.name'))); ?>
<?php $__env->startSection('description', $entry->meta_description ?? ''); ?>

<?php $__env->startSection('content'); ?>


<div class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20">
        <div class="max-w-3xl">
            <h1 class="text-4xl sm:text-5xl font-bold text-gray-900 dark:text-white leading-tight tracking-tight mb-4">
                <?php echo e($entry->title); ?>

            </h1>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($entry->meta_description ?? null): ?>
            <p class="text-lg text-gray-600 dark:text-gray-400"><?php echo e($entry->meta_description); ?></p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
</div>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($entry->featured_image_id ?? null): ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-8 mb-0">
    <div class="rounded-2xl overflow-hidden shadow-xl max-h-[480px]">
        <?php if (isset($component)) { $__componentOriginal2d62a2f0e3650962aee0f8158be82357 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2d62a2f0e3650962aee0f8158be82357 = $attributes; } ?>
<?php $component = Awcodes\Curator\View\Components\Glider::resolve(['media' => $entry->featured_image_id,'width' => '1280','height' => '480'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
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
    </div>
</div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>


<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($entry->content)): ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <?php echo mason(content: $entry->content, bricks: $bricks ?? [])->toHtml(); ?>

</div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('template::layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\mipresscz\resources\views/templates/default/pages/page.blade.php ENDPATH**/ ?>