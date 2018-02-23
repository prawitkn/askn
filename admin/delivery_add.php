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
<?php
	$sqlRole = "";
	switch($s_userGroupCode){
		case 'sales' :
			$sqlRole = " AND ct.smCode='$s_smCode' ";
			break;
		case 'salesAdmin' :
			$sqlRole = " AND ct.smAdmCode='$s_smCode' ";
			break;
		default :
	}
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
		<?php
			
		?>
      <h1>
       Delivery Order
        <small>Delivery Order management</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="customer.php"><i class="fa fa-dashboard"></i>Delivery Order Information</a></li>
        <li class="active">Delivery Order Information</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

      <!-- Your Page Content Here -->
    <a href="delivery.php" class="btn btn-google">Back</a>
    <div class="box box-primary">
		<?php	
			$sql = "SELECT *
			, ct.custName, ct.custAddr
			, sm.name as smName 
			FROM delivery_header dh 
			INNER JOIN customer ct on ct.code=dh.custCode
				".$sqlRole."
			LEFT JOIN salesman sm on sm.code=dh.smCode 
			WHERE dh.statusCode='B' AND dh.createByID=:s_userID 
			";
			$stmt = $pdo->prepare($sql);
			$stmt->bindParam(':s_userID', $s_userID);	
			$stmt->execute();
			$hdr = $stmt->fetch();
			$doNo = $hdr['doNo'];
			$soNo = $hdr['soNo'];
		?>
        <div class="box-header with-border">
        <h3 class="box-title">Add Delivery Order No. : <?=$doNo;?></h3>
        <div class="box-tools pull-right">
          <!-- Buttons, labels, and many other things can be placed here! -->
          <!-- Here is a label for example -->
         
        </div><!-- /.box-tools -->
        </div><!-- /.box-header -->
        <div class="box-body">			
            <div class="row">
				<form id="form1" action="delivery_add_insert.php" method="post" class="form" novalidate>				
                <div class="col-md-12">   
					<div class="row">
						<div class="col-md-3">
							<div class="from-group">
                            <label for="soNo">SO No.</label>
							<select id="soNo" name="soNo" class="form-control" data-smk-msg="Require SO No." <?php echo ($soNo==''?'':' disabled '); ?> required >
								<option value=""> -- Select -- </option>
								<?php
								$sql = "
									SELECT a.`soNo`, a.`saleDate`, a.`custCode`, a.`smCode`, a.`createTime`, a.`createByID`, a.statusCode, 
									ct.custName, ct.custAddr, ct.custTel, ct.custFax,
									c.name as smName,
									d.userFullname as createByName
									FROM `sale_header` a
									left join customer ct on a.custCode=ct.code 
										".$sqlRole."
									left join salesman c on a.smCode=c.code
									left join user d on a.createByID=d.userID
									WHERE 1 
									AND a.statusCode='P' 
									AND a.isClose='N' 
									
									ORDER BY a.createTime DESC
							";
								$result_cust = mysqli_query($link, $sql);
								while($row = mysqli_fetch_assoc($result_cust)){
									$selected = ($soNo==$row['soNo']?' selected ':'');
									echo '<option value="'.$row['soNo'].'" 
										 data-custCode="'.$row['custCode'].'" 									 
										 data-smCode="'.$row['smCode'].'" 
										 '.$selected.'
										 >SO No. '.$row['soNo'].' / Cust.'.$row['custName'].' / Sale.'.$row['smName'].'</option>';
								}
								?>
							</select> 
							</div>
							<!--from group-->
							
                        </div>		
						<!--col-md-6-->			
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
				<input type="text" id="deliveryDate" name="deliveryDate" value="<?=$hdr['deliveryDate'];?>" class="form-control datepicker" data-smk-msg="Require Order Date." required <?php echo ($soNo==''?'':' disabled '); ?> >
				</div>
				<!--from group-->				
			</div>
			<!--col-md-->
			<div class="col-md-3">	
				<div class="from-group">
				<label for="refNo">Ref No.</label>
				<input type="text" id="refNo" name="refNo" value="<?=$hdr['refNo'];?>" class="form-control" <?php echo ($soNo==''?'':' disabled '); ?> >
				</div>
				<!--from group-->
			</div>
			<!--col-md-->
			<div class="col-md-6">	
				<div class="from-group">
					<label for="remark">Remark</label>
					<input type="text" id="remark" name="remark" value="<?=$hdr['remark'];?>" class="form-control" <?php echo ($soNo==''?'':' disabled '); ?> >
				</div>
				<!--from group-->
			</div>
			<!--col-md-->
		</div>
		<!--row-->
		<div class="row" <?php echo ($soNo==''?'':' style="display: none;" '); ?>>
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
			
		<div class="row col-md-12">
			<form id="form2" action="delivery_add_item_submit_ajax.php" method="post" class="form" novalidate>
				<input type="hidden" name="doNo" value="<?=$doNo;?>" />
				<?php
					$sql = "SELECT od.`id`, od.`prodCode`, od.`qty`
					, pd.prodName, pd.prodDesc, pd.salesUom
                    , (SELECT IFNULL(SUM(dd.qty),0) FROM delivery_header dh 
                    	LEFT JOIN delivery_detail dd on dh.doNo=dd.doNo
                       	WHERE dh.soNo=oh.soNo AND dd.prodCode=od.prodCode and dh.statusCode='P' ) as sentQty
                    , (SELECT IFNULL(SUM(dd.qty),0) FROM delivery_header dh 
                    	LEFT JOIN delivery_detail dd on dh.doNo=dd.doNo
                       	WHERE dh.soNo=oh.soNo AND dd.prodCode=od.prodCode and dh.statusCode='B' ) as curQty
					FROM `sale_detail` od
					INNER JOIN sale_header oh on oh.soNo=od.soNo 
					LEFT JOIN product pd on od.prodCode=pd.code 
					WHERE 1
					AND oh.soNo=:soNo 
					
					ORDER BY od.prodCode 
							";
					$stmt = $pdo->prepare($sql);
					$stmt->bindParam(':soNo', $soNo);		
					$stmt->execute();
				?>
				<div class="table-responsive">
				<table id="tbl_items" class="table table-striped">
					<tr>
						<th>No.</th>
						<th>Product Name</th>
						<th>UOM</th>
						<th>Order Qty</th>
						<th>Sent Qty</th>
						<th>Delivery Qty</th>
						<th>#</th>
					</tr>
					<?php $row_no=1; while ($row = $stmt->fetch()) { 
						$sentQty = $row['sentQty'];
						$curQty = $row['curQty'];
						$remainQty = $row['curQty']-$row['curQty'];
					?>
					<tr>
						<td><?= $row_no; ?></td>
						<td><?= $row['prodName']; ?></br>
							<small><?= $row['prodDesc']; ?></small></td>	
						<td><?= $row['salesUom']; ?></td>	
						<td style="text-align: right;"><?= number_format($row['qty'],0,'.',','); ?></td>
						<td style="text-align: right;"><?= number_format($row['sentQty'],0,'.',','); ?></td>
						<td>
							<input type="hidden" name="prodCode[]" value="<?=$row['prodCode'];?>" />
							<input type="hidden" name="sentQty[]" value="<?=$row['sentQty'];?>" />
							<input type="text" class="form-control" name="qty[]" value="<?php echo ($soNo==''?$remainQty:$curQty); ?>"  style="text-align: right;" data-smk-msg="Require Quantity."required
							onkeypress="return numbersOnly(this, event);" 
							onpaste="return false;"
							<?php echo ($row_no==1?' id="txt_row_first" ':'');?>
								>
						</td>
					</tr>
					<?php $row_no+=1; } ?>
				</table>
				</div>
				<!--/.table-responsive-->
				<a name="btn_submit" href="#" class="btn btn-primary"><i class="glyphicon glyphicon-save"></i> Submit</a>
				<a name="btn_view" href="delivery_view.php?doNo=<?=$doNo;?>" class="btn btn-default"><i class="glyphicon glyphicon-search"></i> View</a>
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

	$('#form1 a[name=btn_create]').click (function(e) {
		if ($('#form1').smkValidate()){
			$.smkConfirm({text:'Are you sure to Create? ?',accept:'Yes.', cancel:'Cancel'}, function (e){if(e){
				$.post({
					url: 'delivery_add_hdr_insert_ajax.php',
					data: $("#form1").serialize(),
					dataType: 'json'
				}).done(function(data) {
					if (data.success){  
						$.smkAlert({
							text: data.message,
							type: 'success',
							position:'top-center'
						});
						window.location.href = "delivery_add.php?doNo=" + data.doNo;
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

	
	$('#form2 a[name=btn_submit]').click (function(e) {
		if ($('#form2').smkValidate()){
			$.smkConfirm({text:'Are you sure to Submit ?',accept:'Yes', cancel:'Cancel'}, function (e){if(e){
				$.post({
					url: 'delivery_add_item_submit_ajax.php',
					data: $("#form2").serialize(),
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
		});  //กำหนดเป็นวันปัจุบัน
		//กำหนดเป็น วันที่จากฐานข้อมูล
		var queryDate = '<?=$hdr['deliveryDate'];?>',
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