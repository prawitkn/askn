<?php

include('session.php');
//include('prints_function.php');
//include('inc_helper.php');

// Include the main TCPDF library (search for installation path).
require_once('../tcpdf/tcpdf.php');

class MYPDF extends TCPDF {

    //Page header
    public function Header() {
		$this->setImageScale(PDF_IMAGE_SCALE_RATIO);
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
		
		/*$this->SetFont('Times', 'B', 16, '', true);		
		$this->SetY(11);	
		$this->Cell(0, 5, 'Asia Kungnum Co.,Ltd.', 0, false, 'C', 0, '', 0, false, 'M', 'M');
		$this->Ln(7);
		$this->SetFont('Times', 'B', 14, '', true);	
        $this->Cell(0, 5, 'Sales Order', 0, false, 'C', 0, '', 0, false, 'M', 'M');*/
    }
    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        ///$this->SetY(-15);
        // Set font 
        $this->SetFont('THSarabun', '', 12, '', true);
        // Page number
		$tmp = date('Y-m-d H:i:s');
		//$tmp = to_thai_short_date_fdt($tmp);
		$this->Cell(0, 10,'FM-MS-003; rev.03', 0, false, 'L', 0, '', 0, false, 'T', 'M');
		$this->Cell(0, 10,'Print : '. $tmp, 0, false, 'R', 0, '', 0, false, 'T', 'M');
    }
	public function head($hdr){
		//head 
		$this->AddPage('P');
		
		$this->SetFont('THSarabun', '', 12, '', true);
		
		$this->setCellHeightRatio(1.25);
		
		$html='<table width="100%"  >		
		<tr>
			<td rowspan="3" ><img src="../asset/img/logo-ak_60x60.jpg" /></td>
			<td colspan="5"><span style="font-size: 120%;" >บริษัท เอเชีย กังนัม จำกัด</span></td>
			<td></td>
			<td colspan="3" >&nbsp;<img src="dist/img/icon/radio-'.($hdr['suppTypeFact']==1?'checked':'uncheck').'.jpg" width="75%" height="75%" /> <span style="font-size: 85%;" >สินค้าผลิตในโรงงาน</span></td>
		</tr>
		<tr>
			<td colspan="5"><span style="font-size: 120%;" >ASIA KANGNAM  COMPANY LIMITED</span></td>
			<td></td>
			<td colspan="3" >&nbsp;<img src="dist/img/icon/radio-'.($hdr['suppTypeImp']==1?'checked':'uncheck').'.jpg" width="75%" height="75%" /> <span style="font-size: 85%;" >สินค้านำเข้าจากต่างประเทศ</span></td>
		</tr>
		<tr>
			<td colspan="5"><span style="font-size: 60%;" >69/1 ม.6 ต.ท่าข้าม อ.บางปะกง จ.ฉะเชิงเทรา 24130  โทร 0 – 3857 – 3635 แฟ็กซ์ 0 – 3857 – 3634</span></td>
			<td colspan="2">&nbsp;<img src="dist/img/icon/radio-'.($hdr['prodTypeOld']==1?'checked':'uncheck').'.jpg" width="75%" height="75%" /> <span style="font-size: 85%;" >สินค้าเก่า (Current Product)</span></td>
			<td colspan="2">&nbsp;<img src="dist/img/icon/radio-'.($hdr['prodTypeNew']==1?'checked':'uncheck').'.jpg" width="75%" height="75%" /> <span style="font-size: 85%;" >สินค้าใหม่ (New Product)</span></td>
		</tr>
		<tr>
			<td style="text-align: center;"><span style="font-size: 60%;" >www.askn.com</span></td>
			<td colspan="5"><span style="font-size: 60%;" >69/1 Moo 6, Thakam, Bangpakong, Chachoengsao 24130 Thailand Tel: 66 – 3857 – 3635 Fax: 66 – 3857 – 3634</span></td>
			<td colspan="2">&nbsp;<img src="dist/img/icon/radio-'.($hdr['custTypeOld']==1?'checked':'uncheck').'.jpg" width="75%" height="75%" /> <span style="font-size: 85%;" >ลูกค้าเก่า (Current Customer)</span></td>
			<td colspan="2">&nbsp;<img src="dist/img/icon/radio-'.($hdr['custTypeNew']==1?'checked':'uncheck').'.jpg" width="75%" height="75%" /> <span style="font-size: 85%;" >ลูกค้าใหม่ (New Customer)</span></td>
		</tr>
		</table>
		';
		$this->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
		$this->setCellHeightRatio(1.50);
		
		$html='<table width="100%"  >	
		<tr>
			<td colspan="3" style="border: o.1em solid black; text-align: center; font-size: large;">SALES ORDER FORM (ใบสั่งขาย)</td>
			<td colspan="3" ></td>
			<td colspan="2" >&nbsp;รหัสลูกค้า (Customer Code) : </td>
			<td colspan="2"  style="border-bottom: 0.1em solid black;">'.$hdr['custCode'].'</td>
		</tr>
		<tr>
			<td colspan="2" >ชื่อลูกค้า (Customer Name) : </td>
			<td colspan="4"  style="border-bottom: 0.1em solid black;">'.$hdr['custName'].'</td>			
			<td colspan="2" >&nbsp;วันที่ (Date) : </td>
			<td colspan="2" style="border-bottom: 0.1em solid black;">'.date('d M Y',strtotime( $hdr['saleDate'] )).'</td>			
		</tr>
		<tr>
			<td colspan="2" ><span style="font-size: 90%;" >ที่อยู่เปิด Invoice (Destination) : </span></td>
			<td colspan="4"  style="border-bottom: 0.1em solid black;">'.$hdr['shipToName'].'</td>	
			<td colspan="2" >&nbsp;SO No. : </td>
			<td colspan="2"  style="border-bottom: 0.1em solid black;">'.$hdr['soNo'].($hdr['revCount']<>0?' rev.'.$hdr['revCount']:'').'</td>
		</tr>
		<tr>
			<td colspan="6"  style="border-bottom: 0.1em solid black;">'.$hdr['shipToAddr1'].'</td>
			<td colspan="2" >&nbsp;PO No. : </td>
			<td colspan="2"  style="border-bottom: 0.1em solid black;">'.$hdr['poNo'].'</td>
		</tr>
		<tr>
			<td colspan="6" style="border-bottom: 0.1em solid black;">'.$hdr['shipToAddr2'].'</td>
			<td colspan="2" >&nbsp;PI No. : </td>
			<td colspan="2"  style="border-bottom: 0.1em solid black;">'.$hdr['piNo'].'</td>
		</tr>
		<tr>
			<td colspan="6"  style="border-bottom: 0.1em solid black;">'.$hdr['shipToAddr3'].$hdr['shipToZipcode'].'</td>
			<td colspan="2" ></td>
			<td colspan="2" ></td>
		</tr>
		</table>
		';
		$this->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
		$this->setCellHeightRatio(1.50);
	}
	
	public function foot($hdr, $html){
		$html .='</tbody></table>';
		$this->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
		//$pdf->Ln(2);
		
		$html='<table width="100%"  >
		<tr>
			<td colspan="3" >&nbsp;<img src="dist/img/icon/checkbox-'.($hdr['prodStkInStk']==1?'checked':'uncheck').'.jpg" width="75%" height="75%" /> สินค้ามีในสต๊อกทั้งหมด / บางส่วน</td>
			<td colspan="2"  >&nbsp;<img src="dist/img/icon/checkbox-'.($hdr['prodStkOrder']==1?'checked':'uncheck').'.jpg" width="75%" height="75%" /> สินค้าสั่งผลิต</td>
			<td colspan="1"  >&nbsp;<img src="dist/img/icon/checkbox-'.($hdr['prodStkOther']==1?'checked':'uncheck').'.jpg" width="75%" height="75%" /> 
				<span style="font-size: 80%;">อื่นๆ  (Other)</span></td>		
			<td colspan="4" style="border-bottom: 0.1em solid black;" >'.$hdr['prodStkRem'].'</td>
		</tr>
		<tr>
			<td colspan="2" ><span style="text-decoration: underline;">การบรรจุ (Packing)</span> : </td>
			<td colspan="2"  >&nbsp;<img src="dist/img/icon/checkbox-'.($hdr['packTypeAk']==1?'checked':'uncheck').'.jpg" width="75%" height="75%" />  มี LOGO AK</td>
			<td colspan="1" >&nbsp;<img src="dist/img/icon/checkbox-'.($hdr['packTypeNone']==1?'checked':'uncheck').'.jpg" width="75%" height="75%" />  ไม่มี LOGO</td>
			<td colspan="1" >&nbsp;<img src="dist/img/icon/checkbox-'.($hdr['packTypeOther']==1?'checked':'uncheck').'.jpg" width="75%" height="75%" /> 
				<span style="font-size: 80%;">อื่นๆ  (Other)</span></td>	
			<td colspan="4" style="border-bottom: 0.1em solid black;" >'.$hdr['packTypeRem'].'</td>
		</tr>
		<tr>
			<td colspan="2" ><span style="text-decoration: underline; font-size: 90%;">กรณีส่งต่างประเทศ (Export) by</span> : </td>
			<td >&nbsp;<img src="dist/img/icon/checkbox-'.($hdr['shipByLcl']==1?'checked':'uncheck').'.jpg" width="75%" height="75%" /> LCL</td>
			<td >&nbsp;<img src="dist/img/icon/checkbox-'.($hdr['shipByFcl']==1?'checked':'uncheck').'.jpg" width="75%" height="75%" /> FCL</td>
			<td colspan="1" >Load Remark : </td>
			<td colspan="5" style="border-bottom: 0.1em solid black;" >'.$hdr['shipByRem'].'</td>
		</tr>
		<tr>
			<td colspan="2" ><span style="text-decoration: underline;">Shipping Options</span></td>
			<td colspan="2" >&nbsp;<img src="dist/img/icon/checkbox-'.($hdr['remCoa']==1?'checked':'uncheck').'.jpg" width="75%" height="75%" />  ขอ COA</td>
			<td colspan="2" ><span style="text-decoration: underline;">Shipping Mark</span> : </td>
		</tr>
		<tr>
			<td colspan="2" ><span style="text-decoration: underline;"></span></td>
			<td colspan="2" >&nbsp;<img src="dist/img/icon/checkbox-'.($hdr['remPalletBand']==1?'checked':'uncheck').'.jpg" width="75%" height="75%" /> PALLET ตีตรา </td>
			<td colspan="2" rowspan="2" >'.
			($hdr['shippingMarksFilePath']!=""?'<img src="../images/shippingMarks/'.$hdr['shippingMarksFilePath'].'" />':'<img src="../images/shippingMarks/default.jpg" />').
			'</td>
		</tr>
		<tr>
			<td colspan="2" ></td>
			<td colspan="2" >&nbsp;<img src="dist/img/icon/checkbox-'.($hdr['remFumigate']==1?'checked':'uncheck').'.jpg" width="75%" height="75%" /> รมยาตู้คอนเทนเนอร์</td>			
		</tr>
		<tr>
			<td colspan="2" >ราคา (Price) : </td>
			<td colspan="2" >&nbsp;<img src="dist/img/icon/checkbox-'.($hdr['priceOnOrder']==1?'checked':'uncheck').'.jpg" width="75%" height="75%" />  ตามใบสั่งซื้อ</td>
			<td colspan="1" >&nbsp;<img src="dist/img/icon/checkbox-'.($hdr['priceOnOther']==1?'checked':'uncheck').'.jpg" width="75%" height="75%" /> 
				<span style="font-size: 80%;">อื่นๆ  (Other)</span></td>	
			<td colspan="5" style="border-bottom: 0.1em solid black;" >'.$hdr['priceOnRem'].'</td>
		</tr>
		<tr>
			<td colspan="2" >ผู้เสนอขาย (Sales) : </td>
			<td colspan="8" style="border-bottom: 0.1em solid black;" >'.$hdr['smName'].'</td>
		</tr>
		<tr>
			<td colspan="2" >หมายเหตุ : </td>
			<td colspan="8" style="border-bottom: 0.1em solid black;" >'.$hdr['remark'].'</td>
		</tr>		
		</table>
		';
		$this->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
		
		$html='<table width="100%"  >
		<tr>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td colspan="3"  style="border-top: 0.1em solid black; border-left: 0.1em solid black;" ></td>
			<td colspan="3"  style="border-top: 0.1em solid black; border-left: 0.1em solid black; border-right: 0.1em solid black;" >&nbsp;สถานที่ส่งสินค้า (Place to Delivery)</td>
			<td colspan="4"  style="border-top: 0.1em solid black; border-left: 0.1em solid black; border-right: 0.1em solid black;" ></td>
		</tr>
		<tr>
			<td colspan="3" style="border-left: 0.1em solid black;">&nbsp;เครดิต (Credit) '.'<span style="text-decoration: underline; font-size: 120%; font-weight: bold;">'.$hdr['payTypeCreditDays'].'</span> วัน (Days)</td>
			<td colspan="3" style="border-left: 0.1em solid black; border-right: 0.1em solid black;">&nbsp;<img src="dist/img/icon/radio-'.($hdr['plac2deliCode']=='FACT'?'checked':'uncheck').'.jpg" width="75%" height="75%" />&nbsp;ลูกค้ามารับที่โรงงาน AK</td>
			<td colspan="4" style="border-left: 0.1em solid black; border-right: 0.1em solid black;">&nbsp;จัดทำโดย (Issue By) : <span style="text-decoration: underline;">'.$hdr['createByName'].'</span></td>
		</tr>
		<tr>
			<td colspan="3" style="border-left: 0.1em solid black;">&nbsp;<img src="dist/img/icon/radio-'.($hdr['payTypeCode']=='CASH'?'checked':'uncheck').'.jpg" width="75%" height="75%" />&nbsp;เก็บเงินสด</td>
			<td colspan="3" style="border-left: 0.1em solid black; border-right: 0.1em solid black;">&nbsp;<img src="dist/img/icon/radio-'.($hdr['plac2deliCode']=='SEND'?'checked':'uncheck').'.jpg" width="75%" height="75%" />&nbsp;ส่งสินค้าจากโรงงาน AK ที่ 
				<span style="text-decoration: underline; ">'.$hdr['plac2deliCodeSendRem'].'</span>
			</td>
			<td colspan="4" style="border-left: 0.1em solid black; border-right: 0.1em solid black;">&nbsp;วันที่ (Date) : <span style="text-decoration: underline;">'.date('d M Y H:m',strtotime( $hdr['createTime'] )).'</span></td>
		</tr>
		<tr>
			<td colspan="3" style="border-left: 0.1em solid black;">&nbsp;<img src="dist/img/icon/radio-'.($hdr['payTypeCode']=='CHEQ'?'checked':'uncheck').'.jpg" width="75%" height="75%" />&nbsp;เก็บเช็คล่วงหน้า</td>
			<td colspan="3" style="border-left: 0.1em solid black;">&nbsp;<img src="dist/img/icon/radio-'.($hdr['plac2deliCode']=='MAPS'?'checked':'uncheck').'.jpg" width="75%" height="75%" />&nbsp;ตามแผนที่ </td>
			<td colspan="4" style="border-left: 0.1em solid black; border-right: 0.1em solid black;">&nbsp;ตรวจสอบโดย (ผู้ขาย) : <span style="text-decoration: underline;">
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
		</tr>
		<tr>
			<td colspan="3" style="border-left: 0.1em solid black;">&nbsp;<img src="dist/img/icon/radio-'.($hdr['payTypeCode']=='TRAN'?'checked':'uncheck').'.jpg" width="75%" height="75%" />&nbsp;ลูกค้าโอนเงินเข้าบัญชี</td>
			<td colspan="3" style="border-left: 0.1em solid black;">&nbsp;<img src="dist/img/icon/radio-'.($hdr['plac2deliCode']=='LOGI'?'checked':'uncheck').'.jpg" width="75%" height="75%" />&nbsp;ขนส่ง </td>
			<td colspan="4" style="border-left: 0.1em solid black; border-right: 0.1em solid black;">&nbsp;ผู้อนุมัติ (Approved by) : <span style="text-decoration: underline;">
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
		</tr>
		<tr>
			<td colspan="3"  style="border-bottom: 0.1em solid black; border-left: 0.1em solid black;"  ></td>
			<td colspan="3"  style="border-bottom: 0.1em solid black; border-left: 0.1em solid black;" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(เก็บเงินปลายทาง)</td>
			<td colspan="4"  style="border-bottom: 0.1em solid black; border-left: 0.1em solid black; border-right: 0.1em solid black;" ></td>
		</tr>
		</table>
		';
		$this->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
				
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
$pdf->SetMargins(15, 15, 10);	//หน้า ๓ บนถึงตูดเลขหน้า ๒ ตูดเลขหน้าถึงตูดบรรทัดแรก ๑.๕
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
$pdf->SetFont('THSarabun', '', 16, '', true);

//Set Line spacing
$pdf->setCellHeightRatio(1.50);

// Set some content to print
if( isset($_GET['soNo']) ){			
			$pdf->SetTitle($_GET['soNo']);
						
			$soNo = $_GET['soNo'];
			$sql = "
			SELECT a.`soNo`, a.`saleDate`,a.`poNo`,a.`piNo`, a.`custId`,  a.`shipToId`, a.`smId`, a.`revCount`
			, a.`deliveryDate`, a.`shipByLcl`, a.`shipByFcl`, a.`shipByRem`, a.`shippingMarksId`, a.`suppTypeFact`
			, a.`suppTypeImp`, a.`prodTypeOld`, a.`prodTypeNew`, a.`custTypeOld`, a.`custTypeNew`
			, a.`prodStkInStk`, a.`prodStkOrder`, a.`prodStkOther`, a.`prodStkRem`, a.`packTypeAk`
			, a.`packTypeNone`, a.`packTypeOther`, a.`packTypeRem`, a.`priceOnOrder`, a.`priceOnOther`
			, a.`priceOnRem`, a.`remark`, a.`plac2deliCode`, a.`plac2deliCodeSendRem`, a.`plac2deliCodeLogiRem`, a.`payTypeCode`, a.`payTypeCreditDays`
			, a.`isClose`, a.`statusCode`, a.`createTime`, a.`createByID`, a.`updateTime`, a.`updateById`
			, a.shippingMark, a.`remCoa`, a.`remPalletBand`, a.`remFumigate`
			, b.code as custCode, b.name as custName, b.addr1 as custAddr1, b.addr2 as custAddr2, b.addr3 as custAddr3, b.zipcode as custZipcode, b.tel as custTel, b.fax as custFax
			, st.code as shipToCode, st.name as shipToName, st.addr1 as shipToAddr1, st.addr2 as shipToAddr2, st.addr3 as shipToAddr3, st.zipcode as shipToZipcode, st.tel as shipToTel, st.fax as shipToFax
			, c.code as smCode, c.name as smName, c.surname as smSurname
			, spm.name as shippingMarksName, IFNULL(spm.filePath,'') as shippingMarksFilePath
			
			, d.userFullname as createByName
			, a.confirmTime, cu.userFullname as confirmByName
			, a.approveTime, au.userFullname as approveByName
			FROM `sale_header` a
			left join customer b on b.id=a.custId 
			left join shipto st on st.id=a.shipToId  
			left join salesman c on c.id=a.smId 
			left join shipping_marks spm on spm.id=a.shippingMarksId 
			left join user d on a.createById=d.userId
			left join user cu on a.confirmById=cu.userId
			left join user au on a.approveById=au.userId
			WHERE 1
			AND a.soNo=:soNo 					
			ORDER BY a.createTime DESC
			LIMIT 1
			";
			$stmt = $pdo->prepare($sql);			
			$stmt->bindParam(':soNo', $soNo);	
			$stmt->execute();
			$hdr = $stmt->fetch();	
	   		
			$sql = "
			SELECT COUNT(*) as countTotal 
			FROM `sale_detail` a
			LEFT JOIN product b on b.id=a.prodId 
			WHERE 1
			AND a.`soNo`=:soNo 
			ORDER BY a.createTime
			";
			$stmt = $pdo->prepare($sql);	
			$stmt->bindParam(':soNo', $hdr['soNo']);
			$stmt->execute();
			$row = $stmt->fetch();
			$countTotal = $row['countTotal'];
			
			$sql = "
			SELECT a.`id`, a.`prodId`, a.`salesPrice`, a.`qty`, a.`rollLengthId`, a.`remark`, a.deliveryDate, a.`soNo`
			, b.code as prodCode, b.name as prodName, b.uomCode as prodUomCode, b.description 
			, (SELECT IFNULL(SUM(id.qty),0) FROM invoice_detail id 
					INNER JOIN invoice_header ih on ih.invNo=id.invNo										
					INNER JOIN delivery_header dh on dh.doNo=ih.doNo 
					WHERE dh.soNo=a.soNo AND id.prodCode=a.prodId ) as sentQty 
			, rl.name as rollLengthName 
			FROM `sale_detail` a
			LEFT JOIN product b on a.prodId=b.id
			LEFT JOIN product_roll_length rl ON rl.id=a.rollLengthId 
			WHERE 1
			AND a.`soNo`=:soNo 
			ORDER BY a.createTime
			";
			$stmt = $pdo->prepare($sql);	
			$stmt->bindParam(':soNo', $hdr['soNo']);
			$stmt->execute();	
			
			
			
			
			//Loop all item
			$iRow=0;
			$row_no = 1;  while ($row = $stmt->fetch()) { 
				if($iRow==0){
					
					$pdf->head($hdr);
					
					$html="";					
					$html ='
							<table class="table table-striped no-margin" style="width:100%; table-layout: fixed;"  >
								<thead>	
									<tr>										
										<th style="font-weight: bold; text-align: center; width: 150px; border: 0.1em solid black;">Product Name</th>
										<th style="font-weight: bold; text-align: center; width: 150px; border: 0.1em solid black;">Product Code</th>
										<th style="font-weight: bold; text-align: center; width: 170px; border: 0.1em solid black;">Specification</th>								
										<th style="font-weight: bold; text-align: center; width: 60px; border: 0.1em solid black;">Qty</th>								
										<th style="font-weight: bold; text-align: center; width: 40px; border: 0.1em solid black;">Unit</th>
										<th style="font-weight: bold; text-align: center; width: 80px; border: 0.1em solid black;"><span style="font-size: 75%">Delivery/Load Date</span></th>
									</tr>
								</thead>
								  <tbody>
							'; 
				}
				$html .='<tr>							
							<td style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 150px;
										border: 0.1em solid black; padding: 10px; width: 150px;"> 
										 '.$row['prodName'].'</td>
							<td style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;  max-width: 150px;
										border: 0.1em solid black; padding: 10px; width: 150px;"> '.$row['prodCode'].'</td>
							<td style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;  max-width: 150px;
										border: 0.1em solid black; padding: 10px; width: 170px;"> '.$row['remark'].' '.($row['rollLengthId']<>'0'?'[RL:'.$row['rollLengthName'].']':'').'</td>
							<td style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;  max-width: 60px;
										border: 0.1em solid black; text-align: right; width: 60px;">'.number_format($row['qty'],0,'.',',').'</td>						
							<td style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;  max-width: 40px;
										border: 0.1em solid black; text-align: right; width: 40px;">'.$row['prodUomCode'].'</td>						
							<td style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 65px;
										border: 0.1em solid black; padding: 10px; width: 80px;"> '.date('d M Y',strtotime( $row['deliveryDate'] )).'</td>
						</tr>';	
				
				//Loop item per page
				$iRow+=1;
				if($iRow==8){
					$pdf->foot($hdr, $html);
					
					
					$iRow=0;
				}
			}//end loop all item
			
			if($iRow<>9){
				for($iRowRemain=$iRow; $iRowRemain<=8; $iRowRemain++){
					$html .='<tr>
							<td style="font-weight: bold; text-align: center; width: 150px;border: 0.1em solid black;"></td>
							<td style="font-weight: bold; text-align: center; width: 150px;border: 0.1em solid black;"></td>
							<td style="font-weight: bold; text-align: center; width: 170px;border: 0.1em solid black;"></td>								
							<td style="font-weight: bold; text-align: center; width: 60px;border: 0.1em solid black;"></td>								
							<td style="font-weight: bold; text-align: center; width: 40px;border: 0.1em solid black;"></td>
							<td style="font-weight: bold; text-align: center; width: 80px;border: 0.1em solid black;"></td>							
						</tr>';	
				}
			}
			
			
			
			$pdf->foot($hdr, $html);
			
			



			}
			//<!--if isset $_GET['from_date']-->
		
		 
		   

// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.
$pdf->Output($soNo.'.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
	?>