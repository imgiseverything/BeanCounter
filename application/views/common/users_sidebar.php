<?php
	
/**
 *	Users sidebar
 */

?>
<div class="content-secondary">
	<form id="filterForm" method="get" action="<?php echo $objScaffold->getFolder(); ?>" class="hidden">
			<fieldset>
				<legend>Filter <?php echo $objScaffold->getNamePlural(); ?></legend>
				<fieldset>
					<legend>&nbsp;</legend>
					<label for="search">Search:</label>
					<input type="search" name="search" id="search" value="<?php echo $objScaffold->getSearch(); ?>" /><br />
					<label for="status">Status:</label>
					<select name="status" id="status">
						<?php echo drawDropDown(getDropDownOptions('status'), $objScaffold->getStatus());?>
					</select>
				</fieldset>
				<fieldset>
					<legend>&nbsp;</legend>
					<label for="show">Show:</label>
					<input type="tel" name="show" id="show" value="<?php echo $objScaffold->getPerPage(); ?>" class="int" /> <span class="help">Enter anywhere from 1 to <?php echo $objScaffold->getTotal(); ?></span><br />
					<label for="sort">Order by:</label>
					<select name="sort" id="sort">
						<?php echo drawDropDown($sort_options, $objScaffold->getOrderBy()); ?>
					</select>
				</fieldset>
				<button type="submit">Filter</button>
			</fieldset>
	 </form>
</div>
