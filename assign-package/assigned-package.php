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
$packages = $assignpackage->getPackage();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width initial-scale=1.0">
    <link rel="shortcut icon" href="../../docs-assets/ico/favicon.png">
    <title>Packages</title>
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
                                <img src="<?php echo $config['site_url']; ?>/images/profile-icon.png" class="m-r-10">Package </a>
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
                                        <a href="#" class="active pl-0"><i class="lnr lnr-home mr-1"></i></a>
                                        <a href="<?php echo $config['site_url']; ?>">Home</a>
                                        <a href="#">Package</a>
                                    </div>
									
                                </div>
              <!--Action boxes-->
				 <div class="profile-back">
						<div class="row">
						  <div class="widget-content nopadding">
						   <a href="<?php echo $config['site_url']; ?>/assign-package/assign-package.php" id="assign_package" class="btn btn-primary">Add New</a>
					       <h5>Assigned Packages</h5><br>
							<table id="dynamic-table" class="table table-bordered data-table">
																	<thead>
																		<tr>
																			
																			<th>SNo</th>
																			<th>Assigned User Name</th>
																			<th>Assigned User Email</th>
																			<th>Assigned Package</th>
																		</tr>
																	</thead>
																	<tbody>
																		<?php
																		if(!empty($packages)){
																			 foreach($packages as $package){?>
																		<tr>															
																			<td><?php echo $package['id'];?></td>
																			<td><?php echo $package['name'];?></td>
																			<td><?php echo $package['email'];?></td>
																			<td><?php echo $package['package'];?></td>
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