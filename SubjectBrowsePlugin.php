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
        'public_navigation_items',
        'displayItemDublinCoreSubject' => array('Display', 'Item', 'Dublin Core', 'Subject'),
    );

    /**
     * @var array Options and their default values.
     */
    protected $_options = array(
        'subject_browse_enable_list' => true,
        'subject_browse_DC_id' => 1,
        'subject_browse_DC_Subject_id' => 49,
        'subject_browse_alphabetical_skiplinks' => 1,
        'subject_browse_headers' => 1,
        'subject_browse_enable_tree' => false,
        'subject_browse_expanded_tree' => true,
        'subject_browse_hierarchy' => '',
        'subject_browse_item_links' => 1,
    );

    /**
     * Install the plugin.
     */
    public function hookInstall()
    {
        // Get the 'id' key values for the Dublin Core element set and Subject
        // element. Currently only Subject is used.
        $element = $this->_db->getTable('Element')->findByElementSetNameAndElementName('Dublin Core', 'Subject');
        $this->_options['subject_browse_DC_id'] = $element->element_set_id;
        $this->_options['subject_browse_DC_Subject_id'] = $element->id;

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
     *
     * @return void
     */
    public function hookConfigForm()
    {
        echo get_view()->partial(
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
        // Options are already cleaned via Zend.
        foreach ($post as $key => $value) {
            set_option($key, $value);
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
     * Filter for public navigation items.
     *
     * @return nav
     */
    public function filterPublicNavigationItems($nav)
    {
        if (get_option('subject_browse_enable_list')) {
            $nav['Browse by Subject'] = array(
                'label'=>__('Browse by Subject'),
                'uri' => url(self::SUBJECT_BROWSE_PATH_LIST),
            );
        }

        if (get_option('subject_browse_enable_tree')) {
            $nav['Hierarchy of Subjects'] = array(
                'label'=>__('Hierarchy of Subjects'),
                'uri' => url(self::SUBJECT_BROWSE_PATH_TREE),
            );
        }

        return $nav;
    }

    /**
     * Filter for item links.
     *
     * This filter is used only if it is set in config form.
     *
     * @return nav
     */
    public function displayItemDublinCoreSubject($subject)
    {
        if (get_option('subject_browse_item_links')) {
            $subject = sprintf('<a href="%s">%s</a>',
                url(sprintf('items/browse?search=&amp;advanced[0][element_id]=%s&amp;advanced[0][type]=contains&amp;advanced[0][terms]=%s&amp;submit_search=Search',
                    get_option('subject_browse_DC_Subject_id'),
                    urlencode(html_entity_decode($subject))
                )),
                $subject
            );
        }
        return $subject;
    }
}
