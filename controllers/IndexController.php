<?php
/**
 * The Reference index controller class.
 *
 * @package Reference
 */
class Reference_IndexController extends Omeka_Controller_AbstractActionController
{
    protected $_DC_Title_id = 50;

    public function browseAction()
    {
        $slugs = json_decode(get_option('reference_slugs'), true) ?: array();
        // Remove disabled slugs.
        foreach ($slugs as $slug => $slugData) {
            if (empty($slugData['active'])) {
                unset($slugs[$slug]);
            }
        }
        if (empty($slugs)) {
            throw new Omeka_Controller_Exception_404;
        }
        $this->view->references = $slugs;

        $types = array();
        foreach ($slugs as $slug => $slugData) {
            $types[$slugData['type']] = true;
        }
        $this->view->types = $types;
    }

    /**
     * Alphabet action.
     */
    public function listAction()
    {
        $slugs = json_decode(get_option('reference_slugs'), true) ?: array();
        if (empty($slugs)) {
            $this->forward('browse', 'items', 'default');
        }

        $slug = $this->getParam('slug');
        if (!isset($slugs[$slug]) || empty($slugs[$slug]['active'])) {
            throw new Omeka_Controller_Exception_404;
        }

        $this->view->slug = $slug;
        $this->view->slugData = $slugs[$slug];
        $this->view->references = $this->_getReferencesList($slugs[$slug]);
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
     * When the type is not element, a filter is added and the list of titles
     * are returned.
     *
     * @see Reference_View_Helper_Reference::_getReferencesList()
     * @param array $slug
     * @return array
     */
    protected function _getReferencesList($slug)
    {
        $elementId = $slug['type'] == 'Element' ? $slug['id'] : $this->_DC_Title_id;

        $db = get_db();
        $elementTextsTable = $db->getTable('ElementText');
        $elementTextsAlias = $elementTextsTable->getTableAlias();
        $select = $elementTextsTable->getSelect()
            ->reset(Zend_Db_Select::COLUMNS)
            ->from(array(), array($elementTextsAlias . '.text'))
            ->joinInner(array('items' => $db->Item), $elementTextsAlias . ".record_type = 'Item' AND items.id = $elementTextsAlias.record_id", array())
            ->where("element_texts.record_type = 'Item'")
            ->where($elementTextsAlias . '.element_id = ' . (integer) $elementId)
            ->group($elementTextsAlias . '.text')
            ->order($elementTextsAlias . '.text ASC' . " COLLATE 'utf8_unicode_ci'");

        if ($slug['type'] == 'ItemType') {
            $select->where('items.item_type_id = ' . (integer) $slug['id']);
        }

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
