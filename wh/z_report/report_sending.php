<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<?php 
	include 'head.php'; /*$s_userFullname = $row_user['userFullname'];
        $s_userPicture = $row_user['userPicture'];
		$s_username = $row_user['userName'];
		$s_userGroupCode = $row_user['userGroupCode'];
		$s_userDeptCode = $row_user['userDeptCode'];
		$s_userID=$_SESSION['userID'];*/
	
	$rootPage="report_sending2";
	
	$dateFrom = (isset($_GET['dateFrom'])?$_GET['dateFrom']:'');
	$dateTo = (isset($_GET['dateTo'])?$_GET['dateTo']:'');
	$fromCode = (isset($_GET['fromCode'])?$_GET['fromCode']:'');
	$toCode = (isset($_GET['toCode'])?$_GET['toCode']:'');
	$prodCode = (isset($_GET['prodCode'])?$_GET['prodCode']:'');
	$prodId = (isset($_GET['prodId']) ?$_GET['prodId']:'');	
	if($prodCode=="") $prodId="";

	$dateFrom = str_replace('/', '-', $dateFrom);
	$dateTo = str_replace('/', '-', $dateTo);
	$dateFromYmd=$dateToYmd="";
	if($dateFrom<>""){ $dateFromYmd = date('Y-m-d', strtotime($dateFrom));	}
	if($dateTo<>""){ $dateToYmd =  date('Y-m-d', strtotime($dateTo));	}
?>    

