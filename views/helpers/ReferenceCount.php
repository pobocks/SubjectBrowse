<?php
/**
 * Reference Count helper.
 */

/**
 * @package Reference
 */
class Reference_View_Helper_ReferenceCount extends Zend_View_Helper_Abstract
{
    // This is true in all installations of Omeka (forced).
    protected $_DC_Title_id = 50;

    /**
     * Count the total of distinct element texts for an element.
     *
     * @param string $slug
     * @return integer
     */
    public function referenceCount($slug)
    {
        $slugs = json_decode(get_option('reference_slugs'), true) ?: array();
        if (empty($slugs) || empty($slugs[$slug]['active'])) {
            return;
        }

        $slugData = $slugs[$slug];

        $elementId = $slugData['type'] == 'Element' ? $slugData['id'] : $this->_DC_Title_id;

        $db = get_db();
        $elementTextsTable = $db->getTable('ElementText');
        $elementTextsAlias = $elementTextsTable->getTableAlias();
        $select = $elementTextsTable->getSelect()
            ->reset(Zend_Db_Select::COLUMNS)
            ->from(array(), array($elementTextsAlias . '.text'))
            ->joinInner(array('items' => $db->Item), $elementTextsAlias . ".record_type = 'Item' AND items.id = $elementTextsAlias.record_id", array())
            ->where($elementTextsAlias . ".record_type = 'Item'")
            ->where($elementTextsAlias . '.element_id = ' . (integer) $elementId)
            ->group($elementTextsAlias . '.text');

        if ($slugData['type'] == 'ItemType') {
            $select->where('items.item_type_id = ' . (integer) $slugData['id']);
        }

        $permissions = new Omeka_Db_Select_PublicPermissions('Items');
        $permissions->apply($select, 'items');

        $totalRecords = $db->query($select . " COLLATE 'utf8_unicode_ci'")->rowCount();
        return $totalRecords;
    }
}
