<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('memory_limit', '756M');

require( 'init.php' );
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width initial-scale=1.0">
    <link rel="shortcut icon" href="../../docs-assets/ico/favicon.png">
    <title>Package</title>
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
    <link rel="stylesheet" type="text/css" href="css/jquery.dataTables.min.css">
<!--===============================================================================================-->


   <!-- <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"> -->
    <script src="js/jquery-3.4.1.min.js"></script>
	<script src="https://unpkg.com/popper.js@1.15.0/dist/umd/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.4.0/css/bootstrap4-toggle.min.css" rel="stylesheet">
	<script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.4.0/js/bootstrap4-toggle.min.js"></script>
<?='<script>var firstSignIn = '.(isset($_GET['i']) ? 'true' : 'false').';</script>';?>
<!-- <link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.4.0/css/bootstrap4-toggle.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.4.0/js/bootstrap4-toggle.min.js"></script> -->
    <script src="js/bootstrap-notify.min.js"></script>
   <!-- <link href="css/main.min.css" rel="stylesheet"> -->
   <script src="js/jquery.dataTables.min.js"></script>
   <script src="js/coupon.min.js"></script>
	<style> 
	.error{
		border:1px solid red;
	}
	#coupon_error{
		color:red;
	}
	</style> 
</head>
<body>




<div class="main-area">

<div class="limiter">
        <div class="container-login100 p-0">
            <div class="container">
                <div class="header p-b-25 p-t-10">
                    <img src="images/inner-logo.png" alt="Logo">
                </div>
                <div id="nav-tab" role="tablist">
                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <a class="nav-link border-tab active" id="profileInfo" href="#">
                                <img src="images/profile-icon.png" class="m-r-10">Package </a>
                        </li>
                    </ul>
                </div>
                <div class="tab-content min-height-tab" id="nav-tabContent">
                    <div class="tab-pane main-area-profile active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                        <div class="row tab-back">
                            <div class="col-md-12 p-0">

                                <div class="title d-flex">
                                    <h3 class="d-inline-flex">Package</h3>
                                    <div class="breadcrumb flat d-inline-flex">
                                        <a href="#" class="active pl-0"><i class="lnr lnr-home mr-1" ></i></a>
                                        <a href="<?php echo $config['site_url']; ?>">Home</a>
                                        <a href="#">Package</a>
                                    </div>
									
                                </div>
                                <div class="profile-back">
									<?php $pack = $package_data[$_GET['package']];?>
									<h5>Apply Coupon Code</h5><br/>
									<div class="row">
										<div class="col-md-12 col-sm-12 col-xs-12 confim_input first_child">
											<input type="text" id="coupon_code" style="width:225px;display:inline" class="form-control" >
											<a href="javascript:void(0);" style="display:inline" id="apply_coupon" class="btn btn-primary">Apply</a>
											<span id="coupon_error" class="name-error"></span>
                                        </div>
									</div>	
									<br/>
                                    <ul class="profile_detail_list">
                                        <li class="first-li-item">
                                            <span class="profile_detail_key">Name :</span>
                                            <span class="profile_detail_value" id="profileName"><?= $pack['plan-name'];?></span>
                                        </li>
                                        <li>
                                            <span class="profile_detail_key">Plan Price :</span>
                                            <span class="profile_detail_value" id="profileEmail"><?= ($pack['plan-price'] == 0) ? 'Free' : (($pack['plan-price'] == '?') ? 'Contact Support' : '$'.$pack['plan-price']);?>/<?= ($pack['plan-period'] == 'monthly') ? 'Month' : 'Year';?></span>
                                        </li>
                                        <li>
                                            <span class="profile_detail_key">Sites :</span>
                                           <span class="profile_detail_value" id="profileIp"><?= $pack['sites'];?> Domains / Sites</span>
                                        </li>
                                        <li class="first-li-item">
                                            <span class="profile_detail_key">Monitored Links :</span>
                                           <span class="profile_detail_value" id="profileBacklinks"><?= $pack['monitored-links'];?> Monitored Links</span>
                                        </li>
                                        <li>
                                            <span class="profile_detail_key">Plan Period :</span>
                                            <span class="profile_detail_value" id="profileWebsites"><?= ($pack['plan-period'] == 'monthly') ? 'Monthly' : 'Yearly';?></span>
                                        </li>
                                        <li>
                                            <span style="color:green;" class="profile_detail_key">Thanks for signing up, please follow the activation link on your email then subscribe below.</span>
                                        </li>										
                                    </ul>
									<?php 
									if(!$pack['plan-price']): ?>
										<a href="<?php echo $config['site_url']; ?>/thankyou.php?cm=<?= $_GET['package'] ?>&tx=free"><input type="image" src="http://www.paypal.com/en_US/i/btn/btn_subscribe_LG.gif" border="0"  alt="Make payments with PayPal - it's fast, free and secure!">
									<?php else: ?>
									<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
										<input type="hidden" name="cmd" value="_xclick-subscriptions">
										<input type="hidden" name="business" value="payments@ndmedia.uk">
										<input type="hidden" name="item_name" value="<?= $pack['plan-name'];?>">
										<input type="hidden" name="currency_code" value="USD">
										<input type="hidden" name="lc" value="US">
										<input type="hidden" name="no_shipping" value="1">
										<input name = "rm" value = "2" type = "hidden">
										<input type="hidden" name="return" value="<?php echo $config['site_url']; ?>/thankyou.php">
										<input type="hidden" name="cancel_return" value="<?php echo $config['site_url']; ?>/cancel.php">
										<input type="image" src="http://www.paypal.com/en_US/i/btn/btn_subscribe_LG.gif" border="0" name="submit" alt="Make payments with PayPal - it's fast, free and secure!">
										<input type="hidden" id="planprice" name="a3" value="<?= $pack['plan-price'];?>.00">
										<input type="hidden" name="p3" value="1"> 
										<input type="hidden" name="t3" value="<?= ($pack['plan-period'] == 'monthly') ? 'M' : 'Y';?>">
										    <input type="hidden" id="custom" name="custom" value="<?= $_GET['package'];?>">
										<input type="hidden" name="src" value="1">
										<input type="hidden" name="sra" value="1">
										
									</form>
									<?php endif; ?>
									

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

</body>
</html>