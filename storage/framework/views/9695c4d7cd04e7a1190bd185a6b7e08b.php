<?php $__env->startSection('title', '404 – Stránka nenalezena'); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('template::errors.404', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('template::layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\mipresscz\resources\views/errors/404.blade.php ENDPATH**/ ?>