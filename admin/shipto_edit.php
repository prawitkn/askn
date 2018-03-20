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

$rootPage = 'shipto';
$tb="shipto"; 

//Check user roll.
switch($s_userGroupCode){
	case 'admin' : case 'salesAdmin' :
		break;
	default : 
		header('Location: access_denied.php');
		exit();
}

?>
  
  <!-- Left side column. contains the logo and sidebar -->
   <?php include 'leftside.php'; 
   $sql = "SELECT h.*
	, cust.name as custName 
	FROM ".$tb." h 
	INNER JOIN customer cust ON cust.id=h.custId 
	WHERE 1
	AND h.id=".$_GET['id']." 
	";
	echo $sql;
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
		<h1><i class="glyphicon glyphicon-download-alt"></i>
       Ship to Customer
        <small>Ship to Customer management</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?=$rootPage;?>.php"><i class="glyphicon glyphicon-list"></i>Ship to Customer List</a></li>
		<li><a href="#"><i class="glyphicon glyphicon-edit"></i>Ship to Customer</a></li>
      </ol>
    </section>


    <!-- Main content -->
    <section class="content">

      <!-- Your Page Content Here -->
    <div class="box box-primary">
        <div class="box-header with-border">
        <h3 class="box-title">Edit Ship to Customer : <?=$row['name'];?></h3>
        <div class="box-tools pull-right">
          <!-- Buttons, labels, and many other things can be placed here! -->
          <!-- Here is a label for example -->
         
        </div><!-- /.box-tools -->
        </div><!-- /.box-header -->
        <div class="box-body">            
            <div class="row">                
                    <form id="form1" method="post" class="form" enctype="multipart/form-data" validate>
					<input type="hidden" name="action" value="edit" />				
					<input type="hidden" name="id" value="<?=$row['id'];?>" />	
					<div class="col-md-6">
						<div class="form-group">
							<label for="custId" >Customer Name</label>
							<div class="form-group row">
								<div class="col-md-9">
									<input type="hidden" name="custId" class="form-control" value="<?=$row['custId'];?>"  />
									<input type="text" name="custName" class="form-control" value="<?=$row['custName'];?>" />
								</div>
								<div class="col-md-3">
									<a href="#" name="btn_search" class="btn btn-primary" ><i class="glyphicon glyphicon-search" ></i></a>								
								</div>
							</div>
						</div>
						
						<div class="row col-md-12">
							<div class="form-group col-md-6">
								<label for="code">Ship to Code</label>                            
								<input id="code" type="text" class="form-control col-md-6" name="code" value="<?=$row['code'];?>" data-smk-msg="Require Code"  required>							
							</div>
						</div>
						<div class="row col-md-12">
							<div class="form-group col-md-12">
							<label for="name">Ship to Name</label>                            
							<input id="name" type="text" class="form-control" name="name" value="<?=$row['name'];?>" data-smk-msg="Require Name" required>							
							</div>
						</div>
						<div class="row col-md-12">
							<div class="form-group col-md-12">
							<label for="addr1">Address</label>                            
							<input id="addr1" type="text" class="form-control" name="addr1" value="<?=$row['addr1'];?>" data-smk-msg="Require Addrss"  required>							
							<input id="addr2" type="text" class="form-control" name="addr2" value="<?=$row['addr2'];?>" data-smk-msg=""  >							
							<input id="addr3" type="text" class="form-control" name="addr3" value="<?=$row['addr3'];?>" data-smk-msg=""  >							
							</div>
						</div>
						<div class="row col-md-12">
							<div class="form-group col-md-6">
							<label for="zipcode">Zipcode</label>                            
							<input id="zipcode" type="text" class="form-control" name="zipcode" value="<?=$row['zipcode'];?>" data-smk-msg="Require Zipcode" required>							
							</div>
						</div>
						<div class="row col-md-12">
							<div class="form-group col-md-12">
							<label for="countryName">Country Name</label>                            
							<input id="countryName" type="text" class="form-control" name="countryName" value="<?=$row['countryName'];?>" data-smk-msg="Require Country Name" required>							
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
									$selected=($row['locationCode']==$optItm['code']?' selected ':'');					
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
									$selected=($row['marketCode']==$optItm['code']?' selected ':'');							
									echo '<option value="'.$optItm['code'].'" '.$selected.'>'.$optItm['code'].' : '.$optItm['name'].'</option>';
								}
								?>
							</select>
							</div>
						</div>
						<div class="row col-md-12">
							<div class="form-group col-md-6">
							<label for="contact">Contact Name</label>                            
							<input id="contact" type="text" class="form-control" name="contact" value="<?=$row['contact'];?>" data-smk-msg="Require Contact Name" required>	
							</div>
						</div>
						<div class="row col-md-12">
							<div class="form-group col-md-6">
							<label for="contactPosition">Contact Position</label>                            
							<input id="contactPosition" type="text" class="form-control" name="contactPosition"  value="<?=$row['contactPosition'];?>" >							
							</div>
						</div>
						<div class="row col-md-12">
							<div class="form-group col-md-12">
							<label for="email">Email</label>                            
							<input id="email" type="text" class="form-control" name="email"  value="<?=$row['email'];?>" >		
							</div>
						</div>
						<div class="row col-md-12">
							<div class="form-group col-md-6">
							<label for="tel">Telephone</label>                            
							<input id="tel" type="text" class="form-control" name="tel"  value="<?=$row['tel'];?>" data-smk-msg="Require Telephone" required>							
							</div>
						</div>
						<div class="row col-md-12">
							<div class="form-group col-md-6">
							<label for="fax">Fax</label>                            
							<input id="fax" type="text" class="form-control" name="fax"  value="<?=$row['fax'];?>" >							
							</div>
						</div>
						
						<div class="row col-md-12">
							<div class="form-group col-md-6">
							<label for="smId">Salesman</label>   
							<select id="smId" name="smId" class="form-control" >
								<option value="0" <?php echo ($row['smId']==0?'selected':''); ?> >--All--</option>
								<?php
								$sql = "SELECT `id`, `code`, `name` FROM salesman WHERE smType='S' AND statusCode='A' ORDER BY name ASC ";
								$stmt = $pdo->prepare($sql);
								$stmt->execute();					
								while ($optItm = $stmt->fetch()){
									$selected=($row['smId']==$optItm['id']?' selected ':'');						
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
								$sql = "SELECT `id`, `code`, `name` FROM salesman WHERE smType='A' AND statusCode='A' ORDER BY name ASC ";
								$stmt = $pdo->prepare($sql);
								$stmt->execute();					
								while ($optItm = $stmt->fetch()){
									$selected=($row['smAdmId']==$optItm['id']?' selected ':'');						
									echo '<option value="'.$optItm['id'].'" '.$selected.'>'.$optItm['code'].' : '.$optItm['name'].'</option>';
								}
								?>
							</select>
							</div>
						</div>
						
						
						<div class="row col-md-12">
							<div class="form-group col-md-12">
							<label for="statusCode">Status</label>
							<input id="statusCode" name="statusCode" type="checkbox" value="A" <?= ($row['statusCode']=='A'?' checked ':'');?> > Active						
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
                <!--/.row-->       
            </div>
			<!--.body-->    
    </div>
	<!-- /.box box-primary -->
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




<!-- Modal -->
<div id="modal_search" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Search Customer</h4>
      </div>
      <div class="modal-body">
        <div class="form-horizontal">
			<div class="form-group">	
				<label for="txt_search_word" class="control-label col-md-2">Customer Name</label>
				<div class="col-md-4">
					<input type="text" class="form-control" id="txt_search_word" />
				</div>
			</div>
		
		<table id="tbl_search" class="table">
			<thead>
				<tr bgcolor="4169E1" style="color: white; text-align: center;">
					<td>#Select</td>
					<td style="display: none;">Id</td>
					<td>Code</td>
					<td>Name</td>
					<td style="display: none;">SM ID</td>
					<td>Salesman</td>
					<td style="display: none;">Addr1</td>
					<td style="display: none;">Addr2</td>
					<td style="display: none;">Addr3</td>
					<td style="display: none;">Zipcode</td>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
		</form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
      </div>
    </div>

  </div>
</div>




  
  
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




	//SEARCH Begin
	$('a[name="btn_search"]').click(function(){
		//prev() and next() count <br/> too.	
		$txtName = $(this).closest("div").prev().find('input[type="text"]');
		//alert($btn.attr('name'));
		//curId = $btn.attr('name');
		curId = $(this).closest("div").prev().find('input[type="hidden"]').attr('name');
		curName = $(this).closest("div").prev().find('input[type="text"]').attr('name');
		//alert($txtName);
		if(!$txtName.prop('disabled')){
			$('#modal_search').modal('show');
		}
	});	
	$('#txt_search_word').keyup(function(e){ 
		if(e.keyCode == 13)
		{
			var params = {
				search_word: $('#txt_search_word').val()
			};
			if(params.search_word.length < 3){
				alert('Search word must more than 3 character.');
				return false;
			}
			/* Send the data using post and put the results in a div */
			  $.ajax({
				  url: "search_customer_ajax.php",
				  type: "post",
				  data: params,
				datatype: 'json',
				  success: function(data){
								//alert(data);
								$('#tbl_search tbody').empty();
								$.each($.parseJSON(data), function(key,value){
									$('#tbl_search tbody').append(
									'<tr>' +
										'<td>' +
										'	<div class="btn-group">' +
										'	<a href="javascript:void(0);" data-name="btn_search_checked" ' +
										'	class="btn" title="เลือก"> ' +
										'	<i class="glyphicon glyphicon-ok"></i> เลือก</a> ' +
										'	</div>' +
										'</td>' +
										'<td style="display: none;">'+ value.id +'</td>' +	//1
										'<td>'+ value.code +'</td>' +	
										'<td>'+ value.name +'</td>' +	
										'<td style="display: none;">'+ value.smId +'</td>' +
										'<td>'+ value.smName +'</td>' +
										'<td style="display: none;">'+ value.addr1 +'</td>' +
										'<td style="display: none;">'+ value.addr2 +'</td>' +
										'<td style="display: none;">'+ value.addr3 +'</td>' +
										'<td style="display: none;">'+ value.zipcode +'</td>' +
									'</tr>'
									);			
								});
							
				  }, //success
				  error:function(){
					  alert('error');
				  }   
				}); 
		}/* e.keycode=13 */	
	});
	
	$(document).on("click",'a[data-name="btn_search_checked"]',function() {		
		$('input[name='+curId+']').val($(this).closest("tr").find('td:eq(1)').text());
		$('input[name='+curName+']').val($(this).closest("tr").find('td:eq(3)').text());
		/*$('#smId').val($(this).closest("tr").find('td:eq(4)').text());
		$('#custAddr').val($(this).closest("tr").find('td:eq(6)').text()+
			$(this).closest("tr").find('td:eq(7)').text()+
			$(this).closest("tr").find('td:eq(8)').text()+
			$(this).closest("tr").find('td:eq(9)').text());*/
				
		//$('#'+curName).val($(this).closest("tr").find('td:eq(2)').text());	
		$('#modal_search').modal('hide');
	});
	//Search End
	
	
	
	
	
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
					history.back();
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

<link href="bootstrap-datepicker-custom-thai/dist/css/bootstrap-datepicker.css" rel="stylesheet" />
<script src="bootstrap-datepicker-custom-thai/dist/js/bootstrap-datepicker-custom.js"></script>
<script src="bootstrap-datepicker-custom-thai/dist/locales/bootstrap-datepicker.th.min.js" charset="UTF-8"></script>
  
<script>
	$(document).ready(function () {
		$('.datepicker').datepicker({
			todayHighlight: true,
			daysOfWeekHighlighted: "0,6",
			autoclose: true,
			format: 'dd/mm/yyyy',
			todayBtn: true,
			language: 'th',             //เปลี่ยน label ต่างของ ปฏิทิน ให้เป็น ภาษาไทย   (ต้องใช้ไฟล์ bootstrap-datepicker.th.min.js นี้ด้วย)
			thaiyear: true              //Set เป็นปี พ.ศ.
		});  //กำหนดเป็นวันปัจุบัน
		
		//กำหนดเป็น วันที่จากฐานข้อมูล		
		<?php if($row['dateOfBirth']<>"") { ?>
			var queryDate = '<?=$row['dateOfBirth'];?>',
			dateParts = queryDate.match(/(\d+)/g)
			realDate = new Date(dateParts[0], dateParts[1] - 1, dateParts[2]); 
			$('#dateOfBirth').datepicker('setDate', realDate);
		<?php }else{ ?> $('#dateOfBirth').datepicker('setDate', '0'); <?php } ?>
		//จบ กำหนดเป็น วันที่จากฐานข้อมูล
		
	});
</script>
	 
	 
</body>
</html>
