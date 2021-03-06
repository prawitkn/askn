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
		
		
		<?php switch($s_userGroupCode){ case 'admin' :  ?>	
		<li class="header">Core Master Menu</li>		
			<!-- Optionally, you can add icons to the links -->
			<?php if($s_userFullname=="Mr.Prawit  Khamnet"){ ?>
				<li><a href="shelf_x.php"><i class="glyphicon glyphicon-wrench"></i> <span>Shelf Column</span></a></li>	
				<li><a href="shelf_y.php"><i class="glyphicon glyphicon-wrench"></i> <span>Shelf Row</span></a></li>	
				<li><a href="shelf_z.php"><i class="glyphicon glyphicon-wrench"></i> <span>Shelf Rack</span></a></li>					
			<?php } ?>    
					
			<li><a href="userGroup.php"><i class="glyphicon glyphicon-th-large"></i> <span>User Group</span></a></li>
			<li><a href="userDept.php"><i class="glyphicon glyphicon-th-large"></i> <span>User Prod. Dept.</span></a></li>
		<?php } ?>

		<?php if ( $s_userGroupCode == 'admin' || ( $s_userGroupCode == 'pdSup' && $s_userDeptCode == 'T' ) ) { ?>
			<li><a href="product.php"><i class="fa fa-barcode"></i> <span>Product Data</span></a></li>						
			<li><a href="product_roll_length.php"><i class="fa fa-archive"></i> <span>Product Roll Length</span></a></li>
			<li><a href="productionMappingProduct.php"><i class="fa fa-link"></i> <span>Production Prod. Mapping</span></a></li>		

			<li><a href="prodMapCustProdCode.php"><i class="fa fa-link"></i> <span>Customer's Product Code Mapping</span></a></li>		
		<?php } ?>
		
		<?php switch($s_userGroupCode){ case 'admin' : ?>
		<li class="header">Master Management</li>
			<li><a href="user.php"><i class="fa fa-male"></i> <span>User</span></a></li>
			<li><a href="shelf.php"><i class="glyphicon glyphicon-wrench"></i> <span>Shelf</span></a></li>	
		<?php } ?>
			

		<li class="header">Master Data</li>
			<li><a href="product_list.php"><i class="fa fa-barcode"></i> <span>Product List</span></a></li>
		





		<?php switch($s_userGroupCode){ case 'admin' : case 'whOff' : case 'whSup' : case 'pdOff' : case 'pdSup' : case 'pdMgr' : case 'whMgr' : case 'acc' : ?>
			<li class="header">Transaction Menu</li>

			<?php switch($s_userGroupCode){ case 'admin' : case 'whOff' : case 'whSup' : case 'pdOff' : case 'pdSup' : case 'pdMgr' : case 'whMgr' : ?>			
			<li><a href="trans_send_sync.php"><i class="glyphicon glyphicon-transfer"></i> <span>Sync Data</span></a></li>
			<li><a href="send2.php"><i class="glyphicon glyphicon-arrow-up"></i> <span>Send</span></a></li>
			<li><a href="receive.php"><i class="glyphicon glyphicon-arrow-down"></i> <span>Sending Receive</span></a></li>
			<li><a href="send.php"><i class="glyphicon glyphicon-eject"></i> <span>Send (Warehouse)</span></a></li>
			<li><a href="rt.php"><i class="glyphicon glyphicon-arrow-left"></i> <span>Return</span></a></li>
			<?php break; default : ?>	
			<?php } ?>	

			
			<?php switch($s_userGroupCode){ case 'whOff' : case 'whSup' : case 'whMgr' : break; //Not Show ?>
			<?php default : ?>				
				<!--<li><a href="rtrc.php"><i class="glyphicon glyphicon-retweet"></i> <span>Return Receive</span></a></li>-->
			<?php } ?>		
			
			<!--<li><a href="wip.php"><i class="glyphicon glyphicon-hourglass"></i> <span>Work In Process</span></a></li-->
			<?php switch($s_userGroupCode){ case 'admin' : case 'whOff' : case 'whSup' : case 'whMgr' : ?>
			<li><a href="shelf_mm.php"><i class="glyphicon glyphicon-object-align-bottom"></i> <span>Shelf Movement</span></a></li>
			<!--<li><a href="crrc.php"><i class="glyphicon glyphicon-repeat"></i> <span>Customer Return Receive</span></a></li>-->
			<li><a href="picking.php"><i class="glyphicon glyphicon-shopping-cart"></i> <span>Picking</span></a></li>			
			<li><a href="prepare.php"><i class="glyphicon glyphicon-th-large"></i> <span>Prepare</span></a></li>
			<?php break; default : } ?>	

			<?php switch($s_userGroupCode){ case 'admin' : case 'whOff' : case 'whSup' : case 'whMgr' : case 'acc' : ?>
			<li><a href="delivery.php"><i class="glyphicon glyphicon-shopping-cart"></i> <span>Delivery</span></a></li>			
			<?php break; default : } ?>	

			<?php switch($s_userGroupCode){ case 'admin' : ?>
			<li><a href="closingStk_list.php"><i class="fa fa-warning"></i> <span>Closing Stock</span></a></li>	
			<?php break; default : } ?>	
		<?php break; default : } ?>			
		
		<?php switch($s_userGroupCode){ case 'admin' : case 'whOff' : case 'whSup' : case 'whMgr' : ?> 
			<li class="header">Tool Menu</li>
			<li><a href="picking_prod_search_shelf.php"><i class="glyphicon glyphicon-search"></i> <span>Picking Shelf</span></a></li>
			<li><a href="report_itm_dtl_by_prd.php?sloc=8&prodCode=109"><i class="glyphicon glyphicon-search"></i> <span>Available Item Stock Info</span></a></li>
			<li><a href="utility_search_barcode.php"><i class="glyphicon glyphicon-search"></i> <span>Barcode Info</span></a></li>

		<?php break; case 'pdSup' : 
				if ( $s_userDeptCode == 'T' ) { ?>
			<li class="header">Tool Menu</li>
			<li><a href="report_itm_dtl_by_prd.php?sloc=8&prodCode=109"><i class="glyphicon glyphicon-search"></i> <span>Available Item Stock Info</span></a></li>

		<?php } ?>

		<?php break;   default :  } ?>	

		<?php switch($s_userGroupCode){  default :   ?>
			<li class="header">Report</li>
			<li><a href="rpt_so_by_deli.php"><i class="fa fa-list-alt"></i> <span>Sales Order by Delivery Date Report</span></a></li>			
			<!--<li><a href="report_prod_stk.php"><i class="fa fa-list-alt"></i> <span>Stock Report</span></a></li>-->
			<li><a href="report_prod_stk.php"><i class="fa fa-list-alt"></i> <span>Stock Report</span> <i class="fa fa-star"></i></a></li>
			<li><a href="report_prod_stk_stmt.php"><i class="fa fa-list-alt"></i> <span>Stock Movement Report</span></a></li>
			<!--<li><a href="report_prod_stk_movement.php"><i class="fa fa-list-alt"></i> <span>Stock Movement Report</span></a></li>-->
			<li><a href="report_sending2.php"><i class="fa fa-list-alt"></i> <span>Sending Report</span></a></li>
			<!--<li><a href="report_send_by_prod.php"><i class="fa fa-list-alt"></i> <span>Sending by Prod. Report</span></a></li>-->
			<li><a href="report_onway_sending.php"><i class="fa fa-list-alt"></i> <span>Unreceived Sending Report</span></a></li>
			<li><a href="report_receiving.php"><i class="fa fa-list-alt"></i> <span>Receiving Report</span></a></li>
			<?php switch($s_userGroupCode){ case 'admin' : case 'whOff' : case 'whSup' : case 'pdSup' : case 'pdMgr' : case 'whMgr' : ?>
			<li><a href="report_delivery2.php"><i class="fa fa-list-alt"></i> <span>Delivery Report</span></a></li>
			<?php  } ?>	

		<?php break; } ?>		

		<li class="header">Setting Menu</li>		
			<!-- Optionally, you can add icons to the links -->
			<li><a href="user_change_pw.php"><i class="glyphicon glyphicon-wrench"></i> <span> Change Password</span></a></li>   

		<?php switch($s_userGroupCode){ case 'admin' :  ?>	
		<li class="header">Config Menu</li>		
			<!-- Optionally, you can add icons to the links -->
			<li><a href="trans_adj_stk_in_import_file.php"><i class="glyphicon glyphicon-wrench"></i> <span>Adjust In</span></a></li>
			<li><a href="trans_adj_stk_out_import_file.php"><i class="glyphicon glyphicon-wrench"></i> <span>Adjust Out</span></a></li>	 
		<?php break; default : break; } ?>				
      </ul>
      <!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
  </aside>