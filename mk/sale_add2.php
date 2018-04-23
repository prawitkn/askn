<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<?php include 'head.php'; /*$s_userID = $row_user['userID'];
        $s_userFullname = $row_user['userFullname'];
        $s_userPicture = $row_user['userPicture'];
		$s_username = $row_user['userName'];
		$s_userGroupCode = $row_user['userGroupCode'];
		$s_smCode = $row_user['smCode'];*/

$rootPage="sales_add2";

		
		$sql = "SELECT hdr.`soNo`, hdr.`poNo`, hdr.`saleDate`, hdr.`custCode`, hdr.`shipToCode`, hdr.`smCode`, hdr.`total`, hdr.`vatAmount`, hdr.`netTotal`
		, hdr.`prodGFC`, hdr.`prodGFM`, hdr.`prodGFT`, hdr.`prodSC`, hdr.`prodCFC`, hdr.`prodEGWM`, hdr.`prodGT`, hdr.`prodCSM`, hdr.`prodWR`
		, hdr.`deliveryDate`, hdr.`deliveryRem`, hdr.`deliveryRemImg`, hdr.`sentDate`, hdr.`sentById`, hdr.`suppTypeFact`, hdr.`suppTypeImp`
		, hdr.`prodTypeOld`, hdr.`prodTypeNew`, hdr.`custTypeOld`, hdr.`custTypeNew`, hdr.`prodStkInStk`, hdr.`prodStkOrder`, hdr.`prodStkOther`, hdr.`prodStkRem`
		, hdr.`packTypeAk`, hdr.`packTypeNone`, hdr.`packTypeOther`, hdr.`packTypeRem`, hdr.`priceOnOrder`, hdr.`priceOnOther`, hdr.`priceOnRem`, hdr.`remark`
		, hdr.`plac2deliCode`, hdr.`plac2deliRem`, hdr.`payTypeCode`, hdr.`payTypeRem`, hdr.`isClose`, hdr.`statusCode`
		, hdr.`createTime`, hdr.`createByID`, hdr.`updateTime`, hdr.`updateById`, hdr.`confirmTime`, hdr.`confirmById`, hdr.`approveTime`, hdr.`approveById` 
		FROM `sale_header` hdr
		WHERE 1 
		AND hdr.statusCode='B' AND hdr.createByID=:s_userID 
		";
		
		$stmt = $pdo->prepare($sql);
		$stmt->bindParam(':s_userID', $s_userID);	
		$stmt->execute();
		$hdr = $stmt->fetch();
		$soNo = $hdr['soNo'];
		$poNo = $hdr['poNo'];
		if($stmt->rowCount() >= 1){
			switch($s_userGroupCode){ 
				case 'it' : 
				case 'admin' :
					break;
				case 'sales' :
				case 'salesAdmin' :
					//if($hdr['toCode']!=$s_userDept) { header("Location: access_denied.php"); exit();}			
					break;
				default :
			}	
		}
?>
    
