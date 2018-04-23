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
       Add Customer Information
        <small>Customer management</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="customer.php"><i class="fa fa-dashboard"></i>Customer Information</a></li>
        <li class="active">Add Customer Information</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

      <!-- Your Page Content Here -->
      
    <div class="box box-primary">
        <div class="box-header with-border">
        <h3 class="box-title">Add Customers..</h3>
        <div class="box-tools pull-right">
          <!-- Buttons, labels, and many other things can be placed here! -->
          <!-- Here is a label for example -->
         
        </div><!-- /.box-tools -->
        </div><!-- /.box-header -->
        <div class="box-body">
            
            <div class="row">
                <div class="col-md-6">
                    <form id="form1" action="insert_cust.php" method="post" class="form" novalidate>
                        <div class="form-group">
                            <label for="custName">Customer Name</label>
                            <input id="custName" type="text" class="form-control" name="custName" data-smk-msg="Require Customer Name."required>
                        </div>
                        <div class="form-group">
                            <label for="custAddr">Address</label>
                            <textarea id="custAddr" name="custAddr" class="form-control" rows="4" data-smk-msg="Require Address" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="custUsername">Customer Username</label>
                            <input id="custUsername" type="text" class="form-control" name="custUsername" data-smk-msg="Require Customer Username" required>
                        </div>
                        <div class="form-group">
                            <label for="custPassword">Password</label>
                            <input id="custPassword" type="password" class="form-control" name="custPassword" data-smk-msg= "Require Password" required>
                        </div>
                        <div class="form-group">
                            <label for="custEmail">Email</label>
                            <input id="custEmail" type="email" class="form-control" name="custEmail" data-smk-msg="Require Email" required>
                        </div>
                        <div class="form-group">
                            <label for="custTel">Telephone</label>
                            <input id="custTel" type="text" class="form-control" name="custTel" data-smk-msg="Require Telephone number" required>                        </div>
                        
                        
                        <button id="btn1" type="button" class="btn btn-default">Submit</button>
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
    //       alert("jquery ok");
            $("#custName").focus();
            
  // Append and Hide spinner.          
            var spinner = new Spinner().spin();
            $("#spin").append(spinner.el);
            $("#spin").hide();
  //           
             $('#btn1').click (function(e) {
                if ($('#form1').smkValidate()) 
              {
   //                 alert("submit ok");
                    $.smkAlert({
                    text: 'Validate OK',
                    type: 'success',
                   position:'top-left'
               });     
    //             alert("after if ok");
            
                  $.post("insert_cust.php", $("#form1").serialize() )
                              .done(function(data) {
    //                         alert(data.status); 
                                   if (data.status === "success"){                  
                                
                                       $.smkAlert({
                                        text: data.message,
                                        type: data.status,
                                        position:'top-center'
                                        });
                                 } else {
                                        $.smkAlert({
                                        text: data.message,
                                        type: data.status,
       //                                 position:'top-center'
                                        });
                                 }
                                 
                                 $('#form1').smkClear();
                                 $("#custName").focus();
                              });  
                              
               e.preventDefault();
                }   
               e.preventDefault();
            });
            
            
            $( "#custUsername" ).on("blur",function(e) {
   //          alert( "Keyup OK" );
               $.get("check_username.php",{custUsername: $("#custUsername").val()})
                       .done(function(data) {  
                            if (data.status === "active") {
                            alert(data.message);
                            $("#custUsername").val('');
                            $("#custUsername").focus();
                            }
               
                       });
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
