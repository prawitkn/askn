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
       Add Product Types
        <small>Product type management</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="product_type.php"><i class="fa fa-dashboard"></i> Product Types</a></li>
        <li class="active">Add Product Type</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

      <!-- Your Page Content Here -->
      
    <div class="box box-primary">
        <div class="box-header with-border">
        <h3 class="box-title">Add Product Types..</h3>
        <div class="box-tools pull-right">
          <!-- Buttons, labels, and many other things can be placed here! -->
          <!-- Here is a label for example -->
         
        </div><!-- /.box-tools -->
        </div><!-- /.box-header -->
        <div class="box-body">
            
            <div class="row">
                <div class="col-md-6">
                    <form id="form1" action="insert_pt.php" method="post" class="form">
                        <div class="form-group">
                            <label for="prodTypeID">Product Type ID</label>
                            <input id="prodTypeID" type="text" class="form-control" name="prodTypeID" placeholder="Product Type ID">
                        </div>
                        <div class="form-group">
                            <label for="prodTypeName">Product Type Name</label>
                            <input id="prodTypeName" type="text" class="form-control" name="prodTypeName" placeholder="Product Type Name">
                        </div>
                        <button type="submit" class="btn btn-default">Submit</button>
                    </form>
                </div>           
            </div>
          
    
    </div><!-- /.box-body -->
  <div class="box-footer">
      
      
    <!--The footer of the box -->
  </div><!-- box-footer -->
</div><!-- /.box -->

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

<script> 
        $(document).ready(function() 
        {
            //alert("jquery ok");
            $("#prodTypeID").focus();
            $("#form1").on("submit",function(e)
            {
                if ($("#prodTypeID").val() === '' || $("#prodTypeName").val() === '' )
                {
                    alert("Please fill out all the required information.");
                    e.preventDefault();
                }   
                    else
                    {
                      $.post("insert_pt.php",{prodTypeID: $("#prodTypeID").val(), prodTypeName: $("#prodTypeName").val() })
                              .done(function( data ) {
                    //             alert(data.status); 
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
                                        position:'top-center'
                                        });
                                 }
                                 
                                 $('#form1').smkClear();
                                 $("#prodTypeID").focus();
                              });  
            
                    e.preventDefault();
                    }
            });
       });
  </script>

<!-- Optionally, you can add Slimscroll and FastClick plugins.
     Both of these plugins are recommended to enhance the
     user experience. Slimscroll is required when using the
     fixed layout. -->
</body>
</html>
