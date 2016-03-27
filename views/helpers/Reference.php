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
     * Display the references list or the tree of subjects via a partial view.
     *
     * For the mode "list", the references come from the items and are listed in
     * alphabetical order.
     * For the mode "tree", the subjects come from the configuration and an
     * interactive hierarchical list is build with javascript .
     *
     * @uses http://www.jqueryscript.net/other/jQuery-Flat-Folder-Tree-Plugin-simplefolders.html
     *
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
     * @param array $references Array of references or subjects elements to
     * show, if any, else default ones.
     * @param array $options Options to display the references. They depend on
     * the key "mode" ("list" (default) or "tree"). Values are booleans:
     * - raw: Show references as raw text, not links (default to false)
     * - strip: Remove html tags (default to true)
     * - skiplinks: Add the list of letters at top and bottom of the page
     * - headings: Add each letter as headers
     * For "tree"
     * - raw: Show subjects as raw text, not links (default to false)
     * - strip: Remove html tags (default to true)
     * - expanded: Show tree as expanded (defaul to config)
     *
     * @return string Html list.
     */
    public function reference($references = array(), array $options = array())
    {
        $view = $this->view;

        $options = $this->_cleanOptions($options);
        $options['references'] = $references;

        switch ($options['mode']) {
            case 'tree':
                if (get_option('reference_tree_enabled')) {
                    return $this->_tree($options);
                }
                break;

            case 'list':
            default:
                return $this->_list($options);
        }
    }

    public function _list($options)
    {
        $slugs = json_decode(get_option('reference_slugs'), true) ?: array();
        $references = $options['references'];

        $slug = $options['slug'];
        if (empty($slugs) || empty($slugs[$slug]['active'])) {
            return;
        }

        if (empty($references)) {
            $references = $this->_getReferencesList($slugs[$slug]);
        }
         unset($options['references']);

        if ($options['strip']) {
            $references = array_map('strip_formatting', $references);
            // List of subjects may need to be reordered after reformatting.
            natcasesort($references);
            $references = array_unique($references);
        }

        $html = $this->view->partial('common/reference-list.php', array(
            'references' => $references,
            'slug' => $slug,
            'slugData' => $slugs[$slug],
            'options' => $options,
        ));

        return $html;
    }

    public function _tree($options)
    {
        $subjects = $options['references'] ?: $this->_getSubjectsTree();
        unset($options['references']);

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
     * Get the list of references.
     *
     * When the type is not element, a filter is added and the list of titles
     * are returned.
     *
     * @see Reference_IndexController::_getReferencesList()
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
