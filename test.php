<?php
/**
 * Created by PhpStorm.
 * User: Лука
 * Date: 29.01.2020
 * Time: 13:01
 */
include 'DBWorker.php';
$dbWorker = new DBWorker();
$ads = $dbWorker->getAvitoFullAds();
for ($i = 0; $i < 24032; $i++)
{
	echo $ads[$i]['postfix'].'  ';
	$ads[$i]['postfix'] = unicodeString($ads[$i]['postfix']);
	//var_dump($ads[$i]);
	echo $ads[$i]['ad_id'].' '.$ads[$i]['postfix'] . '<br/>';
	$dbWorker->updateAvitoAdPostFix( $ads[$i]['ad_id'],$ads[$i]['postfix']);
}
function unicodeString($str, $encoding=null) {
	if (is_null($encoding)) $encoding = ini_get('mbstring.internal_encoding');
	return preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/u', function($match) use ($encoding) {
		return mb_convert_encoding(pack('H*', $match[1]), $encoding, 'UTF-16BE');
	}, $str);
}