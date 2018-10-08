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
		
		$this->SetFont('THSarabun', 'B', 12, '', true);
		
		$this->setCellHeightRatio(1.0);
		
		$html='<table width="100%"  >		
		<tr>
			<td rowspan="3" ><img src="../asset/img/logo-ak_60x60.jpg" /></td>
			<td colspan="5"><span style="font-size: 120%;" >บริษัท เอเซีย กังนัม จำกัด</span></td>
			<td colspan="4"></td>
		</tr>
		<tr>
			<td colspan="5"><span style="font-size: 120%;" >ASIA KANGNAM  COMPANY LIMITED</span></td>
			<td colspan="7" ></td>
		</tr>
		<tr>
			<td colspan="5"><span style="font-size: 60%;" >69/1 ม.6 ต.ท่าข้าม อ.บางปะกง จ.ฉะเชิงเทรา 24130  โทร 0 – 3857 – 3635 แฟ็กซ์ 0 – 3857 – 3634</span><br/>
				<span style="font-size: 60%;" >69/1 Moo 6, Thakam, Bangpakong, Chachoengsao 24130 Thailand Tel: 66 – 3857 – 3635 Fax: 66 – 3857 – 3634</span>
			</td>
			<td colspan="5" ></td>
		</tr>
		<tr>
			<td style="text-align: center;"><span style="font-size: 60%;" >www.askn.com</span></td>
			<td colspan="5"></td>
			<td colspan="6" ></td>
		</tr>
		</table>
		';
		$this->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
		$this->setCellHeightRatio(1.50);
		
		$html='<table width="100%"  >	
		<tr>
			<td colspan="3" style="border: o.1em solid black; text-align: center; font-size: large;">SALES ORDER FORM (ใบสั่งขาย)</td>
			<td colspan="7" style="text-align: center; font-size: large; color: red" ></td>
		</tr>
		<tr>
			<td colspan="2" >ชื่อลูกค้า (Customer Name) : </td>
			<td colspan="4"  style="border-bottom: 0.1em solid black;">'.$hdr['custName'].'</td>
			<td colspan="2" style="font-size: 95%;" >รหัสลูกค้า (Customer Code) : </td>
			<td colspan="2"  style="border-bottom: 0.1em solid black;">'.$hdr['custCode'].'</td>		
		</tr>
		<tr>
			<td colspan="2" ><span >ที่อยู่ (Address) : </span></td>
			<td colspan="4"  style="border-bottom: 0.1em solid black;">'.$hdr['custAddr1'].'</td>				
			<td colspan="1" >&nbsp;วันที่ (Date) : </td>
			<td colspan="3" style="border-bottom: 0.1em solid black;">'.date('d M Y',strtotime( $hdr['saleDate'] )).'</td>	
		</tr>
		<tr>
			<td colspan="6"  style="border-bottom: 0.1em solid black;">'.$hdr['custAddr2'].'</td>
			<td colspan="1" >&nbsp;SO No. : </td>
			<td colspan="3"  style="border-bottom: 0.1em solid black;">'.$hdr['soNo'].($hdr['revCount']<>0?' rev.'.$hdr['revCount']:'').'</td>
		</tr>
		<tr>
			<td colspan="6" style="border-bottom: 0.1em solid black;">'.$hdr['custAddr3'].' '.$hdr['custZipcode'].'</td>
			<td colspan="1" >&nbsp;PO No. : </td>
			<td colspan="3"  style="border-bottom: 0.1em solid black;">'.$hdr['poNo'].'</td>
		</tr>
		<tr>
			<td colspan="2" ><span >ที่ส่งสินค้า (Ship to) : </span></td>
			<td colspan="4"  style="border-bottom: 0.1em solid black;">'.$hdr['shipToName'].'</td>	
			<td colspan="2" >&nbsp;PI No./ใบรับการสั่งซื้อ : </td>
			<td colspan="2"  style="border-bottom: 0.1em solid black;">'.$hdr['piNo'].'</td>
		</tr>
		<tr>
			<td colspan="6"  style="border-bottom: 0.1em solid black;">'.$hdr['shipToAddr1'].'</td>
			<td colspan="1" >&nbsp;กลุ่มสินค้า. : </td>
			<td colspan="3"  style="border-bottom: 0.1em solid black;">'.$hdr['suppTypeName'].'</td>
		</tr>
		<tr>
			<td colspan="10"  style="border-bottom: 0.1em solid black;">'.$hdr['shipToAddr2'].' '.$hdr['shipToAddr3'].' '.$hdr['shipToZipcode'].'</td>
		</tr>

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
		</table>
		';
		$this->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
		$this->setCellHeightRatio(1.40);
	}
	
	public function foot($hdr, $html){
		$html .='</tbody></table>';
		$this->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
		//$pdf->Ln(2);
		
		$html='<table width="100%"  >
		<tr>
			<td colspan="1">สินค้ามีในสต๊อก :</td>
			<td colspan="4" ><span style="text-decoration: underline;">'.$hdr['stkTypeName'].'</span></td>
		</tr>
		<tr>
			<td colspan="1">บรรจุภัณฑ์ (Package) :</td>
			<td colspan="4" ><span style="text-decoration: underline;">'.$hdr['packageTypeName'].'</span></td>
		</tr>
		<tr>
			<td colspan="2">กรณีส่งต่างผระเทศ (Export) by :</td>
			<td colspan="3" ><span style="text-decoration: underline;">'.$hdr['containerLoadName'].'</span></td>
		</tr>
		<tr>
			<td colspan="2">Shipping Mark :</td>
			<td colspan="3" >'.
			($hdr['shippingMarksFilePath']!=""?'<img src="../images/shippingMarks/'.$hdr['shippingMarksFilePath'].'" />':'<img src="../images/shippingMarks/default.jpg" />').
			'</td>
		</tr>
		<tr>
			<td colspan="1">ราคา (Price) :</td>
			<td colspan="4" ><span style="text-decoration: underline;">'.$hdr['priceTypeName'].'</span></td>
		</tr>
		<tr>
			<td colspan="1">ผู้เสนอขาย (Sales) : </td>
			<td colspan="4" ><span style="text-decoration: underline;">'.$hdr['smName'].'</span></td>
		</tr>
		<tr>
			<td colspan="1">Remark :</td>
			<td colspan="4" ><span style="text-decoration: underline;">'.$hdr['remark'].'</span></td>
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
			<td colspan="3"  style="border-top: 0.1em solid black; border-left: 0.1em solid black;" >
				&nbsp;เครดิต (Credit) '.'<span style="text-decoration: underline; font-size: 120%; font-weight: bold;"> '.$hdr['payTypeCreditDays'].' </span> วัน (Days)
			</td>
			<td colspan="3"  style="border-top: 0.1em solid black; border-left: 0.1em solid black; border-right: 0.1em solid black;" >&nbsp;สถานที่ส่งสินค้า (Place to Delivery)
			</td>
			<td colspan="4"  style="border-top: 0.1em solid black; border-left: 0.1em solid black; border-right: 0.1em solid black;" >
				&nbsp;จัดทำโดย (Issue By) : <span style="text-decoration: underline;">'.$hdr['createByName'].'</span>
			</td>
		</tr>
		<tr>
			<td colspan="3" style="border-left: 0.1em solid black;">
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="text-decoration: underline;">'.$hdr['creditTypename'].'</span>
			</td>
			<td colspan="3" style="border-left: 0.1em solid black; border-right: 0.1em solid black;">
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="text-decoration: underline;">'.$hdr['deliveryTypeName'].'</span>
			</td>
			<td colspan="4" style="border-left: 0.1em solid black; border-right: 0.1em solid black;">
				&nbsp;วันที่ (Date) : <span style="text-decoration: underline;">'.date('d M Y',strtotime( $hdr['createTime'] )).'</span>
			</td>
		</tr>
		<tr>
			<td colspan="3" style="border-left: 0.1em solid black;">
				
			</td>

			<td colspan="3" style="border-left: 0.1em solid black; border-right: 0.1em solid black;">
			
			</td>
			<td colspan="4" style="border-left: 0.1em solid black; border-right: 0.1em solid black;">
				&nbsp;ตรวจสอบโดย (ผู้ขาย) : <span style="text-decoration: underline;">
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
			</td>
		</tr>
		<tr>
			<td colspan="3" style="border-bottom: 0.1em solid black; border-left: 0.1em solid black;">

			</td>
			<td colspan="3" style="border-bottom: 0.1em solid black; border-left: 0.1em solid black;">

			</td>
			<td colspan="4" style="border-bottom: 0.1em solid black; border-left: 0.1em solid black; border-right: 0.1em solid black;">
				&nbsp;ผู้อนุมัติ (Approved by) : <span style="text-decoration: underline;">
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
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
			SELECT a.`soNo`, a.`poNo`, a.`piNo`, a.`saleDate`, a.`custId`, a.`shipToId`, a.`smId`, a.`revCount`, a.`deliveryDate`, a.`suppTypeId`, a.`stkTypeId`, a.`packageTypeId`, a.`priceTypeId`, a.`deliveryTypeId`, a.`shippingMarksId`, a.`deliveryRem`, a.`containerLoadId`, a.`creditTypeId`, a.`remark`, a.`payTypeCreditDays`, a.`isClose`, a.`statusCode`, a.`createTime`, a.`createById`, a.`updateTime`, a.`updateById`, a.`confirmTime`, a.`confirmById`, a.`approveTime`, a.`approveById`
			, b.code as custCode, b.name as custName, b.addr1 as custAddr1, b.addr2 as custAddr2, b.addr3 as custAddr3, b.zipcode as custZipcode, b.tel as custTel, b.fax as custFax
			, st.code as shipToCode, st.name as shipToName, st.addr1 as shipToAddr1, st.addr2 as shipToAddr2, st.addr3 as shipToAddr3, st.zipcode as shipToZipcode, st.tel as shipToTel, st.fax as shipToFax
			, c.code as smCode, c.name as smName, c.surname as smSurname
			, sst.name as suppTypeName, stkt.name as stkTypeName, sst.name as stockTypeName
			, spt.name as packageTypeName, prit.name as priceTypeName, sdt.name as deliveryTypeName
			, sct.name as creditTypename, clt.name as containerLoadName 
			, spm.name as shippingMarksName, IFNULL(spm.filePath,'') as shippingMarksFilePath
			
			, d.userFullname as createByName
			, a.confirmTime, cu.userFullname as confirmByName
			, a.approveTime, au.userFullname as approveByName
			FROM `sale_header` a
			left join customer b on b.id=a.custId 
			left join shipto st on st.id=a.shipToId  
			left join salesman c on c.id=a.smId 
			left join sale_supp_type sst ON sst.id=a.suppTypeId
			left join sale_stk_type stkt ON stkt.id=a.stkTypeId 
			left join sale_package_type spt ON spt.id=a.packageTypeId
			left join sale_price_type prit ON prit.id=a.priceTypeId		
			left join sale_delivery_type sdt ON sdt.id=a.deliveryTypeId
			left join sale_credit_type sct ON sct.id=a.creditTypeId	
			left join sale_container_load_type clt ON clt.id=a.containerLoadId 	
		
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
			$soNo = $hdr['soNo'];
	   		
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
			SELECT a.`id`, a.`prodId`, a.`qty`, a.`rollLengthId`, a.`remark`, a.deliveryDate, a.`soNo`
			, b.code as prodCode, b.name as prodName, b.uomCode as prodUomCode, b.description 
			, (SELECT IFNULL(SUM(dd.qty),0) FROM delivery_detail dd 									
					INNER JOIN delivery_header dh on dh.doNo=dd.doNo 
					WHERE dh.soNo=a.soNo AND dd.prodCode=a.prodId ) as sentQty 
			, rl.name as rollLengthName 
			FROM `sale_detail` a
			LEFT JOIN product b on a.prodId=b.id
			LEFT JOIN product_roll_length rl ON rl.id=a.rollLengthId 
			WHERE 1
			AND a.`soNo`=:soNo 
			ORDER BY a.id, a.createTime
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
										<th style="font-weight: bold; text-align: center; width: 150px; border: 0.1em solid black;">Product Series</th>
										<th style="font-weight: bold; text-align: center; width: 150px; border: 0.1em solid black;">Product Code</th>
										<th style="font-weight: bold; text-align: center; width: 170px; border: 0.1em solid black;">Description</th>								
										<th style="font-weight: bold; text-align: center; width: 60px; border: 0.1em solid black;">Quantity</th>								
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