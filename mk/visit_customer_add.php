<?php

?>
<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<?php include 'head.php'; ?>
    
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
       Add Visit Customer Information
        <small>Visit Customer management</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="customer.php"><i class="fa fa-dashboard"></i>Customer Information</a></li>
        <li class="active">Add Visit Customer Information</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

      <!-- Your Page Content Here -->
      <a href="visit_customer.php" class="btn btn-google">Back</a>
    <div class="box box-primary">
        <div class="box-header with-border">
        <h3 class="box-title">Add Visit Customers..</h3>
        <div class="box-tools pull-right">
          <!-- Buttons, labels, and many other things can be placed here! -->
          <!-- Here is a label for example -->
         
        </div><!-- /.box-tools -->
        </div><!-- /.box-header -->
        <div class="box-body">
            
            <div class="row">
                <div class="col-md-6">
                    <form id="form1" action="visit_customer_insert_ajax.php" method="post" class="form" novalidate>
						<div class="form-group">
                            <label for="visitDate">Visit Date</label>
                            <input id="visitDate" type="text" class="form-control datepicker" name="visitDate" data-smk-msg="Require Customer Name."required>
                        </div>
                        
						<div class="form-group">
                            <label for="custCode">Customer Name</label>
                            <select id="custCode" name="custCode" class="form-control" data-smk-msg="Require Customer." required >
								<option value=""> -- Select -- </option>
								<?php
								$sql_cust = "SELECT `ID`, `code`, `custAddr`, `custName`, `custContact`, `custTel`, `smCode` FROM `customer` WHERE `statusCode`='A' ORDER BY `custName` ASC ";
								$result_cust = mysqli_query($link, $sql_cust);
								while($row = mysqli_fetch_assoc($result_cust)){
									echo '<option value="'.$row['code'].'" 
										 data-custAddr="'.$row['custAddr'].'" 									 
										 data-smCode="'.$row['smCode'].'" 
										 data-custContact="'.$row['custContact'].'" 	
										 data-custTel="'.$row['custTel'].'" 	
										 >'.$row['custName'].' : ['.$row['code'].']</option>';
								}
								?>
							</select> 
                        </div>
						
						<div class="form-group">
                            <label for="custAddr">Customer Address</label>
							<textarea id="custAddr" class="form-control" name="custAddr" disabled></textarea>
                        </div>
						
						<div class="form-group">
                            <label for="smCode">Salesman Name</label>							
							<select id="smCode" name="smCode" class="form-control" data-smk-msg="Require Salesman." required>
								<option value=""> -- Select -- </option>
								<?php
								$sql_sm = "SELECT `id`,  `code`,  `name`, `surname`, `mobileNo`, `email` FROM `salesman` WHERE `statusCode`='A' ";
								$result_sm = mysqli_query($link, $sql_sm);
								while($row = mysqli_fetch_assoc($result_sm)){
									echo '<option value="'.$row['code'].'">'.$row['code'].' : '.$row['name'].' '.$row['surname'].'</option>';
								}
								?>
							</select>                               
                        </div>
						
						<div class="form-group">
                            <label for="custContractName">Customer Contract Name</label>
                            <input id="custContractName" name="custContactName" type="text" class="form-control" data-smk-msg="Require Customer Name."required>
                        </div>
						<div class="form-group">
                            <label for="custContractTelNo">Customer Contract Tel</label>
                            <input id="custContractTelNo" name="custContactTelNo" type="text" class="form-control" data-smk-msg="Require Customer Name."required>
                        </div>	
						<div class="form-group">
                            <label for="visitTypeCode">Visit Type</label>							
							<select id="visitTypeCode" name="visitTypeCode" class="form-control" data-smk-msg="Require Salesman." required>
								<option value=""> -- Select -- </option>
								<?php
								$sql = "SELECT `id`, `code`, `name` FROM `visit_type` WHERE `statusCode`='A' ";
								$stmt = $pdo->prepare($sql);
								$stmt->execute();
								while($row = $stmt->fetch()){
									echo '<option value="'.$row['code'].'">'.$row['code'].' : '.$row['name'].'</option>';
								}
								?>
							</select>                               
                        </div>
                        <div class="form-group">
							<label for="remark">Remark</label>
							  <textarea id="remark" name="remark" class="form-control"></textarea>
						  </div>
					  
                        <button id="btn1" type="button" class="btn btn-default">Submit</button>
                    </form>
                </div>
                        
            </div>
          
    
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
	$('#btn1').click (function(e) {
		if ($('#form1').smkValidate()){
			//alert($('#custID').val());
			$.post("visit_customer_insert_ajax.php", $("#form1").serialize() )
			.done(function(data) {
			if (data.success){ 
				$.smkAlert({
				text: data.message,
				type: 'success',
				position:'top-center'
				});
			} else {
				$.smkAlert({
				text: data.message,
				type: 'danger',
				//                                 position:'top-center'
				});
			}
			$('#form1').smkClear();
		//$("#visitDate").focus();
		}).error(function (response) {
			  alert(response.responseText);
		});  

		e.preventDefault();
		}//.if end
	});
	//.btn_click end
            
            
	$("#custCode").on("change",function(e) {
		$('#custAddr').val($('option:selected', this).attr('data-custAddr'));
		$('#smCode').val($('option:selected', this).attr('data-smCode'));
		$('#custContractName').val($('option:selected', this).attr('data-custContact'));
		$('#custContractTelNo').val($('option:selected', this).attr('data-custTel'));
		e.preventDefault();
	 });
	 //custCode change
});
//doc ready.
        
        
   
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
