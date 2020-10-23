<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('memory_limit', '756M');

require( '../init.php' );
$coupon_name = ((isset($_POST['coupon_name'])) ? $_POST['coupon_name'] : null);
$coupon_date = ((isset($_POST['coupon_date'])) ? $_POST['coupon_date'] : null);
$coupon_discount = ((isset($_POST['coupon_discount'])) ? $_POST['coupon_discount'] : null);

if(isset($_POST['coupon_submit'])){
$coupon->createCoupon($coupon_name,$coupon_date,$coupon_discount);
header("Location:".$config['site_url']."/coupon/coupon.php");
}

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width initial-scale=1.0">
    <link rel="shortcut icon" href="../../docs-assets/ico/favicon.png">
    <title>Coupon</title>
    <!-- Bootstrap core CSS-->

<meta name="viewport" content="width=device-width, initial-scale=1">
<!--===============================================================================================-->  
    <link rel="icon" type="image/png" href="<?php echo $config['site_url']; ?>/images/favicon.ico"/> 
<!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="<?php echo $config['site_url']; ?>/css/bootstrap.min.css">
<!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="<?php echo $config['site_url']; ?>/fonts/font-awesome/css/font-awesome.min.css">
<!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="<?php echo $config['site_url']; ?>/fonts/linearicons/icon-font.min.css">
<!--===============================================================================================-->  
    <link rel="stylesheet" type="text/css" href="<?php echo $config['site_url']; ?>/css/hamburgers.min.css">
<!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="<?php echo $config['site_url']; ?>/css/select2.min.css">
<!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="<?php echo $config['site_url']; ?>/css/util.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $config['site_url']; ?>/css/style.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $config['site_url']; ?>/css/jquery.dataTables.min.css">
<!--===============================================================================================-->


   <!-- <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"> -->
    <script src="<?php echo $config['site_url']; ?>/js/jquery-3.4.1.min.js"></script>
	<script src="https://unpkg.com/popper.js@1.15.0/dist/umd/popper.min.js"></script>
    <script src="<?php echo $config['site_url']; ?>/js/bootstrap.min.js"></script>
    <link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.4.0/css/bootstrap4-toggle.min.css" rel="stylesheet">
	<script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.4.0/js/bootstrap4-toggle.min.js"></script>
<?='<script>var firstSignIn = '.(isset($_GET['i']) ? 'true' : 'false').';</script>';?>
<!-- <link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.4.0/css/bootstrap4-toggle.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.4.0/js/bootstrap4-toggle.min.js"></script> -->
    <script src="<?php echo $config['site_url']; ?>/js/bootstrap-notify.min.js"></script>
   <!-- <link href="css/main.min.css" rel="stylesheet"> -->
   <script src="<?php echo $config['site_url']; ?>/js/jquery.dataTables.min.js"></script>

</head>
<body>




<div class="main-area">

<div class="limiter">
        <div class="container-login100 p-0">
            <div class="container">
                <div class="header p-b-25 p-t-10">
                    <img src="<?php echo $config['site_url']; ?>/images/inner-logo.png" alt="Logo">
                </div>
                <div id="nav-tab" role="tablist">
                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <a class="nav-link border-tab active" id="profileInfo" href="#">
                                <img src="<?php echo $config['site_url']; ?>/images/profile-icon.png" class="m-r-10">Coupon </a>
                        </li>
                    </ul>
                </div>
				 <div class="tab-content min-height-tab" id="nav-tabContent">
                    <div class="tab-pane main-area-profile active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                        <div class="row tab-back">
                            <div class="col-md-12 p-0">
							<div class="title d-flex">
                                    <h3 class="d-inline-flex">Coupon</h3>
                                    <div class="breadcrumb flat d-inline-flex">
                                        <a href="#" class="active pl-0"><i class="lnr lnr-home mr-1"></i></a>
                                        <a href="<?php echo $config['site_url']; ?>">Home</a>
                                        <a href="#">Add Coupon</a>
                                    </div>
									
                                </div>
              <!--Action boxes-->
				 <div class="profile-back">
					<h5>Coupon Code</h5><br>
						<div class="row">
						  <div class="widget-content nopadding">
							  <form enctype="multipart/form-data"  id="coupon-form" class="form-horizontal ui-formwizard" action="" method="post" >
								  <div id="form-wizard-1" class="step ui-formwizard-content" style="display: block;">
									<div class="control-group">
									  <label class="control-label">Coupon Name</label>
									  <div class="controls">
										<input  type="text" value="" name="coupon_name" class="form-control" required />
									  </div>
									</div>
									<div class="control-group">
									  <label class="control-label">Date</label>
									  <div class="controls">
										<input type="text" name="coupon_date" class="form-control" id="js-date" value="" required />
									  </div>
									</div>
									<div class="control-group">
									  <label class="control-label">Discount (in %)</label>
									  <div class="controls">
										<input type="text" name="coupon_discount" class="form-control" id="js-date" value="" required />
									  </div>
									</div>
								  </div>
								 <br/>
								  <div class="form-actions">
									<input id="next" class="btn btn-primary ui-wizard-content ui-formwizard-button" type="submit" name="coupon_submit" value="Save">
									<div id="status"></div>
								  </div>
								  <div id="submitted"></div>
								</form>
						  </div>
						
				  </div>
				  </div>

            </div>
        </div>
    </div>

</div>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.js"></script>	
<script>
$(document).ready(function() {
    $('#js-date').datepicker({        format: "dd-mm-yyyy"
});
});
</script>
</body>
</html>