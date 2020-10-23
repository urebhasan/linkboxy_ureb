<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('memory_limit', '756M');

require( '../init.php' );
if($user->user_authorized() !== true) header("Location: index.php");
$user_info = $user->user_info_by_id();
if(!in_array($user_info['email'],$admin_email)){
    //header("Location: https://app.linkboxy.com/staging/main.php?i");
	header("Location:".$config['site_url']."/main.php?i");
    exit;
}
$coupons = $coupon->getCoupon();
if(isset($_GET['action']) && $_GET['action'] == 'delete')
	$coupon->deleteCoupon($_GET['id']);

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
                                        <a href="#">Coupon</a>
                                    </div>
									
                                </div>
              <!--Action boxes-->
				 <div class="profile-back">
				 	<a href="<?php echo $config['site_url']; ?>/coupon/add_coupon.php" id="apply_coupon" class="btn btn-primary">Add New</a>
					<h5>Coupon Code</h5><br>
						<div class="row">
						  <div class="widget-content nopadding">
							<table id="dynamic-table" class="table table-bordered data-table">
																	<thead>
																		<tr>
																			
																			<th>Sno</th>
																			<th>Coupon Code</th>
																			<th>Coupon From Date</th>
																			<th>Coupon To Date</th>
																			<th>Coupon Discount(in %)</th>
																			<th>
																				<i class="ace-icon fa fa-clock-o bigger-110"></i>
																				Action
																			</th>
																		</tr>
																	</thead>
																	<tbody>
																		<?php
																		if(!empty($coupons)){
																			 foreach($coupons as $coup){?>
																		<tr>															
																			<td><?php echo $coup['ID'];?></td>
																			<td><?php echo $coup['coupon_name'];?></td>
																			<td><?php echo date('d-m-Y',time($coup['coupon_date']));?></td>
																			<td><?php echo $coup['coupon_date'];?></td>
																			<td><?php echo $coup['coupon_discount'];?></td>
																			<td>
																			
																			<div class="hidden-sm hidden-xs action-buttons">
																				 
																				<a class="btn btn-danger" onclick="return confirm('Once deleted cant be recover again...');" href="<?php echo $config['site_url']; ?>/coupon/coupon.php?id=<?php echo $coup['ID'];?>&action=delete">
																					Delete
																				</a>
																			</div>													
																		</td>
																		</tr>
																		<?php } 
																		} ?>														
																	</tbody>
															</table>
						  </div>
						
				  </div>
				  </div>

            </div>
        </div>
    </div>

</div>

</body>
</html>