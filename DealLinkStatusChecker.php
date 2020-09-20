<?php
/**
 * Created by PhpStorm.
 * User: Лука
 * Date: 23.12.2019
 * Time: 23:28
 */
require_once 'DBWorker.php';
$dbWorker = new DBWorker();
$deals = $dbWorker->getAmoDeals();
$count = 0;
if (time() - fileatime('cookie5.txt') > 82800)
{
	authorize();
}
foreach ($deals as $deal)
{
	$id = $deal['deal_id'];
	$deal_index = $deal['deal_index'];
	$result = array();
	$status = 1;
	$url = $deal['url'];
	if (strrpos($url, 'cian') !== false)
	{
		$matches = array();
		preg_match('/(\d*)$/m', $url, $matches);
		$cianid = $matches[0];
		echo isActive($cianid) . " " . $url . "<br/>";
	}
}
function updateStatus($deal_id)
{
	$data = array('update' => array(0 => array('id' => '$deal_id', 'updated_at' => time(), 'custom_fields' => array(0 => array('id' => '2016467', 'values' => array(0 => array('value' => '1',),),),),),),);
	$link = "https://zavidov.amocrm.ru/api/v2/leads/";

	$headers[] = "Accept: application/json";

	//Curl options
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_USERAGENT, "amoCRM-API-client-
undefined/2.0");
	curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
	curl_setopt($curl, CURLOPT_URL, $link);
	curl_setopt($curl, CURLOPT_HEADER, false);
	curl_setopt($curl, CURLOPT_COOKIEFILE, dirname(__FILE__) . "/cookie5.txt");
	curl_setopt($curl, CURLOPT_COOKIEJAR, dirname(__FILE__) . "/cookie5.txt");
	$out = curl_exec($curl);
	curl_close($curl);
	$result = json_decode($out, TRUE);
}

function calculateTheDistance($φA, $λA, $φB, $λB)
{

	$lat1 = $φA * M_PI / 180;
	$lat2 = $φB * M_PI / 180;
	$long1 = $λA * M_PI / 180;
	$long2 = $λB * M_PI / 180;

	$cl1 = cos($lat1);
	$cl2 = cos($lat2);
	$sl1 = sin($lat1);
	$sl2 = sin($lat2);
	$delta = $long2 - $long1;
	$cdelta = cos($delta);
	$sdelta = sin($delta);

	$y = sqrt(pow($cl2 * $sdelta, 2) + pow($cl1 * $sl2 - $sl1 * $cl2 * $cdelta, 2));
	$x = $sl1 * $sl2 + $cl1 * $cl2 * $cdelta;

	$ad = atan2($y, $x);
	$dist = $ad * EARTH_RADIUS;
	return $dist;
}

function isActive($id)
{
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.79 Safari/537.36');
	curl_setopt($curl, CURLOPT_URL, 'https://www.cian.ru/sale/flat/' . $id);
	curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
	curl_setopt($curl, CURLOPT_PROXY, "118.175.93.148:42409");
	curl_setopt($curl, CURLOPT_HEADER, true);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
	$out = curl_exec($curl);
	$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	exit();
	if (strpos($out, "Объявление снято с публикации") !== false)
	{
		return "false";
	}
	else
	{
		return "true";
	}
}

function authorize()
{
	$user = array('USER_LOGIN' => 'teo@zavidov.realty', 'USER_HASH' => 'e4de44d0315786800bbe997baa0fba9ab3df0927',);
	$subdomain = 'zavidov';
	$link = 'https://' . $subdomain . '.amocrm.ru/private/api/auth.php?type=json';
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_USERAGENT, 'amoCRM-API-client/1.0');
	curl_setopt($curl, CURLOPT_URL, $link);
	curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
	curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($user));
	curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	curl_setopt($curl, CURLOPT_HEADER, false);
	curl_setopt($curl, CURLOPT_COOKIEFILE, dirname(__FILE__) . '/cookie5.txt');
	curl_setopt($curl, CURLOPT_COOKIEJAR, dirname(__FILE__) . '/cookie5.txt');
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
	$out = curl_exec($curl);
	$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	curl_close($curl);
	$code = (int)$code;
	$errors = array(301 => 'Moved permanently', 400 => 'Bad request', 401 => 'Unauthorized', 403 => 'Forbidden', 404 => 'Not found', 500 => 'Internal server error', 502 => 'Bad gateway', 503 => 'Service unavailable',);
	try
	{
		if ($code != 200 && $code != 204)
		{
			throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undescribed error', $code);
		}

	}
	catch (Exception $E)
	{
		die('Ошибка: ' . $E->getMessage() . PHP_EOL . 'Код ошибки: ' . $E->getCode());
	}
	$Response = json_decode($out, true);
	$Response = $Response['response'];
	if (isset($Response['auth']))
	{
		//echo 'Авторизация прошла успешно';
	}
}
