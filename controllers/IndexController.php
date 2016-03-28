<?php
/**
 * The Subject Browse index controller class.
 *
 * @package SubjectBrowse
 */
class SubjectBrowse_IndexController extends Omeka_Controller_AbstractActionController
{
    /**
     * Index action.
     */
    public function indexAction()
    {
        // Default action is to browse by subject alphabetically.
        $this->_forward('list');
    }

    /**
     * Alphabet action.
     */
    public function listAction()
    {
        if (get_option('subject_browse_list_enabled')) {
            $this->_list();
        }
        else {
            $this->forward('browse', 'items', 'default');
        }
    }

    /**
     * Hierarchy action.
     */
    public function treeAction()
    {
        if (get_option('subject_browse_tree_enabled')) {
            $this->_tree();
        }
        else {
            $this->forward('browse', 'items', 'default');
        }
    }

    /**
     * Prepare the list view.
     */
    protected function _list()
    {
        // A query allows quick access to all subjects (no need for elements).
        $dcSubjectId = (integer) get_option('subject_browse_DC_Subject_id');
        $db = get_db();
        $sql = "
            SELECT DISTINCT `text`
            FROM `$db->ElementTexts`
            WHERE `element_id` = $dcSubjectId
            ORDER BY `text`
            COLLATE 'utf8_unicode_ci'
        ";
        $result = $db->fetchCol($sql);

        $this->view->subjects = $result;
    }

    /**
     * Prepare the tree view.
     */
    protected function _tree()
    {
        $subjects = get_option('subject_browse_tree_hierarchy');
        $subjects = array_filter(explode(PHP_EOL, $subjects));

        $this->view->subjects = $subjects;
    }
}
