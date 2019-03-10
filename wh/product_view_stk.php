 
<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<?php include 'head.php'; ?>  

	<!-- fancybox CSS -->
	<link rel="stylesheet" type="text/css" href="plugins/fancybox-master/dist/jquery.fancybox.min.css">

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
		$sloc = ( isset($_GET['sloc']) ? $_GET['sloc'] : '' );

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
				<div class="col-md-3">
					<div class="col-md-12" style="text-align: center;">
						
						<a href="../images/product/<?php echo (empty($row['photo'])? 'default.jpg' : $row['photo']) ?> " data-fancybox="images" data-caption="<?=$row['code'];?>">
							<image src="../images/product/<?php echo (empty($row['photo'])? 'default.jpg' : $row['photo']) ?> " width="200" height="200" />
						</a>
					</div>				
	                <div class="col-md-12">
						<label>Product Name : </label>
						<?= $row['name']; ?><br/>
						<label>Description : </label>
						<?= $row['description']; ?><br/>						
	                </div><!-- /.col-10 -->
	         	</div>
	         	<!--/.col-md-8-->

				<div class="col-md-9">
					<h3>Avalible Stock by Length/Box/KG</h3>
					<?php									
						$sql = "select itm.prodCodeId as prodId, itm.qty, itm.grade, itm.gradeTypeId, itm.remarkWh 
,rh.toCode as sloc 
                        , pgt.name as gradeTypeName 
						, sum(itm.qty) as sumQty
from product prd
INNER JOIN product_item itm ON itm.prodCodeId=prd.id 
INNER JOIN receive_detail rd ON rd.prodItemId=itm.prodItemId AND rd.statusCode='A' 
INNER JOIN receive rh ON rh.rcNo=rd.rcNo AND rh.statusCode='P' ";
	if ( $sloc=="" ) { $sql.= "AND rh.toCode IN ('8','E') "; }else{ $sql .= "AND rh.toCode=:sloc "; }
