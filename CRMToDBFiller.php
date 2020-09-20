<?php
/**
 * Created by PhpStorm.
 * User: Лука
 * Date: 17.12.2019
 * Time: 18:00
 */
include 'DBWorker.php';
$dbWorker = new DBWorker();
authorize();
$max = $dbWorker->getDealsMaxUpdateTime();
$json = getLeads($max);
foreach ($json['_embedded']['items'] as $item)
{
	$deal_id = $item['id'];
	if (!$dbWorker->checkIfDealExists($deal_id))
	{
		$fields = getDealFields($item);
		$dbWorker->insertDealToFullListTable($item['id'], $item['updated_at'],
			isset($fields['2009773'][0]['value']) ? $fields['2009773'][0]['value'] : '',
			isset($fields['1582680'][0]['value']) ? $fields['1582680'][0]['value'] : '',
			isset($fields['2004177'][0]['value']) ? $fields['2004177'][0]['value'] : '',
			isset($fields['2004179'][0]['value']) ? $fields['2004179'][0]['value'] : '',
			isset($fields['1582682'][0]['value']) ? $fields['1582682'][0]['value'] : '',
			isset($fields['1582708'][0]['value']) ? $fields['1582708'][0]['value'] : '',
			isset($fields['1582710'][0]['value']) ? $fields['1582710'][0]['value'] : '',
			isset($fields['1582702'][0]['value']) ? $fields['1582702'][0]['value'] : '');
	}
}
function getDealFields($deal)
{
	$result = array();
	foreach ($deal['custom_fields'] as $custom_field)
	{
		$result[$custom_field['id']] = $custom_field['values'];
	}
	return $result;
}

function isActive($id)
{
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.79 Safari/537.36');
	curl_setopt($curl, CURLOPT_URL, 'https://www.cian.ru/sale/flat/' . $id . '/');
	curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
	curl_setopt($curl, CURLOPT_HEADER, false);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
	$out = curl_exec($curl);
	$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	if (strpos($out, "Объявление снято с публикации") !== false)
	{
		echo 'Снято';
	}
	else
	{
		echo 'Активно';
	}
}

function getLeads($time)
{
	$link = 'https://zavidov.amocrm.ru/api/v2/leads?filter%5Bdate_modify%5D%5Bfrom%5D=' . $time . '&status%5B%5D=28826554&status%5B%5D=30804025&status%5B%5D=30804283&status%5B%5D=12997674&status%5B%5D=12997677&status%5B%5D=12997680&status%5B%5D=12997683&status%5B%5D=12997722&status%5B%5D=12997725&status%5B%5D=12997719';

	$headers[] = "Accept: application/json";

	//Curl options
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_USERAGENT, "amoCRM-API-client-
undefined/2.0");
	curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($curl, CURLOPT_URL, $link);
	curl_setopt($curl, CURLOPT_HEADER, false);
	curl_setopt($curl, CURLOPT_COOKIEFILE, dirname(__FILE__) . "/cookie.txt");
	curl_setopt($curl, CURLOPT_COOKIEJAR, dirname(__FILE__) . "/cookie.txt");
	$out = curl_exec($curl);
	curl_close($curl);
	$result = json_decode($out, TRUE);
	return $result;
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
	curl_setopt($curl, CURLOPT_COOKIEFILE, dirname(__FILE__) . '/cookie.txt');
	curl_setopt($curl, CURLOPT_COOKIEJAR, dirname(__FILE__) . '/cookie.txt');
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
