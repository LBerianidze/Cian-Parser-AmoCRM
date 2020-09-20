<?php
/**
 * Created by PhpStorm.
 * User: Лука
 * Date: 29.01.2020
 * Time: 13:30
 */
include 'DBWorker.php';
$dbWorker = new DBWorker();
$handle = fopen("new 335.txt", "r");

while (($line = fgets($handle)) !== false) {
	$dbWorker->updateAvitoAdPostFix($line,'Частное лицо');
}