<div class="wrapper">
  <!-- Main Header -->
  <?php include 'header.php'; 
  ?>  
  
  <!-- Left side column. contains the logo and sidebar -->
   <?php include 'leftside.php'; ?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
		Sending Report
        <small>Report</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Main</a></li>
        <li class="active">Report</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
	
      <!-- Your Page Content Here -->
    <div class="box box-primary">
        <div class="box-header with-border">
        <h3 class="box-title">Sending Report</h3>		
		
        <div class="box-tools pull-right">
          <!-- Buttons, labels, and many other things can be placed here! -->
          <!-- Here is a label for example -->
          <?php
				 $sql = "SELECT count(DISTINCT prd.code, prd.description) as countTotal
				FROM `send` hdr
				INNER JOIN send_detail dtl on dtl.sdNo=hdr.sdNo
				INNER JOIN product_item itm on itm.prodItemId=dtl.prodItemId  
				LEFT JOIN sloc fsl on hdr.fromCode=fsl.code
				LEFT JOIN sloc tsl on hdr.toCode=tsl.code					
				LEFT JOIN product prd on prd.id=itm.prodCodeId  
				WHERE 1 ";

				if($dateFrom<>""){ $sql .= " AND hdr.sendDate>=:dateFrom ";	}
				if($dateTo<>""){ $sql .= " AND hdr.sendDate<=:dateTo ";	}		
				if($fromCode<>""){ $sql .= " AND hdr.fromCode=:fromCode ";	}
				if($toCode<>""){ $sql .= " AND hdr.toCode=:toCode ";	}			
				if($prodCode<>""){ $sql .= " AND prd.code like :prodCode ";	}	
				if($prodId<>""){ $sql .= " AND prd.id=:prodId ";	}	
				$sql .= "AND hdr.statusCode='P' ";
				$sql.="GROUP BY prd.code, prd.description ";

				$stmt = $pdo->prepare($sql);
				if($dateFrom<>""){ $stmt->bindParam(':dateFrom', $dateFromYmd );	}
				if($dateTo<>""){ $stmt->bindParam(':dateTo', $dateToYmd );	}
				if($fromCode<>""){ $stmt->bindParam(':fromCode', $fromCode );	}
				if($dateFrom<>""){ $stmt->bindParam(':dateFrom', $dateFrom );	}
				if($toCode<>""){ $stmt->bindParam(':toCode', $toCode );	}
				if($prodCode<>""){ $tmp='%'.$prodCode.'%'; $stmt->bindParam(':prodCode', $tmp );	}
				if($prodId<>""){ $stmt->bindParam(':prodId', $prodId );	}
				$stmt->execute();	
			
                $countTotal = $stmt->fetch()['countTotal'];
				
				$rows=20;
				$page=0;
				if( !empty($_GET["page"]) and isset($_GET["page"]) ) $page=$_GET["page"];
				if($page<=0) $page=1;
				$total_data=$countTotal;
				$total_page=ceil($total_data/$rows);
				if($page>=$total_page) $page=$total_page;
				$start=($page-1)*$rows;
				if($page==0) $start=0;
          ?>
		  
          <span class="label label-primary">Total <?php echo $countTotal; ?> items</span>
        </div><!-- /.box-tools -->
        </div><!-- /.box-header -->
        <div class="box-body">
			<div class="row" style="margin-bottom: 5px;">
			<!--<div class="col-md-12">
				<ul class="nav nav-tabs">
					<li class="active" ><a href="#divSearch" toggle="tab">Search</a></li>
					<li class="" ><a href="#divResult" toggle="tab">Result</a></li>
				</ul>
				
				<div class="tab-content clearfix">
				<div class="tab-pane active" id="divSearch">a
				</div>
				<div class="tab-pane" id="divResult">b
				</div>
				</div>
				
			</div>-->
			<div class="col-md-12">					
                    <form id="form1" action="#" method="get" class="form-inline"  style="background-color: gray; padding: 5px;"  novalidate>
                    	<label for="dateFrom">Date From : </label>
						<input type="text" id="dateFrom" name="dateFrom" value="" class="form-control datepicker" data-smk-msg="Require From Date." required >

						<label for="dateTo">Date To : </label>
						<input type="text" id="dateTo" name="dateTo" value="" class="form-control datepicker" data-smk-msg="Require To Date." required >

						<label>From : </label>
					<select name="fromCode" class="form-control">
						<option value="" <?php echo ($fromCode==""?'selected':''); ?> >--All--</option>
						<?php
						$sql = "SELECT `code`, `name` FROM sloc WHERE statusCode='A' ORDER BY code ASC ";
						$stmt = $pdo->prepare($sql);
						$stmt->execute();					
						while ($row = $stmt->fetch()){
							$selected=($fromCode==$row['code']?'selected':'');						
							echo '<option value="'.$row['code'].'" '.$selected.'>'.$row['code'].' : '.$row['name'].'</option>';
						}
						?>
					</select>	

					<label>To : </label>
					<select name="toCode" class="form-control">
						<option value="" <?php echo ($toCode==""?'selected':''); ?> >--All--</option>
						<?php
						$sql = "SELECT `code`, `name` FROM sloc WHERE statusCode='A' ORDER BY code ASC ";
						$stmt = $pdo->prepare($sql);
						$stmt->execute();					
						while ($row = $stmt->fetch()){
							$selected=($toCode==$row['code']?'selected':'');						
							echo '<option value="'.$row['code'].'" '.$selected.'>'.$row['code'].' : '.$row['name'].'</option>';
						}
						?>
					</select>		
					
					<label for="prodCode">Product Code : </label>
					<input type="hidden" name="prodId" id="prodId" class="form-control" value="<?=$prodId;?>"  />
					<input type="text" name="prodCode" id="prodCode" class="form-control" value="<?=$prodCode;?>"  />
					<a href="#" name="btnSdNo" class="btn btn-default" ><i class="glyphicon glyphicon-search" ></i> </a>
                    </form>

                    <a name="btnSubmit" id="btnSubmit" href="#" class="btn btn-primary"><i class="glyphicon glyphicon-search"></i> Search</a>
                </div>   
				<!--col-md-12-->
				
			</div>
			<!--row-->
			
			<div class="table-responsive">
                <table class="table table-hover no-margin">
                  <thead>
                  <tr>
					<th>No.</th>
                    <th>Product Code</th>
					<th>Description</th>
					<th>Grade</th>
					<th>Box/Roll</th>
					<th>Piece/Length/KG</th>
                  </tr>
                  </thead>
                  <tbody>
					<?php
						 $sql = "SELECT prd.code, prd.description, itm.grade, count(itm.prodItemId) as qty, sum(itm.qty) as totalQty 
						FROM `send` hdr
						INNER JOIN send_detail dtl on dtl.sdNo=hdr.sdNo
						INNER JOIN product_item itm on itm.prodItemId=dtl.prodItemId  
						LEFT JOIN sloc fsl on hdr.fromCode=fsl.code
						LEFT JOIN sloc tsl on hdr.toCode=tsl.code
						LEFT JOIN product prd on prd.id=itm.prodCodeId  
						WHERE 1 ";

						if($dateFrom<>""){ $sql .= " AND hdr.sendDate>=:dateFrom ";	}
						if($dateTo<>""){ $sql .= " AND hdr.sendDate<=:dateTo ";	}		
						if($fromCode<>""){ $sql .= " AND hdr.fromCode=:fromCode ";	}
						if($toCode<>""){ $sql .= " AND hdr.toCode=:toCode ";	}			
						if($prodCode<>""){ $sql .= " AND prd.code like :prodCode ";	}	
						if($prodId<>""){ $sql .= " AND prd.id=:prodId ";	}	
						$sql .= "AND hdr.statusCode='P' ";

						$sql.="GROUP BY prd.code, prd.description ";
						$sql.="ORDER BY hdr.createTime DESC
						LIMIT $start, $rows 
						";
						$stmt = $pdo->prepare($sql);
						if($dateFrom<>""){ $stmt->bindParam(':dateFrom', $dateFromYmd );	}
						if($dateTo<>""){ $stmt->bindParam(':dateTo', $dateToYmd );	}
						if($fromCode<>""){ $stmt->bindParam(':fromCode', $fromCode );	}
						if($dateFrom<>""){ $stmt->bindParam(':dateFrom', $dateFrom );	}
						if($toCode<>""){ $stmt->bindParam(':toCode', $toCode );	}
						if($prodCode<>""){ $tmp='%'.$prodCode.'%'; $stmt->bindParam(':prodCode', $tmp );	}
						if($prodId<>""){ $stmt->bindParam(':prodId', $prodId );	}
						$stmt->execute();	
						$rowCount=$stmt->rowCount();
						if($rowCount==0){ ?>
							<tr>
								<td colspan="6" style="text-align: center; color: red; font-weight: bold;" >Data not found.</td>
			                </tr><?php
						}
				   ?>             
					
					
						<?php $c_row=($start+1); while ($row = $stmt->fetch() ) { 
							$gradeName='';
							switch($row['grade']){
								case '0' : $gradeName='A'; break;
								case '1' : $gradeName='B'; break;
								case '2' : $gradeName='N'; break;
								default : $gradeName='N/A';
							}
					?>
                  <tr>
					<td><?= $c_row; ?></td>
					<td><?= $row['code']; ?></td>
					<td><?= $row['description']; ?></td>
					<td><?= $gradeName; ?></td>
					<td style="text-align: right;"><?= number_format($row['qty'],0,'.',','); ?></td>
					<td style="text-align: right;"><?= number_format($row['totalQty'],2,'.',','); ?></td>
                </tr>
                 <?php $c_row +=1; } ?>
                  </tbody>
                </table>
              </div>
              <!-- /.table-responsive -->
			  
			<?php $condQuery="?dateFrom=".$dateFrom."&dateTo=".$dateTo."&fromCode=".$fromCode."&toCode=".$toCode."&prodCode=".$prodCode."&prodId=".$prodId; ?>

             <?php if($rowCount>0){    ?>
               <div class="col-md-12">
			<a href="<?=$rootPage;?>_xls.php<?=$condQuery;?>" class="btn btn-default pull-right"><i class="glyphicon glyphicon-print"></i> Export Summary</a>

			<a href="<?=$rootPage;?>_dtl_xls.php<?=$condQuery;?>" class="btn btn-default pull-right" style="margin-right: 5px;"><i class="glyphicon glyphicon-print"></i> Export Detail</a>

			
			<nav>
			<ul class="pagination">				
				
				<li <?php if($page==1) echo 'class="disabled"'; ?> >
					<a href="<?=$rootPage;?>.php<?=$condQuery;?>.&=page=<?= $page-1; ?>" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a>
				</li>
				<?php for($i=1; $i<=$total_page;$i++){ ?>
				<li <?php if($page==$i) echo 'class="active"'; ?> >
					<a href="<?=$rootPage;?>.php<?=$condQuery;?>&page=<?= $i?>" > <?= $i;?></a>			
				</li>
				<?php } ?>
				<li <?php if($page==$total_page) echo 'class="disabled"'; ?> >
					<a href="<?=$rootPage;?>.php<?=$condQuery;?>&page=<?=$page+1?>" aria-labels="Next"><span aria-hidden="true">&raquo;</span></a>
				</li>
			</ul>
			</nav>
			</div>
		<?php } ?>

        </div><!-- /.box-body -->
  <div class="box-footer">
 




 <!-- Modal -->
