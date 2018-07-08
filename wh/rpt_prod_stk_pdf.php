<?php

include('session.php');
//include('prints_function.php');
//include('inc_helper.php');

// Include the main TCPDF library (search for installation path).
require_once('../tcpdf/tcpdf.php');

class MYPDF extends TCPDF {

    //Page header
    public function Header() {
		// Set font
		$this->SetFont('THSarabun', '', 16, '', true);
		// Title
        
		//$this->SetY(11);			
		//if($this->page != 1){
			$this->Cell(0, 5, $this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
			//$this->Cell(0, 5, '- '.$this->getAliasNumPage().' -', 0, false, 'C', 0, '', 0, false, 'T', 'M');
		//}
		 // Logo
        //$image_file = '../asset/img/logo-asia-kangnam.jpg';
        //$this->Image($image_file, 10, 10, 15, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
		$this->SetY(11);	
		$this->Cell(0, 5, 'Asia Kangnam CO.,LTD', 0, false, 'C', 0, '', 0, false, 'M', 'M');
		$this->Ln(5);
        $this->Cell(0, 5, 'Stock', 0, false, 'C', 0, '', 0, false, 'M', 'M');
    }
    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        ///$this->SetY(-15);
        // Set font
        $this->SetFont('THSarabun', '', 14, '', true);
        // Page number
		$tmp = date('Y-m-d H:i:s');
		//$tmp = to_thai_short_date_fdt($tmp);
		$this->Cell(0, 10,'Print : '. $tmp, 0, false, 'R', 0, '', 0, false, 'T', 'M');
    }
}

// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Prawit Khamnet');
$pdf->SetTitle('RTARF DUTY');
//$pdf->SetSubject('TCPDF Tutorial');
//$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

//remove header
//$pdf->setPrintHeader(false);

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins (left, top, right)
//$pdf->SetMargins(24, 26, 30);	//หน้า ๓ บนถึงตูดเลขหน้า ๒ ตูดเลขหน้าถึงตูดบรรทัดแรก ๑.๕
$pdf->SetMargins(20, 20, 10);	//หน้า ๓ บนถึงตูดเลขหน้า ๒ ตูดเลขหน้าถึงตูดบรรทัดแรก ๑.๕
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
	require_once(dirname(__FILE__).'/lang/eng.php');
	$pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// set default font subsetting mode
$pdf->setFontSubsetting(true);

// Set font
$pdf->SetFont('THSarabun', '', 14, '', true);













