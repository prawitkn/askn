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
	$sql = "SELECT `soNo`, `poNo`, `saleDate`, `custCode`, `smCode`, `total`, `vatAmount`, `netTotal`, `prodGFC`, `prodGFM`, `prodGFT`, `prodSC`, `prodCFC`, `prodEGWM`, `prodGT`, `prodCSM`, `prodWR`, `deliveryDate`, `deliveryRem`, `suppTypeFact`, `suppTypeImp`, `prodTypeOld`, `prodTypeNew`, `custTypeOld`, `custTypeNew`, `prodStkInStk`, `prodStkOrder`, `prodStkOther`, `prodStkRem`, `packTypeAk`, `packTypeNone`, `packTypeOther`, `packTypeRem`, `priceOnOrder`, `priceOnOther`, `priceOnRem`, `remark`, `plac2deliCode`, `plac2deliRem`, `payTypeCode`, `payTypeRem`, `statusCode`, `createTime`, `createByID` 
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
        <h3 class="box-title">Edit Sales Order : <b style="color: red;"><?= $r['soNo']; ?></b></h3>
        <div class="box-tools pull-right">
          <!-- Buttons, labels, and many other things can be placed here! -->
          <!-- Here is a label for example -->
         
        </div><!-- /.box-tools -->
        </div><!-- /.box-header -->
        <div class="box-body">
            
            <div class="row">
				<form id="form1" action="sale_edit_ajax.php" method="post" class="form" novalidate>
                <div class="col-md-6"> 
					<input type="hidden" id="id" name="id" value="<?= $r['id']; ?>" />
					<div class="row">
						<div class="col-md-6 form-group">
                            <label for="saleDate">sale Date</label>
                            <input id="saleDate" type="text" class="form-control datepicker" name="saleDate" value="<?= to_thai_date($r['saleDate']); ?>" data-smk-msg="Require sale Date." required>
                        </div>
						<div class="col-md-6 form-group">
                            <label for="poNo">PO No.</label>
                            <input id="poNo" type="text" class="form-control" name="poNo" value="<?= $r['poNo']; ?>" data-smk-msg="Require PO No." required>
                        </div>	
					</div>
						
						<div class="form-group">
                            <label for="custCode">Customer Name</label>
                            <select id="custCode" name="custCode" class="form-control" data-smk-msg="Require Customer." required >
								<option value=""> -- Select -- </option>
								<?php
								$custAddr = '';
								$sql_cust = "SELECT `code`, `custAddr`, `custName`, `custContact`, `custTel`, `smCode` FROM `customer` WHERE `statusCode`='A' ORDER BY `custName` ASC ";
								$result_cust = mysqli_query($link, $sql_cust);
								while($row = mysqli_fetch_assoc($result_cust)){
									$selected = ( $r['custCode'] == $row['code'] ? 'selected' : '' );
									$custAddr = ( $r['custCode'] == $row['code'] ? $row['custAddr'] : '' );
									echo '<option value="'.$row['code'].'" 
										 data-custAddr="'.$row['custAddr'].'" 									 
										 data-smCode="'.$row['smCode'].'" 
										 '.$selected.'
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
								$sql_sm = "SELECT `code`, `name`, `surname`, `mobileNo`, `email` FROM `salesman` WHERE `statusCode`='A' ";
								$result_sm = mysqli_query($link, $sql_sm);
								while($row = mysqli_fetch_assoc($result_sm)){
									$selected = ( $r['smCode'] == $row['code'] ? 'selected' : '' );
									echo '<option value="'.$row['code'].'" '.$selected.' >'.$row['code'].' : '.$row['name'].' '.$row['surname'].'</option>';
								}
								?>
							</select>                            
                        </div>						
						
						<div class="form-group">
                            <label for="custAddr">Customer Address</label>
							<textarea id="custAddr" class="form-control" name="custAddr" disabled><?= $custAddr; ?></textarea>
                        </div>
                        
						<div class="row">
							<div class="col-md-6 form-group">
								<label for="deliveryDate">Delivery Date / Load Date</label>
								<input type="text" id="deliveryDate" name="deliveryDate" class="form-control datepicker" value="<?= to_thai_date($r['deliveryDate']); ?>" data-smk-msg="Require Delivery Date / Load Date." required>
							</div>
							<div class="col-md-6 form-group">
								<label for="deliveryRem">Delivery Remark / Load Remark</label>
								<input type="text" id="deliveryRem" name="deliveryRem" value="<?= $r['deliveryRem']; ?>" class="form-control" >
							</div>
						</div>
						
						<div class="form-group">
							<label for="remark">Order Remark</label>
							  <input type="text" id="remark" name="remark"  value="<?= $r['remark']; ?>" class="form-control" />
						  </div>
						  						  
						 <div class="form-group">
							<label for="prodName">Product Name</label><br/>
							<div class="col-md-6">
							  <input type="checkbox" name="prodGFC" id="prodGFC" <?php echo ($r['prodGFC']==1? 'checked' : ''); ?>  >
							  GLASS FIBER CLOTH</br>
							  <input type="checkbox" name="prodGFM" id="prodGFM" <?php echo ($r['prodGFM']==1? 'checked' : ''); ?>  >
							  GLASS FIBER MESH</br>
							  <input type="checkbox" name="prodGFT" id="prodGFT" <?php echo ($r['prodGFT']==1? 'checked' : ''); ?>  >
							  GLASS FIBER TAPE</br>
							  <input type="checkbox" name="prodSC" id="prodSC" <?php echo ($r['prodSC']==1? 'checked' : ''); ?>  >
							  SILICA CLOTH</br>
							  <input type="checkbox" name="prodCFC" id="prodCFC" <?php echo ($r['prodCFC']==1? 'checked' : ''); ?>  >
							  CABON FIBER CLOTH</br>
							</div>
							<div class="col-md-6">
							  <input type="checkbox" name="prodEGWM" id="prodEGWM" <?php echo ($r['prodEGWM']==1? 'checked' : ''); ?>  >
							  E-GLASS WOOL MAT</br>
							  <input type="checkbox" name="prodGT" id="prodGT" <?php echo ($r['prodGT']==1? 'checked' : ''); ?>  >
							  GLASS TISSUE</br>
							  <input type="checkbox" name="prodCSM" id="prodCSM" <?php echo ($r['prodCSM']==1? 'checked' : ''); ?>  >
							  CHOPPED STRAND MAT</br>
							  <input type="checkbox" name="prodWR" id="prodWR" <?php echo ($r['prodWR']==1? 'checked' : ''); ?>  >
							  WOVEN ROVING
							  </div>
						  </div>
						</div>
						<!-- col-md-6 --> 
						
						<div class="col-md-6">					  
						  <!-- checkbox -->
						  <div class="form-group">
							<label for="suppType">Product Supp Type</label><br/>
							  <input type="checkbox" name="suppTypeFact" id="suppTypeFact" <?php echo ($r['suppTypeFact']==1? 'checked' : ''); ?> >
							  Factory Product&nbsp;&nbsp;
							  <input type="checkbox" name="suppTypeImp" id="suppTypeImp" <?php echo ($r['suppTypeImp']==1? 'checked' : ''); ?> >
							  Import Product
						  </div>
						  
						  <div class="form-group">
							<label for="prodType">Product Type</label><br/>
							<input type="checkbox" name="prodTypeOld" id="prodTypeOld" <?php echo ($r['prodTypeOld']==1? 'checked' : ''); ?> >
							  Old Product&nbsp;&nbsp;
							  <input type="checkbox" name="prodTypeNew" id="prodTypeNew" <?php echo ($r['prodTypeNew']==1? 'checked' : ''); ?> >
							  New Product
						  </div>
						  
						  <div class="form-group">
							<label for="custType">Customer Type</label><br/>
							  <input type="checkbox" name="custTypeOld" id="custTypeOld" <?php echo ($r['custTypeOld']==1? 'checked' : ''); ?> >
							  Old Customer&nbsp;&nbsp;
							  <input type="checkbox" name="custTypeNew" id="custTypeNew" <?php echo ($r['custTypeNew']==1? 'checked' : ''); ?> >
							  New Customer
						  </div>
						  
						  <div class="form-group">
							<label for="prodStk">Product Stock</label><br/>
									<input type="checkbox" name="prodStkInStk" id="prodStkInStk" <?php echo ($r['prodStkInStk']==1? 'checked' : ''); ?> >
									In Stock&nbsp;&nbsp;
									<input type="checkbox" name="prodStkOrder" id="prodStkOrder" <?php echo ($r['prodStkOrder']==1? 'checked' : ''); ?> >
									Order&nbsp;&nbsp;
								  <input type="checkbox" name="prodStkOther" id="prodStkOther" <?php echo ($r['prodStkOther']==1? 'checked' : ''); ?> class="">
								  Other
									<input type="text" name="prodStkRem" id="prodStkRem" value="<?= $r['prodStkRem']; ?>" class="col-md-2 form-control" <?php echo ($r['prodStkOther']==1? '' : 'style="display: none;"'); ?> >
								<!-- row -->
						  </div>
						  
						  <div class="form-group">
							<label for="packType">Packing Type</label><br/>
								  <input type="checkbox" name="packTypeAk" id="packTypeAk" <?php echo ($r['packTypeAk']==1? 'checked' : ''); ?> >
								  AK Logo&nbsp;&nbsp;
								  <input type="checkbox" name="packTypeNone" id="packTypeNone" <?php echo ($r['packTypeNone']==1? 'checked' : ''); ?> >
								  Non AK Logo&nbsp;&nbsp;
								  <input type="checkbox" name="packTypeOther" id="packTypeOther" <?php echo ($r['packTypeOther']==1? 'checked' : ''); ?> class="">
								  Other <input type="text" name="packTypeRem" id="packTypeRem" value="<?= $r['packTypeRem']; ?>" class="col-md-2 form-control" <?php echo ($r['packTypeOther']==1? '' : 'style="display: none;"'); ?> >
								<!-- row -->
						  </div>
											  
						  <div class="form-group">
								<label for="priceOn">Price On</label><br/>
								  <input type="checkbox" name="priceOnOrder" id="priceOnOrder" <?php echo ($r['priceOnOrder']==1? 'checked' : ''); ?> >
								  on Sales Order&nbsp;&nbsp;
								  <input type="checkbox" name="priceOnOther" id="priceOnOther" <?php echo ($r['priceOnOther']==1? 'checked' : ''); ?> >
								  Other 								  
								  <input type="text" name="priceOnRem" id="priceOnRem" value="<?= $r['priceOnRem']; ?>" class="col-md-2 form-control" <?php echo ($r['priceOnOther']==1? '' : 'style="display: none;"'); ?> >							 											  
						  </div>
						  
						  <div class="row">
							<div class="col-md-6 form-group">
								<label for="plac2deliCode">Place to Delivery</label><br/>
								  <input type="radio" name="plac2deliCode" id="plac2deliCodeFact" value="FACT" <?php echo ($r['plac2deliCode']=='FACT'? 'checked' : ''); ?>   data-smk-msg="Require Place to Delivery." required>
								  AK Factory<br/>
								  <input type="radio" name="plac2deliCode" id="plac2deliCodeSend" value="SEND" <?php echo ($r['plac2deliCode']=='SEND'? 'checked' : ''); ?> >
								  Send by AK Factory<br/>
								  <input type="radio" name="plac2deliCode" id="plac2deliCodeMaps" value="MAPS" <?php echo ($r['plac2deliCode']=='MAPS'? 'checked' : ''); ?> >
								  Map<br/>
								  <input type="radio" name="plac2deliCode" id="plac2deliCodeLogi" value="LOGI" <?php echo ($r['plac2deliCode']=='LOGI'? 'checked' : ''); ?> >
								  Logistic
								<input type="textbox" name="plac2deliRem" id="plac2deliRem" value="<?= $r['plac2deliRem']; ?>" class="form-control" />
							  </div>
							  
							<div class="col-md-6 form-group">
							<label for="payTypeCode">Credit</label><br/>
							  <input type="radio" name="payTypeCode" value="CASH" <?php echo ($r['payTypeCode']=='CASH'? 'checked' : ''); ?> >
							  by Cash<br/>
							  <input type="radio" name="payTypeCode" value="CHEQ" <?php echo ($r['payTypeCode']=='CHEQ'? 'checked' : ''); ?> >
							  by Cheque<br/>
							  <input type="radio" name="payTypeCode" value="TRAN" <?php echo ($r['payTypeCode']=='TRAN'? 'checked' : ''); ?> >
							  Transfer
							  <input type="textbox" name="payTypeRem" id="payTypeRem" value="<?= $r['payTypeRem']; ?>" class="form-control" />
						  </div>
					  
							  
						  </div>
						  <!-- row -->		  
                </div>
                <!-- col-md-6 -->


<button id="btn1" type="button" class="btn btn-default">Submit</button>				
            </div>          
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
	$('#btn1').click (function(e) {
		if ($('#form1').smkValidate()){
			$.post("sale_edit_ajax.php", $("#form1").serialize() )
				.done(function(data) {
					if (data.success){   
						/*$.smkAlert({
							text: data.message,
							type: 'success',
							position:'top-center'
						});*/
						alert(data.message);
						window.location.href = "sale.php";
					} else {
						$.smkAlert({
							text: data.message,
							type: 'danger',
							//position:'top-center'
						});
					}
					$('#form1').smkClear();
					//$("#visitDate").focus();
				});
			e.preventDefault();
		}//.smkValidate()
	});//.btn_click
	
	$("#custCode").on("change",function(e) {
		$('#custAddr').val($('option:selected', this).attr('data-custAddr'));
		$('#smCode').val($('option:selected', this).attr('data-smCode'));
		e.preventDefault();
	 });
	$('input[name=prodStkOther]').on("change" ,function() {
		if($(this).is(':checked')){
			$('#prodStkRem').show().focus();
		}else{
			$('#prodStkRem').val('').hide();
		}
	}); 
	$('input[name=packTypeOther]').on("change" ,function() {
		if($(this).is(':checked')){
			$('#packTypeRem').show().focus();
		}else{
			$('#packTypeRem').val('').hide();
		}
	}); 
	$('input[name=priceOnOther]').on("change" ,function() {
		if($(this).is(':checked')){
			$('#priceOnRem').show().focus();
		}else{
			$('#priceOnRem').val('').hide();
		}
	}); 
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
