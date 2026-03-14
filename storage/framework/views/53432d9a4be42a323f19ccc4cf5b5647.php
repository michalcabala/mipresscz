<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'bricks' => [],
    'hasGridActions' => false,
    'hasColorModeToggle' => false,
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'bricks' => [],
    'hasGridActions' => false,
    'hasColorModeToggle' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $brickIds = array_map(fn ($brick) => $brick::getLabel(), $bricks);
?>

<div
    class="<?php echo \Illuminate\Support\Arr::toCssClasses([
        'mason-sidebar',
        'has-grid-actions' => $hasGridActions,
        'has-color-mode-toggle' => $hasColorModeToggle ?? false,
    ]); ?>"
    <?php echo e($attributes); ?>

>
    <?php if (isset($component)) { $__componentOriginal1d5dbf2fd64c911b6e6c36726de29e4e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1d5dbf2fd64c911b6e6c36726de29e4e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'mason::components.controls','data' => ['hasColorModeToggle' => $hasColorModeToggle]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mason::controls'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['has-color-mode-toggle' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($hasColorModeToggle)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal1d5dbf2fd64c911b6e6c36726de29e4e)): ?>
<?php $attributes = $__attributesOriginal1d5dbf2fd64c911b6e6c36726de29e4e; ?>
<?php unset($__attributesOriginal1d5dbf2fd64c911b6e6c36726de29e4e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal1d5dbf2fd64c911b6e6c36726de29e4e)): ?>
<?php $component = $__componentOriginal1d5dbf2fd64c911b6e6c36726de29e4e; ?>
<?php unset($__componentOriginal1d5dbf2fd64c911b6e6c36726de29e4e); ?>
<?php endif; ?>
    <div
        class="mason-actions"
        wire:ignore
        x-data="{
            actions: <?php echo \Illuminate\Support\Js::from($brickIds)->toHtml() ?>,
            search: '',
            filterActions: function () {
                return this.actions.filter((name) =>
                    name.toLowerCase().includes(this.search.toLowerCase()),
                )
            },
        }"
    >
        <div class="mason-actions-search">
            <?php if (isset($component)) { $__componentOriginal505efd9768415fdb4543e8c564dad437 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal505efd9768415fdb4543e8c564dad437 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.input.wrapper','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::input.wrapper'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                <?php if (isset($component)) { $__componentOriginal9ad6b66c56a2379ee0ba04e1e358c61e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ad6b66c56a2379ee0ba04e1e358c61e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.input.index','data' => ['xRef' => 'search','xOn:input.debounce.300ms' => 'filterActions()','placeholder' => ''.e(trans('mason::mason.brick_search_placeholder')).'','type' => 'search','xModel' => 'search']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['x-ref' => 'search','x-on:input.debounce.300ms' => 'filterActions()','placeholder' => ''.e(trans('mason::mason.brick_search_placeholder')).'','type' => 'search','x-model' => 'search']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ad6b66c56a2379ee0ba04e1e358c61e)): ?>
<?php $attributes = $__attributesOriginal9ad6b66c56a2379ee0ba04e1e358c61e; ?>
<?php unset($__attributesOriginal9ad6b66c56a2379ee0ba04e1e358c61e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ad6b66c56a2379ee0ba04e1e358c61e)): ?>
<?php $component = $__componentOriginal9ad6b66c56a2379ee0ba04e1e358c61e; ?>
<?php unset($__componentOriginal9ad6b66c56a2379ee0ba04e1e358c61e); ?>
<?php endif; ?>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal505efd9768415fdb4543e8c564dad437)): ?>
<?php $attributes = $__attributesOriginal505efd9768415fdb4543e8c564dad437; ?>
<?php unset($__attributesOriginal505efd9768415fdb4543e8c564dad437); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal505efd9768415fdb4543e8c564dad437)): ?>
<?php $component = $__componentOriginal505efd9768415fdb4543e8c564dad437; ?>
<?php unset($__componentOriginal505efd9768415fdb4543e8c564dad437); ?>
<?php endif; ?>
        </div>
        <div class="mason-actions-bricks">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $bricks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brick): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                <div
                    draggable="true"
                    x-on:dragstart="
                        $event.dataTransfer.setData('brick', <?php echo \Illuminate\Support\Js::from($brick::getId())->toHtml() ?>)
                        $el.classList.add('dragging')
                    "
                    x-on:dragend="$el.classList.remove('dragging')"
                    class="mason-actions-brick"
                    x-on:open-modal.window="isLoading = false"
                    x-on:run-mason-commands.window="isLoading = false"
                    x-bind:class="{
                        'filtered': ! filterActions().includes(<?php echo \Illuminate\Support\Js::from($brick::getLabel())->toHtml() ?>),
                    }"
                >
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(filled($brick::getIcon())): ?>
                        <?php if (isset($component)) { $__componentOriginalbfc641e0710ce04e5fe02876ffc6f950 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.icon','data' => ['icon' => $brick::getIcon(),'class' => 'h-5 w-5 shrink-0']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($brick::getIcon()),'class' => 'h-5 w-5 shrink-0']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950)): ?>
<?php $attributes = $__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950; ?>
<?php unset($__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbfc641e0710ce04e5fe02876ffc6f950)): ?>
<?php $component = $__componentOriginalbfc641e0710ce04e5fe02876ffc6f950; ?>
<?php unset($__componentOriginalbfc641e0710ce04e5fe02876ffc6f950); ?>
<?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php echo e($brick::getLabel()); ?>

                </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>
    </div>
</div>
<?php /**PATH C:\laragon\www\mipresscz\vendor\awcodes\mason\resources\views/components/sidebar.blade.php ENDPATH**/ ?>