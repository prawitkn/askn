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
	
	$sloc = (isset($_GET['sloc'])?$_GET['sloc']:'8');
	$catCode = (isset($_GET['catCode'])?$_GET['catCode']:'');
	$appCode = (isset($_GET['appCode'])?$_GET['appCode']:'');
	
	$search_word="";
	$sqlSearch = "";
	
	if(isset($_GET['sloc'])){
		/*$sql = "SELECT IFNULL(MAX(transDate),CURDATE()) fromDate FROM `stk_ini` ";
		$stmt = $pdo->prepare($sql);
		$stmt->execute();	
		$row = $stmt->fetch();
		$fromDate = $row['fromDate'];
		
		$sql = "TRUNCATE TABLE stk_bal_temp WHERE userId=:s_userID ";
		$stmt = $pdo->prepare($sql);
		$stmt->bindParam(':s_userID', $s_userID);
		$stmt->execute();	
		$row = $stmt->fetch();
		$fromDate = $row['fromDate'];
		
		
		$sql = "INSERT INTO stk_bal_temp 
			SELECT *, (sumIni+sumProduce-sumSent+sumRecv-sumReturn) as balance, :userId  
			from (SELECT sloc.code as slocCode, sloc.name as slocName, prd.code as prodCode,  prd.prodName
			, IFNULL((SELECT SUM(sIni.balance) FROM stk_ini sIni 
				WHERE sIni.sloc=sloc.code
				AND sIni.prodCode=prd.code),0) as sumIni
			, IFNULL((SELECT SUM(s.qty) FROM product_item s 
			  WHERE s.prodCode=prd.code 
			 AND left(s.prodId,1)=sloc.code),0) as sumProduce 
			, IFNULL((SELECT SUM(d.qty) FROM send h, send_detail d 
				WHERE h.sdNo=d.sdNo 
				AND h.statusCode='P'
				AND d.prodCode=prd.code
				AND h.fromCode=sloc.code),0) as sumSent
			, IFNULL((SELECT SUM(d.qty) FROM rt h, rt_detail d 
				WHERE h.rtNo=d.rtNo 
				AND h.statusCode='P'
				AND d.prodCode=prd.code
				AND h.fromCode=sloc.code),0) as sumReturn      
			, IFNULL((SELECT SUM(d.qty) FROM receive h, receive_detail d 
				WHERE h.rcNo=d.rcNo 
				AND h.statusCode='P'
				AND d.prodCode=prd.code
				AND h.toCode=sloc.code),0) as sumRecv
			, IFNULL((SELECT SUM(d.qty) FROM send h, send_detail d 
				WHERE h.sdNo=d.sdNo 
				AND h.statusCode='P'
				AND (h.rcNo = '' OR h.rcNo is null)
				AND d.prodCode=prd.code
				AND h.toCode=sloc.code),0) as sumOnWay
			, IFNULL((SELECT SUM(d.qty) FROM rt h, rt_detail d 
				WHERE h.rtNo=d.rtNo 
				AND h.statusCode='P'
				AND d.prodCode=prd.code
				AND (h.rcNo = '' OR h.rcNo is null) 
				AND h.toCode=sloc.code ),0) as sumRtOnway
			FROM product prd, sloc 
			WHERE sloc.code='8' 

			ORDER BY prd.code) as tmp 
		WHERE tmp.sumProduce <> 0
		OR tmp.sumSent <> 0
		OR tmp.sumRecv <> 0
		OR tmp.sumRtOnway <> 0
		";
		$stmt = $pdo->prepare($sql);
		$stmt->bindParam(':userId', $s_username);
		$stmt->execute();	*/
	}//if GET
	
	
	
	
