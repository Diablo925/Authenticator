<?php
class module_controller extends ctrl_module
{
		
		
    /**
     * The 'worker' methods.
     */

		static function doSave()
    {
        global $controller;
		$currentuser = ctrl_users::GetUserDetail();
        runtime_csfr::Protect();
        $formvars = $controller->GetAllControllerRequests('FORM');
        if (isset($formvars['inEnable'])) {
            $Enable = 1;
        } else {
            $Enable = 0;
        }
        if (self::ExecuteUpdate($currentuser['userid'], $Enable))
			return true;
				return false;
    }
	
	static function ExecuteUpdate($uid, $enable)
	 {
		 global $zdbh;
		 global $controller;		 
		$currentuser = ctrl_users::GetUserDetail();
		include('GoogleAuthenticator.php');
		$ga = new PHPGangsta_GoogleAuthenticator();
		
		
		$sql = $zdbh->prepare("SELECT ac_otp_vc FROM x_accounts WHERE ac_id_pk = :uid AND ac_deleted_ts IS NULL");
		$sql->bindParam(':uid', $currentuser['userid']);
		$sql->execute();
		$result = $sql->fetch();
		$status = $result['ac_otp_vc'];
		
		if($enable == 1)
		{
			if($status == 0) { 
		$secret = $ga->_createSecret();
		$sql = $zdbh->prepare("UPDATE x_accounts SET ac_otp_vc=:enable, ac_otpkey_vc=:Seckey WHERE ac_id_pk = :uid");
		$sql->bindParam(':enable', $enable);
		$sql->bindParam(':Seckey', $secret);
        $sql->bindParam(':uid', $currentuser['userid']);
        $sql->execute();
			}
			return true;
		
		}
		if($enable == 0) 
		{
		$otp_key = "";
		$sql = $zdbh->prepare("UPDATE x_accounts SET ac_otp_vc=:enable, ac_otpkey_vc=:Seckey WHERE ac_id_pk = :uid");
		$sql->bindParam(':enable', $enable);
		$sql->bindParam(':Seckey', $otp_key);
        $sql->bindParam(':uid', $currentuser['userid']);
        $sql->execute();
			
			return true;
		}
		
			return true;
		
	 }
	/**
     * End 'worker' methods.
     */

    /**
     * Webinterface sudo methods.
     */ 
	 		
	static function getBarcode() {
		global $zdbh;
		global $controller;
		require_once 'GoogleAuthenticator.php';
		$ga = new PHPGangsta_GoogleAuthenticator();
		
	$currentuser = ctrl_users::GetUserDetail();
	$sql = $zdbh->prepare("SELECT ac_otpkey_vc FROM x_accounts WHERE ac_id_pk = :uid AND ac_deleted_ts IS NULL");
	$sql->bindParam(':uid', $currentuser['userid']);
    $sql->execute();
	$result = $sql->fetch();
	$secret = $result['ac_otpkey_vc'];
	$website = ctrl_options::GetSystemOption('sentora_domain');
	$barcode = $ga->getQRCodeGoogleUrl($currentuser['username'], $secret, $website);
	$barimg = "<image src=". $barcode .">";
	
		if(!empty($secret) || ($secret == 'null')) {
        return $barimg;
		} else { return ""; }
    }
	
	static function getEnable() {
		global $zdbh;
		global $controller;
	$currentuser = ctrl_users::GetUserDetail();
	$sql = $zdbh->prepare("SELECT ac_otp_vc FROM x_accounts WHERE ac_id_pk = :uid AND ac_deleted_ts IS NULL");
	$sql->bindParam(':uid', $currentuser['userid']);
    $sql->execute();
	$result = $sql->fetch();
	$secret = $result['ac_otp_vc'];
		if($secret == 1) {
        return "CHECKED"; } else { 
		return ""; }
    }
	
	 
	static function getSecKey() {
		global $zdbh;
		global $controller;
	$currentuser = ctrl_users::GetUserDetail();
	$sql = $zdbh->prepare("SELECT ac_otpkey_vc FROM x_accounts WHERE ac_id_pk = :uid AND ac_deleted_ts IS NULL");
	$sql->bindParam(':uid', $currentuser['userid']);
    $sql->execute();
	$result = $sql->fetch();
	$secret = $result['ac_otpkey_vc'];
	
		return $secret;
    }
	
	static function getZadmininfo() {
		global $controller;
	$currentuser = ctrl_users::GetUserDetail();
	if($currentuser['username'] == "zadmin") { 
	$res = "You have to download the additional <a href=http://repo.alio.dk/auth/more.zip targer=_blank>file</a> to get it to work.<br />
 they must be copied into their raspative directory under /etc/sentora/panel/...<br />
 <b>Otherwise it does not.</b>"; } else { $res = ""; }
	
		return $res;
    }
	 /**
     * Webinterface sudo methods.
     */
}
?>