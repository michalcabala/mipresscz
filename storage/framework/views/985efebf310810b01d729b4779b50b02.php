<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>

<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <title><?php echo e(config('app.name')); ?></title>
        <link><?php echo e(url('/')); ?></link>
        <description><?php echo e(config('app.name')); ?></description>
        <language><?php echo e($locale); ?></language>
        <lastBuildDate><?php echo e(now()->toRfc1123String()); ?></lastBuildDate>
        <atom:link href="<?php echo e(url('/feed.xml')); ?>" rel="self" type="application/rss+xml"/>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $entries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
            <?php
                $entryUrl = $entry->locale === $defaultLocale
                    ? url($entry->uri)
                    : url('/'.$entry->locale.$entry->uri);
            ?>
            <item>
                <title><![CDATA[<?php echo e($entry->title); ?>]]></title>
                <link><?php echo e($entryUrl); ?></link>
                <guid isPermaLink="true"><?php echo e($entryUrl); ?></guid>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($entry->meta_description): ?>
                    <description><![CDATA[<?php echo e($entry->meta_description); ?>]]></description>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($entry->published_at): ?>
                    <pubDate><?php echo e($entry->published_at->toRfc1123String()); ?></pubDate>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </item>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    </channel>
</rss>
<?php /**PATH C:\laragon\www\mipresscz\packages\mipresscz\core\src/../resources/views/feed/rss.blade.php ENDPATH**/ ?>