// Set some content to print
					if( isset($_GET['sloc']) ){
			$sloc = $_GET['sloc'];
			$sqlSearch='';
			if(isset($_GET['search_word']) and isset($_GET['search_word'])){
				$search_word=$_GET['search_word'];
				$sqlSearch = "and (prodName like '%".$_GET['search_word']."%' OR prodNameNew like '%".$_GET['search_word']."%') ";
			}			
			
			$sql = "SELECT a.*
			,sb.prodCode, sb.sloc, sb.`open`, sb.`produce`, sb.`onway`, sb.`receive`, sb.`send`, sb.`sales`, sb.`delivery`, sb.`balance` 
			FROM stk_bal sb
			INNER JOIN product a on sb.prodCode=a.code 
			WHERE 1 ";
			($sloc==''?'':$sql.=' AND sb.sloc=:sloc ');
			if(isset($_GET['search_word']) and isset($_GET['search_word'])){
				$search_word=$_GET['search_word'];
				$sql .= "and (prodName like '%".$_GET['search_word']."%' OR prodNameNew like '%".$_GET['search_word']."%') ";
			}
			$sql .= "ORDER BY a.code desc
			";
			$result = mysqli_query($link, $sql);
			$stmt = $pdo->prepare($sql);
			$stmt->bindParam(':sloc', $sloc);
			$stmt->execute(); 
					   			
			$html ='
							<table class="table table-striped no-margin" >
								  <thead>									
								  <tr>
										<th style="font-weight: bold;">SLOC</th>
										<th>'.$sloc.'</th>
									</tr>
									<tr>
										<th style="font-weight: bold; text-align: center; width: 50px;" border="1"></th>
										<th style="font-weight: bold; text-align: center; width: 100px;" border="1"></th>
										<th style="font-weight: bold; text-align: center; width: 300px;" border="1"></th>
										<th style="font-weight: bold; text-align: center; width: 50px;" border="1"></th>										
										<th style="font-weight: bold; text-align: center; width: 50px;" border="1"></th>
										<th style="font-weight: bold; text-align: center; width: 50px;" border="1"></th>
										<th style="font-weight: bold; text-align: center; width: 50px;" border="1"></th>
										<th colspan="2" style="font-weight: bold; text-align: center; width: 100px;" border="1">IN Qty</th>	
										
										<th colspan="2" style="font-weight: bold; text-align: center; width: 100px;" border="1">OUT Qty</th>										
										
										<th style="font-weight: bold; text-align: center; width: 50px;" border="1"></th>
										<th style="font-weight: bold; text-align: center; width: 50px;" border="1"></th>
									</tr>
								  <tr>
										<th style="font-weight: bold; text-align: center; width: 50px;" border="1">No.</th>
										<th style="font-weight: bold; text-align: center; width: 100px;" border="1">SLOC</th>
										<th style="font-weight: bold; text-align: center; width: 300px;" border="1">Product</th>	
										<th style="font-weight: bold; text-align: center; width: 50px;" border="1">ProdCat</th>										
										<th style="font-weight: bold; text-align: center; width: 50px;" border="1">SaleCat</th>
										<th style="font-weight: bold; text-align: center; width: 50px;" border="1">Open</th>
										<th style="font-weight: bold; text-align: center; width: 50px;" border="1">OnWay</th>
										<th style="font-weight: bold; text-align: center; width: 50px;" border="1">Produce</th>										
										<th style="font-weight: bold; text-align: center; width: 50px;" border="1">Receive</th>
										<th style="font-weight: bold; text-align: center; width: 50px;" border="1">Send</th>										
										<th style="font-weight: bold; text-align: center; width: 50px;" border="1">Delivery</th>
										<th style="font-weight: bold; text-align: center; width: 50px;" border="1">Sales</th>
										<th style="font-weight: bold; text-align: center; width: 50px;" border="1">Balance Qty</th>
									</tr>
								  </thead>
								  <tbody>
							'; 
							
					$row_no = 1; $iPackQty=$iQty=$oPackQty=$oQty=$bPackQty=$bQty=0; while ($row = $stmt->fetch()) { 
						
					$html .='<tr>						
						<th style="font-weight: bold; text-align: center; width: 50px;" border="1">'.$row_no.'</th>
						<th style="font-weight: bold; text-align: center; width: 100px;" border="1">'.$row['sloc'].'</th>
						<th style="font-weight: bold; text-align: left; width: 300px;" border="1">'.$row['prodCode'].'</th>										
						<th style="font-weight: bold; text-align: center; width: 50px;" border="1">'.$row['prodCat'].'</th>	
						<th style="font-weight: bold; text-align: center; width: 50px;" border="1">'.$row['appId'].'</th>
						<th style="font-weight: bold; text-align: center; width: 50px;" border="1">'.number_format($row['open'],0,'.',',').'</th>
						<th style="font-weight: bold; text-align: center; width: 50px;" border="1">'.number_format($row['onway'],0,'.',',').'</th>
						<th style="font-weight: bold; text-align: center; width: 50px;" border="1">'.number_format($row['produce'],0,'.',',').'</th>										
						<th style="font-weight: bold; text-align: center; width: 50px;" border="1">'.number_format($row['receive'],0,'.',',').'</th>
						<th style="font-weight: bold; text-align: center; width: 50px;" border="1">'.number_format($row['send'],0,'.',',').'</th>										
						<th style="font-weight: bold; text-align: center; width: 50px;" border="1">'.number_format($row['delivery'],0,'.',',').'</th>
						<th style="font-weight: bold; text-align: center; width: 50px;" border="1">'.number_format($row['sales'],0,'.',',').'</th>
						<th style="font-weight: bold; text-align: center; width: 50px;" border="1">'.number_format($row['balance'],0,'.',',').'</th>
					</tr>';		
												
													
					$row_no +=1; }
					//<!--end while div-->	
					
					$html .='</tbody></table>';
						
					$pdf->AddPage('L');
					$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
					}
					//<!--if isset $_GET['from_date']-->
		
		 
		   

// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.
$pdf->Output($sloc.'.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
	?>