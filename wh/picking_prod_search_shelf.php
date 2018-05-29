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
  <?php include 'header.php'; ?>  
  
  <!-- Left side column. contains the logo and sidebar -->
   <?php include 'leftside.php'; 
   $rootPage="picking_prod_search_shelf";
   ?>
   
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      
	  <h1><i class="glyphicon glyphicon-shopping-cart"></i>
       Product Stock Info
        <small>Picking management</small>
      </h1>
      <ol class="breadcrumb">
		<li><a href="#"><i class="glyphicon glyphicon-list"></i>Product Stock Info</a></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
	
      <!-- Your Page Content Here -->
	  
	
	
	<!-- Main row -->
      <div class="row">
		<div class="col-md-12">
			
			<!-- TABLE: LATEST ORDERS -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Available Item Stock</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
				<form id="form1" action="<?=$rootPage;?>.php" method="get" class="form" novalidate>
					<div class="row">
						<div class="col-md-6">					
							<label for="prodId" >Product Code</label>
							<div class="form-group row">
								<div class="col-md-9">
									<input type="hidden" name="prodId" value="" />
									<input type="text" name="prodCode" class="form-control" value=""  />
								</div>
								<div class="col-md-3">
									<a href="#" name="btnSdNo" class="btn btn-primary"  ><i class="glyphicon glyphicon-search" ></i></a>								
								</div>
							</div>   
							<button id="btn1" type="submit" class="btn btn-default">Submit</button>
						</div>
						<!--/.col-md-->
						<div class="col-md-6">
						</div>
					</div><!--row-->
			<?php
				$id=(isset($_GET['prodId'])?$_GET['prodId']:0);
				$sql = "
				SELECT itm.`prodCodeId`, itm.`issueDate`, itm.`grade`, itm.`qty`
				, count(*) as packQty, IFNULL(SUM(itm.`qty`),0) as total				
				,prd.id as prodId, prd.code as prodCode
				FROM `receive` hdr 
				INNER JOIN receive_detail dtl on dtl.rcNo=hdr.rcNo  
				INNER JOIN product_item itm ON itm.prodItemId=dtl.prodItemId 
				INNER JOIN product prd ON prd.id=itm.prodCodeId 
				WHERE 1=1
				AND hdr.statusCode='P' 	
				AND dtl.statusCode='A' ";
				if(isset($_GET['prodId'])){
					$sql .= "AND itm.prodCodeId=:id ";
				}
				
				$sql .= "GROUP BY itm.`prodCodeId`, itm.`issueDate`, itm.`grade`, prd.code , itm.`qty`
								
				ORDER BY itm.`issueDate` ASC  
				"; //echo $sql;
				//$result = mysqli_query($link, $sql);
				$stmt = $pdo->prepare($sql);
				if(isset($_GET['prodId'])){
					$stmt->bindParam(':id', $id);
				}
				$stmt->execute();	
					
				?>			
              <div class="table-responsive">
                <table class="table no-margin">
                  <thead>
                  <tr>
					<th>No.</th>
                    <th>Product Code</th>
					<th>issue Date</th>
					<th>Grade</th>
					<th>Per Pack</th>
                    <th style="color: blue;">Pack</th>
					<th style="color: blue;">Qty Total</th>	
					<th>#</th>
                  </tr>
                  </thead>
                  <tbody>
				  <?php $row_no = 1; while ($row = $stmt->fetch()) { 
				  $gradeName = '<b style="color: red;">N/A</b>'; 
					switch($row['grade']){
						case 0 : $gradeName = 'A'; break;
						case 1 : $gradeName = '<b style="color: red;">B</b>'; $sumGradeNotOk+=1; break;
						case 2 : $gradeName = '<b style="color: red;">N</b>'; $sumGradeNotOk+=1; break;
						default : 
							$gradeName = '<b style="color: red;">N/a</b>'; $sumGradeNotOk+=1;
					}
				?>
                  <tr>
					<td><?= $row_no; ?></td>
					<td><?= $row['prodCode']; ?></td>					
					<td><?= $row['issueDate']; ?></td>
					<td><?= $gradeName; ?></td>
					<td><?= $row['qty']; ?></td>
					<td style="color: blue;"><?= $row['packQty']; ?></td>
					<td style="color: blue;"><?= $row['total']; ?></td>		
					
					<td><a href="<?=$rootPage;?>_dtl.php?id=<?=$row['prodId'];?>&issueDate=<?=$row['issueDate'];?>&grade<?=$row['grade'];?>" 
						class="btn btn-default" ><i class="glyphicon glyphicon-search"></i> Shelf View</a></td>
					
                </tr>
                <?php $row_no+=1; } ?>
                  </tbody>
                </table>
              </div>
              <!-- /.table-responsive -->
            </div>
            <!-- /.box-body -->
            <div class="box-footer clearfix">
				
            </div>
            <!-- /.box-footer -->
			</form>
			<!--form-->
          </div>
          <!-- /.box -->
		  
		  </div>
		  <!-- col-md-12 -->
		  
      </div>
      <!-- /.row  -->
	   	  
	<div id="spin"></div>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Main Footer -->
  <?php include'footer.php'; ?>  
  
  
  
  
  
  <!-- Modal -->
