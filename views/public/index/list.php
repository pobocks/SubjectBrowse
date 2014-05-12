<?php
$title = __('Browse Items by Subject');
echo head(array(
    'title' => $title,
    'bodyclass' => 'subject-browse browse list',
)); ?>
<div id="primary" class="subject-browse">
    <h1><?php echo __('Browse Items By Subject (%d Headings)', count($subjects)); ?></h1>
    <nav class="items-nav navigation secondary-nav">
        <?php echo public_nav_items(); ?>
    </nav>
    <?php if (get_option('subject_browse_alphabetical_skiplinks')) : ?>
    <div class="pagination sb-pagination" id="pagination-top">
        <ul class="pagination_list">
            <li class="pagination_range"><a href="#number">#0-9</a></li>
            <?php $pagination_list = '';
            foreach (range('A', 'Z') as $i) {
                $pagination_list .= sprintf('<li class="pagination_range"><a href="#%s">%s</a></li>', $i, $i);
            }
            echo $pagination_list;
            ?>
        </ul>
    </div>
    <?php endif; ?>
    <div id="sb-subject-headings">
        <?php
        if (count($subjects)) {
            $current_header = '';
            foreach ($subjects as $header) {
                $first_char = substr($header, 0, 1);
                if (preg_match('/\W|\d/', $first_char)) {
                    $first_char = '#0-9';
                }
                if ($current_header !== strtoupper($first_char)) {
                    $current_header = strtoupper($first_char);
                    if (get_option('subject_browse_headers')) {
                        if ($current_header === '#0-9') {
                            echo "<h3 class='sb-subject-heading' id='number'>$current_header</h3>";
                        }
                        else {
                            echo "<h3 class='sb-subject-heading' id='$current_header'>$current_header</h3>";
                        }
                    }
                }
                printf('<p class="sb-subject"><a href="%s">%s</a></p>',
                    url(sprintf('items/browse?search=&advanced[0][element_id]=%s&advanced[0][type]=contains&advanced[0][terms]=%s&submit_search=Search',
                        get_option('subject_browse_DC_Subject_id'),
                        urlencode($header)
                    )),
                    $header
                );
            }
        }
        else {
            echo '<p>' . __('There is no subjects.') . '</p>';
        } ?>
    </div>
    <?php if (get_option('subject_browse_alphabetical_skiplinks')) : ?>
    <div class="pagination sb-pagination" id="pagination-bottom">
        <ul class="pagination_list">
            <li class="pagination_range"><a href="#number">#0-9</a></li>
            <?php echo $pagination_list; ?>
        </ul>
    </div>
    <?php endif; ?>
</div>
<?php echo foot(); ?>
