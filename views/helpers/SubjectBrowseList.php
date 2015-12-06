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
        $view = $this->view;

        $this->_DC_Subject_id = (integer) get_option('subject_browse_DC_Subject_id');

        $options = $this->_cleanOptions($options);

        if (empty($subjects)) {
            $subjects = $this->_getSubjectsList();
        }

        $html = $view->partial('common/subject-browse-list.php', array(
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
            WHERE element_id = {$this->_DC_Subject_id}
            ORDER BY text;
        ";
        $result = $db->fetchCol($sql);

        return $result;
    }
}
