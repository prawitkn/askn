<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<?php include 'head.php'; ?>  
<?php include 'inc_helper.php'; ?>      
<link rel="stylesheet" type="text/css" href="bootstrap-clockpicker-gh-pages/dist/bootstrap-clockpicker.min.css">
 
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
      <h1>
		ข้อมูลหลัก
        <small>Data management</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> หน้าหลัก</a></li>
        <li class="active">ข้อมูลหลัก</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
	
      <!-- Your Page Content Here -->
      <a href="index.php" class="btn btn-google">กลับ</a>
    <div class="box box-primary">
        <div class="box-header with-border">
			<h3 class="box-title">อัพโหลดข้อมูลหลัก</h3>          
        </div><!-- /.box-header -->
		
        <div class="box-body">
           <div class="row">
				<div class="col-md-12">
				<?php 
					if(isset($_POST["Import"]))
					{
						//First we need to make a connection with the database
						
						echo $filename=$_FILES["file"]["tmp_name"];
										
						if($_FILES["file"]["size"] > 0)
						{
							$file = fopen($filename, "r");
							//$sql_data = "SELECT * FROM prod_list_1 ";
							$count = 0;                                         // add this line
							while (($emapData = fgetcsv($file, 10000, ",")) !== FALSE)
							{
								//print_r($emapData);
								//exit();
								$count++;                                      // add this line
								if($count>1){                                  // add this line									
									$sql = "INSERT into target_cust(
									`year`, `month`, `custCode`, `budget`, `forecast`
									) values 
									('$year','$emapData[0]','$emapData[1]','$emapData[2]','$emapData[3]')";
									mysqli_query($link, $sql);
								}                                              // add this line
							}
							fclose($file);
							echo 'CSV File has been successfully Imported';
							//header('Location: index.php');
						}
						else
							echo 'Invalid File:Please Upload CSV File';
					}
					?>
				<form enctype="multipart/form-data" method="post" role="form" >
					<div class="form-group">
						<label for="exampleInputFile">File Upload</label>
						<input type="file" name="file" id="file" size="150">
						<p class="help-block">Only Excel/CSV File Import.</p>
					</div>
					<button type="submit" class="btn btn-default" name="Import" value="Import">Upload</button>
				</form>
				</div>
				<!--col-->
            </div>
			<!--row-->
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

<!-- jQuery 2.2.3 -->
<script src="plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="bootstrap/js/bootstrap.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/app.min.js"></script>
<!-- Add Spinner feature -->
<script src="bootstrap/js/spin.min.js"></script>

<script src="bootstrap/js/smoke.min.js"></script>



<script> 
  // to start and stop spiner.  
$( document ).ajaxStart(function() {
        $("#spin").show();
		}).ajaxStop(function() {
            $("#spin").hide();
        });  		
		
       $(document).ready(function() {    
            $("#title").focus();
            var spinner = new Spinner().spin();
            $("#spin").append(spinner.el);
            $("#spin").hide();
				
			$('a[name=btn_submit]').click(function(){
				var params = {
					id: $('#id').val(),
					nickname: $('#nickname').val(),
					birth_date: $('#birth_date').val(),
					origin_gen_no: $('#origin_gen_no').val(),
					service_sub_abb: $('#service_sub_abb').val(),
					height: $('#height').val(),
					weight: $('#weight').val(),
					blood_type_code: $('#blood_type_code').val()
				};
				alert(params.birth_date);
				$.post({
					url: 'info2_edit_ajax.php',
					data: params,
					dataType: 'json'
				}).done(function (data) {					
					 if (data.success){ 
						 $.smkAlert({
							 text: data.message,
							 type: 'success',
							 position:'top-center'
						 });
						 } else {
							 $.smkAlert({
								 text: data.message,
								 type: 'danger'//,
	   //                        position:'top-center'
								 });
						 }
						 $('#form1').smkClear();
						 $("#title").focus(); 
				}).error(function (response) {
					  alert(response.responseText);
				});    
				e.preventDefault();
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
	
	
	

	
<!-- TIEM PICKER -->	
<script type="text/javascript" src="bootstrap-clockpicker-gh-pages/dist/bootstrap-clockpicker.min.js"></script>
	<script type="text/javascript">
	$('.clockpicker').clockpicker();
</script>
<script type="text/javascript">
$('.clockpicker').clockpicker()
	.find('input').change(function(){
		console.log(this.value);
		//alert($(this).val());
});

</script>
<!-- TIEM PICKER END-->	



	
	
</body>
</html>
