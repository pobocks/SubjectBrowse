<?php
$title = __('Hierarchy of Subjects');

queue_css_file('jquery-simple-folders');
queue_js_file('jquery-simple-folders');

echo head(array(
    'title' => $title,
    'bodyclass' => 'subject-browse browse hierarchy',
)); ?>
<div id="primary" class="subject-browse">
    <h1><?php echo __('Hierarchy of Subjects (%d total)', count($subjects)); ?></h1>
    <nav class="items-nav navigation secondary-nav">
        <?php echo public_nav_items(); ?>
    </nav>
    <div id="sb-subject-headings">
        <?php
        if (count($subjects)) :
            echo $this->treeList($subjects);
        else :
            echo '<p>' . __('There is no hierarchy of subjects.') . '</p>';
        endif;
        ?>
    </div>
</div>
<?php echo foot(); ?>
