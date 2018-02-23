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
		Salesman All
        <small>Salesman all</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Main</a></li>
        <li class="active">Salesman all</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
		<div class="row"><div class="col-md-12">
			<div class="box"><div class="box-body">
			<form id="frmPeriod" method="get" class="form-inline" >
				<input type="hidden" name="id" value="<?= $_GET['id']; ?>" />
				<label>Year : </label>
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
				<label>Month : </label>
				<select name="month" class="form-control">
					<option value="" <?php echo ($month==""?'selected':''); ?> >--All--</option>
					<?php
					$sql = "SELECT `id`, `abb`, `name`, `abb_eng`, `name_eng` FROM month";
					$stmt = $pdo->prepare($sql);
					$stmt->execute();					
					while ($row = $stmt->fetch()){
						$selected=($month==$row['id']?'selected':'');
						if($month==$row['id']) $monthName=$row['abb_eng'];							
						echo '<option value="'.$row['id'].'" '.$selected.'>'.$row['abb_eng'].'</option>';
					}
					?>
				</select>
				<button type="submit"  class="form-control">Submit</button>				
			  </form>
			  </div>
			  <!-- box body -->
			  </div>
			  <!-- box-->
			  </div>
			  <!-- col-md-12 -->
		</div>
		<!-- row-->
      <!-- Your Page Content Here -->
	  <div class="row">
        <div class="col-md-12">
          <div class="box">
            <div class="box-header with-border">              
			  <form id="frmPeriod" method="get">
				<label class="box-title">Actual Amount By Salesman [Year: <span style="color: red"><?=$year;?></span>, Month: <span style="color: red"><?=$monthName;?></span>]</label>				
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
				
				<?php		
							$sql = "SELECT sm.id, sm.code, sm.name , sm.surname, sm.photo
									, IFNULL(sum(oh.`netTotal`),1) as netTotal
									FROM salesman sm
									LEFT JOIN order_header oh on oh.statusCode='P'
										and sm.code=oh.smCode
										and year(oh.orderDate)=:year ".($month<>""?"and month(oh.orderDate)=:month":"")."									
									WHERE 1
									GROUP BY sm.id, sm.code, sm.name , sm.surname, sm.photo
									ORDER BY 6 desc
									LIMIT 1
									";					
							$stmt = $pdo->prepare($sql);
							$stmt->bindParam(':year', $year);
							($month<>""?$stmt->bindParam(':month', $month):"");
							$stmt->execute();
							$row = $stmt->fetch();
							$maxNetTotal = $row['netTotal'];
							
							$sql = "SELECT sm.id, sm.code, sm.name , sm.surname, sm.photo
									, IFNULL(sum(oh.`netTotal`),0) as netTotal
									FROM salesman sm
									LEFT JOIN order_header oh on oh.statusCode='P'
										and sm.code=oh.smCode
										and year(oh.orderDate)=:year ".($month<>""?"and month(oh.orderDate)=:month":"")."									
									WHERE 1
									
									GROUP BY sm.id, sm.code, sm.name , sm.surname, sm.photo
									ORDER BY 6 desc
									";					
							$stmt = $pdo->prepare($sql);
							$stmt->bindParam(':year', $year);
							($month<>""?$stmt->bindParam(':month', $month):"");
							$stmt->execute();
							?>
							
							<?php
							$row_no = 1; while ($row = $stmt->fetch()) { 
							$netTotal = $row['netTotal'];
							$percentTotal = $netTotal/$maxNetTotal*100;
						?>
							<div class="row">
								<div class="col-md-1" style="text-align: center;">
									<a class="users-list-name" href="salesman_view.php?id=<?= $row['id']; ?>">
										<img class="img-circle" src="dist/img/<?= $row['photo']; ?>" alt="User Image" width="50"></a>
								</div>
								<div class="col-md-11">
									<div class="progress-group">
										<span class="progress-text"><?= $row['name'].' '.$row['surname']; ?> <?=number_format($percentTotal, 2, '.', ',');?>%</span>
										<span class="progress-number"><b><?=number_format($netTotal, 2, '.', ',');?></b>/<?=number_format($maxNetTotal, 2, '.', ',');?></span>

										<div class="progress sm">
										  <div class="progress-bar progress-bar-aqua" style="width: <?=$percentTotal;?>%"></div>
										</div>
									</div>
								</div>								
							 </div><!-- /.row -->
							<?php } ?>
				
                  </div> 
			<!-- box-body-->
		</div> 
		<!-- box-->
	</div> 
	<!-- col-->
</div> 
<!-- row-->
	  
	  
	
	
  
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
                          	INNER JOIN customer cm1 on tg.custCode=cm1.code AND cm1.smCode=:smCode
                         	WHERE tg.month=a.id AND tg.year=:yearBudget)as sumBudget
                        ,(SELECT IFNULL(sum(tg.Forecast),0)/1000 FROM target_cust tg
                          	INNER JOIN customer cm1 on tg.custCode=cm1.code AND cm1.smCode=:smCode2
                         	WHERE tg.month=a.id AND tg.year=:yearForecast )as sumForecast
						, IFNULL(sum(b.total),0)/1000 as sumActual
						FROM `month` a                        
						LEFT JOIN `order_header` b on month(b.orderDate)=a.id and b.statusCode='P' and b.smCode=:smCode3
							and year(b.orderDate)=:year
						WHERE 1
						GROUP BY a.id, a.abb_eng
						";
		$stmt = $pdo->prepare($sql);
		$stmt->bindParam(':smCode', $smCode);
		$stmt->bindParam(':smCode2', $smCode);
		$stmt->bindParam(':smCode3', $smCode);
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
		credit:{
			enable: false
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
            data: [<?php echo implode(",", $sumForecast); ?>],
            //data: [1, 0, 4]
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
                enabled: true,
				inside: true,
				rotation: 270,
				y: -50,
				style: {
                    fontWeight: 'bold'
                },
                format: '{point.y:,.0f} Baht'
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
            text: <?php echo $year; ?>
        },
		credit:{
			enable: false
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
