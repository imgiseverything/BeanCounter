<?php
/**
 * Project AJAX file
 * produces <options> of projects from database for easy AJAX adding to forms
 */
 
// generic site settings
require_once(APPLICATION_PATH . '/inc/settings.inc.php');
// what is the current selected option?
$client = $objApplication->getParameter('client');
$project = $objApplication->getParameter('project');
$current = $db->get_var("SELECT `id` FROM `project` WHERE `id` = '{$project}' ORDER BY date_added DESC LIMIT 1;");
// create drop down menu
$objApplication->setFilter('client', $client);
$objApplication->setFilter('per_page', 1000);
$objApplication->setFilter('project_stage', array(1, 2, 3, 4));
$objProject = new Project($db, $objApplication->getFilters(), false);
$projects = $objProject->getProperties();

if(!empty($projects)){
 foreach($projects as $option){
 	$selected = ($current == $option['id']) ? ' selected="selected"' : ''; 
 	echo '<option value="' . $option['id'] . '"' . $selected . '>' . $option['title'] . '</option>';
 }
} else{
	echo '<option value="">There are no projects for this client - please add a new project</option>';
}
