<?php
include 'inc_helper.php';  
include 'session.php';	
	
try{
	$poNo = $_POST['poNo'];
    $orderDate = $_POST['orderDate'];
    $smCode = $_POST['smCode'];
    $custCode = $_POST['custCode'];
	$deliveryDate = $_POST['deliveryDate'];
	$deliveryRem = $_POST['deliveryRem'];
	$remark = $_POST['remark'];
	$prodGFC = (isset($_POST['prodGFC'])? 1 : 0 );
	$prodGFM = (isset($_POST['prodGFM'])? 1 : 0 );
	$prodGFT = (isset($_POST['prodGFT'])? 1 : 0 );
	$prodSC = (isset($_POST['prodSC'])? 1 : 0 );
	$prodCFC = (isset($_POST['prodCFC'])? 1 : 0 );
	$prodEGWM = (isset($_POST['prodEGWM'])? 1 : 0 );
	$prodGT = (isset($_POST['prodGT'])? 1 : 0 );
	$prodCSM = (isset($_POST['prodCSM'])? 1 : 0 );
	$prodWR = (isset($_POST['prodWR'])? 1 : 0 );
	$suppTypeFact = (isset($_POST['suppTypeFact'])? 1 : 0 );
	$suppTypeImp = (isset($_POST['suppTypeImp'])? 1 : 0 );
	$prodTypeOld = (isset($_POST['prodTypeOld'])? 1 : 0 );
	$prodTypeNew = (isset($_POST['prodTypeNew'])? 1 : 0 );
	$custTypeOld = (isset($_POST['custTypeOld'])? 1 : 0 );
	$custTypeNew = (isset($_POST['custTypeNew'])? 1 : 0 );
	$prodStkInStk = (isset($_POST['prodStkInStk'])? 1 : 0 );
	$prodStkOrder = (isset($_POST['prodStkOrder'])? 1 : 0 );
	$prodStkOther = (isset($_POST['prodStkOther'])? 1 : 0 );
	$prodStkRem = $_POST['prodStkRem'];
	$packTypeAk = (isset($_POST['packTypeAk'])? 1 : 0 );
	$packTypeNone = (isset($_POST['packTypeNone'])? 1 : 0 );
	$packTypeOther = (isset($_POST['packTypeOther'])? 1 : 0 );
	$packTypeRem = $_POST['packTypeRem'];
	$priceOnOrder = (isset($_POST['priceOnOrder'])? 1 : 0 );
	$priceOnOther = (isset($_POST['priceOnOther'])? 1 : 0 );
	$priceOnRem = $_POST['priceOnRem'];
	$plac2deliCode = (isset($_POST['plac2deliCode'])? $_POST['plac2deliCode'] : '' );
	$plac2deliRem = $_POST['plac2deliRem'];
	$payTypeCode = (isset($_POST['payTypeCode'])? $_POST['payTypeCode'] : '' );
	$payTypeRem = $_POST['payTypeRem'];
	$soNo = substr(str_shuffle(MD5(microtime())), 0, 10);
	
	$orderDate = to_mysql_date($orderDate);
	$deliveryDate = to_mysql_date($deliveryDate);
	
	//begin
	//$pdo->beginTransaction();
	
	$sql = "INSERT INTO `order_header`
	(`soNo`, `poNo`, `orderDate`, `custCode`, `smCode`, `prodGFC`, `prodGFM`, `prodGFT`, `prodSC`, `prodCFC`, `prodEGWM`, `prodGT`, `prodCSM`, `prodWR`, `deliveryDate`, `deliveryRem`, `suppTypeFact`, `suppTypeImp`, `prodTypeOld`, `prodTypeNew`, `custTypeOld`, `custTypeNew`, `prodStkInStk`, `prodStkOrder`, `prodStkOther`, `prodStkRem`, `packTypeAk`, `packTypeNone`, `packTypeOther`, `packTypeRem`, `priceOnOrder`, `priceOnOther`, `priceOnRem`, `remark`, `plac2deliCode`, `plac2deliRem`, `payTypeCode`, `payTypeRem`, `statusCode`, `createTime`, `createByID`) 
	VALUES 
	(:soNo, :poNo, :orderDate, :custCode, :smCode, :prodGFC, :prodGFM, :prodGFT, :prodSC, :prodCFC, :prodEGWM, :prodGT, :prodCSM, :prodWR, :deliveryDate, :deliveryRem, :suppTypeFact, :suppTypeImp, :prodTypeOld, :prodTypeNew, :custTypeOld, :custTypeNew, :prodStkInStk, :prodStkOrder, :prodStkOther, :prodStkRem, :packTypeAk, :packTypeNone, :packTypeOther, :packTypeRem, :priceOnOrder, :priceOnOther, :priceOnRem, :remark, :plac2deliCode, :plac2deliRem, :payTypeCode, :payTypeRem, 'B', now(), :s_userID) 
	";
 
    //$result = mysqli_query($link, $sql);
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':soNo', $soNo);
	$stmt->bindParam(':poNo', $poNo);
	$stmt->bindParam(':orderDate', $orderDate);
	$stmt->bindParam(':custCode', $custCode);
	$stmt->bindParam(':smCode', $smCode);
	$stmt->bindParam(':prodGFC', $prodGFC);
	$stmt->bindParam(':prodGFM', $prodGFM);
	$stmt->bindParam(':prodGFT', $prodGFT);
	$stmt->bindParam(':prodSC', $prodSC);
	$stmt->bindParam(':prodCFC', $prodCFC);
	$stmt->bindParam(':prodEGWM', $prodEGWM);
	$stmt->bindParam(':prodGT', $prodGT);
	$stmt->bindParam(':prodCSM', $prodCSM);
	$stmt->bindParam(':prodWR', $prodWR);
	$stmt->bindParam(':deliveryDate', $deliveryDate);
	$stmt->bindParam(':deliveryRem', $deliveryRem);
	$stmt->bindParam(':suppTypeFact', $suppTypeFact);
	$stmt->bindParam(':suppTypeImp', $suppTypeImp);
	$stmt->bindParam(':prodTypeOld', $prodTypeOld);
	$stmt->bindParam(':prodTypeNew', $prodTypeNew);
	$stmt->bindParam(':custTypeOld', $custTypeOld);
	$stmt->bindParam(':custTypeNew', $custTypeNew);
	$stmt->bindParam(':prodStkInStk', $prodStkInStk);
	$stmt->bindParam(':prodStkOrder', $prodStkOrder);
	$stmt->bindParam(':prodStkOther', $prodStkOther);
	$stmt->bindParam(':prodStkRem', $prodStkRem);
	$stmt->bindParam(':packTypeAk', $packTypeAk);
	$stmt->bindParam(':packTypeNone', $packTypeNone);
	$stmt->bindParam(':packTypeOther', $packTypeOther);
	$stmt->bindParam(':packTypeRem', $packTypeRem);
	$stmt->bindParam(':priceOnOrder', $priceOnOrder);
	$stmt->bindParam(':priceOnOther', $priceOnOther);
	$stmt->bindParam(':priceOnRem', $priceOnRem);
	$stmt->bindParam(':remark', $remark);
	$stmt->bindParam(':plac2deliCode', $plac2deliCode);
	$stmt->bindParam(':plac2deliRem', $plac2deliRem);
	$stmt->bindParam(':payTypeCode', $payTypeCode);
	$stmt->bindParam(':payTypeRem', $payTypeRem);
	$stmt->bindParam(':s_userID', $s_userID);	
	$stmt->execute();
	
	/*$id = $pdo->lastInsertId();
	$soNo = substr('0000000000'.(string)$id,-10);	
	$sql = "UPDATE `order_header` SET soNo=:soNo WHERE id=:id ";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':soNo', $soNo);	
	$stmt->bindParam(':id', $id);	
	$stmt->execute();*/
	
	//commit
	//$pdo->commit();
	
	header('Content-Type: application/json');
    echo json_encode(array('success' => true, 'message' => 'Data Inserted Complete.'));
    /*if ($result) {
      header('Content-Type: application/json');
      echo json_encode(array('success' => true, 'message' => 'Data Inserted Complete.'));
   } else {
      header('Content-Type: application/json');
      $errors = "Error on Data Insertion. Please try again. " . mysqli_error($link);
      echo json_encode(array('success' => false, 'message' => $errors));
   }*/
} 
//Our catch block will handle any exceptions that are thrown.
catch(Exception $e){
    //Rollback the transaction.
    //$pdo->rollBack();
	//return JSON
	header('Content-Type: application/json');
	$errors = "Error on Data Submit. Please try again. " . $poNo . $e->getMessage();
	echo json_encode(array('success' => false, 'message' => $errors));
}
