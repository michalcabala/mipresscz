<?php $__env->startSection('title', ($entry->meta_title ?? null) ?: ($entry->title ?? config('app.name'))); ?>
<?php $__env->startSection('description', $entry->meta_description ?? ''); ?>

<?php $__env->startSection('content'); ?>


<div class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20">

        
        <nav class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-8">
            <a href="<?php echo e(url('/')); ?>" class="hover:text-gray-900 dark:hover:text-white transition-colors"><?php echo e(__('Domů')); ?></a>
            <span>/</span>
            <span class="text-gray-900 dark:text-white"><?php echo e($entry->title); ?></span>
        </nav>

        <h1 class="text-4xl sm:text-5xl font-bold text-gray-900 dark:text-white leading-tight tracking-tight mb-6">
            <?php echo e($entry->title); ?>

        </h1>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($entry->meta_description ?? null): ?>
        <p class="text-xl text-gray-600 dark:text-gray-400 leading-relaxed mb-8"><?php echo e($entry->meta_description); ?></p>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($entry->published_at ?? null): ?>
            <time datetime="<?php echo e(\Carbon\Carbon::parse($entry->published_at)->toIso8601String()); ?>" class="font-mono">
                <?php echo e(\Carbon\Carbon::parse($entry->published_at)->translatedFormat('j. F Y')); ?>

            </time>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($entry->author ?? null): ?>
            <span class="flex items-center gap-2">
                <span class="w-1 h-1 rounded-full bg-gray-400"></span>
                <?php echo e($entry->author->name); ?>

            </span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
</div>


<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($entry->featured_image_id ?? null): ?>
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 mt-10">
    <div class="rounded-2xl overflow-hidden shadow-xl">
        <?php if (isset($component)) { $__componentOriginal2d62a2f0e3650962aee0f8158be82357 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2d62a2f0e3650962aee0f8158be82357 = $attributes; } ?>
<?php $component = Awcodes\Curator\View\Components\Glider::resolve(['media' => $entry->featured_image_id,'width' => '1024','height' => '576'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('curator-glider'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Awcodes\Curator\View\Components\Glider::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-full h-auto']); ?>
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
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="prose prose-lg dark:prose-invert max-w-none">
        <?php echo mason(content: $entry->content, bricks: $bricks ?? [])->toHtml(); ?>

    </div>
</div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>


<?php
    $relatedArticles = \MiPressCz\Core\Models\Entry::query()
        ->with(['featuredImage'])
        ->whereHas('collection', fn ($q) => $q->where('handle', 'articles'))
        ->published()
        ->where('locale', app()->getLocale())
        ->where('id', '!=', $entry->id)
        ->orderByDesc('published_at')
        ->limit(2)
        ->get();
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($relatedArticles->isNotEmpty()): ?>
<section class="border-t border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-900">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-8"><?php echo e(__('Další příspěvky')); ?></h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $relatedArticles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $related): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
            <article class="group flex flex-col bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden hover:border-blue-300 dark:hover:border-blue-600 transition-colors">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($related->featured_image_id ?? null): ?>
                <div class="aspect-video overflow-hidden bg-gray-100 dark:bg-gray-700">
                    <?php if (isset($component)) { $__componentOriginal2d62a2f0e3650962aee0f8158be82357 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2d62a2f0e3650962aee0f8158be82357 = $attributes; } ?>
<?php $component = Awcodes\Curator\View\Components\Glider::resolve(['media' => $related->featured_image_id,'width' => '600','height' => '338'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('curator-glider'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Awcodes\Curator\View\Components\Glider::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-full h-full object-cover group-hover:scale-105 transition-transform duration-300']); ?>
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
                <div class="p-6">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($related->published_at ?? null): ?>
                    <time class="text-xs font-mono text-gray-500 dark:text-gray-400">
                        <?php echo e(\Carbon\Carbon::parse($related->published_at)->translatedFormat('j. F Y')); ?>

                    </time>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <h3 class="mt-2 font-semibold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors leading-snug">
                        <a href="<?php echo e(url($related->uri)); ?>"><?php echo e($related->title); ?></a>
                    </h3>
                </div>
            </article>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('template::layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\mipresscz\resources\views/templates/default/articles/show.blade.php ENDPATH**/ ?>