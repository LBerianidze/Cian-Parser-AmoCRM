<?php
/**
 * Created by PhpStorm.
 * User: Лука
 * Date: 15.12.2019
 * Time: 15:39
 */
require_once 'DBWorker.php';
define('EARTH_RADIUS', 6372795);
$dbWorker = new DBWorker();
$maxCianId = $dbWorker->getAvitoMaxCreatedDealCianID();
$deals = $dbWorker->getAvitoTenDealUpThenId($maxCianId);
foreach ($deals as $deal)
{
	$dbWorker->insertAmoAvitoCreatedDeal($deal['ad_id']);
	createDeal($deal['address'],$deal['price'],$deal['rooms'],$deal['metro'],$deal['area'],$deal['kitchen_area'],$deal['living_area'],$deal['floor'],$deal['floors'],$deal['region'],$deal['lat'],$deal['lng'],$deal['description'],$deal['phone'],$deal['url']);
	//createDeal($deal['address'],$deal['price'],$deal['rooms'],$deal['metro'],$deal['area'],$deal['kitchen_area'],$deal['living_area'],$deal['floor'],$deal['floors'],$deal['city'],$deal['lat'],$deal['lng'],$deal['description'],$deal['phone'],$deal['url']);
    sleep(1);
}
function escapeCity($source)
{
	$source = str_replace('Москва г.,','',$source);
	$source = str_replace('г Москва.','',$source);
	$source = str_replace('Москва г,','',$source);
	$source = str_replace('г Москва,','',$source);
	$source = str_replace('г. Москва,','',$source);

	$source = str_replace('Москва,','',$source);
	$source = str_replace('Россия,','',$source);
	$source = str_replace('Москва ','',$source);
	$source = str_replace('Москва.','',$source);
	$source = str_replace('москва,','',$source);

	return trim( $source,"\t\n\r\0\x0B,.");
}

function createDeal($address,$price,$rooms,$metro,$area,$kitchen_area,$living_area, $floor, $floors, $city, $lat, $lng, $description, $phone,$url)
{
	$data = array (
		'add' =>
			array (
				0 =>
					array (
						'source_name' => 'Parser',
						'source_uid' => '1',
						'created_at' => time(),
						'incoming_entities' =>
							array (
								'leads' =>
									array (
										0 =>
											array (
												'name' => escapeCity($address),
												'custom_fields' =>
													array (
														0 =>
															array (
																'id' => '1582680',
																'values' =>
																	array (
																		0 =>
																			array (
																				'value' => $price,
																			),
																	),
															),
														1 =>
															array (
																'id' => '1582682',
																'values' =>
																	array (
																		0 =>
																			array (
																				'value' => $rooms,
																			),
																	),
															),
														2 =>
															array (
																'id' => '1582684',
																'values' =>
																	array (
																		0 =>
																			array (
																				'value' => explode(',',$metro)[0],
																			),
																	),
															),
														3 =>
															array (
																'id' => '1582688',
																'values' =>
																	array (
																		0 =>
																			array (
																				'value' => '4644061',
																			),
																	),
															),
														4 =>
															array (
																'id' => '1582702',
																'values' =>
																	array (
																		0 =>
																			array (
																				'value' => $area,
																			),
																	),
															),
														5 =>
															array (
																'id' => '1582704',
																'values' =>
																	array (
																		0 =>
																			array (
																				'value' => $kitchen_area,
																			),
																	),
															),
														6 =>
															array (
																'id' => '1582706',
																'values' =>
																	array (
																		0 =>
																			array (
																				'value' => $living_area,
																			),
																	),
															),
														7 =>
															array (
																'id' => '1923278',
																'values' =>
																	array (
																		0 =>
																			array (
																				'value' => $address,
																			),
																	),
															),
														8 =>
															array (
																'id' => '1582708',
																'values' =>
																	array (
																		0 =>
																			array (
																				'value' => $floor,
																			),
																	),
															),
														9 =>
															array (
																'id' => '1582710',
																'values' =>
																	array (
																		0 =>
																			array (
																				'value' => $floors,
																			),
																	),
															),
														10 =>
															array (
																'id' => '1582730',
																'values' =>
																	array (
																		0 =>
																			array (
																				'value' => $city,
																			),
																	),
															),
														11 =>
															array (
																'id' => '2004177',
																'values' =>
																	array (
																		0 =>
																			array (
																				'value' => $lat,
																			),
																	),
															),
														12 =>
															array (
																'id' => '2004179',
																'values' =>
																	array (
																		0 =>
																			array (
																				'value' => $lng,
																			),
																	),
															),
														13 =>
															array (
																'id' => '2001247',
																'values' =>
																	array (
																		0 =>
																			array (
																				'value' => $description,
																			),
																	),
															),
														14 =>
															array (
																'id' => '2009773',
																'values' =>
																	array (
																		0 =>
																			array (
																				'value' => $url,
																			),
																	),
															),
													),

												'sale' => $price*0.03,
												'tags' => 'ТОП1,AVITO',
											),
									),
								'contacts' =>
									array (
										0 =>
											array (
												'name' => 'Аноним',
												'custom_fields' =>
													array (
														0 =>
															array (
																'id' => '1582556',
																'values' =>
																	array (
																		0 =>
																			array (
																				'value' => $phone,
																				'enum' => 'WORK',
																			),
																	),
															),
													),
											),
									),
							),
						'incoming_lead_info' =>
							array (
								'form_id' => '1',
								'form_page' => 'avito.ru',
								'ip' => '185.154.54.3',
								'service_code' => 'Luka',
							),
					),
			),
	);
	$link = "https://zavidov.amocrm.ru/api/v2/incoming_leads/form?login=igor.knyazev@zavidov.realty&api_key=cf6c680ac1727c834d78f2978524dd6e5aa53bfa&";

	$headers[] = "Accept: application/json";

	//Curl options
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
	curl_setopt($curl, CURLOPT_USERAGENT, "amoCRM-API-client-
undefined/2.0");
	curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
	curl_setopt($curl, CURLOPT_URL, $link);
	curl_setopt($curl, CURLOPT_HEADER,false);
	$out = curl_exec($curl);
	curl_close($curl);
	$result = json_decode($out,TRUE);
	var_dump($result);
}