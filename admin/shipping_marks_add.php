<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<?php include 'head.php'; ?>  
<div class="wrapper">
  <!-- Main Header -->
  <?php include 'header.php'; ?>  
  <?php include 'inc_helper.php'; 
  $rootPage="shipping_marks";
  ?>   
  
  <!-- Left side column. contains the logo and sidebar -->
   <?php include 'leftside.php'; ?>
   
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
	<section class="content-header">
		<h1><i class="glyphicon glyphicon-certificate"></i>
       Shipping Marks
        <small>Shipping Marks management</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?=$rootPage;?>.php"><i class="glyphicon glyphicon-list"></i>Shipping Marks List</a></li>
		<li><a href="#"><i class="glyphicon glyphicon-edit"></i>Shipping Marks</a></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
	
      <!-- Your Page Content Here -->
    <div class="box box-primary">
        <div class="box-header with-border">
			<h3 class="box-title">Add Shipping Marks</h3>        
        </div><!-- /.box-header -->
		
        <div class="box-body">
           <div class="row">
                <div class="col-md-6">
                    <form id="form1" action="#" method="post" class="form" validate>
						<?php						
							$typeCode="";
						?>
						<div class="row col-md-12">
							<div class="form-group col-md-6">
                            <label for="code">Code</label>                            
							<div class="input-group">
								<input id="code" type="text" class="form-control" name="code" value="" data-smk-msg="Require Code" required>							
							</div>
                        	</div>
						</div>
						<div class="row col-md-12">
							<div class="form-group col-md-6">
                            <label for="name">Name</label>                            
							<div class="input-group">							
								<textarea id="name" class="form-control" name="name" data-smk-msg="Require Name" required ></textarea>
							</div>
                        	</div>
						</div>
						
						<div class="row col-md-12">
							<div class="form-group col-md-6">
                            <label for="typeCode">Shipping Marks Type</label>                            							
							<select name="typeCode" class="form-control" >
								<option value="" <?php echo ($typeCode==""?'selected':''); ?> >--All--</option>
								<option value="TXT" <?php echo ($typeCode=="TXT"?'selected':''); ?> >TXT - Text</option>
								<option value="IMG" <?php echo ($typeCode=="IMG"?'selected':''); ?> >IMG - Image</option>
							</select>
							</div>
						</div>
						
						<div class="row col-md-12">
							<div class="form-group col-md-6">
                            <label for="statusCode">Status</label>
							<div class="input-group">
								<input id="statusCode" name="statusCode" type="checkbox" value="A" checked > Active
							</div>							
							</div>
						</div>
						<!--<a name="btn_submit" class="btn btn-default">Submit</a>--->
						<button type="submit" name="btn_submit" class="btn btn-default" >Submit</button>
                    
                </div>
				
				<div class="col-md-6">
					<input type="hidden" name="curPhoto" id="curPhoto" value="" />
					<input type="file" name="inputFile" accept="image/*" multiple  onchange="showMyImage(this)" /> <br/>
					<img id="thumbnil" style="width:50%; margin-top:10px;"  src="../images/product/default.jpg" alt="image"/>
				</div>
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
						
				
	$('#form1').on("submit", function(e) {
		if ($('#form1').smkValidate()) {			
			$.ajax({
				url: '<?=$rootPage;?>_add_ajax.php',
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
					} else {
						$.smkAlert({
							text: data.message,
							type: 'danger',
						});
					}
					$('#form1')[0].reset();
					$("#userFullname").focus(); 
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
</script>
  



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
