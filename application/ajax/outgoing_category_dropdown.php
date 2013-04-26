<?php
/**
 * Outgoing categories AJAX file
 * produces <options> of outgoing categories from database for easy AJAX adding to forms
 */
 // generic site settings
 require_once(APPLICATION_PATH."/inc/settings.inc.php");
 // what is the new option?
 $new = $db->get_row("SELECT id, title FROM outgoing_category ORDER BY date_added DESC LIMIT 1;", "ARRAY_A");
 // create drop down menu
 echo drawDropDown(array($new['id'] => $new['title']), $new['id']);
?>