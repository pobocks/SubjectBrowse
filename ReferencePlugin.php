<?php
/**
 * Reference
 *
 * Allows to serve an alphabetized and a hierarchical page of links to searches
 * for all subjects of all items of an Omeka instance.
 *
 * @copyright William Mayo 2011
 * @copyright Copyright Daniel Berthereau, 2014
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Reference
 */

/**
 * The Reference plugin.
 * @package Omeka\Plugins\Reference
 */
class ReferencePlugin extends Omeka_Plugin_AbstractPlugin
{
    const REFERENCE_PATH_LIST = 'references';
    const REFERENCE_PATH_TREE = 'subjects/tree';

    /**
     * @var array Hooks for the plugin.
     */
    protected $_hooks = array(
        'initialize',
        'install',
        'uninstall',
        'config_form',
        'config',
        'define_routes',
    );

    /**
     * @var array Filters for the plugin.
     */
    protected $_filters = array(
        'public_navigation_main',
        'public_navigation_items',
    );

    /**
     * @var array Options and their default values.
     */
    protected $_options = array(
        // 49 is the element id of Dublin Core Subject, forced during install.
        'reference_list_elements' => array(
            'active' => array(49),
            'slug' => array(49 => 'subjects'),
            'label' => array(49 => 'Subjects'),
        ),
        'reference_list_skiplinks' => 1,
        'reference_list_headings' => 1,
        'reference_tree_enabled' => false,
        'reference_tree_expanded' => true,
        'reference_tree_hierarchy' => '',
    );

    /**
     * Add the translations.
     */
    public function hookInitialize()
    {
        add_translation_source(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'languages');
        add_shortcode('reference', array($this, 'shortcodeReference'));
        add_shortcode('subjects', array($this, 'shortcodeSubjects'));
    }

    /**
     * Install the plugin.
     */
    public function hookInstall()
    {
        // Keep old parameters.
        if (plugin_is_active('SubjectBrowse')) {
            if (get_option('subject_browse_list_enabled')) {
                $id = get_option('subject_browse_DC_Subject_id');
                $this->_options['reference_list_elements'] = array(
                   'active' => array($id),
                    'slug' => array($id => 'subjects'),
                    'label' => array($id => __('Subjects')),
                );
            }
            $this->_options['reference_list_skiplinks'] = get_option('subject_browse_list_skiplinks');
            $this->_options['reference_list_headings'] = get_option('subject_browse_list_headings');
            $this->_options['reference_tree_enabled'] = get_option('subject_browse_tree_enabled');
            $this->_options['reference_tree_expanded'] = get_option('subject_browse_tree_expanded');
            $this->_options['reference_tree_hierarchy'] = get_option('subject_browse_tree_hierarchy');
        }
        $this->_options['reference_list_elements'] = json_encode($this->_options['reference_list_elements']);

        $this->_installOptions();
    }

    /**
     * Uninstall the plugin.
     */
    public function hookUninstall()
    {
        $this->_uninstallOptions();
    }

    /**
     * Shows plugin configuration page.
     */
    public function hookConfigForm($args)
    {
        $settings = json_decode(get_option('reference_list_elements'), true) ?: $this->_options['reference_list_elements'];

        $table = get_db()->getTable('Element');
        $select = $table->getSelect()
            ->order('elements.element_set_id')
            ->order('ISNULL(elements.order)')
            ->order('elements.order');
        $elements = $table->fetchObjects($select);

        // Initialize the empty slugs.
        foreach ($elements as $element) {
            if (empty($settings['slug'][$element->id])) {
                $elementSet = $element->getElementSet();
                // By default, no set name for Dublin Core.
                $slug = $elementSet->name == 'Dublin Core'
                    ? $element->name
                    : $elementSet->name . '-' . $element->name;
                $slug = strtolower($slug);
                $slug = str_replace(array(' ', '/'), '-', $slug);
                // Keep only alphanumeric characters, underscores, and dashes.
                $settings['slug'][$element->id] = preg_replace('/[^\w\/-]/i', '', $slug);
            }
            if (empty($settings['label'][$element->id])) {
                $elementSet = $element->getElementSet();
                // By default, no set name for Dublin Core.
                $settings['label'][$element->id] = $elementSet->name == 'Dublin Core'
                    ? $element->name
                    : $elementSet->name . ' : ' . __($element->name);
            }
        }

        $view = $args['view'];
        echo $view->partial(
            'plugins/reference-config-form.php',
            array(
                'settings' => $settings,
                'elements' => $elements,
        ));
    }

