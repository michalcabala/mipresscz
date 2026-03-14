<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($text): ?>
    <div class="mason-brick mason-heading text-<?php echo e($alignment ?? 'left'); ?>">
        <<?php echo e($level ?? 'h2'); ?> class="font-bold leading-tight">
            <?php echo e($text); ?>

        </<?php echo e($level ?? 'h2'); ?>>
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    //
</div>
<?php /**PATH C:\laragon\www\mipresscz\resources\views/mason/heading.blade.php ENDPATH**/ ?>