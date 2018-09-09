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


    public function head($hdr){
		//head 
		//$this->AddPage('P');
		
		$this->SetFont('THSarabun', 'B', 12, '', true);
		
		$this->setCellHeightRatio(1);
		
		$html='<table width="100%"  >		
		<tr>
			<td rowspan="3" ><img src="../asset/img/logo-ak_60x60.jpg" /></td>
			<td colspan="5"><span style="font-size: 120%;" >บริษัท เอเซีย กังนัม จำกัด</span></td>
			<td></td>
			<td colspan="3" ></td>
		</tr>
		<tr>
			<td colspan="5"><span style="font-size: 120%;" >ASIA KANGNAM  COMPANY LIMITED</span></td>
			<td></td>
			<td colspan="3" ></td>
		</tr>
		<tr>
			<td colspan="5"><span style="font-size: 60%;" >69/1 ม.6 ต.ท่าข้าม อ.บางปะกง จ.ฉะเชิงเทรา 24130  โทร 0 – 3857 – 3635 แฟ็กซ์ 0 – 3857 – 3634</span></td>
			<td colspan="2"></td>
			<td colspan="2"></td>
		</tr>
		<tr>
			<td style="text-align: center;"><span style="font-size: 60%;" >www.askn.com</span></td>
			<td colspan="5"><span style="font-size: 60%;" >69/1 Moo 6, Thakam, Bangpakong, Chachoengsao 24130 Thailand Tel: 66 – 3857 – 3635 Fax: 66 – 3857 – 3634</span></td>
			<td colspan="2"></td>
			<td colspan="2"></td>
		</tr>
		</table>
		';

		$html.='<table width="100%"  >	
		<tr>
			<td colspan="3" ></td>
			<td colspan="3" style="border: o.1em solid black; text-align: center; font-size: large;" >ใบส่งสินค้า (DELIVERY ORDER)</td>
			<td colspan="2" style="font-size: 95%;" >&nbsp;วันที่ (Date) : </td>
			<td colspan="2"  style="border-bottom: 0.1em solid black;">'.date('d M Y',strtotime( $hdr['deliveryDate'] )).'</td>
		</tr>
		<tr>
			<td colspan="1" >ชื่อลูกค้า : </td>
			<td colspan="4"  style="border-bottom: 0.1em solid black;"> '.$hdr['custName'].'</td>			
			<td colspan="1" > รหัสลูกค้า : </td>
			<td colspan="1"  style="border-bottom: 0.1em solid black;" > '.$hdr['custCode'].'</td>
			<td colspan="1" >เลขที่ใบสั่งขาย :</td>
			<td colspan="1"  style="border-bottom: 0.1em solid black;" > '.$hdr['soNo'].'</td>		
		</tr>
		<tr>
			<td colspan="1" ><span>สถานที่ส่ง : </span></td>
			<td colspan="4"  style="border-bottom: 0.1em solid black;"> '.$hdr['shipToName'].'</td>	
			<td colspan="1" > พนักงานขาย : </td>
			<td colspan="1"  style="border-bottom: 0.1em solid black;" > '.$hdr['smName'].'</td>
			<td colspan="1" >รหัสพนักงาน :</td>
			<td colspan="1"  style="border-bottom: 0.1em solid black;" > '.$hdr['smCode'].'</td>		
		</tr>
		<tr>
			<td colspan="10"  style="border-bottom: 0.1em solid black;">'.$hdr['shipToAddr1'].' '.$hdr['shipToAddr2'].'</td>
		</tr>
		<tr>
			<td colspan="10"  style="border-bottom: 0.1em solid black;">'.$hdr['shipToAddr3'].$hdr['shipToZipcode'].'</td>
		</tr>	
		</table>
		';
		$this->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
		$this->setCellHeightRatio(1.25);
	}
	
	public function foot($hdr, $html){
		$html .='</tbody></table>';
		$this->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
		//$pdf->Ln(2);
						
		$html='<table width="100%"  >
		<tr>
			<td colspan="6"><br/><br/>
				ผู้จัดทำ : <span style="text-decoration: underline;">
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
				<br/>
				หมายเลขรถ : <span style="text-decoration: underline;">
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
				<br/>
				ผู้ส่ง &nbsp;&nbsp;  :  <span style="text-decoration: underline;">
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
				<br/>
				ลงชื่อ รปภ. :  <span style="text-decoration: underline;">
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
			</td>
			
			<td colspan="6" style="text-align: left;"><br/><br/>							
				ผู้อนุมัติ &nbsp;&nbsp;&nbsp;&nbsp;: <span style="text-decoration: underline;">
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
				<br/>
				ผู้รับ &nbsp;&nbsp; : <span style="text-decoration: underline;">
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
				<br/>
				วันที่รับ : '.'<span style="text-decoration: underline;">
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>'.'
			</td>		
		</tr>
		</table>
		';
		$this->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
				
	}
}

