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

$url = "https://rest-app.net/api-cian/ads?region_id=1&login=" . urlencode($login) . "&token=" . urlencode($token) . '&category_id=1&deal_id=1&price1=8000000&date1=' . urlencode($dtString) . '&date2=' . urlencode($dtString1);
echo $url;
$str = file_get_contents($url);
$json = json_decode($str);
foreach ($json->data as $ad)
{
	$ad_id = $ad->Id;
	$dbAd = $dbWorker->getCianAd($ad_id);
	if ($dbAd)
	{
		$dbWorker->deleteCianAd($ad_id);
		$update_info = $dbAd['update_info'];
		$duplicatesCount = $dbAd['duplicate_count'] + 1;
		if ($ad->price != $dbAd['price'] || $dbAd['phone'] != $ad->phone)
		{
			$update_info .= $dbAd['updated'] . '_' . $dbAd['price'] . '_' . $dbAd['phone'] . ';';
		}
		$values = array();
		$values[] = $ad->Id;
		$values[] = $ad->url;
		$values[] = $ad->price;
		$values[] = $ad->time_publish;
		$values[] = $ad->time;
		$values[] = $ad->phone;
		$values[] = $ad->person_type;
		$values[] = $ad->region;
		$values[] = $ad->city;
		$values[] = $ad->address;
		$values[] = $ad->metro;
		$values[] = $ad->rooms_count;
		$values[] = $ad->floor_number;
		$values[] = $ad->floors_count;
		$values[] = $ad->area;
		$values[] = $ad->area_kitchen;
		$values[] = $ad->area_living;
		$values[] = $ad->area_land;
		$values[] = $ad->building_year;
		$values[] = $ad->deal_type;
		$values[] = $ad->images;
		$values[] = $ad->description;
		$values[] = $ad->category;
		$values[] = $ad->subcategory;
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
		$values[] = isset($ad->price_per_month) ? $ad->price_per_month : 0;
		$values[] = $ad->published_user_id;
		$values[] = $ad->cian_user_id;
		$values[] =  isset($ad->building_material_type) ? $ad->building_material_type : '';
		$values[] = $update_info;
		$values[] = $duplicatesCount;
		$dbWorker->insertCianAd($values);
		$dbWorker->insertCianAdCreateInfo($ad_id,true);
	}
	else
	{
		$values = array();
		$values[] = $ad->Id;
		$values[] = $ad->url;
		$values[] = $ad->price;
		$values[] = $ad->time_publish;
		$values[] = $ad->time;
		$values[] = $ad->phone;
		$values[] = $ad->person_type;
		$values[] = $ad->region;
		$values[] = $ad->city;
		$values[] = $ad->address;
		$values[] = $ad->metro;
		$values[] = $ad->rooms_count;
		$values[] = $ad->floor_number;
		$values[] = $ad->floors_count;
		$values[] = $ad->area;
		$values[] = $ad->area_kitchen;
		$values[] = $ad->area_living;
		$values[] = $ad->area_land;
		$values[] = $ad->building_year;
		$values[] = $ad->deal_type;
		$values[] = $ad->images;
		$values[] = $ad->description;
		$values[] = $ad->category;
		$values[] = $ad->subcategory;
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
		$values[] = isset($ad->price_per_month) ? $ad->price_per_month : 0;
		$values[] = $ad->published_user_id;
		$values[] = $ad->cian_user_id;
		$values[] = isset($ad->building_material_type) ? $ad->building_material_type : '';
		$values[] = '';
		$values[] = 0;
		$dbWorker->insertCianAd($values);
		$dbWorker->insertCianAdCreateInfo($ad_id,false);
	}
}


$today = new DateTime($dt->format('Y-m-d'));
$today->add(new DateInterval('PT23H'));
$dtString2 = $today->format('Y-m-d H:i:s');
file_put_contents("dt.txt",$url.'   '.$dtString2);