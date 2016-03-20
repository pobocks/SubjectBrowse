<?php
/**
 * Subject Browse
 *
 * Allows to serve an alphabetized and a hierarchical page of links to searches
 * for all subjects of all items of an Omeka instance.
 *
 * @copyright William Mayo 2011
 * @copyright Copyright Daniel Berthereau, 2014
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package SubjectBrowse
 */

/**
 * The Subject Browse plugin.
 * @package Omeka\Plugins\SubjectBrowse
 */
class SubjectBrowsePlugin extends Omeka_Plugin_AbstractPlugin
{
    const SUBJECT_BROWSE_PATH_LIST = 'subjects/list';
    const SUBJECT_BROWSE_PATH_TREE = 'subjects/tree';

    /**
     * @var array Hooks for the plugin.
     */
    protected $_hooks = array(
        'initialize',
        'install',
        'upgrade',
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
        'subject_browse_DC_Subject_id' => 49,
        'subject_browse_list_enabled' => true,
        'subject_browse_list_skiplinks' => 1,
        'subject_browse_list_headings' => 1,
        'subject_browse_tree_enabled' => false,
        'subject_browse_tree_expanded' => true,
        'subject_browse_tree_hierarchy' => '',
    );

    /**
     * Add the translations.
     */
    public function hookInitialize()
    {
        add_translation_source(dirname(__FILE__) . '/languages');
        if (version_compare(OMEKA_VERSION, '2.2', '>=')) {
            add_shortcode('subjects', array($this, 'shortcodeSubjects'));
        }
    }

    /**
     * Install the plugin.
     */
    public function hookInstall()
    {
        // Get the 'id' key values for the Dublin Core element set and Subject
        // element. Currently only Subject is used.
        $element = $this->_db->getTable('Element')->findByElementSetNameAndElementName('Dublin Core', 'Subject');
        $this->_options['subject_browse_DC_Subject_id'] = $element->id;

        $this->_installOptions();
    }

    /**
     * Upgrades the plugin.
     */
    public function hookUpgrade($args)
    {
        $oldVersion = $args['old_version'];
        $newVersion = $args['new_version'];

        if (version_compare($oldVersion, '2.1', '<')) {
            delete_option('subject_browse_DC_id');
            delete_option('subject_browse_item_links');
            set_option('subject_browse_expanded', get_option('subject_browse_expanded_tree'));
            delete_option('subject_browse_expanded_tree');
        }

        if (version_compare($oldVersion, '2.2', '<')) {
            delete_option('subject_browse_item_links');
            set_option('subject_browse_list_enabled', get_option('subject_browse_enable_list'));
            delete_option('subject_browse_enable_list');
            set_option('subject_browse_list_skiplinks', get_option('subject_browse_alphabetical_skiplinks'));
            delete_option('subject_browse_alphabetical_skiplinks');
            set_option('subject_browse_list_headings', get_option('subject_browse_headers'));
            delete_option('subject_browse_headers');
            set_option('subject_browse_tree_enabled', get_option('subject_browse_enable_tree'));
            delete_option('subject_browse_enable_tree');
            set_option('subject_browse_tree_expanded', get_option('subject_browse_expanded'));
            delete_option('subject_browse_expanded');
            set_option('subject_browse_tree_hierarchy', get_option('subject_browse_hierarchy'));
            delete_option('subject_browse_hierarchy');
        }
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
        $view = $args['view'];
        echo $view->partial(
            'plugins/subject-browse-config-form.php'
        );
    }

    /**
     * Processes the configuration form.
     *
     * @return void
     */
    public function hookConfig($args)
    {
        $post = $args['post'];
        foreach ($this->_options as $optionKey => $optionValue) {
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
        $args['router']->addConfig(new Zend_Config_Ini(dirname(__FILE__) . '/routes.ini', 'routes'));
    }

    /**
     * Filter for public main navigation.
     *
     * @return nav
     */
    public function filterPublicNavigationMain($nav)
    {
        if (get_option('subject_browse_list_enabled')) {
            $nav[] = array(
                'label'=>__('Subjects List'),
                'uri' => url(self::SUBJECT_BROWSE_PATH_LIST),
            );
        }

        if (get_option('subject_browse_tree_enabled')) {
            $nav[] = array(
                'label'=>__('Subjects Tree'),
                'uri' => url(self::SUBJECT_BROWSE_PATH_TREE),
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
        if (get_option('subject_browse_list_enabled')) {
            $nav['Browse by Subject'] = array(
                'label'=>__('Browse by Subject'),
                'uri' => url(self::SUBJECT_BROWSE_PATH_LIST),
            );
        }

        if (get_option('subject_browse_tree_enabled')) {
            $nav['Hierarchy of Subjects'] = array(
                'label'=>__('Hierarchy of Subjects'),
                'uri' => url(self::SUBJECT_BROWSE_PATH_TREE),
            );
        }

        return $nav;
    }

    /**
     * Shortcode for adding list or tree of subjects.
     *
     * @param array $args
     * @param Omeka_View $view
     * @return string
     */
    public function shortcodeSubjects($args, $view)
    {
        $args['view'] = $view;
        $subjects = isset($args['subjects'])
            ? array_filter(array_map('trim', explode(',', $args['subjects'])))
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
        return $view->subjectBrowse($subjects, $args);
    }
}
