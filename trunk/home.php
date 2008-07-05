
<div class="holder" style="color:#fff; margin:-15px;background:#1f97ff url(http://images.sinofool.net/xnapp-astrology/header.jpg) no-repeat;padding-top:225px;">
<?php
require_once("xiaonei.php");
function arrayToStr($array){
	if(count($array)==0){
		return "";
	};
	$result="";
	foreach($array as $value){
		$result .= $value . ",";
	};
	return $result;
}
$client = new XiaoNeiRestClient($_REQUEST["xn_sig_api_key"],"9c3b7045303f4c32865c6df3e88f6e56");
$client->session_key = $_REQUEST["xn_sig_session_key"];
$ownerId = $client->user_getLoggedInUser(); 
$ids=$_REQUEST["ids"];
$infos = $client->user_getInfo($ownerId . "," . arrayToStr($ids));
if($_REQUEST["ids"]!="" && $_REQUEST["withInvite"]!=""){
	$client->requests_sendRequest(arrayToStr($_REQUEST["ids"]));
}

if($_REQUEST["subFriend_x"]!=""){
	$opt="friend";
}elseif($_REQUEST["subCross_x"]!=""){
	$opt="cross";
}
switch($opt) {
	case "cross":
		include "name_count.inc.php";
	break;
	case "friend":
		include "name_count.inc.php";
		include "xingzuo_yunshi.inc.php";
	break;
	default:
	break;
}
include "birth_flower.inc.php";
include "friendselect.inc.php";
?>
	<div class="footer" style="background:url(http://images.sinofool.net/xnapp-astrology/footer.jpg) no-repeat left bottom;height:243px;"></div>
</div>
