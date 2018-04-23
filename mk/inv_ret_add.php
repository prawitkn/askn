<?php
  //  include '../db/database.php';
?>
<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<?php include 'head.php'; /*$s_userFullname = $row_user['userFullname'];
        $s_userPicture = $row_user['userPicture'];
		$s_username = $row_user['userName'];
		$s_userGroupCode = $row_user['userGroupCode'];
		$s_userDept = $row_user['userDept'];*/
$rootPage='inv_ret';
switch($s_userGroupCode){ 
	case 'it' : 
	case 'admin' : 
	case 'warehouse' :
		break;
	case 'production' :
		 header("Location: access_denied.php"); exit();	
		break;
	default :
}			
		$sql = "SELECT hdr.`docNo`, hdr.`refNo`, hdr.`docDate`, hdr.`custCode`, hdr.`smCode`, hdr.`totalExcVat`, hdr.`vatAmount`, hdr.`totalIncVat`
		, hdr.`remark`, hdr.`statusCode`
		, hdr.`createTime`, hdr.`createByID`, hdr.`confirmTime`, hdr.`confirmById`, hdr.`approveTime`, hdr.`approveById` 
		, ct.custName, ct.custAddr, ct.taxId, ct.creditDay 
		, concat(sm.name, '  ', sm.surname) as smFullname 
		, sh.soNo, sh.poNo 
		, uca.userFullname as createByName, ucf.userFullname as confirmByName, uap.userFullname as approveByName
		FROM `inv_ret` hdr
		LEFT JOIN invoice_header inv on inv.invNo=hdr.refNo 
		LEFT JOIN  delivery_header dh on dh.doNo=inv.doNo 			
		LEFT JOIN  prepare pa on pa.ppNo=dh.ppNo 				
		LEFT JOIN  picking pi on pi.pickNo=pa.pickNo
		LEFT JOIN sale_header sh on sh.soNo=pi.soNo 
		LEFT JOIN customer ct on ct.code=hdr.custCode ";
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
		$sql .= "
		LEFT JOIN salesman sm on sm.code=hdr.smCode 
		LEFT JOIN user uca on hdr.createByID=uca.userID
		LEFT JOIN user ucf on hdr.confirmByID=ucf.userID
		LEFT JOIN user uap on hdr.approveByID=uap.userID

		WHERE 1 
		AND hdr.statusCode='B' AND hdr.createByID=:s_userID ";
				
		$stmt = $pdo->prepare($sql);
		$stmt->bindParam(':s_userID', $s_userID);
		switch($s_userGroupCode){
			case 'it' : case 'admin' : 
				break;
			case 'sales' : $stmt->bindParam(':s_smCode', $s_smCode);
				break;
			case 'salesAdmin' : $stmt->bindParam(':s_smCode', $s_smCode);
				break;
			default : 
		}			
		$stmt->execute();
		$hdr = $stmt->fetch();
		$docNo = $hdr['docNo'];
		$refNo = $hdr['refNo'];
