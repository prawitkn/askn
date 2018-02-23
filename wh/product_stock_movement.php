<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<?php include 'head.php'; ?>  
<?php include 'inc_helper.php'; ?>      
   
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
      <h1>
		Product Info
      </h1>
      <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Main</a></li>
        <li class="active">Product Info</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
	
      <!-- Your Page Content Here -->
	  <div class="row">
        <div class="col-md-12">
          <div class="box">
            <div class="box-header with-border">              
			  <form id="frmPeriod" method="get" class="form-inline">
				<label class="box-title">Product Info</label>
				&nbsp;&nbsp;
				<input type="hidden" name="id" value="<?= $_GET['id']; ?>" />
				Year : 
				<select name="year" class="form-control">
					<?php 
						$y = date('Y', strtotime('-2 years'));
						while($y <= date('Y')){
							$selected=($year==$y?'selected':'');
							echo '<option value="'.$y.'" '.$selected.' >'.$y.'</option>';
							$y+=1;
						}
					?>
				</select>				
				Month : 
				<select name="month" class="form-control">
					<option value="0" <?php echo ($month=="0"?'selected':''); ?> >--All--</option>
					<?php
					$sql = "SELECT * FROM month";
					$stmt = $pdo->prepare($sql);
					$stmt->execute();					
					while ($row = $stmt->fetch()){
						$selected=($month==$row['id']?'selected':'');						
						echo '<option value="'.$row['id'].'" '.$selected.'>'.$row['abb_eng'].'</option>';
					}
					?>
				</select>
				<button type="submit" >Submit</button>				
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
				<?php				
					$code = $_GET['code'];
					$sql = "SELECT `code`, `prodGroup`, `prodName`, `prodNameNew`, `photo`, `prodPrice`, `prodDesc`, `appID`, `statusCode` 
							FROM `product` 
							WHERE 1
							AND code=:code 
							";
					$stmt = $pdo->prepare($sql);
					$stmt->bindParam(':code', $code);
					$stmt->execute();
					$sm = $stmt->fetch();
				?>
				<div class="col-md-4">
					<image src="dist/img/product/<?php echo (empty($sm['photo'])? 'default.jpg' : $sm['photo']) ?> " width="300" height="300" />
				</div>				
                <div class="col-md-8">
					<table>
						<tr>
							<td style="font-size: large; font-weight: bold;">Product Name : </td>
							<td style="font-size: large;"><?= $sm['code'].'&nbsp;&nbsp;'.$sm['prodNameNew']; ?></td>
						</tr>
						<tr>
							<td style="font-size: large; font-weight: bold;">Description : </td>
							<td style="font-size: large;"><?= $sm['prodDesc']; ?></td>
						</tr>
						<tr>
							<td style="font-size: large; font-weight: bold;">App ID : </td>
							<td style="font-size: large;"><?= $sm['appID']; ?></td>
						</tr>
						<tr>
							<td style="font-size: large; font-weight: bold;">Status : </td>
							<td style="font-size: large;"><?= $sm['statusCode']; ?></td>
						</tr>
						<tr>
							<td style="font-size: large; font-weight: bold;">Sales Price : </td>
							<td style="font-size: large;"><?= number_format($sm['prodPrice'],2,'.',','); ?></td>
						</tr>
					</table>
                </div><!-- /.col-10 -->
            </div><!-- /.row -->
            </div>  
