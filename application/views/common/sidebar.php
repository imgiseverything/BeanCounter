<?php 

/**
 *	Sidebar
 *	Generic sidebar content - used in most object_all.php views
 *	Contains a form to filter the data in the main content block	
 */

if($objTemplate->getMode() == 'normal'): 
?>
 <div class="filter-form">
	<form id="filterForm" method="get" action="<?php echo $objScaffold->getFolder(); ?>" class="hidden">
		<fieldset>
			<fieldset class="fieldset-row">
        		<div class="field">
            		<label for="search">Find <?php echo $objScaffold->getNamePlural(); ?> by keyword:</label>
                	<input type="search" name="search" id="search" value="<?php echo $objScaffold->getSearch(); ?>" /><br />
        		</div>
            	<div class="field">
                	<label for="show">Number of items to show:</label>
                	<input type="tel" name="show" id="show" value="<?php echo min($objScaffold->getTotal(), $objScaffold->getPerPage()); ?>" class="int" /> <span class="help">Enter anywhere from 1 to <?php echo $objScaffold->getTotal(); ?></span>
                </div>
                <div class="field">
               	 <label for="sort">Order by:</label>
                	<select name="sort" id="sort">
						<?php echo drawDropDown($sort_options, $objScaffold->getOrderBy()); ?>
                	</select>
                </div>
        	</fieldset>
			<button type="submit">Filter <?php echo $objScaffold->getNamePlural(); ?></button>
		</fieldset>
	 </form>
</div>
<?php endif; ?>
