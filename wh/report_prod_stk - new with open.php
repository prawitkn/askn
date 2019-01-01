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
	
	$rootPage="report_prod_stk";
	
	$dateFrom=$dateTo="";
	$dateFromYmd=$dateToYmd="";
	if(isset($_GET['dateFrom'])){
		$dateFrom=$_GET['dateFrom'];
		$dateArr = explode('/', $dateFrom);
	    $dateY = (int)$dateArr[2];
	    $dateM = $dateArr[1];
	    $dateD = $dateArr[0];
	    $dateFromYmd = $dateY . '-' . $dateM . '-' . $dateD;
	}else{
		$dateFrom=date('d/m/Y');
		$dateFromYmd=date('Y-m-d');
	}
	if(isset($_GET['dateTo'])){
		$dateTo=$_GET['dateTo'];
		$dateArr = explode('/', $dateTo);
	    $dateY = (int)$dateArr[2];
	    $dateM = $dateArr[1];
	    $dateD = $dateArr[0];
	    $dateToYmd = $dateY . '-' . $dateM . '-' . $dateD;
	}else{
		$dateTo=date('d/m/Y');
		$dateToYmd=date('Y-m-d');
	}

	$search_word = (isset($_GET['search_word'])?trim($_GET['search_word']):'');
	$sloc = (isset($_GET['sloc'])?$_GET['sloc']:'8');
	$catCode = (isset($_GET['catCode'])?$_GET['catCode']:'');
	//$prodId = (isset($_GET['prodId']) ?$_GET['prodId']:'');
	$prodCode = (isset($_GET['prodCode'])?trim($_GET['prodCode']):'');
	//if($prodCode=="") $prodId="";
?>    

 
</head>
<body class="hold-transition <?=$skinColorName;?> sidebar-mini">


	
  
