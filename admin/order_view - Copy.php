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
       Sales Order Information
        <small>Sales Order management</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="customer.php"><i class="fa fa-dashboard"></i>Sales Order Information</a></li>
        <li class="active">View Sales Order</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
		<?php
			$hdrID = $_GET['hdrID'];
			$sql = "
					SELECT a.`ID`, a.`OrderNo`, a.`order_date`, a.`custID`, a.`salesmanID`, a.`createTime`, a.`createByID`, a.`statusCode`, 
					b.custName, b.custAddr, b.custTel, b.custFax,
					c.name as smName, c.surname as smSurname, 
					d.userFullname as createByName
					FROM `order_header` a
					left join customer b on a.custID=b.ID
					left join salesman c on a.salesmanID=c.ID
					left join user d on a.createByID=d.userID
					WHERE 1
					AND a.ID=".$hdrID." 
					
					ORDER BY a.createTime DESC
					LIMIT 100
			";
			$result = mysqli_query($link, $sql);
			$hdr = mysqli_fetch_assoc($result);
	   ?> 
      <!-- Your Page Content Here -->
      <a href="order.php" class="btn btn-google">Back</a>
    <div class="box box-primary">
        <div class="box-header with-border">
			<h3 class="box-title">View Sales Order ID : <b><?= $hdrID; ?></b></h3>
			<div class="box-tools pull-right">
				<?php $statusName = "Being"; switch($hdr['statusCode']){
							case 'C' : $statusName = '<b style="color: blue;">Confirmed</b>'; break;
							case 'P' : $statusName = '<b style="color: green;">Approved</b>'; break;
							default : 
				} ?>
				<h3 class="box-title">Status : <?= $statusName; ?></h3>
			</div><!-- /.box-tools -->
        </div><!-- /.box-header -->
        <div class="box-body">
            <div class="row">				
                <div class="col-md-4">
					Salesman : <br/>
					<b><?= $hdr['smName'].'&nbsp;&nbsp;'.$hdr['smSurname']; ?></b>
				</div><!-- /.col-md-4-->	
				<div class="col-md-4">
					Customer : <br/>
					<b><?= $hdr['custName']; ?></b><br/>
					<?= $hdr['custAddr']; ?>
				</div><!-- /.col-md-4-->	
				<div class="col-md-4">
					Order ID : <br/>
					<b><?= $hdr['ID']; ?></b><br/>
					Order Date : <br/>
					<b><?= $hdr['order_date']; ?></b><br/>
				</div>	<!-- /.col-md-4-->	
		</div> <!-- row add items -->
		
			<div class="row"><!-- row show items -->
				<div class="box-header with-border">
				<h3 class="box-title">Item List</h3>
				<div class="box-tools pull-right">
				  <!-- Buttons, labels, and many other things can be placed here! -->
				  <!-- Here is a label for example -->
				  <?php
						$sql_so = "SELECT COUNT(*) AS COUNTSO FROM order_detail`
									WHERE hdrID=".$hdrID."
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
								SELECT a.`ID`, a.`prodID`, a.`salesPrice`, a.`qty`, a.`total`, 
								a.`discPercent`, a.`discAmount`, a.`netTotal`, a.`Amount`, a.`hdrID`,
								b.prodName
								FROM `order_detail` a
								LEFT JOIN product b on a.prodID=b.ID
								WHERE 1
								AND a.`hdrID`=".$_GET['hdrID']."
						";
						$result = mysqli_query($link, $sql);
				   ?> 	
					<input type="hidden" id="hdrID" value="<?= $hdrID; ?>" />
					<table class="table table-striped">
						<tr>
							<th>No.</th>
							<th>Product Name</th>
							<th>Sales Price</th>
							<th>Qty</th>
							<th>Total</th>
							<th>disc. (%)</th>
							<th>disc.(amount)</th>
							<th>Total</th>
						</tr>
						<?php $row_no=1; while ($row = mysqli_fetch_assoc($result)) { ?>
						<tr>
							<td style="text-align: center;"><?= $row_no; ?></td>
							<td><?= $row['prodName']; ?></td>
							<td style="text-align: right;"><?= $row['salesPrice']; ?></td>							
							<td style="text-align: right;"><?= $row['qty']; ?></td>
							<td style="text-align: right;"><?= $row['total']; ?></td>
							<td style="text-align: right;"><?= $row['discPercent']; ?></td>
							<td style="text-align: right;"><?= $row['discAmount']; ?></td>
							<td style="text-align: right;"><?= $row['netTotal']; ?></td>
						</tr>
						<?php $row_no+=1; } ?>
					
					<?php
						$sql = "
								SELECT sum(a.`netTotal`) as netTotal
								FROM `order_detail` a
								WHERE 1
								AND a.`hdrID`=".$_GET['hdrID']."
						";
						$result = mysqli_query($link, $sql);
						$row = mysqli_fetch_assoc($result)
				   ?> 
						<tr>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td><b>Total</b></td>
							<td style="text-align: right;"><input type="hidden" id="hdrTotal" value="<?= $row['netTotal']; ?>" />
								<b><?= $row['netTotal']; ?><b>
							</td>
						</tr>
						<tr>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td><b>Vat 7%</b></td>
							<td style="text-align: right;"><input type="hidden" id="hdrVatAmount" value="<?= $row['netTotal']*0.07; ?>" />
								<b><?= $row['netTotal']*0.07; ?><b>
							</td>
						</tr>
						<tr>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td><b>Net Total</b></td>
							<td style="text-align: right;"><input type="hidden" id="hdrNetTotal" value="<?= $row['netTotal'] + ($row['netTotal']*0.07); ?>" />
								<b><?= $row['netTotal'] + ($row['netTotal']*0.07); ?><b>
							</td>
						</tr>
						
					</table>
				</div><!-- /.box-body -->
	</div><!-- /.row add items -->
	<div class="col-md-12">
          <a href="invoice-print.html" target="_blank" class="btn btn-default"><i class="fa fa-print"></i> Print</a>
		  
		  
          <!--<button id="btn_approve" type="button" class="btn btn-success pull-right"  <?php echo ($hdr['statusCode']=='C' ? '' : 'disabled'); ?>>
		  <i class="fa ion-android-done-all"></i> Approve
          </button>
          <button id="btn_verify" type="button" class="btn btn-primary pull-right" style="margin-right: 5px;" <?php echo ($hdr['statusCode']=='A' ? '' : 'disabled'); ?> >
            <i class="fa ion-checkmark"></i> Verify
          </button> -->  
			<button id="btn_approve" type="button" class="btn btn-success pull-right" >
		  <i class="fa ion-android-done-all"></i> Approve
          </button>
          <button id="btn_verify" type="button" class="btn btn-primary pull-right" style="margin-right: 5px;" >
            <i class="fa ion-checkmark"></i> Verify
          </button>  		  
	</div><!-- /.col-md-12 -->
			
			
			
          
    
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
// Append and Hide spinner.          
var spinner = new Spinner().spin();
$("#spin").append(spinner.el);
$("#spin").hide();
//           
$('#btn_verify').click (function(e) {				 
	var params = {					
	hdrID: $('#hdrID').val(),
	hdrTotal: $('#hdrTotal').val(),
	hdrVatAmount: $('#hdrVatAmount').val(),
	hdrNetTotal: $('#hdrNetTotal').val()					
	};
	//alert(params.hdrID);
	$.smkConfirm({text:'คุณแน่ใจที่จะยืนยันรายการนี้ใช่หรือไม่ ?',accept:'ยืนยัน', cancel:'ไม่ยืนยัน'}, function (e){if(e){
		$.post({
			url: 'order_confirm_ajax.php',
			data: params,
			dataType: 'json'
		}).done(function(data) {
			if (data.success){  
				$.smkAlert({
					text: data.message,
					type: 'success',
					position:'top-center'
				});		
				location.reload();
			}else{
				$.smkAlert({
					text: data.message,
					type: 'warning',
					position:'top-center'
				});
			}
			//e.preventDefault();		
		}).error(function (response) {
			alert(response.responseText);
		});
		//.post		
	}else{ 
		$.smkAlert({ text: 'คุณไม่ได้ยืนยันการทำงาน', type: 'info', position:'top-center'});	
	}});
	//smkConfirm
});
//.btn_click

