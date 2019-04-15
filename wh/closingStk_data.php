<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<?php include 'head.php'; 

//Check user roll.
switch($s_userGroupCode){
	case 'admin' : case 'whMgr' : 
		break;
	default : 
		header('Location: access_denied.php');
		exit();
}
?>	<!-- head.php included session.php! -->
 
</head>
<body class="hold-transition <?=$skinColorName;?> sidebar-mini">

<?php 

	$rootPage = 'closingStk';
	$tb = 'wh_closing_stock';

?>	
</head>
<body class="hold-transition skin-yellow sidebar-mini">   
	
<div class="wrapper">

  <!-- Main Header -->
  <?php include 'header.php'; ?>
  
  <!-- Left side column. contains the logo and sidebar -->
   <?php include 'leftside.php'; ?>
   <?php

   //clossingDate
	$closingDate = (isset($_GET['closingDate'])?$_GET['closingDate']: date('d-m-Y') );
	$closingDate = str_replace('/', '-', $closingDate);
	$closingDateYmd="";
	if($closingDate<>""){ $closingDateYmd = date('Y-m-d', strtotime($closingDate));	}
	//end clossing date

   ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
     <h1><i class="fa fa-warning"></i>
       Closing Stock
        <small>Transaction management</small>
      </h1>

	  <ol class="breadcrumb">
        <li><a href="<?=$rootPage;?>_list.php"><i class="fa fa-list"></i>Closing Stock List</a></li>
		<li><a href="#"><i class="fa fa-edit"></i>Add new Closing Stock</a></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

      <!-- Your Page Content Here -->
    <div class="box box-primary">
        <div class="box-header with-border">
		<label class="box-tittle" style="font-size: 20px;"> Add Closing Stock</label>

		
        <div class="box-tools pull-right">
          <!-- Buttons, labels, and many other things can be placed here! -->
          <!-- Here is a label for example -->
         
        </div><!-- /.box-tools -->
        </div><!-- /.box-header -->
        <div class="box-body">  <form id="form1" method="post" class="form" enctype="multipart/form-data" validate>
			<form id="form1" action="<?=$rootPage;?>_ajax.php" method="post" class="form" validate >
				<div class="row"> 
					<input type="hidden" name="action" value="add" />
					
					<div class="row col-md-6">					
                        <div class="form-group col-md-6">
                            <label for="closingDate">Closing Date</label>
						<input type="text" id="closingDate" name="closingDate" value="" class="form-control datepicker" data-smk-msg="Require Closing Date." required >
                        </div>
                       
					</div>
					<!--/.col-md-->
					<div class="row col-md-6">
						
					</div>
					<!--/.col-md-->
				</div>
				<!--/.row-->   
				
				<div class="row">
					<div class="col-md-6">   
					<!--<a href="#" name="btnLockTransection" class="btn btn-default" ><i class="glyphicon glyphicon-play" ></i> </a>
					-->

					<button id="btnSubmit" type="submit" class="btn btn-defalut">Submit</button>
					</div>
				</div>
				<!--/.row--> 
			</form>	
			<!--form1-->
				
        </div>
		<!--.body-->    
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
	$("#code").focus();

	var spinner = new Spinner().spin();
	$("#spin").append(spinner.el);
	$("#spin").hide();
//           
	
	$('#btnSubmit').click(function(){
        if(prompt('Please print out CLOSING STOCK STATEMENT REPORT, then enter "HAS BEEN PRINTED OUT" for execute closing stock process.', '')==="HAS BEEN PRINTED OUT"){ 
            return true;
        }
        alert('Process is canceled.');
        return false;
    });//$('#current_pwd').focusout(function(){

	$('#form1').on("submit", function(e) {
		if ($('#form1').smkValidate()) {
			$.ajax({
			url: '<?=$rootPage;?>_ajax.php',
			type: 'POST',
			data: new FormData( this ),
			processData: false,
			contentType: false,
			dataType: 'json'
			}).done(function (data) { 
				if (data.status === "success"){  
					$.smkAlert({
						text: data.message,
						type: data.status,
						position:'top-center'
					});					
					setTimeout(function(){history.back();}, 1000);
				}else{
					$.smkAlert({
						text: data.message,
						type: data.status,
						position:'top-center'
					});
				}
			})
			.error(function (response) {
				  alert(response.responseText);
			});  
			//.ajax		
			e.preventDefault();
		}   
		//end if 
		e.preventDefault();
	});
	//form.submit
});
//doc ready
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
		<?php if($closingDateYmd<>"") { ?>
			var queryDate = '<?=$closingDateYmd;?>',
			dateParts = queryDate.match(/(\d+)/g)
			realDate = new Date(dateParts[0], dateParts[1] - 1, dateParts[2]); 
			$('#closingDate').datepicker('setDate', realDate);
		<?php }else{ ?> $('#closingDate').datepicker('setDate', '0'); <?php } ?>
		//จบ กำหนดเป็น วันที่จากฐานข้อมูล
				
		
	});
</script>

</body>
</html>
