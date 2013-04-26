<?php
/**
 * Invoice Number Generator
 * creates an invoice number based on the last most recent one in the database
 */
 // generic site settings
 require_once(APPLICATION_PATH."/inc/settings.inc.php");
 // what was the last invoice number?
 $last = $db->get_var("SELECT internal_reference_number FROM project WHERE internal_reference_number IS NOT NULL ORDER BY date_added DESC LIMIT 1;");
 // echo new invoice number
 echo sprintf("%04d",((int)$last+1));
 
?>