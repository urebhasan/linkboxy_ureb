<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width initial-scale=1.0">
    <link rel="shortcut icon" href="../../docs-assets/ico/favicon.png">
    <title>Betfair</title>
    <!-- Bootstrap core CSS-->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="js/jquery-3.4.1.min.js"></script>
	<script src="https://unpkg.com/popper.js@1.15.0/dist/umd/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.4.0/css/bootstrap4-toggle.min.css" rel="stylesheet">
	<script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.4.0/js/bootstrap4-toggle.min.js"></script>

<!-- <link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.4.0/css/bootstrap4-toggle.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.4.0/js/bootstrap4-toggle.min.js"></script> -->
    <script src="js/bootstrap-notify.min.js"></script>
<style>
@import url(https://fonts.googleapis.com/css?family=Open+Sans+Condensed:300,700|Open+Sans:400,300,600);
* {
  box-sizing: border-box;
}
body {
  font-family: 'open sans', helvetica, arial, sans;
  background: url(background.png) no-repeat center center fixed;
  -webkit-background-size: cover;
  -moz-background-size: cover;
  -o-background-size: cover;
  background-size: cover;
}
.log-form {
  width: 40%;
  min-width: 320px;
  max-width: 475px;
  background: #fff;
  position: absolute;
  top: 50%;
  left: 50%;
  -webkit-transform: translate(-50%, -50%);
  -moz-transform: translate(-50%, -50%);
  -o-transform: translate(-50%, -50%);
  -ms-transform: translate(-50%, -50%);
  transform: translate(-50%, -50%);
  box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.25);
}
@media (max-width: 40em) {
  .log-form {
    width: 95%;
    position: relative;
    margin: 2.5% auto 0 auto;
    left: 0%;
    -webkit-transform: translate(0%, 0%);
    -moz-transform: translate(0%, 0%);
    -o-transform: translate(0%, 0%);
    -ms-transform: translate(0%, 0%);
    transform: translate(0%, 0%);
  }
}
.log-form form {
  display: block;
  width: 100%;
  padding: 2em;
}
.log-form h2 {
  color: #5d5d5d;
  font-family: 'open sans condensed';
  font-size: 1.35em;
  display: block;
  background: #2a2a2a;
  width: 100%;
  text-transform: uppercase;
  padding: .75em 1em .75em 1.5em;
  box-shadow: inset 0px 1px 1px rgba(255, 255, 255, 0.05);
  border: 1px solid #1d1d1d;
  margin: 0;
  font-weight: 200;
}
.log-form input {
  display: block;
  margin: auto auto;
  width: 100%;
  margin-bottom: 2em;
  padding: .5em 0;
  border: none;
  border-bottom: 1px solid #eaeaea;
  padding-bottom: 1.25em;
  color: #757575;
}
.log-form input:focus {
  outline: none;
}
.log-form .btn {
  display: inline-block;
  background: #1fb5bf;
  border: 1px solid #1ba0a9;
  padding: .5em 2em;
  color: white;
  margin-right: .5em;
  box-shadow: inset 0px 1px 0px rgba(255, 255, 255, 0.2);
}
.log-form .btn:hover {
  background: #23cad5;
}
.log-form .btn:active {
  background: #1fb5bf;
  box-shadow: inset 0px 1px 1px rgba(0, 0, 0, 0.1);
}
.log-form .btn:focus {
  outline: none;
}
.log-form .forgot {
  color: #33d3de;
  line-height: .5em;
  position: relative;
  top: 2.5em;
  text-decoration: none;
  font-size: .75em;
  margin: 0;
  padding: 0;
  float: right;
}
.log-form .forgot:hover {
  color: #1ba0a9;
}

.btn{
 cursor: pointer;
}

</style>
<script>
$(function(){
    $('#login_form').on('submit', function(e){
      e.preventDefault();
	  login = $('#login').val();
	  password = $('#password').val();
	  token = $('#token').val();
      		var request = $.ajax({
			url: "ajax/signIn.php",
			type: "POST",
			data: 'login='+login+'&password='+password+'&token='+token,
			dataType: "json",
			error: function () {alert('Ошибка 3229. Попробуйте позже.');},
			beforeSend: function () { },
			success: function (data) { 
				if(data['success'] === true){
					$.notify({message: data['text']},{placement:{from: 'bottom', align: 'center'}, type: 'info'});
					setTimeout(function() { 
						window.location.href = "main.php";
					}, 500);
				}else if(data['error'] === true){
					$.notify({message: data['text']},{placement:{from: 'bottom', align: 'center'}, type: 'danger'});	
				}
			},
				complete: function () {}
			});
    });
});
</script>
</head>
<body>
<div class="log-form">
  <h2>Backlinks Checker Sign In <i class="fa fa-sign-in" aria-hidden="true"></i></h2>
  <form id='login_form'>
    <input type="email" id="login" placeholder="Email" autocomplete="off" />
    <input type="password" id="password" placeholder="Password" autocomplete="off" />
	<input type="hidden" id="token" value="Rm9abWNrTkdVdA==" />
    <button type="submit" class="btn">Log in</button>
  </form>
</div><!--end log form -->
</body>
</html>