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
$size = array(  200,  100);
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
$pdf->SetFont('helvetica', '', 12, '', true);


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

					$lotNo=substr($row['barcode'],20);	
					$itemCodeId=str_replace('-','',$row['barcode']);
					$gradeName = '<b style="color: red;">N/A</b>'; 
						switch($row['grade']){
							case 0 : $gradeName = 'A'; break;
							case 1 : $statusName = '<b style="color: red;">B</b>';  break;
							case 2 : $statusName = '<b style="color: red;">N</b>'; break;
							default : $gradeName = '<b style="color: red;">N/A</b>'; 
						} 
						//$lotNo=substr($itemCodeId,15);	


					$pdf->AddPage('L');

					//$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

					//style texst
					$styleText = array(
						'position' => '',
						'align' => 'C',
						'stretch' => false,
						'fitwidth' => true,
						'cellfitalign' => '',
						'border' => false,
						'hpadding' => 'auto',
						'vpadding' => 'auto',
						'fgcolor' => array(0,0,0),
						'bgcolor' => false, //array(255,255,255),
						'text' => false,
						'font' => 'helvetica',
						'fontsize' => 8,
						'stretchtext' => 3
					);

					// EAN 13
					$style = array(
						'position' => '',
						'align' => 'C',
						'stretch' => false,
						'fitwidth' => true,
						'cellfitalign' => '',
						'border' => false,
						'hpadding' => 'auto',
						'vpadding' => 'auto',
						'fgcolor' => array(0,0,0),
						'bgcolor' => false, //array(255,255,255),
						'text' => false,
						'font' => 'helvetica',
						'fontsize' => 8,
						'stretchtext' => 3
					);

				
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
					$yHeight=17;
					$y+=25;
					$pdf->writeHTMLCell(195, $yHeight, $x, $y, '<h1 style="text-align: center; font-size: 200%;">'.$row['prodName'].'</h1>', $border = $isBorder, $ln = 0, $align = '', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M' );
					$y+=$yHeight;	// new line


					$x=2;
					$yHeight=15;					
					$pdf->writeHTMLCell(60, $yHeight, $x, $y, '<h3 style="text-align: center;">'.$row['prodCode'].'</h3>', $border = $isBorder, $ln = 0, $align = '', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M' ); 
					$x+=60;	

					$length="-";
					switch($row['catCode']){
						case '70' : case '71' : $length=$row['qty'].' mm.'; break;
						default : 
					}

					$qty=$row['qty'];
					switch($row['catCode']){
						case '80' : 
							if ( $row['uomCode']=='KG.' ) $qty=$row['NW']; break;
						default : 
					}

					$pdf->writeHTMLCell(45, $yHeight, $x, $y, '<h3 style="text-align: center;">'.$length.'</h3>' , $border = $isBorder, $ln = 0, $align = '', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M' ); 
					$x+=45;
					$pdf->writeHTMLCell(45, $yHeight, $x, $y, '<h3 style="text-align: center;">'.number_format($row['width'],0,'.',',').' mm.</h3>' , $border = $isBorder, $ln = 0, $align = '', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M' ); 
					$x+=45;					
					$pdf->writeHTMLCell(45, $yHeight, $x, $y, '<h3 style="text-align: center;">'.$row['qty'].' '.$row['uomCode'].'</h3>', $border = $isBorder, $ln = 0, $align = '', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M'); 
					$y+=$yHeight; // new line

					$x=2;
					$yHeight=13;		
					$pdf->writeHTMLCell(60, $yHeight, $x, $y, '<h3 style="text-align: center;">'.$row['NW'].' KG.</h3>',  $border = $isBorder, $ln = 0, $align = '', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M'); 
					$x+=60;				
					$pdf->writeHTMLCell(45, $yHeight, $x, $y, '<h3 style="text-align: center;">'.number_format($row['weight'],0,'.',',').' g/m<sup>2</sup></h3>' , $border = $isBorder, $ln = 0, $align = '', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M'); 
					$x+=45;
					$pdf->writeHTMLCell(45, $yHeight, $x, $y, '<h3 style="text-align: center;">'.$gradeName.'</h3>' , $border = $isBorder, $ln = 0, $align = '', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M'); 
					$x+=45;
					$pdf->writeHTMLCell(45, $yHeight, $x, $y, '', $border = $isBorder, $ln = 0, $align = '', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M'); 
					$y+=$yHeight; // new line



					$x=2;
					$yHeight=15;
					$pdf->writeHTMLCell(60, $yHeight, $x, $y+2, '<h3 style="text-align: center;">'.$lotNo.'</h3>', $border = $isBorder, $ln = 0, $align = '', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M'); 
					$x+=60;	
					/*$pdf->writeHTMLCell(5, $yHeight, $x, $y, '', $border = $isBorder, $ln = 0, $align = '', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M'); 
					$x+=5;	*/
					$pdf->write1DBarcode($itemCodeId, 'C128', '', $y, 90, 12, 90, $style, 'M'); 
					$x+=90;

					/*$pdf->writeHTMLCell(5, $yHeight, $x, $y, '', $border = $isBorder, $ln = 0, $align = '', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M'); 
					$x+=5;	*/
					$pdf->writeHTMLCell(45, $yHeight, $x, $y, '', $border = $isBorder, $ln = 0, $align = '', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M'); $x=0;


					//$x = $pdf->GetX();
           			// $y = $pdf->GetY();
					 //
					

					$html='';
					$rowPerPage=0;
												
					$sumQty+=$qty ; $sumNW+=$row['NW']; $sumGW+=$row['GW'] ;		


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