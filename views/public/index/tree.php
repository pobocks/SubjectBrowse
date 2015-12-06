<?php
$title = __('Hierarchy of Subjects');
echo head(array(
    'title' => $title,
    'bodyclass' => 'subject-browse browse hierarchy',
)); ?>
<div id="primary" class="subject-browse">
    <h1><?php echo __('Hierarchy of Subjects (%d total)', count($subjects)); ?></h1>
    <nav class="items-nav navigation secondary-nav">
        <?php echo public_nav_items(); ?>
    </nav>
    <?php
    if (count($subjects)) :
        echo $this->subjectBrowse($subjects, array('mode' => 'tree'));
    else :
        echo '<p>' . __('There is no hierarchy of subjects.') . '</p>';
    endif;
    ?>
</div>
<?php echo foot();