<div class="wrapper">

  <!-- Main Header -->
  <?php include 'header.php'; ?>
  
  <!-- Left side column. contains the logo and sidebar -->
   <?php include 'leftside.php'; ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->	
    <section class="content-header">
		<h1><i class="glyphicon glyphicon-arrow-down"></i>
       Sales
        <small>Sales management</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?=$rootPage;?>.php"><i class="glyphicon glyphicon-list"></i>Sales List</a></li>
		<li><a href="#"><i class="glyphicon glyphicon-edit"></i>Sales</a></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

      <!-- Your Page Content Here -->
    <div class="box box-primary">
        <div class="box-header with-border">
        <h3 class="box-title">Add Sales Order No. : <?=$soNo;?></h3>
		
		
        <div class="box-tools pull-right">
          <!-- Buttons, labels, and many other things can be placed here! -->
          <!-- Here is a label for example -->
         
        </div><!-- /.box-tools -->
        </div><!-- /.box-header -->
        <div class="box-body">			
			<ul class="nav nav-tabs">
			  <li class="active"><a data-toggle="tab" href="#primary">Primary</a></li>
			  <li><a data-toggle="tab" href="#option">Other</a></li>
			</ul>

			<div class="tab-content">
			  <div id="primary" class="tab-pane fade in active">
				<div class="col-md-6">   
					<div class="row">
						<div class="col-md-6 form-group">
                            <label for="saleDate">sale Date</label>
                            <input id="saleDate" type="text" class="form-control datepicker" name="saleDate" data-smk-msg="Require sale Date." required>
                        </div>
						<div class="col-md-6 form-group">
                            <label for="poNo">PO No.</label>
                            <input type="text" name="poNo" id="poNo" class="form-control" data-smk-msg="Require PO No." required>
                        </div>						
					</div>
						
						<div class="form-group">
                            <label for="custCode">Customer Name</label>
                            <select id="custCode" name="custCode" class="form-control" data-smk-msg="Require Customer." required >
								<option value=""> -- Select -- </option>
								<?php
								$sql_cust = "SELECT `code`, `custAddr`, `custName`, `custContact`, `custTel`, `smCode` FROM `customer` ";
								$sql_cust .= " WHERE `statusCode`='A' ";
								$sql_cust .= $sqlCust;
								$sql_cust .= " ORDER BY `custName` ASC ";
								$result_cust = mysqli_query($link, $sql_cust);
								while($row = mysqli_fetch_assoc($result_cust)){
									echo '<option value="'.$row['code'].'" 
										 data-custAddr="'.$row['custAddr'].'" 									 
										 data-smCode="'.$row['smCode'].'" 
										 >'.$row['custName'].' : ['.$row['code'].']</option>';
								}
								?>
							</select> 
                        </div>
						
                        <div class="form-group">
                            <label for="smCode">Salesman Name</label>							
							<select id="smCode" name="smCode" class="form-control" data-smk-msg="Require Salesman." required>
								<option value=""> -- Select -- </option>
								<?php
								$sql_sm = "SELECT `code`,  `name`, `surname`, `mobileNo`, `email` FROM `salesman` WHERE `statusCode`='A' ";
								$result_sm = mysqli_query($link, $sql_sm);
								while($row = mysqli_fetch_assoc($result_sm)){
									echo '<option value="'.$row['code'].'">'.$row['code'].' : '.$row['name'].' '.$row['surname'].'</option>';
								}
								?>
							</select>                            
                        </div>						
						
						<div class="form-group">
                            <label for="custAddr">Customer Address</label>
							<textarea id="custAddr" class="form-control" name="custAddr" disabled></textarea>
                        </div>
                        
						<div class="row">
							<div class="col-md-6 form-group">
								<label for="deliveryDate">Delivery Date / Load Date</label>
								<input type="text" id="deliveryDate" name="deliveryDate" class="form-control datepicker" data-smk-msg="Require Delivery Date / Load Date." required>
							</div>
							<div class="col-md-6 form-group">
								<label for="deliveryRem">Delivery Remark / Load Remark</label>
								<input type="text" id="deliveryRem" name="deliveryRem" class="form-control" >
							</div>
						</div>
						
						<div class="form-group">
							<label for="remark">sales Remark</label>
							  <input type="text" id="remark" name="remark" class="form-control" />
						  </div>
					</div><!--col-md6-->
					
			  </div><!-- end primary content tab-->
			  
			  <div id="option" class="tab-pane fade">
				<h3>Option</h3>
				<p>Some content in menu 1.</p>
			  </div><!-- end oftion content tab-->
			  
			</div><!--tab-content-->
		
		
		
		
		
		
            <div class="row">
				<form id="form1" action="delivery_add_insert.php" method="post" class="form" novalidate>				
                <div class="col-md-12">   
					<div class="row">
						<div class="col-md-3">
							<label for="sdNo" >Sending No.</label>
							<div class="form-group row">
								<div class="col-md-9">
									<input type="text" name="sdNo" class="form-control" <?php echo ($sdNo==''?'':' value="'.$sdNo.'" disabled '); ?>  />
								</div>
								<div class="col-md-3">
									<a href="#" name="btnSdNo" class="btn btn-primary" <?php echo ($sdNo==''?'':' disabled '); ?> ><i class="glyphicon glyphicon-search" ></i></a>								
								</div>
							</div>
							<!--from group-->
							
                        </div>		
						<!--col-md-6-->			
						<div class="col-md-3">					  
					  <div class="from-group">
						<label for="fromName">From</label>
						<input type="text" id="fromName" name="fromName" value="<?=$hdr['fromName'];?>" class="form-control" disabled>
					</div>
					<!--from group-->
				</div>
				<!-- col-md-->
				
				<div class="col-md-3">					  
				  <!-- checkbox -->
					<div class="from-group">
						<label for="toName">To</label>
						<input type="text" id="toName" name="toName" value="<?=$hdr['toName'];?>" class="form-control" disabled>
					</div>
					<!--from group-->		  
				</div>
				<!-- col-md-->
				
					</div>	
					<!--row-->
					
		<div class="row">
			<div class="col-md-3">		
				<div class="from-group">
				<label for="receiveDate">Receive Date</label>
				<input type="text" id="receiveDate" name="receiveDate" class="form-control datepicker" data-smk-msg="Require Order Date." required <?php echo ($sdNo==''?'':' disabled '); ?> >
				</div>
				<!--from group-->				
			</div>
			<!--col-md-->			
			<div class="col-md-6">	
				<div class="from-group">
					<label for="remark">Remark</label>
					<input type="text" id="remark" name="remark" value="<?=$hdr['remark'];?>" class="form-control" <?php echo ($sdNo==''?'':' disabled '); ?> >
				</div>
				<!--from group-->
			</div>
			<!--col-md-->
			<div class="col-md-3">	
				<!--<div class="from-group">
				<label for="refNo">Ref No.</label>
				<input type="text" id="refNo" name="refNo" value="<?=$hdr['refNo'];?>" class="form-control" <?php echo ($sdNo==''?'':' disabled '); ?> >
				</div>
				from group-->
			</div>
			<!--col-md-->
		</div>
		<!--row-->
		<div class="row" <?php echo ($sdNo==''?'':' style="display: none;" '); ?> >
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
			$sql = "SELECT `id`, `prodItemId`, `prodId`, `prodCode`, `barcode`, `issueDate`, `machineId`, `seqNo`, `NW`, `GW`
			, `qty`, `packQty`, `grade`, `gradeDate`, `refItemId`, `itemStatus`, `remark`, `problemId`, `rcNo` 
			FROM `receive_detail` dtl			
			WHERE 1
			AND dtl.rcNo=:rcNo  
			";
			$stmt = $pdo->prepare($sql);
			$stmt->bindParam(':rcNo', $rcNo);		
			$stmt->execute();
			$rowCount = $stmt->rowCount();
		?>

		<div class="row col-md-12" <?php echo ($sdNo!=''?'':' style="display: none;" '); ?> >
			<div class="box-header with-border">
				<h3 class="box-title">Item List</h3>				
				
				<div class="box-tools pull-right">
				  <span class="label label-primary">Total <?=$rowCount; ?> items</span>
				</div><!-- /.box-tools -->
			</div><!-- /.box-header -->
				
			<form id="form2" action="delivery_add_item_submit_ajax.php" method="post" class="form" novalidate>
				<input type="hidden" name="rcNo" value="<?=$hdr['rcNo'];?>" />
				
				<div class="table-responsive">
				<table id="tbl_items" class="table table-striped">
					<tr>
						<th>No.</th>
						<th>Product Code</th>
						<th>Barcode</th>
						<th>Grade</th>
						<th>Net<br/>Weight(kg.)</th>
						<th>Gross<br/>Weight(kg.)</th>
						<th>Qty</th>
						<th>Produce Date</th>
					</tr>
					<?php $row_no=1; while ($row = $stmt->fetch()) { 
					?>
					<tr>
						<td><?= $row_no; ?></td>
						<td><?= $row['prodCode']; ?></td>	
						<td><?= $row['barcode']; ?></td>	
						<td style="text-align: center;"><?= $row['grade']; ?></td>	
						<td style="text-align: right;"><?= $row['NW']; ?></td>	
						<td style="text-align: right;"><?= $row['GW']; ?></td>	
						<td style="text-align: right;"><?= number_format($row['qty'],0,'.',','); ?></td>
						<td><?= $row['issueDate']; ?></td>	
						<td>
							
						</td>
					</tr>
					<?php $row_no+=1; } ?>
				</table>
				</div>
				<!--/.table-responsive-->
				
				<a name="btn_view" href="receive_view.php?rcNo=<?=$rcNo;?>" class="btn btn-default"><i class="glyphicon glyphicon-search"></i> View</a>
				</form>
			</div>
			<!--/.row dtl-->
		
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





  
<!-- Modal -->
<div id="modal_search_person" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Search Sending No.</h4>
      </div>
      <div class="modal-body">
        <div class="form-horizontal">
			<div class="form-group">	
				<label for="year_month" class="control-label col-md-2">SD NO.</label>
				<div class="col-md-4">
					<input type="text" class="form-control" id="txt_search_fullname" />
				</div>
			</div>
		
		<table id="tbl_search_person_main" class="table">
			<thead>
				<tr bgcolor="4169E1" style="color: white; text-align: center;">
					<td>#Select</td>
					<td>Sending No.</td>
					<td>Send Date</td>
					<td>Send From</td>
					<td>Send To</td>
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
				alert('search name surname must more than 3 character.');
				return false;
			}
			/* Send the data using post and put the results in a div */
			  $.ajax({
				  url: "search_sending_ajax.php",
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
										'<td>'+ value.sdNo +'</td>' +
										'<td>'+ value.sendDate +'</td>' +
										'<td>'+ value.fromCode+' : '+value.fromName+'</td>' +
										'<td>'+ value.toCode+' : '+value.toName+'</td>' +
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
			$.smkConfirm({text:'Are you sure to Create? ?',accept:'Yes.', cancel:'Cancel'}, function (e){if(e){
				$.post({
					url: 'receive_add_insert_ajax.php',
					data: $("#form1").serialize(),
					dataType: 'json'
				}).done(function(data) {
					if (data.success){  
						$.smkAlert({
							text: data.message,
							type: 'success',
							position:'top-center'
						});
						window.location.href = "receive_add.php?rcNo=" + data.rcNo;
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
		<?php if(isset($hdr['receiveDate'])){ ?>
		var queryDate = '<?=$hdr['receiveDate'];?>',
		dateParts = queryDate.match(/(\d+)/g)
		realDate = new Date(dateParts[0], dateParts[1] - 1, dateParts[2]); 
		$('#receiveDate').datepicker('setDate', realDate);
		<?php }else{ ?> $('#receiveDate').datepicker('setDate', '0'); <?php } ?>
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