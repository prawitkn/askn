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
		$this->Cell(0, 5, 'Asia Kungnum CO.,LTD', 0, false, 'C', 0, '', 0, false, 'M', 'M');
		$this->Ln(5);
        $this->Cell(0, 5, 'Stock Movement', 0, false, 'C', 0, '', 0, false, 'M', 'M');
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
					if( isset($_GET['code']) ){
			$code = $_GET['code'];
			$sloc = 8; 
			$sql = "SELECT * FROM (
				SELECT 'I' as docType, hdr.`rcNo` as docNo, hdr.`receiveDate` as transDate, hdr.`remark`, hdr.`statusCode`, hdr.`createTime`, hdr.`createByID`, hdr.`updateTime`, hdr.`updateById`, hdr.`confirmTime`, hdr.`confirmById`, hdr.`approveTime`, hdr.`approveById` 
				, dtl.`prodCode`
				, dtl.`issueDate`, SUM(dtl.`qty`) as qty, SUM(dtl.`packQty`) as packQty 
				FROM `receive` hdr 
				INNER JOIN receive_detail dtl on dtl.rcNo=hdr.rcNo 
				WHERE 1=1
				AND dtl.prodCode=:prodCode 
				AND hdr.toCode=:sloc
				GROUP BY hdr.`rcNo`,hdr.`receiveDate`,hdr.`remark`, hdr.`statusCode`, hdr.`createTime`, hdr.`createByID`, hdr.`updateTime`, hdr.`updateById`, hdr.`confirmTime`, hdr.`confirmById`, hdr.`approveTime`, hdr.`approveById`, dtl.`prodCode` , dtl.`issueDate`
				
				UNION 
				SELECT 'O' as docType, hdr.`sdNo` as docNo, hdr.`sendDate` as transDate, hdr.`remark`, hdr.`statusCode`, hdr.`createTime`, hdr.`createByID`, hdr.`updateTime`, hdr.`updateById`, hdr.`confirmTime`, hdr.`confirmById`, hdr.`approveTime`, hdr.`approveById` 
				, dtl.`prodCode` 
				, dtl.`issueDate`, SUM(dtl.`qty`) as qty, SUM(dtl.`packQty`) as packQty  
				FROM `send` hdr 
				INNER JOIN send_detail dtl on dtl.sdNo=hdr.sdNo 
				WHERE 1=1
				AND dtl.prodCode=:prodCode2 
				AND hdr.fromCode=:sloc2
				GROUP BY hdr.`sdNo`,hdr.`sendDate`,hdr.`remark`, hdr.`statusCode`, hdr.`createTime`, hdr.`createByID`, hdr.`updateTime`, hdr.`updateById`, hdr.`confirmTime`, hdr.`confirmById`, hdr.`approveTime`, hdr.`approveById`, dtl.`prodCode` , dtl.`issueDate`				
				UNION 
				
				SELECT 'O' as docType, hdr.`rtNo` as docNo, hdr.`returnDate` as transDate, hdr.`remark`, hdr.`statusCode`, hdr.`createTime`, hdr.`createByID`, hdr.`updateTime`, hdr.`updateById`, hdr.`confirmTime`, hdr.`confirmById`, hdr.`approveTime`, hdr.`approveById` 
				, dtl.`prodCode` 
				, dtl.`issueDate`, SUM(dtl.`qty`) as qty, SUM(dtl.`packQty`) as packQty 
				FROM `rt` hdr 
				INNER JOIN rt_detail dtl on dtl.rtNo=hdr.rtNo 
				WHERE 1=1
				AND dtl.prodCode=:prodCode3
				AND hdr.fromCode=:sloc3
				GROUP BY hdr.`rtNo`,hdr.`returnDate`,hdr.`remark`, hdr.`statusCode`, hdr.`createTime`, hdr.`createByID`, hdr.`updateTime`, hdr.`updateById`, hdr.`confirmTime`, hdr.`confirmById`, hdr.`approveTime`, hdr.`approveById`, dtl.`prodCode` , dtl.`issueDate`
				
				) as tmp 
				ORDER BY tmp.`transDate`, tmp.createTime ASC 
						";
				$result = mysqli_query($link, $sql);
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':prodCode', $code);
				$stmt->bindParam(':prodCode2', $code);
				$stmt->bindParam(':prodCode3', $code);
				$stmt->bindParam(':sloc', $sloc);
				$stmt->bindParam(':sloc2', $sloc);
				$stmt->bindParam(':sloc3', $sloc);
				$stmt->execute();	
					   			
			$html ='
							<table class="table table-striped no-margin" >
								  <thead>									
								  <tr>
										<th style="font-weight: bold;">Prod Code</th>
										<th>'.$code.'</th>
										<th style="font-weight: bold;">SLOC</th>
										<th>'.$sloc.'</th>
									</tr>								
								  <tr>
										<th style="font-weight: bold; text-align: center; width: 50px;" border="1">No.</th>
										<th style="font-weight: bold; text-align: center; width: 100px;" border="1">Trans Date</th>
										<th style="font-weight: bold; text-align: center; width: 300px;" border="1">Doc No.</th>										
										<th style="font-weight: bold; text-align: center; width: 50px;" border="1">In Pack</th>
										<th style="font-weight: bold; text-align: center; width: 50px;" border="1">In Qty</th>
										<th style="font-weight: bold; text-align: center; width: 50px;" border="1">Out Pack</th>
										<th style="font-weight: bold; text-align: center; width: 50px;" border="1">Out Qty</th>
										<th style="font-weight: bold; text-align: center; width: 50px;" border="1">Balance Pack</th>
										<th style="font-weight: bold; text-align: center; width: 50px;" border="1">Balance Qty</th>
									</tr>
								  </thead>
								  <tbody>
							'; 
							
					$row_no = 1; $iPackQty=$iQty=$oPackQty=$oQty=$bPackQty=$bQty=0; while ($row = $stmt->fetch()) { 
						switch($row['docType']){
							case 'I' : $iPackQty += $row['packQty']; $iQty += $row['qty']; $bPackQty+=$row['packQty']; $bQty+=$row['qty']; break;
							case 'O' : $oPackQty += $row['packQty']; $oQty += $row['qty']; $bPackQty-=$row['packQty']; $bQty-=$row['qty']; break;
							default : 						
						}
						
					$html .='<tr>
						<td style="border: 0.1em solid black; text-align: center; width: 50px;">'.$row_no.'</td>
						<td style="border: 0.1em solid black; cell-padding: 5px; width: 100px;">'.$row['transDate'].'</td>
						<td style="border: 0.1em solid black; text-align: center; width: 300px;">'.$row['docNo'].'</td>
						<td style="border: 0.1em solid black; text-align: center; width: 50px;">'.number_format($iPackQty,0,'.',',').'</td>
						<td style="border: 0.1em solid black; text-align: center; width: 50px;">'.number_format($iQty,0,'.',',').'</td>
						<td style="border: 0.1em solid black; text-align: center; width: 50px;">'.number_format($oPackQty,0,'.',',').'</td>
						<td style="border: 0.1em solid black; text-align: center; width: 50px;">'.number_format($oQty,0,'.',',').'</td>
						<td style="border: 0.1em solid black; text-align: center; width: 50px;">'.number_format($bPackQty,0,'.',',').'</td>
						<td style="border: 0.1em solid black; text-align: center; width: 50px;">'.number_format($bQty,0,'.',',').'</td>
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
$pdf->Output($code.'.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
	?>