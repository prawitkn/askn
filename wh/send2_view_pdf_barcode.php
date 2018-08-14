<?php

include('session.php');
//include('prints_function.php');
//include('inc_helper.php');

// Include the main TCPDF library (search for installation path).
require_once('../tcpdf/tcpdf.php');

class MYPDF extends TCPDF {

    
}

// create new PDF document
$size = array(  100,  50);
$pdf = new MYPDF('L', 'mm', $size, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Prawit Khamnet');
//$pdf->SetSubject('TCPDF Tutorial');
//$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

//remove header
//$pdf->setPrintHeader(false);

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins (left, top, right)
//$pdf->SetMargins(24, 26, 30);	//หน้า ๓ บนถึงตูดเลขหน้า ๒ ตูดเลขหน้าถึงตูดบรรทัดแรก ๑.๕
$pdf->SetMargins(0, 0, 0);	//หน้า ๓ บนถึงตูดเลขหน้า ๒ ตูดเลขหน้าถึงตูดบรรทัดแรก ๑.๕
//$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
//$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);

// set auto page breaks
//$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->SetAutoPageBreak(TRUE, 0);

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
$pdf->SetFont('THSarabun', '', 12, '', true);


// export as SVG image
//$barcodeobj->getBarcodeSVG(2, 30, 'black');

// export as PNG image
//$barcodeobj->getBarcodePNG(2, 30, array(0,128,0));

// export as HTML code








// Set some content to print
					if( isset($_GET['sdNo']) ){
	   		$sdNo=$_GET['sdNo'];
	
			$sql = "SELECT dtl.id, dtl.prodItemId 
			, itm.prodCodeId, itm.barcode, itm.NW, itm.GW, itm.grade, itm.qty, itm.seqNo, itm.issueDate 
			, prd.code as prodCode, prd.description
			FROM send_detail dtl 
			LEFT JOIN product_item itm on itm.prodItemId=dtl.prodItemId 
			LEFT JOIN product prd ON prd.id=itm.prodCodeId 						
			WHERE sdNo=:sdNo  
			ORDER BY dtl.refNo, itm.barcode
			";			
			$stmt = $pdo->prepare($sql);	
			$stmt->bindParam(':sdNo', $sdNo);
			$stmt->execute();	

						
					$html='';		
					$row_no = 1; $rowPerPage=0; $sumQty=$sumNW=$sumGW=0; $itemCodeId="";  while ($row = $stmt->fetch()) {

					$itemCodeId=str_replace('-','',$row['barcode']);
					$gradeName = '<b style="color: red;">N/A</b>'; 
						switch($row['grade']){
							case 0 : $gradeName = 'A'; break;
							case 1 : $statusName = '<b style="color: red;">B</b>';  break;
							case 2 : $statusName = '<b style="color: red;">N</b>'; break;
							default : $gradeName = '<b style="color: red;">N/A</b>'; 
						} 
						$lotNo=substr($itemCodeId,15,9);	
						
						$html ='
							<table class="table table-striped no-margin" >
									<thead>
									<tr>
										<td></td><td></td><td></td><td></td><td></td><td></td>
									</tr>
									</thead>
								  <tbody>
							'; 
					$html .='<tr>
						<td colspan="6">'.$row['barcode'].'</td>
					</tr>
					<tr>
						<td colspan="2"> '.$row['prodCode'].'</td>
						<td colspan="2">'.number_format($row['qty'],0,'.',',').'&nbsp;&nbsp;</td>
						<td style="text-align: center;">'.$gradeName.'</td>
						<td style="text-align: center;"></td>
					</tr>
					<tr>
						<td style="center;">'.$row['NW'].'&nbsp;&nbsp;</td>
						<td colspan="2" style="text-align: center;">'.$lotNo.'</td>
						
						<td colspan="2">'.$row['seqNo'].'</td>
						<td></td>
					</tr>';			
						
					
					//Footer for write 
					$html .='</tbody></table>';					
					
					$pdf->AddPage('L');



					$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

					// EAN 13
					$style = array(
						'position' => '',
						'align' => 'C',
						'stretch' => false,
						'fitwidth' => false,
						'cellfitalign' => '',
						'border' => false,
						'hpadding' => 'auto',
						'vpadding' => 'auto',
						'fgcolor' => array(0,0,0),
						'bgcolor' => false, //array(255,255,255),
						'text' => true,
						'font' => 'helvetica',
						'fontsize' => 8,
						'stretchtext' => 3
					);
					$pdf->write1DBarcode($itemCodeId, 'C39E', '', '', '', 20, 0.4, $style, 'N');
					

					$html='';
					$rowPerPage=0;
												
					$sumQty+=$row['qty'] ; $sumNW+=$row['NW']; $sumGW+=$row['GW'] ;		


					$row_no +=1; $rowPerPage+=1; }
					//<!--end while div-->	
					
					
						
						
					}
					//<!--if isset $_GET['from_date']-->
					//Footer for write 
							
		 
		   

// ---------------------------------------------------------

$pdf->SetTitle($sdNo);
// Close and output PDF document
// This method has several options, check the source code documentation for more information.
$pdf->Output($sdNo.'.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
	?>