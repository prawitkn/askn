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
		Product Set Target
      </h1>
      <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Main</a></li>
        <li class="active">Product Set Target</li>
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
				<label class="box-title">Product Set Target</label>			
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
						<form id="form1" action="delivery_add_insert.php" method="post" class="form" novalidate>				
						<div class="col-md-12">   
							<div class="row">
								<div class="col-md-3">
									<div class="from-group">
									<?php
									$year = date('Y');
	$month = "0";//date('m');
	$monthName = "All";
	if(isset($_GET['year'])) $year = $_GET['year'];
	if(isset($_GET['month'])) $month = $_GET['month'];
	?>
									<label for="soNo">Year</label>
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
									</div>
									<!--from group-->
									
									
									<div class="from-group">
									<label for="prodCode">Product Code</label>
											<input type="hidden" name="hdr_id" id="hdr_id" value="<?= $_GET['hdr_id']; ?>" />
											<input type="hidden" name="id" id="id" value="" />
											<input type="text" class="form-control search_prod" name="prodCode" id="prodCode" data-smk-msg="Require Name" required>	
											
									</div>
									<!--from group-->
								</div>		
								<!--col-md-6-->							
							</div>	
							<!--row-->
				<!--row-->
				<div class="row">
					<div class="col-md-12">		
						<a name="btn_search" href="#" class="btn btn-default"><i class="glyphicon glyphicon-search" ></i> Search</a>
						<a name="btn_create" href="#" class="btn btn-default"><i class="glyphicon glyphicon-plus" ></i> Create</a>
					</div>
				</div>
				<!--row-->
							
						</div>
						<!-- col-md-6 --> 
								
						
						
						

					
					</form>			
					</div>  
					
            </div>  
