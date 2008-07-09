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
$client = new XiaoNeiRestClient($_REQUEST["xn_sig_api_key"],"");
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
include "header.php";
include "showres.php";
include "footer.php";
?>
