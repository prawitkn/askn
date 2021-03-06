<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<?php include 'head.php'; ?>  

</head>
<body class="hold-transition <?=$skinColorName;?> sidebar-mini">


	

<div class="wrapper">
  <!-- Main Header -->
  <?php include 'header.php'; ?>  
  <?php 
  $rootPage="product";
  ?>      

  
  <!-- Left side column. contains the logo and sidebar -->
   <?php include 'leftside.php'; ?>
   
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
	<section class="content-header">
		<h1><i class="glyphicon glyphicon-barcode"></i>
       Product
        <small>Product management</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?=$rootPage;?>.php"><i class="glyphicon glyphicon-list"></i>Product List</a></li>
		<li class="active"><a href="#"><i class="glyphicon glyphicon-edit"></i>Product</a></li>
      </ol>
    </section>


    <!-- Main content -->
    <section class="content">
	
      <!-- Your Page Content Here -->
      <a href="product.php" class="btn btn-google">Back</a>
    <div class="box box-primary">
        <div class="box-header with-border">
			<h3 class="box-title">Edit Product</h3>           
        </div><!-- /.box-header -->
		
        <div class="box-body">
           <div class="row">
                <div class="col-md-8">
                    <form id="form1" action="#" method="post" class="form" validate>
						<input type="hidden" name="action" value="edit" />
						<?php							
							$sql = "SELECT  `id`, `code`, `catCode`, `name`, `uomCode`, `width`, `weight`, `ratioPack`, `packUomCode`
							, `sourceTypeCode`, `appCode`, `isFg`, `isWip`, `photo`, `specFile`, `description`, `statusCode`
									FROM product a
									WHERE 1
									AND a.id=".$_GET['id']."
									ORDER BY a.id desc
									";
							$result = mysqli_query($link, $sql);  
							$row = mysqli_fetch_assoc($result);
							
							$appCode=$row['appCode'];
							$sourceTypeCode=$row['sourceTypeCode'];
							$catCode=$row['catCode'];
						?>
						<input type="hidden" name="id" id="id" value="<?= $row['id']; ?>" />
						<div class="row col-md-12">
							<div class="form-group col-md-6">
                            <label for="id">Product ID</label>                            
							<div class="input-group">
								<input id="id" type="text" class="form-control" name="id" value="<?= $row['id']; ?>" data-smk-msg="Require Group" disabled required>							
							</div>
                        	</div>
						</div>
						<div class="row col-md-12">
							<div class="form-group col-md-6">
                            <label for="code">Product Code</label>                            
							<input id="code" type="text" class="form-control" name="code" value="<?= $row['code']; ?>" data-smk-msg="Require Group" required>	
                        	</div>

                        	<div class="form-group col-md-6">
                            <label for="name">Product Name</label>                            
							<input id="name" type="text" class="form-control" name="name" value="<?= $row['name']; ?>" data-smk-msg="Require Name" required>	
                        	</div>
						</div>

						<div class="row col-md-12">
							<div class="form-group col-md-4">
                            <label for="uomCode">Sales UOM</label>                            
							<input id="uomCode" type="text" class="form-control" name="uomCode" value="<?= $row['uomCode']; ?>" data-smk-msg="Require UOM" required>							
                        	</div>

                        	<div class="form-group col-md-4">
	                            <label for="width">Width (MM.)</label>                            
								<input id="width" type="text" class="form-control" name="width" value="<?= $row['width']; ?>" style="text-align: right;" data-smk-msg="Require Width" >						
                        	</div>

                        	<div class="form-group col-md-4">
	                            <label for="weight">Weight (G/M<sup>2</sup>)</label>                            
								<input id="weight" type="text" class="form-control" name="weight" value="<?= $row['weight']; ?>" style="text-align: right;" data-smk-msg="Require Weight" >						
                        	</div>
                        </div>

                        <div class="row col-md-12">
                        	<div class="form-group col-md-4">
                            <label for="catCode">Category</label>                            							
							<select name="catCode" class="form-control" >
								<option value="" <?php echo ($catCode==""?'selected':''); ?> >--All--</option>
								<?php
								$sql = "SELECT `code`, `name` FROM product_category WHERE statusCode='A' ORDER BY name ASC ";
								$stmt = $pdo->prepare($sql);
								$stmt->execute();					
								while ($optItm = $stmt->fetch()){
									$selected=($catCode==$optItm['code']?'selected':'');						
									echo '<option value="'.$optItm['code'].'" '.$selected.'>'.$optItm['code'].' : '.$optItm['name'].'</option>';
								}
								?>
							</select>
							</div>

							<div class="form-group col-md-4">
                            <label for="sourceTypeCode">Source Type Code</label>                            							
							<select name="sourceTypeCode" class="form-control" >
								<option value="" <?php echo ($sourceTypeCode==""?'selected':''); ?> >--All--</option>
								<?php
								$sql = "SELECT `code`, `name` FROM product_source_type WHERE statusCode='A' ORDER BY name ASC ";
								$stmt = $pdo->prepare($sql);
								$stmt->execute();					
								while ($optItm = $stmt->fetch()){
									$selected=($sourceTypeCode==$optItm['code']?'selected':'');						
									echo '<option value="'.$optItm['code'].'" '.$selected.'>'.$optItm['code'].' : '.$optItm['name'].'</option>';
								}
								?>
							</select>
							</div>
						</div>

						<div class="row col-md-12">
							<div class="form-group col-md-12">
                            <label for="description">Description</label>                            
							<input id="description" type="text" class="form-control" name="description" value="<?= $row['description']; ?>" data-smk-msg="Require Description" required>	
                        	</div>
						</div>


						<div class="row col-md-12">
							<div class="form-group col-md-6">
                            <label for="appCode">MKT Group</label>   
							<select name="appCode" class="form-control" >
								<option value="" <?php echo ($appCode==""?'selected':''); ?> >--All--</option>
								<?php
								$sql = "SELECT `code`, `name` FROM market WHERE statusCode='A' ORDER BY name ASC ";
								$stmt = $pdo->prepare($sql);
								$stmt->execute();					
								while ($optItm = $stmt->fetch()){
									$selected=($appCode==$optItm['code']?'selected':'');						
									echo '<option value="'.$optItm['code'].'" '.$selected.'>'.$optItm['code'].' : '.$optItm['name'].'</option>';
								}
								?>
							</select>
                        	</div>

                        	<div class="form-group col-md-6">
                            <label for="statusCode">Status</label>
							<div class="input-group">
								<input id="statusCode" name="statusCode" type="checkbox" value="A" <?php if ($row['statusCode']=='A') echo 'checked'; ?> > Active
							</div>							
							</div>
						</div>

						<!--<a name="btn_submit" class="btn btn-default">Submit</a>--->
						<button type="submit" name="btn_submit" class="btn btn-default" ><i class="fa fa-save"></i> Update Product</button>
                    
                </div>
				
				<div class="col-md-4">
					<input type="hidden" name="curPhoto" id="curPhoto" value="<?=$row['photo'];?>" />
					<input type="file" name="inputFile" accept="image/*" multiple  onchange="showMyImage(this)" /> <br/>
					<img id="thumbnil" style="width:50%; margin-top:10px;"  src="../images/product/<?php echo (empty($row['photo'])? 'default.jpg' : $row['photo']); ?>" alt="image"/>

					<br/><br/>
					ไฟล์ Product Specification : 
					<input type="hidden" name="curPdf" id="curPdf" value="<?=$row['specFile'];?>" /><br/>
					<?php if ($row['specFile']<>"") { ?>
					<a href="../pdf/product/<?=$row['specFile'];?>" target="_blank" ><i class="fa fa-file"> </i>  Specification file</a>
					<a id="btn_spec_file_delete" class="btn btn-danger" href="#" data-id="<?=$row['id'];?>" ><i class="fa fa-trash"> </i>  Delete file</a>
					<?php } ?>
					<input type="file" name="inputFile2" accept="application/pdf,application/vnd.ms-excel" /> <br/>

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
<!-- Add _.$ jquery coding -->
<!--<script src="assets/underscore-min.js"></script>-->


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
					//$('#form1')[0].reset();
					//$("#userFullname").focus(); 
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

	$('#btn_spec_file_delete').click (function(e) {	
		//alert($(this).attr('data-id'));			 
		var params = {		
		action: 'specFileDelete',
		id: $(this).attr('data-id')				
		};
		alert(params.id);
		$.smkConfirm({text:'Are you sure to delete Specification file ?', accept:'Yes', cancel:'Cancel'}, function (e){if(e){
			$.post({
				url: '<?=$rootPage;?>_ajax.php',
				data: params,
				dataType: 'json'
			}).done(function(data) {
				if (data.success){  
					alert(data.message);
					//window.location.href = '<?=$rootPage;?>.php';
					window.location.reload(); 
				}else{
					$.smkAlert({
						text: data.message,
						type: 'danger',
						position:'top-center'
					});
				}
				//e.preventDefault();		
			}).error(function (response) {
				alert(response.responseText);
			});
			//.post
		}});
		//smkConfirm
	});
	//.btn_click
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
