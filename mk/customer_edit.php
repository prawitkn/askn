<?php include 'inc_helper.php';  ?>
<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<?php 
include 'head.php'; 
?>     

<div class="wrapper">
  <!-- Main Header -->
  <?php include 'header.php';
	$rootPage="customer";
  ?>  
  
  <!-- Left side column. contains the logo and sidebar -->
   <?php include 'leftside.php'; 
   
   $sql = "SELECT a.*
	FROM customer a
	WHERE 1
	AND a.id=".$_GET['id']."
	ORDER BY a.id desc
	";
	$result = mysqli_query($link, $sql);  
	$row = mysqli_fetch_assoc($result);

	$locationCode=$row['locationCode'];
	$marketCode=$row['marketCode'];
   
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
		<li class="active">Edit Customer</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
	
      <!-- Your Page Content Here -->
    <div class="box box-primary">
        <div class="box-header with-border">
			<h3 class="box-title">Edit Customer</h3>           
        </div><!-- /.box-header -->
		
        <div class="box-body">
           <div class="row">
				<form id="form1" action="#" method="post" class="form" novalidate>
                <div class="col-md-6">				
						<input type="hidden" name="id" id="id" value="<?= $row['id']; ?>" />
						<div class="row col-md-12">
							<div class="form-group col-md-6">
								<label for="code">Code</label>                            
								<input id="code" type="text" class="form-control col-md-6" name="code" value="<?= $row['code']; ?>" data-smk-msg="Require Group"  required>							
							</div>
						</div>
						<div class="row col-md-12">
							<div class="form-group col-md-12">
                            <label for="name">Name</label>                            
							<input id="name" type="text" class="form-control" name="name" value="<?= $row['name']; ?>" data-smk-msg="Require Name" required>							
                        	</div>
						</div>
						<div class="row col-md-12">
							<div class="form-group col-md-12">
                            <label for="addr1">Address</label>                            
							<input id="addr1" type="text" class="form-control" name="addr1" value="<?= $row['addr1']; ?>" data-smk-msg="Require Addrss" required>							
							<input id="addr2" type="text" class="form-control" name="addr2" value="<?= $row['addr2']; ?>" data-smk-msg="" >							
							<input id="addr3" type="text" class="form-control" name="addr3" value="<?= $row['addr3']; ?>" data-smk-msg="" >							
                        	</div>
						</div>
						<div class="row col-md-12">
							<div class="form-group col-md-6">
                            <label for="zipcode">Zipcode</label>                            
							<input id="zipcode" type="text" class="form-control" name="zipcode" value="<?= $row['zipcode']; ?>" data-smk-msg="Require Zipcode" required>							
                        	</div>
						</div>
						<div class="row col-md-12">
							<div class="form-group col-md-12">
                            <label for="countryName">Country Name</label>                            
							<input id="countryName" type="text" class="form-control" name="countryName" value="<?= $row['countryName']; ?>" data-smk-msg="Require Country Name" required>							
                        	</div>
						</div>
						<div class="row col-md-12">
							<div class="form-group col-md-12">
                            <label for="creditDay">Credit Days</label>                            
							<input id="creditDay" type="text" class="form-control" name="creditDay" value="<?= $row['creditDay']; ?>" 
							onkeypress="return numbersOnly(this, event);" 
							onpaste="return false;"
							>						
                        	</div>
						</div>
						
						
						
                    
                </div>
				
				<div class="col-md-6">
						<div class="row col-md-12">
							<div class="form-group col-md-6">
							<label for="addr1">Location Type</label>   
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
							<label for="addr1">App ID</label>   
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
							<input id="contact" type="text" class="form-control" name="contact" value="<?= $row['contact']; ?>" data-smk-msg="Require Contact Name" required>	
						</div>
						<div class="row col-md-12">
							<div class="form-group col-md-6">
							<label for="contactPosition">Contact Position</label>                            
							<input id="contactPosition" type="text" class="form-control" name="contactPosition" value="<?= $row['contactPosition']; ?>" >							
							</div>
						</div>
						<div class="row col-md-12">
							<div class="form-group col-md-12">
							<label for="email">Email</label>                            
							<input id="email" type="text" class="form-control" name="email" value="<?= $row['email']; ?>" >		
							</div>
						</div>
						<div class="row col-md-12">
							<div class="form-group col-md-6">
							<label for="tel">Telephone</label>                            
							<input id="tel" type="text" class="form-control" name="tel" value="<?= $row['tel']; ?>" data-smk-msg="Require Telephone" required>							
							</div>
						</div>
						<div class="row col-md-12">
							<div class="form-group col-md-6">
							<label for="fax">Fax</label>                            
							<input id="fax" type="text" class="form-control" name="fax" value="<?= $row['fax']; ?>" >							
							</div>
						</div>
						
						<div class="row col-md-12">
							<div class="form-group col-md-6">
							<label for="smId">Salesman</label>   
							<select id="smId" name="smId" class="form-control" >
								<option value="0" <?php echo ($row['smId']==0?'selected':''); ?> >--All--</option>
								<?php
								$sql = "SELECT `id`, `code`, `name` FROM salesman WHERE statusCode='A' ORDER BY name ASC ";
								$stmt = $pdo->prepare($sql);
								$stmt->execute();					
								while ($optItm = $stmt->fetch()){
									$selected=($row['smId']==$optItm['id']?'selected':'');						
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
								<option value="0" <?php echo ($row['smAdmId']==0?'selected':''); ?> >--All--</option>
								<?php
								$sql = "SELECT `id`, `code`, `name` FROM salesman WHERE statusCode='A' ORDER BY name ASC ";
								$stmt = $pdo->prepare($sql);
								$stmt->execute();					
								while ($optItm = $stmt->fetch()){
									$selected=($row['smAdmId']==$optItm['id']?'selected':'');						
									echo '<option value="'.$optItm['id'].'" '.$selected.'>'.$optItm['code'].' : '.$optItm['name'].'</option>';
								}
								?>
							</select>
							</div>
						</div>
						
						
						<div class="row col-md-12">
							<div class="form-group col-md-12">
							<label for="statusCode">Status</label>
							<input id="statusCode" name="statusCode" type="checkbox" value="A" <?php if ($row['statusCode']=='A') echo ' checked '; ?> > Active						
							</div>
						</div>
						
						<div class="row col-md-12">
							<div class="form-group col-md-6">
                            <label for="statusCode"></label>
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
			$.post("customer_edit_ajax.php", $("#form1").serialize() )
			
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
				$('#form1').smkClear();
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
  

















<!-- search modal dialog box. -->
<script>
	var cur_hid_mid_id = "";
	var cur_txt_fullname_id = "";
	var cur_txt_mobile_no_id = "";
	var cur_txt_position_act_name_id = "";	
	var cur_txt_origin_gen_no_id = "";
	$(document).ready(function(){
		$('.fullname').click(function(){
			//.prev() and .next() count <br/> too.
			cur_hid_mid_id = $(this).prev().attr('id');			
			cur_txt_fullname_id = $(this).attr('id');			
			cur_txt_mobile_no_id = 'mobile_no';	
			cur_txt_position_act_name_id = 'position_act_name';
			cur_txt_origin_gen_no_id = 'origin_gen_no';
			//show modal.
			$('#modal_search_person').modal('show');
		});	
		
		$('#modal_search_person').on('shown.bs.modal', function () {
			$('#txt_search_fullname').focus();
		});
		$(document).on("click",'a[data-name="search_person_btn_checked"]',function() {
			$('#'+cur_hid_mid_id).val($(this).attr('attr-id'));
			$('#'+cur_txt_fullname_id).val($(this).closest("tr").find('td.search_td_fullname').text());
			$('#'+cur_txt_mobile_no_id).val($(this).closest('tr').find('td.search_td_mobile_no').text());
			$('#'+cur_txt_position_act_name_id).val($(this).closest('tr').find('td.search_td_position_act_name').text());
			$('#'+cur_txt_origin_gen_no_id).val($(this).closest('tr').find('td.search_td_origin_gen_no').text());
			//hide modal.
			$('#modal_search_person').modal('hide');
		});
		$('#txt_search_fullname').keyup(function(e){
			if(e.keyCode == 13)
			{
				var params = {
					search_org_code: '',
                    search_fullname: $('#txt_search_fullname').val()					
                };
				if(params.search_fullname.length < 3){
					alert('search name surname must more than 3 character.');
					return false;
				}
				/* Send the data using post and put the results in a div */
				  $.ajax({
					  url: "search_person_by_org_code_and_fullname_ajax.php",
					  type: "post",
					  data: params,
					datatype: 'json',
					  success: function(data){	
								if(data.success){
									console.log(data);
									console.log(data.rows);
									//alert(data);
									_.each(data.rows, function(v){										
										$('#tbl_search_person_main tbody').append(										
											'<tr>' +
												'<td>' +
												'	<div class="btn-group">' +
												'	<a href="javascript:void(0);" data-name="search_person_btn_checked" ' +
												'   attr-id="'+v['id']+'" '+
												'	class="btn" title="เลือก"> ' +
												'	<i class="glyphicon glyphicon-ok"></i> เลือก</a> ' +
												'	</div>' +
												'</td>' +
												'<td class="search_td_fullname">'+ v['fullname'] +'</td>' +
												'<td class="search_td_mobile_no">'+ v['mobile_no'] +'</td>' +	
												'<td class="search_td_origin_gen_no">'+ v['origin_gen_no'] +'</td>' +	
												'<td class="search_td_position_act_name">'+ v['position_act_name'] +'</td>' +																							
											'</tr>'
										);			
									}); 
								}else{
									alert('data.success = '+data.success);
								}
								
								
					  }, //success
					  error:function(response){
						  alert('error');
						  alert(response.responseText);
					  }		  
					}); 
			}/* e.keycode=13 */	
		});
	});	
</script>
<!-- search modal dialog box. END -->


	
	
</body>
</html>


<!--Integers (non-negative)-->
<script>
  function numbersOnly(oToCheckField, oKeyEvent) {
    return oKeyEvent.charCode === 0 ||
        /\d/.test(String.fromCharCode(oKeyEvent.charCode));
  }
</script>