<!-- Day 8 31:10 -->
    <?php
        include '../mpdf/mpdf.php';
        ob_start();
    ?>

<!DOCTYPE html>
<html>
    <head>
        
        
    </head>
    <body>
        <p class="text-center">Hello Testing mPDF</p>
          

<!-- Day 8 0:40:09 -->        
        <?php
            $html = ob_get_contents();
            ob_end_clean();
 //           date_timezone_set('Europe/Amsterdam'); 
                    
            $mpdf = new mPDF('utf-8');
 //           $mpdf = new mPDF('utf-8','A4-L');
 //           $mpdf = new mPDF('utf-8', array(300,100));
            
            $mpdf->margin_header = 9;
            $mpdf->SetHTMLHeader('Report by Marketing Dept | Report Total Customers | Report Date = '.date('D, F d,  Y   H:i:s'));
 // time not correct 
            $mpdf->margin_footer = 9;
            $mpdf->SetHTMLFooter('Page {PAGENO}');
 // page right align ?
            
            $stylesheet = file_get_contents('./bootstrap/css/printpdf.css');
            $mpdf->WriteHTML($stylesheet,1);
            $mpdf->WriteHTML($html,2);
            $mpdf->Output();
            
            exit;
        ?>
        
    </body>
    
    
</html>