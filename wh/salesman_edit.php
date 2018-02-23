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
		Salesman
        <small>Salesman management</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Main</a></li>
        <li class="active">Salesman</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
	
      <!-- Your Page Content Here -->
      <a href="salesman.php" class="btn btn-google">Back</a>
    <div class="box box-primary">
        <div class="box-header with-border">
			<h3 class="box-title">Edit Salesman</h3>           
        </div><!-- /.box-header -->
		
        <div class="box-body">
           <div class="row">
                <div class="col-md-6">
                    <form id="form1" action="salesman_edit_ajax.php" method="post" class="form" novalidate>
						<?php							
							$sql = "SELECT a.*
									FROM salesman a
									WHERE 1
									AND a.id=".$_GET['id']."
									ORDER BY a.id desc
									";
							$result = mysqli_query($link, $sql);  
							$row = mysqli_fetch_assoc($result);
						?>
						<input type="hidden" name="id" id="id" value="<?= $row['ID']; ?>" />
						<div class="form-group">
                            <label for="smId">Salesman ID</label>                            
							<div class="input-group">
								<input id="smId" type="text" class="form-control" name="smId" value="<?= $row['ID']; ?>" data-smk-msg="Require Group" disabled required>							
							</div>
                        </div>
						<div class="form-group">
                            <label for="name">Name</label>                            
							<div class="input-group">
								<input id="name" type="text" class="form-control" name="name" value="<?= $row['name']; ?>" data-smk-msg="Require Group" required>							
							</div>
                        </div>
						<div class="form-group">
                            <label for="surname">Surname</label>                            
							<div class="input-group">
								<input id="surname" type="text" class="form-control" name="surname" value="<?= $row['surname']; ?>" data-smk-msg="Require Name" required>							
							</div>
                        </div>
						<div class="form-group">
                            <label for="positionName">Position Name</label>                            
							<div class="input-group">
								<input id="positionName" type="text" class="form-control" name="positionName" value="<?= $row['positionName']; ?>" data-smk-msg="Require Name New" required>							
							</div>
                        </div>
						<div class="form-group">
                            <label for="mobileNo">Mobile</label>                            
							<div class="input-group">
								<input id="mobileNo" type="text" class="form-control" name="mobileNo" value="<?= $row['mobileNo']; ?>" data-smk-msg="Require Description" required>							
							</div>
                        </div>
						<div class="form-group">
                            <label for="email">Email</label>                            
							<div class="input-group">
								<input id="email" type="text" class="form-control" name="email" value="<?= $row['email']; ?>" data-smk-msg="Require Price" value="" required>							
							</div>
                        </div>
						<div class="form-group">
                            <label for="statusCode">Status</label>
							<div class="input-group">
								<input id="statusCode" name="statusCode" type="checkbox" value="A" <?php if ($row['statusCode']=='A') echo 'checked'; ?> > Active
							</div>							
                        </div>
						<a name="btn_submit" class="btn btn-default">Submit</a>
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

<!-- jQuery 2.2.3 -->
<script src="plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="bootstrap/js/bootstrap.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/app.min.js"></script>
<!-- Add Spinner feature -->
<script src="bootstrap/js/spin.min.js"></script>
<!-- Add smoke dialog -->
<script src="bootstrap/js/smoke.min.js"></script>
<!-- Add _.$ jquery coding -->
<script src="assets\underscore-min.js"></script>


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
				var checked='';
				$('input[name=statusCode]:checked').each(function(){
					if(checked.length==0){
						checked=$(this).val();
					}else{
						checked=checked+','+$(this).val();
					}
				});
				var params = {
					id: $('#id').val(),
					name: $('#name').val(),
					surname: $('#surname').val(),
					positionName: $('#positionName').val(),
					mobileNo: $('#mobileNo').val(),
					email: $('#email').val(),
					statusCode: checked
				};								
				//alert(params.status_code);
				$.post({
					url: 'salesman_edit_ajax.php',
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
						 //$("#title").focus(); 
				}).error(function (response) {
					  alert(response.responseText);
				});    				
			});
	});
  </script>
  

</body>
</html>
