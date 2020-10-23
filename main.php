<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('memory_limit', '756M');

require( 'init.php' );

if($user->user_authorized() !== true) header("Location: index.php");
$user_info = $user->user_info_by_id();
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
<script src='js/main.min.js'></script>

</head>
<body>
<!-- Add backlinks modal -->
<div class="modal fade" id="websitesAddModal" tabindex="-1" role="dialog" aria-labelledby="websitesAddModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
      
      <div class="modal-body">
        <div class="wrap-login100 p-l-100 p-r-100 p-t-40 p-b-40 passwordChangeWrap">
           <span class="login100-form-title p-b-40">
            <img src="images/logo.png" alt="Logo">
          </span>
          <span class="login100-form-title p-b-40">
            Add Your Website URL <br> (Including The Trailing Slash)
          </span>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <div class="input-group mb-3">
          <input type="text" placeholder="https://www.domain.com/" class="input100" id="domainToAdd" aria-describedby="basic-addon3">
        <span class="focus-input100"></span>
            <span class="symbol-input100">
              <span class="lnr lnr-link"></span>
            </span>
        </div>

        <div class="container-login100-form-btn p-t-40">
            <button type="submit" data-dismiss="modal" id="addDomain" class="login100-form-btn">
             Add domain
            </button>
          </div>
      </div>
    </div>
    
    
  </div>
</div>
<!-- Add backlinks modal -->
<div class="modal fade" id="backlinksAddModal" tabindex="-1" role="dialog" aria-labelledby="backlinksAddModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
   
      
      <div class="modal-body">
         <div class="wrap-login100 p-l-100 p-r-100 p-t-40 p-b-40 passwordChangeWrap">
           <span class="login100-form-title p-b-40">
            <img src="images/logo.png" alt="Logo">
          </span>
          <span class="login100-form-title p-b-40">
            Add your backlinks
          </span>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>

        <div class="wrap-input100 m-b-30">
          <div class="input-group-prepend">
            <label class="input-group-text" for="backlinksWebsite">For domain</label>
          </div>
          <select class="custom-select wrap-input100" id="backlinksWebsite">
          </select>
        </div>

          <div class="wrap-input100">
            <div class="input-group-prepend">
              <span class="input-group-text">One backlink per line</span>
            </div>
            <textarea class="form-control" rows="10" aria-label="" id="backlinksValue"></textarea>
          </div>
<div class="container-login100-form-btn p-t-40 justify-content-center">
           <button type="button" class="login100-form-btn m-b-30 d-inline-flex w-170 m-r-10" data-dismiss="modal">Close</button>
        <button type="button" class="login100-form-btn d-inline-flex w-170" data-dismiss="modal" id="saveBacklinks">Save changes</button>
      </div>
        </div>
      </div>
    
   
  </div>
</div>


<!-- Add Forgot-password modal -->
<div class="modal fade" id="forgotPass" tabindex="-1" role="dialog" aria-labelledby="backlinksAddModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
   
      <div class="modal-body">
    
<div class="wrap-login100 p-l-100 p-r-100 p-t-40 p-b-40 passwordChangeWrap">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
         <span class="login100-form-title p-b-40">
            <img src="images/logo.png" alt="Logo">
          </span>
          <span class="login100-form-title p-b-40">
            Change your password
          </span>
        <form id="changePasswordForm" class="login100-form validate-form">
          <div class="wrap-input100 validate-input m-b-16" data-validate = "Password is required">
            <input id="oldP" type="pasword" class="input100" placeholder="Old password" autocomplete="off">
            <span class="focus-input100"></span>
            <span class="symbol-input100">
              <span class="lnr lnr-lock"></span>
            </span>
          </div>
           <div class="wrap-input100 validate-input m-b-16" data-validate = "Password is required">
            <input id="newP" class="input100" type="password" placeholder="New password" autocomplete="off">
            
            <span class="focus-input100"></span>
            <span class="symbol-input100">
              <span class="lnr lnr-lock"></span>
            </span>
          </div>

          <div class="wrap-input100 validate-input m-b-16" data-validate = "Password is required">
            <input id="newPRepeat" class="input100" type="password" placeholder="Repeat new password" autocomplete="off">
            <span class="focus-input100"></span>
            <span class="symbol-input100">
              <span class="lnr lnr-lock"></span>
            </span>
          </div>
          <div class="container-login100-form-btn p-t-40">
            <button type="submit"  id="changePassword" class="login100-form-btn">
             Change Password
            </button>
          </div>
        </form>
      </div>
         
     
    
    </div>
  </div>
