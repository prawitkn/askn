<?php
  //  include '../db/database.php';
  include 'inc_helper.php';
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
<!-- iCheck for checkboxes and radio inputs -->
<link rel="stylesheet" href="plugins/iCheck/all.css">
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
       Delivery Order Information
        <small>Delivery Order management</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="customer.php"><i class="fa fa-dashboard"></i>Delivery Order Information</a></li>
        <li class="active">Delivery Order</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
		<?php
			$doNo = $_GET['doNo'];
			$sql = "
					SELECT dh.`doNo`, dh.`soNo`, dh.`refNo`, oh.`poNo`
					, dh.`deliveryDate`, dh.`remark`
					, dh.`statusCode`, dh.`createTime`, dh.`createByID`, dh.`updateTime`, dh.`updateById`
					, dh.`confirmTime`, dh.`confirmById`, dh.`approveTime`, dh.`approveById`
					, oh.`custCode`, oh.`smCode`
					, ct.custName, ct.custAddr
					, concat(sm.name, '  ', sm.surname) as smFullname 
					, uca.userFullname as createByName, ucf.userFullname as confirmByName, uap.userFullname as approveByName
					FROM delivery_header dh 
					LEFT JOIN sale_header oh on dh.soNo=oh.soNo
					LEFT JOIN customer ct on ct.code=oh.custCode
					LEFT JOIN salesman sm on sm.code=oh.smCode
					LEFT JOIN user uca on uca.userID=dh.createByID					
					LEFT JOIN user ucf on ucf.userID=dh.confirmById
					LEFT JOIN user uap on uap.userID=dh.approveById
					WHERE 1
					AND dh.doNo=:doNo
			";
			$stmt = $pdo->prepare($sql);			
			$stmt->bindParam(':doNo', $doNo);	
			$stmt->execute();
			$hdr = $stmt->fetch();			
	   ?> 
      <!-- Your Page Content Here -->
      <a href="delivery.php" class="btn btn-google">Back</a>
    <div class="box box-primary">
        <div class="box-header with-border">
			<input type="hidden" name="doNo" id="doNo" value="<?=$doNo;?>" />
			<h3 class="box-title">Delivery Order No : <b><?= $doNo; ?></b></h3>
			<div class="box-tools pull-right">
				<?php $statusName = '<b style="color: red;">Unknown</b>'; switch($hdr['statusCode']){
					case 'B' : $statusName = '<b style="color: blue;">Begin</b>'; break;
					case 'C' : $statusName = '<b style="color: blue;">Confirmed</b>'; break;
					case 'P' : $statusName = '<b style="color: green;">Approved</b>'; break;
					default : 
				} ?>
				<h3 class="box-title" id="statusName">Status : <?= $statusName; ?></h3>
			</div><!-- /.box-tools -->
        </div><!-- /.box-header -->
        <div class="box-body">
			<input type="hidden" id="doNo" value="<?= $doNo; ?>" />
            <div class="row">				
					<div class="col-md-3">
						Salesman : <br/>
						<b><?= $hdr['smFullname']; ?></b>
					</div><!-- /.col-md-3-->	
					<div class="col-md-3">
						Customer : <br/>
						<b><?= $hdr['custName']; ?></b><br/>
						<?= $hdr['custAddr']; ?>
					</div><!-- /.col-md-3-->	
					<div class="col-md-3">
						Delivery Date : <b><?= $hdr['deliveryDate']; ?></b><br/>
						SO No : <b><?= $hdr['soNo']; ?></b><br/><input type="hidden" id="soNo" value="<?=$hdr['soNo'];?>" />		
						PO No : <b><?= $hdr['poNo']; ?></b><br/>		
					</div>	<!-- /.col-md-3-->	
					<div class="col-md-3">
						
					</div>	<!-- /.col-md-3-->	
			</div> <!-- row add items -->
		
			<div class="row"><!-- row show items -->
				<div class="box-header with-border">
				<h3 class="box-title">Item List</h3>
				<div class="box-tools pull-right">
				  <!-- Buttons, labels, and many other things can be placed here! -->
				  <!-- Here is a label for example -->
				  <?php
						$sql = "SELECT COUNT(id) as rowCount FROM delivery_detail
								WHERE doNo=:doNo 
									";						
						$stmt = $pdo->prepare($sql);	
						$stmt->bindParam(':doNo', $hdr['doNo']);
						$stmt->execute();	
						$row = $stmt->fetch(PDO::FETCH_ASSOC);
				  ?>
				  <span class="label label-primary">Total <?php echo $row['rowCount']; ?> items</span>
				</div><!-- /.box-tools -->
				</div><!-- /.box-header -->
				<div class="box-body">
				   <?php
						$sql = "
						SELECT dd.`id`, dd.`prodCode`, dd.`qty`, dd.`createTime`, dd.`doNo`
						, od.qty as salesQty
						, (SELECT IFNULL(SUM(dd2.qty),0) FROM delivery_detail dd2 
												INNER JOIN delivery_header dh2 on dh2.doNo=dd2.doNo AND dh2.statusCode='P' 
												WHERE dd2.prodCode=dd.prodCode ) as sentQty
						, p.prodName, p.prodDesc, p.salesUom 
						FROM `delivery_detail` dd
						INNER JOIN delivery_header dh on dh.doNo=dd.doNo 
						LEFT JOIN product p on dd.prodCode=p.code
						LEFT JOIN sale_detail od on od.prodCode=dd.prodCode and od.soNo=dh.soNo
						WHERE 1
						AND dd.`doNo`=:doNo 
						
						ORDER BY dd.prodCode
						";
						$stmt = $pdo->prepare($sql);	
						$stmt->bindParam(':doNo', $hdr['doNo']);
						$stmt->execute();	
				   ?>	
					<table class="table table-striped">
						<tr>
							<th>No.</th>
							<th>Product Name</th>
							<th>UOM</th>
							<th>Sales Qty</th>
							<th>Sent Qty</th>
							<th>Delivery Qty</th>
							<th>Remain Qty</th>
						</tr>
						<?php $remainTotal=0; $row_no=1; while ($row = $stmt->fetch()) { 
							$remainQty = $row['salesQty']-($row['sentQty']+$row['qty']);
							if($hdr['statusCode']=='P'){
								$remainQty = $row['salesQty']-$row['sentQty'];
							}else{
								$remainQty = $row['salesQty']-($row['sentQty']+$row['qty']);
							}
							$remainTotal += $remainQty;
						?>
						<tr>
							<td style="text-align: center;"><?= $row_no; ?></td>
							<td><?= $row['prodName']; ?><br/>
							<small><?= $row['prodDesc']; ?></small></td>
							<td><?= $row['salesUom']; ?></td>
							<td style="text-align: right;"><?= number_format($row['salesQty'],0,'.',','); ?></td>
							<td style="text-align: right;"><?= number_format($row['sentQty'],0,'.',','); ?></td>
							<td style="text-align: right; color: blue; font-weight: bold;"><?= number_format($row['qty'],0,'.',','); ?></td>
							<td style="text-align: right; color: red;"><?= number_format($remainQty,0,'.',','); ?></td>
						</tr>
						<?php $row_no+=1; } ?>
					</table>
					<!-- for automatic close SO No. -->
					<input type="hidden" name="isClose" id="isClose" value="<?=($remainTotal<=0?'Yes':'No'); ?>" />
				</div><!-- /.box-body -->
	</div><!-- /.row add items -->
		
	<div class="row">
		<div class="col-md-4">
					
		</div>
		<div class="col-md-4">
					
		</div>
		<div class="col-md-4">
			<div class="row">
				<div class="col-md-4">
					Create By : </br>
					Create Time : </br>
					Confirm By : </br>
					Confirm Time : </br>
					Approve By : </br>
					Approve Time : 		
				</div>
				<div class="col-md-8">
					<label class=""><?php echo $hdr['createByName']; ?></label></br>
					<label class=""><?php echo to_thai_datetime_fdt($hdr['createTime']); ?></label></br>
					<label class=""><?php echo $hdr['confirmByName']; ?></label></br>
					<label class=""><?php echo to_thai_datetime_fdt($hdr['confirmTime']); ?></label></br>
					<label class=""><?php echo $hdr['approveByName']; ?></label></br>
					<label class=""><?php echo to_thai_datetime_fdt($hdr['approveTime']); ?></label>	
				</div>				
			</div>			
		</div>
	</div>
	<!-- /.row -->
	
	
    </div><!-- /.box-body -->
  <div class="box-footer">
    <div class="col-md-12">
		  <?php switch($s_userGroupCode){ case 'admin' : case 'salesAdmin' : ?>
			  <button type="button" id="btn_approve" class="btn btn-success pull-right" <?php echo ($hdr['statusCode']=='C'?'':'disabled'); ?>>
			 <i class="glyphicon glyphicon-check">
				</i> Approve
			  </button>
		  <?php break; default : } ?>
		  
		  <button type="button" id="btn_reject" class="btn btn-warning pull-right" style="margin-right: 5px;" <?php echo ($hdr['statusCode']=='C'?'':'disabled'); ?>>
		  <i class="glyphicon glyphicon-remove">
			</i> Reject
          </button>
          <button type="button" id="btn_verify" class="btn btn-primary pull-right" style="margin-right: 5px;" <?php echo ($hdr['statusCode']=='B'?'':'disabled'); ?> >
            <i class="glyphicon glyphicon-ok"></i> Verify
          </button>   
			<button type="button" id="btn_delete" class="btn btn-danger pull-right" style="margin-right: 5px;" <?php echo ($hdr['statusCode']<>'P'?'':'disabled'); ?> >
            <i class="glyphicon glyphicon-trash"></i> Delete
          </button>
	</div><!-- /.col-md-12 -->
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
	doNo: $('#doNo').val()				
	};
	//alert(params.hdrID);
	$.smkConfirm({text:'Are you sure to Verify ?',accept:'Yes.', cancel:'Cancel'}, function (e){if(e){
		$.post({
			url: 'delivery_verify_ajax.php',
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
					type: 'danger',
					position:'top-center'
				});
			}
			//e.preventDefault();		
		}).error(function (response) {
			alert(response.responseText);
		});
		//.post		
	}else{ 
		$.smkAlert({ text: 'Cancelled', type: 'info', position:'top-center'});	
	}});
	//smkConfirm
});
//.btn_click

