<?php
ob_start();
session_start();
include("inc/config.php");
include("inc/functions.php");
include("inc/CSRF_Protect.php");
$csrf = new CSRF_Protect();
$error_message = '';


// Getting all language variables into array as global variable
$i = 1;
$statement = $pdo->prepare("SELECT * FROM tbl_language");
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);
foreach ($result as $row) {
	define('LANG_VALUE_' . $i, $row['lang_value']);
	$i++;
}


if (isset($_POST['form1'])) {

	$valid = 1;
	if (empty($_POST['CompanyName']) || empty($_POST['phonenumber']) || empty($_POST['selleremail']) || empty($_POST['password']) || empty($_POST['password2'])) {
		//  
		$error_message = 'Please fill in the fields below to Continue.<br>';
	} else {
		$CompanyName = strip_tags($_POST['CompanyName']);
		$phone = strip_tags($_POST['phonenumber']);
		// valdate the email address and see if the email address is not already in the systemn to avoid spam users
		if (filter_var($_POST['selleremail'], FILTER_VALIDATE_EMAIL) === false) {
			$valid = 0;
			$error_message .= LANG_VALUE_134 . "<br>";
		} else {
			$statement = $pdo->prepare("SELECT * FROM tbl_user WHERE email=?");
			$statement->execute(array($_POST['selleremail']));
			$total = $statement->rowCount();
			if ($total) {
				$valid = 0;
				$error_message .= LANG_VALUE_147 . "<br>";
			}
		}

		if (empty($_POST['password']) && empty($_POST['password2'])) {
			$valid = 0;
			$error_message .= LANG_VALUE_138 . "<br>";
		}

		if (!empty($_POST['password']) && !empty($_POST['password2'])) {
			if ($_POST['password'] != $_POST['password2']) {
				$valid = 0;
				$error_message .= LANG_VALUE_139 . "<br>";
			}
		}

		// code below for getting user Location in grid co-ordinates to fill then=m in the database, this is to make the user/buyer and the administrator locate the buyer



		if ($valid == 1) {
			// with the code below, we are asking the system to send the data to the database and also send the user to confirm the email and verify
			$token = md5(time());
			$cust_datetime = date('Y-m-d h:i:s');
			$cust_timestamp = time();
			// echo $token;
			// saving into the database
			$statement = $pdo->prepare("INSERT INTO tbl_user (
                                      	full_name,
										email,
										phone,
										Password,
										role,
										status,
										Location                                  
                                    ) VALUES 	(?,
												?,
												?,
												?,
												?,
												?)");
			$statement->execute(array(
				strip_tags($_POST['CompanyName']),
				strip_tags($_POST['selleremail']),
				strip_tags($_POST['phonenumber']),
				md5($_POST['password']),
				"Seller Admin",
				"Not Active"
				// $cust_datetime,
				// $cust_timestamp,
				// 0
			));
			
			// Send email for confirmation of the account
		$to = $_POST['selleremail'];

		$subject = LANG_VALUE_150;
		$verify_link = BASE_URL . '/verify.php?email=' . $to . '&token=' . $token;
		$message = '' . LANG_VALUE_151 . '<br><br>

<a href="' . $verify_link . '">' . $verify_link . '</a>';

		$headers = "From: El School Connect noreply@" . BASE_URL . "\r\n" .
			"Reply-To: support@" . BASE_URL . "\r\n" .
			"X-Mailer: PHP/" . phpversion() . "\r\n" .
			"MIME-Version: 1.0\r\n" .
			"Content-Type: text/html; charset=ISO-8859-1\r\n";

		// Sending Email
		mail($to, $subject, $message, $headers);

		unset($_POST['CompanyName']);
		unset($_POST['selleremail']);
		unset($_POST['phonenumber']);


		$success_message = LANG_VALUE_152;
		}
		
	}
}
?>
<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Sign Up | El School Connect</title>

	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/font-awesome.min.css">
	<link rel="stylesheet" href="css/ionicons.min.css">
	<link rel="stylesheet" href="css/datepicker3.css">
	<link rel="stylesheet" href="css/all.css">
	<link rel="stylesheet" href="css/select2.min.css">
	<link rel="stylesheet" href="css/dataTables.bootstrap.css">
	<link rel="stylesheet" href="css/AdminLTE.min.css">
	<link rel="stylesheet" href="css/_all-skins.min.css">

	<link rel="stylesheet" href="style.css">
</head>

<body class="hold-transition login-page sidebar-mini">

	<div class="login-box">
		<div class="login-logo">
			<b>Sell on El School Connect</b>
		</div>
		<div class="login-box-body">
			<p class="login-box-msg">Sign Up From Here</p>

			<?php
			if ((isset($error_message)) && ($error_message != '')) :
				echo '<div class="error">' . $error_message . '</div>';
			endif;
			?>

			<form action="" method="post">
				<?php $csrf->echoInputField(); ?>
				<div class="form-group has-feedback">
					<input class="form-control" placeholder="Company Name" name="CompanyName" type="text" autocomplete="off" autofocus>
				</div>

				<div class="form-group has-feedback">
					<input class="form-control" placeholder="Phone Number" name="phonenumber" type="phone" autocomplete="off" autofocus>
				</div>
				<div class="form-group has-feedback">
					<input class="form-control" placeholder="Email address" name="selleremail" type="email" autocomplete="off" autofocus>
				</div>
				<div class="form-group has-feedback">
					<input class="form-control" placeholder="Password" name="password" type="password" autocomplete="off" value="">
				</div>
				<div class="form-group has-feedback">
					<input class="form-control" placeholder="Re-enter Password" name="password2" type="password" autocomplete="off" value="">
				</div>
				<div class="row" style="display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;">

					<div class="col-xs-12">
						<input type="submit" class="btn btn-success btn-block btn-flat login-button" name="form1" value="Finish Sign Up">
					</div>
					<br><br>
					<div class="col-xs-6"><button class="btn btn-success btn-lock btn-flat"><a href="login.php" style="color: #fff;">Log In from Here</a></button></div>
				</div>
			</form>
		</div>
	</div>


	<script src="js/jquery-2.2.3.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/jquery.dataTables.min.js"></script>
	<script src="js/dataTables.bootstrap.min.js"></script>
	<script src="js/select2.full.min.js"></script>
	<script src="js/jquery.inputmask.js"></script>
	<script src="js/jquery.inputmask.date.extensions.js"></script>
	<script src="js/jquery.inputmask.extensions.js"></script>
	<script src="js/moment.min.js"></script>
	<script src="js/bootstrap-datepicker.js"></script>
	<script src="js/icheck.min.js"></script>
	<script src="js/fastclick.js"></script>
	<script src="js/jquery.sparkline.min.js"></script>
	<script src="js/jquery.slimscroll.min.js"></script>
	<script src="js/app.min.js"></script>
	<script src="js/demo.js"></script>

</body>

</html>