<!-- Day8 00:05:45-->            
    
	
	
	<!-- Main row -->
      <div class="row">
        <!-- Left col -->
        <div class="col-md-12">
          
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
					$sql = "SELECT `id`, `year`, `month`, `prodCode`, `budget_qty`, `budget`, `forecast_qty`, `forecast`, `actual_qty`, `actual` 
					FROM `target_prod`
					WHERE 1
					AND year=:year
					
					ORDER BY prodCode, year, month 
					LIMIT 20 
					";
					$result = mysqli_query($link, $sql);
					$stmt = $pdo->prepare($sql);
					$stmt->bindParam(':year', $year);
					$stmt->execute();	
					
				?>
              <div class="table-responsive">
                <table class="table no-margin">
                  <thead>
                  <tr>
                    <th>Prod Code</th>
                    <th>JAN</th>
					<th>FEB</th>
					<th>MAR</th>
					<th>APL</th>
					<th>MAY</th>
					<th>JUN</th>
					<th>JUL</th>
					<th>AUG</th>
					<th>SEP</th>
					<th>OCT</th>
					<th>NOV</th>
					<th>DEC</th>
                  </tr>
                  </thead>
                  <tbody>
				  <?php $row_no = 1; while ($row = $stmt->fetch()) { 
						?>
                  <tr>
					<td><?= $row['prodCode']; ?></td>
						<td>
							QTY Budget : <input type="textbox" /></br>
							Amount Budget : <input type="textbox" /></br>
							QTY Forecast : <input type="textbox" /></br>
							Amount Forecast : <input type="textbox" /></br>
						</td> 
						<td>
							QTY Budget : <input type="textbox" /></br>
							Amount Budget : <input type="textbox" /></br>
							QTY Forecast : <input type="textbox" /></br>
							Amount Forecast : <input type="textbox" /></br>
						</td> 
						<td>
							QTY Budget : <input type="textbox" /></br>
							Amount Budget : <input type="textbox" /></br>
							QTY Forecast : <input type="textbox" /></br>
							Amount Forecast : <input type="textbox" /></br>
						</td> 
						<td>
							QTY Budget : <input type="textbox" /></br>
							Amount Budget : <input type="textbox" /></br>
							QTY Forecast : <input type="textbox" /></br>
							Amount Forecast : <input type="textbox" /></br>
						</td> 
						<td>
							QTY Budget : <input type="textbox" /></br>
							Amount Budget : <input type="textbox" /></br>
							QTY Forecast : <input type="textbox" /></br>
							Amount Forecast : <input type="textbox" /></br>
						</td>
						<td>
							QTY Budget : <input type="textbox" /></br>
							Amount Budget : <input type="textbox" /></br>
							QTY Forecast : <input type="textbox" /></br>
							Amount Forecast : <input type="textbox" /></br>
						</td>
						<td>
							QTY Budget : <input type="textbox" /></br>
							Amount Budget : <input type="textbox" /></br>
							QTY Forecast : <input type="textbox" /></br>
							Amount Forecast : <input type="textbox" /></br>
						</td> 
						<td>
							QTY Budget : <input type="textbox" /></br>
							Amount Budget : <input type="textbox" /></br>
							QTY Forecast : <input type="textbox" /></br>
							Amount Forecast : <input type="textbox" /></br>
						</td>
						<td>
							QTY Budget : <input type="textbox" /></br>
							Amount Budget : <input type="textbox" /></br>
							QTY Forecast : <input type="textbox" /></br>
							Amount Forecast : <input type="textbox" /></br>
						</td> 
						<td>
							QTY Budget : <input type="textbox" /></br>
							Amount Budget : <input type="textbox" /></br>
							QTY Forecast : <input type="textbox" /></br>
							Amount Forecast : <input type="textbox" /></br>
						</td>
						<td>
							QTY Budget : <input type="textbox" /></br>
							Amount Budget : <input type="textbox" /></br>
							QTY Forecast : <input type="textbox" /></br>
							Amount Forecast : <input type="textbox" /></br>
						</td> 
						<td>
							QTY Budget : <input type="textbox" /></br>
							Amount Budget : <input type="textbox" /></br>
							QTY Forecast : <input type="textbox" /></br>
							Amount Forecast : <input type="textbox" /></br>
						</td> 
					<td></td>
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

      </div>
      <!-- /.row second box col8 & col 4 -->
	  










<!-- Modal -->
<div id="modal_search_product" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Product Search</h4>
      </div>
      <div class="modal-body">
        <div class="form-horizontal">			
			<div class="form-group">	
				<label for="year_month" class="control-label col-md-2">Product Code/Name</label>
				<div class="col-md-4">
					<input type="text" class="form-control" id="txt_search_product" />
				</div>
			</div>		
		<table id="tbl_search_product_main" class="table">
			<thead>
				<tr bgcolor="4169E1" style="color: white; text-align: center;">
					<td>Action</td>
					<td>Product Code</td>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
		</form>
		<div id="div_search_product_result">
		</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
      </div>
    </div>

  </div>
</div>
<!--/.Modal-->










	  
	
	
  
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
  
  
  
  
<!-- Add _.$ jquery coding -->
<script src="plugins\jQueryUnderscore\underscore-min.js"></script>
<!-- search modal dialog box. -->
<script>
	var cur_hid_id = "";
	var cur_txt_search = "";
	$(document).ready(function(){
		$('.search_prod').click(function(){
			//.prev() and .next() count <br/> too.
			cur_hid_id = $(this).prev().attr('id');	
			cur_txt_search = $(this).attr('id');
			//cur_hid_ta_id = $(this).next().attr('id');			
			//show modal.
			$('#modal_search_product').modal('show');
		});	
		
		$('#modal_search_product').on('shown.bs.modal', function () {
			$('#txt_search_product').focus();
		});
		$(document).on("click",'a[data-name="search_product_btn_checked"]',function() {
			$('#'+cur_hid_id).val($(this).attr('attr-id'));
			$('#'+cur_txt_search).val($(this).closest("tr").find('td.search_td_code').text());
			//$('#'+cur_txt_position_act_name_id).val($(this).closest('tr').find('td.search_td_position_act_name').text());
			//hide modal.
			$('#modal_search_product').modal('hide');
		});
		$('#txt_search_product').keyup(function(e){
			if(e.keyCode == 13)
			{
				var params = {
					search_org_code: '',
                    search_fullname: $('#txt_search_product').val()					
                };
				if(params.search_fullname.length < 3){
					alert('Search must more than 3 character.');
					return false;
				}
				/* Send the data using post and put the results in a div */
				  $.ajax({
					  url: "search_product_modal_ajax.php",
					  type: "post",
					  data: params,
					datatype: 'json',
					  success: function(data){	
								if(data.success){
									console.log(data);
									console.log(data.rows);
									//alert(data);
									$('#tbl_search_product_main tbody').empty();
									_.each(data.rows, function(v){										
										$('#tbl_search_product_main tbody').append(										
											'<tr>' +
												'<td>' +
												'	<div class="btn-group">' +
												'	<a href="javascript:void(0);" data-name="search_product_btn_checked" ' +
												'   attr-id="'+v['id']+'" '+
												'	class="btn" title="เลือก"> ' +
												'	<i class="glyphicon glyphicon-ok"></i> เลือก</a> ' +
												'	</div>' +
												'</td>' +
												'<td class="search_td_fullname">'+ v['prodName'] +'</td>' +
											'</tr>'
										);			
									}); 
								}else{
									alert('data.success = '+data.success);
								}
					  }, //success
					  error:function(response){
						  alert(response.responseText);
					  }		  
					}); 
			}/* e.keycode=13 */	
		});
	});	
</script>
<!-- search modal dialog box. END -->
  
</html>




