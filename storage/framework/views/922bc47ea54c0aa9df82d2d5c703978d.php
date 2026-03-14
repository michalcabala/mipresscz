<?php
    use Filament\Support\Facades\FilamentView;
    use Filament\Support\Icons\Heroicon;
    use function Filament\Support\generate_icon_html;

    $id = $getId();
    $key = $getKey();
    $statePath = $getStatePath();
    $isDisabled = $isDisabled();
    $bricks = $getBricks();
    $defaultColorMode = $getDefaultColorMode();
    $hasColorModeToggle = $hasColorModeToggle();
?>

<?php if (isset($component)) { $__componentOriginal511d4862ff04963c3c16115c05a86a9d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal511d4862ff04963c3c16115c05a86a9d = $attributes; } ?>
<?php $component = Illuminate\View\DynamicComponent::resolve(['component' => $getFieldWrapperView()] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dynamic-component'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\DynamicComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['field' => $field]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <div
        <?php if(FilamentView::hasSpaMode()): ?>
            x-load="visible || event (x-modal-opened)"
        <?php else: ?>
            x-load
        <?php endif; ?>
        x-load-src="<?php echo e(\Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc("mason", "awcodes/mason")); ?>"
        x-data="masonComponent({
                    key: <?php echo \Illuminate\Support\Js::from($key)->toHtml() ?>,
                    livewireId: <?php echo \Illuminate\Support\Js::from($this->getId())->toHtml() ?>,
                    state: $wire.<?php echo e($applyStateBindingModifiers("\$entangle('{$statePath}')", isOptimisticallyLive: false)); ?>,
                    statePath: <?php echo \Illuminate\Support\Js::from($statePath)->toHtml() ?>,
                    placeholder: <?php echo \Illuminate\Support\Js::from($getPlaceholder())->toHtml() ?>,
                    disabled: <?php echo \Illuminate\Support\Js::from($isDisabled)->toHtml() ?>,
                    dblClickToEdit: <?php echo \Illuminate\Support\Js::from($shouldDblClickToEdit())->toHtml() ?>,
                    bricks: <?php echo \Illuminate\Support\Js::from(array_map(fn ($brick) => is_string($brick) ? $brick : get_class($brick), $bricks))->toHtml() ?>,
                    previewLayout: <?php echo \Illuminate\Support\Js::from($getPreviewLayout())->toHtml() ?>,
                    defaultColorMode: <?php echo \Illuminate\Support\Js::from($defaultColorMode)->toHtml() ?>,
                    hasColorModeToggle: <?php echo \Illuminate\Support\Js::from($hasColorModeToggle)->toHtml() ?>,
                })"
        id="<?php echo e("mason-wrapper-" . $statePath); ?>"
        class="mason-wrapper"
        tabindex="-1"
        x-bind:class="{
            'fullscreen': fullscreen,
            'display-mobile': viewport === 'mobile',
            'display-tablet': viewport === 'tablet',
            'display-desktop': viewport === 'desktop',
        }"
        x-on:keydown.escape.window="fullscreen = false"
        x-on:click.away="deselectAllBlocks()"
    >
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $isDisabled): ?>
            <div class="mason-topbar">
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
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if (isset($component)) { $__componentOriginal505efd9768415fdb4543e8c564dad437 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal505efd9768415fdb4543e8c564dad437 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.input.wrapper','data' => ['valid' => ! $errors->has($statePath),'attributes' => 
                \Filament\Support\prepare_inherited_attributes($getExtraAttributeBag())
                    ->class([
                        'mason-input-wrapper',
                    ])
            ]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::input.wrapper'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['valid' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(! $errors->has($statePath)),'attributes' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(
                \Filament\Support\prepare_inherited_attributes($getExtraAttributeBag())
                    ->class([
                        'mason-input-wrapper',
                    ])
            )]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

            <div
                class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                    "flex flex-1",
                    "flex-row-reverse" =>
                        $getSidebarPosition() === \Awcodes\Mason\Enums\SidebarPosition::Start,
                ]); ?>"
            >
                <div
                    class="mason-editor-wrapper"
                    <?php echo e(\Filament\Support\prepare_inherited_attributes($getExtraInputAttributeBag())->class([
                            "mason-input-wrapper",
                        ])); ?>

                >
                    <iframe
                        x-ref="previewIframe"
                        name="<?php echo e("mason-preview-iframe-" . $statePath); ?>"
                        class="mason-iframe"
                        wire:ignore
                    ></iframe>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $isDisabled && filled($bricks)): ?>
                    <?php if (isset($component)) { $__componentOriginale04cb7bff099e20315328ca2e65c2d19 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale04cb7bff099e20315328ca2e65c2d19 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'mason::components.sidebar','data' => ['bricks' => $bricks,'hasGridActions' => $hasGridActions(),'hasColorModeToggle' => $hasColorModeToggle,'wire:key' => 'sidebar-'.e(hash('sha256', json_encode($bricks))).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mason::sidebar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['bricks' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($bricks),'has-grid-actions' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($hasGridActions()),'has-color-mode-toggle' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($hasColorModeToggle),'wire:key' => 'sidebar-'.e(hash('sha256', json_encode($bricks))).'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale04cb7bff099e20315328ca2e65c2d19)): ?>
<?php $attributes = $__attributesOriginale04cb7bff099e20315328ca2e65c2d19; ?>
<?php unset($__attributesOriginale04cb7bff099e20315328ca2e65c2d19); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale04cb7bff099e20315328ca2e65c2d19)): ?>
<?php $component = $__componentOriginale04cb7bff099e20315328ca2e65c2d19; ?>
<?php unset($__componentOriginale04cb7bff099e20315328ca2e65c2d19); ?>
<?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
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

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $isDisabled && filled($bricks)): ?>
            <?php if (isset($component)) { $__componentOriginal7bbc70a87cf50fe51d158dcd66785867 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7bbc70a87cf50fe51d158dcd66785867 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'mason::components.brick-picker-modal','data' => ['bricks' => $bricks]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mason::brick-picker-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['bricks' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($bricks)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7bbc70a87cf50fe51d158dcd66785867)): ?>
<?php $attributes = $__attributesOriginal7bbc70a87cf50fe51d158dcd66785867; ?>
<?php unset($__attributesOriginal7bbc70a87cf50fe51d158dcd66785867); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7bbc70a87cf50fe51d158dcd66785867)): ?>
<?php $component = $__componentOriginal7bbc70a87cf50fe51d158dcd66785867; ?>
<?php unset($__componentOriginal7bbc70a87cf50fe51d158dcd66785867); ?>
<?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $attributes = $__attributesOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $component = $__componentOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__componentOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>
<?php /**PATH C:\laragon\www\mipresscz\vendor\awcodes\mason\resources\views/mason.blade.php ENDPATH**/ ?>