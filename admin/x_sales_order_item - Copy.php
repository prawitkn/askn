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
      <a href="sales_order.php" class="btn btn-google">Back</a>
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
                <div class="col-md-12">
					<?php
						$sql = "
								SELECT a.`ID`, a.`salesOrderNo`, a.`order_date`, a.`custID`, a.`salesmanID`, a.`createTime`, `createByID`,
								b.custName, b.custAddr, b.custTel, b.custFax,
								c.name as smName, c.surname as smSurname, 
								d.userFullname as createByName
								FROM `t_sales_order_header` a
								left join m_customer b on a.custID=b.ID
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
                    <form id="" action="#" method="post" class="form-horizontal" novalidate>
						<div class="form-group">							
                            <label for="orderDate" class="control-label col-md-3">Order ID</label>
							<div class="col-md-3">
								<span><?= $row['ID']; ?></span>								
							</div>                            
                        </div>
						<div class="form-group">							
                            <label for="orderDate" class="control-label col-md-3">Order Date</label>
							<div class="col-md-3">
								<span><?= $row['order_date']; ?></span>								
							</div>                            
                        </div>						
						<div class="form-group">							
                            <label for="orderDate" class="control-label col-md-3">Salesman Name</label>
							<div class="col-md-3">
								<span><?= $row['smName'].'&nbsp;&nbsp;'.$row['smSurname']; ?></span>								
							</div>                            
                        </div>
						<div class="form-group">							
                            <label for="orderDate" class="control-label col-md-3">Customer Name</label>
							<div class="col-md-3">
								<span><?= $row['custName']; ?></span>								
							</div>                            
                        </div>
						<div class="form-group">							
                            <label for="orderDate" class="control-label col-md-3">Customer Address</label>
							<div class="col-md-3">
								<span><?= $row['custAddr']; ?></span>								
							</div>                            
                        </div>
                    </form>
            </div><!-- /.col-md-12-->
		</div> <!-- row add items -->
				
		<div class="row"><!-- row add items -->
				<!--<a href="sales_order_item_add.php" class="btn btn-google">Add Sales Orders</a>-->
                <div class="col-md-12">
                    <form id="form1" action="sales_order_item_insert.php" method="post" class="form" novalidate>
						<div class="form-group">
							<input type="hidden" id="hdrID" name="hdrID" value="<?= $row['ID']; ?>" />
                            <label for="prodID">Product Name</label>
                            <select id="prodID" name="prodID" class="form-control" >
								<option value=""> -- Select -- </option>
								<?php
								$sql_cust = "SELECT `ID`, `prodGroup`, `prodName`, `prodNameNew`, `prodDesc`, `prodPrice`, `appID` FROM `m_product` WHERE 1";
								$result_cust = mysqli_query($link, $sql_cust);
								while($row = mysqli_fetch_assoc($result_cust)){
									echo '<option value="'.$row['ID'].'" 
										 data-prodDesc="'.$row['prodDesc'].'" 									 
										 data-prodPrice="'.$row['prodPrice'].'" 
										 data-appID="'.$row['appID'].'" 	
										 >'.$row['prodName'].' : ['.$row['prodNameNew'].']</option>';
								}
								?>
							</select> 
                        </div>
						<div class="form-group">
                            <label for="prodDesc">Product Desc</label>
							<textarea id="prodDesc" class="form-control" name="prodDesc" disabled></textarea>
                        </div>
						<div class="form-group">
                            <label for="prodPrice">Price</label>
                            <input id="prodPrice" type="text" class="form-control" name="prodPrice" data-smk-msg="Require Quantity."required>
                        </div>
						<div class="form-group">
                            <label for="prodPrice">Discount (%)</label>
                            <input id="prodPrice" type="text" class="form-control" name="prodPrice" data-smk-msg="Require Quantity."required>
                        </div>
						<div class="form-group">
                            <label for="qty">Qty</label>
                            <input id="qty" type="text" class="form-control" name="qty" data-smk-msg="Require Quantity."required>
                        </div>
                        <div class="form-group">
                            <label for="amount">Amount</label>							
							<input id="amount" type="text" class="form-control" name="amount" data-smk-msg="Require Amount."required>                         
                        </div>
                        
                        <button id="btn1" type="button" class="btn btn-default">Submit</button>
                    </form>
                </div> 
            </div>
			<!-- /.row add items -->
			
			
			
			
			<div class="row"><!-- row show items -->
				<div class="box-header with-border">
				<h3 class="box-title">Item List</h3>
				<div class="box-tools pull-right">
				  <!-- Buttons, labels, and many other things can be placed here! -->
				  <!-- Here is a label for example -->
				  <?php
						$sql_so = "SELECT COUNT(*) AS COUNTSO FROM t_sales_order_detail`
									WHERE hdrID=".$_GET['hdrID']."
									";
						$result_so = mysqli_query($link, $sql_so);
						$count_so = mysqli_fetch_assoc($result_so);
				  ?>
				  <span class="label label-primary">Total <?php echo $count_so['COUNTSO']; ?> items</span>
				</div><!-- /.box-tools -->
				</div><!-- /.box-header -->
				<div class="box-body">
				   <?php
						$sql = "
								SELECT a.`ID`, a.`prodID`, a.`qty`, a.`Amount`,
								b.prodName
								FROM `t_sales_order_detail` a
								LEFT JOIN m_product b on a.prodID=b.ID
								WHERE 1
								AND a.`hdrID`=".$_GET['hdrID']."
						";
						$result = mysqli_query($link, $sql);
				   ?> 
					
					<table class="table table-striped">
						<tr>
							<th>Product ID</th>
							<th>Product Name</th>
							<th>Qty</th>
							<th>Amount</th>
							<th>Delete</th>
						</tr>
						<?php while ($row = mysqli_fetch_assoc($result)) { ?>
						<tr>
							<td>
								 <?= $row['prodID']; ?>
							</td>
							<td>
								 <?= $row['prodName']; ?>
							</td>
							<td>
								 <?= $row['qty']; ?>
							</td>
							<td>
								 <?= $row['Amount']; ?>
							</td>
							<td>
								<a href="sales_order_item_del.php?id=<?= $row['ID']; ?>"><i class="fa fa-trash"></i></a>
							</td>
						</tr>
						<?php } ?>
					</table>
				</div><!-- /.box-body -->
	</div><!-- /.row add items -->
			
			
			
			
          
    
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
                  $.post("sales_order_item_insert.php", $("#form1").serialize() )
                              .done(function(data) {
										alert(data.status);
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


			$("#prodID").on("change",function(e) {
				$('#prodDesc').val($('option:selected', this).attr('data-prodDesc'));
				$('#prodPrice').val($('option:selected', this).attr('data-prodPrice'));
				
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
