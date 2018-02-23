<?php

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
		$this->Cell(0, 5, 'Asia Kungnum CO.,LTD', 0, false, 'C', 0, '', 0, false, 'M', 'M');
		$this->Ln(5);
        $this->Cell(0, 5, 'Delivery Order', 0, false, 'C', 0, '', 0, false, 'M', 'M');
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
$pdf->SetTitle('PDF');
//$pdf->SetSubject('TCPDF Tutorial');
//$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

//remove header
//$pdf->setPrintHeader(false);

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins (left, top, right)
//$pdf->SetMargins(24, 26, 30);	//หน้า ๓ บนถึงตูดเลขหน้า ๒ ตูดเลขหน้าถึงตูดบรรทัดแรก ๑.๕
$pdf->SetMargins(20, 10, 10);	//หน้า ๓ บนถึงตูดเลขหน้า ๒ ตูดเลขหน้าถึงตูดบรรทัดแรก ๑.๕
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
$pdf->SetFont('THSarabun', '', 16, '', true);













// Set some content to print
//if( isset($_GET['doNo']) ){
	//$doNo = $_GET['doNo'];
	
	$pdf->SetTitle("Test");
						
					$html ='
						<table class="table table-striped no-margin" border="1" >
							  <thead>	
							  <tr>
									<th style="font-weight: bold; text-align: center; width: 30px;" border="1">30</th>
									<th style="font-weight: bold; text-align: center; width: 250px;" border="1">250</th>
									<th style="font-weight: bold; text-align: center; width: 50px;" border="1">50</th>
									<th style="font-weight: bold; text-align: center; width: 50px;" border="1">50</th>
									<th style="font-weight: bold; text-align: center; width: 80px;" border="1">80</th>
								</tr>
								<tr>
									<th style="font-weight: bold; text-align: center; width: 330px;" border="1">330</th>
									<th style="font-weight: bold; text-align: center; width: 50px;" border="1">50</th>
									<th style="font-weight: bold; text-align: center; width: 80px;" border="1">80</th>
								</tr>
							  </thead>
							  <tbody>
						'; 
							
					for($row_no=0; $row_no <=200; $row_no+=1){
						$html .='<tr><td>row_no : '.$row_no.'</td></tr>';
						
					}
						
						
					//<!--end while div-->	
									
					$html .='</tbody></table>';
						
					$pdf->AddPage('P');
					$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
					//}
					//<!--if isset $_GET['from_date']-->
		
		 
		   

// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.
$pdf->Output("test".'_Shelf.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
	?>