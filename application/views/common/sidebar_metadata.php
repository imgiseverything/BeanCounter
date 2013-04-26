<?php

/**
 *	Sidebar metadata
 *	Common data show int eh sideabar when viewing an
 *	individual item
 */

?>
<div id="SecondaryContent">
	<h2>Relevant dates</h2>
	<p>Date added: <?php echo DateFormat::getDate('date', $date_added); ?> <em><?php echo DateFormat::howManyDays($date_added); ?> ago</em><br />
Date edited: <?php echo (!empty($date_edited)) ? DateFormat::getDate('date', $properties['date_edited']) . ' <em>' . DateFormat::howManyDays($date_edited) . ' ago</em>' : 'N/A'; ?></p>
	<div id="Options">
		<h2>Options</h2>
		<ul>
			<li><a href="<?php echo $objScaffold->getFolder(); ?>">View all <?php echo $objScaffold->getNamePlural(); ?></a></li>
			<li><a href="<?php echo $objScaffold->getFolder(); ?>edit/<?php echo $objScaffold->getId(); ?>/" class="edit">Edit this <?php echo $objScaffold->getName(); ?></a></li>
			<li><a href="<?php echo $objScaffold->getFolder(); ?>duplicate/<?php echo $objScaffold->getId(); ?>/" class="edit">Duplicate this <?php echo $objScaffold->getName(); ?></a></li>
			<li><a href="<?php echo $objScaffold->getFolder(); ?>delete/<?php echo $objScaffold->getId(); ?>/" class="negative">Delete this <?php echo $objScaffold->getName(); ?></a></li>
		</ul>
	</div>
</div>