<?php

$section = "create-affiliate";
require('header.php');


if (\LeadMax\TrackYourStats\System\Session::userType() == \App\Privilege::ROLE_AFFILIATE || \LeadMax\TrackYourStats\System\Session::userType() == \App\Privilege::ROLE_UNKNOWN)
{
	send_to("home.php");
}

if (!\LeadMax\TrackYourStats\System\Session::permissions()->can("create_affiliates"))
{
	send_to("home.php");
}



$assign = new \LeadMax\TrackYourStats\Table\Assignments(
	[
	]
);

$create = new \LeadMax\TrackYourStats\User\Create();
$assign->getAssignments();
$assign->setGlobals();


$na     = new \LeadMax\TrackYourStats\User\User();
$result = $na->RegisterAndSetPriviliges('/user/manage');


$create->dumpAssignablesToJavaScript();

$create->dumpPermissionsToJavascript();


switch ($result)
{
	case "USR_OR_EMAIL":
		$error = "The username or email you entered already exists in the system.";
		break;
	
	case "PWD":
		$error = "Passwords do no match.";
		break;
	
	case "EMPTY_USR_NAME" :
		$error = "Please enter a username.";
		break;
	
	case "EMPTY_PWD" :
		$error = "Please enter a password.";
		break;
	
	default:
		$error = "";
		break;
}


?>

<script type = "text/javascript" src = "js/aff.js"></script>


