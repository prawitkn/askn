<aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

      <!-- Sidebar user panel (optional) -->
      <div class="user-panel">
        <div class="pull-left image">
          <img src="dist/img/<?php echo (empty($s_userPicture)? 'default-50x50.gif' : $s_userPicture) ?> " class="img-circle" alt="<?= $s_userFullname ?>">
        </div>
        <div class="pull-left info">
          <p><?= $s_userFullname ?></p>
          <!-- Status -->
          <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
        </div>
      </div>
	
      <!-- search form (Optional) 
      <form action="#" method="get" class="sidebar-form">
        <div class="input-group">
          <input type="text" name="year" class="form-control" placeholder="<?= $year; ?>">
              <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
        </div>
      </form>
      <!-- /.search form -->

      <!-- Sidebar Menu -->
      <ul class="sidebar-menu ">
		
		<li class="header">Core Master Menu</li>
		<?php switch($s_userGroupCode){ case 'it' : ?>			
			<!-- Optionally, you can add icons to the links -->
			<?php if($s_userFullname=="Mr.Prawit  Khamnet"){ ?>
				<li><a href="shelf_x.php"><i class="glyphicon glyphicon-wrench"></i> <span>Shelf Column</span></a></li>	
				<li><a href="shelf_y.php"><i class="glyphicon glyphicon-wrench"></i> <span>Shelf Row</span></a></li>	
				<li><a href="shelf_z.php"><i class="glyphicon glyphicon-wrench"></i> <span>Shelf Rack</span></a></li>					
			<?php } ?>    
					
			<li><a href="userGroup.php"><i class="fa fa-list-ol"></i> <span>User Group</span></a></li>
			<li><a href="userDept.php"><i class="fa fa-list-ol"></i> <span>User Prod. Dept.</span></a></li>
		<?php } ?>
		<?php switch($s_userGroupCode){ case 'it' : case 'whOff' : case 'whSup' : ?>
			
		<?php } ?>
		<?php switch($s_userGroupCode){ case 'it' : case 'tech' : ?>
			<li><a href="product.php"><i class="fa fa-barcode"></i> <span>Product Data</span></a></li>
			<li><a href="productionMappingProduct.php"><i class="fa fa-list-ol"></i> <span>Production Prod. Mapping</span></a></li>		
		<?php } ?>
		
		<li class="header">Master Management</li>
		<?php switch($s_userGroupCode){ case 'admin' : case 'it' : ?>
			<li><a href="user.php"><i class="fa fa-male"></i> <span>User</span></a></li>
			<li><a href="shelf.php"><i class="glyphicon glyphicon-wrench"></i> <span>Shelf</span></a></li>	
		<?php } ?>
			
		<?php switch($s_userGroupCode){ case 'it' : case 'admin' : case 'whOff' : case 'whSup' : case 'pdOff' : case 'pdSup' :  ?>
			<li class="header">Transaction Menu</li>
			<li><a href="trans_send_sync.php"><i class="glyphicon glyphicon-transfer"></i> <span>Sync Data</span></a></li>
			<li><a href="send2.php"><i class="glyphicon glyphicon-arrow-up"></i> <span>Send</span></a></li>
			<li><a href="receive.php"><i class="glyphicon glyphicon-arrow-down"></i> <span>Sending Receive</span></a></li>
			<li><a href="rt.php"><i class="glyphicon glyphicon-arrow-left"></i> <span>Return</span></a></li>
			
			<?php switch($s_userGroupCode){ case 'whOff' : case 'whSup' : break; //Not Show ?>
			<li><a href="rtrc.php"><i class="glyphicon glyphicon-retweet"></i> <span>Return Receive</span></a></li>
			<?php break; default : } ?>		
			
			<!--<li><a href="wip.php"><i class="glyphicon glyphicon-hourglass"></i> <span>Work In Process</span></a></li-->
			<?php switch($s_userGroupCode){ case 'it' : case 'admin' : case 'whOff' : case 'whSup' ?>
			<li><a href="picking.php"><i class="glyphicon glyphicon-shopping-cart"></i> <span>Picking</span></a></li>			
			<li><a href="picking_prod_search_shelf.php"><i class="glyphicon glyphicon-search"></i> <span>Picking Shelf</span></a></li>
			<li><a href="prepare.php"><i class="glyphicon glyphicon-th-large"></i> <span>Prepare</span></a></li>
			<li><a href="delivery.php"><i class="glyphicon glyphicon-shopping-cart"></i> <span>Delivery</span></a></li>
			<!--<li><a href="crrc.php"><i class="glyphicon glyphicon-repeat"></i> <span>Customer Return Receive</span></a></li>-->
			<?php break; default : } ?>	
		<?php break; default : } ?>			
			
		<?php switch($s_userGroupCode){ case 'it' : case 'admin' : case 'whOff' : case 'whSup' : case 'pdOff' : case 'pdSup' :   ?>
			<li class="header">Report</li>
			<li><a href="rpt_so_by_deli.php"><i class="fa fa-list-alt"></i> <span>Sales Order by Delivery Date Report</span></a></li>			
			<li><a href="report_prod_stk.php"><i class="fa fa-list-alt"></i> <span>Stock Report</span></a></li>
			<li><a href="report_sending.php"><i class="fa fa-list-alt"></i> <span>Sending Report</span></a></li>
			<li><a href="report_send_by_prod.php"><i class="fa fa-list-alt"></i> <span>Sending by Prod. Report</span></a></li>
			<li><a href="report_send_pending.php"><i class="fa fa-list-alt"></i> <span>Send Pending Report</span></a></li>
			<li><a href="report_receiving.php"><i class="fa fa-list-alt"></i> <span>Receiving Report</span></a></li>
			<?php switch($s_userGroupCode){ case 'it' : case 'admin' : case 'whOff' : case 'whSup' : ?>
			<li><a href="report_delivery.php"><i class="fa fa-list-alt"></i> <span>Delivery Report</span></a></li>
			<?php break; default : } ?>	
		<?php break; default : } ?>						
      </ul>
      <!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
  </aside>