</div>



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
                                <img src="images/profile-icon.png" class="m-r-10">Profile </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link border-tab" id="manageWebsites" href="#">
                                <img src="images/domains.png" class="m-r-10">Manage Your Sites</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link border-tab" id="manageBacklinks" href="#">
                                <img src="images/manage-backlink.png" class="m-r-10">Manage Backlinks</a>
                        </li>
                        <?php if(in_array($user_info['email'],$admin_email)): ?>
                          <li class="nav-item">
                            <a class="nav-link border-tab" id="manageBacklinks" href="<?php echo $config['site_url']?>/coupon/coupon.php">
                                <img src="images/manage-backlink.png" class="m-r-10">Manage Coupon</a>
                        </li>
						<li class="nav-item">
                            <a class="nav-link border-tab" id="manageBacklinks" href="<?php echo $config['site_url']?>/assign-package/assigned-package.php">
                                <img src="images/manage-backlink.png" class="m-r-10">Assign Package</a>
                        </li>
                        <?php endif; ?>
                        <li class="nav-item">
                            <button class="nav-link border-tab" type="button" id="signOut">
                                <img src="images/signout.png" class="m-r-10">Sign out</button>
                        </li>
                    </ul>
                </div>
                <div class="tab-content min-height-tab" id="nav-tabContent">
                    <div class="tab-pane main-area-profile" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                        <div class="row tab-back">
                            <div class="col-md-12 p-0">

                                <div class="title d-flex">
                                    <h3 class="d-inline-flex">Profile</h3>
                                    <div class="breadcrumb flat d-inline-flex">
                                        <a href="#" class="active pl-0"><i class="lnr lnr-home mr-1" ></i></a>
                                        <a href="<?php echo $config['site_url']; ?>">Home</a>
                                        <a href="#">Profile</a>
                                    </div>
									
                                </div>
								
                                <div class="profile-back">
                                    <ul class="profile_detail_list">
                                        <li class="first-li-item">
                                            <span class="profile_detail_key">Name :</span>
                                            <span class="profile_detail_value" id="profileName"><?=$user_info['name'];?></span>
                                        </li>
                                        <li>
                                            <span class="profile_detail_key">Email :</span>
                                            <span class="profile_detail_value" id="profileEmail"><?=$user_info['email'];?></span>
                                        </li>
                                        <li>
                                            <span class="profile_detail_key">IP :</span>
                                           <span class="profile_detail_value" id="profileIp"><?=long2ip($user_info['ip']);?></span>
                                        </li>
                                        <li class="first-li-item">
                                            <span class="profile_detail_key">Link :</span>
                                           <span class="profile_detail_value" id="profileBacklinks"><?=$user_info['user_backlinks'];?></span>
                                        </li>
                                        <li>
                                            <span class="profile_detail_key">Sites :</span>
                                            <span class="profile_detail_value" id="profileWebsites"><?=$user_info['user_websites'];?></span>
                                        </li> 
										<li>
                                            <span class="profile_detail_key">Current Package :</span>
                                            <span class="profile_detail_value" id="profileWebsites"><?= isset($user_info['package']) ? $package_data[$user_info['package']]['plan-name'] : 'N/A';?></span>
											&nbsp;&nbsp;&nbsp;&nbsp; <a class="btn btn-primary" href="https://linkboxy.com">Upgrade Package</a>
                                        </li>
										<li>
                                            <span class="profile_detail_key">Quick Setup :</span>
                                            <span class="profile_detail_value">Click the Manage Your Sites tab above to add your sites, then Manage Backlinks to add the links to them :)
                                        </li>										
                                    </ul>
                                    
                                    <button class="login100-form-btn" id="forgotPass" data-toggle="modal" data-target="#forgotPass">Change Password</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane main-area-websites" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                        <div class="row tab-back">
                            <div class="col-md-12 p-0">
                                <div class="title m-b-30 d-flex">
                                    <h3 class="d-inline-flex">Domains</h3>
                                    <div class="breadcrumb d-inline-flex flat">
                                        <a href="#" class="active pl-0"><span class="lnr lnr-home mr-1"></span></a>
                                        <a href="<?php echo $config['site_url']; ?>">Home</a>
                                        <a href="#">Domains</a>
                                    </div>
                                </div>
                                <div class="box-shadow-set m-t-40">
                                    <div class="tit-header">
                                        <h1 class="sub-title d-inline domain-title">Your Websites</h1>
                                        <div class="add-button float-right d-inline">
                                            <button id="addWebsite" data-toggle="modal" data-target="#websitesAddModal"><i class="fa fa-plus"></i><span>Add</span></button>
                                        </div>
                                    </div>
                                    <div class="table-responsive mt-60">

                                                 

                                                  <div class="content">

                                                  </div>
                                                 


                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane main-area-backlinks" id="nav-contact" role="tabpanel" aria-labelledby="nav-contact-tab">
                        <div class="row tab-back">
                            <div class="col-md-12 p-0">
                                <div class="title m-b-30 d-flex">
                                    <h3 class="d-inline-flex">Backlink</h3>
                                    <div class="breadcrumb d-inline-flex flat">
                                        <a href="#" class="active pl-0"><span class="lnr lnr-home mr-1" ></span></a>
                                        <a href="<?php echo $config['site_url']; ?>">Home</a>
                                        <a href="#">Backlink</a>
                                    </div>
                                </div>
                                <div class="box-shadow-set m-t-40">
                                    <div class="tit-header">
                                        <h1 class="sub-title d-inline">Backlink List</h1>
                                        <div class="add-button float-right d-inline">
                                            <button id="addBacklinks" data-toggle="modal" data-target="#backlinksAddModal"><i class="fa fa-plus"></i><span>Add</span></button>
                                            <button onclick="location.href = '<?php echo $common->domain . '/export.php'; ?>'" type="button">Export CSV</button>
                                        </div>
                                    </div>
                                    <div class="table-responsive mt-60">
                                        <div class="content">

										</div>
                                    </div>
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