<div id="modal_search_person" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Search Product</h4>
      </div>
      <div class="modal-body">
        <div class="form-horizontal">
			<div class="form-group">	
				<label for="year_month" class="control-label col-md-2">Product Code</label>
				<div class="col-md-4">
					<input type="text" class="form-control" id="txt_search_fullname" />
				</div>
			</div>
		
		<table id="tbl_search_person_main" class="table">
			<thead>
				<tr bgcolor="4169E1" style="color: white; text-align: center;">
					<td>#Select</td>
					<td style="display: none;">Id</td>
					<td>Code</td>
					<td>Category</td>
					<td>Name</td>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
		</form>
		<div id="div_search_person_result">
		</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
      </div>
    </div>

  </div>
</div>





</div>
<!-- ./wrapper -->
</body>

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
	var spinner = new Spinner().spin();
	$("#spin").append(spinner.el);
	$("#spin").hide();
				
				
				
				
				
	
	//SEARCH Begin
	$('a[name="btnSdNo"]').click(function(){
		//prev() and next() count <br/> too.		
		$txtName = $(this).closest("div").prev().find('input[type="text"]');
		//alert($btn.attr('name'));
		//curId = $btn.attr('name');
		curId = $(this).closest("div").prev().find('input[type="hidden"]').attr('name');
		curName = $(this).closest("div").prev().find('input[type="text"]').attr('name');
		//alert($txtName);
		//alert(curId);
		//alert(curName);
		if(!$txtName.prop('disabled')){
			$('#modal_search_person').modal('show');
		}
		
		//alert(curHidMid+' '+curSlOrgCode+' '+curTxtFullName+' ' +curTxtMobilePhoneNo);
		
	});	
	$('#txt_search_fullname').keyup(function(e){
		if(e.keyCode == 13)
		{
			var params = {
				search_fullname: $('#txt_search_fullname').val()
			};
			if(params.search_fullname.length < 3){
				alert('search word must more than 3 character.');
				return false;
			}
			/* Send the data using post and put the results in a div */
			  $.ajax({
				  url: "search_production_mapping_ajax.php",
				  type: "post",
				  data: params,
				datatype: 'json',
				  success: function(data){
								//alert(data);
								$('#tbl_search_person_main tbody').empty();
								$.each($.parseJSON(data), function(key,value){
									$('#tbl_search_person_main tbody').append(
									'<tr>' +
										'<td>' +
										'	<div class="btn-group">' +
										'	<a href="javascript:void(0);" data-name="search_person_btn_checked" ' +
										'	class="btn" title="เลือก"> ' +
										'	<i class="glyphicon glyphicon-ok"></i> เลือก</a> ' +
										'	</div>' +
										'</td>' +
										'<td style="display: none;">'+ value.prodId +'</td>' +
										'<td>'+ value.prodCode +'</td>' +
										'<td>'+ value.prodCatCode +'</td>' +
										'<td>'+ value.prodName +'</td>' +
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
	
	$(document).on("click",'a[data-name="search_person_btn_checked"]',function() {
		$('input[name='+curId+']').val($(this).closest("tr").find('td:eq(1)').text());
		$('input[name='+curName+']').val($(this).closest("tr").find('td:eq(2)').text());
			
		$('#modal_search_person').modal('hide');
	});
	//Search End
	
	
	
	
	
	
	function post_data(params){
		if ($('#form1').smkValidate()){
			//$.smkConfirm({text:'Are you sure to Submit ?',accept:'Yes', cancel:'Cancel'}, function (e){if(e){
				$.post({
					url: 'picking_add_item_search_row_submit_ajax.php',
					data: params,
					dataType: 'json'
				}).done(function(data) {
					if (data.success){  
						$.smkAlert({
							text: data.message,
							type: 'success',
							position:'top-center'
						});
						
						window.location.href = "picking_add.php?pickNo=" + params.pickNo;
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
			//}});
			//smkConfirm
		e.preventDefault();
		}//.if end
	}
		
});
</script>

</html>
