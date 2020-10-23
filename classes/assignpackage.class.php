<?php
class assignpackage extends common{

public function assignpackagetoUser($package_name,$user_email){

		$stmt = $this->mysqli->prepare("UPDATE users SET package=? WHERE email=?");
		$stmt->bind_param("ss", $package_name,$user_email);
		if(!$stmt->execute()) return false;
		$stmt->fetch();
		$stmt->close();
}

public function getPackage($package_name = false){

		if($package_name){
			$stmt = $this->mysqli->prepare("SELECT * FROM users WHERE package=? LIMIT 1;");
			$stmt->bind_param("s",$package_name);
		}else{
				$stmt = $this->mysqli->prepare("SELECT * FROM users");
		}
		
		if(!$stmt->execute()) return 0;
		$result = $stmt->get_result();
		$stmt->close();
		$rslt = $result->fetch_all(MYSQLI_ASSOC);


	if(!isset($rslt)) return 0;
	
	return $rslt;
}

}