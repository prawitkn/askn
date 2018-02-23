<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<?php 
	$year=2017;
	$month=7;
?>
<?php include 'head.php'; ?>
    
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
        Welcome to Marketing Department
        <small>Mr. <?php echo $s_userFullname; ?> [ ID: <?php echo $_SESSION['userID']; ?>] </small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Main Page</a></li>
        <li class="active">Here</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

      <!-- Your Page Content Here -->
      <div class="row">
           <div class="col-md-3 col-sm-6 col-xs-12">
               <div class="info-box">
                   <span class="info-box-icon bg-aqua"><i class="fa fa-user"></i></span>
                   <div class="info-box-content"> 
                       <?php
                        $sql_user ="SELECT COUNT(*) AS count_user FROM user";
                        $result_user = mysqli_query($link, $sql_user);
                        $count_user = mysqli_fetch_assoc($result_user);
                        ?>
                       <span class="info-box-text"> Number of persons login</span>
                       <span class="info-box-number"><?= $count_user['count_user']; ?> <small> xx  .</small></span>
                   </div><!-- /.info-box-content -->
               </div> <!-- /.info-box -->
            </div> <!-- /.col --> 
            
            <div class="col-md-3 col-sm-6 col-xs-12">
               <div class="info-box">
                   <span class="info-box-icon bg-red"><i class="fa fa-newspaper-o"></i></span>
                   <div class="info-box-content"> 
                       
                       <span class="info-box-text"> Number of News </span>
                       <span class="info-box-number"> 40,000</span>
                       
                   </div><!-- /.info-box-content -->
               </div> <!-- /.info-box -->
            </div> <!-- /.col --> 
            
            <div class="clearfix visible-sm-block"></div>
            
             <div class="col-md-3 col-sm-6 col-xs-12">
               <div class="info-box">
                   <span class="info-box-icon bg-green"><i class="ion ion-ios-cart-outline"></i></span>
                   <div class="info-box-content"> 
                       
                       <span class="info-box-text"> Number of Orders </span>
                       <span class="info-box-number"> 760</span>
                       
                   </div><!-- /.info-box-content -->
               </div> <!-- /.info-box -->
            </div> <!-- /.col --> 
            
            <div class="col-md-3 col-sm-6 col-xs-12">
               <div class="info-box">
                   <span class="info-box-icon bg-yellow"><i class="ion ion-ios-people-outline"></i></span>
                   <div class="info-box-content"> 
                       <?php
                        $sql_cust ="SELECT COUNT(*) AS COUNTCUST FROM customer";
                        $result_cust = mysqli_query($link, $sql_cust);
                        $count_cust = mysqli_fetch_assoc($result_cust);
                        ?>
                       <span class="info-box-text"> Number of customers </span>
                       <span class="info-box-number"><?= $count_cust['COUNTCUST']; ?> <small>   .</small></span>
                   </div><!-- /.info-box-content -->
               </div> <!-- /.info-box -->
            </div> <!-- /.col --> 
         </div> <!-- /.row -->   
  

	<div class="row">
        <div class="col-md-12">
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Monthly Recap Report</h3>

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
					
                  <div id="container" style="width:100%; height:400px;">
				  
					</div>
				  <!-- /.container -->
				  
                </div>
                <!-- /.col -->
				
				
				
				
                <div class="col-md-4">
					<div id="container2" style="width:100%; height:400px;">
				  
					</div>
                  <!-- /.container -->
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->
            </div>  
<!-- Day8 00:05:45-->            
    
    
    
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Main Footer -->
  <?php include'footer.php'; ?>
  
  
</div>
<!-- ./wrapper -->

<!-- REQUIRED JS SCRIPTS -->

<!-- jQuery 2.2.3 -->
<script src="plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="bootstrap/js/bootstrap.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/app.min.js"></script>
<!-- Hightchart -->
<script src="Highcharts-5.0.12/code/highcharts.js"></script>
<script src="Highcharts-5.0.12/code/modules/exporting.js"></script>




