<?php
/**
 * Created by PhpStorm.
 * User: professional slacker
 * Date: 2/5/2018
 * Time: 4:58 PM
 */


$webroot = getWebRoot();



?>

<!DOCTYPE html>
<html>
<head>
	
	<meta http-equiv = "Content-Type" content = "text/html; charset=utf-8"/>
	<meta name = "viewport" content = "width=device-width, initial-scale=1">
	
	<link rel = "shortcut icon" type = "image/ico"
		  href = "<?PHP echo\LeadMax\TrackYourStats\System\Company::loadFromSession()->getImgDir() . "/favicon.ico"; ?>"/>
	<link rel = "shortcut icon" type = "image/ico"
		  href = "<?PHP echo\LeadMax\TrackYourStats\System\Company::loadFromSession()->getImgDir() . "/favicon.ico"; ?>"/>
	<link href = "css/bootstrap.min.css" rel = "stylesheet">
	<link href = "css/animate.css" rel = "stylesheet">
	
	
	<link rel = "stylesheet" type = "text/css" href = "<?php echo $webroot; ?>css/default.css"/>
	
	<link rel = "stylesheet" media = "screen" type = "text/css"
		  href = "<?php echo $webroot; ?>css/company.php"/>
	
	
	<link rel = "stylesheet" type = "text/css" href = "<?php echo $webroot; ?>css/font-awesome.min.css">
	<link rel = "stylesheet" href = "<?php echo $webroot; ?>css/magic.min.css">
	
	<script type = "text/javascript" src = "<?php echo $webroot; ?>js/jquery_2.1.3_jquery.min.js"></script>
	<script type = "text/javascript" src = "<?php echo $webroot; ?>js/jquery-ui.min.js"></script>
	
	<script type = "text/javascript" src = "<?php echo $webroot; ?>js/jscolor.min.js"></script>
	<link rel = "stylesheet" href = "css/jquery-ui.min.css"/>
	
	<script type = "text/javascript" src = "<?php echo $webroot; ?>js/tables.js"></script>
	<script type = "text/javascript" src = "<?php echo $webroot; ?>js/bootstrap-notify.min.js"></script>
	
	
	<title><?php echo\LeadMax\TrackYourStats\System\Company::loadFromSession()->getShortHand(); ?></title>
</head>
<body style = "background-color:#EAEEF1;">
<div class = "top_sec value_span1">
	<div class = "logo">
		<a href = "<?php echo $webroot ?>"><img src = "<?=\LeadMax\TrackYourStats\System\Company::loadFromSession()->getImgDir() ?>/logo.png" alt = "<?php echo\LeadMax\TrackYourStats\System\Company::loadFromSession()->getShortHand(); ?>"
												title = "<?php echo\LeadMax\TrackYourStats\System\Company::loadFromSession()->getShortHand(); ?>"/></a>
	</div>

</div> <!-- top_sec -->

<style>
	
	.white_box {
		
		margin-top: 40px;
	}
	
	.white_box_outer {
		float: none;
		margin: 0 auto;
		max-width: 750px;
	}
	
	.left_con01 {
		width: auto;
		padding: 5px;
		padding-top: 10px;
		padding-left: 17px;
		padding-right: 10px;
		float: none;
		
	}
	
	.heading_holder {
		margin: 0px 0px 10px 0px;
	}
	
	.left_con01 p input {
	
	}
	
	.btn_yellow {
	
	}
</style>


	   <!--right_panel-->
<div class = "white_box_outer">
	
	<div class = "clear"></div>
	<div class = "white_box value_span8">
		<div class = "com_acc">
			
			<div class = "left_con01">
				<div class = "heading_holder">
					<h3 class = " value_span9">Congratulations!</h3>
				</div>
				
				<?php
				$company =\LeadMax\TrackYourStats\System\Company::loadFromSession();
				?>
				
				<p>
					You application was successfully submitted.
					Your Account will be approved for login within 24 Hrs.
				</p>
				
			<!--	<br/>
				<label >Email: <?=$company->getEmail()?></label> -->
			
			</div>
		</div>
	</div><!-- white_box -->
</div><!-- white_box_outer -->


<?php include 'footer.php'; ?>


</html>










