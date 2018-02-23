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
                   <span class="info-box-icon bg-green"><i class="ion ion-ios-cart-outline"></i></span>
                   <div class="info-box-content"> 
                       <?php
                        $sql_box1 ="SELECT COUNT(*) as countOrder
									FROM t_sales_order_header
									WHERE year(order_date)=".$year."								
									";
                        $result_box1 = mysqli_query($link, $sql_box1);
                        $count_box1 = mysqli_fetch_assoc($result_box1);
                        ?>
                       <span class="info-box-text"> Number of Orders </span>
                       <span class="info-box-number"><?= number_format($count_box1['countOrder'], 0, '.', ','); ?> Orders.</span>
                       
                   </div><!-- /.info-box-content -->
               </div> <!-- /.info-box -->
            </div> <!-- /.col --> 
            
            <div class="col-md-3 col-sm-6 col-xs-12">
               <div class="info-box">
                   <span class="info-box-icon bg-aqua"><i class="ion ion-arrow-graph-up-right"></i></span>
                   <div class="info-box-content"> 
                       <?php
                        $sql_box2 ="SELECT SUM(b.amount) AS sumAmount 
									FROM t_sales_order_header a
									inner join t_sales_order_detail b on a.ID=b.hdrID
									WHERE 1
									AND year(a.order_date)=".$year."
									";
                        $result_box2 = mysqli_query($link, $sql_box2);
                        $count_box2 = mysqli_fetch_assoc($result_box2);
                        ?>
                       <span class="info-box-text"> Amount of Orders </span>					   
                       <span class="info-box-number"><?= number_format($count_box2['sumAmount'], 2, '.', ','); ?> <small> Baht</small></span>
                   </div><!-- /.info-box-content -->
               </div> <!-- /.info-box -->
            </div> <!-- /.col --> 
            
            <div class="clearfix visible-sm-block"></div>
            
             <div class="col-md-3 col-sm-6 col-xs-12">
               <div class="info-box">
                   <span class="info-box-icon bg-yellow"><i class="fa fa-calendar"></i></span>
                   <div class="info-box-content"> 
                       <?php
                        $sql3 ="SELECT SUM(forecast) as sumForecast, 
									(SELECT SUM(Amount) FROM t_sales_order_header a, t_sales_order_detail b
										WHERE a.ID=b.hdrID
										AND year(a.order_date)=".$year."
										AND month(a.order_date)=".$month.") as sumActual
									FROM t_sales_monthly
									WHERE year=".$year."	
									and month=".$month." 
									";
                        $result3 = mysqli_query($link, $sql3);
                        $box3 = mysqli_fetch_assoc($result3);
                        ?>
                       <span class="info-box-text"> Forecast Monthly</span>
					   <span class="info-box-number"><?= number_format($box3['sumForecast'], 0, '.', ','); ?> Baht</span>
                       <span class="info-box-number"><?= number_format(($box3['sumActual']/$box3['sumForecast'])*100, 2, '.', ','); ?>%</span>
                   </div><!-- /.info-box-content -->
               </div> <!-- /.info-box -->
            </div> <!-- /.col --> 
            
            <div class="col-md-3 col-sm-6 col-xs-12">
               <div class="info-box">
                   <span class="info-box-icon bg-red"><i class="ion ion-ios-pricetag"></i></span>
                   <div class="info-box-content"> 
                       <?php
                        $sql4 ="SELECT SUM(forecast) as sumForecast, sum(actual) as sumActual
									FROM t_sales_monthly
									WHERE year=".$year."	
									";
                        $result4 = mysqli_query($link, $sql4);
                        $box4 = mysqli_fetch_assoc($result4);
                        ?>
                       <span class="info-box-text"> Forecast Yearly</span>
					   <span class="info-box-number"><?= number_format($box4['sumForecast'], 0, '.', ','); ?> Baht</span>
                       <span class="info-box-number"><?= number_format(($box4['sumActual']/$box4['sumForecast'])*100, 2, '.', ','); ?>%</span>
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
				  
				  
                </div>
                <!-- /.col -->
				
				
				
				
                <div class="col-md-4">
                  <p class="text-center">
                    <strong>Goal Completion</strong>
                  </p>

                  <div class="progress-group">
                    <span class="progress-text">Add Products to Cart</span>
                    <span class="progress-number"><b>160</b>/200</span>

                    <div class="progress sm">
                      <div class="progress-bar progress-bar-aqua" style="width: 80%"></div>
                    </div>
                  </div>
                  <!-- /.progress-group -->
                  <div class="progress-group">
                    <span class="progress-text">Complete Purchase</span>
                    <span class="progress-number"><b>310</b>/400</span>

                    <div class="progress sm">
                      <div class="progress-bar progress-bar-red" style="width: 80%"></div>
                    </div>
                  </div>
                  <!-- /.progress-group -->
                  <div class="progress-group">
                    <span class="progress-text">Visit Premium Page</span>
                    <span class="progress-number"><b>480</b>/800</span>

                    <div class="progress sm">
                      <div class="progress-bar progress-bar-green" style="width: 80%"></div>
                    </div>
                  </div>
                  <!-- /.progress-group -->
                  <div class="progress-group">
                    <span class="progress-text">Send Inquiries</span>
                    <span class="progress-number"><b>250</b>/500</span>

                    <div class="progress sm">
                      <div class="progress-bar progress-bar-yellow" style="width: 80%"></div>
                    </div>
                  </div>
                  <!-- /.progress-group -->
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
        $sql_rpt = "SELECT p.prodID, p.prodName, IFNULL(sum(s.qty),0) as sumQty, IFNULL(sum(s.total),0) as sumTotal
                    FROM product p
                    left join `sales` s on p.prodID=s.prodID
                    group by p.prodID";
        $result_rpt = mysqli_query($link, $sql_rpt);
        $prodName = array();
        $sumQty = array();
        $sumTotal = array();
        while($row = mysqli_fetch_assoc($result_rpt)){
            $prodName[] = $row['prodName'];
            $sumQty[] = $row['sumQty'];
            $sumTotal[] = $row['sumTotal'];
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
            text: 'จำนวนสินค้า'
        },
        xAxis: {
            
            //categories: ['Apples', 'Bananas', 'Oranges'],
            categories: [<?php echo "'" . implode("','", $prodName) . "'"; ?>]
                        //'prod5','prod6','prod7'
        },
        yAxis: {
            title: {
                text: 'จำนวนสินค้า'
            }
        },
        series: [{
            name: 'จำนวนสินค้า',
            data: [<?php echo implode(",", $sumQty); ?>],
            //data: [1, 0, 4]
            dataLabels: {
                //enabled: true,
                //format: '{y} ชิ้น'
            }
        },
             {
                name: 'EST',
                data: [10, 10, 15, 15, 20, 3000]
             }
        ]
    });
});
</script>


</html>
