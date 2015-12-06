<fieldset id="fieldset-subject-browse-general"><legend><?php echo __('Subject Browse'); ?></legend>
    <p>
        <?php echo __('Most of these options for list and for tree can be overridden in the theme.'); ?>
    </p>
</fieldset>
<fieldset id="fieldset-subject-browse-list"><legend><?php echo __('Subject Index'); ?></legend>
    <div class="field">
        <div class="two columns alpha">
            <?php echo $this->formLabel('subject_browse_list_enabled',
                __('Enable list view')); ?>
        </div>
        <div class="inputs five columns omega">
            <?php echo $this->formCheckbox('subject_browse_list_enabled', true,
                array('checked' => (boolean) get_option('subject_browse_list_enabled'))); ?>
            <p class="explanation">
                <?php echo __('Enable the page and display the link "%s" to the list view in the navigation bar.',
                    '<a href="' . url(SubjectBrowsePlugin::SUBJECT_BROWSE_PATH_LIST) . '">' . url(SubjectBrowsePlugin::SUBJECT_BROWSE_PATH_LIST). '</a>'); ?>
            </p>
        </div>
    </div>
    <div class="field">
        <div class="two columns alpha">
            <?php echo $this->formLabel('subject_browse_list_skiplinks',
                __('Print skip links')); ?>
        </div>
        <div class="inputs five columns omega">
            <?php echo $this->formCheckbox('subject_browse_list_skiplinks', true,
                array('checked' => (boolean) get_option('subject_browse_list_skiplinks'))); ?>
            <p class="explanation">
                <?php echo __('Print skip links at the top and bottom of each page, which link to the alphabetical headers.'); ?>
                <?php echo __('Note that if headers are turned off, skiplinks do not work.'); ?>
            </p>
        </div>
    </div>
    <div class="field">
        <div class="two columns alpha">
            <?php echo $this->formLabel('subject_browse_list_headings',
                __('Print headings')); ?>
        </div>
        <div class="inputs five columns omega">
            <?php echo $this->formCheckbox('subject_browse_list_headings', true,
                array('checked' => (boolean) get_option('subject_browse_list_headings'))); ?>
            <p class="explanation">
                <?php echo __('Print headers for each section (#0-9 and symbols, A, B, etc.)'); ?>
            </p>
        </div>
    </div>
</fieldset>
<fieldset id="fieldset-subject-browse-tree"><legend><?php echo __('Hierarchy of Subjects'); ?></legend>
    <div class="field">
        <div class="two columns alpha">
            <?php echo $this->formLabel('subject_browse_tree_enabled',
                __('Enable tree view')); ?>
        </div>
        <div class="inputs five columns omega">
            <?php echo $this->formCheckbox('subject_browse_tree_enabled', true,
                array('checked' => (boolean) get_option('subject_browse_tree_enabled'))); ?>
            <p class="explanation">
                <?php echo __('Enable the page and display the link "%s" to the hierarchical view in the navigation bar.',
                    '<a href="' . url(SubjectBrowsePlugin::SUBJECT_BROWSE_PATH_TREE) . '">' . url(SubjectBrowsePlugin::SUBJECT_BROWSE_PATH_TREE) . '</a>'); ?>
            </p>
        </div>
    </div>
    <div class="field">
        <div class="two columns alpha">
            <?php echo $this->formLabel('subject_browse_tree_expanded',
                __('Expand tree')); ?>
        </div>
        <div class="inputs five columns omega">
            <?php echo $this->formCheckbox('subject_browse_tree_expanded', true,
                array('checked' => (boolean) get_option('subject_browse_tree_expanded'))); ?>
            <p class="explanation">
                <?php echo __('Check this box to display the tree expanded.'); ?>
                <?php echo __('This option can be overridden by the theme.'); ?>
            </p>
        </div>
    </div>
    <div class="field">
        <div class="two columns alpha">
            <?php echo $this->formLabel('subject_browse_tree_hierarchy',
                __('Set the hierarchy of subjects')); ?>
        </div>
        <div class="inputs five columns omega">
           <div class='input-block'>
                <?php echo $this->formTextarea(
                    'subject_browse_tree_hierarchy',
                    get_option('subject_browse_tree_hierarchy'),
                    array(
                        'rows' => 20,
                        'cols' => 60,
                        'class' => array('textinput'),
                        // The place holder can't use end of line, so a symbol
                        // is used for it.
                        'placeholder' => '
Europe ↵
- France ↵
-- Paris ↵
-- Lyon ↵
-- Marseille ↵
- United Kingdom ↵
-- London ↵
-- Manchester ↵
Asia ↵
',
                )); ?>
            </div>
            <p class="explanation">
                <?php echo __('If any, write the hierarchy of all your subjects in order to display them in the "Hierarchy of Subjects" page.'); ?>
                <?php echo __('Format is: one subjet by line, preceded by zero, one or more "-" to indicate the hierarchy level. Separate the "-" and the subject with a space. Empty lines are not considered.'); ?>
            </p>
        </div>
    </div>
</fieldset>