<!-- Day8 00:05:45-->            
    
	<div class="row">
		<?php 
		$sql = "SELECT `code`, `prodGroup`, `prodName`, `prodNameNew`, `photo`, `prodPrice`, `prodDesc`, `appID`, `statusCode` 
							FROM `product` 
							WHERE 1
							AND code=:code 
							";
					$stmt = $pdo->prepare($sql);
					$stmt->bindParam(':code', $code);
					$stmt->execute();
					$sm = $stmt->fetch();
		?>
		
	</div>
	
	
	<!-- Main row -->
      <div class="row">
        <!-- Left col -->
        <div class="col-md-8">
          <!-- MAP & BOX PANE -->
          <div class="box box-success">
            <div class="box-header with-border">
              <h3 class="box-title">Forecast VS Actual Amount Monthly</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
				 <div id="container" style="width:100%; height:400px;">
					
				</div> 
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
		  
          <!-- Forecast VS Actual Amount Year to Date -->
		  <div class="box box-warning direct-chat direct-chat-warning">
			<div class="box-header with-border">
			  <h3 class="box-title">Forecast VS Actual Amount Year to Date</h3>
			  <div class="box-tools pull-right">
				<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
				</button>
				<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
			  </div>
			</div>
			<!-- /.box-header -->
			<div class="box-body">
				  <div id="container2" style="width:100%; height:400px;">
					
				</div> 
			</div>
			<!-- /.box-body -->
		  </div>
		  <!-- /.box -->

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
					$sql = "SELECT a.`soNo`, a.`saleDate`, a.`custCode`, a.`smCode`, a.`statusCode`, a.`createTime`
							,b.custName
							,c.name as smName
							FROM `sale_header` a 
							INNER JOIN `sale_detail` od on a.soNo=od.soNo and od.prodCode=:prodCode 
							INNER JOIN `customer` b on a.`custCode`= b.code
							INNER JOIN `salesman` c on a.`smCode`= c.code
							WHERE 1

							ORDER BY a.`createTime` DESC
							LIMIT 10
							";
					$result = mysqli_query($link, $sql);
					$stmt = $pdo->prepare($sql);
					$stmt->bindParam(':prodCode', $code);
					$stmt->execute();	
					
				?>
              <div class="table-responsive">
                <table class="table no-margin">
                  <thead>
                  <tr>
                    <th>Order No.</th>
                    <th>Customer</th>
					<th>Salesman</th>
                    <th>Status</th>
                    <th>Create Time</th>
                  </tr>
                  </thead>
                  <tbody>
				  <?php $row_no = 1; while ($row = $stmt->fetch()) { 
					  $statusName = '<label class="label label-info">Being</label>';
						switch($row['statusCode']){
							case 'C' : $statusName = '<label class="label label-defalut">Confirmed</label>'; break;
							case 'P' : $statusName = '<label class="label label-success">Approved</label>'; break;
							default : 						
						}
						?>
                  <tr>
                    <td><a href="sale_view.php?soNo=<?=$row['soNo'];?>" target="_blank"><?= $row['soNo']; ?></a></td>
					<td><?= $row['custName']; ?></td>
					<td><?= $row['smName']; ?></td>
					<td>
						<?=$statusName;?>
					</td>
					<td><a href="pages/examples/invoice.html"><?= to_thai_datetime_fdt($row['createTime']); ?></a></td>
                </tr>
                <?php $row_no+=1; } ?>
                  </tbody>
                </table>
              </div>
              <!-- /.table-responsive -->
            </div>
            <!-- /.box-body -->
            <div class="box-footer clearfix">
              <a href="javascript:void(0)" class="btn btn-sm btn-info btn-flat pull-left">Place New Order</a>
              <a href="javascript:void(0)" class="btn btn-sm btn-default btn-flat pull-right">View All Orders</a>
            </div>
            <!-- /.box-footer -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->

        <div class="col-md-4">
          <!-- TOP 10 PRODUCT LIST -->
          <div class="box box-danger">
            <div class="box-header with-border">
              <h3 class="box-title">Top 10 Salesman</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
				<?php			
					$sql = "SELECT sm.`code`, sm.`name`
							, IFNULL(SUM(od.qty),0) as sumQty
							, IFNULL(SUM(od.netTotal),0) as sumNetTotal 
							FROM `salesman` sm
							INNER JOIN sale_header oh on sm.code=oh.smCode and oh.statusCode='P'
								and year(oh.saleDate)=:year ".($month<>"0"?"and month(oh.saleDate)=:month":"")."
							INNER JOIN sale_detail od on oh.soNo=od.soNo and od.prodCode=:prodCode 							
							WHERE 1
							ORDER BY 4 DESC 
							LIMIT 10
							";					
					$stmt = $pdo->prepare($sql);
					$stmt->bindParam(':prodCode', $code);
					$stmt->bindParam(':year', $year);
					($month<>"0"?$stmt->bindParam(':month', $month):"");
					$stmt->execute();	
				?>
		
				<div class="table-responsive">
				<table class="table no-margin">
				  <thead>
				  <tr>
					<th>No.</th>
					<th>Salesman</th>
					<th>Qty</th>
					<th>Amount</th>
				  </tr>
				  </thead>
				  <tbody>
				  <?php $row_no = 1; while ($row = $stmt->fetch()) { ?>
				  <tr>
					<td>
						 <?= $row_no; ?>
					</td>
					<td>
						 <a target="_blank" href="salesman_view.php?code=<?=$row['code'];?>" ><?= $row['name']; ?></a>
					</td>
					<td style="text-align: right;">
						 <?= number_format($row['sumQty'],0,'.',','); ?>
					</td>
					<td style="text-align: right;">
						 <?= number_format($row['sumNetTotal'],2,'.',','); ?>
					</td>
				</tr>
				<?php $row_no+=1; } ?>
				  </tbody>
				</table>
            </div>
            <!-- /.box-body -->
            <div class="box-footer text-center">
              <a href="javascript:void(0)" class="uppercase">View All Salesman</a>
            </div>
            <!-- /.box-footer -->
          </div>
          <!-- /.box -->
		  
		  <!-- TOP 10 CUSTOMER LIST -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Top 10 Customers</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
				<?php							
					$sql = "SELECT cm.`code`, cm.`custName`
							, IFNULL(SUM(od.qty),0) as sumQty
							, IFNULL(SUM(od.netTotal),0) as sumNetTotal 
							FROM `customer` cm
							INNER JOIN sale_header oh on cm.code=oh.custCode and oh.statusCode='P'
								and year(oh.saleDate)=:year ".($month<>"0"?"and month(oh.saleDate)=:month":"")."
							INNER JOIN sale_detail od on oh.soNo=od.soNo and od.prodCode=:prodCode 							
							WHERE 1
							ORDER BY 4 DESC 
							LIMIT 10
							";					
					$stmt = $pdo->prepare($sql);
					$stmt->bindParam(':prodCode', $code);
					$stmt->bindParam(':year', $year);
					($month<>"0"?$stmt->bindParam(':month', $month):"");
					$stmt->execute();	
				?>
		
				<div class="table-responsive">
				<table class="table no-margin">
				  <thead>
				  <tr>
					<th>No.</th>
					<th>Cust. Name</th>
					<th>Qty</th>
					<th>Amount</th>
				  </tr>
				  </thead>
				  <tbody>
				  <?php $row_no = 1; while ($row = $stmt->fetch()) { ?>
				  <tr>
					<td>
						 <?= $row_no; ?>
					</td>
					<td>
						<a target="_blank" href="customer_view.php?code=<?=$row['code'];?>" ><?= $row['custName']; ?></a>
					</td>
					<td style="text-align: right;">
						 <?= number_format($row['sumQty'],0,'.',','); ?>
					</td>
					<td style="text-align: right;">
						 <?= number_format($row['sumNetTotal'],2,'.',','); ?>
					</td>
				</tr>
				<?php $row_no+=1; } ?>
				  </tbody>
				</table>
            </div>
            <!-- /.box-body -->
            <div class="box-footer text-center">
              <a href="javascript:void(0)" class="uppercase">View All Products</a>
            </div>
            <!-- /.box-footer -->
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
<!-- Hightchart -->
<script src="plugins/highcharts-5.0.12/code/highcharts.js"></script>
<script src="plugins/highcharts-5.0.12/code/modules/exporting.js"></script>

 <?php 
		$sql = "SELECT
						a.id, a.abb_eng as monthName
						,(SELECT IFNULL(sum(tg.budget),0)/1000 FROM target_prod tg
                         	WHERE tg.month=a.id 
							AND tg.prodCode=:code AND tg.year=:yearBudget )as sumBudget
                        ,(SELECT IFNULL(sum(tg.Forecast),0)/1000 FROM target_prod tg
                         	WHERE tg.month=a.id 
							AND tg.prodCode=:code2 AND tg.year=:yearForecast )as sumForecast
						, IFNULL(sum(od.netTotal),0)/1000 as sumActual
						FROM `month` a                        
						LEFT JOIN `sale_header` oh on month(oh.saleDate)=a.id and oh.statusCode='P' 
							and year(oh.saleDate)=:year
						LEFT JOIN `sale_detail` od on oh.soNo=od.soNo AND od.prodCode=:code3  
						WHERE 1
						GROUP BY a.id, a.abb_eng
						";
		$stmt = $pdo->prepare($sql);
		$stmt->bindParam(':code', $code);
		$stmt->bindParam(':code2', $code);
		$stmt->bindParam(':code3', $code);
		$stmt->bindParam(':yearBudget', $year);
		$stmt->bindParam(':yearForecast', $year);
		$stmt->bindParam(':year', $year);
		$stmt->execute();
		$monthName = array();
        $sumBudget = array();
		$sumForecast = array();
        $sumActual = array();
		$arrYtdBudget = array();
		$arrYtdForecast = array();
		$arrYtdActual = array();
		$tmpBudget = 0;
		$tmpForecast = 0;
		$tmpActual = 0;
        while($row = $stmt->fetch()){
            $monthName[] = $row['monthName'];
            $sumBudget[] = $row['sumBudget'];
			$sumForecast[] = $row['sumForecast'];
            $sumActual[] = $row['sumActual'];
			
			$tmpBudget += $row['sumBudget'];
			$tmpForecast += $row['sumForecast'];
			$tmpActual += $row['sumActual'];
			$arrYtdBudget[] = $tmpBudget;
			$arrYtdForecast[] = $tmpForecast;
			$arrYtdActual[] = $tmpActual;
        }
  ?>
