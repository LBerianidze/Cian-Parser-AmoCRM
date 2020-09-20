<?php
require_once 'DBWorker.php';
$dt = new DateTime('now');
$dt->sub(new DateInterval('P1D'));
$dt = $dt->format('yy-m-d');
$dbWorker = new DBWorker();
$cian = $dbWorker->getCianAdsCountByDay($dt, 1);
$cian1 = $dbWorker->getCianAdsCountByDay($dt, 0);
$avito = $dbWorker->getAvitoAdsCountByDay($dt, 1);
$avito1 = $dbWorker->getAvitoAdsCountByDay($dt, 0);
$cianTotal = $cian + $cian1;
$avitoTotal = $avito + $avito1;
$left = 10000 - $cianTotal - $avitoTotal;
$left = $left < 0 ? 0 : $left;
$to = "Tieo@mail.ru,berianidze_luka@mail.ru";
$subject = "Отчет парсинга за " . $dt;

$message = "
<html>
<head>
<title>Информация о парсинге за $dt</title>
</head>
<body>
<p>Количество спарсенных объявлений с cian: $cianTotal</p>
<p>Из них новых: $cian1,существующих: $cian</p>
<p>Количество спарсенных объявлений с avito: $avitoTotal</p>
<p>Из них новых: $avito1,существующих: $avito</p>
<p>Количество спарсенных объявлений с cian за прошлое: $left </p>
</body>
</html>
";

// Always set content-type when sending HTML email
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

$headers .= 'From: <cian@vh258879.eurodir.ru>' . "\r\n";
$result = mail($to, $subject, $message, $headers);