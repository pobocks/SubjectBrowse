<?php
/**
 * The Subject Browse index controller class.
 *
 * @package SubjectBrowse
 */
class SubjectBrowse_IndexController extends Omeka_Controller_AbstractActionController
{
    public function indexAction()
    {
        // A query allows quick access to all subjects.
        $db = get_db();
        $sql = "
            SELECT DISTINCT text
            FROM $db->ElementTexts
            WHERE element_id = " . get_option('subject_browse_DC_Subject_id') . "
            ORDER BY text;
        ";
        $result = $db->fetchCol($sql);

        $this->view->subjects = $result;
    }
}
