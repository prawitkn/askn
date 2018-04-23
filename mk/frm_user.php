<?php
  //  include '../db/database.php';
?>
<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<?php include 'head.php'; ?>
    
<!--
BODY TAG OPTIONS:
=================
Apply one or more of the following classes to get the
desired effect
|---------------------------------------------------------|
| SKINS         | skin-blue                               |
|               | skin-black                              |
|               | skin-purple                             |
|               | skin-yellow                             |
|               | skin-red                                |
|               | skin-green                              |
|---------------------------------------------------------|
|LAYOUT OPTIONS | fixed                                   |
|               | layout-boxed                            |
|               | layout-top-nav                          |
|               | sidebar-collapse                        |
|               | sidebar-mini                            |
|---------------------------------------------------------|
-->
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <!-- Main Header -->
  <?php include 'header.php'; ?>
  
  <!-- Left side column. contains the logo and sidebar -->
   <?php include 'leftside.php'; ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
       Add User Information
        <small>User management</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="customer.php"><i class="fa fa-dashboard"></i>User Information</a></li>
        <li class="active">Add User Information</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

      <!-- Your Page Content Here -->
      
    <div class="box box-primary">
        <div class="box-header with-border">
        <h3 class="box-title">Add User..</h3>
        <div class="box-tools pull-right">
          <!-- Buttons, labels, and many other things can be placed here! -->
          <!-- Here is a label for example -->
         
        </div><!-- /.box-tools -->
        </div><!-- /.box-header -->
        <div class="box-body">
            
            <div class="row">
                <div class="col-md-6">
                    <form id="form1" action="insert_user.php" method="post" class="form" enctype="multipart/form-data" novalidate>
                        <div class="form-group">
                            <label for="userFullname">userFullname</label>
                            <input id="userFullname" type="text" class="form-control" name="userFullname" data-smk-msg="Require userFullname."required>
                        </div>
                        
                        <div class="form-group">
                            <label for="userName">Username</label>
                            <input id="userName" type="text" class="form-control" name="userName" data-smk-msg="Require userName" required>
                        </div>
                        <div class="form-group">
                            <label for="userPassword">userPassword</label>
                            <input id="userPassword" type="password" class="form-control" name="userPassword" data-smk-msg= "Require userPassword" required>
                        </div>
                        <div class="form-group">
                            <label for="userEmail">userEmail</label>
                            <input id="userEmail" type="email" class="form-control" name="userEmail" data-smk-msg="Require userEmail" required>
                        </div>
                        <div class="form-group">
                            <label for="userTel">Telephone</label>
                            <input id="userTel" type="text" class="form-control" name="userTel" data-smk-msg="Require Telephone number" required>                        </div>
                        </div>
                        <div class="form-group">
                            <label for="userPicture">Choose personal picture file input.</label>
                            <input type="file" id="userPicture" name="userPicture">
                            <p class="help-block">Please select picture file .jpg, .png, .gif</p>
                        </div>
                        <button id="btn1" type="submit" class="btn btn-default">Submit</button>
                    </form>
                </div>
                        
            </div>
          
    
    </div><!-- /.box-body -->
  <div class="box-footer">
      
      
    <!--The footer of the box -->
  </div><!-- box-footer -->
</div><!-- /.box -->

<div id="spin"></div>

    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Main Footer -->
  <?php include'footer.php'; ?>
  
  
</div>
<!-- ./wrapper -->

<!-- REQUIRED JS SCRIPTS -->

<!-- jQuery 2.2.3 -->
<script src="plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="bootstrap/js/bootstrap.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/app.min.js"></script>

<script src="bootstrap/js/smoke.min.js"></script>

<!-- Add Spinner feature -->
<script src="bootstrap/js/spin.min.js"></script>

<script> 
  // to start and stop spiner.  
        $( document ).ajaxStart(function() {
        $("#spin").show();
        }).ajaxStop(function() {
            $("#spin").hide();
        });
  //   
  
       $(document).ready(function() {
    
            $("#userFullname").focus();
            
          
            var spinner = new Spinner().spin();
            $("#spin").append(spinner.el);
            $("#spin").hide();
  //           
             $('#form1').on("submit", function(e) {
                if ($('#form1').smkValidate()) {
                 
  //                alert("submit ok");
                    $.smkAlert({
                    text: 'Validate OK',
                    type: 'success',
                   position:'top-left'
               });      
            
            $.ajax({
                        url: 'insert_user.php',
                        type: 'POST',
                        data: new FormData( this ),
                        processData: false,
                        contentType: false,
                        dataType: 'json'
                    }).done(function (data) {
                         if (data.status === "success"){                  
                         alert("data.status  success");       
                             $.smkAlert({
                             text: data[1],
                             type: data.status,
                             position:'top-center'
                             });
                             } else {
                                 $.smkAlert({
                                 text: data.message,
                                 type: data.status,
                                 text: 
       //                        position:'top-center'
                                 });
                                 }
                                 
                                 $('#form1')[0].reset();
                                 $("#userFullname").focus(); 
                    });    
                    e.preventDefault();
                }   
               e.preventDefault();
            });
            
           


            
             });
        
        
   
  </script>

<!-- Optionally, you can add Slimscroll and FastClick plugins.
     Both of these plugins are recommended to enhance the
     user experience. Slimscroll is required when using the
     fixed layout. -->
</body>
</html>