?>
   
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
	  <h1><i class="glyphicon glyphicon-arrow-left"></i>
       Customer Return
        <small>Customer Return management</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?=$rootPage;?>.php"><i class="glyphicon glyphicon-list"></i>Customer Return List</a></li>
		<li><a href="<?=$rootPage;?>_add.php?docNo=<?=$docNo;?>"><i class="glyphicon glyphicon-edit"></i>No. <?=$docNo;?></a></li>
    </section>

    <!-- Main content -->
    <section class="content">

      <!-- Your Page Content Here -->
    <div class="box box-primary">
        <div class="box-header with-border">
        <h3 class="box-title">Add Customer Return No. : <?=$docNo;?></h3>
		
		
		<input type="hidden" id="docNo" value="<?=$docNo;?>" />
		
        <div class="box-tools pull-right">
          <!-- Buttons, labels, and many other things can be placed here! -->
          <!-- Here is a label for example -->
         
        </div><!-- /.box-tools -->
        </div><!-- /.box-header -->
        <div class="box-body">			
            <div class="row">
				<form id="form1" action="#" method="post" class="form" novalidate>				
                <div class="col-md-12">   
					<div class="row">
						<div class="col-md-3">
							<label for="refNo" >Referene Invoice No.</label>
							<div class="form-group row">
								<div class="col-md-9">
									<input type="text" name="refNo" id="refNo" class="form-control" <?php echo ($docNo==''?'':' value="'.$hdr['refNo'].'" disabled '); ?>  />
								</div>
								<div class="col-md-3">
									<a href="#" name="btnRefNo" class="btn btn-primary" <?php echo ($docNo==''?'':' disabled '); ?> ><i class="glyphicon glyphicon-search" ></i></a>								
								</div>
							</div>
							<!--from group-->
														
                        </div>		
						<!--col-md-6-->			
						<div class="col-md-3">					  
					  <div class="from-group">
						<label for="fromName">Customer</label>
						<input type="text" id="fromName" name="fromName" value="<?=$hdr['custName'];?>" class="form-control" disabled>
					</div>
					<!--from group-->
				</div>
				<!-- col-md-->
				
				<div class="col-md-3">					  
				  <!-- checkbox -->
					<div class="from-group">
						<label for="toName">Salesman</label>
						<input type="text" id="toName" name="toName" value="<?=$hdr['smFullname'];?>" class="form-control" disabled>
					</div>
					<!--from group-->		  
				</div>
				<!-- col-md-->
				
					</div>	
					<!--row-->
					
		<div class="row">
			<div class="col-md-3">		
				<div class="from-group">
				<label for="docDate">Date</label>
				<input type="text" id="docDate" name="docDate" class="form-control datepicker" data-smk-msg="Require Order Date." required <?php echo ($docNo==''?'':' disabled '); ?> >
				</div>
				<!--from group-->				
			</div>
			<!--col-md-->
			<div class="col-md-6">	
				<div class="from-group">
					<label for="remark">Remark</label>
					<input type="text" id="remark" name="remark" value="<?=$hdr['remark'];?>" class="form-control" <?php echo ($docNo==''?'':' disabled '); ?> >
				</div>
				<!--from group-->
			</div>
			<!--col-md-->
			<div class="col-md-3">	
			</div>
			<!--col-md-->
		</div>
		<!--row-->
		<div class="row" <?php echo ($docNo==''?'  ':' style="display: none;" '); ?> >
			<div class="col-md-12">					
				<a name="btn_create" href="#" class="btn btn-default"><i class="glyphicon glyphicon-plus" ></i> Create</a>
			</div>
		</div>
		<!--row-->
					
				</div>
				<!-- col-md-6 --> 
						
				
				
				

			
			</form>			
            </div>   
			<!--/.row hdr-->
			
			
			<?php
			$sql = "SELECT dtl.`id`, dtl.`prodItemId`, dtl.`prodCode`, dtl.`returnReasonCode`, dtl.`returnReasonRemark`
			, itm.barcode, itm.qty 
			, rrt.name as returnReasonName 
			FROM `inv_ret_detail` dtl	
			LEFT JOIN product_item itm on itm.prodItemId=dtl.prodItemId 
			LEFT JOIN wh_inv_return_reason_type rrt on rrt.code=dtl.returnReasonCode 
			WHERE 1
			AND dtl.docNo=:docNo  
			";
			$stmt = $pdo->prepare($sql);
			$stmt->bindParam(':docNo', $docNo);		
			$stmt->execute();
			$rowCount = $stmt->rowCount();
		?>

		<div class="row col-md-12" <?php echo ($docNo==''?' style="display: none;" ':'  '); ?> >
			<div class="box-header with-border">
				<h3 class="box-title">Item List</h3>				
				<a name="btn_search_prod" href="#" class="btn btn-default"><i class="glyphicon glyphicon-plus" ></i> Add item</a>
				
				<div class="box-tools pull-right">
				  <span class="label label-primary">Total <?=$rowCount; ?> items</span>
				</div><!-- /.box-tools -->
			</div><!-- /.box-header -->
				
			<form id="form2" action="delivery_add_item_submit_ajax.php" method="post" class="form" novalidate>
				<input type="hidden" name="rtNo" value="<?=$hdr['rtNo'];?>" />
				
				<div class="table-responsive">
				<table id="tbl_items" class="table table-striped">
					<tr>
						<th>No.</th>
						<th>Product Code</th>
						<th>Barcode</th>
						<th>Qty</th>
						<th>Return Type</th>
						<th>Return Remark</th>
						<th>#</th>
					</tr>
					<?php $row_no=1; while ($row = $stmt->fetch()) { 
					?>
					<tr>
						<td><?= $row_no; ?></td>
						<td><?= $row['prodCode']; ?></td>	
						<td><?= $row['barcode']; ?></td>	
						<td style="text-align: right;"><?= number_format($row['qty'],0,'.',','); ?></td>
						<td><?= $row['returnReasonName']; ?></td>	
						<td><?= $row['returnReasonRemark']; ?></td>	
						<td><a class="btn btn-danger fa fa-trash" name="btn_row_delete" <?php echo ($hdr['statusCode']=='B'?' data-id="'.$row['id'].'" ':' disabled '); ?> > Delete</a></td>
					</tr>
					<?php $row_no+=1; } ?>
				</table>
				</div>
				<!--/.table-responsive-->
				
				<a name="btn_view" href="<?=$rootPage;?>_view.php?docNo=<?=$docNo;?>" class="btn btn-default"><i class="glyphicon glyphicon-search"></i> View</a>
				</form>
			</div>
			<!--/.row dtl-->
		
    </div><!-- /.box-body -->
  <div class="box-footer">
  
  
  
  
  