<div class="wrapper">
  <!-- Main Header -->
  <?php include 'header.php'; 
  
	include 'inc_helper.php'; 
  ?>  
  
  <!-- Left side column. contains the logo and sidebar -->
   <?php include 'leftside.php'; ?>
   
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
		Report
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
        <h3 class="box-title">Product Stock Report</h3>		
		
        <div class="box-tools pull-right">
          <!-- Buttons, labels, and many other things can be placed here! -->
          <!-- Here is a label for example -->
          <?php

          	$pdo->beginTransaction();

          	$sql = "
          	CREATE TEMPORARY TABLE tmpStock (
          		`prodId` int(11) NOT NULL,
				  `sloc` varchar(10) NOT NULL,
				  `openAcc` decimal(10,2) NOT NULL,
				  `openTrans` decimal(10,2) NOT NULL,
				  `onway` decimal(10,2) NOT NULL,
				  `receive` decimal(10,2) NOT NULL,
				  `sent` decimal(10,2) NOT NULL,
				  `return` decimal(10,2) NOT NULL,
				  `delivery` decimal(10,2) NOT NULL,
				  `balance` decimal(10,2) NOT NULL,
				  `book` decimal(10,2) NOT NULL,
		      	PRIMARY KEY (`prodId`,`sloc`)
		    )";
          	$stmt = $pdo->prepare($sql);		
			$stmt->execute();

			$sql = "
	          INSERT INTO tmpStock (prodId, sloc)
	          SELECT prd.id, sl.code 
	          FROM product prd
	          CROSS JOIN sloc sl ON sl.code IN ('8','E')
          	";
          	$stmt = $pdo->prepare($sql);		
			$stmt->execute();

			//Open
			$sql = "UPDATE tmpStock hdr
	         ,(SELECT td.prodId, td.sloc, td.balance as sumQty FROM stk_closing_detail td 
	          				WHERE td.hdrId=(SELECT th.id FROM stk_closing th 
	          								WHERE th.statusCode='A' AND th.closingDate<='$dateFromYmd' LIMIT 1) 
	          				) as tmp 
	          SET hdr.openAcc=tmp.sumQty 
	          WHERE hdr.prodId=tmp.prodId AND hdr.sloc=tmp.sloc 
          	";
          	$stmt = $pdo->prepare($sql);		
			$stmt->execute();

			//Onway
			$sql = "UPDATE tmpStock hdr
	         ,(SELECT itm.prodCodeId, sh.toCode, SUM(itm.qty) as sumQty FROM product_item itm 
	          				INNER JOIN send_detail sd ON sd.prodItemId=itm.prodItemId  
	         				INNER JOIN send sh ON sh.sdNo=sd.sdNo AND sh.statusCode='P' AND sh.rcNo IS NULL 
	          				GROUP BY itm.prodCodeId, sh.toCode
	          				) as tmp 
	          SET hdr.onway=tmp.sumQty 
	          WHERE hdr.prodId=tmp.prodCodeId AND hdr.sloc=tmp.toCode 
          	";
          	$stmt = $pdo->prepare($sql);		
			$stmt->execute();

			//Receive
			$sql = "UPDATE tmpStock hdr
	         ,(SELECT itm.prodCodeId, th.toCode as fromCode, SUM(itm.qty) as sumQty FROM product_item itm 
	          				INNER JOIN receive_detail td ON td.prodItemId=itm.prodItemId  
	         				INNER JOIN receive th ON th.rcNo=td.rcNo AND th.statusCode='P' 
	         					AND th.receiveDate > (SELECT th.closingDate FROM stk_closing th 
	          								WHERE th.statusCode='A' AND th.closingDate<='$dateFromYmd' LIMIT 1)
	          				GROUP BY itm.prodCodeId, th.toCode
	          				) as tmp 
	          SET hdr.receive=tmp.sumQty 
	          WHERE hdr.prodId=tmp.prodCodeId AND hdr.sloc=tmp.fromCode 
          	";
          	$stmt = $pdo->prepare($sql);		
			$stmt->execute();

			//Sent
			$sql = "UPDATE tmpStock hdr
	         ,(SELECT itm.prodCodeId, th.fromCode, SUM(itm.qty) as sumQty 
	         				FROM product_item itm 
	          				INNER JOIN send_detail td ON td.prodItemId=itm.prodItemId  
	         				INNER JOIN send th ON th.sdNo=td.sdNo AND th.statusCode='P' 
	         					AND th.sendDate > (SELECT th.closingDate FROM stk_closing th 
	          								WHERE th.statusCode='A' AND th.closingDate<='$dateFromYmd' LIMIT 1)
	          				GROUP BY itm.prodCodeId, th.fromCode
	          				) as tmp 
	          SET hdr.sent=tmp.sumQty 
	          WHERE hdr.prodId=tmp.prodCodeId AND hdr.sloc=tmp.fromCode 
          	";
          	$stmt = $pdo->prepare($sql);		
			$stmt->execute();

			//return
			$sql = "UPDATE tmpStock hdr
	         ,(SELECT itm.prodCodeId, th.fromCode, SUM(itm.qty) as sumQty FROM product_item itm 
	          				INNER JOIN rt_detail td ON td.prodItemId=itm.prodItemId  
	         				INNER JOIN rt th ON th.rtNo=td.rtNo AND th.statusCode='P' 
	         					AND th.returnDate > (SELECT th.closingDate FROM stk_closing th 
	          								WHERE th.statusCode='A' AND th.closingDate<='$dateFromYmd' LIMIT 1)
	          				GROUP BY itm.prodCodeId, th.fromCode
	          				) as tmp 
	          SET hdr.return=tmp.sumQty 
	          WHERE hdr.prodId=tmp.prodCodeId AND hdr.sloc=tmp.fromCode 
          	";
          	$stmt = $pdo->prepare($sql);		
			$stmt->execute();

			//delivery
			$sql = "UPDATE tmpStock hdr
	         ,(SELECT itm.prodCodeId, cust.locationCode as fromCode, SUM(itm.qty) as sumQty FROM product_item itm 
	          				INNER JOIN delivery_detail td ON td.prodItemId=itm.prodItemId  
	         				INNER JOIN delivery_header th ON th.doNo=td.doNo AND th.statusCode='P' 
	         					AND th.deliveryDate > (SELECT th.closingDate FROM stk_closing th 
	          								WHERE th.statusCode='A' AND th.closingDate<='$dateFromYmd' LIMIT 1)
	         				INNER JOIN sale_header shd ON shd.soNo=th.soNo 
	         				INNER JOIN customer cust ON cust.id=shd.custId 
	          				GROUP BY itm.prodCodeId, cust.locationCode 
	          				) as tmp 
	          SET hdr.delivery=tmp.sumQty 
	          WHERE hdr.prodId=tmp.prodCodeId AND hdr.sloc=tmp.fromCode 
          	";
          	$stmt = $pdo->prepare($sql);		
			$stmt->execute();

			//delete
			$sql = "UPDATE tmpStock 
			SET `balance`=`openAcc`+`openTrans`+`receive`-`sent`-`return`-`delivery`
          	";
          	$stmt = $pdo->prepare($sql);		
			$stmt->execute();

			//delete
			$sql = "DELETE FROM tmpStock 
			WHERE `openAcc`=0 AND `openTrans`=0 AND `onway`=0
			AND `receive`=0 AND `sent`=0 AND `return`=0 AND `delivery`=0 
			AND `balance`=0 AND `book`=0 
          	";
          	$stmt = $pdo->prepare($sql);		
			$stmt->execute();

			//We've got this far without an exception, so commit the changes.
			$pdo->commit();	



			$sql = "SELECT  
			sb.`prodId`, sb.`sloc`, sb.`openAcc`, sb.`openTrans`, sb.`onway`, sb.`receive` ,sb.`sent`,sb.`return` ,sb.`delivery` ,sb.`balance` ,sb.`book` 	
			, prd.code as prodCode 		
			FROM tmpStock sb 
			INNER JOIN product prd on prd.id=sb.prodId  ";
			if($prodCode<>""){ $sql .= " AND prd.code like '%".$prodCode."%' ";	}	
			$sql.="
			WHERE 1 ";
			if($search_word<>""){ $sql = "and (prd.code like '%".$search_word."%' OR prd.name like '%".$search_word."%') "; }
			if($sloc<>""){ $sql .= " AND sb.sloc='$sloc' ";	}else{ $sql .= " AND sb.sloc IN ('8','E') "; }
			if($catCode<>""){ $sql .= " AND catCode='$catCode' ";	}		
			$sql.="ORDER BY prd.code  ";
			//$sql.="LIMIT $start, $rows ";
			$result = mysqli_query($link, $sql);   
			$stmt = $pdo->prepare($sql);		
			$stmt->execute();
			$countTotal = $stmt->rowCount();

			//We've got this far without an exception, so commit the changes.
			//$pdo->commit();	
				
			$rows=100;
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

						<label for="dateTo"> To : </label>
						<input type="text" id="dateTo" name="dateTo" value="" class="form-control datepicker" data-smk-msg="Require To Date." required >
						<br/>	

						<label>Location : </label>
					<select name="sloc" class="form-control">
						<option value="" <?php echo ($sloc==""?'selected':''); ?> >--All--</option>
						<?php
						$sql = "SELECT `code`, `name` FROM sloc WHERE statusCode='A' AND code IN ('8','E') ORDER BY code ASC ";
						$stm = $pdo->prepare($sql);
						$stm->execute();					
						while ($row = $stm->fetch()){
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
						$stm = $pdo->prepare($sql);
						$stm->execute();					
						while ($row = $stm->fetch()){
							$selected=($catCode==$row['code']?'selected':'');						
							echo '<option value="'.$row['code'].'" '.$selected.'>'.$row['code'].' : '.$row['name'].'</option>';
						}
						?>
					</select>				
					
					<label for="prodCode">Product Code : </label>
					<input type="hidden" name="prodId" id="prodId" class="form-control" value=""  />
					<input type="text" name="prodCode" id="prodCode" class="form-control" value="<?=$prodCode;?>"  />
					<a href="#" name="btnSdNo" class="btn btn-default" ><i class="glyphicon glyphicon-search" ></i> </a>


                    </form>

                    <a name="btnSubmit" id="btnSubmit" href="#" class="btn btn-primary"><i class="glyphicon glyphicon-search"></i> Search</a>
                </div>   
				<!--col-md-12-->
				
			</div>
			<!--row-->
			
			<div class="">
				<table border=1 class="table table-hover no-margin" style="table-layout: fixed;">
                  <thead>
                  <tr>
					<th style="width: 50px; text-align: center;">No.</th>
                    <th style="width: 200px; text-align: center;">Product Code</th>
					<th style="width: 50px; text-align: center;">Loc.</th>
					<th style="width: 100px; text-align: center; color: green;">Available</th>
					<th style="width: 100px; text-align: center; color: blue; ">Balance</th>
					<th style="width: 100px; text-align: center; color: orange;">Onway</th>
					<!--<th style="width: 100px; text-align: center;">Category</th>-->
					<th style="width: 100px; text-align: center;">Open</th>
					<th style="width: 100px; text-align: center;">IN</th>
					<th style="width: 100px; text-align: center;">OUT</th>			
					<!--<th>Pick</th>
					<th style="color: #006600; background-color: #ccccff;">Remain (Balance-Pick)</th>-->
                  </tr>
                  </thead>
                  <tbody>
				<?php 
				//$pdo->beginTransaction();
				if($countTotal==0){ 
					$sql = "SELECT 
				sb.`prodId`, sb.`sloc`, sb.`openAcc`, sb.`openTrans`, sb.`onway`, sb.`receive` ,sb.`sent`,sb.`return` ,sb.`delivery` ,sb.`balance` ,sb.`book` 	
				, sl.name as slocName 
				, prd.code as prodCode, prd.catCode 	
				FROM tmpStock sb 
				INNER JOIN sloc sl ON sl.code=sb.sloc 
				INNER JOIN product prd on prd.id=sb.prodId  ";
				if($prodCode<>""){ $sql .= " AND prd.code like '%".$prodCode."%' ";	}	
				$sql.="
				WHERE 1 ";
				if($search_word<>""){ $sql = "and (prd.code like '%".$search_word."%' OR prd.name like '%".$search_word."%') "; }
				if($sloc<>""){ $sql .= " AND sb.sloc='$sloc' ";	}else{ $sql .= " AND sb.sloc IN ('8','E') "; }
				if($catCode<>""){ $sql .= " AND catCode='$catCode' ";	}		
				$sql.="ORDER BY prd.code  ";
				$sql.="LIMIT $start, $rows ";
				$result = mysqli_query($link, $sql);   
				$stmt = $pdo->prepare($sql);		
				$stmt->execute();
				//$countTotal = $stmt->rowCount();
				?>
					<tr>
						<td colspan="9" style="text-align: center; color: red; font-weight: bold;" >Data not found.</td>
					</tr>
		       	<?php }else{ 
		       		//there data.
  
		       		$c_row=($start+1); while ($row = $stmt->fetch() ) { 
							//$img = 'dist/img/product/'.(empty($row['photo'])? 'default.jpg' : $row['photo']);
					?>
                  <tr>
					<td style="text-align: right;"><?= $c_row; ?></td>
                    <td style="width: 200px;"><a target="_blank" href="product_view_stk.php?id=<?=$row['prodId'];?>&sloc=<?=$sloc;?>" ><?= $row['prodCode']; ?></a></td>
					<td style="text-align: center;"><?= $row['sloc']; ?></td>
					<td style="text-align: right; color: green;"><?= number_format($row['balance']-$row['book'],0,'.',','); ?></td>
					<td style="text-align: right; color: blue;"><a target="_blank" href="report_itm_dtl_by_prd.php?prodCode=<?=$row['prodCode'];?>&sloc=<?=$row['sloc'];?>" ><?= number_format($row['balance'],0,'.',','); ?></a></td>
					<td style="text-align: right; color: orange;"><?= number_format($row['onway'],0,'.',','); ?></td>
					<!--<td><?= $row['catCode']; ?></td>-->
					<td style="text-align: right;"><?= number_format($row['openAcc']+$row['openTrans'],0,'.',','); ?></td>
					<td style="text-align: right;"><?= number_format($row['receive'],0,'.',','); ?></td>
					<td style="text-align: right;"><?= number_format($row['sent']+$row['return']+$row['delivery'],0,'.',','); ?></td>
					<td>
						<?php echo $row['sent'].' / '.$row['return'].' / '.$row['delivery']; ?>
					</td>
					
					<!--<td style="text-align: right;"><?= number_format(-1*$row['pick'],0,'.',','); ?></td>
					<td style="text-align: right; color: #006600; background-color: #ccccff;"><?= number_format($row['balance']-$row['pick'],0,'.',','); ?></td>-->
                </tr>
                 <?php 
                 	$c_row +=1; }//end while					
				?>   

		       <?php }//if count total ?   ?>
                  </tbody>
                </table>
              </div>
              <!-- /.table-responsive -->
			  
			<?php $condQuery="?dateFrom=".$dateFrom."&dateTo=".$dateTo."&sloc=".$sloc."&catCode=".$catCode."&prodCode=".$prodCode; ?>

             <?php if($countTotal>0){    ?>
               <div class="col-md-12">
			<a target="_blank" href="<?=$rootPage;?>_xls.php<?=$condQuery;?>" class="btn btn-default pull-right"><i class="glyphicon glyphicon-print"></i> Export</a>
			
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
				'	<i class="fa fa-circle-o"></i> เลือก</a> ' +
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

	$('#prodCodeX').keyup(function(e){ 
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


	$('#prodCode').keyup(function(e){ 
		if(e.keyCode == 13)
		{ 
			var params = {
				search_word: $('#prodCode').val()
			};
			curName = $(this).attr('name');
			curId = $(this).prev().attr('name'); //alert(curName); alert(curId);

			if(params.search_word.length < 3){
				$('#modal_search_product').modal('show');
				$('#txt_search_word_product').val(params.search_word).focus().select();

				//alert('search word must more than 3 character.');
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
							case 0 : //alert('Data not found.');
								$('#tbl_search_person_main tbody').empty().fadeIn('slow');
								return false; break;
							case 1 :
								$.each($.parseJSON(data.data), function(key,value){
									$('input[name='+curName+']').val(value.prodCode);
									$('input[name='+curId+']').val(value.prodId);			
									//getRollLength(value.prodId);
								});
								break;
							default : 
								$('#tbl_search_person_main tbody').empty();
								$.each($.parseJSON(data.data), function(key,value){
									$('#tbl_search_person_main tbody').append(
									'<tr>' +
										'<td>' +
										'	<div class="btn-group">' +
										'	<a href="javascript:void(0);" data-name="search_person_btn_checked" ' +
										'	class="btn" title="เลือก"> ' +
										'	<i class="fa fa-circle-o"></i> เลือก</a> ' +
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
								$('#modal_search_product').modal('show');	
								$('#tbl_search_person_main tbody').fadeIn('slow');
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
