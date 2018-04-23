<?php
  //  include '../db/database.php';
?>
<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<?php include 'head.php'; 
/*$s_userFullname = $row_user['userFullname'];
        $s_userPicture = $row_user['userPicture'];
		$s_username = $row_user['userName'];
		$s_userGroupCode = $row_user['userGroupCode'];
		$s_userDept = $row_user['userDept'];*/
$rootPage="invoice";

$sql = "SELECT ih.`invNo`, ih.`doNo`, ih.`refNo`, ih.`invoiceDate`, ih.`custCode`, ih.`smCode`, ih.`totalExcVat`
, ih.`vatAmount`, ih.`totalIncVat`, ih.`remark`, ih.`statusCode`, ih.`createTime`, ih.`createById`
, ih.`updateTime`, ih.`updateById`, ih.`confirmTime`, ih.`confirmById`, ih.`approveTime`, ih.`approveById`
, ct.custName, ct.custAddr, ct.taxId, ct.creditDay 
, concat(sm.name, '  ', sm.surname) as smFullname 
, dh.remark as delivery_remark 
FROM invoice_header ih 			
LEFT JOIN customer ct on ct.code=ih.custCode ";
switch($s_userGroupCode){
	case 'it' : case 'admin' : 
		break;
	case 'sales' : $sql .= " AND ct.smCode=:s_smCode "; break;
	case 'salesAdmin' : 	$sql .= " AND ct.smAdmCode=:s_smCode "; break;
	default : 
		//return JSON
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'Access Denied.'));
		exit();
}		
$sql .= "LEFT JOIN salesman sm on sm.code=ih.smCode 
LEFT JOIN delivery_header dh on dh.doNo=ih.doNo 
WHERE ih.statusCode in ('A','B') AND ih.createByID=:s_userID

		";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':s_userID', $s_userID);	