<!-- Optionally, you can add Slimscroll and FastClick plugins.
     Both of these plugins are recommended to enhance the
     user experience. Slimscroll is required when using the
     fixed layout. -->
</body>


 <?php
        $sql_rpt = "SELECT `month`,sum(`forecast`) as forecast, sum(`actual`) as actual 
					FROM `t_sales_monthly` 
					WHERE 1
					and `year`=".$year." 
					group by `month`
					";
        $result_rpt = mysqli_query($link, $sql_rpt);
        $arrMonth = array();
        $arrForecast = array();
        $arrActual = array();
        while($row = mysqli_fetch_assoc($result_rpt)){
            $arrMonth[] = $row['month'];
            $arrForecast[] = $row['forecast'];
			$arrActual[] = $row['actual'];
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
            text: 'Forecast VS Actual '+<?php echo $year; ?>
        },
		tooltip: {
			pointFormat: '{series.name} <b>{point.y:,.0f}</b> Baht'
		},
		plotOptions: {
			area: {
				//pointStart: 1940,
				marker: {
					enabled: false,
					symbol: 'circle',
					radius: 2,
					states: {
						hover: {
							enabled: true
						}
					}
				}
			}
		},
        xAxis: {
            
            //categories: ['Apples', 'Bananas', 'Oranges'],
            categories: [<?php echo "'" . implode("','", $arrMonth) . "'"; ?>]
                        //'prod5','prod6','prod7'
        },
        yAxis: {
            title: {
                text: 'จำนวนเงิน'
            }
        },
        series: [{
            name: 'Forecast',
            data: [<?php echo implode(",", $arrForecast); ?>],
            //data: [1, 0, 4]
            dataLabels: {
                //enabled: true,
                //format: '{y} ชิ้น'
            }
        },
             {
                name: 'Actual',
                data: [<?php echo implode(",", $arrActual); ?>],
             }
        ]
    });
});
</script>



<?php
        $sql_rpt = "SELECT a.name, IFNULL(sum(b.forecast),0) as forecast, IFNULL(sum(b.actual),0) as actual,
					IFNULL(IFNULL(sum(b.actual),0) / IFNULL(sum(b.forecast),0) * 100,0) as actualPercent
					FROM m_salesman a
					left join t_sales_monthly b on a.id=b.salesmanID and b.year=".$year."  
					group by a.name
					";
        $result_rpt = mysqli_query($link, $sql_rpt);
        $xForecast = array();
		$xActual = array();
		$xActualPercent = array();
        $ySalesman = array();
        while($row = mysqli_fetch_assoc($result_rpt)){			
            $xForecast[] = $row['forecast'];
			$xActual[] = $row['actual'];
			$xActualPercent[] = $row['actualPercent'];
            $ySalesman[] = $row['name'];
        }
  ?>
<script>
    $(function () { 
    var myChart2 = Highcharts.chart('container2', {
        chart: {
            type: 'bar'
        },
        data: {
            decimalPoint: "."
        },
        title: {
            text: 'Salesman Actual (%) '+<?php echo $year; ?>
        },
		tooltip: {
			pointFormat: '{categories} <b>{point.y:,.0f}</b> %'
		},
		legend: {
			enabled: false,
		},
        xAxis: {
            categories: [<?php echo "'" . implode("','", $ySalesman) . "'"; ?>]
        },
        yAxis: {
            title: {
                text: '%'
            },
			visible: false
        },
        series: [
					/*{
						name: 'Forecast',
						data: [<?php echo implode(",", $xForecast); ?>],
						dataLabels: {
							//enabled: true,
							//format: '{y} ชิ้น'
						}
					},
					{
						name: 'Actual',
						data: [<?php echo implode(",", $xActual); ?>],
					},*/
					{
					name: 'PerActual',
					data: [<?php echo implode(",", $xActualPercent); ?>],
					dataLabels: {
							enabled: true,
							format: '{point.x.name} {y:,.0f} %'
						}
					}
				]
    });
});
</script>


</html>
