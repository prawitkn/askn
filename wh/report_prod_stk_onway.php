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
	
	$rootPage="report_prod_stk_onway";
	
	$search_word = (isset($_GET['search_word'])?$_GET['search_word']:'');
	$toCode = (isset($_GET['sloc'])?$_GET['sloc']:'8');
	$prodId = (isset($_GET['prodId']) ?$_GET['prodId']:'');

	$sql = "
	SELECT code FROM product WHERE id=:id ";	
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':id', $prodId);
	$stmt->execute();
	$prodCode = $stmt->fetch()['code'];
?>    
 
</head>
<body class="hold-transition skin-green sidebar-mini">


	
  
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
		<h1><i class="glyphicon glyphicon-list"></i>
		Product Stock Report
        <small>Report</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Main</a></li>
        <li class="active">Product Stock Report</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
	
      <!-- Your Page Content Here -->
    <div class="box box-primary">
        <div class="box-header with-border">
        <h3 class="box-title">Product Stock Report  : <span style="color: blue;">[Onway] <?=$prodCode;?></span></h3>		
		
        <div class="box-tools pull-right">
          <!-- Buttons, labels, and many other things can be placed here! -->
          <!-- Here is a label for example -->
          <?php

				$sql = "
				SELECT COUNT(DISTINCT hdr.sdNo) AS countTotal
				FROM send hdr 
				INNER JOIN send_detail dtl ON dtl.sdNo=hdr.sdNo 
				INNER JOIN product_item itm ON itm.prodItemId=dtl.prodItemId 
					AND itm.prodCodeId=:prodId
				WHERE 1 
				AND hdr.toCode=:toCode 
				AND hdr.statusCode<>'P' 
				AND hdr.rcNo IS NULL  ";
				if(isset($_GET['search_word']) and $_GET['search_word']<>""){
					$sql .= "AND hdr.sdNo like :search_word ";		
				}
				$sql .= "GROUP BY hdr.sdNo ";		
				$stmt = $pdo->prepare($sql);


				switch($s_userGroupCode){ 	
					case 'pdOff' :
					case 'pdSup' :
						$stmt->bindParam(':s_userDeptCode', $s_userDeptCode);
						break;
					default :	// it, admin 
				}
				if( isset($_GET['search_word']) and $_GET['search_word']<>"" ){
					$tmp='%'.$search_word.'%';
					$stmt->bindParam(':search_word', $tmp);
				}
				$stmt->bindParam(':prodId', $prodId);
				$stmt->bindParam(':toCode', $toCode);
				$stmt->execute();
				$row = $stmt->fetch();
				$countTotal = $row['countTotal'];				
				
				/*if($search_word<>""){ $sql = "and (prd.code like '%".$search_word."%' OR prd.name like '%".$search_word."%') "; }
				if($sloc<>""){ $sql .= " AND sb.sloc='$sloc' ";	}
				if($catCode<>""){ $sql .= " AND catCode='$catCode' ";	}	
				if($prodId<>""){ $sql .= " AND prodId=$prodId ";	}*/
							
				$rows=20;
				$page=0;
				if( !empty($_GET["page"]) and isset($_GET["page"]) ) $page=$_GET["page"];
				if($page<=0) $page=1;
				$total_data=$countTotal;
				$total_page=ceil($total_data/$rows);
				//if($page>=$total_page) $page=$total_page;
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
                    <!--<form id="form1" action="#" method="get" class="form-inline"  style="background-color: gray; padding: 5px;"  novalidate>
						<label>SLOC : </label>
					<select name="sloc" class="form-control">
						<option value="" <?php echo ($sloc==""?'selected':''); ?> >--All--</option>
						<?php
						$sql = "SELECT `code`, `name` FROM sloc WHERE statusCode='A' AND code IN ('8','E') ORDER BY code ASC ";
						$stmt = $pdo->prepare($sql);
						$stmt->execute();					
						while ($row = $stmt->fetch()){
							$selected=($sloc==$row['code']?'selected':'');						
							echo '<option value="'.$row['code'].'" '.$selected.'>'.$row['code'].' : '.$row['name'].'</option>';
						}
						?>
					</select>	
					
						<label>Cat : </label>
					<select name="catCode" class="form-control">
						<option value="" <?php echo ($catCode==""?'selected':''); ?> >--All--</option>
						<?php
						$sql = "SELECT `code`, `name` FROM product_category WHERE statusCode='A'	ORDER BY code ASC ";
						$stmt = $pdo->prepare($sql);
						$stmt->execute();					
						while ($row = $stmt->fetch()){
							$selected=($catCode==$row['code']?'selected':'');						
							echo '<option value="'.$row['code'].'" '.$selected.'>'.$row['code'].' : '.$row['name'].'</option>';
						}
						?>
					</select>				
					
					<label for="prodCode">Product Code</label>
					<input type="hidden" name="prodId" id="prodId" class="form-control" value="<?=$prodId;?>"  />
					<input type="text" name="prodCode" id="prodCode" class="form-control" value="<?=$prodCode;?>"  />
					<a href="#" name="btnSdNo" class="btn btn-default" ><i class="glyphicon glyphicon-search" ></i> </a>
                    </form>

                    <a name="btnSubmit" id="btnSubmit" href="#" class="btn btn-primary"><i class="glyphicon glyphicon-search"></i> Search</a>
					-->
			</div>   
			<!--col-md-12-->
				
			</div>
			<!--row-->
			
			<div class="table-responsive">
                <table class="table no-margin">
                  <thead>
                  <tr>
					<th style="text-align: center;">No.</th>
                    <th style="text-align: center;">Sending No.</th>
					<th style="text-align: center;">Sending Date</th>					
					<th style="text-align: center;">Qty</th>
                  </tr>
                  </thead>
                  <tbody>
					<?php
						$sql = "
				SELECT hdr.sdNo, hdr.sendDate, SUM(itm.qty) as sumQty  
				FROM send hdr 
				INNER JOIN send_detail dtl ON dtl.sdNo=hdr.sdNo 
				INNER JOIN product_item itm ON itm.prodItemId=dtl.prodItemId 
					AND itm.prodCodeId=:prodId 
				WHERE 1 
				AND hdr.toCode=:toCode 
				AND hdr.statusCode<>'X' 
				AND hdr.rcNo IS NULL  ";
			
				if(isset($_GET['search_word']) and $_GET['search_word']<>""){
					$sql .= "AND hdr.sdNo like :search_word ";		
				}		

				
						
						/*if($search_word<>""){ $sql = "and (prd.code like '%".$search_word."%' OR prd.name like '%".$search_word."%') "; }
						if($sloc<>""){ $sql .= " AND sb.sloc='$sloc' ";	}
						if($catCode<>""){ $sql .= " AND catCode='$catCode' ";	}	
						if($prodId<>""){ $sql .= " AND prodId='$prodId' ";	}*/

						$sql .= "GROUP BY hdr.sdNo, hdr.sendDate ";
						$sql.="ORDER BY hdr.`createTime` DESC  ";
						$sql.="LIMIT $start, $rows ";
						$stmt = $pdo->prepare($sql);

						switch($s_userGroupCode){ 	
							case 'pdOff' :
							case 'pdSup' :
								$stmt->bindParam(':s_userDeptCode', $s_userDeptCode);
								break;
							default :	// it, admin 
						}
						if( isset($_GET['search_word']) and $_GET['search_word']<>"" ){
							$tmp='%'.$search_word.'%';
							$stmt->bindParam(':search_word', $tmp);
						}
						$stmt->bindParam(':prodId', $prodId);	
						$stmt->bindParam(':toCode', $toCode);	
						$stmt->execute();
						$rowCount=$stmt->rowCount();
						if($rowCount==0){ ?>
							<tr>
								<td colspan="8" style="text-align: center; color: red; font-weight: bold;" >Data not found.</td>
			                </tr><?php
						}else{
				   ?>             
					
					
						<?php $c_row=($start+1); $qtyTotal=0; while ($row = $stmt->fetch() ) { 
							$qtyTotal += $row['sumQty'];
							//$img = 'dist/img/product/'.(empty($row['photo'])? 'default.jpg' : $row['photo']);
					?>
                  <tr>
					<td style="text-align: center;"><?= $c_row; ?></td>
                    <td style="text-align: center;"><a href="send2_view.php?sdNo=<?=$row['sdNo'];?>" ><?= $row['sdNo']; ?></a></td>
					<td style="text-align: center;"><?= date('d M Y',strtotime( $row['sendDate'] )); ?></td>
					<td style="text-align: center;"><?= number_format($row['sumQty'],2,'.',','); ?></td>
                </tr>
                 <?php $c_row +=1; } ?>
                  </tbody>
                  <tfoot>
                  	<tr>
                  		<td colspan="3"></td>
                  		<td style="text-align: center; font-weight: bold;">
                  			<?= number_format($qtyTotal,2,'.',','); ?>
                  		</td>
                  	</tr>
                  </tfoot>
              <?php } //end if count ?>
                </table>
              </div>
              <!-- /.table-responsive -->
			  
			<?php $condQuery="?" ?>

             <?php if($rowCount>0){    ?>
               <div class="col-md-12">
			<!--
			<a href="<?=$rootPage;?>_xls.php?<?=$condQuery;?>" class="btn btn-default pull-right"><i class="glyphicon glyphicon-print"></i> Export</a>
			-->
			
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
		getList();
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

</body>
</html>
