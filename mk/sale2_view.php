<?php
  //  include '../db/database.php';
  //include 'inc_helper.php';
?>
<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<?php include 'head.php'; 

$rootPage="sale2";


?>
<!-- iCheck for checkboxes and radio inputs -->
<link rel="stylesheet" href="plugins/iCheck/all.css">
 
</head>
<body class="hold-transition <?=$skinColorName;?> sidebar-mini">


	
	
	
<div class="wrapper">

  <!-- Main Header -->
  <?php include 'header.php'; ?>
  <?php $soNo = $_GET['soNo']; ?>
  
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
        <li><a <?php switch($s_userGroupCode){ case 'admin' : case 'salesAdmin' : case 'sales' : ?> href="<?=$rootPage;?>.php" <?php break; default : } //end switch roll. ?> ><i class="fa fa-list"></i>Sales List</a></li>
		<li><a <?php switch($s_userGroupCode){ case 'admin' : case 'salesAdmin' : case 'sales' : ?> href="<?=$rootPage;?>_add.php?soNo=<?=$soNo;?>" <?php break; default : } //end switch roll. ?> ><i class="fa fa-edit"></i>SO No.<?=$soNo;?></a></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
		<?php
			$soNo = $_GET['soNo'];
			$sql = "
			SELECT a.`soNo`, a.`poNo`, a.`piNo`, a.`saleDate`, a.`custId`, a.`shipToId`, a.`smId`, a.`revCount`, a.`deliveryDate`, a.`suppTypeId`, a.`stkTypeId`, a.`packageTypeId`, a.`priceTypeId`, a.`deliveryTypeId`, a.`shippingMarksId`, a.`deliveryRem`, a.`containerLoadId`, a.`creditTypeId`, a.`remark`, a.`payTypeCreditDays`, a.`isClose`, a.`statusCode`, a.`createTime`, a.`createById`, a.`updateTime`, a.`updateById`, a.`confirmTime`, a.`confirmById`, a.`approveTime`, a.`approveById`
			, b.code as custCode, b.name as custName, b.addr1 as custAddr1, b.addr2 as custAddr2, b.addr3 as custAddr3, b.zipcode as custZipcode, b.tel as custTel, b.fax as custFax
			, st.code as shipToCode, st.name as shipToName, st.addr1 as shipToAddr1, st.addr2 as shipToAddr2, st.addr3 as shipToAddr3, st.zipcode as shipToZipcode, st.tel as shipToTel, st.fax as shipToFax
			, c.code as smCode, c.name as smName, c.surname as smSurname
			, sst.name as suppTypeName, stkt.name as stkTypeName, sst.name as stockTypeName
			, spt.name as packageTypeName, prit.name as priceTypeName, sdt.name as deliveryTypeName
			, sct.name as creditTypename, clt.name as containerLoadName 
			, spm.name as shippingMarksName, IFNULL(spm.filePath,'') as shippingMarksFilePath
			
			, d.userFullname as createByName
			, a.confirmTime, cu.userFullname as confirmByName
			, a.approveTime, au.userFullname as approveByName
			FROM `sale_header` a
			left join customer b on b.id=a.custId 
			left join shipto st on st.id=a.shipToId  
			left join salesman c on c.id=a.smId 
			left join sale_supp_type sst ON sst.id=a.suppTypeId
			left join sale_stk_type stkt ON stkt.id=a.stkTypeId 
			left join sale_package_type spt ON spt.id=a.packageTypeId
			left join sale_price_type prit ON prit.id=a.priceTypeId		
			left join sale_delivery_type sdt ON sdt.id=a.deliveryTypeId
			left join sale_credit_type sct ON sct.id=a.creditTypeId	
			left join sale_container_load_type clt ON clt.id=a.containerLoadId 	
		
			left join shipping_marks spm on spm.id=a.shippingMarksId

			left join user d on a.createById=d.userId
			left join user cu on a.confirmById=cu.userId
			left join user au on a.approveById=au.userId
			WHERE 1
			AND a.soNo=:soNo 					
			ORDER BY a.createTime DESC
			LIMIT 1
			";
			$stmt = $pdo->prepare($sql);			
			$stmt->bindParam(':soNo', $soNo);	
			$stmt->execute();
			$hdr = $stmt->fetch();			
			$soNo = $hdr['soNo'];
	   ?> 
      <!-- Your Page Content Here -->
    <div class="box box-primary">
        <div class="box-header with-border">
			<h3 class="box-title">Sales Order No : <b><?= $soNo; ?><small style="color: red;"><?php echo ($hdr['revCount']<>0?' rev.'.$hdr['revCount']:'');?></small></b>
			<?php
				echo ($hdr['isClose']=='Y'?'<span style="green: blue; font-weight: bold;text-decoration: underline;">[ Closed ]</span>':'<span style="color: red; font-weight: bold;text-decoration: underline;">[ Open ]</span>');
			?>
			</h3>

			<div class="box-tools pull-right">
				<?php $statusName = '<b style="color: red;">Unknown</b>'; switch($hdr['statusCode']){
					case 'A' : $statusName = '<b style="color: red;">Incompleate</b>'; break;
					case 'B' : $statusName = '<b style="color: blue;">Begin</b>'; break;
					case 'C' : $statusName = '<b style="color: blue;">Confirmed</b>'; break;
					case 'P' : $statusName = '<b style="color: green;">Approved</b>'; break;
					case 'X' : $statusName = '<b style="color: black;">Removed</b>'; break;
					default : 
				} ?>
				<h3 class="box-title" id="statusName">Status : <?= $statusName; ?></h3>
			</div><!-- /.box-tools -->
        </div><!-- /.box-header -->
        <div class="box-body">
			<input type="hidden" id="soNo" value="<?= $soNo; ?>" />
            <div class="row">				
					<div class="col-md-4">
						ชื่อลูกค้า : <b><?= $hdr['custName']; ?></b><br/>
						ที่อยู่ (Address) : 
						<?= $hdr['custAddr1']; ?><br/>
						<?= $hdr['custAddr2']; ?><br/>
						<?= $hdr['custAddr3'].' '.$hdr['custZipcode']; ?>
					</div><!-- /.col-md-3-->	
					<div class="col-md-4">
						สถานที่จัดส่ง (Ship to) : <b><?= $hdr['shipToName']; ?></b><br/>
						<?= $hdr['shipToAddr1']; ?><br/>
						<?= $hdr['shipToAddr2']; ?><br/>
						<?= $hdr['shipToAddr3'].' '.$hdr['shipToZipcode']; ?>
					</div><!-- /.col-md-3-->	
					<div class="col-md-4">
						Order No : <br/>
						<b><?= $hdr['soNo']; ?></b><br/>
						Order Date : <br/>
						<b><?= date('d M Y',strtotime( $hdr['saleDate'] )); ?></b><br/>
						Product Group : <br/>
						<b><?=$hdr['suppTypeName'];?></b>

					</div>	<!-- /.col-md-3-->	

			</div> <!-- row add items -->
		
			<div class="row"><!-- row show items -->
				<div class="box-header with-border">
				<h3 class="box-title">Item List</h3>
				<div class="box-tools pull-right">
				  <!-- Buttons, labels, and many other things can be placed here! -->
				  <!-- Here is a label for example -->
				  <?php
				  	$sql = "
					SELECT a.`id`, a.`prodId`, a.`qty`, a.`rollLengthId`, a.`remark`, a.deliveryDate, a.`soNo`
					, b.code as prodCode, b.name as prodName, b.uomCode as prodUomCode, b.description 
					, (SELECT IFNULL(SUM(pdtl.qty),0) FROM picking_detail pdtl 
							INNER JOIN picking phdr ON phdr.pickNo=pdtl.pickNo 
							INNER JOIN prepare pph ON pph.pickNo=pdtl.pickNo 
							INNER JOIN delivery_header dhdr ON dhdr.ppNo=pph.ppNo AND dhdr.statusCode='P' 
							WHERE pdtl.saleItemId=a.id 
							) as sentQty 
					, rl.name as rollLengthName 
					FROM `sale_detail` a
					LEFT JOIN product b on a.prodId=b.id
					LEFT JOIN product_roll_length rl ON rl.id=a.rollLengthId 
					WHERE 1
					AND a.`soNo`=:soNo 
					ORDER BY a.id, a.createTime
					";
					$stmt = $pdo->prepare($sql);	
					$stmt->bindParam(':soNo', $hdr['soNo']);
					$stmt->execute();	
					$countTotal = $stmt->rowCount();
				  ?>
				  <span class="label label-primary">Total <?php echo $countTotal; ?> items</span>
				</div><!-- /.box-tools -->
				</div><!-- /.box-header -->
				<div class="box-body">
				   <?php
						
				   ?>	
					<table class="table table-striped">
						<thead><tr>
							<th style="text-align: center;">No.</th>							
							<th style="text-align: center;">Product Series</th>
							<th style="text-align: center;">Product Code</th>
							<th style="text-align: center;">Description</th>
							<th style="text-align: center;">Quantity</th>
							<th style="text-align: center;">Delivery /Load Date</th>
							<th style="text-align: center; color: blue;">Sent Qty</th>
						</tr></thead>
						<tbody>
						<?php $row_no=1; while ($row = $stmt->fetch()) { ?>
						<tr>
							<td style="text-align: center;"><?= $row_no; ?></td>											
							<td><?= $row['prodName']; ?></td>					
							<td><?= $row['prodCode']; ?></td>					
							<td><?= $row['remark'].' '.($row['rollLengthId']<>'0'?'[RL:'.$row['rollLengthName'].']':''); ?></td>		
							<td style="text-align: right;"><?= number_format($row['qty'],2,'.',',').' '.$row['prodUomCode']; ?></td>						
							<td style="text-align: center;"><?= date('d M Y',strtotime( $row['deliveryDate'] )); ?></td>	
							<td style="text-align: right; color: blue;"><?= number_format($row['sentQty'],0,'.',',').'&nbsp;'.$row['prodUomCode']; ?></td>
						</tr>
						<?php $row_no+=1; } ?>		
						</tbody>				
					</table>
				</div><!-- /.box-body -->
	</div><!-- /.row add items -->
	
	<div class="row">		
		<div class="col-md-2">
			สินค้ามีในสต๊อก :
		</div>
		<div class="col-md-10">
			<span style="text-decoration: underline;"><?=$hdr['stkTypeName'];?></span>
		</div>
	</div>
	<div class="row">
		<div class="col-md-2">
			บรรจุภัณฑ์ (Package) :
		</div>
		<div class="col-md-10">
			<span style="text-decoration: underline;"><?=$hdr['packageTypeName'];?></span>
		</div>
	</div>
	<div class="row">	
		<div class="col-md-3">
			กรณีส่งต่างผระเทศ (Export) by :  
		</div>
		<div class="col-md-9">
			<span style="text-decoration: underline;"><?=$hdr['containerLoadName'];?></span>
		</div>
	</div>
	<div class="row">
		<div class="col-md-2">
			Shipping Mark :  
		</div>
		<div class="col-md-10">			
			<?php if($hdr['shippingMarksFilePath']==""){
			}else{
				echo '<img src="../images/shippingMarks/'.$hdr['shippingMarksFilePath'].'" id="shippingMarksImg" />';
			}?>			
		</div>
	</div>
	<div class="row">	
		<div class="col-md-2">
			Pricing on :
		</div>			
		<div class="col-md-10">
			<span style="text-decoration: underline;"><?=$hdr['priceTypeName'];?></span>
		</div>
	</div>

	<div class="row">	
		<div class="col-md-2">
			Salesman :
		</div>
		<div class="col-md-10">
			<span style="text-decoration: underline;"><?=$hdr['smName'];?>&nbsp;&nbsp;<?=$hdr['smSurname'];?></span>
		</div>		
	</div>
	<!-- /.row -->					

	<div class="row">	
		<div class="col-md-2">
			Remark :
		</div>
		<div class="col-md-10">
			<span style="text-decoration: underline;"><?=$hdr['remark'];?></span>
		</div>		
	</div>
	<!-- /.row -->
	







	<div class="row" border="1">
		<div class="col-md-3">
			<div class="row">
				<div class="col-md-4">
					Credit :					
				</div>
				<div class="col-md-8">
				<span style="text-decoration: underline;"><?=$hdr['payTypeCreditDays'];?></span> Days</br>
				<span style="text-decoration: underline;"><?=$hdr['creditTypename'];?></span>	
				</div>
			</div>			
		</div>
		<div class="col-md-4">
			Place to Delivery :
			<div class="row">
				
				<div class="col-md-2">
				</div>
				<div class="col-md-10">	
					<span style="text-decoration: underline;"><?=$hdr['deliveryTypeName'];?></span>			
				</div>
			</div>			
		</div>
		<div class="col-md-5">
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
					<label class=""><?php echo date('d M Y H:m',strtotime( $hdr['createTime'] )); ?></label></br>
					<label class=""><?php echo $hdr['confirmByName']; ?></label></br>
					<label class=""><?php if($hdr['confirmTime']<>"0000-00-00 00:00:00") echo date('d M Y H:m',strtotime( $hdr['confirmTime'] )); ?></label></br>
					<label class=""><?php echo $hdr['approveByName']; ?></label></br>
					<label class=""><?php  if($hdr['confirmTime']<>"0000-00-00 00:00:00") echo date('d M Y H:m',strtotime( $hdr['approveTime'] )); ?></label>	
				</div>				
			</div>			
		</div>
	</div>
	<!-- /.row -->
	
	
	
			
			
			
          
    
    </div><!-- /.box-body -->
  <div class="box-footer">
    <div class="col-md-12">	
		<?php switch($s_userGroupCode) { case 'admin' : case 'salesAdmin' : case 'sales' : ?>		
		
		  <?php switch($s_userGroupCode){ case 'admin' : case 'salesAdmin' : ?>	
				<?php switch($hdr['statusCode']){ case 'P' : ?>	

					<a href="<?=$rootPage;?>_view_pdf.php?soNo=<?=$soNo;?>" target="_blank" class="btn btn-default"><i class="glyphicon glyphicon-print"></i> Print</a>
					
					<?php if($hdr['isClose']=='N'){ ?>
						<button type="button" id="btn_revise" class="btn btn-danger" style="margin-right: 5px;" <?php echo ($hdr['isClose']=='N'?'':'disabled'); ?> >
						<i class="glyphicon glyphicon-wrench"></i> Edit for Revise
						</button>
						
						<button type="button" id="btn_remove" class="btn btn-danger" style="margin-right: 5px;" <?php echo ($hdr['isClose']=='N'?'':'disabled'); ?> >
						<i class="glyphicon glyphicon-remove"></i> Remove Approved SO
						</button>

						<button type="button" id="btn_close_so" class="btn btn-danger pull-right" <?php echo (($hdr['statusCode']=='P' AND $hdr['isClose']=='N')?'':'disabled'); ?>>
						<i class="glyphicon glyphicon-ok-sign">
						</i> Close Sales Order
						</button>
						
					<?php }else{ //.else isClose ?>
						<button type="button" id="btn_reopen_closed_so" class="btn btn-warning pull-right" <?php echo (($hdr['statusCode']=='P' AND $hdr['isClose']=='Y')?'':'disabled'); ?>>
						<i class="glyphicon glyphicon-refresh">
						</i> Re-Open Closed Sales Order.
						</button>

						
						
					<?php } //.if isClose ?>
					
					<?php break; 
					default : ?>				
				<?php } //.switch statusCode ?> 
		  
          <button type="button" id="btn_approve" class="btn btn-success pull-right" style="margin-right: 5px;" <?php echo ($hdr['statusCode']=='C'?'':'disabled'); ?> >
		 <i class="glyphicon glyphicon-check">
			</i> Approve
          </button>
		  
		  <button type="button" id="btn_reject" class="btn btn-warning pull-right" style="margin-right: 5px;" <?php echo ($hdr['statusCode']=='C'?'':'disabled'); ?>>
		  <i class="glyphicon glyphicon-remove">
			</i> Reject
          </button>
		  <?php break; default : } ?>
		  
          <button type="button" id="btn_verify" class="btn btn-primary pull-right" style="margin-right: 5px;" <?php echo ($hdr['statusCode']=='B'?'':'disabled'); ?> >
            <i class="glyphicon glyphicon-ok"></i> Confirm
          </button>      
		  </button>   
		  
          <button type="button" id="btn_delete" class="btn btn-danger pull-right" style="margin-right: 5px;" <?php echo ((($hdr['statusCode']<>'P' AND $hdr['statusCode']<>'X' ) AND ($hdr['revCount']==0))?'':'disabled'); ?> >
            <i class="glyphicon glyphicon-trash"></i> Delete
          </button>
		  
		  
		  <?php break; 
			default : ?>				
			<?php } //switch sales Roll ?> 
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
 




