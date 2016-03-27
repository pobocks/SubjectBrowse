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
        $elementId = (integer) $element;

        $db = get_db();
        $select = $db->getTable('ElementText')
            ->getSelect()
            ->where("element_texts.record_type = 'Item'")
            ->where('element_texts.element_id = ' . $elementId)
            ->group('element_texts.text');
        $totalRecords = $db->query($select)->rowCount();
        return $totalRecords;
    }
}
