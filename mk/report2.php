<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
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
      Customer Report
        <small>Customer Report management</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Main Menu</a></li>
        <li class="active">Report</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        
<!-- Day 8 0:09:09  till 27:50-->

      <!-- Your Page Content Here -->
      
 
 
      <div class="box box-primary">
        <div class="box-header with-border">
        <h3 class="box-title">รายงาน</h3>
        
        </div><!-- /.box-header -->
        <div class="box-body">
          
            <div id="container" style="width:100%; height:400px;">
                
                
                
                
            </div>
            
            
           
    
        </div><!-- /.box-body -->
  <div class="box-footer">
      
      
    <!--The footer of the box -->
  </div><!-- box-footer -->
</div><!-- /.box -->

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
<script src="plugins/highcharts-5.0.12/code/highcharts.js"></script>
<script src="plugins/highcharts-5.0.12/code/modules/exporting.js"></script>

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

var chart1; // globally available
$(function() {
       chart1 = Highcharts.stockChart('container', {
         rangeSelector: {
            selected: 1
         },
         series: [{
            name: 'USD to EUR',
            data: usdtoeur // predefined JavaScript array
         }]
      });
   });
</script>

<!-- Optionally, you can add Slimscroll and FastClick plugins.
     Both of these plugins are recommended to enhance the
     user experience. Slimscroll is required when using the
     fixed layout. -->

</html>
