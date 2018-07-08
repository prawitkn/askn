<?php

include('session.php');
//include('prints_function.php');
//include('inc_helper.php');

// Include the main TCPDF library (search for installation path).
require_once('../tcpdf/tcpdf.php');

class MYPDF extends TCPDF {

    //Page header
    public function Header() {		
		/*	Courier (fixed-width)
			Helvetica or Arial (synonymous; sans serif)
			Times (serif)
			Symbol (symbolic)
			ZapfDingbats (symbolic)*/
		
		// Title
        
		//$this->SetY(11);			
		//if($this->page != 1){
			$this->SetFont('Times', '', 10, '', true);
			$this->Cell(0, 5, $this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
			//$this->Cell(0, 5, '- '.$this->getAliasNumPage().' -', 0, false, 'C', 0, '', 0, false, 'T', 'M');
		//}
		 // Logo
        //$image_file = '../asset/img/logo-asia-kangnam.jpg';		
		//$img = file_get_contents('img\logo-asia-kangnam.jpg');
        //$this->Image($image_file, 10, 10, 15, 15, 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
		
		
		$this->SetFont('Times', 'B', 16, '', true);		
		$this->SetY(11);	
		$this->Cell(0, 5, 'Asia Kangnam Co.,Ltd.', 0, false, 'C', 0, '', 0, false, 'M', 'M');
		$this->Ln(7);
		$this->SetFont('Times', 'B', 14, '', true);	
        $this->Cell(0, 5, 'Send by Product', 0, false, 'C', 0, '', 0, false, 'M', 'M');
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
$pdf->SetFont('THSarabun', '', 12, '', true);













// Set some content to print
if( isset($_GET['dateFrom']) AND isset($_GET['dateTo']) AND isset($_GET['prodId']) ){
	$dateFrom = (isset($_GET['dateFrom'])?$_GET['dateFrom']: date('d-m-Y') );
	$dateTo = (isset($_GET['dateTo'])?$_GET['dateTo']: date('d-m-Y') );
	$prodId = (isset($_GET['prodId'])?$_GET['prodId']: "" );

	$dateFrom = str_replace('/', '-', $dateFrom);
	$dateTo = str_replace('/', '-', $dateTo);
	
	if($dateFrom<>""){ $dateFrom = date('Y-m-d', strtotime($dateFrom));	}
	if($dateTo<>""){ $dateTo =  date('Y-m-d', strtotime($dateTo));	}						

			/*$sql = "SELECT code as prodCode FROM product prd WHERE prd.id=:prodId 
			";			
	
			$stmt = $pdo->prepare($sql);			
			if($prodId<>"") $stmt->bindParam(':prodId', $prodId);	
			$stmt->execute();
			$hdr = $stmt->fetch();	
			$prodCode=$hdr['prodCode'];
			*/
			$sql = "SELECT hdr.`sdNo`, hdr.`sendDate`, hdr.`fromCode`, hdr.toCode
			, fsl.name as fromName, tsl.name as toName
			FROM send hdr 
			INNER JOIN send_detail dtl ON dtl.sdNo=hdr.sdNo 
			LEFT JOIN product_item itm on itm.prodItemId=dtl.prodItemId 	
			LEFT JOIN sloc fsl on hdr.fromCode=fsl.code
			LEFT JOIN sloc tsl on hdr.toCode=tsl.code					
			WHERE hdr.`statusCode`='P' 
			";			
			switch($s_userGroupCode){ 
				case 'whOff' :  case 'whSup' : 
					$sql .= "AND hdr.fromCode IN ('8','E') "; break;
				case 'pdOff' :  case 'pdSup' :
						$sql .= "AND hdr.fromCode=:s_userDeptCode ";
					break;
				default : //case 'it' : case 'admin' : 
			  }
			if($dateFrom<>""){ $sql .= " AND hdr.sendDate>=:dateFrom ";	}
			if($dateTo<>""){ $sql .= " AND hdr.sendDate<=:dateTo ";	}	
			if($prodId<>""){ $sql .= " AND itm.prodId=:prodId ";	}
			$sql.="ORDER BY hdr.`sendDate`, hdr.`sdNo`, itm.barcode ";
	
			$stmt = $pdo->prepare($sql);			
			if($dateFrom<>"") $stmt->bindParam(':dateFrom', $dateFrom);	
			if($dateTo<>"") $stmt->bindParam(':dateTo', $dateTo);	
			if($prodId<>"") $stmt->bindParam(':prodId', $prodId);	
			switch($s_userGroupCode){ 
				case 'pdOff' :  case 'pdSup' :
						if($s_userDeptCode<>"") $stmt->bindParam(':s_userDeptCode', $s_userDeptCode);
					break;
				default : //case 'it' : case 'admin' : 
			  }
			$stmt->execute();
			$hdr = $stmt->fetch();	
	   		


						/*$sql = "SELECT dtl.id, dtl.prodItemId 
						, itm.prodCodeId, itm.barcode, itm.NW, itm.GW, itm.grade, itm.qty, itm.issueDate 
						FROM send_detail dtl 
						LEFT JOIN product_item itm on itm.prodItemId=dtl.prodItemId 						
						WHERE sdNo=:sdNo  
						ORDER BY dtl.refNo, itm.barcode
						";			
						$stmt = $pdo->prepare($sql);	
						$stmt->bindParam(':sdNo', $hdr['sdNo']);
						$stmt->execute();	*/

						
							
					//Detail 		
					$sql = "SELECT hdr.`sdNo`, hdr.`sendDate`, hdr.`fromCode`, hdr.toCode
					, dtl.id, dtl.prodItemId 
					, itm.prodCodeId, itm.barcode, itm.NW, itm.GW, itm.grade, itm.qty, itm.issueDate 
					, fsl.name as fromName, tsl.name as toName
					FROM send hdr 
					INNER JOIN send_detail dtl ON dtl.sdNo=hdr.sdNo 
					LEFT JOIN product_item itm on itm.prodItemId=dtl.prodItemId 	
					LEFT JOIN sloc fsl on hdr.fromCode=fsl.code
					LEFT JOIN sloc tsl on hdr.toCode=tsl.code					
					WHERE hdr.`statusCode`='P' 
					";			
					switch($s_userGroupCode){ 
						case 'whOff' :  case 'whSup' : 
							$sql .= "AND hdr.fromCode IN ('8','E') "; 
							break;
						case 'pdOff' :  case 'pdSup' :
							$sql .= "AND hdr.fromCode=:s_userDeptCode ";
							break;
						default : //case 'it' : case 'admin' : 
					  }
					if($dateFrom<>""){ $sql .= " AND hdr.sendDate >= :dateFrom ";	}
					if($dateTo<>""){ $sql .= " AND hdr.sendDate <= :dateTo ";	}	
					if($prodId<>""){ $sql .= " AND itm.prodCodeId = :prodId ";	}
					$sql.="ORDER BY hdr.`sendDate`, hdr.`sdNo`, itm.barcode ";

					$stmt = $pdo->prepare($sql);			
					if($dateFrom<>"") $stmt->bindParam(':dateFrom', $dateFrom);	
					if($dateTo<>"") $stmt->bindParam(':dateTo', $dateTo);	
					if($prodId<>"") $stmt->bindParam(':prodId', $prodId);	
					switch($s_userGroupCode){ 
						case 'pdOff' :  case 'pdSup' :
							if($s_userDeptCode<>"") $stmt->bindParam(':s_userDeptCode', $s_userDeptCode);
							break;
						default : //case 'it' : case 'admin' : 
					  }
					$stmt->execute();
					
					$html ='
					<table class="table table-striped no-margin" >
						<thead>									
						  <tr>									
							<th style="font-weight: bold; text-align: right;">Sending Date :</th>
							<th>'.date('d M Y',strtotime( $dateFrom )).'</th>
							<th style="font-weight: bold; text-align: right;">To :</th>
							<th>'.date('d M Y',strtotime( $dateTo )).'</th>	
							<th style="font-weight: bold; text-align: right;">Product :</th>
							<th></th>	
						</tr>
						  <tr>
								<th style="font-weight: bold; text-align: center; width: 30px;" border="1">No.</th>
								<th style="font-weight: bold; text-align: center; width: 250px;" border="1">Barcode</th>
								<th style="font-weight: bold; text-align: center; width: 50px;" border="1">Grade</th>
								<th style="font-weight: bold; text-align: center; width: 50px;" border="1">Net<br/>Weight<br/>(kg.)</th>
								<th style="font-weight: bold; text-align: center; width: 50px;" border="1">Gross<br/>Weight<br/>(kg.)</th>										
								<th style="font-weight: bold; text-align: center; width: 50px;" border="1">Qty</th>
								<th style="font-weight: bold; text-align: center; width: 80px;" border="1">Issue Date</th>
							</tr>
						  </thead>
						  <tbody>
					'; 
						
					$row_no = 1; $sumQty=$sumNW=$sumGW=0; while ($row = $stmt->fetch()) { 
					$gradeName = ''; 
					switch($row['grade']){
						case 0 : $gradeName = 'A'; break;
						case 1 : $statusName = '<b style="color: red;">B</b>';  break;
						case 2 : $statusName = '<b style="color: red;">N</b>'; break;
						default : $statusName = '<b style="color: red;">N/A</b>';
					} 
						
					$html .='<tr>
						<td style="border: 0.1em solid black; text-align: center; width: 30px;">'.$row_no.'</td>
						<td style="border: 0.1em solid black; padding: 10px; width: 250px;"> '.$row['barcode'].'</td>
						<td style="border: 0.1em solid black; text-align: center; width: 50px;">'.$gradeName.'</td>
						<td style="border: 0.1em solid black; text-align: right; width: 50px;">'.$row['NW'].'</td>
						<td style="border: 0.1em solid black; text-align: right; width: 50px;">'.$row['GW'].' </td>
						<td style="border: 0.1em solid black; text-align: right; width: 50px;">'.number_format($row['qty'],0,'.',',').' </td>
						<td style="border: 0.1em solid black; text-align: center; width: 80px;">'.date('d M Y',strtotime( $row['issueDate'] )).'</td>
					</tr>';			
												
					$sumQty+=$row['qty'] ; $sumNW+=$row['NW']; $sumGW+=$row['GW'] ;								
					$row_no +=1; }
					//<!--end while div-->	
					
					$html .='<tr>
						<td style="border: 0.1em solid black; text-align: center; width: 30px;"></td>
						<td style="border: 0.1em solid black; text-align: center; padding: 10px; width: 250px;">Total</td>
						<td style="border: 0.1em solid black; text-align: center; width: 50px;"></td>
						<td style="border: 0.1em solid black; text-align: right; width: 50px;">'.number_format($sumNW,2,'.',',').'</td>
						<td style="border: 0.1em solid black; text-align: right; width: 50px;">'.number_format($sumGW,2,'.',',').'</td>
						<td style="border: 0.1em solid black; text-align: right; width: 50px;">'.number_format($sumQty,0,'.',',').'</td>
						<td style="border: 0.1em solid black; text-align: center; width: 80px;"></td>
					</tr>';					
					
					$html .='</tbody></table>';
											
					$pdf->AddPage('P');
										
					$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
					}
					//<!--if isset $_GET['from_date']-->
		
		 
		   

// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.
$pdf->Output('send_by_prod'.'.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
	?>