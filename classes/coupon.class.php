<?php
class coupon extends common{

public function createCoupon($coupon_name,$coupon_date,$coupon_discount){

		$created = date('Y-m-d H:i:s');
		//Записываем хэш
		$stmt = $this->mysqli->prepare("INSERT INTO coupon (coupon_name, coupon_date, coupon_discount, created) VALUES (?, ?, ?, ?)");
		$stmt->bind_param("ssss", $coupon_name,$coupon_date,$coupon_discount,$created);
		if(!$stmt->execute()) return false;
		$stmt->fetch();
		$stmt->close();
}

public function getCoupon($coupon_name = false){

		if($coupon_name){
			$stmt = $this->mysqli->prepare("SELECT * FROM coupon WHERE coupon_name=? LIMIT 1;");
			$stmt->bind_param("s",$coupon_name);
		}else{
				$stmt = $this->mysqli->prepare("SELECT * FROM coupon");
		}
		
		if(!$stmt->execute()) return 0;
		$result = $stmt->get_result();
		$stmt->close();
		$rslt = $result->fetch_all(MYSQLI_ASSOC);


	if(!isset($rslt)) return 0;
	
	return $rslt;
}

public function deleteCoupon($couponid = false){

		if($couponid){
			$stmt = $this->mysqli->prepare("DELETE from coupon WHERE ID=?");
			$stmt->bind_param("i", $couponid );
			$stmt->execute();
			$stmt->fetch();
			$stmt->close();
		}
		
		
}

}