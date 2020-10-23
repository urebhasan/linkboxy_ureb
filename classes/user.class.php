<?php
class user extends common{
public $user_name;
public $user_id;
public $is_authorized;

	function __construct( $config ) {
	    parent::__construct( $config );
	}
// function __construct(){
// 	global $mysqli;
// 	global $m;
// 	//Если пользователь авторизован, то ставим переменную is_authorized в 1
// 	if($this->user_authorized()){
// 		$this->user_id=$_SESSION['user_id'];
// 		$this->is_authorized=1;
// 	}else{
// 		$this->is_authorized=0;
// 	}
// }

public function show_uid(){


return $this->user_id;
}







public function clear_auth_cookie($ban_captcha=0){
setcookie("cookiescookies", '', time()-3333333, '/', $this->domain, false);
}


public function authUserById($uid,$remember = 1){


	if($remember == 1){
		//Формируем hash и authenticator
		$hash = base64_encode(openssl_random_pseudo_bytes(4));
		$authenticator = openssl_random_pseudo_bytes(8);
		$tkn = hash('md5', $authenticator);
		$datetime=date('Y-m-d\TH:i:s', time() + 60*60*24*30);
		//Записываем хэш
		$stmt = $this->mysqli->prepare("INSERT INTO users_tokens (hash, token, userid, expires) VALUES (?, ?, ?, ?)");
		$stmt->bind_param("ssis", $hash,$tkn,$uid,$datetime);
		if(!$stmt->execute()) return false;
		$stmt->fetch();
		$stmt->close();
		setcookie("cookiescookies", $hash.':'.base64_encode($authenticator), time()+60*60*24*30, '/', $this->domain, false);
		$_SESSION['user_id'] = $uid;
	}
	
	return true;
}


public function user_websites(){
		$user_id = $_SESSION['user_id'];
		$stmt = $this->mysqli->prepare("SELECT users_websites.id,users_websites.user_id,users_websites.url,users_websites.time,count(DISTINCT users_backlinks_link.id) as backlinks_count,sum(users_backlinks_link.status=1) as success_backlinks,sum(users_backlinks_link.status=0) as unchecked_backlinks,sum(users_backlinks_link.status=2) as failed_backlinks,sum(users_backlinks_link.nofollow=1) as nofollow_backlinks from users_websites
LEFT JOIN users_backlinks on users_backlinks.domain_id=users_websites.id
LEFT JOIN users_backlinks_link on users_backlinks_link.backlink_id=users_backlinks.id
WHERE users_websites.user_id=? GROUP by users_websites.id ORDER by time DESC");
		$stmt->bind_param("i", $_SESSION['user_id']);
		if(!$stmt->execute()) return false;
		$result = $stmt->get_result();
		$stmt->close();
		$rslt = $result->fetch_all(MYSQLI_ASSOC);

		$filename = "../storage/users/out_user_web".$user_id.".json";
		if (!file_exists($filename)){
			$fp = fopen($filename, 'w');
			fwrite($fp, json_encode($rslt));
			fclose($fp);
		}

	return ((array_key_exists('0', $rslt) and $rslt['0']['id']==null) ? null : $rslt);
}

public function user_backlinks(){

	$user_id = $_SESSION['user_id'];
	if(isset($_GET['domain'])){
		
		$stmt = $this->mysqli->prepare("SELECT users_backlinks.id, users_backlinks.status as backlink_status, users_backlinks.domain_id, users_backlinks.url as backlink_url,users_backlinks_link.id as backlinks_link_id, users_backlinks_link.url as backlink_link_url, users_backlinks_link.status, users_backlinks_link.nofollow, users_backlinks_link.anchor_text, users_backlinks_link.time_checked, users_websites.url as domain from users_backlinks
	LEFT JOIN users_backlinks_link on users_backlinks_link.backlink_id = users_backlinks.id
	LEFT JOIN users_websites on users_websites.id = users_backlinks.domain_id
	WHERE users_backlinks.user_id = ? AND users_backlinks.domain_id = ? ORDER by id DESC, users_backlinks.time_added DESC");
			$stmt->bind_param("ii", $_SESSION['user_id'],$_GET['domain']);

	}else{
		$stmt = $this->mysqli->prepare("SELECT users_backlinks.id, users_backlinks.status as backlink_status, users_backlinks.domain_id, users_backlinks.url as backlink_url,users_backlinks_link.id as backlinks_link_id, users_backlinks_link.url as backlink_link_url, users_backlinks_link.status, users_backlinks_link.nofollow, users_backlinks_link.anchor_text, users_backlinks_link.time_checked, users_websites.url as domain from users_backlinks
		LEFT JOIN users_backlinks_link on users_backlinks_link.backlink_id = users_backlinks.id
		LEFT JOIN users_websites on users_websites.id = users_backlinks.domain_id
		WHERE users_backlinks.user_id = ? ORDER by id DESC, users_backlinks.time_added DESC");
	$stmt->bind_param("i", $_SESSION['user_id']);
	}
	if(!$stmt->execute()) return false;
	$result = $stmt->get_result();
	$stmt->close();
	$rslt = $result->fetch_all(MYSQLI_ASSOC);

	$filename = "../storage/users/out_user_bl".$user_id.".json";
	if (!file_exists($filename)){
		$fp = fopen($filename, 'w');
		fwrite($fp, json_encode($rslt));
		fclose($fp);
	}

	return $rslt;
}

public function user_info_by_id($id=0){
	

	
	if($id == 0 and $this->user_authorized()){
		$id = $_SESSION['user_id'];
	}
	

	$stmt = $this->mysqli->prepare("SELECT users.id,users.name,users.email,users.password,users.package,users.ip,users.activate,count(DISTINCT users_backlinks.id) as user_backlinks, count(DISTINCT users_websites.id) as user_websites FROM users 
LEFT JOIN users_backlinks on users_backlinks.user_id = users.id
LEFT JOIN users_websites on users_websites.user_id = users.id
WHERE users.id=? GROUP by users.id LIMIT 1;");
		if ($stmt === false) return 0;
		$stmt->bind_param("i", $id);
		if(!$stmt->execute()) return 0;
		$result = $stmt->get_result();
		$stmt->close();
		$rslt = $result->fetch_array(MYSQLI_ASSOC);


	if(!isset($rslt['id'])) return 0;
	
	return $rslt;
}


public function activateAccount($code){
	$stmt = $this->mysqli->prepare("SELECT id from users WHERE activate = ? LIMIT 1;");
	$stmt->bind_param("s", $code);
	if(!$stmt->execute()) return 0;
	$result = $stmt->get_result();
	$stmt->close();
	$rslt = $result->fetch_array(MYSQLI_ASSOC);

	if(!$rslt) return false;
	$stmt = $this->mysqli->prepare("UPDATE users SET activate = NULL WHERE id = ".$rslt['id']." LIMIT 1");
	if(!$stmt->execute()) return false;
	$stmt -> close();
	
return $rslt['id'];
}

public function user_info_by_email($email){
	
	
	if(!filter_var($email, FILTER_VALIDATE_EMAIL)) return false;

	$stmt = $this->mysqli->prepare("SELECT users.id,users.name,users.email,users.password,users.ip,users.activate,count(DISTINCT users_backlinks.id) as user_backlinks, count(DISTINCT users_websites.id) as user_websites FROM users 
LEFT JOIN users_backlinks on users_backlinks.user_id = users.id
LEFT JOIN users_websites on users_websites.user_id = users.id
WHERE users.email=? GROUP by users.id LIMIT 1;");
		$stmt->bind_param("s", $email);
		if(!$stmt->execute()) return 0;
		$result = $stmt->get_result();
		$stmt->close();
		$rslt = $result->fetch_array(MYSQLI_ASSOC);


	if(!isset($rslt['id'])) return 0;
	
	return $rslt;
}

public function createUser($name, $email, $password,$package){
	if(!filter_var($email, FILTER_VALIDATE_EMAIL)) return false;
	$freeplans = ['monthly-freebie','yearly-freebie'];
	if(!in_array($package, $freeplans)){ $package = 'monthly-freebie'; }
	$name = strip_tags($name);
	$activate = md5(openssl_random_pseudo_bytes(12));
	$hash = password_hash($password, PASSWORD_DEFAULT);
	$stmt = $this->mysqli->prepare("INSERT INTO users(name,email,password,ip,activate,package) VALUE(?,?,?,INET_ATON(?),?,?)");
	$stmt->bind_param("ssssss", $name, $email, $hash, $_SERVER['REMOTE_ADDR'], $activate, $package);
	if(!$stmt->execute()) return false;
	$userId = $stmt->insert_id;
	$_SESSION['reg_id'] = $userId;
	$stmt->close();

return $activate;
}

public function userChangePassword($userId){

	if(!$this->user_info_by_id($userId)) return false;

	$password = substr(str_shuffle('1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcefghijklmnopqrstuvwxyz'), 0, rand(7,9));
	$hash = password_hash($password, PASSWORD_DEFAULT);
	$stmt = $this->mysqli->prepare("UPDATE users SET password = ? WHERE id = ? LIMIT 1");
	$stmt->bind_param("si", $hash, $userId);
	if(!$stmt->execute()) return false;
	$stmt->close();

return $password;
}

public function user_authorized(){

if(isset($_SESSION['user_id']) and $_SESSION['user_id'] != 0){
	return true;
}

if(!isset($_SESSION['user_id']) or empty($_SESSION['user_id'])){
if(isset($_COOKIE['cookiescookies']) and !empty($_COOKIE['cookiescookies'])) {

	
	if(!$explode=explode(':', $_COOKIE['cookiescookies'])){
		self::clear_auth_cookie(1);
		return false;
	}
    list($hash, $token) = $explode;
	
	if(!$token=hash('md5', base64_decode($token))){
		self::clear_auth_cookie(1);
			//echo'cooki2e2';
		return false;
	}
	
	if(mb_strlen($hash)!=12 or mb_strlen($token)!=64){ //если хеш или токен не равны их референсной длине, то удаляем куки
		self::clear_auth_cookie(1);
			//echo'cookie1231';
	//echo strlen($hash).' '.strlen($token);
		return false;
	}
	
	//echo $hash;
	
	$stmt = $this->mysqli->prepare("SELECT * FROM users_tokens WHERE hash=? ORDER by id DESC LIMIT 1");
	$stmt->bind_param("i", $hash);
	$stmt->execute();
	$result = $stmt->get_result();
	$stmt->close();
	$data = $result->fetch_all(MYSQLI_ASSOC);
	
	if(empty($data['0']['id'])){ //если по текущему хешу ничего не найдено в базе то
	//echo'<h1>nothing</h1>';
		//echo'cookie31231';
			self::clear_auth_cookie(1);
			return false;
	}
	


	
	if(strtotime($data['0']['expires'])<time()){ // если время токена истекло, то удаляем из базы и возвращаем 0 на авторизацию
	$stmt = $this->mysqli->prepare("DELETE from users_tokens WHERE id=?");
	$stmt->bind_param("i", $data['0']['id']);
	$stmt->execute();
	$stmt->fetch();
	$stmt->close();
		//echo'cookie33333333';
	return false;
	}

    if (hash_equals($data['0']['token'], $token)) {
        $_SESSION['user_id'] = $data['0']['userid'];
		$hash = base64_encode(openssl_random_pseudo_bytes(4));
		$authenticator = openssl_random_pseudo_bytes(8);
		$tkn = hash('md5', $authenticator);
		$datetime=date('Y-m-d\TH:i:s', time() + 60*60*24*30);
		
		
		$stmt = $this->mysqli->prepare("UPDATE users_tokens SET hash=?, token=?, expires=? WHERE id=?");
		$stmt->bind_param("sssi", $hash,$tkn,$datetime,$data['0']['id']);
		$stmt->execute();
		$stmt->fetch();
		$stmt->close();
		setcookie("cookiescookies", $hash.':'.base64_encode($authenticator), time()+60*60*24*30, '/', $this->domain, false);

		return true;
    }else{
		self::clear_auth_cookie(1);
		return false;		
	}
}
}



return false;
}





}