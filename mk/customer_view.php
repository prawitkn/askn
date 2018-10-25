<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<?php include 'head.php'; ?>  
<?php include 'inc_helper.php'; ?>      
 
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
      <h1>
		Customer Info
      </h1>
      <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Main</a></li>
        <li class="active">Customer Info</li>
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
				<label class="box-title">Salesman Info</label>
				&nbsp;&nbsp;
				<input type="hidden" name="code" value="<?= $_GET['code']; ?>" />
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
				<button type="submit"  class="form-control">Submit</button>				
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
					$sql = "SELECT cm.`code`, cm.`custName`, cm.`custAddr`, cm.`custContact`, cm.`custContactPosition`, cm.`custEmail`, cm.`custTel`, cm.`custFax`, cm.`smCode`, cm.`statusCode` 
							, sm.name as smName, sm.surname as smSurname							
							FROM `customer` cm 
							LEFT JOIN `salesman` sm on cm.smCode=sm.code  
							WHERE 1
							AND cm.code=:code 
							";
					$stmt = $pdo->prepare($sql);
					$stmt->bindParam(':code', $code);
					$stmt->execute();
					$sm = $stmt->fetch();
					$code = $sm['code'];
				?>
                <div class="col-md-12">
					<table>
						<tr>
							<td style="font-size: large; font-weight: bold;">Customer Name : </td>
							<td style="font-size: large;"><?= $sm['code'].'&nbsp;:&nbsp;'.$sm['custName']; ?></td>
						</tr>
						<tr>
							<td style="font-size: large; font-weight: bold;">Address : </td>
							<td style="font-size: large;"><?= $sm['custAddr']; ?></td>
						</tr>
						<tr>
							<td style="font-size: large; font-weight: bold;">Default Contact : </td>
							<td style="font-size: large;"><?= $sm['custContact']; ?></td>
						</tr>
						<tr>
							<td style="font-size: large; font-weight: bold;">Contact Position : </td>
							<td style="font-size: large;"><?= $sm['custContactPosition']; ?></td>
						</tr>
						<tr>
							<td style="font-size: large; font-weight: bold;">E-mail : </td>
							<td style="font-size: large;"><?= $sm['custEmail']; ?></td>
						</tr>
						<tr>
							<td style="font-size: large; font-weight: bold;">Tel : </td>
							<td style="font-size: large;"><?= $sm['custTel']; ?></td>
						</tr>
						<tr>
							<td style="font-size: large; font-weight: bold;">Fax : </td>
							<td style="font-size: large;"><?= $sm['custFax']; ?></td>
						</tr>
						<tr>
							<td style="font-size: large; font-weight: bold;">Salesman : </td>
							<td style="font-size: large;"><?= $sm['smName'].'&nbsp;&nbsp;'.$sm['smSurname']; ?> [<?= $sm['smCode']; ?>]</td>
						</tr>
						<tr>
							<td style="font-size: large; font-weight: bold;">Status : </td>
							<td style="font-size: large;"><?= $sm['statusCode']; ?></td>
						</tr>
					</table>
                </div><!-- /.col-10 -->
            </div><!-- /.row -->
            </div>  
<!-- Day8 00:05:45-->            
    
	
	
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
							INNER JOIN `customer` b on a.`custCode`= b.code
							INNER JOIN `salesman` c on a.`smCode`= c.code
							WHERE a.custCode=:custCode
							ORDER BY a.`createTime` DESC
							LIMIT 10
							";
					$result = mysqli_query($link, $sql);
					$stmt = $pdo->prepare($sql);
					$stmt->bindParam(':custCode', $code);
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
              <h3 class="box-title">Top 10 Product</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
				<?php							
					$sql = "SELECT a.`prodCode` as code, IFNULL(sum(a.`qty`),0) as qty, IFNULL(sum(a.`netTotal`),0) as netTotal
							, c.`prodNameNew` as prodName
							FROM sale_detail a
							INNER JOIN sale_header b on a.soNo=b.soNo AND b.statusCode='P' 
								and year(b.saleDate)=:year ".($month<>"0"?"and month(b.saleDate)=:month":"")."
							INNER JOIN product c on a.prodCode=c.code
							WHERE 1
							AND b.`custCode`=:custCode 
							GROUP BY a.`prodCode`, c.`prodNameNew`
							ORDER BY 3 desc
							LIMIT 10 
							";					
					$stmt = $pdo->prepare($sql);
					$stmt->bindParam(':custCode', $code);
					$stmt->bindParam(':year', $year);
					($month<>"0"?$stmt->bindParam(':month', $month):"");
					$stmt->execute();
				?>
		
				<div class="table-responsive">
				<table class="table no-margin">
				  <thead>
				  <tr>
					<th>No.</th>
					<th>Item Name</th>
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
						 <a target="_blank" href="product_view.php?code=<?=$row['code'];?>" ><?= $row['prodName']; ?></a>
					</td>
					<td style="text-align: right;">
						 <?= $row['qty']; ?>
					</td>
					<td style="text-align: right;">
						 <?= number_format($row['netTotal'],2,'.',','); ?>
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
				,(SELECT IFNULL(sum(tg.budget),0)/1000 FROM target_cust tg
					WHERE tg.custCode=:custCode 
					AND tg.month=a.id AND tg.year=:yearBudget)as sumBudget
				,(SELECT IFNULL(sum(tg.Forecast),0)/1000 FROM target_cust tg
					WHERE tg.custCode=:custCode2 
					AND tg.month=a.id AND tg.year=:yearForecast) as sumForecast
				, IFNULL(sum(b.total),0)/1000 as sumActual
				FROM `month` a                        
				LEFT JOIN `sale_header` b on month(b.saleDate)=a.id and b.statusCode='P' and b.custCode=:custCode3 
					and year(b.saleDate)=:year
				WHERE 1
				GROUP BY a.id, a.abb_eng
				";
		$stmt = $pdo->prepare($sql);
		$stmt->bindParam(':custCode', $code);
		$stmt->bindParam(':custCode2', $code);
		$stmt->bindParam(':custCode3', $code);
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
            name: 'Target',
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
            //data: [1, 0, 4]
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
        },
        yAxis: {
            title: {
                text: '(1,000) Baht'
            }
        },
        series: [{
            name: 'Forecast',
			//data: [1, 0, 4]
            data: [<?php echo implode(",", $arrYtdForecast); ?>],            
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
