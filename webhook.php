<?php
header("Content-type: text/html; charset=utf-8");
//ошибки
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

//подключение констант и классов
require 'constants.php';
require 'classes/Telegram.php';
require 'classes/KeyboardButton.php';
require 'classes/Object.php';
require 'classes/CrmFinder.php';
require 'classes/DatabaseConnection.php';
require 'classes/DatabaseManager.php';

$telegram = new Telegram();

$update = json_decode(file_get_contents("php://input"), JSON_OBJECT_AS_ARRAY);

//собираем данные из сообщения
$userMessage = $update['message']['text'];
$userName = $update['message']['from']['first_name'];
$chat_id = $update['message']['from']['id'];

//присылаем приветственное сообщение
if ($userMessage == "/start") {
	$text = "Для того, чтобы забронировать объект введите его адрес и цену. Указывайте тип объекта, если это не квартира!\n\nПримеры: Кораблейстроителей, 15, 2 ком, 45 м2, 10 эт, 6700\nДом, Лужайкина, 15, 100 м2, 4500\nУчасток, Мамадышский тракт, 38, 5 сот, 2200\nКоммерция, Техническая, 10, 12500";
    $telegram->sendRequest("sendMessage", ["chat_id" => $chat_id, "text" => $text]);
} else {
    //если пользователь прислал нам адрес
    $messageArray = explode(", ", $userMessage);
    $object = new Object($messageArray);
    $acceptButton = array("text" => "Да, верно", "callback_data" => $userMessage);
    $cancelButton = array("text" => "Нет, неверно", "callback_data" => "Неверно");
    $keyboard = [[$acceptButton], [$cancelButton]];
    $replyMarkup = json_encode(array('inline_keyboard' => $keyboard));
    $telegram->sendRequest("sendMessage", ["chat_id" => $chat_id, "text" => $object->getText(), "reply_markup" => $replyMarkup]);
}

//обрабатываем кнопки
if ($update['callback_query']['data'] == "Неверно") {
    $chat_id = $update['callback_query']['from']['id'];
    $text = "Пришлите адрес повторно. Разделяйте данные запятыми.";
    $telegram->sendRequest("sendMessage", ["chat_id" => $chat_id, "text" => $text]);
}
else {
    $chat_id = $update['callback_query']['from']['id'];
    $data = $update['callback_query']['data'];
    $dataMessage = explode(", ", $data);
    $object = new Object($dataMessage);
    $crmFinder = new CrmFinder();
    $database = new DatabaseManager('alexanb0_listing');

    //проверяем, есть ли объект в CRM
    if($crmFinder->check($object) == "Объекта нет в CRM") {
        $telegram->sendRequest("sendMessage", ["chat_id" => $chat_id, "text" => $database->check($object, $userName)]);
    }
    else {
        $telegram->sendRequest("sendMessage", ["chat_id" => $chat_id, "text" => $crmFinder->check($object)]);
    }
    
}
