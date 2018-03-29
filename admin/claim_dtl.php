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

$rootPage = 'claim';
$tb="claim_hdr"; 

//Check user roll.
switch($s_userGroupCode){
	case 'admin' : case 'sales' : case 'salesAdmin' : 
		break;
	default : 
		header('Location: access_denied.php');
		exit();
}
?>
  
  <!-- Left side column. contains the logo and sidebar -->
   <?php include 'leftside.php'; 
	
	$act=$_GET['act'];
	$docNo=$_GET['docNo'];
	
	$sql = "
	SELECT h.`refNo`, h.`docNo`, h.`docDate`, h.`prodId`, h.`probId`, h.`probRem`, h.`custReqId`
	, h.`acceptId`, h.`acceptRem`, h.`acceptById`, h.`acceptTime`
	, h.`concId`, h.`concRem`, h.`concById`, h.`concTime`
	, h.`custId`, h.`shipToId`, h.`smId`
	, h.`statusCode`, h.`createTime`, h.`createById`, h.`updateTime`, h.`updateById`, h.`confirmTime`, h.`confirmById`, h.`approveTime`, h.`approveById` 
	, cust.name as custName 
	FROM ".$tb." h 
	LEFT JOIN customer cust ON cust.id=h.custId 
	WHERE 1 ";
	$sql.="AND h.docNo=:docNo ";
	//$result = mysqli_query($link, $sql);
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':docNo', $docNo);	
	$stmt->execute();	
	$hdr=$stmt->fetch();
   ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
   <!-- Content Header (Page header) -->
	<section class="content-header">	  
	  <h1><i class="glyphicon glyphicon-alert"></i>
       Claim
        <small>Claim management</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?=$rootPage;?>.php"><i class="glyphicon glyphicon-list"></i> Claim List</a></li>
		<li><a href="#"><i class="glyphicon glyphicon-edit"></i> Claim</a></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

      <!-- Your Page Content Here -->
    <div class="box box-primary">
        <div class="box-header with-border">
        <h3 class="box-title"><?php echo ($act=="add"?"Add":"Edit");?> Calim<?php echo ($act=="add"?"":" No.".$docNo);?></h3>
        <div class="box-tools pull-right">
          <!-- Buttons, labels, and many other things can be placed here! -->
          <!-- Here is a label for example -->
         
        </div><!-- /.box-tools -->
        </div><!-- /.box-header -->
        <div class="box-body">            
            <div class="row">                
                    <form id="form1" method="post" class="form" enctype="multipart/form-data" validate>
					
					<input type="hidden" name="action" value="add" />
					<div class="col-md-6">
							<div class="form-group">
								<label for="refNo" >Delivery No.</label>
								<div class="form-group row">
									<div class="col-md-12">
										<input type="hidden" name="refNo" class="form-control" value="<?=$hdr['refNo'];?>"  />
										<!--<input type="text" name="custName" class="form-control" value="<?=$hdr['custName'];?>" <?php echo ($docNo<>""?' disabled ':'');?> />-->
										<label name="doStr" ><?php echo ($hdr['refNo']==""?"_ _ _ _ _ _ _ _ _ _":$hdr['refNo']);?></label>
										<a href="#" name="btn_search" class="btn btn-primary" <?php echo ($docNo<>""?' disabled ':'');?> ><i class="glyphicon glyphicon-search" ></i></a>
									</div>
								</div>
							</div>
							
							<div class="form-group">
								<label for="docDate">Date</label>
								<input id="docDate" type="text" class="form-control datepicker" name="docDate" data-smk-msg="Require Date." required>
							</div>
							
							<div class="form-group">
								<label for="prodId" >Product Code</label>
								<div class="form-group row">
									<div class="col-md-12">
										<input type="hidden" name="prodId" class="form-control" value="<?=$hdr['prodId'];?>"  />
										<!--<input type="text" name="custName" class="form-control" value="<?=$hdr['custName'];?>" <?php echo ($docNo<>""?' disabled ':'');?> />-->
										<label name="prodIdName" ><?=$hdr['prodId'];?></label>
										<a href="#" name="btn_search2" class="btn btn-primary" <?php echo ($docNo<>""?' disabled ':'');?> ><i class="glyphicon glyphicon-search" ></i></a>
									</div>
								</div>
							</div>
							
							<div class="col-md-6 form-group">
								<label for="probId">Problem </label>
								<select id="probId" name="probId" class="form-control" >
									<option value="0"> -- Select -- </option>
									<?php
									$sql_sm = "SELECT id, `code`,  `name`,  `typeCode`, `filePath` FROM `shipping_marks` WHERE `statusCode`='A' ";
									$result_sm = mysqli_query($link, $sql_sm);
									while($row = mysqli_fetch_assoc($result_sm)){
										$selected = ($hdr['shippingMarksId']==$row['id']?' selected ':'');
										echo '<option value="'.$row['id'].'" data-typeCode="'.$row['typeCode'].'" data-filePath="'.$row['filePath'].'" '.$selected.' >'.$row['code'].' : '.$row['name'].'</option>';
									}
									?>
								</select> 
								<textarea id="probRem" name="probRem" class="form-control" ></textarea>
							</div>

							<div class="col-md-6 form-group">
								<label for="custReqId">Customer Request </label>
								<select id="custReqId" name="custReqId" class="form-control" >
									<option value="0"> -- Select -- </option>
									<?php
									$sql_sm = "SELECT id, `code`,  `name`,  `typeCode`, `filePath` FROM `shipping_marks` WHERE `statusCode`='A' ";
									$result_sm = mysqli_query($link, $sql_sm);
									while($row = mysqli_fetch_assoc($result_sm)){
										$selected = ($hdr['shippingMarksId']==$row['id']?' selected ':'');
										echo '<option value="'.$row['id'].'" data-typeCode="'.$row['typeCode'].'" data-filePath="'.$row['filePath'].'" '.$selected.' >'.$row['code'].' : '.$row['name'].'</option>';
									}
									?>
								</select> 
								<textarea id="probRem" name="probRem" class="form-control" ></textarea>
							</div>
						
					</div>
					
					<div class="col-md-6">
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
<div id="modal_search2" class="modal fade" role="dialog"> 
  <div class="modal-dialog modal-lg"> 

    <!-- Modal content-->
    <div class="modal-content"> 
      <div class="modal-header"> 
        <button type="button" class="close" data-dismiss="modal">&times;</button> 
        <h4 class="modal-title">Search Delivery No.</h4> 
      </div> 
      <div class="modal-body"> 
        <div class="form-horizontal"> 
			<div class="form-group">	
				<label for="txt_search_word" class="control-label col-md-2">Delivery No.</label>
				<div class="col-md-4"> 
					<input type="text" class="form-control" id="txt_search_word" />
				</div> 
			</div> 
		
		<table id="tbl_search" class="table">
			<thead>
				<tr bgcolor="4169E1" style="color: white; text-align: center;">
					<td>#Select</td>
					<td style="display: none;">Id</td>
					<td>DO No.</td>
					<td>Date</td>
					<td>Customer</td>
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
	$('#modal_search #txt_search_word').keyup(function(e){ 
		if(e.keyCode == 13)
		{
			var params = {
				search_word: $('#modal_search #txt_search_word').val()
			};
			if(params.search_word.length < 3){
				alert('Search word must more than 3 character.');
				return false;
			}
			/* Send the data using post and put the results in a div */
			  $.ajax({
				  url: "search_sent_delivery_ajax.php",
				  type: "post",
				  data: params,
				datatype: 'json',
				  success: function(data){
								alert(data);
								$('#modal_search #tbl_search tbody').empty();
								$.each($.parseJSON(data), function(key,value){
									$('#modal_search #tbl_search tbody').append(
									'<tr>' +
										'<td>' +
										'	<div class="btn-group">' +
										'	<a href="javascript:void(0);" data-name="btn_search_checked" ' +
										'	class="btn" title="เลือก"> ' +
										'	<i class="glyphicon glyphicon-ok"></i> เลือก</a> ' +
										'	</div>' +
										'</td>' +
										'<td style="display: none;">'+ value.doNo +'</td>' +	//1
										'<td>'+ value.doNo +'</td>' +
										'<td>'+ value.deliveryDate +'</td>' +	
										'<td>'+ value.custName +'</td>' +	
									'</tr>'
									);			
								});
							
				  }, //success
				  error:function(response) {
					alert(response.responseText);
				  }   
				}); 
		}/* e.keycode=13 */	
	});
	
	$(document).on("click",'#modal_search a[data-name="btn_search_checked"]',function() {		
		$('#modal_search input[name='+curId+']').val($(this).closest("tr").find('td:eq(1)').text());
		$('#modal_search input[name='+curName+']').val($(this).closest("tr").find('td:eq(3)').text());
		/*$('#smId').val($(this).closest("tr").find('td:eq(4)').text());
		$('#custAddr').val($(this).closest("tr").find('td:eq(6)').text()+
			$(this).closest("tr").find('td:eq(7)').text()+
			$(this).closest("tr").find('td:eq(8)').text()+
			$(this).closest("tr").find('td:eq(9)').text());*/
				
		//$('#'+curName).val($(this).closest("tr").find('td:eq(2)').text());	
		$('#modal_search').modal('hide');
	});
	//Search End
	
	
	
	
	
	
	
	
	
	//SEARCH Begin
	$('a[name="btn_search2"]').click(function(){ 
		//prev() and next() count <br/> too.	
		$txtName = $(this).closest("div").prev().find('input[type="text"]');
		//alert($btn.attr('name'));
		//curId = $btn.attr('name');
		curId = $(this).closest("div").prev().find('input[type="hidden"]').attr('name');
		curName = $(this).closest("div").prev().find('input[type="text"]').attr('name');
		//alert($txtName);
		if(!$txtName.prop('disabled')){
			$('#modal_search2').modal('show');
		}
	});	
	$('#modal_search2 #txt_search_word').keyup(function(e){ 
		if(e.keyCode == 13)
		{
			var params = {
				search_word: $('#modal_search2 #txt_search_word').val()
			};
			if(params.search_word.length < 3){
				alert('Search word must more than 3 character.');
				return false;
			}
			/* Send the data using post and put the results in a div */
			  $.ajax({
				  url: "search_sent_delivery_ajax.php",
				  type: "post",
				  data: params,
				datatype: 'json',
				  success: function(data){
								alert(data);
								$('#modal_search2 #tbl_search tbody').empty();
								$.each($.parseJSON(data), function(key,value){
									$('#modal_search2 #tbl_search tbody').append(
									'<tr>' +
										'<td>' +
										'	<div class="btn-group">' +
										'	<a href="javascript:void(0);" data-name="btn_search_checked" ' +
										'	class="btn" title="เลือก"> ' +
										'	<i class="glyphicon glyphicon-ok"></i> เลือก</a> ' +
										'	</div>' +
										'</td>' +
										'<td style="display: none;">'+ value.doNo +'</td>' +	//1
										'<td>'+ value.doNo +'</td>' +
										'<td>'+ value.deliveryDate +'</td>' +	
										'<td>'+ value.custName +'</td>' +	
									'</tr>'
									);			
								});
							
				  }, //success
				  error:function(response) {
					alert(response.responseText);
				  }   
				}); 
		}/* e.keycode=13 */	
	});
	
	$(document).on("click",'#modal_search2 a[data-name="btn_search_checked"]',function() {		
		$('#modal_search2 input[name='+curId+']').val($(this).closest("tr").find('td:eq(1)').text());
		$('#modal_search2 input[name='+curName+']').val($(this).closest("tr").find('td:eq(3)').text());
		/*$('#smId').val($(this).closest("tr").find('td:eq(4)').text());
		$('#custAddr').val($(this).closest("tr").find('td:eq(6)').text()+
			$(this).closest("tr").find('td:eq(7)').text()+
			$(this).closest("tr").find('td:eq(8)').text()+
			$(this).closest("tr").find('td:eq(9)').text());*/
				
		//$('#'+curName).val($(this).closest("tr").find('td:eq(2)').text());	
		$('#modal_search2').modal('hide');
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
				}else{
					$.smkAlert({
						text: data.message,
						type: 'danger',
						position:'top-center'
					});
				}
				$('#form1')[0].reset();
				$("#title").focus(); 
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
		$('#dateOfBirth').datepicker('setDate', '0');
		//จบ กำหนดเป็น วันที่จากฐานข้อมูล
		
	});
</script>

	 
	 
</body>
</html>
