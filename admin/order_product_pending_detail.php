<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<?php include 'head.php'; ?>  
<?php include 'inc_helper.php'; ?>
<?php
	$year = date('Y');
	$month = "";//date('m');
	$monthName = "All";
	if(isset($_GET['year'])) $year = $_GET['year'];
	if(isset($_GET['month'])) $month = $_GET['month'];
?>      

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
		Product Sales Order Pending Detail
        <small>Product Sales Order Pending Detail</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Main</a></li>
        <li class="active">Product Sales Order Pending Detail</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="box">
				<div class="box-body">
					<a class="btn btn-default" href="order_product_pending.php"  >Summary</a>
					<a class="btn btn-default" href="order_product_pending_detail.php"  >Detail</a>
				</div>
				<!-- box body -->
			</div>
			<!-- box-->
		</div>
		<!-- col-md-12 -->
	</div>
	<!-- row-->
		
      <!-- Your Page Content Here -->
	  <div class="row">
        <div class="col-md-12">
          <div class="box">
            <div class="box-header with-border">              
			  <form id="frmPeriod" method="get">
				<label class="box-title">Product Sales Order Pending Detail</label>	
						
			  </form>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <div class="btn-group">
                  <button type="button" class="btn btn-box-tool dropdown-toggle" data-toggle="dropdown">
                    <i class="fa fa-wrench"></i></button>
                  <ul class="dropdown-menu" role="menu">
                    <li><a href="#">Action</a></li>
                    <li><a href="#">Another action</a></li>
                    <li><a href="#">Something else here</a></li>
                    <li class="divider"></li>
                    <li><a href="#">Separated link</a></li>
                  </ul>
                </div>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
				
				<?php		
					$url="order_product_pendeing.php";
					
							$sql = "
								SELECT p.`id`, p.`code`, p.`prodGroup`, p.`prodName`, p.`prodNameNew`, p.`photo`
								, oh.soNo, oh.deliveryDate
								, od.qty
								FROM `product` p
								INNER JOIN order_detail od on p.code=od.prodCode
								INNER JOIN order_header oh on od.soNo=oh.soNo and oh.sentDate IS NULL								
									";					
							$stmt = $pdo->prepare($sql);
							//$stmt->bindParam(':year', $year);
							//($month<>""?$stmt->bindParam(':month', $month):"");
							$stmt->execute();
							?>
							<div class="table-responsive">
								<table class="table no-margin">
								  <thead>
								  <tr>
									<th>No.</th>
									<th>Code</th>
									<th>Image</th>
									<th>Name</th>
									<th>Order No.</th>
									<th>Delivery Date</th>
									<th>Qty</th>
								  </tr>
								  </thead>
								  <tbody>
							<?php
							$row_no = 1; while ($row = $stmt->fetch()) { 
							$img = 'dist/img/product/'.(empty($row['photo'])? 'default.jpg' : $row['photo']);
						?>
							<tr>
								<td><?=$row_no;?></td>
								<td><?=$row['code'];?></td>								
								<td><img class="img-circle" src="<?=$img;?>" alt="Product Image" width="50" /></td>
								<td><?=$row['prodName'];?></td>
								<td><?=$row['soNo'];?></td>
								<td><?=to_thai_date($row['deliveryDate']);?></td>
								<td><?=$row['qty'];?></td>
							</tr>
							<?php 
							$row_no+=1; }
							?>
						</tbody>
						</table>
					  </div>
					  <!-- /.table-responsive -->           
            </div> 
			<div class="box-footer">
			</div>
			<!-- box-body-->
		</div> 
		<!-- box-->
	</div> 
	<!-- col-->
</div> 
<!-- row-->
  
	<div id="spin"></div>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Main Footer -->
  <?php include 'footer.php'; ?>  
  
</div>
<!-- ./wrapper -->
</body>

<!-- jQuery 2.2.3 -->
<script src="plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="bootstrap/js/bootstrap.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/app.min.js"></script>
<!-- Add Spinner feature -->
<script src="bootstrap/js/spin.min.js"></script>
<!-- Add smoke dialog -->
<script src="bootstrap/js/smoke.min.js"></script>
<!-- Add _.$ jquery coding -->
<script src="assets/underscore-min.js"></script>

</html>
