<?php
/**
 *	Popular Clients AJAX file
 *	produces <an optgroup) of most popular clients
 */
 
// Generic site settings
require_once(APPLICATION_PATH . "/inc/settings.inc.php");


echo '<optgroup label="Me">';
echo '<option value="1">Personal project/For me</option>';
echo '</optgroup>';
 
// Get popular clients - based upon number of projects
$query = "
	SELECT c.id, c.title, COUNT( p.id ) AS projects
	FROM `client` c
	LEFT JOIN project p ON p.client = c.id
	WHERE 1
	GROUP BY p.client
	ORDER BY projects DESC, c.title ASC
	LIMIT 0 , 10
";
 

$results = $db->get_results($query, "ARRAY_A");

if(!empty($results)){

	echo '<optgroup label="Popular">';
	foreach($results as $result){
		echo '<option value="' . $result['id'] . '">' . stripslashes($result['title']) . '</option>';
	}
	echo '</optgroup>';
	
}
