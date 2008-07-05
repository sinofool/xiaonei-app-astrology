
<?php
require_once("xiaonei.php");
$client = new XiaoNeiRestClient($_REQUEST["xn_sig_api_key"],"9c3b7045303f4c32865c6df3e88f6e56");
$client->session_key = $_REQUEST["xn_sig_session_key"];
$ownerId = $client->user_getLoggedInUser(); 

$ownerId=$_REQUEST["o"];
$targetId=$_REQUEST["f"];
$type=$_REQUEST["type"];

if($ownerId=="" || $targetId=="") die("需要参数");
if($type!="name" && $type!="fff") die("需要参数");

$ids="".$ownerId.",".$targetId;
$infos = $client->user_getInfo($ids);
if($type=="name"){
	require_once("name_count.func.inc.php");
	$result=calcName($infos[0]["name"], $infos[1]["name"]);

	$conn = mysql_connect("localhost", "root", "");
	mysql_select_db("astrology", $conn);
	mysql_query("INSERT INTO feed_name_record (uid1,uid2,cont) VALUES (".$infos[0]["uid"].",".$infos[1]["uid"].",'".$result."')", $conn);
	$feedid=mysql_insert_id();
	mysql_close($conn);

	$template_id="1";
	$title_data="{}";
	$body_data="{\"uid1\":\"".$infos[0]["uid"]."\",\"uid2\":\"".$infos[1]["uid"]."\",\"cont\":\"".$result."\",\"feedid\":\"".$feedid."\"}";
	$resource_id=$ownerId;
	echo $body_data;
	$ret = $client->feed_publishTemplatizedAction($template_id, $title_data, $body_data, $resource_id);
	if($ret==1){
		echo "发送成功<br/>";
	}
}
?>
