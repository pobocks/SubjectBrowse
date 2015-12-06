<?php
/**
 * Subject Browse Tree helper.
 */

/**
 * @package SubjectBrowse
 */
class SubjectBrowse_View_Helper_SubjectBrowseTree extends Zend_View_Helper_Abstract
{
    protected $_DC_Subject_id = 49;

    /**
     * Display the tree of subjects.
     *
     * Use the partial view to convert a hierarchy string into html hierarchical
     * lists in order to build a javascript interactive tree.
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
     * @param string $subjects Subjects to show, if any, else default ones.
     * Format of this string can be found in the _subjectsTree() method.
     * @param array $options Options can be these booleans:
     * - linked: Show subjects as links (default to true)
     * - expanded: Show view as expanded (defaul to config)
     *
     * @return string Html tree.
     */
    public function subjectBrowseTree($subjects = null, array $options = array())
    {
        $view = $this->view;

        $this->_DC_Subject_id = (integer) get_option('subject_browse_DC_Subject_id');

        $options = $this->_cleanOptions($options);

        if (empty($subjects)) {
            $subjects = $this->_getSubjectsTree();
        }

        $html = $view->partial('common/subject-browse-tree.php', array(
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
        // Get list of options.
        $linked = (boolean) (isset($options['linked']) ? $options['linked'] : true);
        $expanded = (boolean) (isset($options['expanded'])
            ? $options['expanded']
            : get_option('subject_browse_expanded'));

        return array(
            'linked' => $linked,
            'expanded' => $expanded,
        );
    }

    /**
     * Get the dafault tree of subjects.
     */
    protected function _getSubjectsTree()
    {
        $subjects = get_option('subject_browse_hierarchy');
        $subjects = array_filter(explode(PHP_EOL, $subjects));

        return $subjects;
    }
}