$sql .= "LEFT JOIN product_item_grade_type pgt ON pgt.id=itm.gradeTypeId 
WHERE prd.id=:id 
GROUP BY itm.prodCodeId , itm.qty, itm.grade, itm.gradeTypeId, itm.remarkWh , rh.toCode
ORDER BY rh.toCode, itm.gradeTypeId, itm.remarkWh
						";
						$stmt = $pdo->prepare($sql);
						$stmt->bindParam(':id', $id);
						if ( $sloc=="" ){ 
							//donothing 
						}else{ 
							$stmt->bindParam(':sloc', $sloc ); 
						}
						
						$stmt->execute();
							?>
			              <div class="table-responsive">
			                <table class="table no-margin">
			                  <thead>			                  	
			                    <th style="text-align: center;">Location</th>
			                    <th style="text-align: center;">Length/Pieces/KG</th>
			                    <th style="text-align: center;">Grade</th>
			                    <th style="text-align: center;">Grade Type</th>
			                    <th style="text-align: center;">WH Remark</th>
			                    <th style="text-align: center;">Qty</th>
								<th style="text-align: center;">Qty Total</th>
			                  </tr>
			                  </thead>
			                  <tbody>
							  <?php $row_no = 1; $sumQtyTotal=0; while ($row = $stmt->fetch()) { 
							  	$gradeName = '<b style="color: red;">N/A</b>'; 
								switch($row['grade']){
									case 0 : $gradeName = 'A'; break;
									case 1 : $gradeName = '<b style="color: red;">B</b>'; $sumGradeNotOk+=1; break;
									case 2 : $gradeName = '<b style="color: red;">N</b>'; $sumGradeNotOk+=1; break;
									default : 
										$gradeName = '<b style="color: red;">N/a</b>'; $sumGradeNotOk+=1;
								} 
								?>
			                  <tr>			                  	
								<td style="text-align: center;"><?= $row['sloc']; ?></td>
								<td style="text-align: center;"><?= number_format($row['qty'],2,'.',','); ?></td>
								<td style="text-align: center;"><?= $gradeName; ?></td>
								<td style="text-align: center;"><?= $row['gradeTypeName']; ?></td>
								<td style="text-align: center;"><?= $row['remarkWh']; ?></td>
								<td style="text-align: center;"><?= number_format($row['sumQty']/$row['qty'],2,'.',','); ?></td>
								<td style="text-align: center;"><?= number_format($row['sumQty'],2,'.',','); ?></td>
			                </tr>
			                <?php $row_no+=1; $sumQtyTotal+=$row['sumQty']; } ?>
			                  </tbody>
			                  <tfoot>
			                  	<tr>
			                  		<td colspan="6" style="text-align: center; font-weight: bold;">Total</td>
			                  		<td style="text-align: center; font-weight: bold;"><?= number_format($sumQtyTotal,2,'.',','); ?></td>
			                  	</tr>
			                  </tfoot>
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
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Stock Balance VS Order Pendings</h3>

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
          		
		
		
          <!-- TABLE: LATEST ORDERS -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Pending Orders</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
			
			<?php	
					$sql = "
					SELECT soNo, deliveryDate, prodId, prodCode
					, sum(qty) as sumOrderQty, sum(sentQty) as sumSentQty 
					, customerName 
					FROM (
						SELECT hdr.soNo, dtl.deliveryDate
						, dtl.prodId, prd.code as prodCode
						, dtl.id, dtl.qty
						, (SELECT sum(xd.qty) FROM picking xh 
							INNER JOIN picking_detail xd ON xd.pickNo=xh.pickNo 
							WHERE xh.soNo=hdr.soNo AND xd.saleItemId=dtl.id
							AND xh.isFinish='Y' AND xh.statusCode='P') as sentQty  
						, cust.name as customerName 
						FROM `sale_header` hdr
						INNER JOIN sale_detail dtl ON dtl.soNo=hdr.soNo
						INNER JOIN product prd ON prd.id=dtl.prodId 
						INNER JOIN customer cust ON cust.id=hdr.custId
						";				
						if ($sloc<>"") $sql.="AND cust.locationCode=:sloc ";
						
						$sql .= "WHERE 1 
						AND hdr.statusCode='P' 
						AND hdr.isClose='N' 
						";
						if($id<>""){ $sql .= " AND dtl.prodId=:id ";	}		
						$sql .= "
					) as tmp ";
					$sql.="GROUP BY soNo, deliveryDate, prodId, prodCode ";
					$sql.="
					ORDER BY deliveryDate ASC 
					";
					//$sql .= "LIMIT $start, $rows ";
					$stmt = $pdo->prepare($sql);	
					$stmt->bindParam(':id', $id);
					switch ($sloc) {
						case '8': $tmp="L"; $stmt->bindParam(':sloc', $tmp ); break;		
						case 'E': $tmp="E"; $stmt->bindParam(':sloc', $tmp ); break;					
						default: break;
					}

					$stmt->execute();       
					
				?>
              <div class="table-responsive">
                <table class="table no-margin">
                  <thead>
                  <tr>
                    <th style="text-align: center;">Order No.</th>
                    <th style="text-align: center;">Customer</th>
                    <th style="text-align: center;">Delivery Date</th>
                    <th style="text-align: center;">Pending/Order</th>
                  </tr>
                  </thead>
                  <tbody>
				  <?php 
				  	$tmpRemainQty=$sumQtyTotal;
					$dateName = array();
					$remainQty = array();
			        $orderQty = array();

        			$row_no = 1; while ($row = $stmt->fetch()) { 
					 $pendingQty=$row['sumOrderQty']-$row['sumSentQty'];

					$dateNameStr="";
					$dt = new DateTime($row['deliveryDate']); 
					$dateNameStr=$dt->format('d M Y');

					 $dateName[] = $dateNameStr;
					 $remainQty[] = ($tmpRemainQty-$pendingQty);
					 $orderQty[] = $pendingQty;
					 $tmpRemainQty-=$pendingQty;
						?>
                  <tr>
                    <td style="text-align: center;"><a href="sale2_view.php?soNo=<?=$row['soNo'];?>" target="_blank"><?= $row['soNo']; ?></a></td>
                    <td style="text-align: center;"><?= $row['customerName']; ?></a></td>
                    <td style="text-align: center;"><?= $dateNameStr; ?></a></td>
					<td style="text-align: center;"><?= number_format($pendingQty,2,'.',',').' / '.number_format($row['sumOrderQty'],2,'.',','); ?></td>
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

<!-- Hightchart -->
<script src="plugins/highcharts-5.0.12/code/highcharts.js"></script>
<script src="plugins/highcharts-5.0.12/code/modules/exporting.js"></script>

<script>
$(function () { 
  Highcharts.setOptions({
    colors: ['red', 'green', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263', '#6AF9C4']
});

    var myChart = Highcharts.chart('container', {
        chart: {
        type: 'line'
    },
    title: {
        text: 'Stock Balance VS Order Pendings'
    },
    credits: {
        enabled: false
    },
    subtitle: {
        text: ''
    },
    xAxis: {
        allowDecimals: false,
        labels: {
            formatter: function () {
                return this.value; // clean, unformatted number for year
            }
        }
    },
    yAxis: {
        title: {
            text: 'Order Pending'
        },
        labels: {
            formatter: function () {
                return this.value / 1000 + 'k';
            }
        }
    },
    tooltip: {
        pointFormat: 'Quantity <b>{point.y:,.0f}</b>'
    },
        xAxis: {
            
            //categories: ['Apples', 'Bananas', 'Oranges'],
            categories: [<?php echo "'" . implode("','", $dateName) . "'"; ?>]
                        //'prod5','prod6','prod7'
        },
        yAxis: {
            title: {
                text: 'Quantity'
            }
        },
        series: [{
            name: 'Order',
            data: [<?php echo implode(",", $orderQty); ?>],
            //data: [1, 0, 4]
            dataLabels: {
                enabled: true,
                inside: false,
                rotation: 0,
                y: -50,
                style: {
                            fontWeight: 'bold'
                        },
                        format: 'Order {point.y:,.0f}'
                    }
       },{
            name: 'Balance',
            data: [<?php echo implode(",", $remainQty); ?>],
            //data: [1, 0, 4]
            dataLabels: {
                enabled: true,
                inside: false,
                rotation: 0,
                y: -50,
                style: {
                            fontWeight: 'bold'
                        },
                        format: 'Balance {point.y:,.0f} '
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
