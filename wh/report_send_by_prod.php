<?php
	include 'inc_helper.php'; 
?>
<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<?php 
	include 'head.php'; 
?>    
<div class="wrapper">
  <!-- Main Header -->
  <?php include 'header.php';   
	
	$rootPage="report_send_by_prod";
  ?>  
  
  <!-- Left side column. contains the logo and sidebar -->
   <?php include 'leftside.php'; ?>
   
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
		Send by Product Report
        <small>Send Report</small>
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
				$dateFrom = (isset($_GET['dateFrom'])?$_GET['dateFrom']: date('d-m-Y') );
				$dateTo = (isset($_GET['dateTo'])?$_GET['dateTo']: date('d-m-Y') );
				$prodId = (isset($_GET['prodId'])?$_GET['prodId']: '' );
				$prodCode = (isset($_GET['prodCode'])?$_GET['prodCode']: '' );
			
				$dateFrom = str_replace('/', '-', $dateFrom);
				$dateTo = str_replace('/', '-', $dateTo);
			
				if($dateFrom<>""){ $dateFrom = date('Y-m-d', strtotime($dateFrom));	}
				if($dateTo<>""){ $dateTo =  date('Y-m-d', strtotime($dateTo));	}
				
$sql = "
SELECT COUNT(hdr.sdNo) AS countTotal
FROM `send` hdr
INNER JOIN send_detail dtl ON dtl.sdNo=hdr.sdNo  
INNER JOIN product_item itm ON itm.prodItemId=dtl.prodItemId 
WHERE 1 ";
if($dateFrom<>""){ $sql .= " AND hdr.sendDate>=:dateFrom ";	}
if($dateTo<>""){ $sql .= " AND hdr.sendDate<=:dateTo ";	}
if($prodId<>""){ $sql .= " AND itm.prodCodeId=:prodId ";	}
switch($s_userGroupCode){ 
	case 'whOff' :  case 'whSup' : 
		$sql .= "AND hdr.fromCode IN ('8','E') "; break;
	case 'pdOff' :  case 'pdSup' :
			$sql .= "AND hdr.fromCode=:s_userDeptCode ";
		break;
	default : //case 'it' : case 'admin' : 
  }
$sql .= "AND hdr.statusCode='P' 
";				
                //$result = mysqli_query($link, $sql);
                //$countTotal = mysqli_fetch_assoc($result);
				
				$stmt = $pdo->prepare($sql);			
				if($prodId<>"") $stmt->bindParam(':prodId', $prodId);	
				if($dateFrom<>"") $stmt->bindParam(':dateFrom', $dateFrom);	
				if($dateTo<>"") $stmt->bindParam(':dateTo', $dateTo);	
				switch($s_userGroupCode){ 
					case 'pdOff' :  case 'pdSup' :
							if($s_userDeptCode<>"") $stmt->bindParam(':s_userDeptCode', $s_userDeptCode);
						break;
					default : //case 'it' : case 'admin' : 
				  }
				$stmt->execute();
				$countTotal = $stmt->fetch();	

				$rows=20;
				$page=0;
				if( !empty($_GET["page"]) and isset($_GET["page"]) ) $page=$_GET["page"];
				if($page<=0) $page=1;
				$total_data=$countTotal['countTotal'];
				$total_page=ceil($total_data/$rows);
				if($page>=$total_page) $page=$total_page;
				$start=($page-1)*$rows;
				if($page==0) $start=0;
          ?>
		  
          <span class="label label-primary">Total <?php echo $countTotal['countTotal']; ?> items</span>
        </div><!-- /.box-tools -->
        </div><!-- /.box-header -->
        <div class="box-body">
			<div class="row" style="margin-bottom: 5px;">
				<form id="form1" action="<?=$rootPage;?>.php" method="get" class="form-inline" novalidate>
				<div style="background-color: #ccffcc; padding: 5px;">
				<div class="col-md-12">					
	                
						<label for="dateFrom">Date From : </label>
						<input type="text" id="dateFrom" name="dateFrom" value="" class="form-control datepicker" data-smk-msg="Require From Date." required >
						<label for="dateTo">Date To : </label>
						<input type="text" id="dateTo" name="dateTo" value="" class="form-control datepicker" data-smk-msg="Require To Date." required >
						<label for="prodCode">Product Code</label> 
						<input type="hidden" name="prodId" id="prodId" class="form-control" value="<?=$prodId;?>"  />
						<input type="text" name="prodCode" id="prodCode" class="form-control" value="<?=$prodCode;?>"  />
						<a href="#" name="btnSdNo" class="btn btn-primary" ><i class="glyphicon glyphicon-search" ></i> </a>					
	                
	            </div>  
	            </div><!--BAGROUND COLOR-->  
	            <input type="submit" class="btn btn-default" value="ค้นหา">
	            </form>

			</div>
           <?php
 $sql = "SELECT hdr.`sdNo`, hdr.`refNo`, hdr.`sendDate`, hdr.`fromCode`, hdr.`toCode`, hdr.`remark`, hdr.`statusCode`
