<?php

include('session.php');
//include('prints_function.php');
//include('inc_helper.php');

// Include the main TCPDF library (search for installation path).
require_once('../tcpdf/tcpdf.php');

class MYPDF extends TCPDF {

    
}

date_default_timezone_set("Asia/Bangkok");

// create new PDF document
$size = array(  100,  75);
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
$pdf->SetFont('helvetica', '', 8, '', true);


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
			, prd.code as prodCode, prd.name as prodName, prd.description, prd.uomCode 
			, prd.catCode , prd.width, prd.weight  
			, pcc.prodCode as qrProdCode, pcc.prodDesc as qrProdDesc
			FROM send_detail dtl 
			LEFT JOIN product_item itm on itm.prodItemId=dtl.prodItemId 
			LEFT JOIN product prd ON prd.id=itm.prodCodeId 
			LEFT JOIN wh_product_code_by_customer pcc ON pcc.prodId=itm.prodCodeId 
				AND pcc.custId=1000347 AND pcc.statusCode='A' 					
			WHERE sdNo=:sdNo  
			ORDER BY dtl.refNo, itm.barcode
			";			
			$stmt = $pdo->prepare($sql);	
			$stmt->bindParam(':sdNo', $sdNo);
			$stmt->execute();	

						
					$html='';		
					$row_no = 1; $rowPerPage=0; $sumQty=$sumNW=$sumGW=0; $itemCodeId="";  while ($row = $stmt->fetch()) {

					$lotNo=substr($row['barcode'],20,10);	
					$itemCodeId=str_replace('-','',$row['qrProdCode']);
					$pdf->AddPage('L');

					//$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

					

				
		            //Reset X,Y so wrapping cell wraps around the barcode's cell.
		            //$pdf->SetXY($x,$y);
		           // $pdf->Cell(105, 51, $itemCodeId, 1, 0, 'C', FALSE, '', 0, FALSE, 'C', 'B');
					//$pdf->write1DBarcode($itemCodeId, 'C39E', '', '', '', 20, 0.4, $style, 'N');

											
					//Footer for write 
					//$html .='</tbody></table>';					
					$x=$y=0;

					//$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
					#writeHTMLCell(w, h, x, y, html = '', border = 0, ln = 0, fill = 0, reseth = true, align = '', autopadding = true)

					//Cell( $w, $h = 0, $txt = '', $border = 0, $ln = 0, $align = '', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M' )

					$isBorder = 0; // 1 for coding alingment, 0 for Production

					$x=2;
					$yHeight=10;
					//$yHeight=0;
					$y+=2;
					$pdf->writeHTMLCell(95, $yHeight, $x, $y, '<h1 style="text-align: center; font-size: 200%;">ASIA KANGNAM CO.,LTD</h1>', $border = $isBorder, $ln = 0, $align = '', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M' );
					$y+=$yHeight;	// new line


					$x=2;
					$x+=5;
					$yHeight=10;					
					$pdf->writeHTMLCell(50, $yHeight, $x, $y, '<h3 style="text-align: left;">Item '.$itemCodeId.'</h3>', $border = $isBorder, $ln = 0, $align = '', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M' ); 
					$x+=50;	

					$pdf->writeHTMLCell(45, $yHeight, $x, $y, '<h3 style="text-align: left;">Lot '.$lotNo.'</h3>', $border = $isBorder, $ln = 0, $align = '', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M'); 
					$x+=45;	

					//$y+=$yHeight; // new line
					$y+=3;//$yHeight; // new line


					$x=2;
					$yHeight=10;

					// set style for barcode
					$style = array(
					    'border' => 0,
					    'vpadding' => 'auto',
					    'hpadding' => 'auto',
					    'fgcolor' => array(0,0,0),
					    'bgcolor' => false, //array(255,255,255)
					    'module_width' => 1, // width of a single module in points
					    'module_height' => 1 // height of a single module in points
					);
					// QRCODE,L : QR-CODE Low error correction
					$barcodeStr=$itemCodeId.',,'.$lotNo;
					$pdf->write2DBarcode($barcodeStr, 'QRCODE,L', $x+10, $y, 95, 60, $style, 'N');
					//$pdf->Text(20, 25, 'QRCODE L');

					// QRCODE,M : QR-CODE Medium error correction
					//$pdf->write2DBarcode($itemCodeId, 'QRCODE,M', 140, $y, 40, 40, $style, 'N');
					//$pdf->Text(20, 85, 'QRCODE M');

					// QRCODE,Q : QR-CODE Better error correction
					//$pdf->write2DBarcode($itemCodeId, 'QRCODE,Q', 190, $y, 40, 40, $style, 'N');
					//$pdf->Text(20, 145, 'QRCODE Q');

					// QRCODE,H : QR-CODE Best error correction
					//$pdf->write2DBarcode($itemCodeId, 'QRCODE,H', 240, $y, 40, 40, $style, 'N');
					//$pdf->Text(20, 205, 'QRCODE H');

					$y+=$yHeight+10;	// new line
										

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