<script>
$(function () { 
    var myChart = Highcharts.chart('container', {
        chart: {
            type: 'column'
        },
        data: {
            decimalPoint: "."
        },
        title: {
            text: <?php echo $year; ?>
        },
        xAxis: {            
            //categories: ['Apples', 'Bananas', 'Oranges'],
            categories: [<?php echo "'" . implode("','", $monthName) . "'"; ?>]
        },
        yAxis: {
            title: {
                text: '(1,000) Baht'
            }
        },
        series: [{
            name: 'Forecast',
			//data: [1, 0, 4]
            data: [<?php echo implode(",", $sumForecast); ?>],            
            dataLabels: {
                enabled: true,
				inside: true,
				rotation: 270,
				y: -50,
				style: {
                    fontWeight: 'bold'
                },
                format: '{point.y:,.0f} Baht'
            }
        },
             {
            name: 'Actual',
            data: [<?php echo implode(",", $sumActual); ?>],
            dataLabels: {
                //enabled: true,
                //format: '{y} ชิ้น'
            }
        }
        ]
    });
	
	var myChart2 = Highcharts.chart('container2', {
        chart: {
            type: 'line'
        },
        data: {
            decimalPoint: "."
        },
        title: {
            text: 'Year to date '+<?php echo $year; ?>
        },
        xAxis: {
            
            //categories: ['Apples', 'Bananas', 'Oranges'],
            categories: [<?php echo "'" . implode("','", $monthName) . "'"; ?>]
                        //'prod5','prod6','prod7'
        },
        yAxis: {
            title: {
                text: '(1,000) Baht'
            }
        },
        series: [{
            name: 'Forecast',
            data: [<?php echo implode(",", $arrYtdForecast); ?>],
            //data: [1, 0, 4]
            dataLabels: {
                //enabled: true,
                //format: '{y} ชิ้น'
            }
        },{
            name: 'Actual',
            data: [<?php echo implode(",", $arrYtdActual); ?>],
            //data: [1, 0, 4]
            dataLabels: {
                //enabled: true,
                //format: '{y} ชิ้น'
            }
        }
        ]
    });
});

</script>

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
