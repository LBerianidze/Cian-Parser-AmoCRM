<?php
/**
 * Created by PhpStorm.
 * User: Лука
 * Date: 10.12.2019
 * Time: 8:26
 */
require_once 'DBWorker.php';
define('EARTH_RADIUS', 6372795);
$dbWorker = new DBWorker();
$deals = $dbWorker->getAllNotProcessedDeal();
if (time() - fileatime('cookie1.txt') > 82800)
{
	authorize();
}
$t = time();
$count = 0;
$db_all_ads = $dbWorker->getCianAllAds();
foreach ($deals as $item)
{
	if ($count > 25)
	{
		exit();
	}
	$id = $item['deal_id'];
	$request_id = $item['request_id'];
	$details = json_decode(getDealDetailsById($id), true);
	$result = array();
	foreach ($details['_embedded']['items'][0]['custom_fields'] as $custom_field)
	{
		$result[$custom_field['name']] = $custom_field['values'];
	}
	$success = true;
	if (isset($result['Широта']) && isset($result['Долгота']))
	{
		$lat = $result['Широта'][0]['value'];
		$lng = $result['Долгота'][0]['value'];
		$chosen_by_distance = array();
		foreach ($db_all_ads as $item)
		{
			$dist = calculateTheDistance($item['lat'], $item['lng'], $lat, $lng);
			if ($dist <= 100 && $item['ad_id'] != $id)
			{
				$it = array();
				$it[] = $item['ad_id'];
				$chosen_by_distance[] = $it;
			}
		}
		$duplicates_count = 0;
		if (isset($result['Количество дублей']))
		{
			$duplicates_count = $result['Количество дублей'][0]['value'];
		}
		$duplicated_ids = array();
		if (isset($result['ID дублей']))
		{
			//$duplicated_ids = explode(',', $result['ID дублей'][0]['value']);
		}
		$note = '';
		if (isset($result['Примечание По сделке']))
		{
			$note1 = $result['Примечание По сделке'][0]['value'];
		}
		foreach ($chosen_by_distance as $item) // where $item[0] is ad id
		{
			if (!in_array($item[0], $duplicated_ids))
			{
				$ad = $dbWorker->getCianAdMoreInfo($item[0]);
				if ($result['Комнат'][0]['value'] == $ad['rooms'])
				{
					if ($result['Этаж'][0]['value'] == $ad['floor'])
					{
						$area = $result['Общая площадь'][0]['value'];
						$areap = $ad['area'] * 1.1;
						$aream = $ad['area'] * 0.9;
						if ($area >= $aream && $area <= $areap)
						{
							$note .= $ad['floor'] . '/' . $ad['floors'] . ', ' . $ad['area'] . ' м2, ' . $ad['price'] . 'р., ' . ceil($ad['price'] / $ad['area']) . 'р.\\м2, '.$ad['announcer']."\r\n" . $ad['url'] . "\r\n" ;
							$duplicates_count++;
							$duplicated_ids[] = $item[0];
						}

					}
				}
			}
		}
		updateDeal($id, $duplicates_count, $note, implode(',', $duplicated_ids));
	}
	else
	{
		$url = $result['URL inpars'][0]['value'];
		if (strrpos($url, 'cian') !== false)
		{
			$matches = array();
			preg_match('/(\d*)$/m', $url, $matches);

			$ad1 = $dbWorker->getCianAd($matches[0]);
			if ($ad1)
			{
				$lat = $ad1['lat'];
				$lng = $ad1['lng'];
				$chosen_by_distance = array();
				foreach ($db_all_ads as $item)
				{
					$dist = calculateTheDistance($item['lat'], $item['lng'], $lat, $lng);
					if ($dist <= 100 && $item['ad_id'] != $matches[0])
					{
						$it = array();
						$it[] = $item['ad_id'];
						$chosen_by_distance[] = $it;
					}
				}
				$duplicates_count = 0;
				if (isset($result['Количество дублей']))
				{
					$duplicates_count = $result['Количество дублей'][0]['value'];
				}
				$duplicated_ids = array();
				if (isset($result['ID дублей']))
				{
					$duplicated_ids = explode(',', $result['ID дублей'][0]['value']);
				}
				$note = '';
				if (isset($result['Примечание По сделке']))
				{
					$note = $result['Примечание По сделке'][0]['value'];
				}
				foreach ($chosen_by_distance as $item) // where $item[0] is ad id
				{
					if (!in_array($item[0], $duplicated_ids))
					{
						$ad = $dbWorker->getCianAdMoreInfo($item[0]);
						if ($result['Комнат'][0]['value'] == $ad['rooms'])
						{
							if ($result['Этаж'][0]['value'] == $ad['floor'])
							{
								$area = $result['Общая площадь'][0]['value'];
								$areap = $ad['area'] * 1.1;
								$aream = $ad['area'] * 0.9;
								if ($area >= $aream && $area <= $areap)
								{
									$note .= $ad['floor'] . '/' . $ad['floors'] . ', ' . $ad['area'] . ' м2, ' . $ad['price'] . 'р., ' . ceil($ad['price'] / $ad['area']) . 'р.\\м2, '.$ad['announcer']."\r\n" . $ad['url'] . "\r\n" ;
									$duplicates_count++;
									$duplicated_ids[] = $item[0];
								}

							}
						}
					}
				}
				updateDeal($id, $duplicates_count, $note, implode(',', $duplicated_ids));
			}
			else
			{
				$dbWorker->updateDeal($request_id, 2);
				$success = false;
			}
		}
		else
		{
			$dbWorker->updateDeal($request_id, 2);
			$success = false;
		}
	}
	if ($success)
	{
		$dbWorker->updateDeal($request_id, 1);
	}
	sleep(1);
	$count++;
}
function updateDeal($id, $duplicatesCount, $extra, $duplids)
{
	$leads['update'] = array(array('id' => $id, 'updated_at' => time(), 'custom_fields' => array(array('id' => 2015899, 'values' => array(array('value' => $duplicatesCount))), array('id' => 2015901, 'values' => array(array('value' => (new DateTime())->format('Y-m-d')))), array('id' => 2016099, 'values' => array(array('value' => $extra))), array('id' => 2016059, 'values' => array(array('value' => $duplids))))));
	$subdomain = 'zavidov';
	$link = 'https://' . $subdomain . '.amocrm.ru/api/v2/leads';
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_USERAGENT, 'amoCRM-API-client/1.0');
	curl_setopt($curl, CURLOPT_URL, $link);
	curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
	curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($leads));
	curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	curl_setopt($curl, CURLOPT_HEADER, false);
	curl_setopt($curl, CURLOPT_COOKIEFILE, dirname(__FILE__) . '/cookie1.txt');
	curl_setopt($curl, CURLOPT_COOKIEJAR, dirname(__FILE__) . '/cookie1.txt');
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
	$out = curl_exec($curl);
	$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	$code = (int)$code;
	var_dump($out);
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
}

function getDealDetailsById($id)
{
	$subdomain = 'zavidov';
	$link = 'https://' . $subdomain . '.amocrm.ru/api/v2/leads';
	$link = 'https://' . $subdomain . '.amocrm.ru/api/v2/leads?limit_rows=50';
	$link = 'https://' . $subdomain . '.amocrm.ru/api/v2/leads?limit_rows=50&limit_offset=2';
	$link = 'https://' . $subdomain . '.amocrm.ru/api/v2/leads?id=' . $id;
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_USERAGENT, 'amoCRM-API-client/1.0');
	curl_setopt($curl, CURLOPT_URL, $link);
	curl_setopt($curl, CURLOPT_HEADER, false);
	curl_setopt($curl, CURLOPT_COOKIEFILE, dirname(__FILE__) . '/cookie1.txt');
	curl_setopt($curl, CURLOPT_COOKIEJAR, dirname(__FILE__) . '/cookie1.txt');
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

	return $out;
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
	curl_setopt($curl, CURLOPT_COOKIEFILE, dirname(__FILE__) . '/cookie1.txt');
	curl_setopt($curl, CURLOPT_COOKIEJAR, dirname(__FILE__) . '/cookie1.txt');
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