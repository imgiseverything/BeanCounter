<?php
	/**
	 *	Footer template file
	 *	Basic HTML that is used on the bottom of every page 
	 *	(on normal - full screen mode)
	 */
?>
		</div>
	</div>
	<footer id="Footer" class="group">
		<div class="inner">
			<small><?php echo $objTemplate->getCopyright(); ?></small>
	    </div>
	</footer>
</div>
<?php echo $objTemplate->getBehaviour(); ?>
<?php echo $objTemplate->getStats(); ?>
<?php echo $objTemplate->getSpeed(microtime()); ?>
</body>
</html>