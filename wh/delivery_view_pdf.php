<?php

include('session.php');
//include('prints_function.php');
//include('inc_helper.php');
function to_thai_date($eng_date){
	if(strlen($eng_date) != 10){
		return null;
	}else{
		$new_date = explode('-', $eng_date);

		$new_y = (int) $new_date[0] + 543;
		$new_m = $new_date[1];
		$new_d = $new_date[2];

		$thai_date = $new_d . '/' . $new_m . '/' . $new_y;

		return $thai_date;
	}
}
function to_thai_datetime_fdt($eng_date){
	//if(strlen($eng_date) != 10){
	//    return null;
	//}else{
		$new_datetime = explode(' ', $eng_date);
		$new_date = explode('-', $new_datetime[0]);

		$new_y = (int) $new_date[0] + 543;
		$new_m = $new_date[1];
		$new_d = $new_date[2];

		$thai_date = $new_d . '/' . $new_m . '/' . $new_y . ' ' . substr($new_datetime[1],0,5);

		return $thai_date;
	//}
}
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
	
	$doNo = $_GET['doNo'];

	$sql = "
	SELECT dh.`doNo`, dh.`soNo`, dh.`ppNo`, oh.`poNo`
	, dh.`deliveryDate`, dh.`remark`, dh.`driver`
	, dh.`statusCode`, dh.`createTime`, dh.`createById`, dh.`updateTime`, dh.`updateById`
	, dh.`confirmTime`, dh.`confirmById`, dh.`approveTime`, dh.`approveById`
	, ct.code as custCode, ct.name as  custName
	, st.code as shipToCode, st.name as  shipToName ,st.addr1 as shipToAddr1, st.addr2 as shipToAddr2, st.addr3 as shipToAddr3, st.zipcode as shipToZipcode, st.tel as shipToTel, st.fax as shipToFax
	, sm.code as smCode, sm.name as smName, sm.surname as smSurname, concat(sm.name, '  ', sm.surname) as smFullname 
	, uca.userFullname as createByName, ucf.userFullname as confirmByName, uap.userFullname as approveByName
	FROM delivery_header dh 
	LEFT JOIN sale_header oh on dh.soNo=oh.soNo 
	LEFT JOIN customer ct on ct.id=oh.custId
	LEFT JOIN shipto st on st.id=oh.shipToId
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

	$sql = "
	SELECT dtl.`id`, dtl.`qty`, dtl.remark 
	,pd.name as prodName, pd.code as prodCode, pd.uomCode
	, IFNULL((SELECT SUM(sd.qty) FROM sale_detail sd
			WHERE sd.soNo=hdr.soNo
			AND sd.prodId=dtl.prodId),0) AS sumSalesQty
	, (SELECT IFNULL(SUM(dds.qty),0) FROM delivery_header dhs 
		INNER JOIN delivery_prod dds on dhs.doNo=dds.doNo
		WHERE dds.prodId=dtl.prodId 
		AND dhs.statusCode='P' ) as sumSentQty
	, IFNULL(SUM(dtl.qty),0) as sumDeliveryQty 
	FROM delivery_prod dtl
	INNER JOIN delivery_header hdr on hdr.doNo=dtl.doNo 
	LEFT JOIN product pd ON pd.id=dtl.prodId 
	WHERE 1 
	AND hdr.doNo=:doNo

	ORDER BY dtl.`id`
	";
	$stmt = $pdo->prepare($sql);	
	$stmt->bindParam(':doNo', $hdr['doNo']);
	$stmt->execute();
	
	//Loop all item
	$iRow=0;		
	$row_no = 1; $sumQty=$sumNW=$sumGW=0; while ($row = $stmt->fetch()) { 
		if($iRow==0){
			$pdf->AddPage('L','A5');
		
		
			$pdf->Cell(125, 0, '', 0, 0, 'L', 0, '', 0, false, 'T', 'B');
			$pdf->Cell(50, 0, to_thai_date($hdr['deliveryDate']), 0, 0, 'L', 0, '', 0, false, 'T', 'B');
			$pdf->Ln(6);
			
			$pdf->Cell(50, 0, '', 0, 0, 'L', 0, '', 0, false, 'T', 'B');
			$pdf->Cell(50, 0, $hdr['custName'], 0, 0, 'L', 0, '', 0, false, 'T', 'B');
			$pdf->Cell(20, 0, $hdr['custCode'], 0, 0, 'L', 0, '', 0, false, 'T', 'B');
			$custOrder = trim($hdr['soNo']);
			$custOrder=($hdr['poNo']<>""?'/'.$hdr['poNo']:'');
			$pdf->Cell(50, 0, $custOrder, 0, 0, 'L', 0, '', 0, false, 'T', 'B');
			$pdf->Ln(6);
			
			$pdf->Cell(50, 0, '', 0, 0, 'L', 0, '', 0, false, 'T', 'B');
			$pdf->Cell(100, 0, $hdr['shipToAddr1'], 0, 0, 'L', 0, '', 0, false, 'T', 'B');
			$pdf->Cell(50, 0, $hdr['smName'], 0, 0, 'L', 0, '', 0, false, 'T', 'B');
			$pdf->Cell(50, 0, $hdr['smCode'], 0, 0, 'L', 0, '', 0, false, 'T', 'B');
			$pdf->Ln(3);
			
			$pdf->Cell(50, 0, '', 0, 0, 'L', 0, '', 0, false, 'T', 'B');
			$pdf->Cell(100, 0, $hdr['shipToAddr2'], 0, 0, 'L', 0, '', 0, false, 'T', 'B');
			$pdf->Ln(6);
		}//end if iRow=0					
		
		$pdf->Cell(10, 0, $row_no, 0, 0, 'L', 0, '', 0, false, 'T', 'B');
		$pdf->Cell(80, 0, $row['prodName'], 0, 0, 'L', 0, '', 0, false, 'T', 'B');
		$pdf->Cell(50, 0, $row['prodCode'], 0, 0, 'L', 0, '', 0, false, 'T', 'B');
		$pdf->Cell(20, 0, $row['sumSalesQty'], 0, 0, 'L', 0, '', 0, false, 'T', 'B');
		$pdf->Cell(20, 0, $row['sumDeliveryQty'], 0, 0, 'L', 0, '', 0, false, 'T', 'B');
		$pdf->Cell(50, 0, $row['remark'], 0, 0, 'L', 0, '', 0, false, 'T', 'B');
		$pdf->Ln(6);
		
		$row_no+=1;
		$iRow+=1;
		
		if($iRow==10){
			//foot document.
			$pdf->Cell(50, 0, $hdr['confirmByName'], 0, 0, 'L', 0, '', 0, false, 'T', 'B');
			$pdf->Cell(50, 0, $hdr['approveByName'], 0, 0, 'L', 0, '', 0, false, 'T', 'B');
			$pdf->Ln(6);
			
			$pdf->Cell(50, 0, '', 0, 0, 'L', 0, '', 0, false, 'T', 'B');
			$pdf->Cell(50, 0, $hdr['driver'], 0, 0, 'L', 0, '', 0, false, 'T', 'B');
			$pdf->Ln(6);					
			
			$iRow=0;
		}	
	
		if($iRow<>10){
			for($iRowRemain=$iRow; $iRowRemain<=10; $iRowRemain++){
				$pdf->Cell(50, 0, '-', 0, 0, 'L', 0, '', 0, false, 'T', 'B');
				$pdf->Ln(6);
			}
		}

		//foot document.
		$pdf->Cell(50, 0, $hdr['confirmByName'], 0, 0, 'L', 0, '', 0, false, 'T', 'B');
		$pdf->Cell(50, 0, $hdr['approveByName'], 0, 0, 'L', 0, '', 0, false, 'T', 'B');
		$pdf->Ln(6);
		
		$pdf->Cell(50, 0, '', 0, 0, 'L', 0, '', 0, false, 'T', 'B');
		$pdf->Cell(50, 0, $hdr['driver'], 0, 0, 'L', 0, '', 0, false, 'T', 'B');
		$pdf->Ln(6);
	
	
	}
	//end while

}//end if id No.		 
		   

// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.
$pdf->Output($ppNo.'_Shelf.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
	?>