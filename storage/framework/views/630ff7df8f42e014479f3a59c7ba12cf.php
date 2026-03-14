<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($label ?? null) && ($url ?? null)): ?>
    <?php
        $variantClasses = match($variant ?? 'primary') {
            'secondary' => 'bg-gray-600 hover:bg-gray-700 text-white',
            'outline' => 'border-2 border-primary-600 text-primary-600 hover:bg-primary-50',
            default => 'bg-primary-600 hover:bg-primary-700 text-white',
        };
        $alignClass = match($alignment ?? 'left') {
            'center' => 'text-center',
            'right' => 'text-right',
            default => 'text-left',
        };
    ?>
    <div class="mason-brick mason-button <?php echo e($alignClass); ?>">
        <a href="<?php echo e($url); ?>"
           class="inline-block font-semibold px-6 py-3 rounded-lg transition <?php echo e($variantClasses); ?>"
           <?php if($open_in_new_tab ?? false): ?> target="_blank" rel="noopener noreferrer" <?php endif; ?>>
            <?php echo e($label); ?>

        </a>
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    //
</div>
<?php /**PATH C:\laragon\www\mipresscz\resources\views/mason/button.blade.php ENDPATH**/ ?>