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
   $rootPage="utility_search_barcode";
   ?>
   
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      
	  <h1><i class="glyphicon glyphicon-search"></i>
       Item Info by Barcode
        <small>Utiltity</small>
      </h1>

      <ol class="breadcrumb">
		<li><a href="#"><i class="glyphicon glyphicon-list"></i>Item Info by Barcode</a></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
	
      <!-- Your Page Content Here -->
	<?php
		$barcode=(isset($_GET['barcode'])?$_GET['barcode']:'');
		$sql = "SELECT tmp.`prodItemId`,  `prodCodeId` as prodId, `barcode`, `issueDate`, `machineId`, `NW`, `GW`, `qty`, `packQty`, `grade`, `gradeDate`, `refItemId`, `itemStatus`, `remark`, `problemId`, `gradeTypeId`, `remarkWh`
		,prd.code as prodCode, pigt.name as gradeTypeName
		,IFNULL(s.code,'-') as shelfName 
				FROM (SELECT *, REPLACE(`barcode`, '-', '') as barcodeId 
						FROM product_item  
						 
						 ) as tmp
				LEFT JOIN product prd ON prd.id=tmp.prodCodeId 
				LEFT JOIN product_item_grade_type pigt ON pigt.id=tmp.gradeTypeId 	
				LEFT JOIN receive_detail recv ON recv.prodItemId=tmp.prodItemId
				LEFT JOIN wh_shelf_map_item smi ON smi.recvProdId=recv.id AND smi.statusCode='A' 	
				LEFT JOIN wh_shelf s ON s.id=smi.shelfId 	
				WHERE barcodeId=:barcode
				LIMIT 1 ";
		$stmt = $pdo->prepare($sql);
		$stmt->bindParam(':barcode', $barcode);
		$stmt->execute();
		$row_count = $stmt->rowCount();	
		$rpi=$stmt->fetch();
		$prodItemId = $rpi['prodItemId'];
		$prodId=$rpi['prodId'];	
		?>			  
	
	
	<!-- Main row -->
      <div class="row">
		<div class="col-md-12">
			
			<!-- TABLE: LATEST ORDERS -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Item Info</h3>

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
							<label for="prodId" >Barcode</label>
							<input type="text" name="barcode" class="form-control" value=""  />

							<button id="btn1" type="submit" class="btn btn-default">Submit</button>
						</div>
						<!--/.col-md-->
						<div class="col-md-6">
						</div>
					</div><!--row-->
			<?php
			if($row_count!=1){ ?>
				<h3>Barcode Not Found.</h3>	
			<?php }else{ ?>

			<div style="background-color: #ccffcc; padding: 5px;">
			<div class="row">
				<div class="col-md-12">
					<h3>Product Code : <b><?=$rpi['prodCode'];?></b>
						/ Shelf : <b><?=$rpi['shelfName'];?></b> 
					</h3>
				</div>
			</div>

			<div class="row">
				<div class="col-md-3">
					Produce Date : <b><?= date('d M Y',strtotime( $rpi['issueDate'] ));?></b>
				</div>
				<div class="col-md-3">
					Meter : <b><?=$rpi['qty'];?></b>
				</div>
				<div class="col-md-3">
					Net Weigth : <b><?=$rpi['NW'];?></b>
				</div>
				<div class="col-md-3">
					Gross Weigth : <b><?=$rpi['GW'];?></b>
				</div>
			</div>
			<div class="row">
				<div class="col-md-3">
					<?php
					$gradeName='';
					switch($rpi['grade']){
						case 0 : $gradeName='A'; break;
						case 1 : $gradeName='B'; break;
						case 2 : $gradeName='N'; break;
						default : $gradeName='N/A';
					}
					?>
					Grade : <b><?=$gradeName;?></b>
				</div>
				<div class="col-md-3">
					Grade Date : <b><?= date('d M Y',strtotime( $rpi['gradeDate'] ));?></b>
				</div>
				<div class="col-md-3">
					Grade Type : <b><?=$rpi['gradeTypeName'];?></b>
				</div>
				<div class="col-md-3">
					WH Remark : <b><?=$rpi['remarkWh'];?></b>
				</div>
			</div>

			

			<div class="row">
				<?php
					$sql = "SELECT * FROM (
					SELECT hdr.sendDate as transDate, hdr.sdNo as docNo, hdr.fromCode, hdr.toCode
					, fs.name as fromName, ts.name as toName 
					FROM `send` hdr
					INNER JOIN send_detail dtl ON hdr.sdNo=dtl.sdNo AND dtl.prodItemId=:prodItemId 
					LEFT JOIN sloc fs ON fs.code=hdr.fromCode
					LEFT JOIN sloc ts ON ts.code=hdr.toCode 
					UNION 
					SELECT hdr.receiveDate as transDate, hdr.rcNo as docNo, hdr.fromCode, hdr.toCode
					, fs.name as fromName, ts.name as toName 
					FROM `receive` hdr
					INNER JOIN receive_detail dtl ON hdr.rcNo=dtl.rcNo AND dtl.prodItemId=:prodItemId2 
					LEFT JOIN sloc fs ON fs.code=hdr.fromCode
					LEFT JOIN sloc ts ON ts.code=hdr.toCode 
					UNION 
					SELECT hdr.returnDate as transDate, hdr.rtNo as docNo, hdr.fromCode, hdr.toCode
					, fs.name as fromName, ts.name as toName 
					FROM `rt` hdr
					INNER JOIN rt_detail dtl ON hdr.rtNo=dtl.rtNo AND dtl.prodItemId=:prodItemId3 
					LEFT JOIN sloc fs ON fs.code=hdr.fromCode
					LEFT JOIN sloc ts ON ts.code=hdr.toCode 
					UNION 
					SELECT hdr.prepareDate as transDate, hdr.ppNo as docNo
					, '' as fromCode, '' as toCode, '' as fromName, '' as toName 
					FROM `prepare` hdr
					INNER JOIN prepare_detail dtl ON hdr.ppNo=dtl.ppNo AND dtl.prodItemId=:prodItemId4
					UNION 
					SELECT hdr.deliveryDate as transDate, hdr.doNo as docNo
					, '' as fromCode, st.code as toCode, '' as fromName, st.name as toName 
					FROM `delivery_header` hdr
					INNER JOIN delivery_detail dtl ON hdr.doNo=dtl.doNo AND dtl.prodItemId=:prodItemId5 
					INNER JOIN sale_header sh ON sh.soNo=hdr.soNo 
					LEFT JOIN shipto st ON st.id=sh.shiptoId 	
					) as tmp ORDER BY transDate DESC 				
					";
					$stmt = $pdo->prepare($sql);
					$stmt->bindParam(':prodItemId', $prodItemId);
					$stmt->bindParam(':prodItemId2', $prodItemId);
					$stmt->bindParam(':prodItemId3', $prodItemId);
					$stmt->bindParam(':prodItemId4', $prodItemId);
					$stmt->bindParam(':prodItemId5', $prodItemId);
					$stmt->execute();	
				?>	
				
			</div>
		</div>
			<h3>Item Movement</h3>
			<div class="table-responsive">
                <table class="table no-margin">
                  <thead>
                  <tr>
					<th>No.</th>
					<th>Date</th>
                    <th>Transaction</th>
                    <th>From</th>
                    <th>To</th>
                  </tr>
                  </thead>
                  <tbody>
				  <?php $row_no = 1; while ($row = $stmt->fetch()) { 
				?>
                  <tr>
					<td><?= $row_no; ?></td>
					<td><?= date('d M Y',strtotime( $row['transDate'] )); ?></td>					
					<td><?= $row['docNo']; ?></td>
					<td><?= $row['fromCode'].' - '.$row['fromName']; ?></td>
					<td><?= $row['toCode'].' - '.$row['toName']; ?></td>
                </tr>
                <?php $row_no+=1; } ?>
                  </tbody>
                </table>
              </div>
              <!-- /.table-responsive -->
            </div>

			<?php } //endif rowCount ?>


              
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
