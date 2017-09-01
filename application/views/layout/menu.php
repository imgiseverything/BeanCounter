<?php

/**
 * Menu template file
 *
 * Used on every page - user acccess level determines
 * which elements the user can see
 */

if(isset($objAuthorise)):
?>
<nav class="site-nav">
	<div class="mobile-only button site-nav__button" title="Show/hide menu"><span class="ss-icon ss-rows"></span> Menu</div>
	<ul class="inner">
		<li id="home-link"><a href="/" accesskey="1">Dashboard</a></li>
	    <?php if($objAuthorise->getStatus() && $objAuthorise->getStatus() == 'logged-in'): // show menu items if the user is logged in ?>
	    <?php if($objAuthorise->getStatus() && $objAuthorise->getLevel() != 'Basic'): // show the following options to superusers & accountants ?>
	    <li id="accounts-link"<?php echo isURLSelected('/accounts'); ?>><a href="/accounts/">Accounts</a>
	    	<ul>
				<li<?php echo isURLSelected('/accounts/incomings'); ?>><a href="/accounts/incomings/">Revenue</a></li>
				<li<?php echo isURLSelected('/accounts/outgoings'); ?>><a href="/accounts/outgoings/">Expenses</a></li>
				<li<?php echo isURLSelected('/accounts/vat'); ?>><a href="/accounts/vat/">VAT accounts</a></li>
				<li<?php echo isURLSelected('/accounts/details'); ?>><a href="/accounts/details/<?php echo (!empty($_SERVER['QUERY_STRING'])) ? '?' . $_SERVER['QUERY_STRING'] : ''; ?>">Detailed view</a></li>
				<li<?php echo isURLSelected('/dividends'); ?>><a href="/dividends/">Dividends</a></li>
	    	</ul>
	    </li>
	    <li id="bookings-link"<?php echo isURLSelected('/bookings'); ?>><a href="/bookings/">Bookings</a>
	    	<ul>
	    		<li><a href="/bookings/add/">Add a new booking</a>
	    		<li><a href="/bookings/types/">Booking types</a>
	    	</ul>
	    </li>
	    <li id="leads-link"<?php echo isURLSelected('/leads'); ?>><a href="/leads/">Leads</a>
	    	<ul>
	    		<li><a href="/leads/add/">Add a new lead</a>
	    		<li><a href="/leads/types/">Lead types</a>
	    	</ul>
	    </li>
	     <li id="timings-link"<?php echo isURLSelected('/timings'); ?>><a href="/timings/">Time tracking</a>
	    	<ul>
	    		<li><a href="/timings/add/">Add new timing</a>
	    		<li><a href="/timings/tags/">Timing tags</a>
	    	</ul>
	    </li>
	    <?php endif; // end if not basic ?>
		<li id="projects-link"<?php echo isURLSelected('/projects'); ?>><a href="/projects/">Projects</a>
	    	<ul>
	    		<li><a href="/projects/add/">Add a new project</a>
	    	</ul>
	    </li>
		<li id="proposals-link"<?php echo isURLSelected('/proposals'); ?>><a href="/proposals/">Proposals</a>
	    	<ul>
	    		<li><a href="/proposals/add/">Add a new proposal</a>
	    	</ul>
	    </li>
	    <?php if($objAuthorise->getStatus() && $objAuthorise->getLevel() != 'Basic'): // show the following options to superusers ?>
	    <li id="outgoings-link"<?php echo isURLSelected('/outgoings') . isURLSelected('/donations'); ?>><a href="/outgoings/" title="Expenses">Outgoings</a>
	    	<ul>
	    		<li><a href="/outgoings/add/">Add a new outgoing</a>
	    		<li><a href="/mileage/add/">Add some mileage</a>
				<li<?php echo isURLSelected('outgoings/categories'); ?>><a href="/outgoings/categories/">Expense categories</a></li>
				<li<?php echo isURLSelected('donations'); ?>><a href="/donations/">Donations</a></li>
				<li<?php echo isURLSelected('pensions'); ?>><a href="/pensions/">Pension payments</a></li>
			</ul>
	    </li>
	    <?php if($objAuthorise->getStatus() && $objAuthorise->getLevel() == 'Superuser'): // show the following options to superusers  ?>
	    <li<?php echo isURLSelected('/clients'); ?>><a href="/clients/">Companies</a>
	    	<ul>
	    		<li<?php echo isURLSelected('/clients'); ?>><a href="/clients/">Clients</a></li>
	   			<li<?php echo isURLSelected('/suppliers'); ?>><a href="/suppliers/">Suppliers</a></li>
	    	</ul>
	    </li>
	    <?php endif; // end if superuser ?>
	    <?php endif; // end if not basic ?>
	    <?php /*<li id="users-link"<?php echo isURLSelected('/users'); ?>><a href="/users/">Users</a></li><?php */ ?>
	    <?php endif; // end if logged-in ?>
	</ul>
</nav>
<?php endif; ?>