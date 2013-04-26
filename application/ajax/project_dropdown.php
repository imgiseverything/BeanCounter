<?php
/**
 * Project AJAX file
 * produces <options> of projects from database for easy AJAX adding to forms
 */
 // generic site settings
 require_once(APPLICATION_PATH . "/inc/settings.inc.php");
 // what is the current selected option?
 require_once(APPLICATION_PATH . "/inc/settings.inc.php");
 $current = $db->get_var("SELECT `id` FROM `project` ORDER BY `date_added` DESC LIMIT 1;");
 // create drop down menu
 echo drawDropDown(getDropDownOptions('project'), $current);
?>