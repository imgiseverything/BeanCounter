<?php

	// We don't want people viewing a list of users so send them to the 404 page instead
	// THis page isnb't linked anywhere so it's not a big issue
	include(APPLICATION_PATH . "/views/errors.php");
	exit;