$('#btn_reject').click (function(e) {				 
	var params = {					
	doNo: $('#doNo').val()					
	};
	//alert(params.hdrID);
	$.smkConfirm({text:'Are you sure to Reject ?',accept:'Yes', cancel:'Cancel'}, function (e){if(e){
		$.post({
			url: 'delivery_reject_ajax.php',
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
					type: 'danger',
					position:'top-center'
				});
			}
			//e.preventDefault();		
		}).error(function (response) {
			alert(response.responseText);
		});
		//.post		
	}else{ 
		$.smkAlert({ text: 'Cancelled', type: 'info', position:'top-center'});	
	}});
	//smkConfirm
});
//.btn_click

$('#btn_approve').click (function(e) {				 
	var params = {					
	doNo: $('#doNo').val(),
	soNo: $('#soNo').val(),
	isClose: $('#isClose').val()
	};
	<?php if($remainTotal<=0){ ?>
		alert('Sales Order No. <?=$hdr['soNo'];?> will Close automatically.');
	<?php } ?>
	//alert(params.hdrID);
	$.smkConfirm({text:'Are you sure to Approve ?',accept:'Yes', cancel:'Cancel'}, function (e){if(e){
		$.post({
			url: 'delivery_approve_ajax.php',
			data: params,
			dataType: 'json'
		}).done(function(data) {
			if (data.success){  
				$.smkAlert({
					text: data.message,
					type: 'success',
					position:'top-center'
				});
				window.location.href = "delivery_view.php?doNo=" + data.doNo;
			}else{
				$.smkAlert({
					text: data.message,
					type: 'danger',
					position:'top-center'
				});
			}
			//e.preventDefault();		
		}).error(function (response) {
			alert(response.responseText);
		});
		//.post
	}else{ 
		$.smkAlert({ text: 'Cancelled', type: 'info', position:'top-center'});	
	}});
	//smkConfirm
});
//.btn_click

$('#btn_delete').click (function(e) {				 
	var params = {					
	doNo: $('#doNo').val()				
	};
	//alert(params.hdrID);
	$.smkConfirm({text:'Are you sure to Delete ?', accept:'Yes', cancel:'Cancel'}, function (e){if(e){
		$.post({
			url: 'delivery_delete_ajax.php',
			data: params,
			dataType: 'json'
		}).done(function(data) {
			if (data.success){  
				alert(data.message);
				window.location.href = 'delivery.php';
			}else{
				$.smkAlert({
					text: data.message,
					type: 'danger',
					position:'top-center'
				});
			}
			//e.preventDefault();		
		}).error(function (response) {
			alert(response.responseText);
		});
		//.post
	}else{ 
		$.smkAlert({ text: 'Cancelled', type: 'info', position:'top-center'});	
	}});
	//smkConfirm
});
//.btn_click

	$("html,body").scrollTop(0);
	$("#statusName").fadeOut('slow').fadeIn('slow').fadeOut('slow').fadeIn('slow');
});
</script>



<!-- Optionally, you can add Slimscroll and FastClick plugins.
     Both of these plugins are recommended to enhance the
     user experience. Slimscroll is required when using the
     fixed layout. -->
</body>
</html>