switch($s_userGroupCode){
	case 'it' : case 'admin' : 
		break;
	case 'sales' : $stmt->bindParam(':s_smCode', $s_userID);
		break;
	case 'salesAdmin' : $stmt->bindParam(':s_smCode', $s_userID);
		break;
	default : 
}	
$stmt->execute();
$hdr = $stmt->fetch();
$invNo = $hdr['invNo'];
$doNo = $hdr['doNo'];
			
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
       <h1><i class="glyphicon glyphicon-usd"></i>
       Invoice
        <small>Invoice management</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?=$rootPage;?>.php"><i class="glyphicon glyphicon-list"></i>Invoice List</a></li>	
		<li><a href="<?=$rootPage;?>_add.php?invNo=<?=$invNo;?>"><i class="glyphicon glyphicon-edit"></i>Invoice No.<?=$invNo;?></a></li>		
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

      <!-- Your Page Content Here -->
    <div class="box box-primary">
        <div class="box-header with-border">
        <h3 class="box-title">Add Invoice No. : <?=$invNo;?></h3>
		
		<div class="box-tools pull-right">
			<?php $statusName = '<b style="color: red;">Unknown</b>'; switch($hdr['statusCode']){
						case 'A' : $statusName = '<b style="color: orange;">Incomplete</b>'; break;
						case 'B' : $statusName = '<b style="color: blue;">Begin</b>'; break;
						case 'C' : $statusName = '<b style="color: blue;">Confirmed</b>'; break;
						case 'P' : $statusName = '<b style="color: green;">Approved</b>'; break;
						default : 
			} ?>
			<h3 class="box-title" id="statusName">Status : <?= $statusName; ?></h3>
		</div><!-- /.box-tools -->
        </div><!-- /.box-header -->
        <div class="box-body">			
            <div class="row">
				<form id="form1" action="invoice_add_insert_ajax.php" method="post" class="form" novalidate>				
                <div class="col-md-12">   
					<div class="row">
						<div class="col-md-3">
							<label for="doNo" >Delivery No.</label>
							<div class="form-group row">
								<div class="col-md-9">
									<input type="text" name="doNo" id="doNo" class="form-control" <?php echo ($invNo==''?'':' value="'.$doNo.'" disabled '); ?>  />
								</div>
								<div class="col-md-3">
									<a href="#" name="btnSdNo" class="btn btn-primary" <?php echo ($invNo==''?'':' disabled '); ?> ><i class="glyphicon glyphicon-search" ></i></a>								
								</div>
							</div>
							<!--from group-->
							
						</div>
						<!--col-->
						
						<div class="col-md-3">
							<div class="from-group">
								<label for="custName">Customer Name</label>
								<input type="text" id="custName" name="custName" value="<?=$hdr['custName'];?>" class="form-control" disabled>
							</div>
							<!--from group-->
						</div>
						<!--col-->
						
						<div class="col-md-3">
							<div class="from-group">
								<label for="smName">Salesman Name</label>
								<input type="text" id="smName" name="smName" value="<?=$hdr['smName'];?>" class="form-control" disabled>
							</div>
							<!--from group-->		 
						</div>
						<!--col-->
						
						<div class="col-md-3">
							<div class="col-md- form-group">
								<label for="invoiceDate">Invoice Date</label>
								<input type="text" id="invoiceDate" name="invoiceDate" class="form-control datepicker" data-smk-msg="Require Order Date." required <?php echo ($invNo==''?'':' disabled '); ?> >
							</div>
						</div>
						<!--col-->
						
											
					</div>	
					<!--row-->
				<div class="row col-md-12">
					<label>Delivery Order Remark : <span style="color: red;"><?=$hdr['delivery_remark'];?> </span></label>
				</div>
				<div class="row">
					<div class="col-md-3">
						<div class="form-group">
							<label for="refNo">Ref. No.</label>
							<input type="text" id="refNo" name="refNo" class="form-control" value="<?=$hdr['refNo'];?>" <?php echo ($invNo==''?'':' disabled '); ?>>
						</div>
					</div>
					<!--col-->
					<div class="col-md-6">
						<div class="form-group">
							<label for="remark">Remark</label>
							<input type="text" id="remark" name="remark" class="form-control" value="<?=$hdr['remark'];?>" <?php echo ($invNo==''?'':' disabled '); ?>>
						</div>
					</div>
					<!--col-->
					
					<div class="col-md-3">
						<div class="form-group">
							<br/>
							<a name="btn_create" href="#" class="btn btn-default"
							<?php echo ($invNo==''?'':' style="display: none;" '); ?>
							><i class="glyphicon glyphicon-plus" ></i> Create</a>
						</div>
					</div>
					<!--col-->
								
				</div>
				<!--row-->
				</div>
				<!--col-md-->
				</form>	
				
            </div>   
			<!--/.row hdr-->
			
			<div class="row">
			<div class="col-md-12">
			<form id="form2" action="invoice_add_save_ajax.php" method="post" class="form" novalidate>
				<input type="hidden" name="invNo" value="<?=$invNo;?>" />
				<?php
					$sql = "SELECT id.`id`, itm.`prodCode`, id.`salesPrice`, itm.`qty`, id.`total`, id.`discPercent`, id.`discAmount`, id.`netTotal`
					, pd.prodName, pd.prodDesc, pd.salesUom 
					FROM `invoice_detail` id
					INNER JOIN invoice_header ih on ih.invNo=id.invNo 
					INNER JOIN product_item itm ON itm.prodItemId=id.prodItemId 
					LEFT JOIN product pd on itm.prodCode=pd.code 
					WHERE 1
					AND ih.invNo=:invNo 
							";
					$stmt = $pdo->prepare($sql);
					$stmt->bindParam(':invNo', $invNo);		
					$stmt->execute();
				?>
				<div class="table-responsive">
				<table id="tbl_items" class="table table-striped">
					<tr>
						<th>No.</th>
						<th>Product Name</th>
						<th>Product Desc</th>
						<th>Qty</th>
						<th>UOM</th>
						<th>Sales Price</th>
						<th>Total</th>
					</tr>
					<?php $row_no=1; while ($row = $stmt->fetch()) { ?>
					<tr>
						<td><?= $row_no; ?></td>
						<td><?= $row['prodName']; ?></td>	
						<td><?= $row['prodDesc']; ?></td>					
						<td style="text-align: right;">
							<input type="hidden" name="id[]" value="<?=$row['id'];?>" />
							<input type="hidden" name="qty[]" value="<?=$row['qty'];?>" />
							<?= number_format($row['qty'],0,'.',','); ?>						
						</td>
						<td><?= $row['salesUom']; ?></td>	
						<td style="text-align: right;">
							<input type="text" class="form-control" name="salesPrice[]" value="<?= number_format($row['salesPrice'],2,'.',','); ?>"  style="text-align: right;" data-smk-msg="Require sales price."required
							onkeypress="return decimalOnly(this, event);" 
							onpaste="return false;"
							<?php echo ($row_no==1?' id="txt_row_first" ':'');?>
								>
						</td>
						<td style="text-align: right;"><?= number_format($row['netTotal'],2,'.',','); ?></td>
					</tr>
					<?php $row_no+=1; } ?>
					
					<?php
						$sql = "SELECT IFNULL(SUM(id.nettotal),0) as netTotal 
						FROM `invoice_detail` id
						WHERE 1
						AND id.invNo=:invNo 
								";
						$stmt = $pdo->prepare($sql);
						$stmt->bindParam(':invNo', $invNo);		
						$stmt->execute();
						$row = $stmt->fetch(PDO::FETCH_ASSOC);
						$totalExcVat = $row['netTotal'];
						$vatAmount = number_format($row['netTotal']*0.07,2,'.','');
						$totalIncVat = $totalExcVat + $vatAmount;
					?>
					<tr>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td><b>Total</b></td>
						<td style="text-align: right;"><input type="hidden" name="totalExcVat" id="totalExcVat" value="<?=$totalExcVat;?>" />
							<b><?= number_format($totalExcVat,2,'.',','); ?></b>
						</td>
					</tr>
					<tr>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td><b>Vat 7%</b></td>
						<td style="text-align: right;"><input type="hidden" name="vatAmount" id="vatAmount" value="<?=$vatAmount;?>" />
							<b><?= number_format($vatAmount,2,'.',','); ?></b>
						</td>
					</tr>
					<tr>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td><b>Net Total</b></td>
						<td style="text-align: right;"><input type="hidden" name="totalIncVat" id="totalIncVat" value="<?=$totalIncVat; ?>" />
							<b><?= number_format($totalIncVat,2,'.',','); ?></b>
						</td>
					</tr>
				</table>				
				</div>
				<!--/.table-responsive-->
				
				<a name="btn_calc" class="btn btn-default"><i class="glyphicon glyphicon-retweet"  <?php echo ($hdr['statusCode']=='B'?'':'disabled'); ?>></i> Calc. & Update</a>
				<!--<a name="btn_submit" href="#" class="btn btn-default"><i class="glyphicon glyphicon-save"  <?php echo ($hdr['statusCode']=='B'?'':'disabled'); ?>></i> Submit</a>-->
				<a name="btn_view" href="invoice_view.php?invNo=<?=$invNo;?>" class="btn btn-default"><i class="glyphicon glyphicon-search"></i> View</a>		  
				
				</form>
				<!--form2-->	
			</div></div>
			<!--/.row col-->
    </div><!-- /.box-body -->
  <div class="box-footer">
      <div class="col-md-12">
		
		
	</div>
	<!-- /.col-md-12 -->
  </div>
  <!-- box-footer -->
  
  
  
  
  <!-- Modal -->
