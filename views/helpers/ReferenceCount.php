<?php
/**
 * Reference Count helper.
 */

/**
 * @package Reference
 */
class Reference_View_Helper_ReferenceCount extends Zend_View_Helper_Abstract
{
    /**
     * Count the total of distinct element texts for an element.
     *
     * @param Element|integer|string $element Element, element id or slug.
     * @return integer
     */
    public function referenceCount($element)
    {
        if (is_object($element)) {
            $element = $element->id;
        }
        elseif (!is_numeric($element)) {
            $list = json_decode(get_option('reference_list_elements'), true) ?: array('slug' => array());
            $element = array_search($element, $list['slug']);
        }
        $referenceId = $element;

        $db = get_db();
        $elementTextsTable = $db->getTable('ElementText');
        $elementTextsAlias = $elementTextsTable->getTableAlias();
        $select = $elementTextsTable->getSelect()
            ->reset(Zend_Db_Select::COLUMNS)
            ->from(array(), array($elementTextsAlias . '.text'))
            ->joinInner(array('items' => $db->Item), $elementTextsAlias . ".record_type = 'Item' AND items.id = $elementTextsAlias.record_id", array())
            ->where($elementTextsAlias . ".record_type = 'Item'")
            ->where($elementTextsAlias . '.element_id = ' . (integer) $referenceId)
            ->group($elementTextsAlias . '.text');

        $permissions = new Omeka_Db_Select_PublicPermissions('Items');
        $permissions->apply($select, 'items');

        $totalRecords = $db->query($select . " COLLATE 'utf8_unicode_ci'")->rowCount();
        return $totalRecords;
    }
}