    /**
     * Processes the configuration form.
     *
     * @return void
     */
    public function hookConfig($args)
    {
        $post = $args['post'];

        $unique = array_unique($post['reference_list_elements']['slug']);
        if (count($unique) != count($post['reference_list_elements']['slug'])) {
            $msg = __('Some slugs are not single.');
            $msg .= ' ' . __('Changes were reverted.');
            throw new Omeka_Validate_Exception($msg);
        }

        foreach ($this->_options as $optionKey => $optionValue) {
            if (in_array($optionKey, array('reference_list_elements'))) {
               $post[$optionKey] = json_encode($post[$optionKey]) ?: json_encode($optionValue);
            }
            if (isset($post[$optionKey])) {
                set_option($optionKey, $post[$optionKey]);
            }
        }
    }

    /**
     * Defines public routes
     *
     * @return void
     */
    public function hookDefineRoutes($args)
    {
        $args['router']->addConfig(new Zend_Config_Ini(
            dirname(__FILE__) . DIRECTORY_SEPARATOR . 'routes.ini', 'routes'));
    }

    /**
     * Filter for public main navigation.
     *
     * @return nav
     */
    public function filterPublicNavigationMain($nav)
    {
        if (get_option('reference_list_enabled')) {
            $nav[] = array(
                'label'=>__('References'),
                'uri' => url(self::REFERENCE_PATH_LIST),
            );
        }

        if (get_option('reference_tree_enabled')) {
            $nav[] = array(
                'label'=>__('Subjects Tree'),
                'uri' => url(self::REFERENCE_PATH_TREE),
            );
        }

        return $nav;
    }

    /**
     * Filter for public navigation items.
     *
     * @return nav
     */
    public function filterPublicNavigationItems($nav)
    {
        if (get_option('reference_list_elements')) {
            $nav['Browse References'] = array(
                'label'=>__('Browse References'),
                'uri' => url(self::REFERENCE_PATH_LIST),
            );
        }

        if (get_option('reference_tree_enabled')) {
            $nav['Hierarchy of Subjects'] = array(
                'label'=>__('Hierarchy of Subjects'),
                'uri' => url(self::REFERENCE_PATH_TREE),
            );
        }

        return $nav;
    }

    /**
     * Shortcode for adding list of references.
     *
     * @param array $args
     * @param Omeka_View $view
     * @return string
     */
    public function shortcodeReference($args, $view)
    {
        $args['mode'] = 'list';
        return $this->_shortcode($args, $view);
    }

    /**
     * Shortcode for adding tree of subjects.
     *
     * @param array $args
     * @param Omeka_View $view
     * @return string
     */
    public function shortcodeSubjects($args, $view)
    {
        $args['mode'] = 'tree';
        return $this->_shortcode($args, $view);
    }

    /**
     * Helper for shortcodes.
     *
     * @param array $args
     * @param Omeka_View $view
     * @return string
     */
    protected function _shortcode($args, $view)
    {
        $args['view'] = $view;
        $referencesKey = $args['mode'] == 'tree' ? 'subjects' : 'references';
        $references = isset($args[$referencesKey])
            ? array_filter(array_map('trim', explode(',', $args[$referencesKey])))
            : array();
        foreach ($args as &$arg) {
            // Set true values.
            if ($arg == 'true') {
                $arg = true;
            }
            // Set false values.
            elseif ($arg == 'false') {
                $arg = false;
            }
        }
        return $view->reference($references, $args);
    }
}
