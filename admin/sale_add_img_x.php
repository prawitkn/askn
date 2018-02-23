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
<?php include 'inc_helper.php'; ?>
    
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
      <h1>
       Sales Order
        <small>Sales Order management</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="customer.php"><i class="fa fa-dashboard"></i>Sales Order Information</a></li>
        <li class="active">Sales Order Information</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
	<?php 
	$soNo = $_GET['soNo'];
	$sql = "SELECT `soNo`, `poNo`, `deliveryRemImg`, `saleDate`, `custCode`, `smCode`, `total`, `vatAmount`, `netTotal`, `prodGFC`, `prodGFM`, `prodGFT`, `prodSC`, `prodCFC`, `prodEGWM`, `prodGT`, `prodCSM`, `prodWR`, `deliveryDate`, `deliveryRem`, `suppTypeFact`, `suppTypeImp`, `prodTypeOld`, `prodTypeNew`, `custTypeOld`, `custTypeNew`, `prodStkInStk`, `prodStkOrder`, `prodStkOther`, `prodStkRem`, `packTypeAk`, `packTypeNone`, `packTypeOther`, `packTypeRem`, `priceOnOrder`, `priceOnOther`, `priceOnRem`, `remark`, `plac2deliCode`, `plac2deliRem`, `payTypeCode`, `payTypeRem`, `statusCode`, `createTime`, `createByID` 
	FROM `sale_header` 
	WHERE soNo=:soNo ";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':soNo', $soNo);	
	$stmt->execute();
	$r = $stmt->fetch(PDO::FETCH_ASSOC);
	?>
	
      <!-- Your Page Content Here -->
    <a href="sale.php" class="btn btn-google">Back</a>
    <div class="box box-primary">
        <div class="box-header with-border">
        <h3 class="box-title">Attach Image Sales Order : <b style="color: red;"><?= $r['soNo']; ?></b></h3>
        <div class="box-tools pull-right">
          <!-- Buttons, labels, and many other things can be placed here! -->
          <!-- Here is a label for example -->
         
        </div><!-- /.box-tools -->
        </div><!-- /.box-header -->
        <div class="box-body">
            
            <div class="row">
				<form id="form1" action="#" method="post" class="form" enctype="multipart/form-data" novalidate>
                <div class="col-md-6"> 
					<input type="hidden" id="soNo" name="soNo" value="<?= $soNo; ?>" />
					<input type="hidden" id="custCode" name="custCode" value="<?= $r['custCode']; ?>" />
					<div class="row">
						<div class="col-md-6 form-group">
                            <label for="soDeliFile">Image 1</label>
								<input type='file' name="soDeliFile" onchange="readURL(this);" /><br/>
								<img id="soDeliFileImg" src="<?=$r['deliveryRemImg'];?>" alt="your image" width="200px" />
								<script type="text/javascript">
									function readURL(input) {
										if (input.files && input.files[0]) {
											var reader = new FileReader();
											reader.onload = function (e) {
												$('#soDeliFileImg').attr('src', e.target.result);
											}
											reader.readAsDataURL(input.files[0]);
										}
									}
								</script>
                        </div>						
					</div>
					
					<button id="btn1" type="submit" class="btn btn-default">Submit</button>		
				</div>
				<!-- col-md-6 -->
						
            </div>  
			<!--row-->
		</form>
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
// Append and Hide spinner.          
	var spinner = new Spinner().spin();
	$("#spin").append(spinner.el);
	$("#spin").hide();
  //           
	$('#form1').on("submit", function(e) {
		if ($('#form1').smkValidate()){
			var formData = new FormData(this);
			$.ajax({
				url: 'sale_add_img_ajax.php',
				type: 'POST',
				data: formData,
				processData: false,
				contentType: false,
				dataType: 'json'
			}).done(function(data) {
				if (data.success){   
					/*$.smkAlert({
						text: data.message,
						type: 'success',
						position:'top-center'
					});*/
					alert(data.message);
					//window.location.href = "sale.php";
				} else {
					$.smkAlert({
						text: data.message,
						type: 'danger',
						//position:'top-center'
					});
				}
			}).error(function (response) {
				alert(response.responseText);
			});
			e.preventDefault();
		}//.smkValidate()
	});//.btn_click

	$('#btn_delete').click (function(e) {				 
		var params = {					
		soNo: $('#soNo').val()				
		};
		//alert(params.hdrID);
		$.smkConfirm({text:'Are you sure to Delete ?', accept:'Yes', cancel:'Cancel'}, function (e){if(e){
			$.post({
				url: 'sale_img_del_ajax.php',
				data: params,
				dataType: 'json'
			}).done(function(data) {
				if (data.success){  
					alert(data.message);
					$('#soDeliFileImg').attr('src', '#');
					$('#soDeliFileImg').attr('alt', 'Image Deleted.');
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
		}});
		//smkConfirm
	});
	//.btn_click	
	
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
	});
</script>




<!-- Optionally, you can add Slimscroll and FastClick plugins.
     Both of these plugins are recommended to enhance the
     user experience. Slimscroll is required when using the
     fixed layout. -->
</body>
</html>