, hdr.`createTime`, hdr.`createByID`, hdr.`updateTime`, hdr.`updateById`, hdr.`confirmTime`, hdr.`confirmById`, hdr.`approveTime`, hdr.`approveById`
, fsl.name as fromName, tsl.name as toName 
, cu.userFullname as createByName, fu.userFullname as confirmByName, pu.userFullname as approveByName 
FROM `send` hdr
INNER JOIN send_detail dtl ON dtl.sdNo=hdr.sdNo  
INNER JOIN product_item itm ON itm.prodItemId=dtl.prodItemId 
LEFT JOIN sloc fsl on hdr.fromCode=fsl.code
LEFT JOIN sloc tsl on hdr.toCode=tsl.code
LEFT JOIN user cu on hdr.createByID=cu.userId 
LEFT JOIN user fu on hdr.confirmById=fu.userId
LEFT JOIN user pu on hdr.approveById=pu.userId  
WHERE 1 ";
if($dateFrom<>""){ $sql .= " AND hdr.sendDate>=:dateFrom ";	}
if($dateTo<>""){ $sql .= " AND hdr.sendDate<=:dateTo ";	}
if($prodId<>""){ $sql .= " AND itm.prodCodeId=:prodId ";	}
switch($s_userGroupCode){ 
	case 'whOff' :  case 'whSup' : 
			$sql .= "AND hdr.fromCode IN ('8','E') "; break;
	case 'pdOff' :  case 'pdSup' :
			$sql .= "AND hdr.fromCode=:s_userDeptCode ";
		break;
	default : //case 'it' : case 'admin' : 
}	  
$sql .= "AND hdr.statusCode='P' 

ORDER BY hdr.createTime DESC
LIMIT $start, $rows 
";
//echo $sql;
$stmt = $pdo->prepare($sql);			
if($prodId<>"") $stmt->bindParam(':prodId', $prodId);	
if($dateFrom<>"") $stmt->bindParam(':dateFrom', $dateFrom);	
if($dateTo<>"") $stmt->bindParam(':dateTo', $dateTo);	
switch($s_userGroupCode){ 
	case 'pdOff' :  case 'pdSup' :
			if($s_userDeptCode<>"") $stmt->bindParam(':s_userDeptCode', $s_userDeptCode);
		break;
	default : //case 'it' : case 'admin' : 
  }
$stmt->execute();
                         
           ?>             
			
				<div class="table-responsive">
				<table class="table table-striped">
				<thead>
				<tr>
					<th>No.</th>
					<th>Sending No.</th>
					<th>Date</th>
					<th>From</th>
					<th>To</th>
					<th>#</th>
				</tr>
				</thead>
				<tbody>
                <?php $c_row=($start+1); while ($row = $stmt->fetch() ) { 
					/*$isCloseName = '<label class="label label-danger">Unknown</label>';
					switch($row['isClose']){
						case 'N' : $isCloseName = '<label class="label label-warning">No</label>'; break;
						case 'Y' : $isCloseName = '<label class="label label-success">Yes</label>'; break;
						default : 						
					}*/
					?>
					<tr>
						<td><?=$c_row;?></td>
						<td><a href="send_view.php?sdNo=<?=$row['sdNo'];?>"><?=$row['sdNo'];?></a></td>
					<td><?=date('d M Y',strtotime( $row['sendDate'] ));?></td>
						<td><?=$row['fromName'];?></td>
						<td><?=$row['toName'];?></td>
						<td>#</td>
					</tr>
                <?php $c_row +=1; } ?>
				</tbody>
				</table>
				</div>
				<!--table-resposive-->
		
		<div class="col-md-12">
			<?php $pagingString = "?dateFrom=".$dateFrom."&dateTo=".$dateTo."&prodId=".$prodId;
			?>
			<a href="<?=$rootPage."_pdf.php".$pagingString;?>" class="btn btn-default pull-right" aria-label=".CSV"><span aria-hidden="true">
				<i class="glyphicon glyphicon-save-file"></i> PDF </span></a>			
				
			
			<nav>
			<ul class="pagination">
				<li <?php if($page==1) echo 'class="disabled"'; ?> >
					<a href="<?=$rootPage.'.php.'.$pagingString;?>&=page=<?= $page-1; ?>" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a>
				</li>
				<?php for($i=1; $i<=$total_page;$i++){ ?>
				<li <?php if($page==$i) echo 'class="active"'; ?> >
					<a href="<?=$rootPage.'.php.'.$pagingString;?>&page=<?= $i?>" > <?= $i;?></a>			
				</li>
				<?php } ?>
				<li <?php if($page==$total_page) echo 'class="disabled"'; ?> >
					<a href="<?=$rootPage.'.php.'.$pagingString;?>&page=<?=$page+1?>" aria-labels="Next"><span aria-hidden="true">&raquo;</span></a>
				</li>
			</ul>
			</nav>
			
		<div>
			
        </div><!-- /.box-body -->
  <div class="box-footer">
      
      
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
								$('#tbl_search_person_main tbody').empty();
								$.each($.parseJSON(data.data), function(key,value){
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
		getList(0);
	});
	//Search End
	
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
			var queryDate = '<?=$dateFrom;?>',
			dateParts = queryDate.match(/(\d+)/g)
			realDate = new Date(dateParts[0], dateParts[1] - 1, dateParts[2]); 
			$('#dateFrom').datepicker('setDate', realDate);
		<?php }else{ ?> $('#dateFrom').datepicker('setDate', '0'); <?php } ?>
		//จบ กำหนดเป็น วันที่จากฐานข้อมูล
		
		//กำหนดเป็น วันที่จากฐานข้อมูล		
		<?php if($dateToYmd<>"") { ?>
			var queryDate = '<?=$dateTo;?>',
			dateParts = queryDate.match(/(\d+)/g)
			realDate = new Date(dateParts[0], dateParts[1] - 1, dateParts[2]); 
			$('#dateTo').datepicker('setDate', realDate);
		<?php }else{ ?> $('#dateTo').datepicker('setDate', '0'); <?php } ?>
		//จบ กำหนดเป็น วันที่จากฐานข้อมูล
		
		
	});
</script>





</body>
</html>
