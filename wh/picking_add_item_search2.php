<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<?php include 'head.php'; ?>  
<?php include 'inc_helper.php'; 

$rootPage = "picking";

$locationCode=$_GET['locCode'];
$pickNo=$_GET['pickNo'];
$id=$_GET['id'];
$saleItemId=$_GET['saleItemId'];
				
?>      
  
</head>
<body class="hold-transition <?=$skinColorName;?> sidebar-mini">


	
  
<div class="wrapper">
  <!-- Main Header -->
  <?php include 'header.php'; ?>  
  
  <!-- Left side column. contains the logo and sidebar -->
   <?php include 'leftside.php'; ?>
   
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      
	  <h1><i class="glyphicon glyphicon-shopping-cart"></i>
       Product Stock Info
        <small>Picking management</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?=$rootPage;?>.php"><i class="glyphicon glyphicon-list"></i>Picking List</a></li>
		<li><a href="<?=$rootPage;?>_add.php?pickNo=<?=$pickNo;?>" ><i class="glyphicon glyphicon-edit"></i>Picking No.<?=$pickNo;?></a></li>
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
              <h3 class="box-title">Available Item Stock [<?=$locationCode;?>]</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <!-- /.box-header -->
			<form id="form2" action="#" method="post" class="form" novalidate>
					<input type="hidden" name="pickNo" value="<?=$pickNo;?>" />
					<input type="hidden" name="action" value="item_add" />			
					<input type="hidden" name="prodId" value="<?=$id;?>" />	
					<input type="hidden" name="saleItemId" value="<?=$saleItemId;?>" />	
            <div class="box-body">
					
					
			<?php
			$sql='';
			$sql="SELECT catCode FROM product WHERE id=:id ";
			$stmt = $pdo->prepare($sql);
			$stmt->bindParam(':id', $id);
			$stmt->execute();	
			$row=$stmt->fetch();
			$catCode=$row['catCode'];
			
			switch($locationCode){
				case 'L' :
					$sql = "
					SELECT itm.`prodCodeId`, itm.`issueDate`, itm.`grade`, itm.`qty` as meters
					, COUNT(*) as qty, IFNULL(SUM(itm.`qty`),0) as total			
					, (SELECT IFNULL(SUM(pickd.qty),0) FROM picking pickh INNER JOIN picking_detail pickd 
							ON pickh.pickNo=pickd.pickNo
							WHERE pickd.prodId=prd.id AND pickd.issueDate=itm.issueDate AND pickd.grade=itm.grade
							AND pickh.isFinish='N'
							AND pickh.statusCode<>'X' ) as bookedQty
					, (SELECT IFNULL(SUM(pickd.qty),0) FROM picking pickh INNER JOIN picking_detail pickd 
							ON pickh.pickNo=pickd.pickNo
							WHERE pickd.saleItemId=:saleItemId AND pickd.issueDate=itm.issueDate AND pickd.grade=itm.grade
							AND pickh.pickNo=:pickNo ) as pickQty
					,prd.id as prodId, prd.code as prodCode
					, (SELECT IFNULL(x.shelfId,0) FROM wh_shelf_map_item x WHERE x.recvProdId=dtl.id) as isShelfed
					FROM `receive` hdr 
					INNER JOIN receive_detail dtl on dtl.rcNo=hdr.rcNo  		
					INNER JOIN product_item itm ON itm.prodItemId=dtl.prodItemId 
					LEFT JOIN wh_shelf_map_item smi ON smi.recvProdId=dtl.id 
					LEFT JOIN product prd ON prd.id=itm.prodCodeId 				
					WHERE 1=1
					AND hdr.statusCode='P' 	
					AND dtl.statusCode='A' 
					AND itm.prodCodeId=:id ";
					switch($catCode){
						case '70' : //Weaving
							$sql.="AND DATEDIFF(NOW(), itm.issueDate) <= 365 ";
							break;
						case '72' : //Cutting
							$sql.="AND DATEDIFF(NOW(), itm.issueDate) <= 90 ";
							break;
						default : //80					
					}
					
					$sql.="
					GROUP BY itm.`prodCodeId`, itm.`issueDate`, itm.`grade`, prd.code , itm.`qty` 			
									
					ORDER BY itm.`issueDate` ASC  
					";
					break;
				default : //Export 
					$sql = "
					SELECT itm.`prodCodeId`, s.sendDate as issueDate, itm.`grade`, itm.`qty` as meters
					, COUNT(*) as qty, IFNULL(SUM(itm.`qty`),0) as total			
					, (SELECT IFNULL(SUM(pickd.qty),0) FROM picking pickh INNER JOIN picking_detail pickd 
							ON pickh.pickNo=pickd.pickNo
							WHERE pickd.prodId=itm.prodCodeId AND pickd.issueDate=s.sendDate AND pickd.grade=itm.grade
							AND pickh.isFinish='N'
							AND pickh.statusCode<>'X' ) as bookedQty
					, (SELECT IFNULL(SUM(pickd.qty),0) FROM picking pickh INNER JOIN picking_detail pickd 
							ON pickh.pickNo=pickd.pickNo
							WHERE pickd.saleItemId=:saleItemId AND pickd.issueDate=s.sendDate AND pickd.grade=itm.grade
							AND pickh.pickNo=:pickNo ) as pickQty
					,itm.prodCodeId as prodId, prd.code as prodCode
					, (SELECT IFNULL(x.shelfId,0) FROM wh_shelf_map_item x WHERE x.recvProdId=dtl.id) as isShelfed
					FROM `receive` hdr 
					INNER JOIN receive_detail dtl on dtl.rcNo=hdr.rcNo  		
					INNER JOIN product_item itm ON itm.prodItemId=dtl.prodItemId 
					INNER JOIN send s ON s.sdNo=hdr.refNo 
					LEFT JOIN wh_shelf_map_item smi ON smi.recvProdId=dtl.id 
					LEFT JOIN product prd ON prd.id=itm.prodCodeId 				
					WHERE 1=1
					AND hdr.statusCode='P' 	
					AND dtl.statusCode='A' 
					AND itm.prodCodeId=:id ";
					switch($catCode){
						case '70' : //Weaving
							$sql.="AND DATEDIFF(NOW(), s.sendDate) <= 365 ";
							break;
						case '72' : //Cutting
							$sql.="AND DATEDIFF(NOW(), s.sendDate) <= 90 ";
							break;
						default : //80					
					}
					$sql.="
					GROUP BY itm.`prodCodeId`, s.sendDate, itm.`grade`, prd.code , itm.`qty` 			
									
					ORDER BY s.sendDate ASC  
					";
			}
				
				
				
				//$result = mysqli_query($link, $sql);
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':id', $id);
				$stmt->bindParam(':saleItemId', $saleItemId);
				$stmt->bindParam(':pickNo', $pickNo);
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
					<th>Meters</th>
                    <th>Qty</th>
					<th>Total</th>
					<th style="color: red;">Booked</th>
					<th style="color: blue;">Balance</th>					
                    <th>Pick</th>
                  </tr>
                  </thead>
                  <tbody>
				  <?php $row_no = 1; while ($row = $stmt->fetch()) { 
				  $gradeName = '<b style="color: red;">N/A</b>'; 
				switch($row['grade']){
					case 0 : $gradeName = 'A'; break;
					case 1 : $gradeName = '<b style="color: red;">B</b>'; break;
					case 2 : $gradeName = '<b style="color: red;">N</b>'; break;
					default : 
				} 
				?>
                  <tr>
					<td><?= $row_no; ?></td>
					<td><?= $row['prodCode']; ?></td>					
					<td><?= date('d M Y',strtotime( $row['issueDate'] )); ?></td>
					<td><?= $gradeName; ?></td>
					<td style="text-align: right;"><?= $row['meters']; ?></td>
					<td style="text-align: right;"><?= $row['qty']; ?></td>
					<td style="text-align: right;"><?= $row['total']; ?></td>
					<td style="text-align: right; color: red;"><?= $row['bookedQty']; ?></td>
					<td style="text-align: right; color: blue;"><?= $row['total']-$row['bookedQty']; ?></td>
					
					<td >
						<input type="hidden" name="issueDate[]" value="<?=$row['issueDate'];?>" />
						<input type="hidden" name="grade[]" value="<?=$row['grade'];?>" />
						<input type="hidden" name="meter[]" value="<?=$row['meters'];?>" />
						<input type="hidden" name="balanceQty[]" value="<?=$row['total']-$row['bookedQty'];?>" />
						
						<?php if($row['isShelfed']==0){ ?>
							<input type="textbox" name="pickQty[]" class="form-control" value="No Shelf."  
							data-prodId="<?=$row['prodId'];?>" data-issueDate="<?=$row['issueDate'];?>" 
							data-grade="<?=$row['grade'];?>" data-isShelfed="0" 
							onkeypress="return numbersOnly(this, event);" 
							onpaste="return false;"
							style="text-align: right;"
							disabled
							/>
						<?php }else{ ?>
						<!-- <input type="textbox" name="pickQty[]" class="form-control" value="<?=$row['pickQty'];?>"  
						data-prodId="<?=$row['prodId'];?>" data-issueDate="<?=$row['issueDate'];?>" 
						data-grade="<?=$row['grade'];?>"  data-isShelfed="1" 
						onkeypress="return numbersOnly(this, event);" 
						onpaste="return false;"
						style="text-align: right;"
						/> -->
						<input type="textbox" name="pickQty[]" class="form-control" value="<?=$row['pickQty'];?>"  
						data-prodId="<?=$row['prodId'];?>" data-issueDate="<?=$row['issueDate'];?>" 
						data-grade="<?=$row['grade'];?>"  data-isShelfed="1" 
						 
						onpaste="return false;"
						style="text-align: right;"
						/>
						<?php } ?>
					</td>					
                </tr>
                <?php $row_no+=1; } ?>
                  </tbody>
                </table>
              </div>
              <!-- /.table-responsive -->
            </div>
            <!-- /.box-body -->
            <div class="box-footer clearfix">
				
				<a name="btn_submit" href="#" class="btn btn-primary"><i class="glyphicon glyphicon-save"></i> Submit</a>
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
  <?php include 'footer.php'; ?>  
  
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
				
		
	$('#form2 a[name=btn_submit]').click (function(e) {  alert('dfsafd');
		$('#form2 input[name^=pickQty]').prop('disabled', false).val(0);
		if ($('#form2').smkValidate()){
			//$.smkConfirm({text:'Are you sure to Submit ?',accept:'Yes', cancel:'Cancel'}, function (e){if(e){
				$.post({
					url: '<?=$rootPage;?>_ajax.php',
					data: $("#form2").serialize(),
					dataType: 'json'
				}).done(function(data) {
					if (data.success){  
						$.smkAlert({
							text: data.message,
							type: 'success',
							position:'top-center'
						});
						window.location.href = "<?=$rootPage;?>_add.php?pickNo=<?=$pickNo;?>";
					}else{
						$.smkAlert({
							text: data.message,
							type: 'danger',
							position:'top-center'
						});
						$('#form2 input[data-isShelfed=0]').val("No Shelf.").prop('disabled', true);
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
	});
	//.btn_click
});
</script>

</html>


<!--Integers (non-negative)-->
<script>
  function numbersOnly(oToCheckField, oKeyEvent) {
    return oKeyEvent.charCode === 0 ||
        /\d/.test(String.fromCharCode(oKeyEvent.charCode));
  }
</script>