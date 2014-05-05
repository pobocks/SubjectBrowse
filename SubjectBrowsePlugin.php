<?php
/**
 * Subject Browse
 *
 * Allows to serve an alphabetized page of links to searches for all subjects
 * of all items of an Omeka instance.
 *
 * @version $Id$
 * @copyright William Mayo 2011
 * @copyright Copyright Daniel Berthereau, 2014
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package SubjectBrowse
 */

/**
 * The Tagging plugin.
 * @package Omeka\Plugins\Tagging
 */
 class SubjectBrowsePlugin extends Omeka_Plugin_AbstractPlugin
{
    const SUBJECT_BROWSE_PAGE_PATH = 'items/subject-browse';

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
        'subject_browse_DC_id' => 1,
        'subject_browse_DC_Subject_id' => 49,
        'subject_browse_alphabetical_skiplinks' => 1,
        'subject_browse_headers' => 1,
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
        foreach ($post as $key => $value) {
            set_option($key, (integer) (boolean) $value);
        }
    }

    /**
     * Defines public routes
     *
     * @return void
     */
    public function hookDefineRoutes($args)
    {
        $router = $args['router'];
        $router->addRoute(
            'subject_browse_subjectbrowse',
            new Zend_Controller_Router_Route(
                 self::SUBJECT_BROWSE_PAGE_PATH,
                 array(
                    'module' => 'subject-browse',
                    'controller' => 'index',
                    'action' => 'index',
        )));
    }

    /**
     * Filter for public navigation items.
     *
     * @return nav
     */
    public function filterPublicNavigationItems($nav)
    {
        $nav['Browse by Subject'] = array(
            'label'=>__('Browse by Subject'),
            'uri' => url(self::SUBJECT_BROWSE_PAGE_PATH),
        );

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
                url(sprintf('items/browse?search=&advanced[0][element_id]=%s&advanced[0][type]=contains&advanced[0][terms]=%s&submit_search=Search',
                    get_option('subject_browse_DC_Subject_id'),
                    urlencode(html_entity_decode($subject))
                )),
                $subject
            );
        }
        return $subject;
    }
}
