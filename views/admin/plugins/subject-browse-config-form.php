<fieldset id="fieldset-subject-browse-list"><legend><?php echo __('Subject Browse'); ?></legend>
    <div class="field">
        <div class="two columns alpha">
            <label for="subject_browse_enable_list"><?php echo __('Enable link'); ?></label>
        </div>
        <div class="inputs five columns omega">
            <?php echo get_view()->formCheckbox('subject_browse_enable_list', true,
                array('checked' => (boolean) get_option('subject_browse_enable_list'))); ?>
            <p class="explanation">
                <?php echo __('Enable the page and display the link to the list view in the navigation bar.'); ?>
            </p>
        </div>
    </div>
    <div class="field">
        <div class="two columns alpha">
            <label for="subject_browse_headers"><?php echo __('Print headings'); ?></label>
        </div>
        <div class="inputs five columns omega">
            <?php echo get_view()->formCheckbox('subject_browse_headers', true,
                array('checked' => (boolean) get_option('subject_browse_headers'))); ?>
            <p class="explanation">
                <?php echo __('Print headers for each section (#0-9 and symbols, A, B, etc.)'); ?>
            </p>
        </div>
    </div>
    <div class="field">
        <div class="two columns alpha">
            <label for="subject_browse_alphabetical_skiplinks"><?php echo __('Print skip links'); ?></label>
        </div>
        <div class="inputs five columns omega">
            <?php echo get_view()->formCheckbox('subject_browse_alphabetical_skiplinks', true,
                array('checked' => (boolean) get_option('subject_browse_alphabetical_skiplinks'))); ?>
            <p class="explanation">
                <?php echo __('Print skip links at the top and bottom of each page, which link to the alphabetical headers.'); ?>
                <?php echo __('Note that if headers are turned off, skiplinks do not work.'); ?>
            </p>
        </div>
    </div>
</fieldset>
<fieldset id="fieldset-subject-browse-tree"><legend><?php echo __('Hierarchy of Subjects'); ?></legend>
    <div class="field">
        <div class="two columns alpha">
            <label for="subject_browse_enable_tree"><?php echo __('Enable link'); ?></label>
        </div>
        <div class="inputs five columns omega">
            <?php echo get_view()->formCheckbox('subject_browse_enable_tree', true,
                array('checked' => (boolean) get_option('subject_browse_enable_tree'))); ?>
            <p class="explanation">
                <?php echo __('Enable the page and display the link to the hierarchical view in the navigation bar.'); ?>
            </p>
        </div>
    </div>
    <div class="field">
        <div class="two columns alpha">
            <label for="subject_browse_expanded"><?php echo __('Expand Tree'); ?></label>
        </div>
        <div class="inputs five columns omega">
            <?php echo get_view()->formCheckbox('subject_browse_expanded', true,
                array('checked' => (boolean) get_option('subject_browse_expanded'))); ?>
            <p class="explanation">
                <?php echo __('Check this box to display the tree expanded.'); ?>
                <?php echo __('This option can be overridden by the theme.'); ?>
            </p>
        </div>
    </div>
    <div class="field">
        <div class="two columns alpha">
            <label for="subject_browse_hierarchy"><?php echo __('Set the hierarchy of subjects'); ?></label>
        </div>
        <div class="inputs five columns omega">
           <div class='input-block'>
                <?php echo get_view()->formTextarea(
                    'subject_browse_hierarchy',
                    get_option('subject_browse_hierarchy'),
                    array(
                        'rows' => 20,
                        'cols' => 60,
                        'class' => array('textinput'),
                )); ?>
            <p class="explanation">
                <?php echo __('If any, write the hierarchy of all your subjects in order to display them in the "Hierarchy of Subjects" page.'); ?>
                <?php echo __('Format is: one subjet by line, preceded by zero, one or more "-" to indicate the hierarchy level. Separate the "-" and the subject with a space. Empty lines are not considered.'); ?>
            </p>
        </div>
    </div>
</fieldset>
