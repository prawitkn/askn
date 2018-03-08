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
		$this->Cell(0, 5, 'Asia Kungnum Co.,Ltd.', 0, false, 'C', 0, '', 0, false, 'M', 'M');
		$this->Ln(7);
		$this->SetFont('Times', 'B', 14, '', true);	
        $this->Cell(0, 5, 'Picking List', 0, false, 'C', 0, '', 0, false, 'M', 'M');
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
					if( isset($_GET['pickNo']) ){

			$rootPage='picking';
$pickNo = $_GET['pickNo'];
$sql = "
SELECT hdr.`pickNo`, hdr.`soNo`, hdr.`pickDate`, hdr.`remark`, hdr.`statusCode`
, hdr.`createTime`, hdr.`createByID`, hdr.`updateTime`, hdr.`updateById`
, hdr.`confirmTime`, hdr.`confirmById`, hdr.`approveTime`, hdr.`approveById`
, uca.userFullname as createByName, ucf.userFullname as confirmByName, uap.userFullname as approveByName
FROM picking hdr
LEFT JOIN wh_user uca on uca.userId=hdr.createById					
LEFT JOIN wh_user ucf on ucf.userID=hdr.confirmById
LEFT JOIN wh_user uap on uap.userID=hdr.approveById
WHERE 1
AND hdr.pickNo=:pickNo
";
$stmt = $pdo->prepare($sql);			
$stmt->bindParam(':pickNo', $pickNo);	
$stmt->execute();
$hdr = $stmt->fetch();		
	   		


			$sql = "
			SELECT dtl.`id`, dtl.`prodId`, dtl.`issueDate`, dtl.`grade`, dtl.`qty`, dtl.`pickNo` 
			, prd.code as prodCode  
			FROM `picking_detail` dtl 	
			LEFT JOIN product prd ON prd.id=dtl.prodId 
			WHERE 1
			AND dtl.`pickNo`=:pickNo 
			
			ORDER BY dtl.id
			";
			$stmt = $pdo->prepare($sql);	
			$stmt->bindParam(':pickNo', $hdr['pickNo']);
			$stmt->execute();	
			
			$sql = "
			SELECT dtl.`prodId`, dtl.`issueDate`, dtl.`grade`, ws.code as shelfCode, ws.name as shelfName   
			FROM `picking_detail` dtl 		
			INNER JOIN product_item itm ON itm.prodCodeId=dtl.prodId AND itm.issueDate=dtl.issueDate AND itm.grade=dtl.grade 
			INNER JOIN receive_detail rDtl on  itm.prodItemId=rDtl.prodItemId AND rDtl.statusCode='A'  
			INNER JOIN wh_shelf_map_item wmi on wmi.recvProdId=rDtl.id 
			INNER JOIN wh_shelf ws ON wmi.shelfId=ws.id 
			WHERE 1 ";
			/*AND dtl.`pickNo`=:pickNo 
			
			ORDER BY dtl.prodCode 
			";*/
			$stmt2 = $pdo->prepare($sql);	
			$stmt2->bindParam(':pickNo', $hdr['pickNo']);
			$stmt2->execute();	

				
				$html ='<table class="table table-striped no-margin" >
							<thead>									
								  <tr>
									<th style="font-weight: bold;">Picking No. :</th>
									<th style="font-weight: bold; text-align: left;">'.$hdr['pickNo'].'</th>
									<th style="font-weight: bold; text-align: right;">Ref. SO No. :</th>
									<th>'.$hdr['soNo'].'</th>									
									<th style="font-weight: bold; text-align: right;">Pick Date :</th>
									<th>'.$hdr['pickDate'].'</th>
								</tr>
								<tr>
									<th  colspan="6">Remark : '.($hdr['remark']==''?'-':$hdr['remark']).'</th>
									<th></th>
								</tr>
								  <tr>
										<th style="font-weight: bold; text-align: center; width: 30px;" border="1">No.</th>
										<th style="font-weight: bold; text-align: center; width: 150px;" border="1">Product Code</th>
										<th style="font-weight: bold; text-align: center; width: 50px;" border="1">Grade</th>
										<th style="font-weight: bold; text-align: center; width: 80px;" border="1">Issue Date</th>
										<th style="font-weight: bold; text-align: center; width: 50px;" border="1">Qty</th>
										<th style="font-weight: bold; text-align: center; width: 250px;" border="1">Shelf</th>
									</tr>
								  </thead>
								  <tbody>
					'; 
					
					$row_no = 1; while ($row = $stmt->fetch()) { 
					$html .='<tr>
						<td style="border: 0.1em solid black; text-align: center; width: 30px;">'.$row_no.'</td>
						<td style="border: 0.1em solid black; padding: 10px; width: 150px;"> '.$row['prodCode'].'</td>
						<td style="border: 0.1em solid black; text-align: center; width: 50px;">'.$row['grade'].'</td>
						<td style="border: 0.1em solid black; text-align: center; width: 80px;">'.$row['issueDate'].'</td>
						<td style="border: 0.1em solid black; text-align: right; width: 50px;">'.number_format($row['qty'],0,'.',',').'</td>';
						$html .='<td  style="border: 0.1em solid black; text-align: left; width: 250px;"> ';
						$shelfCount=0; while ($row2 = $stmt2->fetch()) { 
							if($row['prodId']==$row2['prodId']){
								$html.= $row2['shelfName'].', ';
								$shelfCount+=1;
							}
							if($shelfCount >= 10 ) exit;
						 }// end while 
						$html .='</td>';
					$html.='</tr>';	

					$row_no +=1; }
					//<!--end while div-->	
					
					$html .='</tbody></table>';
						
					$pdf->AddPage('P');
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
						'stretchtext' => 4
					);
					$pdf->write1DBarcode($hdr['pickNo'], 'C39E', '', '', '', 12, 0.4, $style, 'N');
					$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
					}
					//<!--if isset $_GET['from_date']-->
		
		 
		   

// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.
$pdf->Output($pickNo.'.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
	?>