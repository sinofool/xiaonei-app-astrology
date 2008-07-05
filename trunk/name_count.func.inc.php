<?php
$letter_meaning=array(
"0" => "亲密无间",
"1" => "永远和在一起",
"2" => "水火不相容",
"3" => "知心朋友",
"4" => "心上人",
"5" => "帮助做事的人",
"6" => "互相帮助的人",
"7" => "面和心不合",
"8" => "关系不正常",
"9" => "情投意合",
"10" => "关系马虎",
"11" => "尊敬的人",
"12" => "相爱的人",
"13" => "适合你的",
"14" => "说坏话的人",
"15" => "克星",
"16" => "救星",
"17" => "忠心的人",
"18" => "狼心狗肺的人",
"19" => "单相思",
"20" => "山盟海誓",
"21" => "情敌",
"22" => "服从的人",
"23" => "永远在一起",
"24" => "伴终生",
"25" => "恨你又爱你");

require_once("name_count.db.inc.php");
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

function calcName($name1,$name2){
	global $letter_meaning;
	$id1=countName($name1);
	$id2=countName($name2);
	$ret=$letter_meaning[abs($id1-$id2)];
	if($ret==""){
		$ret="很神秘";
	};
	return $ret;
}


?>
