<?php include 'inc_helper.php'; ?>      
<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<?php include 'head.php'; ?>  

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
      <h1>
		Product Stock Info
      </h1>
      <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Main</a></li>
        <li class="active">Product Stock Info</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
		
	<?php				
		$id = $_GET['id'];
		$sql = "SELECT `code`, `name`, `name2`, `photo`, `price`, `description`, `appCode`, `statusCode` 
				FROM `product` 
				WHERE 1
				AND id=:id 
				";
		$stmt = $pdo->prepare($sql);
		$stmt->bindParam(':id', $id);
		$stmt->execute();
		$row = $stmt->fetch();
	?>
      <!-- Your Page Content Here -->
	  <div class="row">
        <div class="col-md-12">
          <div class="box">
            <div class="box-header with-border">              
			  <form id="frmPeriod" method="get" class="form-inline">
				<label class="box-title">Product Code : <?=$row['code'];?></label>			
			  </form>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <div class="btn-group">
                  <button type="button" class="btn btn-box-tool dropdown-toggle" data-toggle="dropdown">
                    <i class="fa fa-wrench"></i></button>
                  <ul class="dropdown-menu" role="menu">
                    <li><a href="#">Action</a></li>
                    <li><a href="#">Another action</a></li>
                    <li><a href="#">Something else here</a></li>
                    <li class="divider"></li>
                    <li><a href="#">Separated link</a></li>
                  </ul>
                </div>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <div class="row">
				<div class="col-md-8">
					<div class="col-md-4" style="text-align: center;">
						
						<a href="../images/product/<?php echo (empty($row['photo'])? 'default.jpg' : $row['photo']) ?> " data-fancybox="images" data-caption="<?=$row['code'];?>">
							<image src="../images/product/<?php echo (empty($row['photo'])? 'default.jpg' : $row['photo']) ?> " width="200" height="200" />
						</a>
					</div>				
	                <div class="col-md-8">
						<label>Product Name : </label>
						<?= $row['name']; ?><br/>
						<label>Description : </label>
						<?= $row['description']; ?><br/>
						
						<?php				
							$sql = "SELECT `prodId`, `open`, `receive`, `sales`, `delivery`, `balance`, uomCode
							FROM `stk_bal` 
							LEFT JOIN product on product.id=stk_bal.prodId
							WHERE 1
							AND sloc=8 
							AND prodId=:id 
							";
							$id = $_GET['id'];
							if(ISSET($_GET['sloc'])){ $sql.="AND sloc=:sloc "; }
							$stmt = $pdo->prepare($sql);
							$stmt->bindParam(':id', $id);
							if(ISSET($_GET['sloc'])){ $stmt->bindParam(':sloc', $_GET['sloc']); }
							$stmt->execute();
							$row = $stmt->fetch();
						?>
						<label>Total Stock Balance</label>
						<?php if($row['sales']>0) { ?>
							<h3 style="color: blue;"><?php echo number_format($row['balance'],0,'.',',').' (-'.$row['sales'].') '.' '.$row['uomCode']; ?></h3> <br/>
						<?php }else{ ?>
						<h3 style="color: blue;"><?= number_format($row['balance'],0,'.',',').' '.$row['uomCode']; ?></h3> <br/>
						<?php } ?>
						
	                </div><!-- /.col-10 -->
	         	</div>
	         	<!--/.col-md-8-->

				<div class="col-md-4">
					<h3>Avalible Stock by Length/Box/KG</h3>
					<?php									
						$sql = "SELECT itm.prodCodeId as prodId,
						itm.qty, sum(itm.qty) as sumQty
						FROM `receive_detail` dtl 
						INNER JOIN receive hdr ON hdr.rcNo=dtl.rcNo AND hdr.toCode=8 
						INNER JOIN product_item itm ON itm.prodItemId=dtl.prodItemId AND itm.prodCodeId=:id 
						WHERE 1
						GROUP BY itm.prodCodeId, itm.qty
						";
						if(ISSET($_GET['sloc'])){ $sql.="AND hdr.toCode=:sloc "; }
						$stmt = $pdo->prepare($sql);
						$stmt->bindParam(':id', $id);
						
						if(ISSET($_GET['sloc'])){ $stmt->bindParam(':sloc', $_GET['sloc']); }
						$stmt->execute();
							?>
			              <div class="table-responsive">
			                <table class="table no-margin">
			                  <thead>
			                    <th style="text-align: center;">Length/Pieces/KG</th>
			                    <th style="text-align: right;">Qty</th>
								<th style="text-align: right;">Qty Total</th>
			                  </tr>
			                  </thead>
			                  <tbody>
							  <?php $row_no = 1; while ($row = $stmt->fetch()) { 
								?>
			                  <tr>
								<td style="text-align: center;"><?= number_format($row['qty'],0,'.',','); ?></td>
								<td style="text-align: right;"><?= number_format($row['sumQty']/$row['qty'],0,'.',','); ?></td>
								<td style="text-align: right;"><?= number_format($row['sumQty'],0,'.',','); ?></td>
			                </tr>
			                <?php $row_no+=1; } ?>
			                  </tbody>
			                </table>
			              </div>
			              <!-- /.table-responsive -->
					
                </div><!-- /.col-4 -->
            </div><!-- /.row -->
            </div>  
