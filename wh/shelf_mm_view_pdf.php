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
        $this->Cell(0, 5, 'Shelf Movement', 0, false, 'C', 0, '', 0, false, 'M', 'M');
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
$pdf->SetTitle('Shelf Movement');
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
if( isset($_GET['id']) ){
	$tb='shelf_movement'; 

			$id = $_GET['id'];
			$sql = "SELECT hdr.`id`, hdr.`shelfIdFrom`, hdr.`shelfIdTo`, hdr.`remark`, hdr.`statusCode`, hdr.`createTime`, hdr.`createById` 
	            , sf.name as shelfNameFrom, st.name as shelfNameTo
	            , uc.userFullName as createByName
				FROM `".$tb."` hdr 
				LEFT JOIN wh_shelf sf ON sf.id=hdr.shelfIdFrom
				LEFT JOIN wh_shelf st ON st.id=hdr.shelfIdTo 
				LEFT JOIN wh_user uc ON uc.userId=hdr.createById 
				WHERE 1=1 
				";
				$sql.="AND hdr.id=:id ";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':id', $id);	
				$stmt->execute();

			$hdr = $stmt->fetch();			
			
			$statusName = '';
			switch($hdr['statusCode']){
				case 'C' : $statusName=' / <span style="font-weight: bold; color: red;">Confirmed</span>'; break;
				case 'P' : $statusName=''; break;
				default : $statusName=' / <span style="font-weight: bold; color: red;">N/A</span>';
			}
	   		
	
			$sql = "SELECT dtl.id, dtl.recvProdId 			
			,itm.barcode, itm.grade, itm.NW, itm.GW, itm.qty, itm.issueDate  
			FROM ".$tb."_detail dtl 
			INNER JOIN receive_detail rdtl ON rdtl.id=dtl.recvProdId 
			INNER JOIN product_item itm on itm.prodItemId=rdtl.prodItemId 						
			WHERE dtl.hdrId=:id 
			ORDER BY itm.barcode
			";			
			$stmt = $pdo->prepare($sql);	
			$stmt->bindParam(':id', $hdr['id']);
			$stmt->execute();	

						
					$html='';		
					$row_no = 1; $rowPerPage=0; $sumQty=$sumNW=$sumGW=0; while ($row = $stmt->fetch()) {
						if($rowPerPage==30){
							//Footer for write 
							$html .='</tbody></table>';					
							
							$pdf->AddPage('P');
							
							$html='';
							$rowPerPage=0;
						}
						if($html==''){
							$html ='
							<table class="table table-striped no-margin" >
								 <thead>									
								  <tr>
									<th style="font-weight: bold;">Movement ID :</th>
									<th colspan="2" style="font-weight: bold; text-align: left;">'.$hdr['id'].'</th>
									<th style="font-weight: bold; text-align: right;">From :</th>
									<th> <span style="font-size: 1.5em;">'.$hdr['shelfNameFrom'].'</span></th>									
									<th style="font-weight: bold; text-align: right;">To :</th>
									<th> <span style="font-size: 1.5em;">'.$hdr['shelfNameTo'].'</span></th>
								</tr>
								<tr>
									<th colspan="3">
										Remark : '.($hdr['remark']==''?'-':$hdr['remark']).'
									</th>
									<th style="font-weight: bold; text-align: right;"></th>
									<th></th>
									<th colspan="2"></th>
								</tr>
								  <tr>
										<th style="font-weight: bold; text-align: center; width: 30px;" border="1">No.</th>
										<th style="font-weight: bold; text-align: center; width: 320px;" border="1">Barcode</th>
										<th style="font-weight: bold; text-align: center; width: 50px;" border="1">Grade</th>
										<th style="font-weight: bold; text-align: center; width: 50px;" border="1">Net<br/>Weight<br/>(kg.)</th>
										<th style="font-weight: bold; text-align: center; width: 50px;" border="1">Gross<br/>Weight<br/>(kg.)</th>										
										<th style="font-weight: bold; text-align: center; width: 50px;" border="1">Qty</th>
										<th style="font-weight: bold; text-align: center; width: 80px;" border="1">Issue Date</th>
									</tr>
								  </thead>
								  <tbody>
							'; 
						}
						$gradeName = '<b style="color: red;">N/A</b>'; 
							switch($row['grade']){
								case 0 : $gradeName = 'A'; break;
								case 1 : $gradeName = '<b style="color: red;">B</b>';  break;
								case 2 : $gradeName = '<b style="color: red;">N</b>'; break;
								default : 
							} 
						
					$html .='<tr>
						<td style="border: 0.1em solid black; text-align: center; width: 30px;">'.$row_no.'</td>
						<td style="border: 0.1em solid black; padding: 10px; width: 320px;"> '.$row['barcode'].'</td>
						<td style="border: 0.1em solid black; text-align: center; width: 50px;">'.$gradeName.'</td>
						<td style="border: 0.1em solid black; text-align: right; width: 50px;">'.$row['NW'].'&nbsp;&nbsp;</td>
						<td style="border: 0.1em solid black; text-align: right; width: 50px;">'.$row['GW'].'&nbsp;&nbsp;</td>
						<td style="border: 0.1em solid black; text-align: right; width: 50px;">'.number_format($row['qty'],0,'.',',').'&nbsp;&nbsp;</td>
						<td style="border: 0.1em solid black; text-align: center; width: 80px;">'.date('d M Y',strtotime( $row['issueDate'] )).'</td>
					</tr>';			
												
					$sumQty+=$row['qty'] ; $sumNW+=$row['NW']; $sumGW+=$row['GW'] ;								
					$row_no +=1; $rowPerPage+=1; }
					//<!--end while div-->	
					
					$html .='<tr>
						<td style="border: 0.1em solid black; text-align: center; width: 30px;"></td>
						<td style="border: 0.1em solid black; text-align: center; padding: 10px; width: 320px;">Total</td>
						<td style="border: 0.1em solid black; text-align: center; width: 50px;"></td>
						<td style="border: 0.1em solid black; text-align: right; width: 50px;">'.number_format($sumNW,2,'.',',').'&nbsp;&nbsp;</td>
						<td style="border: 0.1em solid black; text-align: right; width: 50px;">'.number_format($sumGW,2,'.',',').'&nbsp;&nbsp;</td>
						<td style="border: 0.1em solid black; text-align: right; width: 50px;">'.number_format($sumQty,0,'.',',').'&nbsp;&nbsp;</td>
						<td style="border: 0.1em solid black; text-align: center; width: 80px;"></td>
					</tr>';
					
					$html .='<tr>
						<td colspan="2"><br/><br/>
							ผู้จัดทำ : <span style="text-decoration: underline;">
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
							<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;('.$hdr['createByName'].')<br/>
							วันที่จัดทำ : '.date('d M Y H:i',strtotime( $hdr['createTime'] )).'<br/>
						</td>
						
						<td colspan="6" style="text-align: left;"><br/><br/>							
							
						</td>
						
					</tr>';
						
						//Footer for write 
						$html .='</tbody></table>';					
						
						$pdf->AddPage('P');
												
						$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
					}
					//<!--if isset $_GET['from_date']-->
		
		 
		   

// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.
$pdf->Output('shelf_mm'.$hdr['id'].'.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
	?>