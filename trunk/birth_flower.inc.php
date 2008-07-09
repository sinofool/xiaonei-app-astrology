<?php
$result_flower="";
$result_word="";
$result_comment="";
$conn = mysql_connect("localhost", "root", "");
mysql_select_db("astrology", $conn);
mysql_query("SET NAMES utf8");
for($pos=0;$pos<count($infos);++$pos){
	$posId = $infos[$pos]["uid"];
	$posName=$infos[$pos]["name"];
	$posBirth=$infos[$pos]["birthday"];
	$birthday=getdate(strtotime($posBirth));
	$res = mysql_query("SELECT flower, word, content FROM birth_flower WHERE month='".$birthday["mon"]."' AND day='".$birthday["mday"]."'", $conn);
	if($row = mysql_fetch_array($res, MYSQL_ASSOC)) {
		$result_flower=$row["flower"] . $birthday["mon"]."月".$birthday["mday"]."日";
		$result_word=$row["word"];
		$result_comment=$row["content"];
	}
	mysql_free_result($res);
}
mysql_close($conn);

$client->profile_setXNML($ownerId, $result_flower . "<br/>" . $result_word . "<br/>" . $result_comment ,"http://apps.xiaonei.com/astrology/");
?>
