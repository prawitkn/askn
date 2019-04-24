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
$rootPage = 'userDept';

//Check user roll.
switch($s_userGroupCode){
	case 'admin' : 
		break;
	default : 
		header('Location: access_denied.php');
		exit();
}
$id=$_GET['id'];

$sql = "SELECT hdr.`id`, hdr.`code`, hdr.`name`, hdr.`statusCode`
, hdr.`createTime`, hdr.`createById`, hdr.`updateTime`, hdr.`updateById`, hdr.`deleteTime`, hdr.`deleteById`
, uc.userFullname as createByName 
, uu.userFullname as updateByName 
FROM `wh_user_dept` hdr 
LEFT JOIN `wh_user` uc on uc.userID=hdr.deleteById 
LEFT JOIN `wh_user` uu on uu.userID=hdr.updateById 
WHERE 1=1 
AND hdr.id=:id 
LIMIT 1  
";		
//$result = mysqli_query($link, $sql);
$stmt = $pdo->prepare($sql);	
$stmt->bindParam(':id', $id);	
$stmt->execute();	
$row=$stmt->fetch();	
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
      <h1><i class="glyphicon glyphicon-th-large"></i>
       User Department
        <small>User Department management</small>
      </h1>
	  <ol class="breadcrumb">
        <li><a href="<?=$rootPage;?>.php"><i class="glyphicon glyphicon-list"></i>User Department List</a></li>
		<li><a href="#"><i class="glyphicon glyphicon-edit"></i>User Department</a></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

      <!-- Your Page Content Here -->
    <div class="box box-primary">
        <div class="box-header with-border">
        <h3 class="box-title">Edit User Department</h3>
        <div class="box-tools pull-right">
          <!-- Buttons, labels, and many other things can be placed here! -->
          <!-- Here is a label for example -->
         
        </div><!-- /.box-tools -->
        </div><!-- /.box-header -->
        <div class="box-body">            
            <div class="row">                
                    <form id="form1"  method="post" class="form" validate>
					<input type="hidden" name="action" value="edit" />				
					<div class="col-md-6">	
						<input id="id" type="hidden" name="id" value="<?=$row['id'];?>" />				
                        <div class="form-group">
                            <label for="code">User Department Code</label>
                            <input id="code" type="text" class="form-control" name="code" value="<?=$row['code'];?>"  data-smk-msg="Require user group code."required>
                        </div>
                        
                        <div class="form-group">
                            <label for="name">User Department Name</label>
                            <input id="name" type="text" class="form-control" name="name" value="<?=$row['name'];?>"  data-smk-msg="Require uer group name" required>
                        </div>
						<div class="form-group">
                            <label for="statusCode">Status</label>
							<input type="radio" name="statusCode" value="A" <?php echo ($row['statusCode']=='A'?' checked ':'');?> >Active
							<input type="radio" name="statusCode" value="I" <?php echo ($row['statusCode']=='I'?' checked ':'');?> >Inactive
						</div>
						
						<button id="btn1" type="submit" class="btn btn-default"><i class="fa fa-save"></i> Edit User Department</button>
					</div>
					<!--/.col-md-->
					<div class="col-md-6">
						
					</div>
					<!--/.col-md-->
                    </form>
                </div>
                <!--/.row-->       
            </div>
			<!--.body-->    
    </div>
	<!-- /.box box-primary -->
  

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
	$("#title").focus();

	var spinner = new Spinner().spin();
	$("#spin").append(spinner.el);
	$("#spin").hide();
//           
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
				if (data.success){  
					$.smkAlert({
						text: data.message,
						type: 'success',
						position:'top-center'
					});					
					setTimeout(function(){history.back();}, 2000);
				}else{
					$.smkAlert({
						text: data.message,
						type: 'danger',
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

<!-- Optionally, you can add Slimscroll and FastClick plugins.
     Both of these plugins are recommended to enhance the
     user experience. Slimscroll is required when using the
     fixed layout. -->
</body>
</html>
