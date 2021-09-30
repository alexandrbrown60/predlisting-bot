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
	$text = "Для того, чтобы забронировать объект введите его адрес и цену. Указывайте тип объекта, если это не квартира!\n\nПримеры: Кораблейстроителей, 15, 2 ком, 45 м2, 10 эт, 6700, Алиса\nДом, Лужайкина, 15, 100 м2, 4500, Айдар\nУчасток, Мамадышский тракт, 38, 5 сот, 2200, Гульназ\nКоммерция, Техническая, 10, 12500, Гульчачак";
    $telegram->sendRequest("sendMessage", ["chat_id" => $chat_id, "text" => $text]);
} else {
    //если пользователь прислал нам адрес
    $messageArray = explode(", ", $userMessage);
    $object = new Object($messageArray);

    //проверка ввода стоимости
    if($object->checkPrice() == false) {
        $telegram->sendRequest("sendMessage", ["chat_id" => $chat_id, "text" => "Вы не ввели стоимость объекта"]);
    }
    else {
        //проверяем на наличие в CRM или в БД
        $crmFinder = new CrmFinder();
        $database = new DatabaseManager('alexanb0_listing');
        $checkingResult = $crmFinder->check($object); 
        if($checkingResult == "Объекта нет в CRM") {
            $telegram->sendRequest("sendMessage", ["chat_id" => $chat_id, "text" => $database->check($object, $userName)]);
        }
        else {
            $telegram->sendRequest("sendMessage", ["chat_id" => $chat_id, "text" => $checkingResult]);
        }
    }
    

    
}

