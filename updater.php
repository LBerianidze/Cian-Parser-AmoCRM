<?php
/**
 * Created by PhpStorm.
 * User: Лука
 * Date: 24.12.2019
 * Time: 22:44
 */
require_once 'DBWorker.php';
$dbWorker = new DBWorker();
$deals = $dbWorker->getAllWithEmptyBuildingType();
foreach ($deals as $deal)
{
	$id = $deal['ad_id'];
	$login = 'teo@vedd.ru';
	$token = '40e567394028867eea5d057c7ae729bc';
	$url = "https://rest-app.net/api-cian/ad?login=" . urlencode($login) . "&token=" . urlencode($token) . '&id=' . $id;
	$str = file_get_contents($url);
	$json = json_decode($str);
	$published_user_id = $json->data[0]->published_user_id;
	$cian_user_id = $json->data[0]->cian_user_id;
	$building_material_type = isset($json->data[0]->building_material_type) ? $json->data[0]->building_material_type : '';
	$dbWorker->updateAd($id,$published_user_id,$cian_user_id,$building_material_type);
	echo $id . '<br/>';
}