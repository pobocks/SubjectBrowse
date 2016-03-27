<?php
/**
 * The Reference index controller class.
 *
 * @package Reference
 */
class Reference_IndexController extends Omeka_Controller_AbstractActionController
{
    public function browseAction()
    {
        $list = json_decode(get_option('reference_list_elements'), true);
        if (empty($list['active'])) {
            throw new Omeka_Controller_Exception_404;
        }

        $references = array_combine(
            array_intersect_key($list['slug'], $list['active']),
            array_intersect_key($list['label'], $list['active'])
        );

        $this->view->references = $references;
    }

    /**
     * Alphabet action.
     */
    public function listAction()
    {
        $list = json_decode(get_option('reference_list_elements'), true);
        if (empty($list)) {
            $this->forward('browse', 'items', 'default');
        }

        $slug = $this->getParam('slug');
        $referenceId = is_numeric($slug)
            ? (integer) $slug
            : array_search($slug, $list['slug']);
        // Check if this slug is allowed.
        if (empty($list['active'][$referenceId])) {
            $this->forward('browse', 'index', 'reference');
        }

        $references = $this->_getReferencesList($referenceId);
        $this->view->references = $references;
        $this->view->referenceId = $referenceId;
        $this->view->referenceSlug = $list['slug'][$referenceId];
        $this->view->referenceLabel = $list['label'][$referenceId];
    }

    /**
     * Hierarchy action.
     */
    public function treeAction()
    {
        if (!get_option('reference_tree_enabled')) {
            $this->forward('browse', 'items', 'default');
        }

        $this->view->subjects = $this->_getSubjectsTree();
    }

    /**
     * Get the list of references.
     *
     * @see Reference_View_Helper_Reference::_getReferencesList()
     * @param integer $referenceId
     * @return array
     */
    protected function _getReferencesList($referenceId)
    {
        // A query allows quick access to all subjects (no need for elements).
        $db = get_db();
        $elementTextsTable = $db->getTable('ElementText');
        $elementTextsAlias = $elementTextsTable->getTableAlias();
        $select = $elementTextsTable->getSelect()
            ->reset(Zend_Db_Select::COLUMNS)
            ->from(array(), array($elementTextsAlias . '.text'))
            ->joinInner(array('items' => $db->Item), $elementTextsAlias . ".record_type = 'Item' AND items.id = $elementTextsAlias.record_id", array())
            ->where("element_texts.record_type = 'Item'")
            ->where($elementTextsAlias . '.element_id = ' . (integer) $referenceId)
            ->group($elementTextsAlias . '.text')
            ->order($elementTextsAlias . '.text ASC' . " COLLATE 'utf8_unicode_ci'");

        $permissions = new Omeka_Db_Select_PublicPermissions('Items');
        $permissions->apply($select, 'items');

        $result = $db->fetchCol($select);
        return $result;
    }

    /**
     * Get the dafault tree of subjects.
     */
    protected function _getSubjectsTree()
    {
        $subjects = get_option('reference_tree_hierarchy');
        $subjects = array_filter(explode(PHP_EOL, $subjects));

        return $subjects;
    }
}
