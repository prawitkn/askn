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
$rootPage = 'delivery';
$stmt="";
if(isset($_GET['doNo'])){
	$sql = "SELECT dh.`doNo`, dh.`soNo`, dh.`ppNo`, dh.`custId`, dh.`shipToId`, dh.`smId`, dh.`deliveryDate`, dh.`driver`, dh.`refInvNo`, dh.`remark`, dh.`statusCode`
	, dh.`createTime`, dh.`createById`, dh.`updateTime`, dh.`updateById`, dh.`confirmTime`, dh.`confirmById`, dh.`approveTime`, dh.`approveById`
	, ct.name as custName, ct.addr1 
	, sm.name as smName 
	FROM delivery_header dh 
	INNER JOIN customer ct on ct.id=dh.custId 
	LEFT JOIN salesman sm on sm.id=dh.smId 
	WHERE dh.doNo=:doNo ";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':doNo', $_GET['doNo']);
}else{
	$sql = "SELECT dh.`doNo`, dh.`soNo`, dh.`ppNo`, dh.`custId`, dh.`shipToId`, dh.`smId`, dh.`deliveryDate`, dh.`driver`, dh.`refInvNo`, dh.`remark`, dh.`statusCode`
	, dh.`createTime`, dh.`createById`, dh.`updateTime`, dh.`updateById`, dh.`confirmTime`, dh.`confirmById`, dh.`approveTime`, dh.`approveById`
	, ct.name as custName, ct.addr1 
	, sm.name as smName 
	FROM delivery_header dh 
	INNER JOIN customer ct on ct.id=dh.custId 
	LEFT JOIN salesman sm on sm.id=dh.smId 
	WHERE dh.statusCode='B' AND dh.createById=:s_userId 
	";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':s_userId', $s_userId);	
}

