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
		Product Stock Info
      </h1>
      <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Main</a></li>
        <li class="active">Product Stock Info</li>
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
				<label class="box-title">Product Stock Info</label>			
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
				<div class="col-md-3" style="text-align: center;">
					<image src="../images/product/<?php echo (empty($sm['photo'])? 'default.jpg' : $row['photo']) ?> " width="200" height="200" />
				</div>				
                <div class="col-md-5">
					<label>Product Name : </label>
					<?= $row['code'].'&nbsp;&nbsp;'.$row['name']; ?><br/>
					<label>Description : </label>
					<?= $row['description']; ?><br/>
					
					<?php				
						$sql = "SELECT `prodId`, `open`, `receive`, `sales`, `delivery`, `balance`, uomCode
								FROM `stk_bal` 
								INNER JOIN product on product.id=stk_bal.prodId
								WHERE 1
								AND prodId=:id 
								";
						$stmt = $pdo->prepare($sql);
						$stmt->bindParam(':id', $id);
						$stmt->execute();
						$row = $stmt->fetch();
					?>
					<label>Stock Balance</label>
					<?php if($row['sales']>0) { ?>
						<h1 style="red"><?php echo $row['balance'].' (-'.$row['sales'].') '.' '.$row['uomCode']; ?></h1> <br/>
					<?php }else{ ?>
					<h1 style="red"><?= number_format($row['balance'],0,'.',',').' '.$row['uomCode']; ?></h1> <br/>
					<?php } ?>
					
                </div><!-- /.col-10 -->
				<div class="col-md-4">
					
					
                </div><!-- /.col-10 -->
            </div><!-- /.row -->
            </div>  
<!-- Day8 00:05:45-->            
    
	
	<!-- Main row -->
      <div class="row">
        <!-- Left col -->
        <div class="col-md-12">
          <!-- TABLE: LATEST ORDERS -->
          <div class="box box-success">
            <div class="box-header with-border">
              <h3 class="box-title">Avalible Stock by Unit</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
			
			<?php	
					
			$sql = "SELECT itm.prodCodeId as prodId,
			itm.qty, sum(itm.qty) as sumQty, count(*) as sumPack 
			FROM `receive_detail` dtl 
			INNER JOIN receive hdr ON hdr.rcNo=dtl.rcNo 
			LEFT JOIN product_item itm ON itm.prodItemId=dtl.prodItemId 
			WHERE 1
			GROUP BY itm.prodCodeId, itm.qty
			AND dtl.prodId=:id 
			";
			$stmt = $pdo->prepare($sql);
			$stmt->bindParam(':id', $id);
			$stmt->execute();
					
				?>
              <div class="table-responsive">
                <table class="table no-margin">
                  <thead>
					<th>No.</th>
                    <th style="text-align: center;">Unit</th>
                    <th style="text-align: right;">Total</th>
					<th style="text-align: right;">Pack</th>
                  </tr>
                  </thead>
                  <tbody>
				  <?php $row_no = 1; $totalQty=0; $totalPack=0; while ($row = $stmt->fetch()) { 
					  
						?>
                  <tr>
					<td><?= $row_no; ?></td>
					<td style="text-align: center;"><?= number_format($row['qty'],0,'.',','); ?></td>
					<td style="text-align: right;"><?= number_format($row['sumQty'],0,'.',','); ?></td>
					<td style="text-align: right;"><?= number_format($row['sumPack'],0,'.',','); ?></td>
                </tr>
                <?php $row_no+=1; $totalQty+=$row['sumQty']; $totalPack+=$row['sumPack']; } ?>
					<tr style="font-weight: bold;">
					<td colspan="2"></td>
					<td style="text-align: right;"><?=number_format($totalQty,0,'.',',');?></td>
					<td style="text-align: right;"><?=number_format($totalPack,0,'.',',');?></td>
					</tr>
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
            <div class="box-footer clearfix">
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
