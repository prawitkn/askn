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
    
<!-- iCheck for checkboxes and radio inputs -->
<link rel="stylesheet" href="plugins/iCheck/all.css">

</head>
<body class="hold-transition <?=$skinColorName;?> sidebar-mini">





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
       Order Product
        <small>Order product report</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="customer.php"><i class="fa fa-dashboard"></i>Order product report</a></li>
        <li class="active">Order product report</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
		<?php
			$id = $_GET['id'];
			$sql = "
					SELECT a.`id`, a.`orderNo`, a.`orderDate`, a.`custCode`, a.`smCode`, a.`total`, a.`vatAmount`, a.`netTotal`, a.`prodGFC`, a.`prodGFM`, a.`prodGFT`, a.`prodSC`, a.`prodCFC`, a.`prodEGWM`, a.`prodGT`, a.`prodCSM`, a.`prodWR`, a.`deliveryDate`, a.`deliveryRem`, a.`suppTypeFact`, a.`suppTypeImp`, a.`prodTypeOld`, a.`prodTypeNew`, a.`custTypeOld`, a.`custTypeNew`, a.`prodStkInStk`, a.`prodStkOrder`, a.`prodStkOther`, a.`prodStkRem`, a.`packTypeAk`, a.`packTypeNone`, a.`packTypeOther`, a.`packTypeRem`, a.`priceOnOrder`, a.`priceOnOther`, a.`priceOnRem`, a.`remark`, a.`plac2deliCode`, a.`plac2deliRem`, a.`payTypeCode`, a.`payTypeRem`, a.`statusCode`, a.`createTime`, a.`createByID`, a.`updateTime`, a.`updateById`
					, b.custName, b.custAddr, b.custTel, b.custFax
					, c.name as smName, c.surname as smSurname
					, d.userFullname as createByName
					, a.confirmTime, cu.userFullname as confirmByName
					, a.approveTime, au.userFullname as approveByName
					FROM `order_header` a
					left join customer b on a.custCode=b.code
					left join salesman c on a.smCode=c.code
					left join user d on a.createByID=d.userID
					left join user cu on a.confirmByID=cu.userID
					left join user au on a.approveByID=au.userID
					WHERE 1
					AND a.id=:id 					
					ORDER BY a.createTime DESC
					LIMIT 100
			";
			$stmt = $pdo->prepare($sql);			
			$stmt->bindParam(':id', $id);	
			$stmt->execute();
			$hdr = $stmt->fetch();			
			$orderNo = $hdr['orderNo'];
	   ?> 
      <!-- Your Page Content Here -->
      <a href="indwx.php" class="btn btn-google">Back</a>
    <div class="box box-primary">
        <div class="box-header with-border">
			<h3 class="box-title">View Sales Order No : <b><?= $orderNo; ?></b></h3>
			<div class="box-tools pull-right">
				<?php $statusName = '<b style="color: blue;">Being</b>'; switch($hdr['statusCode']){
							case 'C' : $statusName = '<b style="color: blue;">Confirmed</b>'; break;
							case 'P' : $statusName = '<b style="color: green;">Approved</b>'; break;
							default : 
				} ?>
				<h3 class="box-title" id="statusName">Status : <?= $statusName; ?></h3>
			</div><!-- /.box-tools -->
        </div><!-- /.box-header -->
        <div class="box-body">
			<input type="hidden" id="orderNo" value="<?= $hdr['orderNo']; ?>" />
            <div class="row">				
					<div class="col-md-3">
						Salesman : <br/>
						<b><?= $hdr['smName'].'&nbsp;&nbsp;'.$hdr['smSurname']; ?></b>
					</div><!-- /.col-md-3-->	
					<div class="col-md-3">
						Customer : <br/>
						<b><?= $hdr['custName']; ?></b><br/>
						<?= $hdr['custAddr']; ?>
					</div><!-- /.col-md-3-->	
					<div class="col-md-3">
						Order No : <br/>
						<b><?= $hdr['orderNo']; ?></b><br/>
						Order Date : <br/>
						<b><?= $hdr['orderDate']; ?></b><br/>
					</div>	<!-- /.col-md-3-->	
					<div class="col-md-3">
						<i class="fa fa-<?php echo ($hdr['suppTypeFact']==0?'square-o':'check-square-o'); ?>"></i> Factory&nbsp;&nbsp;&nbsp;    <i class="fa fa-<?php echo ($hdr['suppTypeImp']==0?'check-square-o':'square-o'); ?>"></i> Import</br>
						<i class="fa fa-<?php echo ($hdr['prodTypeOld']==0?'square-o':'check-square-o'); ?>"></i> Old Product&nbsp;&nbsp;&nbsp; <i class="fa fa-<?php echo ($hdr['prodTypeNew']==0?'check-square-o':'square-o'); ?>"></i> New Product</br>
						<i class="fa fa-<?php echo ($hdr['custTypeOld']==0?'square-o':'check-square-o'); ?>"></i> Old Customer&nbsp;&nbsp;&nbsp; <i class="fa fa-<?php echo ($hdr['custTypeNew']==0?'check-square-o':'square-o'); ?>"></i> New Customer</br>						
					</div>	<!-- /.col-md-3-->	
			</div> <!-- row add items -->
		
			<div class="row"><!-- row show items -->
				<div class="box-header with-border">
				<h3 class="box-title">Item List</h3>
				<div class="box-tools pull-right">
				  <!-- Buttons, labels, and many other things can be placed here! -->
				  <!-- Here is a label for example -->
				  <?php
						$sql = "SELECT COUNT(id) as rowCount FROM order_detail`
								WHERE orderNo=:orderNo 
									";						
						$stmt = $pdo->prepare($sql);	
						$stmt->bindParam(':orderNo', $hdr['orderNo']);
						$stmt->execute();	
						$rowCount = $stmt->rowCount();
				  ?>
				  <span class="label label-primary">Total <?php echo $rowCount; ?> items</span>
				</div><!-- /.box-tools -->
				</div><!-- /.box-header -->
				<div class="box-body">
				   <?php
						//Sumary
						$sql = "
							SELECT prd.`id`, prd.`code`, prd.`prodGroup`, prd.`prodName`, prd.`prodNameNew`, prd.`photo`, prd.`prodPrice`, prd.`prodDesc`, prd.`appID`, prd.`statusCode` 							
							,IFNULL(SUM(od.qty),0) as qty
							FROM `product` prd 
							INNER JOIN order_detail od on prd.code=od.prodCode
							INNER JOIN order_header oh on od.orderNo=oh.orderNo and oh.sentDate IS NULL
							WHERE 1
							
							GROUP BY prd.`id`, prd.`code`, prd.`prodGroup`, prd.`prodName`, prd.`prodNameNew`, prd.`photo`, prd.`prodPrice`, prd.`prodDesc`, prd.`appID`, prd.`statusCode` 							
							ORDER BY prd.id, oh.deliveryDate 
						";
						$stmt = $pdo->prepare($sql);	
						//$stmt->bindParam(':orderNo', $hdr['orderNo']);
						$stmt->execute();
						
						//Detail
						$sql = "
							SELECT prd.`id`, prd.`code`, prd.`prodGroup`, prd.`prodName`, prd.`prodNameNew`, prd.`photo`, prd.`prodPrice`, prd.`prodDesc`, prd.`appID`, prd.`statusCode` 
							,oh.orderNo, oh.orderDate, oh.deliveryDate
							,od.qty
							FROM `product` prd 
							INNER JOIN order_detail od on prd.code=od.prodCode
							INNER JOIN order_header oh on od.orderNo=oh.orderNo and oh.sentDate IS NULL
							WHERE 1
							
							ORDER BY prd.id, oh.deliveryDate 
						";
						$stmt2 = $pdo->prepare($sql);	
						//$stmt->bindParam(':orderNo', $hdr['orderNo']);
						$stmt2->execute();	
				   ?>	
					<table class="table table-striped">
						<tr>
							<th>No.</th>
							<th>Product Name</th>
							<th>Order NO.</th>
							<th>Delivery Date</th>
							<th>Qty</th>
						</tr>
						<?php $row_no=1; while ($row = $stmt->fetch()) {
							$prodCode=$row['prodCode'];
						?>
							<tr>
								<td style="text-align: center;"><?= $row_no; ?></td>
								<td><?= $row['prodName']; ?></td>
								<td></td>
								<td></td>
								<td style="text-align: right;"><?= number_format($row['qty'],0,'.',','); ?></td>
							</tr>
						<?php
							while($row2 = $stmt->fetch()){ 
								if($prodCode==$row2['code']){
									?>
										<tr>
										<td style="text-align: center;"><?= $row_no; ?></td>
										<td><?= $row['prodName']; ?></td>
										<td><?= $row['orderNo']; ?></td>
										<td><?= $row['deliveryDate']; ?></td>
										<td style="text-align: right;"><?= number_format($row['qty'],0,'.',','); ?></td>
									</tr>
									<?php
								}else{
									exit;
								}
							?>
								
								
							<?php 
							}//while2	
							$row_no+=1; 
						}//while ?>
				  
					</table>
				</div><!-- /.box-body -->
	</div><!-- /.row add items -->
