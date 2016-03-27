<style type="text/css">
.reference-boxes {
    text-align: center;
}
.input-block ul {
    list-style: none outside none;
}
</style>
<fieldset id="fieldset-reference-general"><legend><?php echo __('Reference'); ?></legend>
    <p>
        <?php echo __('Most of these options for list and for tree can be overridden in the theme.'); ?>
    </p>
</fieldset>
<fieldset id="fieldset-reference-list"><legend><?php echo __('References Indexes'); ?></legend>
    <div class="field">
        <div>
            <?php echo $this->formLabel('reference_list_elements', __('Display References')); ?>
        </div>
        <div class="inputs">
            <p class="explanation">
                <?php echo __('Select the elements to display and define a slug so the references will be available at "references/:slug".'); ?>
                <?php echo __('Slugs should be single.'); ?>
            </p>
            <table id="hide-elements-table">
                <thead>
                    <tr>
                        <th class="curator-monitor-boxes"><?php echo __('Element'); ?></th>
                        <th class="curator-monitor-boxes"><?php echo __('Display'); ?></th>
                        <th class="curator-monitor-boxes"><?php echo __('Slug'); ?></th>
                        <th class="curator-monitor-boxes"><?php echo __('Label'); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $current_element_set = null;
                foreach ($elements as $element):
                    if ($element->set_name != $current_element_set):
                        $current_element_set = $element->set_name; ?>
                    <tr>
                        <th colspan="4">
                            <strong><?php echo __($current_element_set); ?></strong>
                        </th>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <td><?php echo __($element->name); ?></td>
                        <td class="reference-boxes">
                            <?php echo $this->formCheckbox(
                                "reference_list_elements[active][{$element->id}]",
                                '1', array(
                                    'disableHidden' => true,
                                    'checked' => isset($settings['active'][$element->id]),
                                )
                            ); ?>
                        </td>
                        <td class="reference-boxes">
                            <?php echo $this->formText(
                                "reference_list_elements[slug][{$element->id}]",
                                $settings['slug'][$element->id], null); ?>
                        </td>
                        <td class="reference-boxes">
                            <?php echo $this->formText(
                                "reference_list_elements[label][{$element->id}]",
                                $settings['label'][$element->id], null); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="field">
        <div class="two columns alpha">
            <?php echo $this->formLabel('reference_list_skiplinks',
                __('Print skip links')); ?>
        </div>
        <div class="inputs five columns omega">
            <?php echo $this->formCheckbox('reference_list_skiplinks', true,
                array('checked' => (boolean) get_option('reference_list_skiplinks'))); ?>
            <p class="explanation">
                <?php echo __('Print skip links at the top and bottom of each page, which link to the alphabetical headers.'); ?>
                <?php echo __('Note that if headers are turned off, skiplinks do not work.'); ?>
            </p>
        </div>
    </div>
    <div class="field">
        <div class="two columns alpha">
            <?php echo $this->formLabel('reference_list_headings',
                __('Print headings')); ?>
        </div>
        <div class="inputs five columns omega">
            <?php echo $this->formCheckbox('reference_list_headings', true,
                array('checked' => (boolean) get_option('reference_list_headings'))); ?>
            <p class="explanation">
                <?php echo __('Print headers for each section (#0-9 and symbols, A, B, etc.)'); ?>
            </p>
        </div>
    </div>
    <div class="field">
        <div class="two columns alpha">
            <?php echo $this->formLabel('reference_query_type', __('Query Type')); ?>
        </div>
        <div class='inputs five columns omega'>
            <?php
                echo $this->formRadio('reference_query_type', get_option('reference_query_type') ?: 'is exactly', null, array(
                    'is exactly' => __('Is Exactly'),
                    'contains' => __('Contains'),
                ));
            ?>
            <p class="explanation">
                <?php echo __('The type of query defines how elements are regrouped (see the advanced search).'); ?>
            </p>
        </div>
    </div>
</fieldset>
<fieldset id="fieldset-reference-tree"><legend><?php echo __('Hierarchy of Subjects'); ?></legend>
    <div class="field">
        <div class="two columns alpha">
            <?php echo $this->formLabel('reference_tree_enabled',
                __('Enable tree view')); ?>
        </div>
        <div class="inputs five columns omega">
            <?php echo $this->formCheckbox('reference_tree_enabled', true,
                array('checked' => (boolean) get_option('reference_tree_enabled'))); ?>
            <p class="explanation">
                <?php echo __('Enable the page and display the link "%s" to the hierarchical view in the navigation bar.',
                    '<a href="' . url(ReferencePlugin::REFERENCE_PATH_TREE) . '">' . url(ReferencePlugin::REFERENCE_PATH_TREE) . '</a>'); ?>
            </p>
        </div>
    </div>
    <div class="field">
        <div class="two columns alpha">
            <?php echo $this->formLabel('reference_tree_expanded',
                __('Expand tree')); ?>
        </div>
        <div class="inputs five columns omega">
            <?php echo $this->formCheckbox('reference_tree_expanded', true,
                array('checked' => (boolean) get_option('reference_tree_expanded'))); ?>
            <p class="explanation">
                <?php echo __('Check this box to display the tree expanded.'); ?>
                <?php echo __('This option can be overridden by the theme.'); ?>
            </p>
        </div>
    </div>
    <div class="field">
        <div class="two columns alpha">
            <?php echo $this->formLabel('reference_tree_hierarchy',
                __('Set the hierarchy of subjects')); ?>
        </div>
        <div class="inputs five columns omega">
           <div class='input-block'>
                <?php echo $this->formTextarea(
                    'reference_tree_hierarchy',
                    get_option('reference_tree_hierarchy'),
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