<div id="modal_search_person" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Search Delivery No.</h4>
      </div>
      <div class="modal-body">
        <div class="form-horizontal">
			<div class="form-group">	
				<label for="year_month" class="control-label col-md-2">Delivery NO.</label>
				<div class="col-md-4">
					<input type="text" class="form-control" id="txt_search_fullname" />
				</div>
			</div>
		
		<table id="tbl_search_person_main" class="table">
			<thead>	
				<tr bgcolor="4169E1" style="color: white; text-align: center;">
					<td>#Select</td>
					<td>Delivery No.</td>
					<td>Delivery Date</td>
					<td>SO No.</td>
					<td>Customer</td>
					<td>Salesman</td>
					<td>Remark</td>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
		</form>
		<div id="div_search_person_result">
		</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
      </div>
    </div>

  </div>
</div>






  
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



	//SEARCH Begin
	$('a[name="btnSdNo"]').click(function(){
		//prev() and next() count <br/> too.		
		$btn = $(this).closest("div").prev().find('input');
		curId = $btn.attr('name');
		//curId = $(this).prev().attr('name');
		curTxtFullName = $(this).attr('id');
		if(!$btn.prop('disabled')){
			$('#modal_search_person').modal('show');
		}
		
		//alert(curHidMid+' '+curSlOrgCode+' '+curTxtFullName+' ' +curTxtMobilePhoneNo);
		
	});	
	$('#txt_search_fullname').keyup(function(e){
		if(e.keyCode == 13)
		{
			var params = {
				search_fullname: $('#txt_search_fullname').val()
			};
			if(params.search_fullname.length < 3){
				alert('search keyword must more than 3 character.');
				return false;
			}
			/* Send the data using post and put the results in a div */
			  $.ajax({
				  url: "search_delivery_ajax.php",
				  type: "post",
				  data: params,
				datatype: 'json',
				  success: function(data){	
								$('#tbl_search_person_main tbody').empty();
								$.each($.parseJSON(data), function(key,value){
									$('#tbl_search_person_main tbody').append(
									'<tr>' +
										'<td>' +
										'	<div class="btn-group">' +
										'	<a href="javascript:void(0);" data-name="search_person_btn_checked" ' +
										'	class="btn" title="เลือก"> ' +
										'	<i class="glyphicon glyphicon-ok"></i> เลือก</a> ' +
										'	</div>' +
										'</td>' +
										'<td>'+ value.doNo +'</td>' +
										'<td>'+ value.soNo +'</td>' +
										'<td>'+ value.deliveryDate +'</td>' +
										'<td>'+ value.custName +'</td>' +
										'<td>'+ value.smName +'</td>' +
										'<td>'+ value.remark +'</td>' +
									'</tr>'
									);			
								});
							
				  }, //success  
				  error:function(){
					  alert('error');
				  }   
				}); 
		}/* e.keycode=13 */	
	});
	
	$(document).on("click",'a[data-name="search_person_btn_checked"]',function() {
		
		$('input[name='+curId+']').val($(this).closest("tr").find('td:eq(1)').text());
		//$('#'+curTxtFullName).val($(this).closest("tr").find('td:eq(2)').text());
		//$('#'+curTxtMobilePhoneNo).val($(this).closest('tr').find('td:eq(3)').text());		
		$('#modal_search_person').modal('hide');
	});
	//Search End


	
  //      		
	$('a[name=btn_create]').click (function(e) {
		if ($('#form1').smkValidate()){
			$.smkConfirm({text:'Are you sure to Create ?',accept:'Yes', cancel:'Cancel'}, function (e){if(e){
				$.post({
					url: 'invoice_add_hdr_insert_ajax.php',
					data: $("#form1").serialize(),
					dataType: 'json'
				}).done(function(data) {
					if (data.success){  
						$.smkAlert({
							text: data.message,
							type: 'success',
							position:'top-center'
						});
						window.location.href = "invoice_add.php?invNo=" + data.invNo;
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
				$.smkAlert({ text: 'Cancelled.', type: 'info', position:'top-center'});	
			}});
			//smkConfirm
		e.preventDefault();
		}//.if end
	});
	//.btn_click
	
	$('a[name=btn_calc]').click (function(e) {
		if ($('#form2').smkValidate()){			
				$.post({
					url: 'invoice_add_calc_ajax.php',
					data: $("#form2").serialize(),
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
		e.preventDefault();
		}//.if end
	});
	//.btn_click
	
	$('a[name=btn_submit]').click (function(e) {
		if ($('#form2').smkValidate()){
			totalIncVat = $('#totalIncVat').val();
			if(totalIncVat==0){
				alert('Total is incorrect. Please Calculate before submit.');
				exit;
			}
			$.smkConfirm({text:'Are you sure to Submit ?',accept:'Yes', cancel:'Cancel'}, function (e){if(e){
				$.post({
					url: 'invoice_add_submit_ajax.php',
					data: $("#form2").serialize(),
					dataType: 'json'
				}).done(function(data) {
					if (data.success){  
						$.smkAlert({
							text: data.message,
							type: 'success',
							position:'top-center'
						});
						window.location.href = "invoice_view.php?invNo=" + data.invNo;
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
				$.smkAlert({ text: 'Cancelled.', type: 'info', position:'top-center'});	
			}});
			//smkConfirm
		e.preventDefault();
		}//.if end
	});
	//.btn_click
	
	$("html,body").scrollTop(0);
	$("#statusName").fadeOut('slow').fadeIn('slow').fadeOut('slow').fadeIn('slow');
	
	$('#txt_row_first').select();
});
        
        
   
  </script>
  
  <link href="bootstrap-datepicker-custom-thai/dist/css/bootstrap-datepicker.css" rel="stylesheet" />
    <script src="bootstrap-datepicker-custom-thai/dist/js/bootstrap-datepicker-custom.js"></script>
    <script src="bootstrap-datepicker-custom-thai/dist/locales/bootstrap-datepicker.th.min.js" charset="UTF-8"></script>
  
<script>
	$(document).ready(function () {
		$('.datepicker').datepicker({
			daysOfWeekHighlighted: "0,6",
			autoclose: true,
			format: 'dd/mm/yyyy',
			todayBtn: true,
			language: 'th',             //เปลี่ยน label ต่างของ ปฏิทิน ให้เป็น ภาษาไทย   (ต้องใช้ไฟล์ bootstrap-datepicker.th.min.js นี้ด้วย)
			thaiyear: true              //Set เป็นปี พ.ศ.
		});  
		//กำหนดเป็น วันที่จากฐานข้อมูล
		var queryDate = '<?=$hdr['invoiceDate'];?>',
		dateParts = queryDate.match(/(\d+)/g)
		realDate = new Date(dateParts[0], dateParts[1] - 1, dateParts[2]); 
		$('.datepicker').datepicker('setDate', realDate);
		//จบ กำหนดเป็น วันที่จากฐานข้อมูล
	});
</script>




<!-- Optionally, you can add Slimscroll and FastClick plugins.
     Both of these plugins are recommended to enhance the
     user experience. Slimscroll is required when using the
     fixed layout. -->
</body>
</html>



<!--Integers (non-negative)-->
<script>
  function numbersOnly(oToCheckField, oKeyEvent) {
    return oKeyEvent.charCode === 0 ||
        /\d/.test(String.fromCharCode(oKeyEvent.charCode));
  }
</script>

<!--Decimal points (non-negative)-->
<script>
  function decimalOnly(oToCheckField, oKeyEvent) {        
    var s = String.fromCharCode(oKeyEvent.charCode);
    var containsDecimalPoint = /\./.test(oToCheckField.value);
    return oKeyEvent.charCode === 0 || /\d/.test(s) || 
        /\./.test(s) && !containsDecimalPoint;
  }
</script>