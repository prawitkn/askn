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
   $id=$_GET['id'];
   $sql = "SELECT a.`id`, a.`code`, a.`name`, a.`addr1`, a.`addr2`, a.`addr3`, a.`locationCode`, a.`marketCode`, a.`contact`, a.`contactPosition`, a.`zipcode`, a.`countryName`, a.`taxId`, a.`accNo`, a.`creditDay`, a.`creditLimit`, a.`accCond`, a.`soRemark`, a.`email`, a.`tel`, a.`fax`, a.`smId`, a.`smAdmId`, a.`statusCode`, a.`createTime`, a.`createById`
	FROM customer a
	WHERE 1
	AND a.id=".$id."
	ORDER BY a.id desc
	";
	$result = mysqli_query($link, $sql);  
	$row = mysqli_fetch_assoc($result);

	$locationCode=$row['locationCode'];
	$marketCode=$row['marketCode'];
	$smId=$row['smId'];
	$smAdmId=$row['smAdmId'];
   
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
			<h3 class="box-title">Edit Customer : <span style="color: blue;"><?=$row['name'];?> [ <?=$row['code'];?> ] </h3>           
        </div><!-- /.box-header -->
		
        <div class="box-body">
           <div class="row col-md-12">

           	<ul class="nav nav-pills">
				<li class="nav-item active"><a class="nav-link" data-toggle="pill" href="#home" ><i class="fa fa-star"></i> Customer</a></li>
				<li class="nav-item"><a class="nav-link" data-toggle="pill" href="#menu1" ><i class="fa fa-truck"></i> Ship To</a></li>
			</ul>

			<div class="tab-content">
				<div id="home" class="tab-pane fade in active">
					<form id="form1" action="customer_add_ajax.php" method="post" class="form" novalidate>
						<input type="hidden" name="action" value="edit" />

						<input type="hidden" name="id" value="<?=$id;?>" />

						<div class="row">
							<div class="form-group col-md-2">
								<label for="code">Customer Code</label>                            
								<input id="code" type="text" class="form-control col-md-6" name="code"  value="<?= $row['code']; ?>"  data-smk-msg="Require Code" required>							
							</div>
							<!--/.col-md-2-->

							<div class="form-group col-md-4">
								<label for="name">Customer Name</label>                            
								<input id="name" type="text" class="form-control" name="name"  value="<?= $row['name']; ?>"  data-smk-msg="Require Name" required>
							</div>
							<!--/.col-md-4-->

							<div class="form-group col-md-2">
								<label for="locationCode">Location Type</label>   
								<select name="locationCode" class="form-control"  data-smk-msg="Require Location Type" required >
									<?php
									$sql = "SELECT `code`, `name` FROM customer_location_type WHERE statusCode='A' ORDER BY name desc ";
									$stmt = $pdo->prepare($sql);
									$stmt->execute();					
									while ($optItm = $stmt->fetch()){
										$selected=($locationCode==$optItm['code']?'selected':'');						
										echo '<option value="'.$optItm['code'].'" '.$selected.'>'.$optItm['code'].' : '.$optItm['name'].'</option>';
									}
									?>
								</select>
							</div>

							<div class="form-group col-md-2">
								<label for="marketCode">Customer Market</label>   
								<select name="marketCode" class="form-control" >
									<option value="" <?php echo ($marketCode==""?'selected':''); ?> >--Blank--</option>
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

							<div class="form-group col-md-2">
		                        <label for="creditDay">Credit Days</label>                            
								<input id="creditDay" type="text" class="form-control" style="text-align: right;" name="creditDay" value="<?= $row['creditDay']; ?>" 
								onkeypress="return numbersOnly(this, event);" 
								onpaste="return false;"
								>							
		                	</div>
						</div>
						<!--/.row-->

						<div class="row">
							<div class="form-group col-md-5">
								<label for="addr1">Customer Address</label>                            
								<input id="addr1" type="text" class="form-control" name="addr1" value="<?= $row['addr1']; ?>" data-smk-msg="Require Addrss" required>							
								<input id="addr2" type="text" class="form-control" name="addr2" value="<?= $row['addr2']; ?>" data-smk-msg="" >							
								<input id="addr3" type="text" class="form-control" name="addr3" value="<?= $row['addr3']; ?>" data-smk-msg="" >

								<div  class="row">
									<div class="form-group col-md-6">
										<label for="zipcode">Customer Zipcode</label>                            
										<input id="zipcode" type="text" class="form-control" name="zipcode" value="<?= $row['zipcode']; ?>" data-smk-msg="Require Zipcode" required>
									</div>
									<!--/.col-->

									<div class="form-group col-md-6">
										<label for="countryName">Customer Country Name</label>                            
										<input id="countryName" type="text" class="form-control" name="countryName" value="<?= $row['countryName']; ?>" data-smk-msg="Require Country Name" required>
									</div>
									<!--/.col-->
								</div>
								<!--/.row-->

								<label for="soRemark">Customer SO Remark</label>                            
								<input id="soRemark" type="text" class="form-control" name="soRemark" value="<?=$row['soRemark'];?>" >
													
							</div>
							<!--/.col-->

							<div class="form-group col-md-5">
								<label for="contact">Customer Contact Name</label>                            
								<input id="contact" type="text" class="form-control" name="contact" value="<?= $row['contact']; ?>" data-smk-msg="Require Contact Name" required>	

								<label for="contactPosition">Contact Position</label>                            
								<input id="contactPosition" type="text" class="form-control" name="contactPosition" value="<?= $row['contactPosition']; ?>" >

								<label for="email">Customer Email</label>                            
								<input id="email" type="text" class="form-control" name="email" value="<?= $row['email']; ?>" >



								<div  class="row">
									<div class="form-group col-md-6">
										<label for="tel">Customer Telephone</label>                            
										<input id="tel" type="text" class="form-control" name="tel" value="<?= $row['tel']; ?>" data-smk-msg="Require Telephone" required>
									</div>
									<!--/.col-->

									<div class="form-group col-md-6">
										<label for="fax">Customer Fax</label>                            
										<input id="fax" type="text" class="form-control" name="fax" value="<?= $row['fax']; ?>" >		
									</div>
									<!--/.col-->
								</div>
								<!--/.row-->
							</div>
							<!--/.col-->

							<div class="form-group col-md-2">
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

								<label for="statusCode">Status</label><br/>
								<input id="statusCode" name="statusCode" type="checkbox" value="A" <?php if ($row['statusCode']=='A') echo ' checked '; ?> > Active		
							</div>
							<!--/.col-->
						</div>
						<!--/.row-->

						<div class="row">
							<div class="col-md-12">
								<input type="submit" name="btn_submit" class="btn btn-default" value="Submit" />
							</div>
						</div>
						<!--/.row-->				
						
		            </form>		
				</div>
				<!--/.tab-pand-->






				<div id="menu1" class="tab-pane fade in" >					
					<div class="row col-md-12">
						<form id="form2" action="#" method="post" class="form" novalidate>
							<input type="hidden" name="action" value="shipToSave" />
							<input type="hidden" name="custId" value="<?=$id;?>" />
							<input type="hidden" name="itmId" id="itmId" value="" />

							<div class="row">

								<div class="form-group col-md-3">
									<label for="itmName">Ship To Name</label>                            
									<input id="itmName" type="text" class="form-control" name="itmName"  value=""  data-smk-msg="Require Name" required>
								</div>
								<!--/.col-md-4-->

							</div>
							<!--/.row-->

							<div class="row">
								<div class="form-group col-md-5">
									<label for="itmAddr1">Ship To Address</label>                            
									<input id="itmAddr1" type="text" class="form-control" name="itmAddr1" value="" >							
									<input id="itmAddr2" type="text" class="form-control" name="itmAddr2" value="" >							
									<input id="itmAddr3" type="text" class="form-control" name="itmAddr3" value="" >

									<div  class="row">
										<div class="form-group col-md-6">
											<label for="itmZipcode">Ship To Zipcode</label>                            
											<input id="itmZipcode" type="text" class="form-control" name="itmZipcode" value="">
										</div>
										<!--/.col-->

										<div class="form-group col-md-6">
											<label for="itmCountryName">Ship To Country Name</label>                            
											<input id="itmCountryName" type="text" class="form-control" name="itmCountryName" value="" >
										</div>
										<!--/.col-->
									</div>
									<!--/.row-->
														
								</div>
								<!--/.col-->

								<div class="form-group col-md-5">
									<label for="itmContact">Ship To Contact Name</label>                            
									<input id="itmContact" type="text" class="form-control" name="itmContact" value="" >	

									<label for="itmContactPosition">Contact Position</label>                            
									<input id="itmContactPosition" type="text" class="form-control" name="itmContactPosition" value="" >

									<label for="itmEmail">Ship To Email</label>                            
									<input id="itmEmail" type="text" class="form-control" name="itmEmail" value="" >



									<div  class="row">
										<div class="form-group col-md-6">
											<label for="itmTel">Ship To Telephone</label>                            
											<input id="itmTel" type="text" class="form-control" name="itmTel" value="" >
										</div>
										<!--/.col-->

										<div class="form-group col-md-6">
											<label for="itmFax">Ship To Fax</label>                            
											<input id="itmFax" type="text" class="form-control" name="itmFax" value="" >		
										</div>
										<!--/.col-->
									</div>
									<!--/.row-->
								</div>
								<!--/.col-->

								<div class="form-group col-md-2">
									<label for="itmStatusCode">Ship To Status</label><br/>
									<input id="itmStatusCode" name="itmStatusCode" type="checkbox" value="A" > Active		
								</div>
								<!--/.col-->
							</div>
							<!--/.row-->

							<div class="row">
								<div class="col-md-12">
									<a href="#" name="btnItmSubmit" id="btnItmSubmit" class="btn btn-primary pull-right" ><i class="fa fa-save"></i> Save</a>  

									<a href="#" name="btnItmClear" id="btnItmClear" class="btn btn-primary pull-right" style="margin-right: 5px;" ><i class="fa fa-refresh"></i> Clear</a>  
								</div>
								</div>
							</div>
							<!--/.row-->							
						</form>

				  		<div class="col-md-12">				  			
						<span style="text-decoration: underline;">Ship To List</span>						
				  			<div class="row  col-md-12 table-responsive">
							<table id="tbl_items" class="table table-striped">
								<thead>
									<tr style="background-color: #3c8dbc;">
										<th style="text-align: center; font-weight: bold;">No.</th>																
										<th style="text-align: center; font-weight: bold;">Ship To Code</th>	
										<th style="text-align: center; font-weight: bold;">Ship To Name</th>	
										<th style="text-align: center; font-weight: bold;">Status</th>	
										<th style="text-align: center; font-weight: bold;">#</th>			
									</tr>
								</thead>
								<tbody>

								</tbody>
							</table>
							</div>
							<!--/.table-responsive-->
				  		</div>
				  		<!--col-->
					</div>
					<!--/.row-->					
				</div>
				<!--/.tab-pand-->

			</div>
			<!--/.tab-contendt-->

		</div>
		<!--/.row col-12-->
				
    </div><!-- /.box-body -->

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

	function getShipToList(){		
		var params = {
			action: 'getShipToList',
			id: "<?=$_GET['id'];?>"
		}; //alert(params.soNo);
		/* Send the data using post and put the results in a div */
		  $.ajax({
			  url: "<?=$rootPage;?>_ajax.php",
			  type: "post",
			  data: params,
			datatype: 'json',
			  success: function(data){	//alert(data);
					switch(data.rowCount){
						default : 	//alert('default');						
						//$('#tbl_items tbody').empty();
						$('#tbl_items tbody').fadeOut('slow').empty();
						$rowNo=1;
						$.each($.parseJSON(data.data), function(key,value){
							 var tmpStatusName = ""; 
							 switch(value.statusCode){
							 	case 'A' :
									tmpStatusName='<a class="btn btn-success" name="btn_row_setActive" data-statusCode="I" data-id="'+value.id+'" >Active</a>';
									break;
								case 'I' :
									tmpStatusName='<a class="btn btn-default" name="btn_row_setActive" data-statusCode="A" data-id="'+value.id+'" >Inactive</a>';
									break;
								case 'X' : 
									tmpStatusName='<label style="color: red;" >Removed</label>';
									break;
								default :	
									tmpStatusName='<label style="color: red;" >N/A</label>';
							 }
							$('#tbl_items tbody').append(
								'<tr>'+
								'<td style="text-align: center;">'+$rowNo+'</td>'+
								'<td style="text-align: left;">'+value.code+'</td>'+
								'<td style="text-align: left;">'+value.name+'</td>'+
								'<td style="text-align: center;">'+tmpStatusName+'</td>'+
								'<td><a href="#" name="btnItmEdit" class="btn btn-default" data-ref-id="'+value.id+'" ><i class="fa fa-edit"></i> edit</a>'+
								'<a href="#" name="btnItmDelete" class="btn btn-danger" data-id="'+value.id+'"  ><i class="fa fa-trush"></i> Delete</a>'+

								'</td>'+
								'</tr>');
							$rowNo+=1;
							//alert(value);
						});
						$('#tbl_items tbody').fadeIn('slow');

						$('#itmName').focus().select();							
					}//.switch
			  }   
			}).error(function (response) {
				alert(response.responseText);
			}); 
	}
	
	getShipToList();

	$("#title").focus();
	var spinner = new Spinner().spin();
	$("#spin").append(spinner.el);
	$("#spin").hide();
						
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
					} else {
						$.smkAlert({
							text: data.message,
							type: 'danger',
						});
					}
					$("#code").focus(); 
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


	$('a[name=btnItmSubmit]').click (function(e) { //alert('big2');
		//tmpProductName = $('#product').val();

		if ($('#form2').smkValidate()){
			$.post("<?=$rootPage;?>_ajax.php", $("#form2").serialize() )
				.done(function(data) {
					if (data.success){						
						$.smkAlert({
							text: data.message,
							type: data.success
							//position:'top-center'
						});
						$('#form2')[0].reset();
						$('#form2 input[name=action]').val('shipToSave');
						getShipToList();
						$('#itmName').focus();
					} else {
						//alert('a');
						$.smkAlert({
							text: data.message,
							type: 'danger',
							//position:'top-center'
						});
					}
					//$("#visitDate").focus();
				})
				.error(function (response) {
				  alert(response.responseText);
				}); 
			e.preventDefault();
		}//.smkValidate()
	});//.btn_click

	$('#tbl_items').on("click", "a[name=btnItmEdit]", function(e) {
		var params = {
			action: 'getShipTo',
			id: $(this).attr('data-ref-id')
		}; 
		$.post("<?=$rootPage;?>_ajax.php", params )
		.done(function(data) { //alert(data);
			if (data.success){   
				var itm = $.parseJSON(data.data);
				$('#custId').val(itm.custId);
				$('#itmId').val(itm.id);
				$('#itmName').val(itm.name);
				$('#itmAddr1').val(itm.addr1); 
				$('#itmAddr2').val(itm.addr2); 
				$('#itmAddr3').val(itm.addr3); 
				$('#itmContact').val(itm.contact); 
				$('#itmContactPosition').val(itm.contactPosition); 
				$('#itmZipcode').val(itm.zipcode); 
				$('#itmCountryName').val(itm.countryName); 
				$('#itmEmail').val(itm.email); 
				$('#itmTel').val(itm.tel); 
				$('#itmFax').val(itm.fax); 
				if ( itm.statusCode == 'A' ){
					$('#itmStatusCode').attr('checked','checked');
				}else{
					$('#itmStatusCode').attr('checked','');
				}								

				$('#itmName').focus().select();
			} else {
				//alert('a');
				$.smkAlert({
					text: data.message,
					type: 'danger',
					//position:'top-center'
				});
			}
			//$("#visitDate").focus();
		});
	 });

	$('a[name=btnItmClear]').click (function(e) { //alert('big2');
		$('#form2')[0].reset();
		$('#form2 input[name=action]').val('shipToSave');
		getShipToList();
		$('#itmName').focus();
	});//.btn_click

	//

	$('#tbl_items').on("click", "a[name=btnItmDelete]", function(e) {
		var params = {
			action: 'itemDelete',
			id: $(this).attr('data-id')
		};
		//alert(params.id);
		$.smkConfirm({text:'Are you sure to Delete ?',accept:'Yes', cancel:'Cancel'}, function (e){if(e){
			$.post({
				url: '<?=$rootPage;?>_ajax.php',
				data: params,
				dataType: 'json'
			}).done(function (data) {					
				if (data.success){ 
					$.smkAlert({
						text: data.message,
						type: 'success',
						position:'top-center'
					});
					getShipToList();
				} else {
					alert(data.message);
				}
			}).error(function (response) {
				alert(response.responseText);
			}); 
		}});
		e.preventDefault();
	 });

	$('#tbl_items').on("click", "a[name=btn_row_setActive]", function(e) {
		var params = {
			action: 'itemSetActive',
			id: $(this).attr('data-id'),
			statusCode: $(this).attr('data-statusCode')			
		};
		$.post({
			url: '<?=$rootPage;?>_ajax.php',
			data: params,
			dataType: 'json'
		}).done(function (data) {					
			if (data.success){ 
				getShipToList();
			} else {
				alert(data.message);
				$.smkAlert({
					text: data.message,
					type: 'danger'//,
				//                        position:'top-center'
				});
			}
		}).error(function (response) {
			alert(response.responseText);
		}); 
		e.preventDefault();
	});
	//end btn_row_setActive


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