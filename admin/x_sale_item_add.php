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
       Add Sales Order Information
        <small>Sales Order management</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="customer.php"><i class="fa fa-dashboard"></i>Sales Order Information</a></li>
        <li class="active">Add Sales Order Information</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

      <!-- Your Page Content Here -->
      
    <div class="box box-primary">
        <div class="box-header with-border">
        <h3 class="box-title">Add Sales Order..</h3>
        <div class="box-tools pull-right">
          <!-- Buttons, labels, and many other things can be placed here! -->
          <!-- Here is a label for example -->
         
        </div><!-- /.box-tools -->
        </div><!-- /.box-header -->
        <div class="box-body">
            
            <div class="row">
                <div class="col-md-6">
					<?php
						$sql = "
								SELECT a.`ID`, a.`salesOrderNo`, a.`order_date`, a.`custID`, a.`salesmanID`, a.`createTime`, `createByID`,
								b.custName, b.custAddr, b.custTel, b.custFax,
								c.name as smName,
								d.userFullname as createByName
								FROM `order_header` a
								left join m_customer b on a.custID=b.custID
								left join m_salesman c on a.salesmanID=c.ID
								left join user d on a.createByID=d.userID
								WHERE 1
								AND a.ID=".$_GET['hdrID']." 
								
								ORDER BY a.createTime DESC
								LIMIT 100
						";
						$result = mysqli_query($link, $sql);
						$row = mysqli_fetch_assoc($result);
				   ?> 
                    <form id="" action="#" method="post" class="form" novalidate>
						<div class="form-group">
							<input type="hidden" id="hdrID" name="hdrID" value="<?= $row['ID']; ?>" />
                            <label for="orderDate">Order ID</label>
                            <input id="orderDate" type="text" class="form-control" name="orderDate" value="<?= $row['ID']; ?>">
                        </div>
						<div class="form-group">
                            <label for="orderDate">Order Date</label>
                            <input id="orderDate" type="text" class="form-control" name="orderDate" value="<?= $row['order_date']; ?>">
                        </div>
                        <div class="form-group">
                            <label for="smID">Salesman Name</label>							
							<input id="orderDate" type="text" class="form-control" name="orderDate" value="<?= $row['smName']; ?>">                         
                        </div>
						<div class="form-group">
                            <label for="custID">Customer Name</label>
                            <input id="orderDate" type="text" class="form-control" name="orderDate" value="<?= $row['custName']; ?>">
                        </div>
						
						<div class="form-group">
                            <label for="custContractName">Customer Address</label>
                            <input id="custContractName" type="text" class="form-control" name="custContractName" value="<?= $row['custAddr']; ?>">
                        </div>
                    </form>
                </div>
				
				<div class="row">
				<a href="order_item_add.php" class="btn btn-google">Add Sales Orders</a>
                <div class="col-md-6">
                    <form id="form1" action="order_item_add_insert.php" method="post" class="form" novalidate>
						<div class="form-group">
                            <label for="orderDate">Product Name</label>
                            <input id="orderDate" type="text" class="form-control" name="orderDate" data-smk-msg="Require Customer Name."required>
                        </div>
						<div class="form-group">
                            <label for="orderDate">Product Desc</label>
                            <input id="orderDate" type="text" class="form-control" name="orderDate" data-smk-msg="Require Customer Name."required>
                        </div>
						<div class="form-group">
                            <label for="orderDate">Qty</label>
                            <input id="orderDate" type="text" class="form-control" name="orderDate" data-smk-msg="Require Customer Name."required>
                        </div>
                        <div class="form-group">
                            <label for="smID">Amount</label>							
							<input id="orderDate" type="text" class="form-control" name="orderDate" data-smk-msg="Require Customer Name."required>                         
                        </div>
                        
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
				//alert($('#custID').val());
                  $.post("order_item_add_insert.php", $("#form1").serialize() )
                              .done(function(data) {
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
									 //$("#visitDate").focus();
								  });  
								  
				   e.preventDefault();
			  }//.if end
               });//.btn_click end
			   
			 
            
            
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


			$( "#custID" ).on("change",function(e) {
           //  alert( "Keyup OK" );
			//	alert($(this).attr('data-custContract').val());
               /*$.get("check_username.php",{custUsername: $("#custUsername").val()})
                       .done(function(data) {  
                            if (data.status === "active") {
                            alert(data.message);
                            $("#custUsername").val('');
                            $("#custUsername").focus();
                            }
               
                       });*/
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
