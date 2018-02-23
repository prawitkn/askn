<?php
  //  include '../db/database.php';
  include 'inc_helper.php';
?>
<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<?php include 'head.php'; ?>
<!-- iCheck for checkboxes and radio inputs -->
<link rel="stylesheet" href="plugins/iCheck/all.css">

<div class="wrapper">

  <!-- Main Header -->
  <?php include 'header.php'; ?>
  <?php $soNo = $_GET['soNo']; ?>
  
  <!-- Left side column. contains the logo and sidebar -->
   <?php include 'leftside.php'; ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
       Receive Information
        <small>Receive management</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="sale.php"><i class="fa fa-list"></i>Receive List</a></li>
		<li><a href="sale_item.php?soNo=<?=$soNo;?>"><i class="fa fa-edit"></i>RC No.<?=$soNo;?></a></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
		<?php
			$rcNo = $_GET['rcNo'];
			$sql = "SELECT rc.`rcNo`, rc.`refNo`, rc.`receiveDate`, rc.`fromCode`, rc.`remark`, rc.`statusCode`
			, rc.`createTime`, rc.`createByID`, rc.`confirmTime`, rc.`confirmById`, rc.`approveTime`, rc.`approveById` 
			, fsl.name as fromName, tsl.name as toName
			, d.userFullname as createByName
			, rc.confirmTime, cu.userFullname as confirmByName
			, rc.approveTime, au.userFullname as approveByName
			FROM `receive` rc 
			LEFT JOIN sloc fsl on rc.fromCode=fsl.code 
			LEFT JOIN sloc tsl on rc.toCode=tsl.code 
			left join user d on rc.createByID=d.userID
			left join user cu on rc.confirmByID=cu.userID
			left join user au on rc.approveByID=au.userID
			WHERE 1
			AND rc.rcNo=:rcNo 					
			ORDER BY rc.createTime DESC
			LIMIT 1
					
			";
			$stmt = $pdo->prepare($sql);			
			$stmt->bindParam(':rcNo', $rcNo);	
			$stmt->execute();
			$hdr = $stmt->fetch();			
			$rcNo = $hdr['rcNo'];
	   ?> 
      <!-- Your Page Content Here -->
    <div class="box box-primary">
        <div class="box-header with-border">
			<h3 class="box-title">View Receive No : <b><?= $rcNo; ?></b></h3>
			<div class="box-tools pull-right">
				<?php $statusName = '<b style="color: red;">Unknown</b>'; switch($hdr['statusCode']){
					case 'A' : $statusName = '<b style="color: red;">Incompleate</b>'; break;
					case 'B' : $statusName = '<b style="color: blue;">Begin</b>'; break;
					case 'C' : $statusName = '<b style="color: blue;">Confirmed</b>'; break;
					case 'P' : $statusName = '<b style="color: green;">Approved</b>'; break;
					default : 
				} ?>
				<h3 class="box-title" id="statusName">Status : <?= $statusName; ?></h3>
			</div><!-- /.box-tools -->
        </div><!-- /.box-header -->
        <div class="box-body">
			<input type="hidden" id="rcNo" value="<?= $rcNo; ?>" />
            <div class="row">				
					<div class="col-md-3">
						From : <br/>
						<b><?= $hdr['fromName']; ?></b><br/>
					</div><!-- /.col-md-3-->	
					<div class="col-md-3">
						To : <br/>
						<b><?= $hdr['toName']; ?></b><br/>
					</div><!-- /.col-md-3-->	
					<div class="col-md-3">
						Receive Date : <br/>
						<b><?= $hdr['receiveDate']; ?></b><br/>
					</div>	<!-- /.col-md-3-->	
					<div class="col-md-3">
					</div>	<!-- /.col-md-3-->	
			</div> <!-- row add items -->
		
			<div class="row"><!-- row show items -->
				<div class="box-header with-border">
				<h3 class="box-title">Product List</h3>
				<div class="box-tools pull-right">
				  <!-- Buttons, labels, and many other things can be placed here! -->
				  <!-- Here is a label for example -->
				  <?php
						$sql = "SELECT id FROM receive_detail
								WHERE rcNo=:rcNo 
									";						
						$stmt = $pdo->prepare($sql);	
						$stmt->bindParam(':rcNo', $hdr['rcNo']);
						$stmt->execute();	
						$rowCount = $stmt->rowCount();
				  ?>
				  <span class="label label-primary">Total <?php echo $rowCount; ?> items</span>
				</div><!-- /.box-tools -->
				</div><!-- /.box-header -->
				<div class="box-body">
				   <?php
						$sql = "
								SELECT dtl.*
								FROM `receive_detail` dtl
								LEFT JOIN product b on dtl.prodCode=b.code
								WHERE 1
								AND dtl.`rcNo`=:rcNo 
						";
						$stmt = $pdo->prepare($sql);	
						$stmt->bindParam(':rcNo', $hdr['rcNo']);
						$stmt->execute();	
				   ?>	
					<table class="table table-striped">
						<tr>
							<th>No.</th>
							<th>barcode</th>
							<th>Product Code</th>
							<th>Qty</th>
							<th>Remark</th>
							<th>Shelf</th>
						</tr>
						<?php $row_no=1; while ($row = $stmt->fetch()) { ?>
						<tr>
							<td style="text-align: center;"><?= $row_no; ?></td>							
							<td><?= $row['prodCode']; ?></td>
							<td><?= $row['barcode']; ?></td>
							<td style="text-align: right;"><?= number_format($row['qty'],0,'.',','); ?></td>
							<td><?= $row['remark']; ?></td>
							<td>
								<input type="hidden" id="hid_shelf_code_<?=$row_no;?>" />
								<label id="lbl_shelf_name_<?=$row_no;?>"><?=$row['shelfNo'];?></label><button name="" class="btn btn-default btn_set_shelf" data-id="<?=$row['id'];?>">...</button>
							</td>						
						</tr>
						<?php $row_no+=1; } ?>
					</table>
				</div><!-- /.box-body -->
	</div><!-- /.row add items -->

			
			
          
    
    </div><!-- /.box-body -->
  <div class="box-footer">
    <div class="col-md-12">
		<?php if($hdr['statusCode']=='P'){ ?>
          <a href="invoice-print.html" target="_blank" class="btn btn-default"><i class="glyphicon glyphicon-print"></i> Print</a>
		<?php } ?>
	
		
		  
		 	  
          <button type="button" id="btn_verify" class="btn btn-primary pull-right" style="margin-right: 5px;" <?php echo ($hdr['statusCode']=='P'?'':'disabled'); ?> >
            <i class="glyphicon glyphicon-ok"></i> Verify
          </button>      
		  </button>   
	</div><!-- /.col-md-12 -->
  </div><!-- box-footer -->
