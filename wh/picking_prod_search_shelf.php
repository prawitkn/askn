<?php include 'inc_helper.php'; ?>
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
			<?php
				$prodId=(isset($_GET['prodId'])?$_GET['prodId']:'');
	
				$prodCode="";
				if($prodId<>""){
					$sql = "SELECT code FROM product WHERE id=:id ";			
					$stmt = $pdo->prepare($sql);
					$stmt->bindParam(':id', $prodId);			
					$stmt->execute();
					$row=$stmt->fetch();
					$prodCode=$row['code'];
				}
			?>
            <div class="box-body">
				<div class="row">
					<div class="col-md-12">		
					<form id="form1" action="#" method="get" class="form-inline" style="background-color: gray; padding: 5px;" novalidate>
					
									
							
							<label for="prodCode">Product Code</label> 
							<input type="hidden" name="prodId" id="prodId" class="form-control" value="<?=$prodId;?>"  />
							<input type="text" name="prodCode" id="prodCode" class="form-control" value="<?=$prodCode;?>"  />
							<a href="#" name="btnSdNo" class="btn btn-default" ><i class="glyphicon glyphicon-search" ></i> </a>	
														
						
						<!--/.col-md-->
						<br/><br/>
					</form>
					<!--form-->
					
					<a name="btnSubmit" id="btnSubmit" href="#" class="btn btn-primary"><i class="glyphicon glyphicon-search"></i> Search</a>
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
				AND hdr.toCode IN ('8','E') 
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
					<th>MFD.</th>
					<th>Grade</th>
					<th>Meter</th>
                    <th style="color: blue;">Qty</th>
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
					
					<td><a href="<?=$rootPage;?>_dtl.php?prodId=<?=$row['prodId'];?>&issueDate=<?=$row['issueDate'];?>&grade<?=$row['grade'];?>" 
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
					<input type="text" class="form-control" id="txt_search_word" />
				</div>
			</div>
		
		<table id="tbl_search_person_main" class="table">
			<thead>
				<tr bgcolor="4169E1" style="color: white; text-align: center;">
					<td><i class="glyphicon glyphicon-check"></i> Select</td>
					<td style="display: none;">ID</td>
					<td>Product Code.</td>
					<td>Product Name</td>
					<td style="display: none;">UOM</td>
					<td>Product Category</td>
					<td>App ID</td>
					<td style="display: none;">Balance</td>
					<td style="display: none;">Sales</td>
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
		curName = $(this).prev().attr('name');
		curId = $(this).prev().prev().attr('name');
		if(!$('#'+curName).prop('disabled')){
			$('#modal_search_person').modal('show');
		}
	});	
	$('#txt_search_word').keyup(function(e){ 
		if(e.keyCode == 13)
		{
			var params = {
				search_word: $('#txt_search_word').val()
			};
			if(params.search_word.length < 3){
				alert('search word must more than 3 character.');
				return false;
			}
			/* Send the data using post and put the results in a div */
			  $.ajax({
				  url: "search_product_ajax.php",
				  type: "post",
				  data: params,
				datatype: 'json',
				  success: function(data){	
						data=$.parseJSON(data);
						switch(data.rowCount){
							case 0 : alert('Data not found.');
								return false; break;
							default : 
								$('#tbl_search_person_main tbody').empty();
								$.each($.parseJSON(data.data), function(key,value){
									$('#tbl_search_person_main tbody').append(
									'<tr>' +
										'<td>' +
										'	<div class="btn-group">' +
										'	<a href="javascript:void(0);" data-name="search_person_btn_checked" ' +
										'	class="btn" title="เลือก"> ' +
										'	<i class="glyphicon glyphicon-unchecked"></i></a> ' +
										'	</div>' +
										'</td>' + 
										'<td style="display: none;">'+ value.prodId +'</td>' +
										'<td>'+ value.prodCode +'</td>' +
										'<td>'+ value.prodName +'</td>' +
										'<td style="display: none;">'+ value.prodUomCode +'</td>' +
										'<td>'+ value.prodCatName +'</td>' +
										'<td>'+ value.prodAppName+'</td>' +									
										'<td style="display: none;">'+ value.balance+'</td>' +	
										'<td style="display: none;">'+ value.sales+'</td>' +	
									'</tr>'
									);		
								});
								$('#modal_search_person').modal('show');	
						}	
						
								
							
				  }   
				}).error(function (response) {
					alert(response.responseText);
				});  
		}/* e.keycode=13 */	
	});
	
	$(document).on("click",'a[data-name="search_person_btn_checked"]',function() {
		$('input[name='+curId+']').val($(this).closest("tr").find('td:eq(1)').text());
		$('input[name='+curName+']').val($(this).closest("tr").find('td:eq(2)').text());
			
		$('#modal_search_person').modal('hide');
	});
	//Search End
	
	
	$('#prodCode').keyup(function(e){ 
		if(e.keyCode == 13)
		{
			var params = {
				search_word: $('#prodCode').val()
			};
			if(params.search_word.length < 3){
				alert('search word must more than 3 character.');
				return false;
			}
			curName = $(this).attr('name');
			curId = $(this).prev().attr('name');
			/* Send the data using post and put the results in a div */
			  $.ajax({
				  url: "search_product_ajax.php",
				  type: "post",
				  data: params,
				datatype: 'json',
				  success: function(data){	
						data=$.parseJSON(data);
						switch(data.rowCount){
							case 0 : alert('Data not found.');
								$('#tbl_items tbody').empty();
								return false; break;
							case 1 :
								$.each($.parseJSON(data.data), function(key,value){
									$('input[name='+curName+']').val(value.prodCode);
									$('input[name='+curId+']').val(value.prodId);
								});
								break;
							default : 
								$('#tbl_search_person_main tbody').empty();
								$.each($.parseJSON(data.data), function(key,value){
									$('#tbl_search_person_main tbody').append(
									'<tr>' +
										'<td>' +
										'	<div class="btn-group">' +
										'	<a href="javascript:void(0);" data-name="search_person_btn_checked" ' +
										'	class="btn" title="เลือก"> ' +
										'	<i class="glyphicon glyphicon-unchecked"></i></a> ' +
										'	</div>' +
										'</td>' + 
										'<td style="display: none;">'+ value.prodId +'</td>' +
										'<td>'+ value.prodCode +'</td>' +
										'<td>'+ value.prodName +'</td>' +
										'<td style="display: none;">'+ value.prodUomCode +'</td>' +
										'<td>'+ value.prodCatName +'</td>' +
										'<td>'+ value.prodAppName+'</td>' +									
										'<td style="display: none;">'+ value.balance+'</td>' +	
										'<td style="display: none;">'+ value.sales+'</td>' +	
									'</tr>'
									);		
								});								
								$('#modal_search_person').modal('show');	
						}	
				  }   
				}).error(function (response) {
					alert(response.responseText);
				});  
		}/* e.keycode=13 */	
	});
	
	$("#btnSubmit").click(function(){ 
		var prodId = $('#prodId').val();
		window.location.href = "<?=$rootPage;?>.php?prodId="+prodId;
	});
	
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
