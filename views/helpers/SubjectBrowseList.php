<?php
/**
 * Subject Browse List helper.
 */

/**
 * @package SubjectBrowse
 */
class SubjectBrowse_View_Helper_SubjectBrowseList extends Zend_View_Helper_Abstract
{
    protected $_DC_Subject_id = 49;

    /**
     * Display the list of subjects.
     *
     * @param array $subjects Array of Subjects elements to show, if any, else
     * default ones
     * @param array $options Options can be these booleans (defaul to config):
     * - linked: Show subjects as links (default to true)
     * - headers: Add headers to list view
     * - skiplinks_top: Add the list of letters to list view
     * - skiplinks_bottom: Add the list of letters to list view
     *
     * @return string Html list.
     */
    public function subjectBrowseList($subjects = null, array $options = array())
    {
        $this->_DC_Subject_id = get_option('subject_browse_DC_Subject_id');

        $options = $this->_cleanOptions($options);

        if (empty($subjects)) {
            $subjects = $this->_getSubjectsList();
        }

        $html = $this->_subjectsList($subjects, $options);

        return $html;
    }

    /**
     * Get list of options.
     */
    protected function _cleanOptions($options)
    {
        // Get list of options.
        $linked = (boolean) (isset($options['linked']) ? $options['linked'] : true);
        $headers = (boolean) (isset($options['headers'])
            ? $options['headers']
            : get_option('subject_browse_headers'));
        $skiplinks_top = (boolean) (isset($options['skiplinks_top'])
            ? $options['skiplinks_top']
            : get_option('subject_browse_alphabetical_skiplinks'));
        $skiplinks_bottom = (boolean) (isset($options['skiplinks_bottom'])
            ? $options['skiplinks_bottom']
            : get_option('subject_browse_alphabetical_skiplinks'));

        return array(
            'linked' => $linked,
            'headers' => $headers,
            'skiplinks_top' => $skiplinks_top,
            'skiplinks_bottom' => $skiplinks_bottom,
        );
    }

    /**
     * Get the dafault list of subjects.
     */
    protected function _getSubjectsList()
    {
        // A query allows quick access to all subjects (no need for elements).
        $db = get_db();
        $sql = "
            SELECT DISTINCT text
            FROM $db->ElementTexts
            WHERE element_id = " . $this->_DC_Subject_id . "
            ORDER BY text;
        ";
        $result = $db->fetchCol($sql);

        return $result;
    }

    /**
     * Convert a list of subjects objects into a html list of subjects.
     *
     * @param array $subjects
     * @param boolean $options See subjectBrowse()
     *
     * @return string Html lists, the first list with the class "tree".
     */
    protected function _subjectsList($subjects, $options)
    {
        $html = '';

        if (count($subjects)) {
             if ($options['skiplinks_top']) {
                 $html .= '
    <div class="pagination sb-pagination" id="pagination-top">
        <ul class="pagination_list">
            <li class="pagination_range"><a href="#number">#0-9</a></li>';
            $pagination_list = '';
            foreach (range('A', 'Z') as $i) {
                $pagination_list .= sprintf('<li class="pagination_range"><a href="#%s">%s</a></li>', $i, $i);
            }
            $html .= $pagination_list;
            $html .= '
        </ul>
    </div>' . PHP_EOL;
             }

            $html .= '<div id="sb-subject-headings">';
            $current_header = '';
            foreach ($subjects as $header) {
                $first_char = substr($header, 0, 1);
                if (preg_match('/\W|\d/', $first_char)) {
                    $first_char = '#0-9';
                }
                if ($current_header !== strtoupper($first_char)) {
                    $current_header = strtoupper($first_char);
                    if ($options['headers']) {
                        if ($current_header === '#0-9') {
                            $html .= "<h3 class='sb-subject-heading' id='number'>$current_header</h3>";
                        }
                        else {
                            $html .= "<h3 class='sb-subject-heading' id='$current_header'>$current_header</h3>";
                        }
                    }
                }
                // Get the line.
                if ($options['linked']) {
                    $html .= sprintf('<p class="sb-subject"><a href="%s">%s</a></p>',
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
            }

             if ($options['skiplinks_bottom']) {
                 $html .= PHP_EOL . '
    <div class="pagination sb-pagination" id="pagination-bottom">
        <ul class="pagination_list">
            <li class="pagination_range"><a href="#number">#0-9</a></li>';
            if (!isset($pagination_list)) {
                $pagination_list = '';
                foreach (range('A', 'Z') as $i) {
                    $pagination_list .= sprintf('<li class="pagination_range"><a href="#%s">%s</a></li>', $i, $i);
                }
            }
            $html .= $pagination_list;
            $html .= '
        </ul>
    </div>' . PHP_EOL;
             }
        }

        return $html;
    }
}
