SELECT hdr.sdNo, hdr.sendDate, SUM(itm.qty) as sumQty  
				FROM send hdr 
				INNER JOIN send_detail dtl ON dtl.sdNo=hdr.sdNo 
				INNER JOIN product_item itm ON itm.prodItemId=dtl.prodItemId 
					AND itm.prodCodeId=:prodId 
				WHERE 1 
				AND hdr.toCode='8' 
				AND hdr.statusCode<>'X' 
				AND hdr.rcNo IS NULL 
				GROUP BY hdr.sdNo, hdr.sendDate 
				
				$stmt = $pdo->prepare($sql);

				
						
						/*if($search_word<>""){ $sql = "and (prd.code like '%".$search_word."%' OR prd.name like '%".$search_word."%') "; }
						if($sloc<>""){ $sql .= " AND sb.sloc='$sloc' ";	}
						if($catCode<>""){ $sql .= " AND catCode='$catCode' ";	}	
						if($prodId<>""){ $sql .= " AND prodId='$prodId' ";	}*/
