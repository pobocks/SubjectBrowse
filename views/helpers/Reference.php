<?php
/**
 * Reference helper.
 */

/**
 * @package Reference
 */
class Reference_View_Helper_Reference extends Zend_View_Helper_Abstract
{
    // This is true in all installations of Omeka (forced).
    protected $_DC_Subject_id = 49;
    protected $_DC_Title_id = 50;

    /**
     * Get the reference view object.
     *
     * @return This view helper.
     */
    public function reference()
    {
        return $this;
    }

    /**
     * Get the list of references of the slug.
     *
     * @param string $slug
     * @return array Associative array with total and first record ids.
     */
    public function getList($slug)
    {
        $slugs = json_decode(get_option('reference_slugs'), true) ?: array();
        if (empty($slug) || empty($slugs) || empty($slugs[$slug]['active'])) {
            return;
        }
        $references = $this->_getReferencesList($slugs[$slug]);
        return $references;
    }

    /**
     * Get the list of subjects.
     *
     * @return array.
     */
    public function getTree()
    {
        if (!get_option('reference_tree_enabled')) {
            return array();
        }
        $subjects = $this->_getSubjectsTree();
        return $subjects;
    }

    /**
     * Display the list of references via a partial view.
     *
     * @param array $references Array of references elements to show.
     * @param array $options Options to display references. Values are booleans:
     * - raw: Show references as raw text, not links (default to false)
     * - strip: Remove html tags (default to true)
     * - skiplinks: Add the list of letters at top and bottom of the page
     * - headings: Add each letter as headers
     * @return string Html list.
     */
    public function displayList($references, array $options = array())
    {
        $view = $this->view;

        if (empty($references) || empty($options['slug'])) {
            return;
        }

        $options = $this->_cleanOptions($options);

        $slugs = json_decode(get_option('reference_slugs'), true) ?: array();
        $slug = $options['slug'];
        if (empty($slugs) || empty($slugs[$slug]['active'])) {
            return;
        }
        $references = $this->_getReferencesList($slugs[$slug]);

        if ($options['strip']) {
            $total = count($references);
            $referencesList = array_map('strip_formatting', array_keys($references));
            // List of subjects may need to be reordered after reformatting. The
            // total may have been changed. In that case, total of each
            // reference is lost.
            if ($total == count($referencesList)) {
                $references = array_combine($referencesList, $references);
            }
            // Should be done manually.
            else {
                $referenceList = array_combine($referenceList, array_fill(0, count($referenceList), null));
                foreach ($referenceList as $referenceText => &$value) {
                    foreach ($references as $reference => $referenceData) {
                        if (is_null($value)) {
                            $value = $referenceData;
                        }
                        // Keep the first record id.
                        else {
                            $value['count'] += $referenceData['count'];
                        }
                    }
                }
                $references = $referencesList;
            }
            ksort($references , SORT_STRING | SORT_FLAG_CASE);
        }

        $html = $this->view->partial('common/reference-list.php', array(
            'references' => $references,
            'slug' => $slug,
            'slugData' => $slugs[$slug],
            'options' => $options,
        ));

        return $html;
    }

    /**
     * Display the tree of subjects via a partial view.
     *
     * @uses http://www.jqueryscript.net/other/jQuery-Flat-Folder-Tree-Plugin-simplefolders.html
     *
     *  Example for the mode "tree":
     * @example
     * $references = "
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
     * @param array $references Array of subjects elements to show.
     * @param array $options Options to display the references. Values are booleans:
     * - raw: Show subjects as raw text, not links (default to false)
     * - strip: Remove html tags (default to true)
     * - expanded: Show tree as expanded (defaul to config)
     * @return string Html list.
     */
    public function displayTree($subjects, array $options = array())
    {
        $view = $this->view;

        if (empty($subjects)) {
            return;
        }

        $options = $this->_cleanOptions($options);

        if ($options['strip']) {
            $subjects = array_map('strip_formatting', $subjects);
        }

        $html = $this->view->partial('common/reference-tree.php', array(
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
        $mode = isset($options['mode']) && $options['mode'] == 'tree' ? 'tree' : 'list';

        $cleanedOptions = array(
            'mode' => $mode,
            'raw' => isset($options['raw']) && $options['raw'],
            'strip' => isset($options['strip']) ? (boolean) $options['strip'] : true,
        );

        switch ($mode) {
            case 'list':
                $cleanedOptions['headings'] = (boolean) (isset($options['headings'])
                    ? $options['headings']
                    : get_option('reference_list_headings'));
                $cleanedOptions['skiplinks'] = (boolean) (isset($options['skiplinks'])
                    ? $options['skiplinks']
                    : get_option('reference_list_skiplinks'));
                $cleanedOptions['slug'] = empty($options['slug'])
                    ? $this->_DC_Subject_id
                    : $options['slug'];
                break;

            case 'tree':
                $cleanedOptions['expanded'] = (boolean) (isset($options['expanded'])
                    ? $options['expanded']
                    : get_option('reference_tree_expanded'));
                break;
        }

        return $cleanedOptions;
    }

    /**
     * Get the list of references, the total for each one and the first item.
     *
     * When the type is not an element, a filter is added and the list of titles
     * are returned.
     *
     * @param array $slugData
     * @return array Associative list of references, with the total and the
     * first record.
     */
    protected function _getReferencesList($slugData)
    {
        $elementId = $slugData['type'] == 'Element' ? $slugData['id'] : $this->_DC_Title_id;

        $db = get_db();
        $elementTextsTable = $db->getTable('ElementText');
        $elementTextsAlias = $elementTextsTable->getTableAlias();
        $select = $elementTextsTable->getSelect()
            ->reset(Zend_Db_Select::COLUMNS)
            ->from(array(), array(
                'text' => $elementTextsAlias . '.text',
                'count' => 'COUNT(*)',
                'record_id' => $elementTextsAlias . '.record_id',
            ))
            ->joinInner(array('items' => $db->Item), $elementTextsAlias . ".record_type = 'Item' AND items.id = $elementTextsAlias.record_id", array())
            ->where($elementTextsAlias . ".record_type = 'Item'")
            ->where($elementTextsAlias . '.element_id = ' . (integer) $elementId)
            ->group($elementTextsAlias . '.text')
            ->order($elementTextsAlias . '.text ASC' . " COLLATE 'utf8_unicode_ci'")
            ->order($elementTextsAlias . '.record_id ASC');

        if ($slugData['type'] == 'ItemType') {
            $select->where('items.item_type_id = ' . (integer) $slugData['id']);
        }

        $permissions = new Omeka_Db_Select_PublicPermissions('Items');
        $permissions->apply($select, 'items');

        $result = $db->fetchAssoc($select);
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
