<?php
/**
 * Created by PhpStorm.
 * User: Лука
 * Date: 06.12.2019
 * Time: 20:44
 */
require_once 'DBWorker.php';
$dt = new DateTime('now');
$dt1 = new DateTime('now');
$dt1->sub(new DateInterval('PT5M'));

$dtString = $dt1->format('Y-m-d H:i:s');
$dtString1 = $dt->format('Y-m-d H:i:s');

$dbWorker = new DBWorker();

$login = 'teo@vedd.ru';
$token = '40e567394028867eea5d057c7ae729bc';

$url = "https://rest-app.net/api/ads?region_id=637640&login=" . urlencode($login) . "&token=" . urlencode($token) . '&category_id=24&price1=8000000&date1=' . urlencode($dtString) . '&date2=' . urlencode($dtString1);
echo $url;
$str = file_get_contents($url);
$json = json_decode($str);
/**
 * @param $ad
 * @param array $extraParameters
 */
function getParametersArray($ad, array &$extraParameters)
{
	foreach ($ad->params as $item)
	{
		if (isset($extraParameters[$item->name]))
		{
			$extraParameters[$item->name . '2'] = $item->value;
		}
		else
		{
			$extraParameters[$item->name] = $item->value;
		}
	}
}

foreach ($json->data as $ad)
{
	$ad_id = $ad->avito_id;
	$dbAd = $dbWorker->getAvitoAd($ad_id);
	if ($dbAd)
	{
		$dbWorker->deleteAvitoAd($ad_id);
		$update_info = $dbAd['update_info'];
		$duplicatesCount = $dbAd['duplicate_count'] + 1;
		if ($ad->price != $dbAd['price'] || $dbAd['phone'] != $ad->phone)
		{
			$update_info .= $dbAd['time'] . '_' . $dbAd['price'] . '_' . $dbAd['phone'] . ';';
		}
		$values = array();
		$extraParameters = array();
		getParametersArray($ad, $extraParameters);
		$values[] = $ad->Id;
		$values[] = $ad->avito_id;
		$values[] = $ad->title;
		$values[] = $ad->url;
		$values[] = $ad->price;
		$values[] = $ad->time;
		$values[] = $ad->operator;
		$values[] = $ad->phone;
		$values[] = $ad->name;
		$values[] = $ad->region;
		$values[] = $ad->city;
		$values[] = $ad->district;
		$values[] = $ad->address;
		$values[] = $ad->metro;
		$values[] = isset($extraParameters['Количество комнат']) ? $extraParameters['Количество комнат'] : '';
		$values[] = isset($extraParameters['Этаж']) ? $extraParameters['Этаж'] : '';
		$values[] = isset($extraParameters['Этажей в доме']) ? $extraParameters['Этажей в доме'] : '';
		$values[] = isset($extraParameters['Общая площадь, м²']) ? $extraParameters['Общая площадь, м²'] : '';
		$values[] = isset($extraParameters['Площадь кухни, м²']) ? $extraParameters['Площадь кухни, м²'] : '';
		$values[] = isset($extraParameters['Жилая площадь, м²']) ? $extraParameters['Жилая площадь, м²'] : '';
		$values[] = 0;
		$values[] = 0;
		$values[] = isset($extraParameters['Тип объявления']) ? $extraParameters['Тип объявления'] : '';
		$values[] = $ad->images;
		$values[] = $ad->description;
		$values[] = isset($extraParameters['Категория']) ? $extraParameters['Категория'] : '';

		$values[] = $ad->category_Id;
		$values[] = $ad->region_Id;
		$values[] = $ad->city_Id;
		if ($ad->coords)
		{
			$values[] = $ad->coords->lat;
			$values[] = $ad->coords->lng;
		}
		else
		{
			$values[] = null;
			$values[] = null;
		}
		$values[] = $ad->postfix;
		$values[] = $ad->images_big;
		$values[] = $update_info;
		$values[] = $duplicatesCount;
		$values[] = isset($extraParameters['Тип дома']) ? $extraParameters['Тип дома'] : '';
		$values[] = json_encode(($ad->params));
		$dbWorker->insertAvitoAd($values);
		$dbWorker->insertAvitoAdCreateInfo($ad_id,true);

	}
	else
	{
		$values = array();
		$extraParameters = array();
		getParametersArray($ad, $extraParameters);
		$values[] = $ad->Id;
		$values[] = $ad->avito_id;
		$values[] = $ad->title;
		$values[] = $ad->url;
		$values[] = $ad->price;
		$values[] = $ad->time;
		$values[] = $ad->operator;
		$values[] = $ad->phone;
		$values[] = $ad->name;
		$values[] = $ad->region;
		$values[] = $ad->city;
		$values[] = $ad->district;
		$values[] = $ad->address;
		$values[] = $ad->metro;
		$values[] = isset($extraParameters['Количество комнат']) ? $extraParameters['Количество комнат'] : '';
		$values[] = isset($extraParameters['Этаж']) ? $extraParameters['Этаж'] : '';
		$values[] = isset($extraParameters['Этажей в доме']) ? $extraParameters['Этажей в доме'] : '';
		$values[] = isset($extraParameters['Общая площадь, м²']) ? $extraParameters['Общая площадь, м²'] : '';
		$values[] = isset($extraParameters['Площадь кухни, м²']) ? $extraParameters['Площадь кухни, м²'] : '';
		$values[] = isset($extraParameters['Жилая площадь, м²']) ? $extraParameters['Жилая площадь, м²'] : '';
		$values[] = 0;
		$values[] = 0;
		$values[] = isset($extraParameters['Тип объявления']) ? $extraParameters['Тип объявления'] : '';
		$values[] = $ad->images;
		$values[] = $ad->description;
		$values[] = isset($extraParameters['Категория']) ? $extraParameters['Категория'] : '';

		$values[] = $ad->category_Id;
		$values[] = $ad->region_Id;
		$values[] = $ad->city_Id;
		if ($ad->coords)
		{
			$values[] = $ad->coords->lat;
			$values[] = $ad->coords->lng;
		}
		else
		{
			$values[] = null;
			$values[] = null;
		}
		$values[] = $ad->postfix;
		$str = json_encode(  $ad->postfix,JSON_UNESCAPED_UNICODE);
		echo $str;
		$values[] = $ad->images_big;
		$values[] = '';
		$values[] = 0;
		$values[] = isset($extraParameters['Тип дома']) ? $extraParameters['Тип дома'] : '';
		$values[] = json_encode(($ad->params));
		$dbWorker->insertAvitoAd($values);
		$dbWorker->insertAvitoAdCreateInfo($ad_id,false);
	}
}


$today = new DateTime($dt->format('Y-m-d'));
$today->add(new DateInterval('PT23H'));
$dtString2 = $today->format('Y-m-d H:i:s');
file_put_contents("dt.txt", $url . '   ' . $dtString2);