<!-- Day8 00:05:45-->            
    
	
	<!-- Main row -->
      <div class="row">
        <!-- Left col -->
        <div class="col-md-12">
          <!-- TABLE: LATEST ORDERS -->
          		
		
		
          <!-- TABLE: LATEST ORDERS -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Latest Orders</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
			
			<?php	
					
					$sql = "SELECT sd.`prodId`, sd.`qty`, sd.`soNo` 
					, prd.`code` as prodCode
					FROM sale_detail sd 
					INNER JOIN sale_header s on sd.soNo=s.soNo AND s.isClose='N' 
					LEFT JOIN product prd ON prd.id=sd.prodId 
					WHERE 1 
					AND sd.prodId=:id 
					LIMIT 10 
					";
					$stmt = $pdo->prepare($sql);
					$stmt->bindParam(':id', $id);
					$stmt->execute();
					
				?>
              <div class="table-responsive">
                <table class="table no-margin">
                  <thead>
                  <tr>
                    <th>Order No.</th>
                    <th>Qty</th>
                  </tr>
                  </thead>
                  <tbody>
				  <?php $row_no = 1; while ($row = $stmt->fetch()) { 
					  
						?>
                  <tr>
                    <td><a href="sale_view.php?soNo=<?=$row['soNo'];?>" target="_blank"><?= $row['soNo']; ?></a></td>
					<td><?= $row['qty']; ?></td>
                </tr>
                <?php $row_no+=1; } ?>
                  </tbody>
                </table>
              </div>
              <!-- /.table-responsive -->
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
		  
        </div>
        <!-- /.col -->

      </div>
      <!-- /.row second box col8 & col 4 -->
	  
	  
	
	
  
	<div id="spin"></div>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Main Footer -->
  <?php include'footer.php'; ?>  
  
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
<!-- Add _.$ jquery coding -->
<script src="assets/underscore-min.js"></script>
<!-- Add fancybox JS 
<script src="//code.jquery.com/jquery-3.2.1.min.js"></script>-->
<script src="plugins/fancybox-master/dist/jquery.fancybox.min.js"></script>
 
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
						
				
			$('a[name=btn_submit]').click(function(){				
				var checked='';
				$('input[name=statusCode]:checked').each(function(){
					if(checked.length==0){
						checked=$(this).val();
					}else{
						checked=checked+','+$(this).val();
					}
				});
				var params = {
					id: $('#id').val(),
					name: $('#name').val(),
					surname: $('#surname').val(),
					positionName: $('#positionName').val(),
					mobileNo: $('#mobileNo').val(),
					email: $('#email').val(),
					statusCode: checked
				};								
				//alert(params.status_code);
				$.post({
					url: 'salesman_edit_ajax.php',
					data: params,
					dataType: 'json'
				}).done(function (data) {					
					 if (data.success){ 
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
						 //$("#title").focus(); 
				}).error(function (response) {
					  alert(response.responseText);
				});    				
			});
	});
  </script>
</html>
