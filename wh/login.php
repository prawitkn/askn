<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Login AK-WMS | Log in</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
  
  
  <!-- Theme style -->
   <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="plugins/iCheck/square/blue.css">

  <link rel="stylesheet" href="bootstrap/css/smoke.min.css">
  
  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>
<body class="hold-transition login-page" style="background-color: #85e085; ">
<div class="login-box">
  <div class="login-logo">
    <a href="../../index2.html"><b>AK</b> Warehouse Managment Systems</a>
  </div>
  <!-- /.login-logo -->
  <div class="login-box-body">
  <?php  //echo $_COOKIE['SID']; ?>
    <p class="login-box-msg">Sign in to start your session</p>
    <form id="form1" action="login_go.php" method="post" novalidate>
      <div class="form-group has-feedback">
        <input type="text" id="userName" name="userName"  class="form-control" placeholder="User Name" data-smk-msg="Require name." required>
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <input type="password" id="userPassword" name="userPassword"  class="form-control" placeholder="Password" data-smk-msg="Require password." required>
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
      <div class="row">
        <div class="col-xs-6">
            <a href="../" class="btn btn-default btn-block btn-flat">Return</a>
        </div>
        
        
        <div class="col-xs-6">
          <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
        </div>
        <!-- /.col -->
      </div>
    </form>

    
  </div>
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->

<!-- jQuery 2.2.3 -->
<script src="plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="bootstrap/js/bootstrap.min.js"></script>

 
<script src="bootstrap/js/smoke.min.js"></script>
<script>
    $(document).ready(function() {
        
        $("#userName").focus();
        
        $('#form1').on("submit",function(e) {
     
           if($('#form1').smkValidate()) {
    //alert("Ok validate");      
                $.post("login_go.php", $("#form1").serialize() )
                        .done(function(data) { //alert(data);
                            if (data.status === "danger") {
    // alert("danger message");            
                                $.smkAlert({text: data.message, type: data.status});
                                $('#form1').smkClear();
                                $("#userName").focus();
                             }else{
                                 $(location).attr('href', 'index.php');
                             }
                        });         
                e.preventDefault();               
           }            
            e.preventDefault();
        });
        
    });
</script>
</body>
</html>