<!--right_panel-->
<div class = "right_panel">
	<div class = "white_box_outer">
		<div class = "heading_holder value_span9"><span
					class = "lft"> Add New Affiliate  </span></div>
		
		<div class = "white_box value_span8">
			<span class = "small_txt value_span10"><?PHP echo $error; ?></span>
			
			<form action = "<?php htmlspecialchars($_SERVER['PHP_SELF']); ?>" method = "post" id = "form"
				  class = "form-horizontal" enctype = "multipart/form-data">
				
				<div class = "left_con01">
					<p>
						<label class = "value_span9">First Name</label>
						
						<input type = "text" class = "form-control" name = "first_name" maxlength = "155"
							   id = "first_name"/>
					</p>
					<p>
						<label class = "value_span9">Last Name</label>
						
						<input type = "text" class = "form-control" name = "last_name" maxlength = "155"
							   id = "last_name"/>
					</p>
					<p>
						<label class = "value_span9">Email</label>
						
						<input type = "text" class = "form-control input-sm" name = "email" maxlength = "155"
							   id = "email"/>
					</p>
					<p>
						<label class = "value_span9">Password</label>
						
						<input type = "password" class = "form-control" name = "password" minlength = "5" maxlength = "255"
							   value = ""
							   id = "password"/>
					</p>
					<p>
						<label class = "value_span9">Confirm Password</label>
						
						<input type = "password" class = "form-control" name = "confirmpassword" minlength = "5" maxlength = "255"
							   value = "" id = "confirmpassword"/>
					</p>
					<p>
						<label class = "value_span9">Cell Phone</label>
						
						<input type = "text" class = "form-control input-sm" name = "cell_phone" maxlength = "155"
							   placeholder = "(Optional)"
							   id = "cell_phone"/>
					</p>
					<p>
						<label class = "value_span9">Skype</label>
						<!-- TODO Link Referrer Payout -->
						<input type = "text" class = "form-control" name = "skype" minlength = "5" maxlength = "255"
							   placeholder = "(Optional)"
							   value = "" id = "skype"/>
					</p>
					
					
					<p>
						<label class = "value_span9">Company</label>
						<input type = "text" class = "form-control" name = "company_name" minlength = "5" maxlength = "255"
							   placeholder = "(Optional)"
							   value = "" id = "company_name"/>
					</p>
				
				</div>
				<div class = "right_con01">
					<p>
						<label class = "value_span9">Username</label>
						
						<input type = "text" class = "form-control" name = "user_name" maxlength = "155"
							   id = "user_name"/>
					</p>
					
					
					<p>
						<label class = "value_span9">Status</label>
						<select class = "form-control input-sm " id = "status" name = "status">
							<option value = "1" selected>Active</option>
							;
							<option value = "0">Disabled</option>
							;
						</select>
					</p>
					<p>
						<label class = "value_span9">Privileges</label>
						<?php $create->printRadios(); ?>
					
					
					</p>
					<span class = "btn_yellow"> <input type = "submit" name = "button"
													   class = "value_span6-2 value_span2 value_span1-2"
													   value = "Register"/></span>
					<span class = "btn_yellow" style = "margin-left:2%;"> <a onclick = "history.go(-1);"
																			 class = "value_span6-2 value_span2 value_span1-2"
						>Cancel</a></span>
					<p>
						<label class = "value_span9">Assign To</label>
						<select required class = "form-control input-sm " id = "referrer_repid" name = "referrer_repid">
						
						</select>
					</p>
					
					<?php
					if (\LeadMax\TrackYourStats\System\Session::permissions()->can("edit_referrals"))
					{
						echo "<p id=\"referralP\" style=\"display:none;\">
                        <label  class=\"value_span9\">Referrals</label>
                        <input class=\"fixCheckBox\" type=\"checkbox\" id=\"referralCheckBox\"  name=\"referralCheckBox\"> Enable
                    <p id=\"referralForm\" style=\"display:none;\">";
						
						
						echo " <label style=\"font-size:12px;\" for=\"referralSelectBox\">Referrer</label>
                        <select class=\"form-control\" id=\"referralSelectBox\" name=\"referralSelectBox\" disabled required>
                            ";
						
						\LeadMax\TrackYourStats\User\Referrals::printAffiliatesToSelectBox();
						
						echo "</select>

                        <label style=\"font-size:12px;\"  for=\"start_date\">Start Date</label>
                        <input id=\"start_date\" name=\"start_date\" type=\"date\" disabled required>

                        <label style=\"font-size:12px;\"  for=\"end_date\">End Date (Empty for Indefinite)</label>
                        <input id=\"end_date\" name=\"end_date\" type=\"date\"  disabled>



                        <label style=\"font-size:12px;\"  for=\"referral_type\">Flat Fee / Percentage</label>
                        <select id=\"referral_type\" name=\"referral_type\" class=\"form-control\" disabled required>
                            <option value=\"flat\" id=\"flat_fee\">Flat Fee</option>
                            <option value=\"percentage\" id=\"percentage\">Percentage</option>
                        </select>


                        <label style=\"font-size:12px;\"  for=\"amount\">Amount / Percentage</label>
                        <input id=\"amount\" name=\"amount\" type=\"number\" value=\"0\" disabled required>


                    </p>";
						
					}
					
					?>
					
					
					</p>
					
					<p id = "permissionsP">
					
					
					</p>
				</div>
			
			</form>
			
			
			<script type = "text/javascript">
				
				$("#start_date").datepicker({dateFormat: 'yy-mm-dd'});
				$("#end_date").datepicker({dateFormat: 'yy-mm-dd'});
				
				//load datepickers..
				$(function () {
					$("#start_date").datepicker({dateFormat: 'yy-mm-dd'});
					$("#end_date").datepicker({dateFormat: 'yy-mm-dd'});
					
				});
				
				$(document).ready(function () {
					$("#referralCheckBox").change(function () {
						
						$("#referralCheckBox").attr("disabled", "disabled");
						
						var capForm = $("#referralForm");
						
						if (capForm.css("display") === "none") {
							$("#referralSelectBox").removeAttr("disabled");
							$("#referral_type").removeAttr("disabled");
							$("#amount").removeAttr("disabled");
							$("#start_date").removeAttr("disabled");
							$("#end_date").removeAttr("disabled");
							capForm.slideDown('slow', function () {
								$("#referralCheckBox").removeAttr("disabled");
								
								
							});
						}
						
						else {
							$("#referralSelectBox").prop("disabled", true);
							$("#referral_type").prop("disabled", true);
							$("#amount").prop("disabled", true);
							$("#start_date").prop("disabled", true);
							$("#end_date").prop("disabled", true);
							
							capForm.slideUp('slow', function () {
								$("#referralCheckBox").removeAttr("disabled");
							});
							
						}
						
						
					});
					
					if (cap_enabled)
						$("#enable_cap").click();
					
				});
			
			
			</script>
			
			
			<script>
				
				function enableDisable() {
					$("#referralSelectBox").prop("disabled", !$('#referralCheckBox').prop('checked'));
					$("#referral_type").prop("disabled", !$('#referralCheckBox').prop('checked'));
					$("#amount").prop("disabled", !$('#referralCheckBox').prop('checked'));
					$("#start_date").prop("disabled", !$('#referralCheckBox').prop('checked'));
					$("#end_date").prop("disabled", !$('#referralCheckBox').prop('checked'));
					
				}
				
				$("#referralCheckBox").change(enableDisable);
			</script>
		</div>
	
	
	</div>
	
	
	<!--right_panel-->
	
	<?php include "footer.php"; ?>
	
	
	<script type = "text/javascript">
		
		
		// A $( document ).ready() block.
		$(document).ready(function () {
			console.log("ready!");
			jQuery(function ($) {
				$("#cell_phone").mask("(999) 999-9999");
			});
		});
	
	
	</script>
