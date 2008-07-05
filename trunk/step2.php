<?php
require_once("xiaonei.php");
require_once("letter.inc.php");
require_once("letter.db.php");

$letter_level = array();

foreach($str as $key => $value) {
	$lett = str_split($value, 3);
	foreach($lett as $leKey => $leValue) {
		$letter_level[$leValue] = $key;
	}
}

function countName($name){
	global $letter_level;
	$name_split = str_split($name, 3);
	foreach($name_split as $leKey => $leValue) {
		$count = $letter_level[$leValue];
		$ret += $count;
	}
	return $ret;
}

$client = new XiaoNeiRestClient($_REQUEST["xn_sig_api_key"],"9c3b7045303f4c32865c6df3e88f6e56");
$client->session_key = $_REQUEST["xn_sig_session_key"];
$ownerId = $client->user_getLoggedInUser(); 
$ids=split(",", $ownerId . "," . $_REQUEST["friends_id_list"] );;
#foreach($ids as $key){
#	echo $key;
#}

$infos = $client->user_getInfo($ids);

?>
<p class="large-font">
	<xn:name uid="loggedinuser" linked="true" />&nbsp;你好～
</p>
<form action="/step2.php">
<div class="cube">
    <xn:multi-friend-select-panel max="3" />
</div>
<input type="submit" value="OK" />
<p class="large-font"><b><input type="radio" name="opt" value="self" checked="true" /><input type="radio" name="opt" value="friend" /><input type="radio" name="opt" value="people" /></b></p>
</form>
<?php
include "name_count.inc.php";
include "birth_flower.inc.php";
include "shuxiang.inc.php";
include "xingzuo_dianping.inc.php";
include "xingzuo_xuexing.inc.php";
include "xingzuo_yunshi.inc.php";
?>
