<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<?php include 'head.php'; ?>  
<?php include 'inc_helper.php'; 

$rootPage = "picking";

$pickNo=$_GET['pickNo'];
$id=$_GET['id'];
				
?>      
   
<!--
BODY TAG OPTIONS:
=================
Apply one or more of the following classes to get the
desired effect
|---------------------------------------------------------|
| SKINS         | skin-blue                               |
|               | skin-black                              |
|               | skin-purple                             |
|               | skin-yellow                             |
|               | skin-red                                |
|               | skin-green                              |
|---------------------------------------------------------|
|LAYOUT OPTIONS | fixed                                   |
|               | layout-boxed                            |
|               | layout-top-nav                          |
|               | sidebar-collapse                        |
|               | sidebar-mini                            |
|---------------------------------------------------------|
-->
<body class="hold-transition skin-blue sidebar-mini">
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
              <h3 class="box-title">Available Item Stock</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
				<form id="form1" action="" method="post" class="form" novalidate>
			<?php
				
				$sql = "
				SELECT itm.`prodCodeId`, itm.`issueDate`, itm.`grade`, itm.`qty` as meters
				, COUNT(*) as qty, IFNULL(SUM(itm.`qty`),0) as total			
				, (SELECT IFNULL(SUM(pickd.qty),0) FROM picking pickh INNER JOIN picking_detail pickd 
						ON pickh.pickNo=pickd.pickNo
						WHERE pickd.prodId=prd.id AND pickd.issueDate=itm.issueDate AND pickd.grade=itm.grade
						AND pickh.isFinish='N' ) as bookedQty
				,prd.id as prodId, prd.code as prodCode
				FROM `receive` hdr 
				INNER JOIN receive_detail dtl on dtl.rcNo=hdr.rcNo  
				INNER JOIN wh_shelf_map_item smi ON smi.recvProdId=dtl.id 
				INNER JOIN product_item itm ON itm.prodItemId=dtl.prodItemId 
				LEFT JOIN product prd ON prd.id=itm.prodCodeId 
				
				WHERE 1=1
				AND hdr.statusCode='P' 	
				AND dtl.statusCode='A' ";
				$sql .= "AND itm.prodCodeId=:id ";
				
				$sql .= "GROUP BY itm.`prodCodeId`, itm.`issueDate`, itm.`grade`, prd.code , itm.`qty`
								
				ORDER BY itm.`issueDate` ASC  
				";
				//$result = mysqli_query($link, $sql);
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':id', $id);
				$stmt->execute();	
					
				?>
				<input type="hidden" name="pickNo" value="<?=$pickNo;?>" />				
				<input type="hidden" name="prodId" value="<?=$id;?>" />		
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
					<th>#</th>
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
					<td><?= $row['meters']; ?></td>
					<td><?= $row['qty']; ?></td>
					<td><?= $row['total']; ?></td>
					<td style="color: red;"><?= $row['bookedQty']; ?></td>
					<td style="color: blue;"><?= $row['total']-$row['bookedQty']; ?></td>
					
					<td><input type="textbox" name="pickQty" class="form-control" value=""  
						data-prodId="<?=$row['prodId'];?>" data-issueDate="<?=$row['issueDate'];?>" 
						data-grade="<?=$row['grade'];?>" 
						onkeypress="return numbersOnly(this, event);" 
						onpaste="return false;"
					/></td>
					
					<td><a href="#" name="btn_row_submit" class="btn btn-default" 
					data-prodId="<?=$row['prodId'];?>" data-issueDate="<?=$row['issueDate'];?>" 
					data-grade="<?=$row['grade'];?>" > Add</a></td>
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
				
	
	$('input[name="pickQty"]').keyup(function(e){
		if(e.keyCode == 13)
		{ 
			var params = {
				pickNo: $('input[name=pickNo]').val(),
				prodId: $('input[name=prodId]').val(),
				issueDate: $(this).attr('data-issueDate'),
				grade: $(this).attr('data-grade')//,
			//	pickQty: $(this).val()
			};
			//post_data(params);
		}/* e.keycode=13 */	
	});
	
	
	$('a[name=btn_row_submit]').click (function(e) {
		var params = {
			pickNo: $('input[name=pickNo]').val(),
			prodId: $('input[name=prodId]').val(),
			issueDate: $(this).attr('data-issueDate'),
			grade: $(this).attr('data-grade'),
			pickQty: $(this).closest("tr").find('input[name="pickQty"]').val()
		};
		if(params.pickQty==0){
			$.smkAlert({
				text: 'Quantity can not be zero.',
				type: 'danger',
				position:'top-center'
			});
			return false;
		}
		post_data(params);
	});
	//.btn_click
	
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


<!--Integers (non-negative)-->
<script>
  function numbersOnly(oToCheckField, oKeyEvent) {
    return oKeyEvent.charCode === 0 ||
        /\d/.test(String.fromCharCode(oKeyEvent.charCode));
  }
</script>