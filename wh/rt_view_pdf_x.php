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
        $this->Cell(0, 5, 'Return', 0, false, 'C', 0, '', 0, false, 'M', 'M');
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
if( isset($_GET['rtNo']) ){

			$rtNo = $_GET['rtNo'];
			$sql = "SELECT hdr.`rtNo`, hdr.`refNo`, hdr.`returnDate`, hdr.`fromCode`, hdr.toCode, hdr.`statusCode`,  hdr.`remark`
			, hdr.`createTime`, hdr.`createByID`, hdr.`confirmTime`, hdr.`confirmById`, hdr.`approveTime`, hdr.`approveById` 
			, fsl.name as fromName, tsl.name as toName
			, d.userFullname as createByName
			, hdr.confirmTime, cu.userFullname as confirmByName
			, hdr.approveTime, au.userFullname as approveByName
			FROM `rt` hdr
			LEFT JOIN sloc fsl on hdr.fromCode=fsl.code
			LEFT JOIN sloc tsl on hdr.toCode=tsl.code
			left join user d on hdr.createByID=d.userID
			left join user cu on hdr.confirmByID=cu.userID
			left join user au on hdr.approveByID=au.userID
			WHERE 1
			AND hdr.rtNo=:rtNo 					
			ORDER BY hdr.createTime DESC
			LIMIT 1
					
			";
			$stmt = $pdo->prepare($sql);			
			$stmt->bindParam(':rtNo', $rtNo);	
			$stmt->execute();
			$hdr = $stmt->fetch();
	   		


			$sql = "SELECT dtl.`id`, dtl.`prodItemId`, dtl.`prodId`, dtl.`prodCode`, dtl.`barcode`, dtl.`issueDate`, dtl.`machineId`, dtl.`seqNo`, dtl.`NW`, dtl.`GW`
			, dtl.`qty`, dtl.`packQty`, dtl.`grade`, dtl.`gradeDate`, dtl.`refItemId`, dtl.`itemStatus`, dtl.`remark`, dtl.`problemId`
			, dtl.`returnReasonCode`, dtl.`returnReasonRemark`, dtl.`rtNo` 
			, rrt.name as returnReasonName 
			FROM `rt_detail` dtl	
			LEFT JOIN wh_return_reason_type rrt on rrt.code=dtl.returnReasonCode 
			WHERE 1
			AND dtl.rtNo=:rtNo  
			";
			$stmt = $pdo->prepare($sql);
			$stmt->bindParam(':rtNo', $rtNo);		
			$stmt->execute();
			$rowCount = $stmt->rowCount();
			

					
			$html ='
							<table class="table table-striped no-margin" >
								  <thead>									
								  <tr>
										<th style="font-weight: bold;">From</th>
										<th>'.$hdr['fromCode'].' : '.$hdr['fromName'].'</th>
										<th style="font-weight: bold;">To</th>
										<th>'.$hdr['toCode'].' : '.$hdr['toName'].'</th>
										<th style="font-weight: bold;">Return Date</th>
										<th>'.$hdr['returnDate'].'</th>
										<th></th>
									</tr>
									<tr>
										<th colspan="7">
											Remark : '.($hdr['remark']==''?'-':$hdr['remark']).'
										</th>
									</tr>									
								  <tr>
										<th style="font-weight: bold; text-align: center; width: 50px;" border="1">No.</th>
										<th style="font-weight: bold; text-align: center; width: 300px;" border="1">Barcode</th>
										<th style="font-weight: bold; text-align: center; width: 50px;" border="1">Grade</th>
										<th style="font-weight: bold; text-align: center; width: 50px;" border="1">Qty</th>
										<th style="font-weight: bold; text-align: center; width: 100px;" border="1">Seding Date</th>
									</tr>
								  </thead>
								  <tbody>
							'; 
							
					$row_no = 1; $iPackQty=$iQty=$oPackQty=$oQty=$bPackQty=$bQty=0; while ($row = $stmt->fetch()) { 
						
						
					$html .='<tr>
						<td style="border: 0.1em solid black; text-align: center; width: 50px;">'.$row_no.'</td>
						<td style="border: 0.1em solid black; cell-padding: 5px; width: 300px;">'.$row['barcode'].'<br/><small>'.$row['returnReasonCode'].' : '.$row['returnReasonRemark'].'</small></td>
						<td style="border: 0.1em solid black; text-align: center; width: 50px;">'.$row['grade'].'</td>
						<td style="border: 0.1em solid black; text-align: right; width: 50px;">'.number_format($row['qty'],0,'.',',').'</td>
						<td style="border: 0.1em solid black; text-align: center; width: 100px;">'.$row['issueDate'].'</td>
					</tr>';		
												
													
					$row_no +=1; }
					//<!--end while div-->	
					
					$html .='</tbody></table>';
						
					$pdf->AddPage('P');
					$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
					}
					//<!--if isset $_GET['from_date']-->
		
		 
		   

// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.
$pdf->Output($rtNo.'.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
	?>