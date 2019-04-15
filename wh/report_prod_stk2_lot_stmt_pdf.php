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
        $this->Cell(0, 5, 'Stock Movement Report', 0, false, 'C', 0, '', 0, false, 'M', 'M');
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
    // Page footer
}

date_default_timezone_set("Asia/Bangkok");

// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('AK');
//$pdf->SetSubject('TCPDF Tutorial');
//$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

//remove header
//$pdf->setPrintHeader(false);

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins (left, top, right)
//$pdf->SetMargins(24, 26, 30);	//หน้า ๓ บนถึงตูดเลขหน้า ๒ ตูดเลขหน้าถึงตูดบรรทัดแรก ๑.๕
$pdf->SetMargins(10, 20, 5);	//หน้า ๓ บนถึงตูดเลขหน้า ๒ ตูดเลขหน้าถึงตูดบรรทัดแรก ๑.๕
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
		if( isset($_GET['dateFrom']) ){
			$rootPage="report_sending2";

			$dateFrom=$dateTo="";
			$dateFromYmd=$dateToYmd="";
			if(isset($_GET['dateFrom'])){ 
				$dateFrom=$_GET['dateFrom']; 
				$dateArr = explode('/', $dateFrom); 
			    $dateY = (int)$dateArr[2];
			    $dateM = $dateArr[1];
			    $dateD = $dateArr[0];
			    $dateFromYmd = $dateY . '-' . $dateM . '-' . $dateD;
			}else{
				$dateFrom=date('d/m/Y');
				$dateFromYmd=date('Y-m-d');
			}
			if(isset($_GET['dateTo'])){
				$dateTo=$_GET['dateTo'];
				$dateArr = explode('/', $dateTo);
			    $dateY = (int)$dateArr[2];
			    $dateM = $dateArr[1];
			    $dateD = $dateArr[0];
			    $dateToYmd = $dateY . '-' . $dateM . '-' . $dateD;
			}else{
				$dateTo=date('d/m/Y');
				$dateToYmd=date('Y-m-d');
			}

			$search_word = (isset($_GET['search_word'])?trim($_GET['search_word']):'');
			$sloc = (isset($_GET['sloc'])?$_GET['sloc']:'8');
			$catCode = (isset($_GET['catCode'])?$_GET['catCode']:'');
			//$prodId = (isset($_GET['prodId']) ?$_GET['prodId']:'');
			$prodCode = (isset($_GET['prodCode'])?trim($_GET['prodCode']):'');
			//if($prodCode=="") $prodId="";
	

			

			//SQL Header
			$slocName="All";
			if($sloc<>""){
				$sql = "SELECT `code`, `name` FROM sloc WHERE code=:code ";
				$stmt = $pdo->prepare($sql);
				if($sloc<>""){ $stmt->bindParam(':code', $sloc );	}
				if($stmt->execute()){
					$slocName=$stmt->fetch()['name'];
				}
			}
			/*
			$prodCodeName="All";
			if($prodCode!=""){
				$prodCodeName=$prodCode;
			}

			if($prodCode<>""){
				$sql = "SELECT code FROM product WHERE code like :prodCode ";
				$stmt = $pdo->prepare($sql);
				if($prodCode<>""){ $tmp='%'.$prodCode.'%'; $stmt->bindParam(':prodCode', $tmp );	}
				if($stmt->execute()){
					if($stmt->rowCount()==1){						
						$prodCodeName=$stmt->fetch()['code'];
					}else{						
						$prodCodeName=$prodCode;
					}
				}
			}
			*/
		
	
			$pdo->beginTransaction();

          	$sql = "
          	CREATE TEMPORARY TABLE tmpStock (
          		`prodId` int(11) NOT NULL,
				  `prodCode` varchar(100) NOT NULL,
				  `sloc` varchar(10) NOT NULL,
				  `openAcc` decimal(10,2) NOT NULL,
				  `onway` decimal(10,2) NOT NULL,
				  `receive` decimal(10,2) NOT NULL,
				  `sent` decimal(10,2) NOT NULL,
				  `return` decimal(10,2) NOT NULL,
				  `delivery` decimal(10,2) NOT NULL,
				  `balance` decimal(10,2) NOT NULL,
				  `balanceReCheck` decimal(10,2) NOT NULL,
				  `book` decimal(10,2) NOT NULL,
		      	PRIMARY KEY (`prodId`,`sloc`)
		    )";
          	$stmt = $pdo->prepare($sql);		
			$stmt->execute();

			$sql = "
	          INSERT INTO tmpStock (prodId, prodCode, sloc)
	          SELECT prd.id, prd.code, sl.code 
	          FROM product prd ";
	        $sql .= "
	          CROSS JOIN sloc sl ON 1=1 ";
	        if($sloc<>""){ $sql .= " AND sl.code='$sloc' ";	}else{ $sql .= " AND sl.code IN ('8','E') "; }  
	        $sql .= "WHERE 1=1 ";
	        if($prodCode<>""){ $sql .= "AND prd.code like '%".$prodCode."%' ";	}
	        if($catCode<>""){ $sql .= " AND prd.catCode='$catCode' ";	}

          	$stmt = $pdo->prepare($sql);		
			$stmt->execute();		


			//Last Prev Closing Date. = LPCD
			$sql = "SELECT th.id, th.closingDate FROM stk_closing th WHERE th.statusCode='A' AND DATE(th.closingDate)<='$dateFromYmd' ORDER BY th.closingDate DESC LIMIT 1
          	";
          	$stmt = $pdo->prepare($sql);		
			$stmt->execute();
			$row = $stmt->fetch();
			$lpcDate = $row['closingDate'];
			$lpcdId = $row['id'];

			//Open
			$sql = "UPDATE tmpStock hdr 
	         ,(SELECT td.prodId, td.sloc, td.balance as sumQty FROM stk_closing_detail td 
	          				WHERE td.hdrId=:lpcdId 
	          				) as tmp 
	          SET hdr.openAcc=tmp.sumQty 
	          WHERE hdr.prodId=tmp.prodId AND hdr.sloc=tmp.sloc 
          	";
          	$stmt = $pdo->prepare($sql);	
			$stmt->bindParam(':lpcdId', $lpcdId);	
			$stmt->execute();

			//Onway
			$sql = "UPDATE tmpStock hdr
	         ,(SELECT itm.prodCodeId, sh.toCode, SUM(itm.qty) as sumQty FROM product_item itm 
	          				INNER JOIN send_detail sd ON sd.prodItemId=itm.prodItemId  
	         				INNER JOIN send sh ON sh.sdNo=sd.sdNo AND sh.statusCode='P' AND sh.rcNo IS NULL AND DATE(sh.sendDate) <= '$dateFromYmd'
	          				GROUP BY itm.prodCodeId, sh.toCode
	          				) as tmp 
	          SET hdr.onway=tmp.sumQty 
	          WHERE hdr.prodId=tmp.prodCodeId AND hdr.sloc=tmp.toCode 
          	";
          	$stmt = $pdo->prepare($sql);		
			$stmt->execute();
			
			//Receive
			$sql = "UPDATE tmpStock hdr
	         ,(SELECT itm.prodCodeId, th.toCode as fromCode, SUM(itm.qty) as sumQty FROM product_item itm 
	          				INNER JOIN receive_detail td ON td.prodItemId=itm.prodItemId  
	         				INNER JOIN receive th ON th.rcNo=td.rcNo AND th.statusCode='P' 
	         					AND DATE(th.receiveDate) > '$lpcDate' AND DATE(th.receiveDate) <= '$dateFromYmd'
	          				GROUP BY itm.prodCodeId, th.toCode
	          				) as tmp 
	          SET hdr.receive=tmp.sumQty 
	          WHERE hdr.prodId=tmp.prodCodeId AND hdr.sloc=tmp.fromCode 
          	";
          	$stmt = $pdo->prepare($sql);		
			$stmt->execute();

			//Sent
			$sql = "UPDATE tmpStock hdr
	         ,(SELECT itm.prodCodeId, th.fromCode, SUM(itm.qty) as sumQty 
	         				FROM product_item itm 
	          				INNER JOIN send_detail td ON td.prodItemId=itm.prodItemId  
	         				INNER JOIN send th ON th.sdNo=td.sdNo AND th.statusCode='P' 
	         					AND DATE(th.sendDate) > '$lpcDate' AND DATE(th.sendDate) <= '$dateFromYmd'
	          				GROUP BY itm.prodCodeId, th.fromCode
	          				) as tmp 
	          SET hdr.sent=tmp.sumQty 
	          WHERE hdr.prodId=tmp.prodCodeId AND hdr.sloc=tmp.fromCode 
          	";
          	$stmt = $pdo->prepare($sql);		
			$stmt->execute();

			//return
			$sql = "UPDATE tmpStock hdr 
	         ,(SELECT itm.prodCodeId, th.fromCode, SUM(itm.qty) as sumQty FROM product_item itm 
	          				INNER JOIN rt_detail td ON td.prodItemId=itm.prodItemId  
	         				INNER JOIN rt th ON th.rtNo=td.rtNo AND th.statusCode='P' AND DATE(th.returnDate) > '$lpcDate' AND DATE(th.returnDate) <= '$dateFromYmd' 
	          				GROUP BY itm.prodCodeId, th.fromCode
	          				) as tmp 
	          SET hdr.return=tmp.sumQty 
	          WHERE hdr.prodId=tmp.prodCodeId AND hdr.sloc=tmp.fromCode 
          	";
          	$stmt = $pdo->prepare($sql);		
			$stmt->execute();

			//delivery
			$sql = "UPDATE tmpStock hdr
	         ,(SELECT itm.prodCodeId, CASE WHEN cust.locationCode = 'L' THEN '8' ELSE 'E' END as fromCode, SUM(itm.qty) as sumQty FROM product_item itm 
	          				INNER JOIN delivery_detail td ON td.prodItemId=itm.prodItemId  
	         				INNER JOIN delivery_header th ON th.doNo=td.doNo AND th.statusCode='P' 
	         					AND DATE(th.deliveryDate) > '$lpcDate' AND DATE(th.deliveryDate) <= '$dateFromYmd'
	         				INNER JOIN sale_header shd ON shd.soNo=th.soNo 
	         				INNER JOIN customer cust ON cust.id=shd.custId 
	          				GROUP BY itm.prodCodeId, cust.locationCode 
	          				) as tmp 
	          SET hdr.delivery=tmp.sumQty 
	          WHERE hdr.prodId=tmp.prodCodeId AND hdr.sloc=tmp.fromCode 
          	";
          	$stmt = $pdo->prepare($sql);		
			$stmt->execute();
			
			//balance
			$sql = "UPDATE tmpStock 
			SET `balance`=`openAcc`+`receive`-`sent`-`return`-`delivery`
          	";
          	$stmt = $pdo->prepare($sql);		
			$stmt->execute();
	
			//delete
			$sql = "DELETE FROM tmpStock 
			WHERE `openAcc`=0 AND `onway`=0
			AND `receive`=0 AND `sent`=0 AND `return`=0 AND `delivery`=0 
			AND `balance`=0 AND `book`=0 
          	";
          	$stmt = $pdo->prepare($sql);		
			$stmt->execute();

			//We've got this far without an exception, so commit the changes.
			$pdo->commit();	

			$sql = "SELECT  
			sb.`prodId`, sb.`prodCode`, sb.`sloc`, sb.`openAcc`, sb.`onway`, sb.`receive` ,sb.`sent`,sb.`return` ,sb.`delivery` ,sb.`balance` ,sb.`balanceReCheck` ,sb.`book` 	
			, sl.name as slocName 
			FROM tmpStock sb 
				INNER JOIN sloc sl ON sl.code=sb.sloc ";
				$sql.="ORDER BY sb.prodCode, sb.sloc  ";
				//$sql.="LIMIT $start, $rows ";
				$stmt = $pdo->prepare($sql);		
				$stmt->execute();
			$countTotal = $stmt->rowCount();	

						
			$html='';		
			$row_no = 1; $rowPerPage=0; $sumQty=$sumNW=$sumGW=0; while ($row = $stmt->fetch()) {

				$isNotEqual=false;
       			$bgColor="";
       			if ( $row['balance']<0 ){
       				$isNotEqual=true;
       				$bgColor="bg-danger";
       			}


				if($rowPerPage==40){
					//Footer for write 
					$html .='</tbody></table>';					
					
					$pdf->AddPage('P');
											
					$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
					$html='';
					$rowPerPage=0;
				}
				if($html==''){
					$html ='
					<table class="table table-striped table-layout: fixed;"   >
						 <thead>									
						  <tr>
						  	<th  colspan="3"><span style="font-weight: bold;">Stock Date :</span> '.date('d M Y',strtotime( $dateFromYmd )).' </th>
						  <th  colspan="3"><span style="font-weight: bold;">Location :</span> '.$slocName.'</th>
						  <th  colspan="4"><span style="font-weight: bold;">Product Code :</span> '.$prodCode.'</th>
						</tr>
						  </thead>					
					</table>

					<table class="table table-striped table-layout: fixed;" border="1"  >
						  <tr>
							<th style="width: 30px; text-align: center;">No.</th>
		                    <th style="width: 180px; text-align: center;">Product Code</th>
							<th style="width: 50px; text-align: center;">Lot.</th>
							<th style="width: 30px; text-align: center;">Loc.</th>
							<th style="width: 50px; text-align: center; ">Onway</th>
							<th style="width: 50px; text-align: center;">Open</th>
							<th style="width: 50px; text-align: center;">Recv.</th>
							<th style="width: 50px; text-align: center;">Sent</th>
							<th style="width: 50px; text-align: center;">Return</th>
							<th style="width: 50px; text-align: center;">Delivery</th>	
							<th style="width: 80px; text-align: center; font-weight: bold; ">Balance</th>
		                  </tr>
						  <tbody>
					'; 
				}						
			$html .='<tr class="'.$bgColor.'">
				<td style="width: 30px; text-align: center; font-weight: bold;">'.$row_no.'</td>
                    <td style="width: 180px; font-weight: bold;"> '.$row['prodCode'].($isNotEqual?' *** ':'').'</td>
					<td style="width: 50px; text-align: center; font-weight: bold;"></td>	
					<td style="width: 30px; text-align: center; font-weight: bold;">'.$row['sloc'].'</td>	
					<td style="width: 50px; text-align: right; font-weight: bold;">
						'. number_format($row['onway'],2,'.',',') .'			
					</td>
					<td style="width: 50px;text-align: right; font-size: small; font-weight: bold;">'. number_format($row['openAcc'],2,'.',',') .'</td>
					<td style="width: 50px; text-align: right; font-size: small; font-weight: bold;">'. number_format($row['receive'],2,'.',',') .'</td>
					<td style="width: 50px;text-align: right; font-size: small; font-weight: bold;">'. number_format($row['sent'],2,'.',',') .'</td>
					<td style="width: 50px;text-align: right; font-size: small; font-weight: bold;">'. number_format($row['return'],2,'.',',') .'</td>
					<td style="width: 50px;text-align: right; font-size: small; font-weight: bold;">'. number_format($row['delivery'],2,'.',',') .'</td>	
					<td style="width: 80px; text-align: right; font-weight: bold;">'.number_format($row['balance'],2,'.',',').'				
					</td>
			</tr>';			
					



				//Receive Doc No
				$sql = "SELECT * 
					FROM (	SELECT 'RC' as docType, th.rcNo as docNo, th.receiveDate as issueDate, IF(th.`toCode`='E',sh.sendDate,itm.`issueDate`) as lotDate, SUM(itm.qty) as sumQty FROM product_item itm 
	          				INNER JOIN receive_detail td ON td.prodItemId=itm.prodItemId 
	         				INNER JOIN receive th ON th.rcNo=td.rcNo AND th.statusCode='P' AND th.toCode=:toCodeRc 
	         					AND th.receiveDate > '$lpcDate' AND DATE(th.receiveDate) <= '$dateFromYmd'
	         				INNER JOIN send sh ON th.rcNo=sh.rcNo 
	         				WHERE itm.prodCodeId=:prodCodeIdRc 
	         				GROUP BY  1, 2, 3, 4 
	          			UNION 
	          			SELECT 'SD' as docType, th.sdNo as docNo, th.sendDate as issueDate, NULL as lotDate, SUM(itm.qty) as sumQty FROM product_item itm 
	          				INNER JOIN send_detail td ON td.prodItemId=itm.prodItemId 
	         				INNER JOIN send th ON th.sdNo=td.sdNo AND th.statusCode='P' AND th.fromCode=:fromCodeSd 
	         					AND th.sendDate > '$lpcDate' AND DATE(th.sendDate) <= '$dateFromYmd'
	         				WHERE itm.prodCodeId=:prodCodeIdSd
	         				GROUP BY  1, 2, 3, 4 
	          			UNION  
	          			SELECT 'RT' as docType, th.rtNo as docNo, th.returnDate as issueDate, NULL as lotDate, SUM(itm.qty) as sumQty FROM product_item itm 
	          				INNER JOIN rt_detail td ON td.prodItemId=itm.prodItemId 
	         				INNER JOIN rt th ON th.rtNo=td.rtNo AND th.statusCode='P' AND th.fromCode=:fromCodeRt 
	         					AND th.returnDate > '$lpcDate' AND DATE(th.returnDate) <= '$dateFromYmd'
	         				WHERE itm.prodCodeId=:prodCodeIdRt 
	         				GROUP BY 1, 2, 3, 4
	          			UNION 
	          			SELECT 'DO' as docType, th.doNo as docNo, th.deliveryDate as issueDate, NULL as lotDate, SUM(itm.qty) as sumQty FROM product_item itm 
	          				INNER JOIN delivery_detail td ON td.prodItemId=itm.prodItemId 
	         				INNER JOIN delivery_header th ON th.doNo=td.doNo AND th.statusCode='P' 
	         					AND th.deliveryDate > '$lpcDate' AND DATE(th.deliveryDate) <= '$dateFromYmd'
	         				INNER JOIN sale_header shd ON shd.soNo=th.soNo 
	         				INNER JOIN customer cust ON cust.id=shd.custId AND CASE WHEN cust.locationCode = 'L' THEN '8' ELSE 'E' END =:fromCodeDo 
	         				WHERE itm.prodCodeId=:prodCodeIdDo
	         				GROUP BY 1, 2, 3, 4
	          			) as tmp ";
	          		$sql.="WHERE tmp.sumQty > 0 ";
					$sql.="ORDER BY tmp.issueDate, FIELD(tmp.docType,'RC','SD','RT', 'DO') ";
					//$sql.="LIMIT $start, $rows ";
					$stmt3 = $pdo->prepare($sql);	
					$stmt3->bindParam(':toCodeRc', $row['sloc']);	
					$stmt3->bindParam(':prodCodeIdRc', $row['prodId']);	
					$stmt3->bindParam(':fromCodeSd', $row['sloc']);	
					$stmt3->bindParam(':prodCodeIdSd', $row['prodId']);	
					$stmt3->bindParam(':fromCodeRt', $row['sloc']);	
					$stmt3->bindParam(':prodCodeIdRt', $row['prodId']);	
					$stmt3->bindParam(':fromCodeDo', $row['sloc']);	
					$stmt3->bindParam(':prodCodeIdDo', $row['prodId']);	
					$stmt3->execute();
					while ($row3 = $stmt3->fetch()) {
						if($rowPerPage==40){
							//Footer for write 
							$html .='</tbody></table>';					
							
							$pdf->AddPage('P');
													
							$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
							$html='';
							$rowPerPage=0;
						}
						if($html==''){
							$html ='
					<table class="table table-striped table-layout: fixed;" >
						 <thead>									
						  <tr>
						  	<th  colspan="3"><span style="font-weight: bold;">Stock Date :</span> '.date('d M Y',strtotime( $dateFromYmd )).' </th>
						  <th  colspan="3"><span style="font-weight: bold;">Location :</span> '.$slocName.'</th>
						  <th  colspan="4"><span style="font-weight: bold;">Product Code :</span> '.$prodCode.'</th>
						</tr>
						  </thead>					
					</table>

					<table class="table table-striped table-layout: fixed;" border="1"  >
						  <tr>
							<th style="width: 30px; text-align: center;">No.</th>
		                    <th style="width: 180px; text-align: center;">Product Code</th>
							<th style="width: 50px; text-align: center;">Lot.</th>
							<th style="width: 30px; text-align: center;">Loc.</th>
							<th style="width: 50px; text-align: center; ">Onway</th>
							<th style="width: 50px; text-align: center;">Open</th>
							<th style="width: 50px; text-align: center;">Recv.</th>
							<th style="width: 50px; text-align: center;">Sent</th>
							<th style="width: 50px; text-align: center;">Return</th>
							<th style="width: 50px; text-align: center;">Delivery</th>	
							<th style="width: 80px; text-align: center; font-weight: bold; ">Balance</th>
		                  </tr>
						  <tbody>
					'; 
						}
						$html .='<tr>
						<td style="width: 30px; text-align: center;"></td>
	                    <td style="width: 180px;"> '.$row3['issueDate'].' / '.$row3['docNo'].'</td>
						<td style="width: 50px; text-align: center;">'.($row3['docType']=='RC'? date('d.m.y',strtotime( $row3['lotDate'] )) : '' ).'</td>	
						<td style="width: 30px; text-align: center;"></td>	
						<td style="width: 50px; text-align: center;"></td>	
						<td style="width: 50px; text-align: center;">'.($row3['docType']=='RC'? number_format($row3['sumQty'],2,'.',','):'') .'</td>	
						<td style="width: 50px; text-align: center;">'.($row3['docType']=='SD'? number_format($row3['sumQty'],2,'.',','):'').'</td>	
						<td style="width: 50px; text-align: center;">'.($row3['docType']=='RT'? number_format($row3['sumQty'],2,'.',','):'').'</td>
						<td style="width: 50px; text-align: center;">'.($row3['docType']=='DO'? number_format($row3['sumQty'],2,'.',','):'').'</td>		
						<td style="width: 50px; text-align: right;"></td>
						<td style="width: 80px; text-align: center; font-weight: bold;"></td>	
					</tr>';	
					$rowPerPage+=1; }//while 2 


			//$sumRecv+=$row['receive'] ; $sumSent+=$row['sent']; $sumReturn+=$row['GW'] ;								
			$row_no +=1;  
			$rowPerPage+=1;  }
			//<!--end while div-->	

	

					
				
				//Footer for write 
				$html .='</tbody></table>';					
				
				$pdf->AddPage('P');
										
				$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
				}
				//<!--if isset $_GET['from_date']-->
		
		 
		   

// ---------------------------------------------------------

$pdf->SetTitle('Stock Movement Report');
// Close and output PDF document
// This method has several options, check the source code documentation for more information.
$pdf->Output('Stock Movement Report'.'.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
	?>