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
                if (get_option('reference_list_elements')) {
                    return $this->_list($options);
                }
                break;
        }
    }

    public function _list($options)
    {
        $list = json_decode(get_option('reference_list_elements'), true);
        $references = $options['references'];

        $slug = $options['slug'];
        $referenceId = is_numeric($slug)
            ? (integer) $slug
            : array_search($slug, $list['slug']);
        // Check if this slug is allowed.
        if (empty($list['active'][$referenceId])) {
            return;
        }
        if (empty($references)) {
            $references = $this->_getReferencesList($referenceId);
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
            'referenceId' => $referenceId,
            'referenceSlug' => $list['slug'][$referenceId],
            'referenceLabel' => $list['label'][$referenceId],
            'options' => $options,
        ));

        return $html;
    }

    public function _tree($options)
    {
        $list = json_decode(get_option('reference_list_elements'), true);
        $subjects = $options['references'] ?: $this->_getSubjectsTree();
        unset($options['references']);

        if ($options['strip']) {
            $subjects = array_map('strip_formatting', $subjects);
        }

        $referenceId = $this->_DC_Subject_id;
        $html = $this->view->partial('common/reference-tree.php', array(
            'subjects' => $subjects,
            'referenceId' => $referenceId,
            'referenceSlug' => $list['slug'][$referenceId],
            'referenceLabel' => $list['label'][$referenceId],
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
     */
    protected function _getReferencesList($referenceId)
    {
        // A query allows quick access to all subjects (no need for elements).
        $db = get_db();
        $sql = "
            SELECT DISTINCT `text`
            FROM `$db->ElementTexts`
            WHERE `record_type` = 'Item'
                AND`element_id` = '$referenceId'
            ORDER BY `text`
            COLLATE 'utf8_unicode_ci'
        ";
        $result = $db->fetchCol($sql);

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
