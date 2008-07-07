
<?php
require_once("xiaonei.php");
$client = new XiaoNeiRestClient($_REQUEST["xn_sig_api_key"],"9c3b7045303f4c32865c6df3e88f6e56");
$client->session_key = $_REQUEST["xn_sig_session_key"];
$ownerId = $client->user_getLoggedInUser(); 

$feedId=$_REQUEST["id"];
$type=$_REQUEST["type"];

if($feedId=="") die("需要参数");
if($type!="name" && $type!="fff") die("需要参数");

if($type=="name"){
	require_once("name_count.func.inc.php");

	$conn = mysql_connect("localhost", "root", "");
	mysql_select_db("astrology", $conn);
	$res=mysql_query("SELECT uid1,uid2,cont FROM feed_name_record WHERE id=".$feedId, $conn);
	if($row = mysql_fetch_array($res, MYSQL_ASSOC)) {
		$uid1=$row["uid1"];
		$uid2=$row["uid2"];
		$cont=$row["cont"];

		$template_id="2";
		$title_data="{}";
		$body_data="{\"uid1\":\"".$uid1."\",\"uid2\":\"".$uid2."\",\"cont\":\"".$cont."\",\"feedid\":\"".$feedId."\"}";
		$resource_id=$feedid;
		//echo $body_data;
		$ret = $client->feed_publishTemplatizedAction($template_id, $title_data, $body_data, $resource_id);
		//if($ret==1){
		//	echo "发送成功<br/>";
		//}
	}
	mysql_close($conn);

}
?>
<img src="http://images.sinofool.net/xnapp-astrology/img/done.jpg" />
