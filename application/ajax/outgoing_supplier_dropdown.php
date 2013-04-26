<?php
/**
 * Outgoing supplier AJAX file
 * produces <options> of outgoing suppliers from database for easy AJAX adding to forms
 */
 // generic site settings
 require_once(APPLICATION_PATH . "/inc/settings.inc.php");
 // what is the new option?
 $new = $db->get_row("SELECT id, title FROM outgoing_supplier ORDER BY date_added DESC LIMIT 1;", "ARRAY_A");
 // create drop down menu
 echo drawDropDown(array($new['id'] => $new['title']), $new['id']);

?>