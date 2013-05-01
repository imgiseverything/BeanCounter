<?php	

	/**
	 *	Product Installation View
	 */
?>
<!DOCTYPE html>
<html dir="ltr" lang="en-GB">
<head>  
<meta charset="UTF-8" />
<title>Install <?php echo $objApplication->getApplicationName(); ?></title>
<style>
	
	*{
		-moz-box-sizing: border-box;
		-webkit-box-sizing: border-box;
		box-sizing: border-box;
	}

	body{
		background: #eee;
		font: 16px sans-serif;
		margin: 0 auto;
		padding: 0;
		width: 600px;
	}
	
	h1{
		font: 42px/48px sans-serif;
		padding: 0 20px 20px;
		text-align: center;
	}
	
	p{
		padding: 0 20px 20px;
	}
	
	.site-container{
		background: #fff;
		border-bottom: 5px solid #ddd;
		color: #333;
		margin: 40px auto;
		padding: 20px 0 0;
		width: 100%;
	}
		
	form{
		background: #f5f5f5;
		padding: 20px 0;
	}
	
	fieldset{	
		border: none;
		margin: 0 0 20px;
		padding: 10px 10px;
	}
	
		fieldset > fieldset{	
			border-bottom: 1px solid #ddd;
		}
	
	legend{
		color: #444;
		font-size: 24px;
	}
	
	label, input, textarea, select{
		clear: both;
		display: block;
		float: left;
		font: 18px sans-serif;
	}
	
	label{
		margin-bottom: .25em;
	}
	
	label[for]{
		cursor: pointer;
	}
	
	input{
		border: 1px solid #ddd;
		color: #666;
		padding: 10px 10px;
		width: 100%;
	}
	
	input:focus{
		color: #111;
	}
	
	button,
	a.button{
		background: #666;
		border: none;
		border-bottom: 3px solid #333;
		color: #FFF;
		cursor: pointer;
		font: bold 20px Arial, sans-serif;
		display: block;
		margin: 40px auto 20px;
		padding: 20px 10px;
		text-align: center;
		text-decoration: none;
		text-shadow: 0 1px 0 rgba(0, 0, 0, 1);
		width: 100%;
	}
	
		button:hover,
		a.button:hover{
			background: #333;
			border-bottom-color: #000;
		}
	
	div.field{
		clear: both;
		display: block;
		float: left;
		padding: 10px 0 20px;
		width: 100%;
	}
	
	label.help{
		color: #666;
		font-size: 14px;
		padding: 10px 0 0;
	}
	
	div.feedback{
		background: #ffe;
		margin: 20px;
	}

	div.feedback.error{
		background-color: #ffb;
	}

	div.feedback.success{
		background-color: #e6efc2;
	}

	div.feedback p,
	div.feedback ul{
		padding: 0 10px 20px 30px;
	}
	
		div.feedback h3{
			font-size: 24px;
			padding: 20px 25px 0;
		}
		
			div.feedback p,
			div.feedback li{
				font-size: 18px;
				line-height: 1.3;
			}
			
			div.feedback li{
				padding-bottom: 0.5em;
			}
			
			div.feedback ul li:first-child{
				list-style: none;
				margin-left: 0;
			}

		div.feedback p.hideFeedback{
			float: right;
		}
	
	div.buttons{
		margin: 0 auto;
		width: 300px;
	}
	
	#generate_password{
		background: #ddd;
		border-bottom: 2px solid #bbb;
		color: #000;
		display: inline-block;
		float: left;
		font-size: 14px;
		line-height: 1;
		margin: 10px 0 0 5px;
		padding: 5px 10px;
		text-decoration: none;
	}
	
		#generate_password:hover{
			background: #bbb;
			border-bottom: 2px solid #999;
		}
	
	.success-copy{
		text-align: center;
	}
	
