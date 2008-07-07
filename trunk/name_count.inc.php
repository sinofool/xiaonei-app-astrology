
<h2 style="font-size:15px; font-weight:bold; color:#fff;">姓名笔画</h2>
<?php
require_once("name_count.func.inc.php");
if($opt=="friend"){
	$ownerUid = $infos[0]["uid"];
	$ownerName = $infos[0]["name"];
	$ownerLevel = countName($ownerName);
#foreach($infos as $key => $value) {
	for($pos=1;$pos<count($infos);++$pos){
		$posUid=$infos[$pos]["uid"];
		$posName=$infos[$pos]["name"];
		$posLevel = countName($posName);
?>
<p class="large-font">
&nbsp;&nbsp;<?php echo $posName?>(<?php echo $posLevel?>)和你(<?php echo $ownerLevel?>)的关系是：<?php echo calcName($ownerName, $posName)?>
&nbsp;&nbsp;<a href="sendfeed.php?type=name&o=<?php echo $ownerUid?>&f=<?php echo $posUid?>" target="_blank">发送新鲜事</a>
</p>
<?php
	}
}elseif($opt=="cross"){
	for($pos=1;$pos<count($infos);++$pos){
		$posUid=$infos[$pos]["uid"];
		$posName=$infos[$pos]["name"];
		$posLevel = countName($posName);
		for($tag=$pos;$tag<count($infos);++$tag){
			if($tag==$pos)continue;
			$tagUid=$infos[$tag]["uid"];
			$tagName=$infos[$tag]["name"];
			$tagLevel = countName($tagName);
?>
<p class="large-font">
&nbsp;&nbsp;<?php echo $posName?>(<?php echo $posLevel?>)和<?php echo $tagName?>(<?php echo $tagLevel?>)的关系是：<?php echo calcName($posName, $tagName)?>
&nbsp;&nbsp;<a href="sendfeed.php?type=name&o=<?php echo $posUid?>&f=<?php echo $tagUid?>" target="_blank">发送新鲜事</a>
</p>
<?php
		}
	}
}

if($_REQUEST["friends_id_list"] != ""){
	$conn = mysql_connect("localhost", "root", "");
	mysql_select_db("astrology", $conn);
	mysql_query("INSERT INTO name_number_log (ownerId, targetIds) VALUES (".$ownerId.", '".$_REQUEST["friends_id_list"]."')", $conn);
	mysql_close($conn);
}
?>