<div id="modal_search_person" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Search Product</h4>
      </div>
      <div class="modal-body">
        <div class="form-horizontal">
			<div class="form-group">	
				<label for="txt_search_word" class="control-label col-md-2">Product Code </label>
				<div class="col-md-4">
					<input type="text" class="form-control" id="txt_search_word" />
				</div>
			</div>
		
		<table id="tbl_search_person_main" class="table">
			<thead>
				<tr bgcolor="4169E1" style="color: white; text-align: center;">
					<td>#Select</td>
					<td style="display: none;">ID</td>
					<td>Product Code.</td>
					<td>Product Name</td>
					<td style="display: none;">UOM</td>
					<td>Product Category</td>
					<td>App ID</td>
					<td style="display: none;">Balance</td>
					<td style="display: none;">Sales</td>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
		
		</form>
		<div id="div_search_person_result">
		</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
      </div>
    </div>

  </div>
</div>


     
    
    <!--The footer of the box -->
  </div><!-- box-footer -->
</div><!-- /.box -->

	<div id="spin"></div>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Main Footer -->
  <?php include'footer.php'; ?>  
  
</div>
<!-- ./wrapper -->

<!-- jQuery 2.2.3 -->
<!--Deprecation Notice: The jqXHR.success(), jqXHR.error(), and jqXHR.complete() callbacks are removed as of jQuery 3.0. 
    You can use jqXHR.done(), jqXHR.fail(), and jqXHR.always() instead.-->
