<?php
$pageTitle = __('Browse Items by "%s"', $slugData['label']);
echo head(array(
    'title' => $pageTitle,
    'bodyclass' => 'reference browse list',
)); ?>
<div id="primary" class="reference">
    <h1><?php echo __('Browse Items By "%s" (%d Headings)', $slugData['label'], count($references)); ?></h1>
    <nav class="items-nav navigation secondary-nav">
        <?php echo public_nav_items(); ?>
    </nav>
    <?php
    if (count($references)) :
        echo $this->reference($references, array(
            'mode' => 'list',
            'slug' => $slug,
            'slugData' => $slugData,
        ));
    else:
        echo '<p>' . __('There is no references for "%s".', $slugData['label']) . '</p>';
    endif;
    ?>
</div>
<?php echo foot();
