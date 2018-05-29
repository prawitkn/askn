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
		$s_userDeptCode = $row_user['userDeptCode'];
		$s_userID=$_SESSION['userID'];*/
		
$rootPage="picking";

$sql = "SELECT hdr.`pickNo`, hdr.`soNo`, hdr.`pickDate`, hdr.`remark`, hdr.`statusCode`, hdr.`createTime`, hdr.`createById`
, hdr.`updateTime`, hdr.`updateById`, hdr.`confirmTime`, hdr.`confirmById`, hdr.`approveTime`, hdr.`approveById` 
FROM `picking` hdr 
WHERE hdr.statusCode='B' AND hdr.createById=:s_userId 
";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':s_userId', $s_userId);	
$stmt->execute();
$hdr = $stmt->fetch();
$pickNo = $hdr['pickNo'];
$soNo = $hdr['soNo'];
					
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
	  <h1><i class="glyphicon glyphicon-shopping-cart"></i>
       Picking
        <small>Picking management</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?=$rootPage;?>.php"><i class="glyphicon glyphicon-list"></i>Picking List</a></li>
		<li><a href="#"><i class="glyphicon glyphicon-edit"></i>Picking</a></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

      <!-- Your Page Content Here -->
    <div class="box box-primary">
        <div class="box-header with-border">
        <h3 class="box-title">Add Picking No. : <?=$pickNo;?></h3>
        <div class="box-tools pull-right">
          <!-- Buttons, labels, and many other things can be placed here! -->
          <!-- Here is a label for example -->
         
        </div><!-- /.box-tools -->
        </div><!-- /.box-header -->
        <div class="box-body">			
            <div class="row">
				<form id="form1" action="#" method="post" class="form" novalidate>		
				<input type="hidden" name="action" value="add" />		
                <div class="col-md-12">   
					<div class="row">
						<div class="col-md-3">
							<label for="soNo" >Sales Order No.</label>
							<div class="form-group row">
								<div class="col-md-9">
									<input type="text" name="soNo" id="soNo" class="form-control" <?php echo ($pickNo==''?'':' value="'.$soNo.'" disabled '); ?> data-smk-msg="Require Pick Date." required  />
								</div>
								<div class="col-md-3">
									<a href="#" name="btnSdNo" class="btn btn-primary" <?php echo ($pickNo==''?'':' disabled '); ?> ><i class="glyphicon glyphicon-search" ></i></a>								
								</div>
							</div>
							<!--from group-->
							
                        </div>		
						<!--col-md-6-->			
						<div class="col-md-3">		
						</div>
						<!-- col-md-->
				
						<div class="col-md-3">			  
						</div>
						<!-- col-md-->
				
					</div>	
					<!--row-->
					
		<div class="row">
			<div class="col-md-3">		
				<div class="from-group">
				<label for="pickDate">Pick Date</label>
				<input type="text" id="pickDate" name="pickDate" class="form-control datepicker" value="" data-smk-msg="Require Pick Date." required <?php echo ($soNo==''?'':' disabled '); ?> >
				</div>
				<!--from group-->				
			</div>
			<!--col-md-->
			<div class="col-md-6">	
				<div class="from-group">
					<label for="remark">Remark</label>
					<input type="text" id="remark" name="remark" value="<?=$hdr['remark'];?>" class="form-control" <?php echo ($pickNo==''?'':' disabled '); ?> >
				</div>
				<!--from group-->
			</div>
			<!--col-md-->
			<div class="col-md-3">	
				
			</div>
			<!--col-md-->
		</div>
		<!--row-->
		<div class="row" <?php echo ($pickNo==''?'':' style="display: none;" '); ?>>
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
			
		<div class="row col-md-12"  <?php echo ($pickNo!=''?'':' style="display: none;" '); ?> >
			<form id="form2" action="<?=$rootPage;?>_add_item_submit_ajax.php" method="post" class="form" novalidate>
				<input type="hidden" name="pickNo" id="pickNo" value="<?=$pickNo;?>" />
				<?php
					$sql = "SELECT od.`id`, od.`prodId`, od.`qty`
					,prd.code as prodCode, prd.name as prodName 
					, (SELECT IFNULL(SUM(dtl.qty),0) FROM picking_detail dtl WHERE dtl.pickNo=:pickNo AND dtl.prodId=od.prodId) as pickQty
					FROM `sale_detail` od
					LEFT JOIN product prd ON prd.id=od.prodId 
					WHERE 1
					AND od.soNo=:soNo 
					
					ORDER BY prd.name 
					";
					$stmt = $pdo->prepare($sql);
					$stmt->bindParam(':pickNo', $pickNo);		
					$stmt->bindParam(':soNo', $soNo);	
					$stmt->execute();
					
					$sql = "SELECT dtl.`id`, dtl.prodId, dtl.`issueDate`, dtl.`grade`, dtl.`qty`, dtl.`pickNo` 
					,prd.code as prodCode, prd.name as prodName  
					FROM `picking_detail` dtl
					LEFT JOIN product prd ON prd.id=dtl.prodId 
					WHERE 1
					AND pickNo=:pickNo 
					
					ORDER BY prd.name  
					";
					$stmt2 = $pdo->prepare($sql);
					$stmt2->bindParam(':pickNo', $pickNo);
					$stmt2->execute();
				?>
				<div class="row">
				<div class="col-md-6">
				<div class="table-responsive">
				<table id="tbl_items" class="table table-striped">
					<tr>
						<th>No.</th>
						<th>Product Code</th>
						<th>Order Qty</th>
						<th>Pick Qty</th>
						<th>#</th>
					</tr>
					<?php $row_no=1; while ($row = $stmt->fetch()) { 
					?>
					<tr>
						<td><?= $row_no; ?></td>
						<td><?= $row['prodCode']; ?></td>	
						<td style="text-align: right;"><?= number_format($row['qty'],0,'.',','); ?></td>
						<td style="text-align: right;"><?= number_format($row['pickQty'],0,'.',','); ?></td>
					<td>					
					<a href="<?=$rootPage;?>_add_item_search.php?pickNo=<?=$hdr['pickNo'];?>&doDtlId=<?=$row['id'];?>&id=<?=$row['prodId'];?>" class="btn btn-primary"><i class="glyphicon glyphicon-edit"></i> Add</a>					
					</td>
					</tr>
					<?php $row_no+=1; } ?>
				</table>
				</div>
				<!--/.table-responsive-->		
				</div>
				<!--col-md-6-->
				
				
				<div class="col-md-6">
				<div class="table-responsive">
				<table id="tbl_items" class="table table-striped">
					<tr>
						<th>No.</th>
						<th>Product Code</th>
						<th>Issue Date</th>
						<th>Grade</th>
						<th>Qty</th>
						<th>#</th>
					</tr>
					<?php $row_no=1; while ($row = $stmt2->fetch()) { 
					?>
					<tr>
						<td><?= $row_no; ?></td>
						<td><?= $row['prodCode']; ?></td>	
						<td><?= $row['issueDate']; ?></td>	
						<td><?= $row['grade']; ?></td>	
						<td style="text-align: right;"><?= number_format($row['qty'],0,'.',','); ?></td>
					<td>										
					<a class="btn btn-danger fa fa-trash" name="btn_row_delete" <?php echo ($hdr['statusCode']=='B'?' data-id="'.$row['id'].'" ':' disabled '); ?> > Delete</a>
					</td>
					</tr>
					<?php $row_no+=1; } ?>
				</table>
				</div>
				<!--/.table-responsive-->		
				</div>
				<!--col-md-6-->
				
				</div>
				<!--row-->
				
				
				
				</form>
			</div>
			<!--/.row dtl-->
		
    </div><!-- /.box-body -->
  <div class="box-footer"  <?php echo ($pickNo!=''?'':' style="display: none;" '); ?>  >
  <a name="btn_view" href="<?=$rootPage;?>_view.php?pickNo=<?=$pickNo;?>" class="btn btn-default"><i class="glyphicon glyphicon-search"></i> View</a>
				
				
      <button type="button" id="btn_verify" class="btn btn-primary pull-right" style="margin-right: 5px;" <?php echo ($hdr['statusCode']=='B'?'':'disabled'); ?> >
		<i class="glyphicon glyphicon-ok"></i> Confirm
	  </button>   
		<button type="button" id="btn_delete" class="btn btn-danger pull-right" style="margin-right: 5px;" <?php echo ($hdr['statusCode']<>'P'?'':'disabled'); ?> >
		<i class="glyphicon glyphicon-trash"></i> Delete
	  </button>
      
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
        <h4 class="modal-title">Search Sales Order No.</h4>
      </div>
      <div class="modal-body">
        <div class="form-horizontal">
			<div class="form-group">	
				<label for="year_month" class="control-label col-md-2">SO NO.</label>
				<div class="col-md-4">
					<input type="text" class="form-control" id="txt_search_fullname" />
				</div>
			</div>
		
		<table id="tbl_search_person_main" class="table">
			<thead>
				<tr bgcolor="4169E1" style="color: white; text-align: center;">
					<td>#Select</td>
					<td>Sales Order No.</td>
					<td>Sales Order Date</td>
					<td>Customer</td>
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
				search_word: $('#txt_search_fullname').val()
			};
			if(params.search_word.length < 3){
				alert('search name surname must more than 3 character.');
				return false;
			}
			/* Send the data using post and put the results in a div */
			  $.ajax({
				  url: "search_saleOrder_ajax.php",
				  type: "post",
				  data: params,
				datatype: 'json',
				  success: function(data){	
								data=$.parseJSON(data);
								switch(data.rowCount){
									case 0 : alert('Data not found.');
										return false; break;
									default : 
										$('#tbl_search_person_main tbody').empty();
										$.each($.parseJSON(data.data), function(key,value){
											$('#tbl_search_person_main tbody').append(
											'<tr>' +
												'<td>' +
												'	<div class="btn-group">' +
												'	<a href="javascript:void(0);" data-name="search_person_btn_checked" ' +
												'	class="btn" title="เลือก"> ' +
												'	<i class="glyphicon glyphicon-ok"></i> เลือก</a> ' +
												'	</div>' +
												'</td>' +
												'<td>'+ value.soNo +'</td>' +
												'<td>'+ value.saleDate +'</td>' +
												'<td>'+ value.custName +'</td>' +
											'</tr>'
											);			
										});	
									$('#modal_search_person').modal('show');	
								}							
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
	
	$('#soNo').keyup(function(e){
		if(e.keyCode == 13)
		{	curId = $(this).attr('name');
			var params = {
				search_word: $(this).val()
			};
			if(params.search_word.length < 3){
				alert('search word must more than 3 character.');
				return false;
			} //alert(params.search_word);
			/* Send the data using post and put the results in a div */
			  $.ajax({
				  url: "search_saleOrder_ajax.php",
				  type: "post",
				  data: params,
				datatype: 'json'})
				.done(function (data) {
						data=$.parseJSON(data);
						switch(data.rowCount){
							case 0 : alert('Data not found.');
								return false; break;
							case 1 :
								$.each($.parseJSON(data.data), function(key,value){
									$('#soNo').val(value.soNo).prop('disabled','disabled');
									//$('input[name=fromName]').val(value.fromCode+' : '+value.fromName);
									//$('input[name=toName]').val(value.toCode+' : '+value.toName);
								});
								break;
							default : 
								$('#tbl_search_person_main tbody').empty();
								$.each($.parseJSON(data.data), function(key,value){
									$('#tbl_search_person_main tbody').append(
									'<tr>' +
										'<td>' +
										'	<div class="btn-group">' +
										'	<a href="javascript:void(0);" data-name="search_person_btn_checked" ' +
										'	class="btn" title="เลือก"> ' +
										'	<i class="glyphicon glyphicon-ok"></i> เลือก</a> ' +
										'	</div>' +
										'</td>' +
										'<td>'+ value.soNo +'</td>' +
										'<td>'+ value.saleDate +'</td>' +
										'<td>'+ value.custName +'</td>' +
									'</tr>'
									);			
								});	
							$('#modal_search_person').modal('show');	
						}	
			})
			.error(function (response) {
				  alert(response.responseText);
			});
		}/* e.keycode=13 */	
	});
	
	$('#form1 a[name=btn_create]').click (function(e) {		
		if ($('#form1').smkValidate()){
			$.smkConfirm({text:'Are you sure to Create ?',accept:'Yes.', cancel:'Cancel'}, function (e){if(e){
				$('#soNo').prop('disabled','');
				$.post({
					url: '<?=$rootPage;?>_ajax.php',
					data: $("#form1").serialize(),
					dataType: 'json'
				}).done(function(data) {
					if (data.success){  
						$.smkAlert({
							text: data.message,
							type: 'success',
							position:'top-center'
						});
						window.location.href = "<?=$rootPage;?>_add.php?pickNo=" + data.pickNo;
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
		
		
	$('#btn_delete').click (function(e) {				 
		var params = {		
		action: 'delete',
		pickNo: $('#pickNo').val()				
		};
		//alert(params.pickNo);
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
			   
	$('#btn_verify').click (function(e) {				 
		var params = {		
		action: 'confirm',			
		pickNo: $('#pickNo').val()				
		};
		//alert(params.hdrID);
		$.smkConfirm({text:'Are you sure to Confirm ?',accept:'Yes.', cancel:'Cancel'}, function (e){if(e){
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
					window.location.href = "<?=$rootPage;?>_view.php?pickNo=" + data.pickNo;
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
			language: 'en',             //เปลี่ยน label ต่างของ ปฏิทิน ให้เป็น ภาษาไทย   (ต้องใช้ไฟล์ bootstrap-datepicker.th.min.js นี้ด้วย)
			thaiyear: false              //Set เป็นปี พ.ศ.
		});  //กำหนดเป็นวันปัจุบัน
		//กำหนดเป็น วันที่จากฐานข้อมูล
		<?php if($pickNo<>''){ ?>
		var queryDate = '<?=$hdr['pickDate'];?>',
		dateParts = queryDate.match(/(\d+)/g)
		realDate = new Date(dateParts[0], dateParts[1] - 1, dateParts[2]); 
		$('.datepicker').datepicker('setDate', realDate);
		<?php }else{ ?> $('.datepicker').datepicker('setDate', '0'); <?php } ?>
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