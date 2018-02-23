<!-- Day 8 59:10 -->
    <?php
        include '../db/database.php';    
        include '../mpdf/mpdf.php';
        ob_start();
    
        ?>

<!DOCTYPE html>
<html>
    <head>    
    </head>
    <body>
        
        <div class="text-center">
        <img src="dist/img/user2-160x160.jpg" width="90px"><!-- add Picture  -->
        </div>
            <?php
                    $sql_cust = "SELECT COUNT(*) AS COUNTCUST FROM customer";
                    $result_cust = mysqli_query($link, $sql_cust);
                    $count_cust = mysqli_fetch_assoc($result_cust);
              ?>
   <!-- Day 8 0:59:44 --> 
   <h1 class="text-center">Total customer data :  <?= $count_cust['COUNTCUST']; ?>   items.</h1>

        <section class="content">
          <div class="box box-default">
            <div class="box-header with-border">
   <!-- Can put some heading here.   <h3 class="box-title"> Customer List.  </h3> -->
                  <div class="box-tools pull-right">
                    <!-- Buttons, labels, and many other things can be placed here! -->
                    <!-- Here is a label for example -->
                    <span class="label label-primary"></span>
                  </div><!-- /.box-tools -->
            </div><!-- /.box-header -->     
            <div class="box-body"> 
                <?php
                    $sql = "SELECT * FROM customer ORDER BY custID DESC";
                    $result = mysqli_query($link, $sql); 
                ?>             
         <table class="table table-bordered">
                <tr>
                    <th>ID</th>
                    <th>Customer Name</th>
                    <th>Email</th>
                    <th>Telephone</th>
                    
                </tr>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td>
                         <?= $row['custID']; ?>
                    </td>
                    <td>
                         <?= $row['custName']; ?>
                    </td>
                    <td>
                         <?= $row['custEmail']; ?>
                    </td>
                    <td>
                         <?= $row['custTel']; ?>
                    </td>
                    
                </tr>
                <?php } ?>
            </table> 
                
            </div> <!-- /.box-body -->   
            <div class="box-footer">
                
            </div> <!-- box-footer -->
          </div> <!-- /.box -->
           
        </section><!-- /.content -->
                    
        <?php
            $html = ob_get_contents();
            ob_end_clean();
 //  ??? time printed is not Thailand's=  date_timezone_set('Europe/Amsterdam'); 
                    
            $mpdf = new mPDF('utf-8');
 // Define a Landscape page size/format by name
 //           $mpdf = new mPDF('utf-8','A4-L');
 // Define page size/format by array.          
 //           $mpdf = new mPDF('utf-8', array(300,100));
            
            $mpdf->margin_header = 9;
            //$mpdf->SetHTMLHeader('Report by Marketing Dept | Report Total Customers | Report Date = '.date('D, F d,  Y   H:i:s'));
            
            date_default_timezone_set("Asia/Bangkok");
            $mpdf->SetHeader('Report by Marketing Dept | Report Total Customers | Report Date = '. date('D, F d, Y  H:i:s'));
 // ?? time not correct 
            $mpdf->margin_footer = 9;
            $mpdf->SetFooter('Page {PAGENO}');
 // ?? page right align ?
            
            $stylesheet = file_get_contents('./bootstrap/css/printpdf.css');
 //$mpdf->SetDisplayMode('fullpage');          
           $mpdf->WriteHTML($stylesheet,1);
            
            $mpdf->WriteHTML($html,2);
 //           $mpdf->Output();
            $mpdf->Output(time(),'I');
            exit;
        ?>
        
    </body>
    
    
</html>