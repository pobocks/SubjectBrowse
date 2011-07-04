<?php
class Subjectbrowse_IndexController extends Omeka_Controller_Action
{    	
	public function indexAction()
	{
          $db = get_db();
          $select = "SELECT DISTINCT text
                     FROM " . $db->ElementTexts . "
                     WHERE element_id='" . get_option('subject_browse_DC_Subject_id') . "'
                     ORDER BY text;";
          $result = $db->fetchAll($select);
          $i = 0;
          foreach ($result as $row){
            $subjects[$i] = $row['text'];
            $i++;
          }
          $this->view->subjects = $subjects;
	}
}