<script src="plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="bootstrap/js/bootstrap.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/app.min.js"></script>
<!-- smoke validate -->
<script src="bootstrap/js/smoke.min.js"></script>
<!-- Add Spinner feature -->
<script src="bootstrap/js/spin.min.js"></script>


<script> 		
$(document).ready(function() {    
	//.ajaxStart inside $(document).ready to start and stop spiner.  
	$( document ).ajaxStart(function() {
		$("#spin").show();
	}).ajaxStop(function() {
		$("#spin").hide();
	});
	//.ajaxStart inside $(document).ready END

	$("#title").focus();
	var spinner = new Spinner().spin();
	$("#spin").append(spinner.el);
	$("#spin").hide();	

	//SEARCH Begin
	function modalShow(data){
		$('#tbl_search_person_main tbody').empty();
		$.each($.parseJSON(data), function(key,value){
			$('#tbl_search_person_main tbody').append(
			'<tr>' +
				'<td>' +
				'	<div class="btn-group">' +
				'	<a href="javascript:void(0);" data-name="search_person_btn_checked" ' +
				'	class="btn" title="เลือก"> ' +
				'	<i class="glyphicon glyphicon-ok"></i> เลือก</a> ' +
				'	</div>' +
				'</td>' + 
				'<td style="display: none;">'+ value.prodId +'</td>' +
				'<td>'+ value.prodCode +'</td>' +
				'<td>'+ value.prodName +'</td>' +
				'<td style="display: none;">'+ value.prodUomCode +'</td>' +
				'<td>'+ value.prodCatName +'</td>' +
				'<td>'+ value.prodAppName+'</td>' +									
				'<td style="display: none;">'+ value.balance+'</td>' +	
				'<td style="display: none;">'+ value.sales+'</td>' +	
			'</tr>'
			);		
		});
		$('#modal_search_person').modal('show');	
	}
	//SEARCH Begin
	$('a[name="btnSdNo"]').click(function(){
		curName = $(this).prev().attr('name');
		curId = $(this).prev().prev().attr('name');
		if(!$('#'+curName).prop('disabled')){
			$('#modal_search_person').modal('show');
		}
	});	
	$('#txt_search_word').keyup(function(e){ 
		if(e.keyCode == 13)
		{
			var params = {
				search_word: $('#txt_search_word').val()
			};
			if(params.search_word.length < 3){
				alert('search word must more than 3 character.');
				return false;
			}
			/* Send the data using post and put the results in a div */
			  $.ajax({
				  url: "search_product_ajax.php",
				  type: "post",
				  data: params,
				datatype: 'json',
				  success: function(data){	
						data=$.parseJSON(data);
						switch(data.rowCount){
							case 0 : alert('Data not found.');
								return false; break;
							default : 
								modalShow(data.data);	
						}	
				  }   
				}).error(function (response) {
					alert(response.responseText);
				});  
		}/* e.keycode=13 */	
	});
	
	$(document).on("click",'a[data-name="search_person_btn_checked"]',function() {
		$('input[name='+curId+']').val($(this).closest("tr").find('td:eq(1)').text());
		$('input[name='+curName+']').val($(this).closest("tr").find('td:eq(2)').text());
						
		$('#modal_search_person').modal('hide');
		//getList();
	});
	//Search End

	$('#prodCode').keyup(function(e){ 
		if(e.keyCode == 13)
		{
			var params = {
				search_word: $('#prodCode').val()
			};
			if(params.search_word.length < 3){
				alert('search word must more than 3 character.');
				return false;
			} 
			curName = $(this).attr('name');
			curId = $(this).prev().attr('name');
			//alert(curId);
			/* Send the data using post and put the results in a div */
			  $.ajax({
				  url: "search_product_ajax.php",
				  type: "post",
				  data: params,
				datatype: 'json',
				  success: function(data){	//alert(data);
						data=$.parseJSON(data);
						switch(data.rowCount){
							case 0 : alert('Data not found.');
								//$('#tbl_items tbody').empty();
								return false; break;
							case 1 :
								$.each($.parseJSON(data.data), function(key,value){
									$('input[name='+curName+']').val(value.prodCode);
									$('input[name='+curId+']').val(value.prodId);
								});
								//getList();
								break;
							default : 
								modalShow(data.data);
						}	
				  }   
				}).error(function (response) {
					alert(response.responseText);
				});  
		}/* e.keycode=13 */	
	});
	
	$("#btnSubmit").click(function(){ 
		$('#form1').submit();
	});

});
</script>

