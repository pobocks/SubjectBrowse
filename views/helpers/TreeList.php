<?php
/**
 * Subject Browse Tree List.
 */

/**
 * @package SubjectBrowse
 */
class SubjectBrowse_View_Helper_TreeList extends Zend_View_Helper_Abstract
{
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
     * @param boolean $expanded Default to opened / closed tree.
     * @param boolean $link Make links or print only subject name.
     *
     * @return string Html lists, the first list with the class "tree".
     */
    function treeList($subjects, $expanded = null, $link = true)
    {
        $html = '';

        if (is_null($expanded)) {
            $expanded = (boolean) get_option('subject_browse_expanded_tree');
        }

        if (count($subjects)) {
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
                    $html .= PHP_EOL . '<div class="expander' . ($expanded ? ' expanded' : '') . '"></div><ul' . ($expanded ? ' class="expanded"' : '') . '><li>';
                }
                else {
                    $html .= '<li>';
                }

                // Get the line.
                if ($link) {
                    $html .= sprintf('<a href="%s">%s</a>',
                        url(sprintf('items/browse?search=&amp;advanced[0][element_id]=%s&amp;advanced[0][type]=contains&amp;advanced[0][terms]=%s&amp;submit_search=Search',
                            get_option('subject_browse_DC_Subject_id'),
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
        }

        return $html;
    }
}
