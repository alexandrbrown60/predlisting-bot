<?php
header("Content-type: text/html; charset=utf-8");
//ошибки
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

//подключение констант и классов
require 'constants.php';
require 'classes/Object.php';
require 'classes/CrmFinder.php';

$update = json_decode(file_get_contents("php://input"), JSON_OBJECT_AS_ARRAY);

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
$object = new Object($messageArray);
$crmFinder = new CrmFinder();
sendRequest("sendMessage", ["chat_id" => $chat_id, "text" => $object->getText()]);
sendRequest("sendMessage", ["chat_id" => $chat_id, "text" => $crmFinder->check($object)]);



// function checkInDatabase() {

// }
