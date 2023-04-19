<?php
/**
 * Created by PhpStorm.
 * User: professional slacker
 * Date: 2/6/2018
 * Time: 3:37 PM
 */
include 'header.php';

if (\LeadMax\TrackYourStats\System\Session::userType() !== \App\Privilege::ROLE_AFFILIATE)
{
	send_to('home.php');
}

$postBackUrls = new \LeadMax\TrackYourStats\User\PostBackUrl(\LeadMax\TrackYourStats\System\Session::userID());

if (isset($_POST["postback_url"]))
{
	$result = \LeadMax\TrackYourStats\User\PostBackUrl::updateUserPostBacks(\LeadMax\TrackYourStats\System\Session::userID(), $_POST["postback_url"], $_POST["deduction_url"], $_POST["free_sign_up_url"]);
	
	if ($result)
	{
		send_to('global_postback.php');
	}
	
	
}


?>

<!--right_panel-->
<div class = "right_panel">
	<div class = "white_box_outer large_table">
		<div class = "heading_holder">
			<span class = "lft value_span9">Global PostBack</span>
		
		</div>
		
		
		<div class = "clear"></div>
		<form action = "global_postback.php" method = "POST">
			
			<div class = "white_box value_span8">
				
				<div class = "left_con01">
					
					
					<p>
						<label class = "value_span9">Conversion PostBack URL:</label>
						
						<input type = "text" class = "form-control input-sm" name = "postback_url" maxlength = "155"
							   value = "<?= \LeadMax\TrackYourStats\User\User::getUsersGlobalPostBackURL(\LeadMax\TrackYourStats\System\Session::userID()) ?>" id = "postback_url"/>
						<span class = "small_txt value_span10">If you did not assign an offer a post back url, this url will fire by default.</span>
					</p>
					
					<p>
						<label class = "value_span9">Deduction PostBack URL:</label>
						
						<input type = "text" class = "form-control input-sm" name = "deduction_url" maxlength = "155"
							   value = "<?= $postBackUrls->getDeductionURL() ?>" id = "postback_url"/>
						<span class = "small_txt value_span10">On a conversion deduction, this url will fire.</span>
					</p>
					
					<p>
						<label class = "value_span9">Free Sign Up PostBack URL:</label>
						
						<input type = "text" class = "form-control input-sm" name = "free_sign_up_url" maxlength = "155"
							   value = "<?= $postBackUrls->getFreeSignUpURL() ?>" id = "postback_url"/>
						<span class = "small_txt value_span10">On free sign up conversion, this url will fire. </span>
					</p>
					
					<p>
						<label class = "value_span9"><?php echo \LeadMax\TrackYourStats\System\Company::loadFromSession()->getShortHand(); ?> Vars</label>
						<span>#affid#</span><br/>
						<span>#user#</span><br/>
						<span>#offid#</span> <br/>
						<span>#clickid#</span> <br/>
						
						<span class = "small_txt value_span10"><?php echo \LeadMax\TrackYourStats\System\Company::loadFromSession()->getShortHand(); ?> vars are auto inputed into your URL if they're found.</span>
						<span class = "small_txt value_span10">e.g. "google.com/?var1=<b>#affid#"</b> will translate to "google.com/?var1=<b>32</b>"</span>
						
						<br/>
						<br/>
						<span class = "small_txt value_span10">To store sub vars, have your software append to our offer url with sub1-sub5. Ex: &sub1=3213</span>
						<span class = "small_txt value_span10">Sub vars can be used in postback url as such:  #sub1#,     #sub2#    etc. </span>
						
						<br/>
						<br/>
						<br/>
						<span class = "small_txt value_span10">When getting a url, if not otherwise specified, it is correct convention to seperate additionally vars with an ampersand </span>
						<br/>
						<span class = "small_txt value_span10">Ex: https://yournetwork.com/?var1=#sub1#<b>&</b>var2=#sub2#</span>
					
					</p>
					
					
					<span class = "btn_yellow"> <input type = "submit" name = "button"
													   class = "value_span6-2 value_span2 value_span1-2"
													   value = "Save"/></span>
				</div>
				<div class = "right_con01" id = "offers">
				
				
				</div>
			
			
			</div>
		</form>
	
	
	</div>

</div>

<?php include 'footer.php'; ?>
