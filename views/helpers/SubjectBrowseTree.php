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
        $this->_DC_Subject_id = get_option('subject_browse_DC_Subject_id');

        $options = $this->_cleanOptions($options);

        if (empty($subjects)) {
            $subjects = $this->_getSubjectsTree();
        }

        $html = $this->_subjectsTree($subjects, $options);

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
            : get_option('subject_browse_expanded_tree'));

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

    /**
     * Convert a hierarchy string into html hierarchical lists in order to build
     * a javascript interactive tree.
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
     * @param string $subjects
     * @param boolean $options See subjectBrowse()
     *
     * @return string Html lists, the first list with the class "tree".
     */
    protected function _subjectsTree($subjects, $options)
    {
        $html = '';

        if (count($subjects)) {
            $html .= '<div id="sb-subject-headings">';
            $html .= sprintf('<link href="%s" media="all" rel="stylesheet" type="text/css" />', css_src('jquery-simple-folders'));
            $html .= js_tag('jquery-simple-folders');

            // Create the tree.
            $html .= '<ul class="tree">';
            $previous_level = 0;
            foreach ($subjects as $key => $subject) {
                $first = substr($subject, 0, 1);
                $space = strpos($subject, ' ');
                $level = ($first !== '-' || $space === false) ? 0 : $space;
                $header = trim($level == 0 ? $subject : substr($subject, $space));

                // Close the previous line (done before, because next line is
                // not known yet).
                if ($key == 0) {
                    // Nothing for the first level.
                }
                elseif ($level > $previous_level) {
                    // Deeper level is always the next one.
                }
                // Higher level.
                elseif ($level < $previous_level) {
                    $html .= '</li>' . PHP_EOL . str_repeat('</ul></li>' . PHP_EOL, $previous_level - $level);
                }
                // First line, deeper or equal level.
                else {
                    $html .= '</li>' . PHP_EOL;
                }

                // Start the line with or without a new sub-list.
                if ($level > $previous_level) {
                    // Deeper level is always the next one.
                    $html .= PHP_EOL . '<div class="expander' . ($options['expanded'] ? ' expanded' : '') . '"></div><ul' . ($options['expanded'] ? ' class="expanded"' : '') . '><li>';
                }
                else {
                    $html .= '<li>';
                }

                // Get the line.
                if ($options['linked']) {
                    $html .= sprintf('<a href="%s">%s</a>',
                        url(sprintf('items/browse?search=&amp;advanced[0][element_id]=%s&amp;advanced[0][type]=contains&amp;advanced[0][terms]=%s&amp;submit_search=Search',
                            $this->_DC_Subject_id,
                            urlencode($header)
                        )),
                        $header
                    );
                }
                else {
                    $html .= $header;
                }
                $previous_level = $level;
            }
            // Manage last liine.
            $html .= '</li>' . PHP_EOL . str_repeat('</ul></li>' . PHP_EOL, $previous_level);
            // Close the tree.
            $html .= '</ul>';
            $html .= '</div>';
        }

        return $html;
    }
}
