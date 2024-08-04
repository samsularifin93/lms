<?php

//user_login.php

include 'database_connection.php';

include 'function.php';

if(is_user_login())
{
	header('location:issue_book_details.php');
}

$message = '';

if(isset($_POST["login_button"]))
{
	$formdata = array();

	if(empty($_POST["user_email_address"]))
	{
		$message .= '<li>Email Address is required</li>';
	}
	else
	{
		$formdata['user_email_address'] = trim($_POST['user_email_address']);
		
		/*
		if(!filter_var($_POST["user_email_address"], FILTER_VALIDATE_EMAIL))
		{
			$message .= '<li>Invalid Email Address</li>';
		}
		else
		{
			$formdata['user_email_address'] = trim($_POST['user_email_address']);
		}
		*/
	}

	if(empty($_POST['user_password']))
	{
		$message .= '<li>Password is required</li>';
	}	
	else
	{
		$formdata['user_password'] = trim($_POST['user_password']);
	}

	if($message == '')
	{
		error_reporting(E_ALL);
		ini_set('display_errors', '1');
		$LDAPUserDomain = "@virtualbox.local";
		if(isset($_POST['user_password'])){
			/**************************************************
			 Bind to an Active Directory LDAP server and look
			 something up.
			 ***************************************************/
			$SearchFor="samsul";		//What string do you want to find?
			$SearchField="mail";			//In what Active Directory field do you want to search for the string?
			$LDAPHost = "ldap://192.168.56.26";		//Your LDAP server DNS Name or IP Address
			$dn = "DC=virtualbox,DC=local";		//Put your Base DN here
			$LDAPUser = "samsul";		//A valid Active Directory login
			$LDAPUserPassword = "v!rtualb0x";
			$LDAPFieldsToFind = array("*");		//Search Felids, Wildcard Supported for returning all values

			$cnx = ldap_connect($LDAPHost) or die("Could not connect to LDAP");
			ldap_set_option($cnx, LDAP_OPT_PROTOCOL_VERSION, 3);	//Set the LDAP Protocol used by your AD service
			ldap_set_option($cnx, LDAP_OPT_REFERRALS, 0);		//This was necessary for my AD to do anything
			
			@$ldapbind = ldap_bind($cnx,$LDAPUser.$LDAPUserDomain,$LDAPUserPassword) or die("Could not bind to LDAP");
			
			error_reporting (E_ALL ^ E_NOTICE);			//Suppress some unnecessary messages
			
			if ($ldapbind==true) {
			
				$filter="($SearchField=$SearchFor*)";	//Wildcard is * Remove it if you want an exact match
				$sr=ldap_search($cnx, $dn, $filter, $LDAPFieldsToFind);
				$info = ldap_get_entries($cnx, $sr);
				
				$mail_valid = "";
				
				for ($x=0; $x<$info["count"]; $x++) {
					$mail_valid = $info[$x]['mail'][0];
					
					if($x==0){
						break;
					}
				}
				
				if($formdata['user_email_address'].$LDAPUserDomain == $mail_valid){
					$_SESSION['user_id'] = $formdata['user_email_address'].$LDAPUserDomain;
					$_SESSION['user_name'] = $formdata['user_email_address'].$LDAPUserDomain;
					header('location:issue_book_details.php');
				}
				
				if ($x==0) {
					$message = '<li>Account Not Found.</li>';
				}
			} else {
				$message = '<li>LDAP bind failed.</li>';
			}
		}		
	}
}

include 'header.php';

?>

<div class="d-flex align-items-center justify-content-center" style="height:700px;">
	<div class="col-md-6">
		<?php 

		if($message != '')
		{
			echo '<div class="alert alert-danger"><ul>'.$message.'</ul></div>';
		}

		?>
		<div class="card">
			<div class="card-header">User Login</div>
			<div class="card-body">
				<form method="POST">
					<div class="mb-3">
						<label class="form-label">Email address</label>
						<input type="text" name="user_email_address" id="user_email_address" value="samsul" class="form-control" />
					</div>
					<div class="mb-3">
						<label class="form-label">Password</label>
						<input type="password" name="user_password" id="user_password" value="v!rtualb0x" class="form-control" />
					</div>
					<div class="d-flex align-items-center justify-content-between mt-4 mb-0">
						<input type="submit" name="login_button" class="btn btn-primary" value="Login" />
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<?php 

include 'footer.php';

?>