<link href="bootstrap-datepicker-custom-thai/dist/css/bootstrap-datepicker.css" rel="stylesheet" />
<script src="bootstrap-datepicker-custom-thai/dist/js/bootstrap-datepicker-custom.js"></script>
<script src="bootstrap-datepicker-custom-thai/dist/locales/bootstrap-datepicker.th.min.js" charset="UTF-8"></script>

<script>
	$(document).ready(function () {
		$('.datepicker').datepicker({
			daysOfWeekHighlighted: "0,6",
			autoclose: true,
			format: 'dd/mm/yyyy',
			todayBtn: true,
			language: 'en',             //เปลี่ยน label ต่างของ ปฏิทิน ให้เป็น ภาษาไทย   (ต้องใช้ไฟล์ bootstrap-datepicker.th.min.js นี้ด้วย)
			thaiyear: false              //Set เป็นปี พ.ศ.
		});  //กำหนดเป็นวันปัจุบัน
		//กำหนดเป็น วันที่จากฐานข้อมูล		
		<?php if($dateFromYmd<>"") { ?>
			var queryDate = '<?=$dateFromYmd;?>',
			dateParts = queryDate.match(/(\d+)/g)
			realDate = new Date(dateParts[0], dateParts[1] - 1, dateParts[2]); 
			$('#dateFrom').datepicker('setDate', realDate);
		<?php }else{ ?> $('#dateFrom').datepicker('setDate', '0'); <?php } ?>
		//จบ กำหนดเป็น วันที่จากฐานข้อมูล
		
		//กำหนดเป็น วันที่จากฐานข้อมูล		
		<?php if($dateToYmd<>"") { ?>
			var queryDate = '<?=$dateToYmd;?>',
			dateParts = queryDate.match(/(\d+)/g)
			realDate = new Date(dateParts[0], dateParts[1] - 1, dateParts[2]); 
			$('#dateTo').datepicker('setDate', realDate);
		<?php }else{ ?> $('#dateTo').datepicker('setDate', '0'); <?php } ?>
		//จบ กำหนดเป็น วันที่จากฐานข้อมูล
		
		
	});
</script>

</body>
</html>