$('#btn_approve').click (function(e) {				 
	var params = {					
	hdrID: $('#hdrID').val()				
	};
	//alert(params.hdrID);
	$.smkConfirm({text:'คุณแน่ใจที่จะยืนยันรายการนี้ใช่หรือไม่ ?',accept:'ยืนยัน', cancel:'ไม่ยืนยัน'}, function (e){if(e){
		$.post({
			url: 'order_approve_ajax.php',
			data: params,
			dataType: 'json'
		}).done(function(data) {
			if (data.success){  
				$.smkAlert({
					text: data.message,
					type: 'success',
					position:'top-center'
				});
				location.reload();
			}else{
				$.smkAlert({
					text: data.message,
					type: 'warning',
					position:'top-center'
				});
			}
			//e.preventDefault();		
		}).error(function (response) {
			alert(response.responseText);
		});
		//.post
	}else{ 
		$.smkAlert({ text: 'คุณไม่ได้ยืนยันการทำงาน', type: 'info', position:'top-center'});	
	}});
	//smkConfirm
});
//.btn_click

});
</script>

<!-- Optionally, you can add Slimscroll and FastClick plugins.
     Both of these plugins are recommended to enhance the
     user experience. Slimscroll is required when using the
     fixed layout. -->
</body>
</html>