</div><!-- row-->
	<div class="row">
		<div class="col-md-2">
			Product :
		</div>
		<div class="col-md-10">
			<i class="fa fa-<?php echo ($hdr['prodGFC']==0?'square-o':'check-square-o'); ?>"></i> Glass Fiber Cloth&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<i class="fa fa-<?php echo ($hdr['prodGFM']==0?'square-o':'check-square-o'); ?>"></i> Glass Fiber Mesh&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<i class="fa fa-<?php echo ($hdr['prodGFT']==0?'square-o':'check-square-o'); ?>"></i> Glass Fiber Tape&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<i class="fa fa-<?php echo ($hdr['prodSC']==0?'square-o':'check-square-o'); ?>"></i> Silica Cloth&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<i class="fa fa-<?php echo ($hdr['prodCFC']==0?'square-o':'check-square-o'); ?>"></i> Cabon Fiber Cloth&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</br>
			<i class="fa fa-<?php echo ($hdr['prodEGWM']==0?'square-o':'check-square-o'); ?>"></i> E-Glass Wool Mat&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<i class="fa fa-<?php echo ($hdr['prodGT']==0?'square-o':'check-square-o'); ?>"></i> Glass Tissue&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<i class="fa fa-<?php echo ($hdr['prodCSM']==0?'square-o':'check-square-o'); ?>"></i> Chopped Strand Mat&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<i class="fa fa-<?php echo ($hdr['prodWR']==0?'square-o':'check-square-o'); ?>"></i> Woven Roving
		</div>
		
		<div class="col-md-2">
			Stock Status :
		</div>
		<div class="col-md-10">
			<i class="fa fa-<?php echo ($hdr['prodStkInStk']==0?'square-o':'check-square-o'); ?>"></i> In Stock&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
			<i class="fa fa-<?php echo ($hdr['prodStkOrder']==0?'square-o':'check-square-o'); ?>"></i> Order&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
			<i class="fa fa-<?php echo ($hdr['prodStkOther']==0?'square-o':'check-square-o'); ?>"></i> Other 
			<label class="label label-primary"><?php echo $hdr['prodStkRem']; ?></label>
		</div>
		<div class="col-md-2">
			Packing :
		</div>
		<div class="col-md-10">
			<i class="fa fa-<?php echo ($hdr['packTypeAk']==0?'square-o':'check-square-o'); ?>"></i> AK Logo&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
			<i class="fa fa-<?php echo ($hdr['packTypeNone']==0?'square-o':'check-square-o'); ?>"></i> None AK Logo&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
			<i class="fa fa-<?php echo ($hdr['packTypeOther']==0?'square-o':'check-square-o'); ?>"></i> Other
			<?php echo ($hdr['packTypeRem']<>""?'<label class="text-red h4">'.$hdr['packTypeRem'].'</label>':''); ?>
              
		</div>
		<div class="col-md-2">
			Delivery / Load Date :
		</div>
		<div class="col-md-10">
			<label class="label label-primary"><?php echo $hdr['deliveryDate']; ?></label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Delivery Remark / Shipping Remark :
			<label class="label label-primary"><?php echo $hdr['deliveryRem']; ?></label>
		</div>
		<div class="col-md-2">
			Pricing on :
		</div>
		<div class="col-md-10">
			<i class="fa fa-<?php echo ($hdr['priceOnOrder']==0?'square-o':'check-square-o'); ?>"></i> Sales Order&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
			<i class="fa fa-<?php echo ($hdr['priceOnOther']==0?'square-o':'check-square-o'); ?>"></i> Other
			<label class="label label-primary"><?php echo $hdr['priceOnRem']; ?></label>
		</div>
		<div class="col-md-2">
			Remark :
		</div>
		<div class="col-md-10">
			<label class="label label-primary"><?php echo $hdr['remark']; ?></label>
		</div>		
	</div>
	<!-- /.row -->
	
	<div class="row">
		<div class="col-md-4">
			<div class="row">
				<div class="col-md-4">
					Credit :					
				</div>
				<div class="col-md-8">					
					<label class="label label-primary"><?php echo $hdr['payTypeRem']; ?></label></br>
					<i class="fa fa-<?php echo ($hdr['payTypeCode']=='CASH'?'check-circle-o':'circle-o'); ?>"></i> Cash</br>
					<i class="fa fa-<?php echo ($hdr['payTypeCode']=='CHEQ'?'check-circle-o':'circle-o'); ?>"></i> Cheque</br>
					<i class="fa fa-<?php echo ($hdr['payTypeCode']=='TRAN'?'check-circle-o':'circle-o'); ?>"></i> Transfer					
				</div>
			</div>			
		</div>
		<div class="col-md-4">
			Place to Delivery :
			<div class="row">
				
				<div class="col-md-2">
				</div>
				<div class="col-md-10">					
					<i class="fa fa-<?php echo ($hdr['plac2deliCode']=='FACT'?'check-circle-o':'circle-o'); ?>"></i> AK Factory</br>
					<i class="fa fa-<?php echo ($hdr['plac2deliCode']=='SEND'?'check-circle-o':'circle-o'); ?>"></i> Factory Sent to</br>
					<i class="fa fa-<?php echo ($hdr['plac2deliCode']=='MAP_'?'check-circle-o':'circle-o'); ?>"></i> Map</br>
					<i class="fa fa-<?php echo ($hdr['plac2deliCode']=='LOGI'?'check-circle-o':'circle-o'); ?>"></i> Logistic</br>
					<label class="label label-primary"><?php echo $hdr['plac2deliRem']; ?></label>
				</div>
			</div>			
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
          <a href="invoice-print.html" target="_blank" class="btn btn-default"><i class="glyphicon glyphicon-print"></i> Print</a>
		  
		  
          <button type="button" id="btn_approve" class="btn btn-success pull-right" <?php echo ($hdr['statusCode']=='C'?'':'disabled'); ?>>
		 <i class="glyphicon glyphicon-check">
			</i> Approve
          </button>
		  <button type="button" id="btn_reject" class="btn btn-warning pull-right" style="margin-right: 5px;" <?php echo ($hdr['statusCode']=='C'?'':'disabled'); ?>>
		  <i class="glyphicon glyphicon-remove">
			</i> Reject
          </button>
          <button type="button" id="btn_verify" class="btn btn-primary pull-right" style="margin-right: 5px;" <?php echo ($hdr['statusCode']=='B'?'':'disabled'); ?> >
            <i class="glyphicon glyphicon-ok"></i> Verify
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
	orderNo: $('#orderNo').val(),
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
		$.smkAlert({ text: 'คุณไม่ได้ยืนยันการทำงาน', type: 'info', position:'top-center'});	
	}});
	//smkConfirm
});
//.btn_click

$('#btn_reject').click (function(e) {				 
	var params = {					
	orderNo: $('#orderNo').val()					
	};
	//alert(params.hdrID);
	$.smkConfirm({text:'คุณแน่ใจที่จะส่งคืนรายการนี้ใช่หรือไม่ ?',accept:'ยืนยัน', cancel:'ไม่ยืนยัน'}, function (e){if(e){
		$.post({
			url: 'order_reject_ajax.php',
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
		$.smkAlert({ text: 'คุณไม่ได้ยืนยันการทำงาน', type: 'info', position:'top-center'});	
	}});
	//smkConfirm
});
//.btn_click

$('#btn_approve').click (function(e) {				 
	var params = {					
	orderNo: $('#orderNo').val()				
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
		$.smkAlert({ text: 'คุณไม่ได้ยืนยันการทำงาน', type: 'info', position:'top-center'});	
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
