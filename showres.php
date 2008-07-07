
			<div style="background:#ffd5ff url(http://images.sinofool.net/xnapp-astrology/img/res_bg.jpg) repeat; ">
				<div style="margin-left:40px;margin-right:40px; line-height:20px; font-size:15px;">
					<h1 style="font-size:16px; font-weight:bold; margin-bottom:10px;">测试结果</h1>
					<div style="margin-left:10px;">
<?php
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
?>
						<p class="last">&nbsp;</p>
					</div>		
				</div>
			</div>
