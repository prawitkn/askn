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
//Check user roll.
switch($s_userGroupCode){
	case 'admin' : case 'salesAdmin' : case 'it' :
		break;
	default : 
		include 'access_denied2.php';
		exit();
}
?>

<div class="wrapper">

  <!-- Main Header -->
<?php 

include 'header.php'; 
include 'leftside.php';

$rootPage = 'salesman';
$tb="salesman";

$id=$_GET['id'];
$action=$_GET['act'];

$sql = "SELECT hdr.`id`, hdr.`code`, hdr.`name`, hdr.`surname`, hdr.`smType`, hdr.`photo`, hdr.`positionName`, hdr.`mobileNo`, hdr.`email`, hdr.`statusCode`
FROM ".$tb." hdr 
WHERE 1=1
AND hdr.id=:id 
";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $id);
$stmt->execute();
$row = $stmt->fetch();

?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
	<section class="content-header">
		<h1><i class="glyphicon glyphicon-briefcase"></i>
       Salesman
        <small>Salesman management</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?=$rootPage;?>.php"><i class="glyphicon glyphicon-list"></i>Salesman List</a></li>
		<li><a href="#"><i class="glyphicon glyphicon-edit"></i>Salesman</a></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

      <!-- Your Page Content Here -->
    <div class="box box-primary">
        <div class="box-header with-border">
        <h3 class="box-title"><?php echo ($action=="add"?"Add":"Edit"); ?> Salesman <span style="font-weight: bold;"><?php echo ($action=="add"?"":" : ".$row['name'].' '.$row['surname']); ?></span></h3>
        <div class="box-tools pull-right">
          <!-- Buttons, labels, and many other things can be placed here! -->
          <!-- Here is a label for example -->
         
        </div><!-- /.box-tools -->
        </div><!-- /.box-header -->
        <div class="box-body">            
            <div class="row">                
                    <form id="form1" method="post" class="form" enctype="multipart/form-data" validate>
					<input id="action" type="hidden" name="action" value="<?=$action;?>" />
					<div class="col-md-6">						
						<input id="id" type="hidden" name="id" value="<?=$row['id'];?>" />				
						<div class="form-group">
                            <label for="code" style="color: blue;">Code</label>
                            <input id="code" type="text" class="form-control" name="code" value="<?=$row['code'];?>" data-smk-msg="Require Code" required>
                        </div>
						<div class="form-group">
                            <label for="name" style="color: blue;">Name</label>
                            <input id="name" type="text" class="form-control" name="name" value="<?=$row['name'];?>" data-smk-msg="Require Name" required>
                        </div>
						<div class="form-group">
                            <label for="surname" >Surname</label>
                            <input id="surname" type="text" class="form-control" name="surname" value="<?=$row['surname'];?>" >
                        </div>
						<div class="form-group">
                            <label for="mobileNo" >Mobile No</label>
                            <input id="mobileNo" type="text" class="form-control" name="mobileNo" value="<?=$row['mobileNo'];?>" >
                        </div>
						<div class="form-group">
                            <label for="email" >E-mail</label>
                            <input id="email"  type="email" class="form-control" name="email" value="<?=$row['email'];?>" >
                        </div>					
					</div>
					<!--/.col-md-->
					<div class="col-md-6">
						<div class="form-group">
                            <label for="positionName" >Position</label>
                            <input id="positionName" type="text" class="form-control" name="positionName" value="<?=$row['positionName'];?>" >
                        </div>						
						<div class="form-group">
                            <label for="smType">Type</label>
							<input type="radio" name="smType" value="S" <?php echo ($row['smType']=='S'?' checked ':'');?> required> Sales
							<input type="radio" name="smType" value="A" <?php echo ($row['smType']=='A'?' checked ':'');?> required> Sales Admin
						</div>	
						<div class="form-group">
                            <label for="statusCode">Status</label>
							<input type="radio" name="statusCode" value="A" <?php echo ($row['statusCode']=='A'?' checked ':'');?> required>Active
							<input type="radio" name="statusCode" value="I" <?php echo ($row['statusCode']=='I'?' checked ':'');?> required>Non-Active
						</div>
						<div class="form-group">
							<input type="hidden" name="curPhoto" id="curPhoto" value="<?=$row['photo'];?>" />
							<input type="file" name="inputFile" accept="image/*" multiple  onchange="showMyImage(this)" /> <br/>
							<img id="thumbnil" style="width:50%; margin-top:10px;"  src="dist/img/<?php echo (empty($row['photo'])? 'default.jpg' : $row['photo']); ?>" alt="image"/>
						</div>
                        <button id="btn_submit" type="submit" class="btn btn-primary">
							<i class="glyphicon glyphicon-save"></i> Submit</button>
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
	$("#code").select();

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
					$('#form1')[0].reset();
					setTimeout(function(){ window.location.href = "<?=$rootPage;?>.php";},1000); 					
				}else{
					$.smkAlert({
						text: data.message,
						type: 'danger',
						position:'top-center'
					});
				}				
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
	 
<script>
function showMyImage(fileInput) {
        var files = fileInput.files;
        for (var i = 0; i < files.length; i++) {           
            var file = files[i];
            var imageType = /image.*/;     
            if (!file.type.match(imageType)) {
                continue;
            }           
            var img=document.getElementById("thumbnil");            
            img.file = file;    
            var reader = new FileReader();
            reader.onload = (function(aImg) { 
                return function(e) { 
                    aImg.src = e.target.result; 
                }; 
            })(img);
            reader.readAsDataURL(file);
        }    
    }
</script>
	 
</body>
</html>
