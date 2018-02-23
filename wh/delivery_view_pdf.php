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
if( isset($_GET['doNo']) ){
	$doNo = $_GET['doNo'];
	
	$pdf->SetTitle($doNo);
	
$sql = "
SELECT dh.`doNo`, dh.`soNo`, dh.`ppNo`, oh.`poNo`
, dh.`deliveryDate`, dh.`remark`, dh.driver
, dh.`statusCode`, dh.`createTime`, dh.`createById`, dh.`updateTime`, dh.`updateById`
, dh.`confirmTime`, dh.`confirmById`, dh.`approveTime`, dh.`approveById`
, ct.code as custCode, ct.name as  custName ,ct.addr1 , ct.addr2 , ct.addr3 , ct.zipcode, ct.tel, ct.fax
, concat(sm.name, '  ', sm.surname) as smFullname 
, uca.userFullname as createByName, ucf.userFullname as confirmByName, uap.userFullname as approveByName
FROM delivery_header dh 
LEFT JOIN prepare pp on pp.ppNo=dh.ppNo 
LEFT JOIN picking pk on pk.pickNo=pp.pickNo 
LEFT JOIN sale_header oh on pk.soNo=oh.soNo 
LEFT JOIN customer ct on ct.id=oh.custId
LEFT JOIN salesman sm on sm.id=oh.smId 
LEFT JOIN wh_user uca on uca.userId=dh.createById					
LEFT JOIN wh_user ucf on ucf.userId=dh.confirmById
LEFT JOIN wh_user uap on uap.userId=dh.approveById
WHERE 1
AND dh.doNo=:doNo
";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':doNo', $doNo);	
$stmt->execute();
$hdr = $stmt->fetch();
$doNo = $hdr['doNo'];
$ppNo = $hdr['ppNo'];
$soNo = $hdr['soNo'];


$sql = "SELECT COUNT(id) as rowCount FROM delivery_detail
		WHERE doNo=:doNo 
			";						
$stmt = $pdo->prepare($sql);	
$stmt->bindParam(':doNo', $hdr['doNo']);
$stmt->execute();	
$rowCount = $stmt->fetch(PDO::FETCH_ASSOC);


$sql = "
SELECT dd.`id`, itm.`prodCodeId`, itm.`qty`
,pd.code as prodCode, pd.uomCode, dd.remark 
, IFNULL((SELECT SUM(sd.qty) FROM sale_detail sd
   		INNER JOIN sale_header sh on sh.soNo=sd.soNo
   		WHERE sh.soNo=pk.soNo
   		AND sd.prodId=itm.prodCodeId),0) AS sumSalesQty
, (SELECT IFNULL(SUM(dds.qty),0) FROM delivery_header dhs 
	INNER JOIN delivery_detail dds on dhs.doNo=dds.doNo
	INNER JOIN product_item itms ON itms.prodItemId=dds.prodItemId 
   	INNER JOIN prepare pps on pps.ppNo=dhs.ppNo
    INNER JOIN picking pks on pks.pickNo=pps.pickNo
    WHERE pks.soNo=pk.soNo 
    AND itms.prodCodeId=itm.prodCodeId
    AND dhs.statusCode='P' ) as sumSentQty
, IFNULL(SUM(dd.qty),0) as sumDeliveryQty 
FROM delivery_detail dd
INNER JOIN delivery_header dh on dh.doNo=dd.doNo 
LEFT JOIN product_item itm ON itm.prodItemId=dd.prodItemId 
INNER JOIN prepare pp on pp.ppNo=dh.ppNo
INNER JOIN picking pk on pk.pickNo=pp.pickNo
INNER JOIN sale_header oh on oh.soNo=pk.soNo

LEFT JOIN product pd on dd.prodCode=itm.prodCodeId 
WHERE 1
AND dh.doNo=:doNo
GROUP BY dd.`id`, dd.`prodCode`, dd.`qty` , pd.name, pd.description, pd.uomCode

ORDER BY dd.`id`, dd.`prodCode`, dd.`qty`, pd.name 
";
$stmt = $pdo->prepare($sql);	
$stmt->bindParam(':doNo', $hdr['doNo']);
$stmt->execute();

					
						$html ='
							<table class="table table-striped no-margin" >
								  <thead>									
								  <tr>
									<th style="font-weight: bold;">Customer :</th>
									<th style="font-weight: bold; text-align: left;">'.$hdr['custCode'].':'.$hdr['custName'].'</th>
									<th style="font-weight: bold;">Ref No. :</th>
									<th style="text-align: left;">'.$hdr['soNo'].'<br/>PO No.'.$hdr['poNo'].'</th>
									<th style="font-weight: bold; text-align: right;">Delivery Date :</th>
									<th>'.$hdr['deliveryDate'].'</th>
								</tr>
								<tr>
									<th style="font-weight: bold;">Address :</th>
									<th style="text-align: left;">'.$hdr['addr1'].'</th>
									<th style="font-weight: bold; text-align: right;"></th>
									<th style="font-weight: bold; text-algn: right;">Salesman :</th>
									<th>'.$hdr['smFullname'].'</th>
								</tr>
								<tr>
									<th colspan="6">
										Remark : '.($hdr['remark']==''?'-':$hdr['remark']).'
									</th>	
								</tr>
								  <tr>
										<th style="font-weight: bold; text-align: center; width: 30px;" border="1">No.</th>
										<th style="font-weight: bold; text-align: center; width: 250px;" border="1">Product Code</th>
										<th style="font-weight: bold; text-align: center; width: 50px;" border="1">Qty</th>
										<th style="font-weight: bold; text-align: center; width: 250px;" border="1">Remark</th>
									</tr>
								  </thead>
								  <tbody>
							'; 
							
					$row_no = 1; $sumQty=$sumNW=$sumGW=0; while ($row = $stmt->fetch()) { 
						
						
					$html .='<tr>
						<td style="border: 0.1em solid black; text-align: center; width: 30px;">'.$row_no.'</td>
						<td style="border: 0.1em solid black; padding: 10px; width: 250px;">'.$row['prodCode'].'</td>
						<td style="border: 0.1em solid black; text-align: right; width: 50px;">'.number_format($row['sumDeliveryQty'],2,'.',',').'</td>
						<td style="border: 0.1em solid black; padding: 10px; width: 250px;">'.$row['remark'].'</td>
					</tr>';									
					$row_no +=1; }
					//<!--end while div-->	
					$html .='<tr>
						<td colspan="2"><br/><br/>
							Create by ..............................................................<br/>
							<label style="padding-left: 20px;">'.$hdr['createByName'].' / <small>'.$hdr['createTime'].'</small></label><br/>
							Verify by ..............................................................<br/>
							<label style="padding-left: 20px;">'.$hdr['confirmByName'].' / <small>'.$hdr['confirmTime'].'</small></label><br/>
							Driver by .....'.$hdr['driver'].'.....
						</td>
						
						<td colspan="6" style="text-align: left;"><br/><br/>							
							Approve by ..............................................................<br/>
							<label style="padding-left: 20px;">'.$hdr['approveByName'].' / <small>'.$hdr['approveTime'].'</small></label><br/>
						</td>
						
					</tr>';
					
					$html .='</tbody></table>';
						
					$pdf->AddPage('L','A5');
					$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
					}
					//<!--if isset $_GET['from_date']-->
		
		 
		   

// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.
$pdf->Output($ppNo.'_Shelf.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
	?>