<?php
$pageTitle = __('Browse Items by "%s"', $referenceLabel);
echo head(array(
    'title' => $pageTitle,
    'bodyclass' => 'reference browse list',
)); ?>
<div id="primary" class="reference">
    <h1><?php echo __('Browse Items By "%s" (%d Headings)', $referenceLabel, count($references)); ?></h1>
    <nav class="items-nav navigation secondary-nav">
        <?php echo public_nav_items(); ?>
    </nav>
    <?php
    if (count($references)) :
        echo $this->reference($references, array(
            'mode' => 'list',
            'slug' => $referenceSlug,
        ));
    else:
        echo '<p>' . __('There is no references for "%s".', $referenceLabel) . '</p>';
    endif;
    ?>
</div>
<?php echo foot();
