<?php
require_once 'DBWorker.php';
$dbWorker = new DBWorker();
if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
	$id = $_POST['leads']['status'][0]['id'];
	$dbWorker->insertAmoRequest($id);
}
else
{
	$id = 28294541;
	$dbWorker->insertAmoRequest($id);
}
file_put_contents('please.txt',"123");