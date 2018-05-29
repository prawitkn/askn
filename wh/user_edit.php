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
$rootPage = 'user';
$tb = 'wh_user';

//Check user roll.
switch($s_userGroupCode){
	case 'admin' : case 'it' : 
		break;
	default : 
		header('Location: access_denied.php');
		exit();
}
$id=$_GET['id'];

$sql = "SELECT hdr.`userID` as id, hdr.`userName`, hdr.`userPassword`, hdr.`userFullname`, hdr.`userGroupCode`
, hdr.`userDeptCode`, hdr.`userEmail`, hdr.`userTel`, hdr.`userPicture`, hdr.`statusCode` , hdr.`loginStatus` 
, ug.`name` as userGroupName 
FROM `".$tb."` hdr 
LEFT JOIN wh_user_group ug on ug.code=hdr.userGroupCode  
WHERE 1=1
AND hdr.userID=:id 
";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $id);
$stmt->execute();
$row = $stmt->fetch();
	
?>

<div class="wrapper">

  <!-- Main Header -->
  <?php include 'header.php'; ?>
  
  <!-- Left side column. contains the logo and sidebar -->
   <?php include 'leftside.php'; ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1><i class="glyphicon glyphicon-user"></i>
       Users
        <small>User management</small>
      </h1>
	  <ol class="breadcrumb">
        <li><a href="<?=$rootPage;?>.php"><i class="glyphicon glyphicon-list"></i>User List</a></li>
		<li><a href="#"><i class="glyphicon glyphicon-edit"></i>User</a></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

      <!-- Your Page Content Here -->
    <div class="box box-primary">
        <div class="box-header with-border">
        <h3 class="box-title">Edit User</h3>
        <div class="box-tools pull-right">
          <!-- Buttons, labels, and many other things can be placed here! -->
          <!-- Here is a label for example -->
         
        </div><!-- /.box-tools -->
        </div><!-- /.box-header -->
        <div class="box-body">            
            <div class="row">                
                    <form id="form1" method="post" class="form" enctype="multipart/form-data" validate>
					<input type="hidden" name="action" value="edit" />
					<div class="col-md-6">	
						<input id="userId" type="hidden" name="userId" value="<?=$row['id'];?>" />		
						<div class="form-group">
                            <label for="userName">Username</label>
                            <input id="userName" type="text" class="form-control" name="userName" value="<?=$row['userName'];?>" data-smk-msg="Require userName" required>
                        </div>
                        <div class="form-group">
                            <label for="userPassword">User Password<small style="color: red;"> *** Leave blank for not change user password.</small></label>
                            <input id="userPassword" type="text" class="form-control" name="userPassword" >							
                        </div>
						
                        <div class="form-group">
                            <label for="userFullname">User Fullname</label>
                            <input id="userFullname" type="text" class="form-control" name="userFullname" value="<?=$row['userFullname'];?>" data-smk-msg="Require userFullname."required>
                        </div>                     
                        
                        <div class="form-group">
                            <label for="userEmail">User Email</label>
                            <input id="userEmail" type="email" class="form-control" name="userEmail" value="<?=$row['userEmail'];?>" data-smk-msg="Require userEmail" required>
                        </div>
                        <div class="form-group">
                            <label for="userTel">Telephone</label>
                            <input id="userTel" type="text" class="form-control" name="userTel" value="<?=$row['userTel'];?>" data-smk-msg="Require Telephone number" required>                        
						</div>
					</div>
					<!--/.col-md-->
					<div class="col-md-6">
						<div class="form-group">
							<label for="userGroupCode">User Group</label>
							<select id="userGroupCode" name="userGroupCode" class="form-control"  data-smk-msg="Require User Group" required>
								<option value=""> -- Select -- </option>
								<?php
								$sql = "SELECT `id`, `code`, `name`, `statusCode`  FROM `wh_user_group` WHERE statusCode='A' ";							
								$stmt = $pdo->prepare($sql);		
								$stmt->execute();
								while($rOption = $stmt->fetch()){
									$selected = ($rOption['code']==$row['userGroupCode']?' selected ':'');									
									echo '<option value="'.$rOption['code'].'" '
										.$selected
										 .'>'.$rOption['code'].' : ['.$rOption['name'].']</option>';
								}
								?>
							</select>
						</div>		
						<div class="form-group">
							<label for="userDeptCode">User Dept.</label>
							<select id="userDeptCode" name="userDeptCode" class="form-control"  data-smk-msg="Require User Group" >
								<option value=""> -- Select -- </option>
								<?php
								$sql = "SELECT `id`, `code`, `name`, `statusCode`  FROM `wh_user_dept` WHERE statusCode='A' ";							
								$stmt = $pdo->prepare($sql);		
								$stmt->execute();
								while($rOption = $stmt->fetch()){
									$selected = ($rOption['code']==$row['userDeptCode']?' selected ':'');									
									echo '<option value="'.$rOption['code'].'" '
										.$selected
										 .'>'.$rOption['code'].' : ['.$rOption['name'].']</option>';
								}
								?>
							</select>
						</div>	
                        <!--<div class="form-group">
							<img src="dist/img/<?=$row['userPicture'];?>" style="width: 100px; height:100px; border: 1px solid black;" /><br/>
                            <label for="userPicture">Choose personal picture file input.</label>
                            <input type="file" id="userPicture" name="userPicture">
                            <p class="help-block">Please select picture file .jpg, .png, .gif</p>
                        </div>-->
						<div class="form-group">
                            <label for="statusCode">Status</label>
							<input type="radio" name="statusCode" value="A" <?php echo ($row['statusCode']=='A'?' checked ':'');?> >Active
							<input type="radio" name="statusCode" value="I" <?php echo ($row['statusCode']=='I'?' checked ':'');?> >Non-Active
						</div>
						<div class="form-group">
                            <label for="loginStatus">Is Loged In</label>
							<input type="radio" name="loginStatus" value="1" <?php echo ($row['loginStatus']=='1'?' checked ':'');?> >Yes
							<input type="radio" name="loginStatus" value="0" <?php echo ($row['loginStatus']=='0'?' checked ':'');?> >No
						</div>
						<div class="form-group">
							<input type="hidden" name="curPhoto" id="curPhoto" value="<?=$row['userPicture'];?>" />
							<input type="file" name="inputFile" accept="image/*" multiple  onchange="showMyImage(this)" /> <br/>
							<img id="thumbnil" style="width:50%; margin-top:10px;"  src="dist/img/<?php echo (empty($row['userPicture'])? 'default.jpg' : $row['userPicture']); ?>" alt="image"/>
						</div>
                        <button id="btn1" type="submit" class="btn btn-default">Submit</button>
					</div>
					<!--/.col-md-->
                    </form>
                </div>
                <!--/.row-->       
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
	$("#userFullname").focus();

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
				})
			.done(function (data) {
					if (data.success){          
						$.smkAlert({
							text: data.message,
							type: 'success',
							position:'top-center'
						});						
						setTimeout(function(){history.back();}, 2000);
					} else {
						$.smkAlert({
							text: data.message,
							type: 'danger',
						});
					}
				})
				.error(function (response) {
					  alert(response.responseText);
				});//error  ;  
				//.ajax
				e.preventDefault();
			}
			//valided
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
