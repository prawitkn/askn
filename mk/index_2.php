
	<!-- Main row -->
      <div class="row">
        <!-- Left col -->
        <div class="col-md-8">
			<!-- TABLE: LATEST ORDERS -->
          <div class="box box-info">
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
					$sql = "SELECT a.`soNo`, a.`saleDate`, a.`custId`, a.`smId`, a.`statusCode`, a.`createTime`, a.`updateTime`
							,b.name as custName
							,c.name as smName
							FROM `sale_header` a 
							INNER JOIN `customer` b on a.`custId`= b.id 
							INNER JOIN `salesman` c on a.`smId`= c.id
							WHERE 1
							AND a.statusCode='P' 

							ORDER BY a.`updateTime` DESC
							LIMIT 10
							";
					$result = mysqli_query($link, $sql);
					
				?>
              <div class="table-responsive">
                <table class="table no-margin">
                  <thead>
                  <tr>
                    <th>Order ID</th>
                    <th>Item</th>
					<th>Salesman</th>
                    <th>Update Time</th>
                  </tr>
                  </thead>
                  <tbody>
				   <?php while ($row = mysqli_fetch_assoc($result)) { 
					$statusName = '<label class="label label-info">Being</label>';
					switch($row['statusCode']){
						case 'C' : $statusName = '<label class="label label-primary">Confirmed</label>'; break;
						case 'P' : $statusName = '<label class="label label-success">Approved</label>'; break;
						default : 						
					}
					?>
                  <tr>
                    <td><a href="sale_view_pdf.php?soNo=<?=$row['soNo'];?>" ><?= $row['soNo']; ?></a></td>
					<td><?= $row['custName']; ?></td>
					<td><?= $row['smName']; ?></td>
					<td><?= date('d M Y H:m',strtotime($row['createTime'])); ?></td>
                </tr>
                <?php  } ?>
                  </tbody>
                </table>
              </div>
              <!-- /.table-responsive -->
            </div>
            <!-- /.box-body -->
            <div class="box-footer clearfix">
              <a href="sale_add.php?soNo=" class="btn btn-sm btn-info btn-flat pull-left">Place New Order</a>
              <!--<a href="javascript:void(0)" class="btn btn-sm btn-default btn-flat pull-right">View All Orders</a>-->
            </div>
            <!-- /.box-footer -->
          </div>
          <!-- /.box -->
		 
		  <!-- USERS LIST -->
		  <div class="box box-danger">
			<div class="box-header with-border">
			  <h3 class="box-title">Sales Member</h3>					
			  <div class="box-tools pull-right">
				<?php
					$sql_smc = "
								SELECT IFNULL(COUNT(*),0) AS countSM FROM `salesman` WHERE `statusCode`='A'
								";
					$result_smc = mysqli_query($link, $sql_smc);
					$row = mysqli_fetch_assoc($result_smc)
				?>
				<span class="label label-danger"><?= $row['countSM']; ?> Members</span>
				<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
				</button>
				<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
				</button>
			  </div>
			</div>
			<!-- /.box-header -->
			<div class="box-body no-padding">
				<?php
					$sql_smm = "
								SELECT `id`, `code`, `name`, `surname`, `photo`, `positionName`, `mobileNo` 
								FROM `salesman` WHERE `statusCode`='A'
								";
					$result_smm = mysqli_query($link, $sql_smm);
					
				?>
			  <ul class="users-list clearfix">
				<?php
					$countSalesmanLi = 0;
					while($row = mysqli_fetch_assoc($result_smm)){		
					?>
						<li>
						  <img src="dist/img/<?= $row['photo']; ?>" alt="User Image">
						  <a class="users-list-name" href="salesman_view.php?code=<?= $row['code']; ?>"><?= $row['name'].' '.$row['surname']; ?></a>
						  <a class="users-list-name" href="#"><?= $row['positionName']; ?></a>
						  <!--<span class="users-list-date"><?= $row['positionName']; ?></span>-->
						</li>
					<?php
						$countSalesmanLi += 1;
						if($countSalesmanLi == 8){ break; }
					}						
				?>
			  </ul>
			  <!-- /.users-list -->
			</div>
			<!-- /.box-body -->
			<div class="box-footer text-center">
			  <a href="salesmans_view.php" class="uppercase">View All Sales</a>
			</div>
			<!-- /.box-footer -->
		  </div>
		  <!--/.box -->
            
          
        </div>
        <!-- /.col -->

		
		
		
		
		
        <div class="col-md-4">			  
          <!-- TOP 10 PRODUCT LIST -->
          <div class="box box-danger">
            <div class="box-header with-border">
              <h3 class="box-title">Top 10 Products</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
				<?php	
					$sql = "SELECT 
							pd.code as prodCode, pd.`name` as prodName, pd.photo
							,IFNULL(SUM(dtl.qty),0) as qty
							FROM sale_detail dtl
							left join product pd on dtl.prodId=pd.id 
							inner join sale_header oh on dtl.soNo=oh.soNo and oh.statusCode='P'							
								AND year(oh.saleDate)=:year ".
								($month<>"0"?"and month(oh.saleDate)=:month":"")."
							GROUP BY pd.code, pd.`name`, pd.photo
							ORDER BY 4 DESC 
							LIMIT 10							
							";
					$stmt = $pdo->prepare($sql);
					$stmt->bindParam(':year', $year);
					($month<>"0"?$stmt->bindParam(':month', $month):"");
					$stmt->execute();	
				?>
		
				<div class="table-responsive">
				<table class="table no-margin">
				  <thead>
				  <tr>
					<th>No.</th>
					<th>Item Name</th>
					<th>Qty</th>
				  </tr>
				  </thead>
				  <tbody>
				  <?php $row_code = 1; while ($row = $stmt->fetch()) { ?>
				  <tr>
					<td>
						 <?= $row_code; ?>
					</td>
					<td>
						 <a href="product_view.php?code=<?= $row['prodCode'];?>" ><?= $row['prodName']; ?></a>
					</td>
					<td style="text-align: right;">
						 <?= number_format($row['qty'],0,'.',','); ?>
					</td>
				</tr>
				<?php $row_code+=1; } ?>
				  </tbody>
				</table>
				</div>
				<!--/.table-responsive-->
			</div>
            <!-- /.box-body -->
            <div class="box-footer text-center">
              <a href="javascript:void(0)" class="uppercase">View All Products</a>
            </div>
            <!-- /.box-footer -->
          </div>
          <!-- /.box -->
		  
		  
		  <!-- TOP 10 PRODUCT LIST -->
          <div class="box box-danger">
            <div class="box-header with-border">
              <h3 class="box-title">Top 10 Customers</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
				<?php	
					$sql = "SELECT 
							c.code, oh.custId, c.name as custName
							,IFNULL(COUNT(oh.soNo),0) as netTotal		
							FROM sale_header oh 
							INNER JOIN customer c on oh.custId=c.id
							WHERE oh.statusCode='P'		
							AND c.statusCode='A' 
							AND year(oh.saleDate)=:year ".
							($month<>"0"?"and month(oh.saleDate)=:month":"")."
							GROUP BY oh.custId, c.name
							ORDER BY 4 DESC 
							LIMIT 10							
							";
					$stmt = $pdo->prepare($sql);
					$stmt->bindParam(':year', $year);
					($month<>"0"?$stmt->bindParam(':month', $month):"");
					$stmt->execute();	
				?>
		
				<div class="table-responsive">
				<table class="table no-margin">
				  <thead>
				  <tr>
					<th>No.</th>
					<th>Customer Name</th>
					<th>SO Amount</th>
				  </tr>
				  </thead>
				  <tbody>
				  <?php $row_code = 1; while ($row = $stmt->fetch()) { ?>
				  <tr>
					<td>
						 <?= $row_code; ?>
					</td>
					<td>
						 <a target="_blank" href="customer_view.php?code=<?= $row['code'];?>" ><?= $row['custName']; ?></a>
					</td>
					<td style="text-align: right;">
						 <?= number_format($row['netTotal'],0,'.',','); ?>
					</td>
				</tr>
				<?php $row_code+=1; } ?>
				  </tbody>
				</table>
				</div>
				<!-- /.table-responsive-->
            </div>
            <!-- /.box-body -->
            <div class="box-footer text-center">
              <a href="javascript:void(0)" class="uppercase">View All Customers</a>
            </div>
            <!-- /.box-footer -->
          </div>
          <!-- /.box -->
		  
		  
		  

          
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row second box col8 & col 4 -->
	  


