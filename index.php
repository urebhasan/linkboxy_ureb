<?php

 
require('init.php');
$freeplans = ['monthly-freebie', 'yearly-freebie'];
 if ($user->user_authorized() === true && isset($_GET['package'])) {
	 $user_info = $user->user_info_by_id();
	$curr_package = $user_info['package'];
	$curr_package = trim($curr_package);
	$package = trim($_GET['package']);
	if($curr_package == $package){
		header("Location: main.php?i");
		exit;
	}
	
	
	 if (!in_array($_GET['package'], $freeplans)) {
		header("Location: package.php?package=".$package);
		exit;
	}else{
		header("Location: main.php?i");
		exit;
	}
	//header("Location: https://app.linkboxy.com/staging/package.php?package=".$_GET['package']);
	//header("Location: ".$config['site_url']."/package.php?package=".$_GET['package']);
	//header("Location:".$config['site_url']."/assign-package/assigned-package.php");
}

if ($user->user_authorized() === true){
	    header("Location: main.php?i");
         exit;
} 
$activation = ((isset($_GET['activation']) and strlen($_GET['activation']) == 32 && ctype_xdigit($_GET['activation'])) ? $_GET['activation'] : null);

if ($activation != null and $activated = $user->activateAccount($activation)) {
    $stmt = $common->mysqli->prepare("UPDATE users SET ip = INET_ATON(?) WHERE id=? LIMIT 1");
    $stmt->bind_param("si", $_SERVER['REMOTE_ADDR'], $activated);
    $stmt->execute();
    $stmt->fetch();
    $stmt->close();
	$package = $_GET['package'];
	
    if ($user->authUserById($activated)) {
		$user_info = $user->user_info_by_id();
		$curr_package = $user_info['package'];
		$curr_package = trim($curr_package);
		$package = trim($package);
		if($curr_package == $package){
			header("Location: main.php?i");
			exit;
		}
		if (!in_array($package, $freeplans)) {
			header("Location: package.php?package=".$package);
			exit;
		}else{
			header("Location: main.php?i");
			exit;
		}
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width initial-scale=1.0">
    <link rel="shortcut icon" href="../../docs-assets/ico/favicon.png">
    <title>Backlinks Checker</title>
    <!-- Bootstrap core CSS-->

<meta name="viewport" content="width=device-width, initial-scale=1">
<!--===============================================================================================-->
    <link rel="icon" type="image/png" href="images/favicon.ico"/>
<!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
<!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="fonts/font-awesome/css/font-awesome.min.css">
<!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="fonts/linearicons/icon-font.min.css">
<!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="css/hamburgers.min.css">
<!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="css/select2.min.css">
<!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="css/util.css">
    <link rel="stylesheet" type="text/css" href="css/style.css">
<!--===============================================================================================-->

    <!--===============================================================================================-->
    <!--<link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/index.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">-->
    <script src="js/jquery-3.4.1.min.js"></script>
    <script src="https://unpkg.com/popper.js@1.15.0/dist/umd/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.4.0/css/bootstrap4-toggle.min.css"
          rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.4.0/js/bootstrap4-toggle.min.js"></script>

    <!-- <link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.4.0/css/bootstrap4-toggle.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.4.0/js/bootstrap4-toggle.min.js"></script> -->
    <script src="js/bootstrap-notify.min.js"></script>

    <script src="js/index.min.js"></script>
</head>
<body>
    <div class="limiter">
        <div class="container-login100">
            <div class="wrap-login100 p-l-100 p-r-100 p-t-40 p-b-30">

                    <span class="login100-form-title p-b-40">
                        <img src="images/logo.png" alt="Logo">
                    </span>
                    <div class="log-form">
                    <form class="login100-form" id="login_form">
                    <span class="login100-form-title p-b-40">
                        Login to Admin
                    </span>

                    <div class="wrap-input100 validate-input m-b-16">

                        <input class="input100" type="email" id="login" placeholder="Email" autocomplete="off"/>
                        <span class="focus-input100"></span>
                        <span class="symbol-input100">
                            <span class="lnr lnr-envelope"></span>
                        </span>
                    </div>

                    <div class="wrap-input100 validate-input m-b-16">

                        <input type="password" id="password" class="input100" placeholder="Password" autocomplete="off"/>

                        <span class="focus-input100"></span>
                        <span class="symbol-input100">
                            <span class="lnr lnr-lock"></span>
                        </span>
                         <input type="hidden" id="token" value="Rm9abWNrTkdVdA=="/>
                    </div>

                    <div class="contact100-form-checkbox m-l-4">
                        <input class="input-checkbox100" id="ckb1" type="checkbox" name="remember-me">
                        <label class="label-checkbox100" for="ckb1">
                            Remember
                        </label>



                    </div>
                    <div class="text-right forgot-text">
                    <a href="#" id="forgotPassword" class="txt1 small">
                    Forgot your password?
                    </a>
                    </div>

                    <div class="container-login100-form-btn p-t-40">
                        <button type="submit" class="login100-form-btn">
                            Sign In
                        </button>
                    </div>

                    <div class="text-center w-full p-t-42 p-b-22">
                        <span class="txt1 small">
                            Dont have an account?
                        </span>
						<a id="signUp"></a>
                        <a class="txt1 link small text-medium"  href="https://linkboxy.com">
                            Sign up
                        </a>
                    </div>
                </form>
            </div>


<div class="forgot-form">

 <form class="login100-form" id="forgot_form">
    <span class="login100-form-title p-b-40">
                        Enter your email id to recovered <br>
your password
                    </span>

                    <div class="wrap-input100 validate-input m-b-16">

                         <input type="email"  class="input100" id="forgot_login" placeholder="Email" autocomplete="off"/>
                        <span class="focus-input100"></span>
                        <span class="symbol-input100">
                            <span class="lnr lnr-envelope"></span>
                        </span>
                    </div>

                    <div class="text-center w-full p-t-20 p-b-20">
                        <a class="txt1 bo1 hov1 small info-link signIn" href="#">
                            Already have an account? Sign in!
                        </a>
                    </div>
                    <div class="container-login100-form-btn p-t-25">
                        <button type="submit" class="login100-form-btn">Retrieve password</button>
                    </div>

                </form>

</div>
<div class="signup-form">

<form class="login100-form" id="signup_form">
<span class="login100-form-title p-b-40">
                        Create your account
                    </span>
                    <div class="wrap-input100 validate-input m-b-16">

                        <input type="text" class="input100" id="name_signup" placeholder="Name" autocomplete="off"/>
                        <span class="focus-input100"></span>
                        <span class="symbol-input100">
                            <span class="lnr lnr-user"></span>
                        </span>
                    </div>
                    <div class="wrap-input100 validate-input m-b-16">
                      <input type="email" class="input100" id="login_signup" placeholder="Email" autocomplete="off"/>
                        <span class="focus-input100"></span>
                        <span class="symbol-input100">
                            <span class="lnr lnr-envelope"></span>
                        </span>
                    </div>

                    <div class="wrap-input100 validate-input m-b-16">
                       <input type="password"  class="input100" id="password_signup" placeholder="Password" autocomplete="off"/>
                        <span class="focus-input100"></span>
                        <span class="symbol-input100">
                            <span class="lnr lnr-lock"></span>
                        </span>
                    </div> 
				<?php if(isset($_GET['package'])): ?>
					<div class="wrap-input100 m-b-16 text-center">
                       <label>Package : <?php echo $package_data[$_GET['package']]['plan-name']; ?></label>
                       <input type="hidden" id="package" name="package" value="<?php echo $_GET['package']; ?>"/>
                    </div>
				<?php endif; ?>
                    <div class="container-login100-form-btn p-t-25">
                        <button type="submit" class="login100-form-btn">
                            Create an Account
                        </button>
                    </div>

                    <div class="text-center w-full p-t-42 p-b-22">
                        <span class="txt1 small">
                            Already have an account?
                        </span>

                        <a class="txt1 link small text-medium info-link signIn" href="#">
                            Sign In
                        </a>
                    </div>

                  </form>

</div>


  <form id = "paypal_checkout" style="display:none;" action = "https://www.sandbox.paypal.com/cgi-bin/webscr" method = "post">
 		<input type="hidden" name="cmd" value="_xclick-subscriptions">
		<input type="hidden" name="business" value="sb-k2j5f894099@business.example.com">
		<input type="hidden" name="item_name" value="">
		<input type="hidden" name="currency_code" value="USD">
		<input type="hidden" name="lc" value="US">
		<input type="hidden" name="no_shipping" value="1">
		<input name = "rm" value = "2" type = "hidden">
		<input type="hidden" name="return" value="<?php echo $config['site_url']; ?>/thankyou.php">
		<input type="hidden" name="cancel_return" value="<?php echo $config['site_url']; ?>/cancel.php">
		<input type="image" src="http://www.sandbox.paypal.com/en_US/i/btn/btn_subscribe_LG.gif" border="0" name="submit" alt="Make payments with PayPal - it's fast, free and secure!">
		<input type="hidden" name="a3" value="">
		<input type="hidden" name="p3" value="1"> 
		<input type="hidden" name="t3" value="">
			<input type="hidden" name="custom" value="">
		<input type="hidden" name="src" value="1">
		<input type="hidden" name="sra" value="1">
    
</form>

            </div>
        </div>
    </div>
	<?php if(isset($_GET['package'])): ?>
		<script>
		$(document).ready(function(){
			$('#signUp').trigger('click');
		});
		</script>
	<?php endif; ?>
</body>
</html>