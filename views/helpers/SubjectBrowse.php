<?php
/**
 * Subject Browse helper.
 */

/**
 * @package SubjectBrowse
 */
class SubjectBrowse_View_Helper_SubjectBrowse extends Zend_View_Helper_Abstract
{
    protected $_DC_Subject_id = 49;

    /**
     * Display the list or the tree of subjects via a partial view.
     *
     * For the mode "list", the subjects come from the items and are listed in
     * alphabetical order.
     * For the mode "tree", the subjects come from the configuration and a
     * interactive hierarchical list is build with javascript .
     *
     * @uses http://www.jqueryscript.net/other/jQuery-Flat-Folder-Tree-Plugin-simplefolders.html
     *
     * @example
     * $subjects = "
     * Europe
     * - France
     * - Germany
     * - United Kingdom
     * -- England
     * -- Scotland
     * -- Wales
     * Asia
     * - Japan
     * ";
     *
     * $hierarchy = "
     * <ul class="tree">
     *     <li>Europe
     *         <div class="expander"></div>
     *         <ul>
     *             <li>France</li>
     *             <li>Germany</li>
     *             <li>United Kingdom
     *                 <div class="expander"></div>
     *                 <ul>
     *                     <li>England</li>
     *                     <li>Scotland</li>
     *                     <li>Wales</li>
     *                 </ul>
     *             </li>
     *         </ul>
     *     </li>
     *     <li>Asia
     *         <div class="expander"></div>
     *         <ul>
     *             <li>Japan</li>
     *         </ul>
     *     </li>
     * </ul>
     * ";
     *
     * @param array $subjects Array of Subjects elements to show, if any, else
     * default ones
     * @param array $options Options to display the subjects. They depend on the
     * key "mode" ("list" (default) or "tree"). Values are booleans:
     * - raw: Show subjects as raw text, not links (default to false)
     * - strip: Remove html tags (default to true)
     * - skiplinks: Add the list of letters at top and bottom of the page
     * - headings: Add each letter as headers
     * For "tree"
     * - raw: Show subjects as raw text, not links (default to false)
     * - strip: Remove html tags (default to true)
     * - expanded: Show tree as expanded (defaul to config)
     *
     * @return string Html list.
     */
    public function subjectBrowse($subjects = array(), array $options = array())
    {
        $view = $this->view;

        $this->_DC_Subject_id = (integer) get_option('subject_browse_DC_Subject_id');

        $options = $this->_cleanOptions($options);

        if (empty($subjects)) {
            $subjects = $options['mode'] == 'tree'
                ? $this->_getSubjectsTree()
                : $this->_getSubjectsList();
        }

        if ($options['strip']) {
            $subjects = array_map('strip_formatting', $subjects);
            // List of subjects may need to be reordered after reformatting.
            if ($options['mode'] == 'list') {
                natcasesort($subjects);
                $subjects = array_unique($subjects);
            }
        }

        $html = $view->partial('common/subject-browse-' . $options['mode'] . '.php', array(
            'dcSubjectId' => $this->_DC_Subject_id,
            'subjects' => $subjects,
            'options' => $options,
        ));

        return $html;
    }

    /**
     * Get list of options.
     */
    protected function _cleanOptions($options)
    {
        $mode = isset($options['mode']) && $options['mode'] == 'tree' ? 'tree' : 'list';

        $cleanedOptions = array(
            'mode' => $mode,
            'raw' => isset($options['raw']) && $options['raw'],
            'strip' => isset($options['strip']) ? (boolean) $options['strip'] : true,
        );

        switch ($mode) {
            case 'list':
                $cleanedOptions['headings'] = (boolean) (isset($options['headings'])
                    ? $options['headings']
                    : get_option('subject_browse_list_headings'));
                $cleanedOptions['skiplinks'] = (boolean) (isset($options['skiplinks'])
                    ? $options['skiplinks']
                    : get_option('subject_browse_list_skiplinks'));
                break;

            case 'tree':
                $cleanedOptions['expanded'] = (boolean) (isset($options['expanded'])
                    ? $options['expanded']
                    : get_option('subject_browse_tree_expanded'));
                break;
        }

        return $cleanedOptions;
    }

    /**
     * Get the dafault list of subjects.
     */
    protected function _getSubjectsList()
    {
        // A query allows quick access to all subjects (no need for elements).
        $db = get_db();
        $sql = "
            SELECT DISTINCT `text`
            FROM `$db->ElementTexts`
            WHERE `element_id` = {$this->_DC_Subject_id}
            ORDER BY `text`
            COLLATE 'utf8_unicode_ci'
        ";
        $result = $db->fetchCol($sql);

        return $result;
    }

    /**
     * Get the dafault tree of subjects.
     */
    protected function _getSubjectsTree()
    {
        $subjects = get_option('subject_browse_tree_hierarchy');
        $subjects = array_filter(explode(PHP_EOL, $subjects));

        return $subjects;
    }
}
