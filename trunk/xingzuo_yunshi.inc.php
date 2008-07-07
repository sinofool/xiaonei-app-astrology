
<p class="large-font">
<b>星座运势</b>
</p>
<?php 
function astName($birth){
	$d=date_parse($birth);
	$month=$d["month"];
	$day=$d["day"];
	$conn = mysql_connect("localhost", "root", "");
	mysql_select_db("astrology", $conn);
	mysql_query("SET NAMES utf8");
	$res = mysql_query("SELECT zh_name, en_name FROM xingzuo_dict WHERE month='".$month."' AND day='".$day."'", $conn);
	$result="";
	while($row = mysql_fetch_array($res, MYSQL_ASSOC)) {
		$result=$row["zh_name"];
	}
	mysql_free_result($res);
	mysql_close($conn);
	return $result;
}
function astId($birth){
	$d=date_parse($birth);
	$month=$d["month"];
	$day=$d["day"];
	$conn = mysql_connect("localhost", "root", "");
	mysql_select_db("astrology", $conn);
	$res = mysql_query("SELECT zh_name, en_name FROM xingzuo_dict WHERE month='".$month."' AND day='".$day."'", $conn);
	$result="";
	while($row = mysql_fetch_array($res, MYSQL_ASSOC)) {
		$result=$row["en_name"];
	}
	mysql_free_result($res);
	mysql_close($conn);
	return $result;
}
if($opt=="friend"){
	$ownerId = $infos[0]["uid"];
	$ownerBirth = $infos[0]["birthday"];
	$ownerSex = $infos[0]["sex"];
	$ownerAstName = astName($ownerBirth);
	$ownerAstId = astId($ownerBirth);
	
	echo "你是", $ownerAstName, "<br/>";
	for($pos=1;$pos<count($infos);++$pos){
		$posName=$infos[$pos]["name"];
		$posBirth=$infos[$pos]["birthday"];
		$posSex=$infos[$pos]["sex"];
		$posAstName = astName($posBirth);
		$posAstId = astId($posBirth);
		if($posAstId=="")continue;
		echo $posName,"是", $posAstName, "<br/>";
		if($ownerSex==$posSex){
			echo "目前只能测异性^_^<br/><br/>";
			continue;
		}
		$maleAstName = $ownerSex=="1" ? $ownerAstName : $posAstName;
		$maleAstId =  $ownerSex=="1" ? $ownerAstId : $posAstId;
		$femaleAstName = $ownerSex=="0" ? $ownerAstName : $posAstName;
		$femaleAstId =  $ownerSex=="0" ? $ownerAstId : $posAstId;

		if($maleAstName=="" || $maleAstId=="" || $femaleAstName=="" || $femaleAstId==""){
			echo "<br/>没有填写生日<br/>";
		}else{
		$conn = mysql_connect("localhost", "root", "");
		mysql_select_db("astrology", $conn);
		mysql_query("SET NAMES utf8");
		$res = mysql_query("SELECT ".$maleAstId." FROM xingzuo WHERE owner='".$femaleAstName."'", $conn);
		while($row = mysql_fetch_array($res, MYSQL_ASSOC)) {
			echo str_replace("\n","<br/>",$row[$maleAstId]), "<br/><br/>";
		}
		}
	}
}
?>
