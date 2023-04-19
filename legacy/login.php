<?php
$domain = $_SERVER["HTTP_HOST"];
$webroot = getWebRoot();
$user = new \LeadMax\TrackYourStats\User\User;
$company =  \LeadMax\TrackYourStats\System\Company::loadFromSession();
$company->reloadSettings();

//checks if the User is already logged in
if ($user->is_loggedin())
{
	if ($user->verify_login_session())
	{
		send_to('dashboard');
	}
}

$user->checkLoginAttempts();


//POST to login.php (self),
//if count is < 5, continue, else, too many attempts for today
if (isset($_POST['txt_uname_email']) && $user->count < 5)
{
	$user_name = $_POST['txt_uname_email'];
	$email     = $_POST['txt_uname_email'];
	$password  = $_POST['txt_password'];
	
	$result = $user->login($user_name, $email, $password);
	
	if ($result == \LeadMax\TrackYourStats\User\Login::RESULT_SUCCESS)
	{
		if (isset($_GET["redirectUri"]))
		{
			send_to(urldecode($_GET["redirectUri"]));
		}
		else
		{
			send_to('dashboard');
		}
		
	}
	else
	{
		$user->badLoginAttempt();
		
		if ($result == \LeadMax\TrackYourStats\User\Login::RESULT_INVALID_CRED)
		{
			$error = "Incorrect login. <p>You have {$user->count} / 5 login attempts remaining. </p>";
		}
		else
		{
			if ($result == \LeadMax\TrackYourStats\User\Login::RESULT_BANNED)
			{
				$error = "This account has been banned. Login attempt has been logged and an administrator will be notified. ";
			}
		}
		
		
	}
}
else
{
	if (isset($_POST["button"]))
	{
		
		$error = "You have {$user->count} / 5 login attempts remaining. Please wait until tomorrow or contact an admin to reset your login attempt and/or password.";
		
	}
}


if($company->login_theme != '') {
    $filePath = __DIR__ . '/../public/login_themes/' . $company->login_theme . '/index.php';
    if (file_exists($filePath)){
        include($filePath);
        die;
    }
}

?>
	<!DOCTYPE html>
	<html>
	<head>
		<meta http-equiv = "Content-Type" content = "text/html; charset=utf-8"/>
		<meta name = "viewport" content = "width=device-width, initial-scale=1">
		<link rel = "shortcut icon" type = "image/ico" href = "<?= \LeadMax\TrackYourStats\System\Company::loadFromSession()->getImgDir() ?>/favicon.ico"/>
		<link rel = "stylesheet" type = "text/css" href = "<?php echo $webroot; ?>css/default.css"/>
		<link rel = "stylesheet" media = "screen" type = "text/css"
			  href = "<?php echo $webroot; ?>css/company.php"/>
		<link href = "<?php echo $webroot; ?>css/responsive_table.css" rel = "stylesheet" type = "text/css"/>
		<link href = "<?php echo $webroot; ?>css/drawer.min.css" rel = "stylesheet">
		<link rel = "stylesheet" type = "text/css" href = "<?php echo $webroot; ?>css/font-awesome.min.css">
		<link rel = "stylesheet" href = "<?php echo $webroot; ?>css/magic.min.css">
		<script type = "text/javascript" src = "<?php echo $webroot; ?>js/jquery_2.1.3_jquery.min.js"></script>
		<script type = "text/javascript" src = "<?php echo $webroot; ?>js/jscolor.min.js"></script>
		<script type = "text/javascript"
				src = "https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/jquery-ui.min.js"></script>
		<script type = "text/javascript" src = "<?php echo $webroot; ?>js/main.js"></script>
		<title><?php echo \LeadMax\TrackYourStats\System\Company::loadFromSession()->getShortHand(); ?></title>
	</head>
<body style = "background-color:#EAEEF1;">
	<div class = "top_sec value_span1">
		<div class = "logo">
			<a href = "<?php echo $webroot ?>"><img src = "<?= \LeadMax\TrackYourStats\System\Company::loadFromSession()->getImgDir() ?>/logo.png" alt = "<?php echo\LeadMax\TrackYourStats\System\Company::loadFromSession()->getShortHand(); ?>"
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
			max-width: 400px;
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
			<form method = "post">
				
				<div class = "left_con01">
					<div class = "heading_holder">
						<span class = "lft value_span9">Sign in</span>
					</div>
					<br/>
					<?php
					if (isset($error))
					{
						?>
						<div class = "alert alert-danger" style = " padding-bottom:5px;">
							<i class = "glyphicon glyphicon-warning-sign"></i> &nbsp;<span
									style = "color:red;"><?php echo $error; ?></span>
						</div>
						<?php
					}
					?>
					<p>
						
						<input type = "text" name = "txt_uname_email" placeholder = "Username or Email"
							   value = "<?php echo $user->autoFillEmail; ?>"
							   required/>
					</p>
					
					<p>
						
						<input type = "password" name = "txt_password" placeholder = "Password"
							   required/>
					</p>
					
					<a class = "small_txt value_span10" style = "font-size:14px;float:right;" href = "aff_help.php">Forgot
																													password?</a>
					
					
					<span class = "btn_yellow" style = "color:#1D4C9E;"> <input type = "submit" name = "button"
																				class = "value_span3-1 value_span2 value_span1-2"
																				value = "Sign In"/></span>
					
					<br/>
				</div>
			</form>
		</div><!-- white_box -->
	</div><!-- white_box_outer -->

<?php include 'footer.php'; ?>
