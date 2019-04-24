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
        $this->Cell(0, 5, 'Product Delivery Summary Report', 0, false, 'C', 0, '', 0, false, 'M', 'M');
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

date_default_timezone_set("Asia/Bangkok");

// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Prawit Khamnet');
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
		if( isset($_GET['dateFrom']) AND isset($_GET['dateTo']) ){
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
			$dateFrom = str_replace('/', '-', $dateFrom);
			$dateTo = str_replace('/', '-', $dateTo);
			$dateFromYmd=$dateToYmd="";
			if($dateFrom<>""){ $dateFromYmd = date('Y-m-d', strtotime($dateFrom));	}
			if($dateTo<>""){ $dateToYmd =  date('Y-m-d', strtotime($dateTo));	}

			$toCode = (isset($_GET['toCode'])?$_GET['toCode']:'');
			$prodCode = (isset($_GET['prodCode'])?$_GET['prodCode']:'');
			$prodId = (isset($_GET['prodId']) ?$_GET['prodId']:'');	
			if($prodCode=="") $prodId="";


			//SQL Header

			$toCodeName="All";
			if($toCode<>""){
				$sql = "SELECT * FROM customer WHERE id=:id ";
				$stmt = $pdo->prepare($sql);
				if($toCode<>""){ $stmt->bindParam(':id', $toCode );	}
				if($stmt->execute()){
					$toCodeName=$stmt->fetch()['name'];
				}
			}

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
		
	
			$sql = "SELECT prd.code , prd.description, itm.grade
			, sh.custId, cust.name as custName 
			, count(itm.prodItemId) as qty, sum(itm.qty) as totalQty 
			FROM `delivery_header` hdr
			INNER JOIN delivery_detail dtl on dtl.doNo=hdr.doNo
            INNER JOIN sale_header sh ON sh.soNo=hdr.soNo 
            INNER JOIN customer cust ON cust.id=sh.custId 
			INNER JOIN product_item itm on itm.prodItemId=dtl.prodItemId  
			INNER JOIN product prd on prd.id=itm.prodCodeId  
			WHERE 1 ";

			if($dateFromYmd<>""){ $sql .= " AND hdr.deliveryDate>=:dateFromYmd ";	}
			if($dateToYmd<>""){ $sql .= " AND hdr.deliveryDate<=:dateToYmd ";	}	
			if($toCode<>""){ $sql .= " AND sh.custId=:toCode ";	}				
			if($prodCode<>""){ $sql .= " AND prd.code like :prodCode ";	}	
			if($prodId<>""){ $sql .= " AND prd.id=:prodId ";	}	
			$sql .= "AND hdr.statusCode='P' ";
			$sql.="GROUP BY prd.code, prd.description, itm.grade, sh.custId, cust.name ";
			$sql.="ORDER BY prd.code, itm.grade, cust.name  ";

			$stmt = $pdo->prepare($sql);
			if($dateFromYmd<>""){ $stmt->bindParam(':dateFromYmd', $dateFromYmd );	}
			if($dateToYmd<>""){ $stmt->bindParam(':dateToYmd', $dateToYmd );	}
			if($toCode<>""){ $stmt->bindParam(':toCode', $toCode );	}
			if($prodCode<>""){ $tmp='%'.$prodCode.'%'; $stmt->bindParam(':prodCode', $tmp );	}
			if($prodId<>""){ $stmt->bindParam(':prodId', $prodId );	}
			$stmt->execute();	

							
			$html ='
			<table class="table table-striped no-margin" >
				 <thead>									
				  <tr>
				  	<th  colspan="4"><span style="font-weight: bold;">Sending Date :</span> '.date('d M Y',strtotime( $dateFrom )).' to '.date('d M Y',strtotime( $dateTo )).'</th>
				  	<th  colspan="4"><span style="font-weight: bold;">Product Code :</span> '.$prodCodeName.'</th>
				</tr>

				<tr>
				  	<th  colspan="4"><span style="font-weight: bold;">To :</span> '.$toCodeName.'</th>
				</tr>
				  <tr>
						<th style="font-weight: bold; text-align: center; width: 30px;" border="1">No.</th>
						<th style="font-weight: bold; text-align: center; width: 100px;" border="1">Product Code</th>
						<th style="font-weight: bold; text-align: center; width: 200px;" border="1">Description</th>
						<th style="font-weight: bold; text-align: center; width: 40px;" border="1">Grade</th>
						<th style="font-weight: bold; text-align: center; width: 50px;" border="1">Box/Roll</th>
						<th style="font-weight: bold; text-align: center; width: 70px;" border="1">Piece/Length<br/>/KG</th>
						<th style="font-weight: bold; text-align: center; width: 100px;" border="1">Customer Name</th>
					</tr>
				  </thead>
				  <tbody>
			'; 	
			$row_no = 1; $rowPerPage=0; $sumQty=$sumTotalQty=0; while ($row = $stmt->fetch()) {
				
				$gradeName = '<b style="color: red;">N/A</b>'; 
					switch($row['grade']){
						case 0 : $gradeName = 'A'; break;
						case 1 : $statusName = '<b style="color: red;">B</b>';  break;
						case 2 : $statusName = '<b style="color: red;">N</b>'; break;
						default : 
					} 
				
			$html .='<tr>
				<td style="border: 0.1em solid black; text-align: center; width: 30px;">'.$row_no.'</td>
				<td style="border: 0.1em solid black; padding: 10px; width: 100px;"> '.$row['code'].'</td>
				<td style="border: 0.1em solid black; padding: 10px; width: 200px;"> '.$row['description'].'</td>
				<td style="border: 0.1em solid black; text-align: center; width: 40px;">'.$gradeName.'</td>
				<td style="border: 0.1em solid black; text-align: right; width: 50px;">'.$row['qty'].'&nbsp;&nbsp;</td>
				<td style="border: 0.1em solid black; text-align: right; width: 70px;">'.number_format($row['totalQty'],2,'.',',').'&nbsp;&nbsp;</td>
				<td style="border: 0.1em solid black; padding: 10px; width: 100px;"> '.$row['custName'].'</td>
			</tr>';			
										
			$sumQty+=$row['qty'] ; $sumTotalQty+=$row['totalQty'];					
			$row_no +=1; $rowPerPage+=1; }
			//<!--end while div-->	
			
			$html .='<tr>
				<td style="font-weight: bold; border: 0.1em solid black; text-align: center; width: 30px;"></td>
				<td style="font-weight: bold; border: 0.1em solid black; text-align: center; padding: 10px; width: 100px;">Total '.number_format($row_no-1,0,'.',',').' items</td>
				<td style="font-weight: bold; border: 0.1em solid black; text-align: center; padding: 10px; width: 200px;"></td>
				<td style="font-weight: bold; border: 0.1em solid black; text-align: center; width: 40px;"></td>
				<td style="font-weight: bold; border: 0.1em solid black; text-align: right; width: 50px;">'.number_format($sumQty,2,'.',',').'&nbsp;&nbsp;</td>
				<td style="font-weight: bold; border: 0.1em solid black; text-align: right; width: 70px;">'.number_format($sumTotalQty,2,'.',',').'&nbsp;&nbsp;</td>
				<td style="font-weight: bold; border: 0.1em solid black; text-align: center; width: 100px;"></td>
			</tr>';
			
			
				
				//Footer for write 
				$html .='</tbody></table>';					
				
				$pdf->AddPage('P');
										
				$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
					}
					//<!--if isset $_GET['from_date']-->
		
		 
		   

// ---------------------------------------------------------

$pdf->SetTitle('Product Sending Detail Report');
// Close and output PDF document
// This method has several options, check the source code documentation for more information.
$pdf->Output('Product Sending Detail Report'.'.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
	?>