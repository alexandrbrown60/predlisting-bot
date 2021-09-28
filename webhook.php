<?php
header("Content-type: text/html; charset=utf-8");
//ошибки
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

$ini = parse_ini_file('config.ini')
//подключение к Телеграм
const TOKEN = $ini['token'];
const BASE_URL = 'https://api.telegram.org/bot' . TOKEN . '/';

$update = json_decode(file_get_contents("php://input"), JSON_OBJECT_AS_ARRAY);
if(!$update) {
	print("Обновления не поступили");
} else {
	print_r($update);
}
//ответ в Телеграмм
function sendRequest($method, $params = []) {
    if(!empty($params)) {
        $url = BASE_URL . $method . '?' . http_build_query($params);
    }
    else {
        $url = BASE_URL . $method;
    }
    
    return json_decode(file_get_contents($url), JSON_OBJECT_AS_ARRAY);
}

//собираем данные из сообщения
$userMessage = $update['message']['text'];
$chat_id = $update['message']['from']['id'];
$text = "";

if ($userMessage == "/start") {
	$text = "Для того, чтобы забронировать объект введите его адрес и цену. Указывайте тип объекта, если это не квартира!\n\nПримеры: Кораблейстроителей, 15, 2 ком, 45 м2, 10 эт, 6700\nДом, Лужайкина, 15, 100 м2, 4500\nУчасток, Мамадышский тракт, 38, 5 сот, 2200\nКоммерция, Техническая, 10, 12500";

}
$messageArray = explode(", ", $userMessage);
if ($messageArray[0] == "Дом" || $messageArray[0] == "Участок") {
	sendRequest('sendMessage', ['chat_id' => $chat_id, 'text' => detectParameters(2, $messageArray)]);
}
elseif ($messageArray[0] == "Коммерция") {
	sendRequest('sendMessage', ['chat_id' => $chat_id, 'text' => detectParameters(3, $messageArray)]);
}
else {
	sendRequest('sendMessage', ['chat_id' => $chat_id, 'text' => detectParameters(1, $messageArray)]);
}

function detectParameters($type, $array) {
	$street = "";
	$house = "";
	$area = "";
	$rooms = "";
	$floor = "";
	$price = "";
	foreach ($array as $value) {
		if(strripos($value, "ком") !== false) {
			$rooms = trim(str_replace("ком", "", $value));
		}
		if(strripos($value, "м2") !== false || strripos($value, "квм") || strripos($value, "сот")) {
			$areaData = ["м2", "квм", "сот"];
			$area = trim(str_replace($areaData, "", $value));
		}
		if(strripos($value, "эт") !== false) {
			$floor = trim(str_replace("эт", "", $value));
		}
		if(is_numeric($value) && strlen($value) > 3) {
			$price = $value;
		}
	}

	if($type == 1) {
		$street = $array[0];
		$house = $array[1];
	} 
	else {
		$street = $array[1];
		$house = $array[2];
	}

	$result = "Вы ввели:\nТип объекта: $type, улица: $street, дом: $house, площадь: $area, этаж: $floor, цена: $price";
	return $result;
}