date_default_timezone_set("Asia/Bangkok");

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
$pdf->SetMargins(5, 3, 5, 3);	//right=5, top=2, bottom=0 
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
//$pdf->SetFont('THSarabun', '', 14, '', true);

// Set some content to print
if( isset($_GET['doNo']) ){
	$doNo = $_GET['doNo'];
	
	$pdf->SetTitle($doNo);

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
	GROUP BY dtl.`prodId` 
	ORDER BY dtl.`id`
	";
	$stmt = $pdo->prepare($sql);	
	$stmt->bindParam(':doNo', $hdr['doNo']);
	$stmt->execute();
	

	//Loop all item
	$iRow=0;
	$row_no = 1;  while ($row = $stmt->fetch()) { 
		if($iRow==0){
			$pdf->AddPage('L','A5');

			$pdf->head($hdr);
			
			$html="";					
			$html ='
					<table class="table table-striped no-margin" style="width:100%; table-layout: fixed;"  >
						<thead>	
							<tr>
								<th style="font-weight: bold; text-align: center; width: 60px; border: 0.1em solid black;">ลำดับที่</th>										
								<th style="font-weight: bold; text-align: center; width: 400px; border: 0.1em solid black;">รายการสินค้า</th>							
								<th style="font-weight: bold; text-align: center; width: 60px; border: 0.1em solid black;">จำนวน</th>								
								<th style="font-weight: bold; text-align: center; border: 0.1em solid black;">หมายเหตุ</th>
							</tr>
						</thead>
						  <tbody>
					'; 
		}
		//endif iRow==0 
		$html .='<tr>	
					<td style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;  max-width: 60px;
								border: 0.1em solid black; text-align: right; width: 60px;">'.$row_no.'</td>						
					<td style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
								border: 0.1em solid black; padding: 10px; width: 400px;"> 
								 '.$row['prodCode'].'</td>
					<td style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap; 
								border: 0.1em solid black; text-align: right; width: 60px;">'.number_format($row['qty'],0,'.',',').'</td>

					<td style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap; 
								border: 0.1em solid black;">'.$row['remark'].'</td>		
									
					
				</tr>';	
		
		//Loop item per page
		$iRow+=1;
		if($iRow==7){
			$pdf->foot($hdr, $html);
			//Re			
			$iRow=0;
		}
	}//end loop all item
	
	if($iRow<>8){
		for($iRowRemain=$iRow; $iRowRemain<=7; $iRowRemain++){
			$html .='<tr>
					<td style="font-weight: bold; text-align: center; width: 60px;border: 0.1em solid black;"></td>
					<td style="font-weight: bold; text-align: center; width: 400px;border: 0.1em solid black;"></td>
					<td style="font-weight: bold; text-align: center; width: 60px;border: 0.1em solid black;"></td>								
					<td style="font-weight: bold; text-align: center; border: 0.1em solid black;"></td>			
				</tr>';	
		}
	}	
	
	$pdf->foot($hdr, $html);
}//end if id No.		 
		   





// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.
$pdf->Output($ppNo.'_Shelf.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
	?>