</style>
</head>
<body>
<div class="site-container">
	<div class="group site-content">
		<div class="inner">

			<div id="PrimaryContent">
		        <?php 
				// only show form if it hasn't been completed successfully: to save repeated inserts/edits
				if(form_success($user_feedback) !== true):
				?>
		        <h1>Install <?php echo $objApplication->getApplicationName(); ?></h1>
		    	<?php echo $objFeedback->getFeedback(); ?>
		        <div class="instructions">
		            <?php			
					 if(file_exists($_SERVER['DOCUMENT_ROOT'] . '/config.php') && $objInstall->getDatabaseWorks() === true): 
					?>        
		        	<form id="edit_settings" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
		                <fieldset>                             
		                    <fieldset>
		                        <legend>Business details</legend>
		                        <div class="field">
		                    		<label for="business_name" class="required">Your business name</label>
		                        	<input type="text" name="business_name" id="business_name" value="<?php echo $business_name; ?>" required="required" aria-required="true" />
		                        </div>
		                    </fieldset>
		                    <fieldset>
		                    	<legend>Personal details</legend>
		                    	<div class="field">
		                    		<label for="firstname" class="required">Your first name</label>
		                        	<input type="text" name="firstname" id="firstname" value="<?php echo $firstname; ?>" required="required" aria-required="true" />
		                        </div>
		                        <div class="field">
		                       	 	<label for="surname" class="required">Your surname</label>
		                        	<input type="text" name="surname" id="surname" value="<?php echo $surname; ?>" required="required" aria-required="true" />
		                        </div>
		                   </fieldset>
		                   <fieldset>
		                    	<legend>Log-in details</legend>
		                        <div class="field">
		                    		<label for="email" class="required">Your email address</label>
		                        	<input type="email" name="email" id="email" value="<?php echo $email; ?>" required="required" aria-required="true" />
		                        </div>
		                        <div class="field">
		                    		<label for="password_x">Your password</label>
		                        	<input type="text" name="password_x" id="password_x" value="<?php echo $password_x; ?>" class="int" autofill="false" required="required" aria-required="true" />
		                        	<label class="help">It is recommended you choose a password made up of both letters and numbers that will be hard for a stranger/hacker/imaginary bad person to guess.</label>
		                        </div>
		                    </fieldset>
		                    <input type="hidden" name="action" value="install" />
		                    <button type="submit">Submit details and install <?php echo $objApplication->getApplicationName(); ?></button>
		              </fieldset>
		            </form>
		            <?php 
					elseif(!file_exists($_SERVER['DOCUMENT_ROOT'] . '/config.php')):
					
						echo drawFeedback(array('type' => 'error', 'content' => 'You have not created a configuration file. Please create your configuration file by copying and pasting the content of config.simple.php into a new file called config.php. Then follow the instructions in that file to ensure you edit the correct variables to connect to your MySQL database.'));
						
		            elseif($objInstall->getDatabaseWorks() !== true):
		           		echo drawFeedback(array('type' => 'error', 'content' => array('Your database isn&#8217;t set-up correctly.'))); 
		           	endif;	
		            ?>
		        </div>
		        <?php
		        else:
				?>
				<div class="success-copy">
					<h1>Hooray!</h1>
					<p>Hi <strong><?php echo $firstname; ?></strong>, you have successfully installed <?php echo $objApplication->getApplicationName(); ?>.</p>
					<p>You are automatically logged in now but in future you can log with your email (<?php echo $email; ?>) and your password which is: <strong><?php echo $password_x; ?></strong>. Make a note of it somewhere.</p>
			        <div class="buttons clearfix" id="SuccessButton">
			        	<a href="/" class="button " style="width: auto;">Start using <?php echo $objApplication->getApplicationName(); ?></a>
			        </div>
				</div>
		        <?php
				endif;
				?>
		    </div>    
		</div>
	</div>
</div>
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>
<script>
function generatePassword(length){
	var password = '',
	characters = '1234567890abcdefghijklmnopqrstuvwxyz1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
	
	for(x = 0; x < length; x ++){
		i = Math.floor(Math.random() * 62);
		password += characters.charAt(i);
	}
		
	return password;
	
}

$(document).ready(function(){		

	var passwordButton = '<span class="help"><a href="#password_x" id="generate_password">Generate a password</a></span>',
		$passwordX = $("#password_x");
	
	
	$passwordX.css('width', 200);
	$(passwordButton).insertAfter("#password_x");

		
	$("#generate_password").click(function(e){
		e.preventDefault();
		$passwordX.val(generatePassword(7));
	});

});
</script>
</body>
</html>