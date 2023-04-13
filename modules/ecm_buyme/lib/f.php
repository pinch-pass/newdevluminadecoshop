<?php
if(strlen($_GET["fields"]) === 0) {
	$f = "Имя, Телефон, -Адрес доставки";
} else {
	$f = $_GET["fields"];
}

function b1c($s) {
	echo $_GET[$s];
}
?>

<div class="b1c-bg"></div>
<div class="b1c-form">
	<div class="b1c-tl">
		<div class="b1c-close">
			<img class="b1c-close b1c-opct" src='/modules/ecm_buyme/templates/blank.gif' alt='"Закрыть"' />
		</div>
		<span class="b1c-title-name"><?php b1c('title'); ?></span>
	</div>
	<div class='b1c-description'>
		<?php b1c('description'); ;?>
	</div>

<?php
$f = str_replace(", ", ",", $f);
$f = str_replace("'", "\"", $f);
//$f = iconv("UTF-8", "windows-1251", $f);
$f = explode(",", $f);
for ($i=0; $i < count($f); $i++){
	if ($f[$i][0] == "-") {
		echo "<div class='b1c-caption'>".substr($f[$i], 1)."</div>";
		echo "<textarea placeholder='".substr($f[$i], 1)."' class='b1c-txt'></textarea>";
	} elseif ($f[$i][0] == "!") {
		$str = substr($f[$i], 1);
		$str = explode("!", $str);
		echo "<div class='b1c-caption'>".$str[0]."</div>";
		echo "<select class='b1c-select' name='".$str[0]."'>";
		for ($j=1; $j<count($str); $j++) {
			echo "<option value=".$str[$j].">".$str[$j]."</option>";
		}
		echo"</select>";
	} else {
		echo "<div class='b1c-caption'>".$f[$i]."</div>";
		echo "<input placeholder='".$f[$i]."' class='b1c-txt' type='text' maxlength='150'>";
	}
}
?>

	<div class="politika-konfic">
	Нажимая кнопку "Оформить заказ" вы соглашаетесь с 
	<a href="/content/16-politika-konfidentsialnosti" target="_blank">политикой конфиденциальности</a>
	</div>

	<div class="b1c-submit-area">
		<input type="button" class="b1c-submit" value="<?php b1c('button'); ?>">
		<div class="b1c-result"></div>
	</div>
</div>