</div><!-- /.box -->







<!-- Modal -->
<div id="search_modal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Warehouse Shelf Search.</h4>
      </div>
      <div class="modal-body">
        <form id="formSearch" class="form-inline">			
			Warehouse : 
			<select name="search_sloc" id="search_ddl_sloc" class="form-control">
				<option value="WH1" selected>WH1 : Warehouse 1</option>
				<option value="WH2">WH2 : Warehouse 2</option>
			</select>
			<a id="search_btn_submit" class="btn btn-default">Submit</a>
		</form>
		<div class="table-responsive">
			<table id="search_tbl_main" class="table table-striped">
				<thead>
					<tr bgcolor="4169E1" style="color: white; text-align: center;">
						<td>#</td>
						<td>Code</td>
						<td>Name</td>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
		<!--div table-responsive-->
      
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
      </div>
    </div>

  </div>
</div>











<div id="spin"></div>

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

<script src="bootstrap/js/smoke.min.js"></script>

<!-- Add Spinner feature -->
<script src="bootstrap/js/spin.min.js"></script>
<!-- Add _.$ jquery coding -->
<script src="..\asset\js\underscore-min.js"></script>

<script> 
// to start and stop spiner.  
$( document ).ajaxStart(function() {
	$("#spin").show();
}).ajaxStop(function() {
	$("#spin").hide();
});
//   

