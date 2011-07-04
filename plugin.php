<?php
/**
 * @version $Id$
 * @copyright William Mayo 2011
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package SubjectBrowse
 */

//Define Constants
define('SUBJECT_BROWSE_PAGE_PATH', 'items/subject-browse/');

// Add plugin hooks.
add_plugin_hook('install', 'subject_browse_install');
add_plugin_hook('uninstall', 'subject_browse_uninstall');
add_plugin_hook('define_routes', 'subject_browse_define_routes');
add_plugin_hook('config', 'subject_browse_config');
add_plugin_hook('config_form', 'subject_browse_config_form');

// Add filters.
add_filter('public_navigation_items', 'subject_browse_public_navigation_items');

function subject_browse_install()
{
  // Get the 'id' key values for the Dublin Core element set and Subject element.  Currently only Subject is used. 
  $db = get_db();
  $select = "SELECT id
             FROM ". $db->ElementSets . "
             WHERE name='Dublin Core';";
  $result = $db->fetchAll($select);
  set_option('subject_browse_DC_id', $result[0]['id'] );
  $select = "SELECT id FROM " . $db->Elements . "
             WHERE name='Subject' AND element_set_id='" . get_option('subject_browse_DC_id') . "';";
  $result = $db->fetchAll($select);
  set_option('subject_browse_DC_Subject_id', $result[0]['id']);
  set_option('subject_browse_alphabetical_skiplinks', 1);
  set_option('subject_browse_headers', 1);
}

function subject_browse_uninstall()
{
  delete_option('subject_browse_DC_id');
  delete_option('subject_browse_DC_Subect_id');
  delete_option('subject_browse_alphabetical_helpers');
  delete_option('subject_browse_headers');
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

function subject_browse_config(){
  set_option('subject_browse_alphabetical_skiplinks', $_POST['alphabetical_skiplinks']);
  set_option('subject_browse_headers', $_POST['headers']);
}

function subject_browse_config_form(){
  include 'config_form.php';
}

function subject_browse_public_navigation_items($nav)
{
  $nav['Browse by Subject'] = uri(SUBJECT_BROWSE_PAGE_PATH);
  return $nav;
}
