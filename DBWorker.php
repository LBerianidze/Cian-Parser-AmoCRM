<?php
/**
 * Created by PhpStorm.
 * User: Лука
 * Date: 06.12.2019
 * Time: 0:35
 */

class DBWorker
{
	var $db_host = "localhost";
	var $userName = "vh258879_cian";
	var $dbName = "vh258879_cian";
	var $password = "S0y0T7c6";
	var $db_con = null;

	public function __construct()
	{

		try
		{
			$this->db_con = new PDO("mysql:host={$this->db_host};dbname={$this->dbName}", $this->userName, $this->password);
			$this->db_con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->db_con->exec("set names utf8");
		}
		catch (PDOException $e)
		{
			echo $e->getMessage();
		}
	}
	public function updateAvitoAdPostFix($id, $postfix)
	{
		$st = $this->db_con->prepare('UPDATE `avito_ad` SET `postfix`=:param2 WHERE `ad_id`=:param1');
		$st->execute(array(':param1' => $id, ':param2' => $postfix));
	}
	public function getAvitoFullAds()
	{
		$st = $this->db_con->query("select ad_id,postfix from `avito_ad`");
		$st->execute();
		$result = $st->fetchAll();
		return $result;
	}
	public function getCianAllAds()
	{
		$st = $this->db_con->query("select ad_id,lat,lng from `cian_ad` ORDER BY `price` ASC");
		$st->execute();
		$result = $st->fetchAll();
		return $result;
	}

	public function getCianAd($id)
	{
		$st = $this->db_con->prepare('Select updated,price,phone,update_info,duplicate_count,lat,lng from `cian_ad` where `ad_id`=:param1');
		$st->execute(array(':param1' => $id));
		$result = $st->fetchAll();
		if (count($result) == 0)
		{
			return false;
		}
		return $result[0];
	}

	public function getAvitoAd($id)
	{
		$st = $this->db_con->prepare('Select time,price,phone,update_info,duplicate_count,lat,lng from `avito_ad` where `avito_id`=:param1');
		$st->execute(array(':param1' => $id));
		$result = $st->fetchAll();
		if (count($result) == 0)
		{
			return false;
		}
		return $result[0];
	}

	public function getCianAdMoreInfo($id)
	{
		$st = $this->db_con->prepare('Select ad_id,rooms,price,area,url,announcer,floor,floors from `cian_ad` where `ad_id`=:param1');
		$st->execute(array(':param1' => $id));
		$result = $st->fetchAll();
		if (count($result) == 0)
		{
			return false;
		}
		return $result[0];
	}

	public function getCianAdFull($id)
	{
		$st = $this->db_con->prepare('Select * from `cian_ad` where `ad_id`=:param1');
		$st->execute(array(':param1' => $id));
		$result = $st->fetchAll();
		if (count($result) == 0)
		{
			return false;
		}
		return $result[0];
	}

	public function deleteCianAd($id)
	{
		$st = $this->db_con->prepare('Delete  from `cian_ad` where `ad_id`=:param1');
		$st->execute(array(':param1' => $id));
	}

	public function deleteAvitoAd($id)
	{
		$st = $this->db_con->prepare('Delete  from `avito_ad` where `avito_id`=:param1');
		$st->execute(array(':param1' => $id));
	}