<!-- Modal -->
<div id="modal_search_person" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Search Invoice No.</h4>
      </div>
      <div class="modal-body">
        <div class="form-horizontal">
			<div class="form-group">	
				<label for="year_month" class="control-label col-md-2">Invoice No.</label>
				<div class="col-md-4">
					<input type="text" class="form-control" id="txt_search_fullname" />
				</div>
			</div>
		
		<table id="tbl_search_person_main" class="table">
			<thead>
				<tr bgcolor="4169E1" style="color: white; text-align: center;">
					<td>#Select</td>
					<td>Invoice No.</td>
					<td>Invoice Date</td>
					<td>Customer</td>
					<td>Salesman</td>
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

  
	//SEARCH Begin
	$('a[name="btnRefNo"]').click(function(){
		//prev() and next() count <br/> too.		
		$btn = $(this).closest("div").prev().find('input');
		curId = $btn.attr('name');
		//curId = $(this).prev().attr('name');
		curTxtFullName = $(this).attr('id');
		if(!$btn.prop('disabled')){
			$('#modal_search_person').modal('show');
		}
				
	});	
	$('#txt_search_fullname').keyup(function(e){
		if(e.keyCode == 13)
		{
			var params = {
				search_fullname: $('#txt_search_fullname').val()
			};
			if(params.search_fullname.length < 3){
				alert('Search keyword must more than 3 character.');
				return false;
			}
			/* Send the data using post and put the results in a div */
			  $.ajax({
				  url: "search_invoice_ajax.php",
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
										'<td>'+ value.invNo +'</td>' +
										'<td>'+ value.invoiceDate +'</td>' +
										'<td>'+ value.custCode+' : '+value.custName+'</td>' +
										'<td>'+ value.smCode+' : '+value.smName+'</td>' +
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


	
	$('#form1 a[name=btn_create]').click (function(e) {
		if ($('#form1').smkValidate()){
			$.smkConfirm({text:'Are you sure to Create ?',accept:'Yes.', cancel:'Cancel'}, function (e){if(e){
				$.post({
					url: '<?=$rootPage;?>_add_hdr_insert_ajax.php',
					data: $("#form1").serialize(),
					dataType: 'json'
				}).done(function(data) {
					if (data.success){  
						$.smkAlert({
							text: data.message,
							type: 'success',
							position:'top-center'
						});
						window.location.href = "<?=$rootPage;?>_add.php?docNo=" + data.docNo;
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
		e.preventDefault();
		}//.if end
	});
	//.btn_click
	
	$('a[name=btn_row_delete]').click(function(){
		var params = {
			id: $(this).attr('data-id')
		};
		//alert(params.id);
		$.smkConfirm({text:'Are you sure to Delete ?',accept:'Yes', cancel:'Cancel'}, function (e){if(e){
			$.post({
				url: '<?=$rootPage;?>_add_item_delete_ajax.php',
				data: params,
				dataType: 'json'
			}).done(function (data) {					
				if (data.success){ 
					$.smkAlert({
						text: data.message,
						type: 'success',
						position:'top-center'
					});
					location.reload();
				} else {
					alert(data.message);
					location.reload();
				}
			}).error(function (response) {
				alert(response.responseText);
			}); 
		}});
		e.preventDefault();
	});
	//btn_click

	
	$('#form2 a[name=btn_submit]').click (function(e) {
		if ($('#form2').smkValidate()){
			$.smkConfirm({text:'Are you sure to Submit ?',accept:'Yes', cancel:'Cancel'}, function (e){if(e){
				$.post({
					url: '<?=$rootPage;?>_add_item_submit_ajax.php',
					data: $("#form2").serialize(),
					dataType: 'json'
				}).done(function(data) {
					if (data.success){  
						$.smkAlert({
							text: data.message,
							type: 'success',
							position:'top-center'
						});
						window.location.href = "<?=$rootPage;?>_view.php?docNo=" + data.docNo;
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
		e.preventDefault();
		}//.if end
	});
	//.btn_click
	$('a[name=btn_search_prod]').click(function(e){
		var docNo = '<?=$docNo;?>';
		var refNo = '<?=$hdr['refNo']?>';
		window.location.href = "<?=$rootPage;?>_add_search_prod.php?docNo="+docNo+"&refNo="+refNo;
	});
	
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
		});  //กำหนดเป็นวันปัจุบัน
		//กำหนดเป็น วันที่จากฐานข้อมูล
		<?php if(isset($hdr['docDate'])){ ?>
		var queryDate = '<?=$hdr['docDate'];?>',
		dateParts = queryDate.match(/(\d+)/g)
		realDate = new Date(dateParts[0], dateParts[1] - 1, dateParts[2]); 
		$('#docDate').datepicker('setDate', realDate);
		<?php }else{ ?> $('#docDate').datepicker('setDate', '0'); <?php } ?>
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