$(document).ready(function() {
// Append and Hide spinner.          
var spinner = new Spinner().spin();
$("#spin").append(spinner.el);
$("#spin").hide();
//           
$('#btn_verify').click (function(e) {				 
	var params = {					
	rcNo: $('#rcNo').val()			
	};
	//alert(params.hdrID);
	$.smkConfirm({text:'Are you sure to Verify ?',accept:'Yes', cancel:'Cancel'}, function (e){if(e){
		$.post({
			url: 'receive_confirm_ajax.php',
			data: params,
			dataType: 'json'
		}).done(function(data) {
			if (data.success){  
				$.smkAlert({
					text: data.message,
					type: 'success',
					position:'top-center'
				});		
				location.reload();
			}else{
				$.smkAlert({
					text: data.message,
					type: 'danger',
					position:'top-center'
				});
			}
			//e.preventDefault();		
		}).error(function (response) {
			alert(response.responseText);
		});
		//.post		
	}});
	//smkConfirm
});
//.btn_click

	$("html,body").scrollTop(0);
	$("#statusName").fadeOut('slow').fadeIn('slow').fadeOut('slow').fadeIn('slow');
});
</script>





<!-- search modal dialog box. -->
<script>
	var hid_code = "";
	var lbl_name = "";
	$(document).ready(function(){
		$('.btn_set_shelf').click(function(){
			hid_code = $(this).prev().prev().attr('id');
			lbl_name = $(this).prev().attr('id');
			
			//show modal.
			$('#search_modal').modal('show');
		});	
		
		$('#search_modal').on('shown.bs.modal', function () {
			//$('#txt_search_fullname').focus();
		});
		$(document).on("click",'a[data-name="search_btn_checked"]',function() {
			$('#'+hid_code).val($(this).attr('attr-id'));
			$('#'+lbl_name).text($(this).closest("tr").find('td.search_td_name').text());
			
			//hide modal.
			$('#search_modal').modal('hide');
		});
		$('#search_btn_submit').click(function(e){
			/*var params = {
				search_sloc: $('#search_ddl_sloc').val()
			};
			if(params.search_fullname.length < 3){
				alert('search name surname must more than 3 character.');
				return false;
			}*/
			/* Send the data using post and put the results in a div */
			  $.ajax({
				  url: "search_shelf_ajax.php",
				  type: "post",
				  data: $('#formSearch').serialize(),
				  //data: params,
				datatype: 'json',
				  success: function(data){	
							if(data.success){
								console.log(data);
								console.log(data.rows);
								//alert(data);
								$('#search_tbl_main tbody').empty();
								_.each(data.rows, function(v){										
									$('#search_tbl_main tbody').append(										
										'<tr>' +
											'<td>' +
											'	<div class="btn-group">' +
											'	<a href="javascript:void(0);" data-name="search_btn_checked" ' +
											'   attr-id="'+v['code']+'" '+
											'	class="btn" title="เลือก"> ' +
											'	<i class="glyphicon glyphicon-ok"></i> เลือก</a> ' +
											'	</div>' +
											'</td>' +
											'<td class="search_td_name">'+ v['name'] +'</td>' +
										'</tr>'
									);			
								}); 
							}else{
								alert('data.success = '+data.success);
							}
				  }, //success
				  error:function(response){
					  alert('error');
					  alert(response.responseText);
				  }		  
				}); 
		});
	});	
</script>
<!-- search modal dialog box. END -->







<!-- Optionally, you can add Slimscroll and FastClick plugins.
     Both of these plugins are recommended to enhance the
     user experience. Slimscroll is required when using the
     fixed layout. -->
</body>
</html>
