<?php
/**
 * @version $Id$
 * @copyright William Mayo 2011
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package SubjectBrowse
 */

  //Define Constants
define('SUBJECT_BROWSE_PAGE_PATH', 'items/subject-browse/');
//Get the Dublin Core ID to use in generating search links
      $db = get_db();
      $select = "SELECT e.id
                 FROM " . $db->Elements . " e
                 JOIN (SELECT id
                       FROM ". $db->ElementSets . "
                       WHERE name='Dublin Core') es
                 ON es.id=e.element_set_id
                 WHERE e.name = 'Subject'";
      $result = $db->fetchAll($select);
define('SUBJECT_BROWSE_DC_ID', $result[0]['id']);

// Add plugin hooks.
add_plugin_hook('install', 'subject_browse_install');
add_plugin_hook('uninstall', 'subject_browse_uninstall');
add_plugin_hook('define_routes', 'subject_browse_define_routes');

// Add filters.
add_filter('public_navigation_items', 'subject_browse_public_navigation_items');

function subject_browse_install()
{
	
}

function subject_browse_uninstall()
{
}

function subject_browse_define_routes($router)
{   
	$router->addRoute(
	    'subject_browse_subjectbrowse', 
	    new Zend_Controller_Router_Route(
                                             SUBJECT_BROWSE_PAGE_PATH,
                                             array('module'       => 'subject-browse')
	    )
	);
}

function subject_browse_public_navigation_items($nav)
{
  $nav['Browse by Subject'] = uri('items/subject-browse');
  return $nav;
}

