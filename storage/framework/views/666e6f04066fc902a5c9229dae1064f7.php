<?php $__env->startSection('title', ($entry->meta_title ?? null) ?: config('app.name', 'miPress')); ?>
<?php $__env->startSection('description', $entry->meta_description ?? __('Moderní CMS postavené na Laravelu 12, Filamentu 5 a Tailwind CSS. Strukturovaný obsah, blokový editor a vícejazyčnost.')); ?>

<?php $__env->startSection('content'); ?>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($entry->content)): ?>
    <?php echo mason(content: $entry->content, bricks: $bricks ?? [])->toHtml(); ?>

<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('template::layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\mipresscz\resources\views/templates/default/pages/home.blade.php ENDPATH**/ ?>