<!-- Modal -->
<div id="modal_reason" class="modal fade" role="dialog">
  <div class="modal-dialog modal-md">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Cancel Approved Sales Order</h4>
      </div>
      <div class="modal-body">
        <div class="form-horizontal">
			<div class="form-group">	
				<label for="txt_reason" class="control-label col-md-4">Reason/Remark : </label>
				<div class="col-md-6">
					<textarea class="form-control" id="txt_reason"></textarea>
				</div>
			</div>
		
		</form>
      </div>
      <div class="modal-footer">
		<button type="button" class="btn btn-danger" id="btn_reason_ok" >OK</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
      </div>
    </div>

  </div>
</div>




 
  
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

	$('#btn_remove').click(function(e) {
	  e.preventDefault();
	  $.smkPrompt({
	    text:'Enter your password to remove approved SO.',
	    accept:'Remove Approved SO.',
	    cancel:'Cancel'
	  },function(res){
	    // Code here
	    if (res) {
	      var params = {
	      		action: 'remove',					
				soNo: $('#soNo').val(),
				pw: res
			};	
			if(params.pw.trim()==""){
				alert('password is required.');
				return false;
			}
			$.post({
				url: '<?=$rootPage;?>_ajax.php',
				data: params,
				dataType: 'json'
			}).done(function(data) {
				if (data.success){  
					$.smkAlert({
						text: data.message,
						type: 'success',
						position:'top-center'
					});
					window.location.href = "<?=$rootPage;?>_view.php?soNo=" + data.soNo;
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
	    } else {
	      //Do nothing.
	    }
	  });
	});

	//Reason Begin
	$('#btn_revise').click(function(){
		$('#modal_reason').modal('show');
	});			
	$('#btn_reason_ok').click(function(){
		var params = {
			action: 'revise',					
			soNo: $('#soNo').val(),
			reason: $('#txt_reason').val()
		};	
		if(params.reason.trim()==""){
			alert('Reason/Remark is required.');
			$('#txt_reason').select();
			return false;
		}
		if (confirm('Are you sure to Edit Approved Sales Order ?')) {
			// Save it!
			$.post({
				url: '<?=$rootPage;?>_ajax.php',
				data: params,
				dataType: 'json'
			}).done(function(data) {
				if (data.success){  
					$.smkAlert({
						text: data.message,
						type: 'success',
						position:'top-center'
					});
					window.location.href = "<?=$rootPage;?>_view.php?soNo=" + data.soNo;
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
		} else {
			// Do nothing!
		}
		//$.smkConfirm({text:'Are you sure to Edit Approved Sales Order ?',accept:'Yes', cancel:'Cancel'}, function (e){if(e){
			
		//}});
		//smkConfirm
		//$('#modal_search').modal('hide');
	});	
	//Reason End


	
$('#btn_verify').click (function(e) {				 
	var params = {			
	action: 'confirm',	
	soNo: $('#soNo').val(),
	hdrTotal: $('#hdrTotal').val(),
	hdrVatAmount: $('#hdrVatAmount').val(),
	hdrNetTotal: $('#hdrNetTotal').val()					
	};
	//alert(params.hdrID);
	$.smkConfirm({text:'Are you sure to Confirm ?',accept:'Yes', cancel:'Cancel'}, function (e){if(e){
		$.post({
			url: '<?=$rootPage;?>_ajax.php',
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
	}});
	//smkConfirm
});
//.btn_click

$('#btn_reject').click (function(e) {				 
	var params = {
	action: 'reject',					
	soNo: $('#soNo').val()					
	};
	//alert(params.hdrID);
	$.smkConfirm({text:'Are you sure to Reject ?',accept:'Yes', cancel:'Cancel'}, function (e){if(e){
		$.post({
			url: '<?=$rootPage;?>_ajax.php',
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
	}});
	//smkConfirm
});
//.btn_click

$('#btn_approve').click (function(e) {				 
	var params = {
	action: 'approve',					
	soNo: $('#soNo').val()				
	};
	//alert(params.hdrID);
	$.smkConfirm({text:'Are you sure to Approve ?',accept:'Yes', cancel:'Cancel'}, function (e){if(e){
		$.post({
			url: '<?=$rootPage;?>_ajax.php',
			data: params,
			dataType: 'json'
		}).done(function(data) {
			if (data.success){  
				$.smkAlert({
					text: data.message,
					type: 'success',
					position:'top-center'
				});
				window.location.href = "<?=$rootPage;?>_view.php?soNo=" + data.soNo;
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
	}});
	//smkConfirm
});
//.btn_click


$('#btn_close_so').click (function(e) {				 
	var params = {
	action: 'close',					
	soNo: '<?=$soNo;?>'			
	};
	//alert(params.hdrID);
	$.smkConfirm({text:'Are you sure to Close ?',accept:'Yes', cancel:'Cancel'}, function (e){if(e){
		$.post({
			url: '<?=$rootPage;?>_ajax.php',
			data: params,
			dataType: 'json'
		}).done(function(data) {
			if (data.success){  
				$.smkAlert({
					text: data.message,
					type: 'success',
					position:'top-center'
				});
				window.location.href = "<?=$rootPage;?>_view.php?soNo=" + data.soNo;
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
	}});
	//smkConfirm
});
//.btn_click

$('#btn_reopen_closed_so').click (function(e) {				 
	var params = {
	action: 'reopen',					
	soNo: '<?=$soNo;?>'			
	};
	//alert(params.hdrID);
	$.smkConfirm({text:'Are you sure to re-open ?',accept:'Yes', cancel:'Cancel'}, function (e){if(e){
		$.post({
			url: '<?=$rootPage;?>_ajax.php',
			data: params,
			dataType: 'json'
		}).done(function(data) {
			if (data.success){  
				$.smkAlert({
					text: data.message,
					type: 'success',
					position:'top-center'
				});
				window.location.href = "<?=$rootPage;?>_view.php?soNo=" + data.soNo;
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
	}});
	//smkConfirm
});
//.btn_click

$('#btn_delete').click (function(e) {				 
	var params = {
	action: 'delete',					
	soNo: $('#soNo').val()				
	};
	//alert(params.hdrID);
	$.smkConfirm({text:'Are you sure to Delete ?', accept:'Yes', cancel:'Cancel'}, function (e){if(e){
		$.post({
			url: '<?=$rootPage;?>_ajax.php',
			data: params,
			dataType: 'json'
		}).done(function(data) {
			if (data.success){  
				alert(data.message);
				window.location.href = '<?=$rootPage;?>.php';
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
