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
		
$rootPage="send2";	
$tb="send";	
		
		$sql = "SELECT hdr.`sdNo`, hdr.`refNo`, hdr.`sendDate`, hdr.`fromCode`, hdr.`toCode`, hdr.`remark`, hdr.`statusCode`, hdr.`createTime`, hdr.`createById`
		, fsl.name as fromName, tsl.name as toName
		, d.userFullname as createByName
		FROM `".$tb."` hdr
		LEFT JOIN sloc fsl on hdr.fromCode=fsl.code 
		LEFT JOIN sloc tsl on hdr.toCode=tsl.code
		left join user d on hdr.createById=d.userId
		WHERE 1 
		AND hdr.statusCode='B' AND hdr.createById=:s_userId 
		";
		
		$stmt = $pdo->prepare($sql);
		$stmt->bindParam(':s_userId', $s_userId);	
		$stmt->execute();
		$hdr = $stmt->fetch();
		$sdNo = $hdr['sdNo'];
		$refNo = $hdr['refNo'];
		if($stmt->rowCount() >= 1){
			switch($s_userGroupCode){ 					
				case 'whOff' :
				case 'whSup' :
				case 'pdOff' :
				case 'pdSup' :
					//if($hdr['fromCode']!=$s_userDeptCode) { header("Location: access_denied.php"); exit();}			
					break;
				default :	// it, admin 
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
	  <h1><i class="glyphicon glyphicon-arrow-up"></i>
       Send
        <small>Send management</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?=$rootPage;?>.php"><i class="glyphicon glyphicon-list"></i>Send List</a></li>
		<!--<li><a href="#"><i class="glyphicon glyphicon-edit"></i>Send</a></li>-->
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

      <!-- Your Page Content Here -->
    <div class="box box-primary">
        <div class="box-header with-border">
        <h3 class="box-title">Add Send No. : <?=$sdNo;?></h3>
		
		
		<input type="hidden" id="sdNo" value="<?=$sdNo;?>" />
		
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
							<label for="refNo" >Referene Receive No.</label>
							<div class="form-group row">
								<!--<div class="col-md-9">
									<input type="text" name="refNo" id="refNo" class="form-control" <?php echo ($sdNo==''?'':' value="'.$refNo.'" disabled '); ?>  />
								</div>
								<div class="col-md-3">
									<a href="#" name="btnSdNo" class="btn btn-primary" <?php echo ($sdNo==''?'':' disabled '); ?> ><i class="glyphicon glyphicon-search" ></i></a>								
								</div>-->
								<div class="col-md-12">									
									<input type="hidden" name="refNo" id="refNo" value="<?=$refNo;?>" />
									<label name="refNoName" id="refNoName" ></label>
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
				<label for="sendDate">Send Date</label>
				<input type="text" id="sendDate" name="sendDate" class="form-control datepicker" data-smk-msg="Require Order Date." required <?php echo ($sdNo==''?'':' disabled '); ?> >
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
			$sql = "SELECT dtl.`id`, dtl.`prodItemId`,itm.`barcode`, itm.`issueDate`, itm.`machineId`, dtl.`seqNo`, dtl.`NW`, dtl.`GW`
			, itm.`qty`, itm.`packQty`, itm.`grade`, itm.`gradeDate`, itm.`refItemId`, itm.`itemStatus`, itm.`remark`, itm.`problemId`
			,prd.id as prodId, prd.code as prodCode 
			, dtl.`sdNo` 
			FROM `".$tb."_detail` dtl	
			LEFT JOIN product_item itm ON itm.prodItemId=dtl.prodItemId 
			LEFT JOIN product prd ON prd.id=itm.prodCodeId  
			WHERE 1
			AND dtl.sdNo=:sdNo  
			";
			$stmt = $pdo->prepare($sql);
			$stmt->bindParam(':sdNo', $sdNo);		
			$stmt->execute();
			$rowCount = $stmt->rowCount();
		?>

		<div class="row col-md-12"  <?php echo ($sdNo==''?' style="display: none;" ':''); ?>  >
			<div class="box-header with-border">
				<h3 class="box-title">Item List</h3>				
				<a name="btn_search_prod" href="#" class="btn btn-default"><i class="glyphicon glyphicon-plus" ></i> Add item</a>
				
				<div class="box-tools pull-right">
				  <span class="label label-primary">Total <?=$rowCount; ?> items</span>
				</div><!-- /.box-tools -->
			</div><!-- /.box-header -->
				
			<form id="form2" action="delivery_add_item_submit_ajax.php" method="post" class="form" novalidate>
				<input type="hidden" name="sdNo" value="<?=$hdr['sdNo'];?>" />
				
				<div class="table-responsive">
				<table id="tbl_items" class="table table-striped">
					<tr>
						<th>No.</th>
						<th>Product Code</th>
						<th>Barcode</th>
						<th>Grade</th>
						<th>Qty</th>
						<th>Issue Date</th>
						<th>#</th>
					</tr>
					<?php $row_no=1; $sumQty=0;  $sumGradeNotOk=0; while ($row = $stmt->fetch()) { 
							$gradeName = '<b style="color: red;">N/A</b>'; 
							switch($row['grade']){
								case 0 : $gradeName = 'A'; break;
								case 1 : $gradeName = '<b style="color: red;">B</b>'; $sumGradeNotOk+=1; break;
								case 2 : $gradeName = '<b style="color: red;">N</b>'; $sumGradeNotOk+=1; break;
								default : 
									$gradeName = '<b style="color: red;">N/a</b>'; $sumGradeNotOk+=1;
							} $sumGradeNotOk=0;
					?>
					<tr>
						<td><?= $row_no; ?></td>
						<td><?= $row['prodCode']; ?></td>	
						<td><?= $row['barcode']; ?></td>	
						<td><?= $gradeName; ?></td>	
						<td style="text-align: right;"><?= number_format($row['qty'],0,'.',','); ?></td>
						<td><?= date('d M Y',strtotime( $row['issueDate'] )); ?></td>		
						<td><a class="btn btn-danger fa fa-trash" name="btn_row_delete" <?php echo ($hdr['statusCode']=='B'?' data-id="'.$row['id'].'" ':' disabled '); ?> > Delete</a></td>
					</tr>
					<?php $row_no+=1; $sumQty+=$row['qty']; } ?>
					<tr>
						<td></td>
						<td>Total</td>	
						<td></td>
						<td></td>
						<td style="text-align: right;"><?= number_format($sumQty,0,'.',','); ?></td>
						<td></td>
						<td></td>
					</tr>
				</table>
				</div>
				<!--/.table-responsive-->
				
				<a name="btn_view" href="<?=$rootPage;?>_view.php?sdNo=<?=$sdNo;?>" class="btn btn-default"><i class="glyphicon glyphicon-search"></i> View</a>
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
        <h4 class="modal-title">Search Product Send Date</h4>
      </div>
      <div class="modal-body">
        <div class="form-horizontal">
			<div class="form-group">	
				<label for="year_month" class="control-label col-md-2">Production Sending Date</label>
				<div class="col-md-2">
					<!--<input type="text" class="form-control" id="txt_search_fullname" />-->
					<input type="text" id="txt_search_fullname" name="txt_search_fullname" class="form-control datepicker" >
				</div>
				<div class="col-md-2">
					<a name="btn_search_ref_no" href="#" class="btn btn-primary"><i class="glyphicon glyphicon-search" ></i> Search</a>
				</div>
			</div>
		
		<table id="tbl_search_person_main" class="table">
			<thead>
				<tr bgcolor="4169E1" style="color: white; text-align: center;">
					<td>#Select</td>
					<td>Production Sending No.</td>
					<td>Production Sending Date</td>
					<td>Sending From</td>
					<td>Sending To</td>
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
	$('a[name="btnSdNo"]').click(function(){
		curId = $(this).prev().prev().attr('id');
		curName = $(this).prev().attr('id');
		<?php if($sdNo==""){ ?>
			$('#modal_search_person').modal('show');
		<?php } ?>
	});	
	
	$('a[name=btn_search_ref_no]').click(function(e){
		var params = {
			search_fullname: $('#txt_search_fullname').val()
		};
		if(params.search_fullname.length < 3){
			alert('search keyword must more than 3 character.');
			return false;
		}
		/* Send the data using post and put the results in a div */
		  $.ajax({
			  url: "search_send_prod_ajax.php",
			  type: "post",
			  data: params,
			datatype: 'json',
			  success: function(data){	
							alert(data);
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
	});
	
	$(document).on("click",'a[data-name="search_person_btn_checked"]',function() {
		
		//$('input[name='+curId+']').val($(this).closest("tr").find('td:eq(1)').text());
		//$('#'+curTxtFullName).val($(this).closest("tr").find('td:eq(2)').text());
		//$('#'+curTxtMobilePhoneNo).val($(this).closest('tr').find('td:eq(3)').text());	
		$('#'+curId).val($(this).closest("tr").find('td:eq(1)').text());
		$('#'+curName).text($(this).closest("tr").find('td:eq(1)').text());
		$('#'+curName).text($(this).closest("tr").find('td:eq(1)').text());
		
		var queryDate = $(this).closest("tr").find('td:eq(2)').text(),
		dateParts = queryDate.match(/(\d+)/g)
		realDate = new Date(dateParts[0], dateParts[1] - 1, dateParts[2]); 
		$('#sendDate').datepicker('setDate', realDate);
		$('#sendDate').attr('disabled','disabled');
		
		$('#modal_search_person').modal('hide');
	});
	//Search End


	
	$('#form1 a[name=btn_create]').click (function(e) {
		if ($('#form1').smkValidate()){
			$.smkConfirm({text:'Are you sure to Create ?',accept:'Yes.', cancel:'Cancel'}, function (e){if(e){
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
						window.location.href = "send2_hdr.php?sdNo=" + data.sdNo;
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
			action: 'item_delete',
			id: $(this).attr('data-id')
		};
		//alert(params.id);
		$.smkConfirm({text:'Are you sure to Delete ?',accept:'Yes', cancel:'Cancel'}, function (e){if(e){
			$.post({
				url: '<?=$rootPage;?>_ajax.php',
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
	
	$('a[name=btn_search_prod]').click(function(e){
		var sdNo = $('#sdNo').val();
		var refNo = $('#refNo').val();
		window.location.href = "<?=$rootPage;?>_hdr_item.php?sdNo="+sdNo+"&refNo="+refNo;
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
			language: 'en',             //เปลี่ยน label ต่างของ ปฏิทิน ให้เป็น ภาษาไทย   (ต้องใช้ไฟล์ bootstrap-datepicker.th.min.js นี้ด้วย)
			thaiyear: false              //Set เป็นปี พ.ศ.
		});  //กำหนดเป็นวันปัจุบัน
		//กำหนดเป็น วันที่จากฐานข้อมูล
		<?php if(isset($hdr['sendDate'])){ ?>
		var queryDate = '<?=$hdr['sendDate'];?>',
		dateParts = queryDate.match(/(\d+)/g)
		realDate = new Date(dateParts[0], dateParts[1] - 1, dateParts[2]); 
		$('#sendDate').datepicker('setDate', realDate);
		<?php }else{ ?> $('#sendDate').datepicker('setDate', '0'); <?php } ?>
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