$stmt->execute();
$hdr = $stmt->fetch();
$doNo = $hdr['doNo'];
$ppNo = $hdr['ppNo'];
$soNo = $hdr['soNo'];
?>
   
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
	  <h1><i class="glyphicon glyphicon-send"></i>
       Delivery Order
        <small>Delivery Order management</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?=$rootPage;?>.php"><i class="glyphicon glyphicon-list"></i>Delivery Order List</a></li>
		<li><a href="#"><i class="glyphicon glyphicon-edit"></i>Delivery Order</a></li>
      </ol>
    </section>
	

    <!-- Main content -->
    <section class="content">

      <!-- Your Page Content Here -->
    <div class="box box-primary">
        <div class="box-header with-border">
        <h3 class="box-title">Add Delivery Order No. : <?=$doNo;?></h3>
        <div class="box-tools pull-right">
          <!-- Buttons, labels, and many other things can be placed here! -->
          <!-- Here is a label for example -->
         
        </div><!-- /.box-tools -->
        </div><!-- /.box-header -->
        <div class="box-body">			
            <div class="row">
				<form id="form1" action="<?=$rootPage;?>_add_insert.php" method="post" class="form" novalidate>		<input type="hidden" name="action" value="add" />
					<input type="hidden" name="doNo" value="<?=$doNo;?>" />

                <div class="col-md-12">   
					<div class="row">
						<div class="col-md-3">
							<label for="ppNo" >Prepare No.</label>
							<div class="form-group row">
								<div class="col-md-9">
									<input type="text" name="ppNo" class="form-control" 
									<?php if($ppNo==''){ 
											if(isset($_GET['ppNo'])) { ?>
												value="<?=$_GET['ppNo'];?>" 
									<?php  }//isset 										
										}else { ?>
											value="<?=$ppNo;?>" disabled <?php
										} ?>
									  />
								</div>
								<div class="col-md-3">
									<a href="#" name="btnSdNo" class="btn btn-primary" <?php echo ($doNo==''?'':' disabled '); ?> ><i class="glyphicon glyphicon-search" ></i></a>								
								</div>
							</div>
							<!--from group-->
							
                        </div>				
						<div class="col-md-3">					  
					  <div class="from-group">
						<label for="custName">Customer Name</label>
						<input type="text" id="custName" name="custName" value="<?=$hdr['custName'];?>" class="form-control" disabled>
					</div>
					<!--from group-->
				</div>
				<!-- col-md-->
				
				<div class="col-md-3">					  
				  <!-- checkbox -->
					<div class="from-group">
						<label for="smName">Salesman Name</label>
						<input type="text" id="smName" name="smName" value="<?=$hdr['smName'];?>" class="form-control" disabled>
					</div>
					<!--from group-->		  
				</div>
				<!-- col-md-->
				
					</div>	
					<!--row-->
					
		<div class="row">
			<div class="col-md-3">		
				<div class="from-group">
				<label for="deliveryDate">Delivery Date</label>
				<input type="text" id="deliveryDate" name="deliveryDate" value="<?=$hdr['deliveryDate'];?>" class="form-control datepicker" data-smk-msg="Require Order Date." required <?php echo ($doNo==''?'':' disabled '); ?> >
				</div>
				<!--from group-->				
			</div>
			<!--col-md-->
			<div class="col-md-3">	
				<div class="from-group">
					<label for="remark">Remark</label>
					<input type="text" id="remark" name="remark" value="<?=$hdr['remark'];?>" class="form-control" <?php echo ($doNo==''?'':' disabled '); ?> >
				</div>
				<!--from group-->
			</div>
			<!--col-md-->
			<div class="col-md-6">	
				
			</div>
			<!--col-md-->
		</div>
		<!--row-->
		<div class="row" <?php echo ($doNo==''?'':' style="display: none;" '); ?>>
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
			
		<div class="row col-md-12"   <?php echo ($doNo!=''?'':' style="display: none;" '); ?> >
			<form id="form2" action="#" method="post" class="form" novalidate>
				<input type="hidden" name="action" value="add_item_submit" />
				<input type="hidden" name="doNo" id="doNo" value="<?=$doNo;?>" />
				<?php					
					/*$sql = "
					SELECT dd.`id`, itm.`qty`
					,pd.code as prodCode, pd.uomCode
					, IFNULL((SELECT SUM(sd.qty) FROM sale_detail sd
							INNER JOIN sale_header sh on sh.soNo=sd.soNo
							WHERE sh.soNo=pk.soNo
							AND sd.prodId=itm.prodCodeId),0) AS sumSalesQty
					, (SELECT IFNULL(SUM(dds.qty),0) FROM delivery_header dhs 
						INNER JOIN delivery_detail dds on dhs.doNo=dds.doNo
						INNER JOIN product_item itms ON itms.prodItemId=dds.prodItemId 
						INNER JOIN prepare pps on pps.ppNo=dhs.ppNo
						INNER JOIN picking pks on pks.pickNo=pps.pickNo
						WHERE pks.soNo=pk.soNo 
						AND itms.prodCodeId=itm.prodCodeId
						AND dhs.statusCode='P' ) as sumSentQty
					, IFNULL(SUM(dd.qty),0) as sumDeliveryQty 
					FROM delivery_detail dd
					INNER JOIN delivery_header dh on dh.doNo=dd.doNo 
					LEFT JOIN product_item itm ON itm.prodItemId=dd.prodItemId 
					INNER JOIN prepare pp on pp.ppNo=dh.ppNo
					INNER JOIN picking pk on pk.pickNo=pp.pickNo
					INNER JOIN sale_header oh on oh.soNo=pk.soNo

					LEFT JOIN product pd on pd.id=itm.prodCodeId 
					WHERE 1 
					AND dh.doNo=:doNo
					GROUP BY dd.`id`, pd.id, dd.`qty` , pd.name, pd.description, pd.uomCode

					ORDER BY dd.`id`, pd.id, dd.`qty`, pd.name 
					";
					$stmt = $pdo->prepare($sql);	
					$stmt->bindParam(':doNo', $hdr['doNo']);
					$stmt->execute();*/
					$sql = "
					SELECT dtl.`id`, dtl.`qty`, dtl.remark 
					,pd.code as prodCode, pd.uomCode
					, IFNULL((SELECT SUM(sd.qty) FROM sale_detail sd
							WHERE sd.soNo=hdr.soNo
							AND sd.prodId=dtl.prodId),0) AS sumSalesQty
					, (SELECT IFNULL(SUM(dds.qty),0) FROM delivery_header dhs 
						INNER JOIN delivery_prod dds on dhs.doNo=dds.doNo
						WHERE dds.prodId=dtl.prodId 
						AND dhs.statusCode='P' ) as sumSentQty
					, IFNULL(SUM(dtl.qty),0) as sumDeliveryQty 
					FROM delivery_prod dtl
					INNER JOIN delivery_header hdr on hdr.doNo=dtl.doNo 
					LEFT JOIN product pd ON pd.id=dtl.prodId 
					WHERE 1 
					AND hdr.doNo=:doNo
					GROUP BY dtl.`prodId` 
					ORDER BY dtl.`id`
					";
					$stmt = $pdo->prepare($sql);	
					$stmt->bindParam(':doNo', $hdr['doNo']);
					$stmt->execute();

				?>
				<div class="table-responsive">
				<table id="tbl_items" class="table table-striped">
					<tr>
						<th>No.</th>
						<th>Product Code</th>
						<th>Order Qty</th>
						<th>Sent Qty</th>
						<th>Delivery Qty</th>
						<th>Remark</th>
						<th>#</th>
					</tr>
					<?php $row_no=1; while ($row = $stmt->fetch()) { 
					?>
					<tr>
						<td><?= $row_no; ?></td>
						<td><?= $row['prodCode']; ?></td>	
						<td style="text-align: right;"><?= number_format($row['sumSalesQty'],0,'.',',').'&nbsp;'.$row['uomCode']; ?></td>
						<td style="text-align: right;"><?= number_format($row['sumSentQty'],0,'.',',').'&nbsp;'.$row['uomCode']; ?></td>
						<td style="text-align: right;"><?= number_format($row['sumDeliveryQty'],0,'.',',').'&nbsp;'.$row['uomCode']; ?></td>
						<td>
							<input type="hidden" name="id[]" value="<?=$row['id'];?>" />	
													
							<input type="text" class="form-control" name="remark[]" value="<?=$row['remark'];?>" <?php echo ($row_no==1?' id="txt_row_first" ':'');?>
							<?php echo ($hdr['statusCode']=='B'?'':' disabled ');?>
							/>
						</td>
					</tr>
					<?php $row_no+=1; } ?>
				</table>
				</div>
				<!--/.table-responsive-->
				<?php if($hdr['statusCode']=='B'){ ?>
				<a id="btn_verify" href="#" class="btn btn-primary pull-right"><i class="glyphicon glyphicon-ok"></i> Submit</a>
				<a id="btn_update_n_verify" href="#" class="btn btn-warning pull-right" style="margin-right: 5px;"><i class="glyphicon glyphicon-ok"></i> Update Item and Confirm</a>
				<?php } ?>

				<button type="button" id="btn_delete" class="btn btn-danger pull-right" style="margin-right: 5px;" <?php echo ($hdr['statusCode']<>'P'?'':'disabled'); ?> >
            <i class="glyphicon glyphicon-trash"></i> Delete
          </button>
				
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
        <h4 class="modal-title">Search Prepare No.</h4>
      </div>
      <div class="modal-body">
        <div class="form-horizontal">
			<div class="form-group">	
				<label for="year_month" class="control-label col-md-2">PP NO.</label>
				<div class="col-md-4">
					<input type="text" class="form-control" id="txt_search_fullname" />
				</div>
			</div>
		
		<table id="tbl_search_person_main" class="table">
			<thead>
				<tr bgcolor="4169E1" style="color: white; text-align: center;">
					<td>#Select</td>
					<td>Prepare No.</td>
					<td>Prepare Date</td>
					<td>Picking No.</td>
					<td>SO No.</td>
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
				  url: "search_prepare_ajax.php",
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
										'<td>'+ value.ppNo +'</td>' +
										'<td>'+ value.ppDate +'</td>' +
										'<td>'+ value.pickNo +'</td>' +
										'<td>'+ value.soNo +'</td>' +
										'<td>'+ value.custName +'</td>' +
										'<td>'+ value.smName +'</td>' +
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
						window.location.href = "<?=$rootPage;?>_add.php?doNo=" + data.doNo;
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

	$('#btn_delete').click (function(e) {				 
		var params = {
		action: 'delete',
		doNo: $('#doNo').val()				
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

	$('#btn_verify').click (function(e) {				 
		var params = {	
		action: 'confirm',				
		doNo: $('#doNo').val()			
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
					window.location.href = "<?=$rootPage;?>_view.php?doNo=" + data.doNo;
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

	$('#btn_update_n_verify').click (function(e) {
		if ($('#form2').smkValidate()){
			$.smkConfirm({text:'Are you sure to Update and Confirm ?',accept:'Yes', cancel:'Cancel'}, function (e){if(e){
				$.post({
					url: '<?=$rootPage;?>_ajax.php',
					data: $("#form2").serialize(),
					dataType: 'json'
				}).done(function(data) {
					if (data.success){  
						$.smkAlert({
							text: data.message,
							type: 'success',
							position:'top-center'
						});
						window.location.href = "<?=$rootPage;?>_view.php?doNo=" + data.doNo;
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
			language: 'en',             //เปลี่ยน label ต่างของ ปฏิทิน ให้เป็น ภาษาไทย   (ต้องใช้ไฟล์ bootstrap-datepicker.th.min.js นี้ด้วย)
			thaiyear: false              //Set เป็นปี พ.ศ.
		});  //กำหนดเป็นวันปัจุบัน
		//กำหนดเป็น วันที่จากฐานข้อมูล
		<?php if($doNo<>''){ ?>
		var queryDate = '<?=$hdr['deliveryDate'];?>',
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