<?php include 'inc_helper.php'; ?>     
<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<?php include 'head.php'; ?>  
 

<div class="wrapper">
  <!-- Main Header -->
  <?php include 'header.php'; 
  $rootPage="customer";
  ?>  
  
  <!-- Left side column. contains the logo and sidebar -->
   <?php include 'leftside.php'; 
	$smId=0;
	$smAdmId=0;
	$locationCode="";
	$marketCode="";
   
   ?>
   
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
	<section class="content-header">	  
	<h1><i class="glyphicon glyphicon-user"></i>
       Customer
        <small>Customer management</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?=$rootPage;?>.php"><i class="glyphicon glyphicon-list"></i>Customer List</a></li>
		<li class="active">Customer</li>
      </ol>
    </section>
	
	

    <!-- Main content -->
    <section class="content">
	
      <!-- Your Page Content Here -->
    <div class="box box-primary">
        <div class="box-header with-border">
			<h3 class="box-title">Add Customer</h3>        
        </div><!-- /.box-header -->
		
        <div class="box-body">
           <div class="row col-md-12">
				<form id="form1" action="customer_add_ajax.php" method="post" class="form" novalidate>
                        <div class="col-md-6">				
							<div class="row col-md-12">
								<div class="form-group col-md-6">
									<label for="code">Code</label>                            
									<input id="code" type="text" class="form-control col-md-6" name="code" value="" data-smk-msg="Require Code" required>							
								</div>
							</div>
							<div class="row col-md-12">
								<div class="form-group col-md-12">
								<label for="name">Name</label>                            
								<input id="name" type="text" class="form-control" name="name" value="" data-smk-msg="Require Name" required>							
								</div>
							</div>
							<div class="row col-md-12">
								<div class="form-group col-md-12">
								<label for="addr1">Address</label>                            
								<input id="addr1" type="text" class="form-control" name="addr1" value="" data-smk-msg="Require Addrss" required>							
								<input id="addr2" type="text" class="form-control" name="addr2" value="" data-smk-msg="" >							
								<input id="addr3" type="text" class="form-control" name="addr3" value="" data-smk-msg="" >							
								</div>
							</div>
							<div class="row col-md-12">
								<div class="form-group col-md-6">
								<label for="zipcode">Zipcode</label>                            
								<input id="zipcode" type="text" class="form-control" name="zipcode" value="" data-smk-msg="Require Zipcode" required>							
								</div>
							</div>
							<div class="row col-md-12">
								<div class="form-group col-md-12">
								<label for="countryName">Country Name</label>                            
								<input id="countryName" type="text" class="form-control" name="countryName" value="" data-smk-msg="Require Country Name" required>							
								</div>
							</div>
							
							
							
						
					</div>
					
					<div class="col-md-6">
							<div class="row col-md-12">
								<div class="form-group col-md-6">
								<label for="locationCode">Location Type</label>   
								<select name="locationCode" class="form-control"  data-smk-msg="Require Location Type" required >
									<option value="" <?php echo ($locationCode==""?'selected':''); ?> >--All--</option>
									<?php
									$sql = "SELECT `code`, `name` FROM customer_location_type WHERE statusCode='A' ORDER BY name ASC ";
									$stmt = $pdo->prepare($sql);
									$stmt->execute();					
									while ($optItm = $stmt->fetch()){
										$selected=($locationCode==$optItm['code']?'selected':'');						
										echo '<option value="'.$optItm['code'].'" '.$selected.'>'.$optItm['code'].' : '.$optItm['name'].'</option>';
									}
									?>
								</select>
								</div>
							</div>
							<div class="row col-md-12">
								<div class="form-group col-md-6">
								<label for="marketCode">App ID</label>   
								<select name="marketCode" class="form-control" >
									<option value="" <?php echo ($marketCode==""?'selected':''); ?> >--All--</option>
									<?php
									$sql = "SELECT `code`, `name` FROM market WHERE statusCode='A' ORDER BY name ASC ";
									$stmt = $pdo->prepare($sql);
									$stmt->execute();					
									while ($optItm = $stmt->fetch()){
										$selected=($marketCode==$optItm['code']?'selected':'');						
										echo '<option value="'.$optItm['code'].'" '.$selected.'>'.$optItm['code'].' : '.$optItm['name'].'</option>';
									}
									?>
								</select>
								</div>
							</div>
							<div class="row col-md-12">
								<div class="form-group col-md-6">
								<label for="contact">Contact Name</label>                            
								<input id="contact" type="text" class="form-control" name="contact" value="" data-smk-msg="Require Contact Name" required>	
								</div>
							</div>
							<div class="row col-md-12">
								<div class="form-group col-md-6">
								<label for="contactPosition">Contact Position</label>                            
								<input id="contactPosition" type="text" class="form-control" name="contactPosition" value="" >							
								</div>
							</div>
							<div class="row col-md-12">
								<div class="form-group col-md-12">
								<label for="email">Email</label>                            
								<input id="email" type="text" class="form-control" name="email" value="" >		
								</div>
							</div>
							<div class="row col-md-12">
								<div class="form-group col-md-6">
								<label for="tel">Telephone</label>                            
								<input id="tel" type="text" class="form-control" name="tel" value="" data-smk-msg="Require Telephone" required>							
								</div>
							</div>
							<div class="row col-md-12">
								<div class="form-group col-md-6">
								<label for="fax">Fax</label>                            
								<input id="fax" type="text" class="form-control" name="fax" value="" >							
								</div>
							</div>
							
							<div class="row col-md-12">
								<div class="form-group col-md-6">
								<label for="smId">Salesman</label>   
								<select id="smId" name="smId" class="form-control" >
									<option value="0" <?php echo ($smId==0?'selected':''); ?> >--All--</option>
									<?php
									$sql = "SELECT `id`, `code`, `name` FROM salesman WHERE statusCode='A' ORDER BY name ASC ";
									$stmt = $pdo->prepare($sql);
									$stmt->execute();					
									while ($optItm = $stmt->fetch()){
										$selected=($smId==$optItm['id']?'selected':'');						
										echo '<option value="'.$optItm['id'].'" '.$selected.'>'.$optItm['code'].' : '.$optItm['name'].'</option>';
									}
									?>
								</select>
								</div>
							</div>
							
							<div class="row col-md-12">
								<div class="form-group col-md-6">
								<label for="smAdmId">Sales Admin</label>   
								<select id="smAdmId" name="smAdmId" class="form-control" >
									<option value="0" <?php echo ($smAdmId==0?'selected':''); ?> >--All--</option>
									<?php
									$sql = "SELECT `id`, `code`, `name` FROM salesman WHERE statusCode='A' ORDER BY name ASC ";
									$stmt = $pdo->prepare($sql);
									$stmt->execute();					
									while ($optItm = $stmt->fetch()){
										$selected=($smAdmId==$optItm['id']?'selected':'');						
										echo '<option value="'.$optItm['id'].'" '.$selected.'>'.$optItm['code'].' : '.$optItm['name'].'</option>';
									}
									?>
								</select>
								</div>
							</div>
							
							
							<div class="row col-md-12">
								<div class="form-group col-md-12">
								<label for="statusCode">Status</label>
								<input id="statusCode" name="statusCode" type="checkbox" value="A" checked > Active						
								</div>
							</div>
							
							<div class="row col-md-12">
								<div class="form-group col-md-6">
								<label for="submit"></label>
								<input type="submit" name="btn_submit" class="btn btn-default" value="Submit" />
								</div>
							</div>
					</div>
                    </form>
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
	
	$('#form1').on("submit",function(e) {
		if($('#form1').smkValidate()) {    
			alert('valid ok');
			$.post("customer_add_ajax.php", $("#form1").serialize() )
			.done(function(data) {
				if (data.success) {         
					//$.smkAlert({text: data.message, type: data.status});
					//$('#form1').smkClear();
					//$("#userName").focus();
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
				//$('#form1').smkClear();
			})//done
			.error(function (response) {
				  alert(response.responseText);
			});//error      
			e.preventDefault();               
		}            
		e.preventDefault();
	});	
						
				
			
	});
</script>
  







	
	
</body>
</html>