?>    

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
				
				$search_word="";
				$sqlSearch = "";
				$url="rpt_prod_stk.php";
				if(isset($_GET['search_word']) and $_GET['search_word']<>""){
					$search_word=$_GET['search_word'];
					$sqlSearch = "and (prd.code like '%".$_GET['search_word']."%' OR prd.name like '%".$_GET['search_word']."%') ";
				}
				if($appCode<>""){ $sqlSearch .= " AND appCode='$appCode' ";	}
				if($catCode<>""){ $sqlSearch .= " AND catCode='$catCode' ";	}
				
                $sql = "SELECT count(*) as countTotal
				FROM stk_bal sb 
				INNER JOIN product prd on prd.id=sb.prodId  
				WHERE 1 ";
				($sloc==''?'':$sql.=" AND sb.sloc='".$sloc."' ");
				$sql.=$sqlSearch."
				"; //echo $sql;
                $result = mysqli_query($link, $sql);
                $countTotal = mysqli_fetch_assoc($result);
				
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
                    <form id="form1" action="<?=$url;?>" method="get" class="form-inline" novalidate>
						<label>SLOC :fdsafd </label>
					<select name="sloc" class="form-control">
						<option value="" <?php echo ($sloc==""?'selected':''); ?> >--All--</option>
						<?php
						$sql = "SELECT `code`, `name` FROM sloc WHERE statusCode='A'	ORDER BY code ASC ";
						$stmt = $pdo->prepare($sql);
						$stmt->execute();					
						while ($row = $stmt->fetch()){
							$selected=($sloc==$row['code']?'selected':'');						
							echo '<option value="'.$row['code'].'" '.$selected.'>'.$row['code'].' : '.$row['name'].'</option>';
						}
						?>
					</select>	
					
						<label>Cat : </label>
					<select name="appCode" class="form-control">
						<option value="" <?php echo ($appCode==""?'selected':''); ?> >--All--</option>
						<?php
						$sql = "SELECT `code`, `name` FROM product_category WHERE statusCode='A'	ORDER BY code ASC ";
						$stmt = $pdo->prepare($sql);
						$stmt->execute();					
						while ($row = $stmt->fetch()){
							$selected=($appCode==$row['code']?'selected':'');						
							echo '<option value="'.$row['code'].'" '.$selected.'>'.$row['code'].' : '.$row['name'].'</option>';
						}
						?>
					</select>				
					
						<div class="form-group">
                            <label for="search_word">Product search key word.</label>
							<div class="input-group">
								<input id="search_word" type="text" class="form-control" name="search_word" data-smk-msg="Require userFullname."required>
								<span class="input-group-addon">
									<span class="glyphicon glyphicon-search"></span>
								</span>
							</div>
                        </div>						
						<input type="submit" class="btn btn-default" value="ค้นหา">
                    </form>
                </div>   
				<!--col-md-12-->
				
			</div>
			<!--row-->
			
			<div class="table-responsive">
                <table class="table no-margin">
                  <thead>
                  <tr>
					<th>No.</th>
                    <th>prodName</th>
					<th>SLOC</th>
					<th>Category</th>
					<th>Sales Cat.</th>
                    <th>Open</th>
					<th>Produce</th>
					<th>On Way</th>
					<th>Receive</th>
					<th>Send</th>
					<th>Sales</th>
					<th>Delivery</th>
					<th>Balance</th>
                  </tr>
                  </thead>
                  <tbody>
					<?php
						$sql = "SELECT prd.*
						,sb.sloc, sb.`open`, sb.`produce`, sb.`onway`, sb.`receive`, sb.`send`, sb.`sales`, sb.`delivery`, sb.`balance` 
						FROM product prd
						LEFT JOIN stk_bal sb on sb.prodId=prd.id  
						WHERE 1 ";
						($sloc==''?'':$sql.=" AND sb.sloc='".$sloc."' ");
						$sql.=$sqlSearch."
						ORDER BY prd.code desc
						LIMIT $start, $rows 
						";
						$result = mysqli_query($link, $sql);                
				   ?>             
					
					
						<?php $c_row=($start+1); while ($row = mysqli_fetch_assoc($result)) { 
							//$img = 'dist/img/product/'.(empty($row['photo'])? 'default.jpg' : $row['photo']);
					?>
                  <tr>
					<td><?= $c_row; ?></td>
                    <td><a href="product_view_stk.php?id=<?=$row['id'];?>&sloc=<?=$sloc;?>" ><?= $row['code']; ?></a></td>
					<td><?= $row['sloc']; ?></td>
					<td><?= $row['catCode']; ?></td>
					<td><?= $row['appCode']; ?></td>
					<td><?= number_format($row['open'],0,'.',','); ?></td>
					<td><?= number_format($row['produce'],0,'.',','); ?></td>
					<td><?= number_format($row['onway'],0,'.',','); ?></td>
					<td><?= number_format($row['receive'],0,'.',','); ?></td>
					<td><?= number_format($row['send'],0,'.',','); ?></td>
					<td><?= number_format($row['sales'],0,'.',','); ?></td>
					<td><?= number_format($row['delivery'],0,'.',','); ?></td>
					<td><?= number_format($row['balance'],0,'.',','); ?></td>
                </tr>
                 <?php $c_row +=1; } ?>
                  </tbody>
                </table>
              </div>
              <!-- /.table-responsive -->
			  
				
                
               
			
			<nav>
			<ul class="pagination">
				<li <?php if($page==1) echo 'class="disabled"'; ?> >
					<a href="<?=$url;?>?search_word=<?= $search_word;?>&catCode=<?= $catCode;?>&marketCode=<?= $marketCode;?>&=page=<?= $page-1; ?>" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a>
				</li>
				<?php for($i=1; $i<=$total_page;$i++){ ?>
				<li <?php if($page==$i) echo 'class="active"'; ?> >
					<a href="<?=$url;?>?search_word=<?= $search_word;?>&catCode=<?= $catCode;?>&marketCode=<?= $marketCode;?>&page=<?= $i?>" > <?= $i;?></a>			
				</li>
				<?php } ?>
				<li <?php if($page==$total_page) echo 'class="disabled"'; ?> >
					<a href="<?=$url;?>?search_word=<?= $search_word;?>&catCode=<?= $catCode;?>&marketCode=<?= $marketCode;?>&page=<?=$page+1?>" aria-labels="Next"><span aria-hidden="true">&raquo;</span></a>
				</li>
			</ul>
			</nav>
			
			
        </div><!-- /.box-body -->
  <div class="box-footer">
      <a href="rpt_prod_stk_pdf.php?sloc=<?=$sloc;?>&catCode=<?=$catCode;?>&search_word=<?=$search_word;?>" class="btn btn-default pull-left"><i class="glyphicon glyphicon-print"></i> Print</a>
    
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
				
});
</script>

</body>
</html>
