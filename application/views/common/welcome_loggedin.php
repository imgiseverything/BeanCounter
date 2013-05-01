<?php

/**
 *	Welcome message view Logged in version
 *
 */
 
?>
<div class="logged-in-status logged-in">
	<div class="inner">
		<p><strong>Hello <a href="/users/edit/<?php echo $objAuthorise->getId(); ?>/"><?php echo $objAuthorise->getName(); ?></a></strong>
		<?php if($objAuthorise->getLevel() == 'Superuser'): ?>
		<a href="/settings/">Edit settings</a>
		<?php endif; ?>
		<a href="/logout/">Log out</a></p>
	</div>
</div>