	public function insertCianAd($values)
	{
		$st = $this->db_con->prepare('INSERT INTO `cian_ad`(`ad_id`, `url`, `price`, `created`, `updated`, `phone`, `announcer`, `region`, `city`,`address`, `metro`, `rooms`, `floor`, `floors`, `area`, `kitchen_area`, `living_area`, `land_area`, `year`, `deal_type`, `images`, `description`,`category`, `subcategory`, `category_id`, `region_id`, `city_id`, `lat`, `lng`, `price_per_month`,`publisher_id`,`cian_user_id`,`building_type`, `update_info`,`duplicate_count`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
		$st->execute($values);
	}
	public function insertAvitoAd($values)
	{
		$st = $this->db_con->prepare('INSERT INTO `avito_ad`(`ad_id`, `avito_id`, `title`, `url`, `price`, `time`,`operator`, `phone`, `name`, `region`, `city`, `district`, `address`, `metro`, `rooms`, `floor`, `floors`, `area`, `kitchen_area`, `living_area`, `land_area`, `year`, `deal_type`, `images`, `description`, `category`, `category_id`, `region_id`, `city_id`, `lat`, `lng`, `postfix`,`images_big`, `update_info`, `duplicate_count`,`building_type`,`params`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
		$st->execute($values);
	}
	public function insertAmoRequest($id)
	{
		$st = $this->db_con->prepare('INSERT INTO `amo_request`(`deal_id`) VALUES (?)');
		$st->execute(array($id));
	}

	public function insertAmoCianCreatedDeal($id)
	{
		$st = $this->db_con->prepare('INSERT INTO `created_deal`(`cian_id`,`deal_create_time`) VALUES (?,?)');
		$st->execute(array($id, (new DateTime('now'))->format('Y-m-d H:i:s')));
	}
	public function insertAmoAvitoCreatedDeal($id)
	{
		$st = $this->db_con->prepare('INSERT INTO `avito_created_deal`(`cian_id`,`deal_create_time`) VALUES (?,?)');
		$st->execute(array($id, (new DateTime('now'))->format('Y-m-d H:i:s')));
	}
	public function insertCianAdCreateInfo($id, $existed)
	{
		$st = $this->db_con->prepare('INSERT INTO `cian_ad_statistic`(`ad_id`,`date`,`existed`) VALUES (?,?,?)');
		$st->execute(array($id, (new DateTime('now'))->format('Y-m-d H:i:s'), $existed));
	}
	public function insertAvitoAdCreateInfo($id, $existed)
	{
		$st = $this->db_con->prepare('INSERT INTO `avito_ad_statistic`(`ad_id`,`date`,`existed`) VALUES (?,?,?)');
		$st->execute(array($id, (new DateTime('now'))->format('Y-m-d H:i:s'), $existed));
	}
	public function getAllNotProcessedDeal()
	{
		$st = $this->db_con->query("select request_id,deal_id from amo_request where processed=0");
		$st->execute();
		$result = $st->fetchAll();
		return $result;
	}

	public function getTenDealUpThenId($id)
	{
		$st = $this->db_con->prepare("select * from `cian_ad` where `ad_id` > :param1 and `announcer`='собственник' LIMIT 10");
		$st->execute(array(':param1' => $id));
		$result = $st->fetchAll();
		return $result;
	}
	public function getAvitoTenDealUpThenId($id)
	{
		$st = $this->db_con->prepare("select * from `avito_ad` where `ad_id` > :param1 and `postfix`='Частное лицо' LIMIT 100");
		$st->execute(array(':param1' => $id));
		$result = $st->fetchAll();
		return $result;
	}

	public function getAllWithEmptyBuildingType()
	{
		$st = $this->db_con->prepare("select * from `cian_ad` where  isnull( `cian_user_id`) limit 1000");
		$st->execute();
		$result = $st->fetchAll();
		return $result;
	}

	public function getAmoDeals()
	{
		$st = $this->db_con->prepare("select * from `amocrm_deal` where  `active`=0 and `update_time`>1578577066 limit 10");
		$st->execute();
		$result = $st->fetchAll();
		return $result;
	}
public function updateCrmDealActiveStatus($id,$val)
{
	$st = $this->db_con->prepare('UPDATE `amocrm_deal` SET `active`=:param1 WHERE `deal_index`=:param2');
	$st->execute(array(':param1' => $val, ':param2' => $id));
}
	public function updateDeal($id, $val)
	{
		$st = $this->db_con->prepare('UPDATE `amo_request` SET `processed`=:param1 WHERE `request_id`=:param2');
		$st->execute(array(':param1' => $val, ':param2' => $id));
	}

	public function updateCrmDeal($id, $val)
	{
		$st = $this->db_con->prepare('UPDATE `amocrm_deal` SET `status`=:param1 WHERE `deal_index`=:param2');
		$st->execute(array(':param1' => $val, ':param2' => $id));
	}

	public function updateAd($id, $publisher, $user, $type)
	{
		$st = $this->db_con->prepare('UPDATE `cian_ad` SET `publisher_id`=:param1,`cian_user_id`=:param2,`building_type`=:param3 WHERE `ad_id`=:param4');
		$st->execute(array(':param1' => $publisher, ':param2' => $user, ':param3' => $type, ':param4' => $id));
	}

	public function getMaxCreatedDealCianID()
	{
		$request = $this->db_con->prepare('SELECT max(cian_id) as maximum FROM `created_deal`');
		$request->execute();
		$result = $request->fetchAll(PDO::FETCH_OBJ);
		$result = $result[0]->maximum;
		return $result;
	}
	public function getAvitoMaxCreatedDealCianID()
	{
		$request = $this->db_con->prepare('SELECT max(cian_id) as maximum FROM `avito_created_deal`');
		$request->execute();
		$result = $request->fetchAll(PDO::FETCH_OBJ);
		$result = $result[0]->maximum;

		return $result !=null?$result : 0;
	}
	public function getCianAdsCountByDay($dt,$existed)
	{
		$request = $this->db_con->prepare('SELECT count(*) as count FROM `cian_ad_statistic` WHERE Date(`date`) = :param1 and `existed`=:param2');
		$request->execute(array(':param1' => $dt,':param2'=>$existed));
		$result = $request->fetchAll(PDO::FETCH_OBJ);
		$result = $result[0]->count;
		return $result;
	}
	public function getAvitoAdsCountByDay($dt,$existed)
	{
		$request = $this->db_con->prepare('SELECT count(*) as count FROM `avito_ad_statistic` WHERE Date(`date`) = :param1 and `existed`=:param2');
		$request->execute(array(':param1' => $dt,':param2'=>$existed));
		$result = $request->fetchAll(PDO::FETCH_OBJ);
		$result = $result[0]->count;
		return $result;
	}
	public function insertDealToFullListTable($id, $updateTime, $url, $price, $lat, $lng, $rooms, $floor, $floors, $area)
	{
		$st = $this->db_con->prepare('INSERT INTO `amocrm_deal`(`deal_id`,`update_time`,`url`,`price`,`lat`,`lng`,`rooms`,`floor`,`floors`,`area`) VALUES (?,?,?,?,?,?,?,?,?,?)');
		$st->execute(array($id, $updateTime, $url, $price, $lat, $lng, $rooms, $floor, $floors, $area));
	}

	public function checkIfDealExists($id)
	{
		$st = $this->db_con->prepare('Select * from `amocrm_deal` where `deal_id`=:param1');
		$st->execute(array(':param1' => $id));
		$result = $st->fetchAll();
		if (count($result) == 0)
		{
			return false;
		}
		return true;
	}

	public function getDealsMaxUpdateTime()
	{
		$request = $this->db_con->prepare('SELECT COALESCE(max(`update_time`),1) as maximum FROM `amocrm_deal`');
		$request->execute();
		$result = $request->fetchAll(PDO::FETCH_OBJ);
		$result = $result[0]->maximum;
		return $result;
	}
}