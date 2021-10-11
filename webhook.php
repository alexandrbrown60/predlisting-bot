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

$update = json_decode(file_get_contents("php://input"), JSON_OBJECT_AS_ARRAY);
$telegram = new Telegram($update);
$database = new DatabaseManager('alexanb0_listing');

//присылаем приветственное сообщение
if($telegram->isMessage()) {
    //собираем данные из сообщения
    $userMessage = $telegram->userMessage;
    $userName = $telegram->userName;
    $chat_id = $telegram->userId;

    if ($userMessage == "/start") {
    $text = "Для того, чтобы забронировать объект введите его адрес и цену. Указывайте тип объекта, если это не квартира!\n\nПримеры: Кораблейстроителей, 15, 2 ком, 45 м2, 10 эт, 6700, Алиса\nДом, Лужайкина, 15, 100 м2, 4500, Айдар\nУчасток, Мамадышский тракт, 38, 5 сот, 2200, Гульназ\nКоммерция, Техническая, 10, 12500, Гульчачак";
    $telegram->sendMessage(["chat_id" => $chat_id, "text" => $text]);
    } else {
        //если пользователь прислал нам адрес
        $messageArray = explode(", ", $userMessage);
        $object = new Object($messageArray);

        //проверка ввода стоимости
        if($object->checkPrice() == false) {
            $telegram->sendMessage(["chat_id" => $chat_id, "text" => "Вы не ввели стоимость объекта"]);
        }
        else {
            //проверяем на наличие в CRM
            $crmFinder = new CrmFinder();
            $checkingResult = $crmFinder->check($object);
            if($checkingResult == "Объекта нет в CRM") {

                //Проверяем наличие в БД
                $objectExist = $database->check($object);
                if($objectExist) {

                    //Если объект свободен, записываем его в БД и отправляем кнопки
                    $id = $database->add($object, $userName);

                    $button1 = array("text" => "Встреча","callback_data" => "meet$id");
                    $button2 = array("text" => "Бронь", "callback_data" => "book$id");
                    $button3 = array("text" => "Отказ", "callback_data" => "fail$id");
                    $inlineKeyboard = [[$button1],[$button2], [$button3]];
                    $keyboard = ["inline_keyboard" => $inlineKeyboard];
                    $replyMarkup = json_encode($keyboard);
                    $telegram->sendMessage(["chat_id" => $chat_id, "text" => "Объект свободен. Выберите действие:", "reply_markup" => $replyMarkup]);
                
                }
                else {
                    $telegram->sendMessage(["chat_id" => $chat_id, "text" => "Объект забронирован"]);
                }
                
            }
            else {
                $telegram->sendMessage(["chat_id" => $chat_id, "text" => $checkingResult]);
            }
        }
           
    }
}
else {
    $clickedButton = $telegram->clickedButton;
    $chatId = $telegram->userId;

    if(strripos($clickedButton, "meet") !== false) {
        $objectId = str_replace("meet", "", $clickedButton);
        $database->setDate($objectId, 4);
        $telegram->sendMessage(["chat_id" => $chatId, "text" => "Объект забронирован за вами на 4 дня вперёд"]);
    }
    if(strripos($clickedButton, "book") !== false) {
        $objectId = str_replace("book", "", $clickedButton);
        $database->setDate($objectId, 2);
        $telegram->sendMessage(["chat_id" => $chatId, "text" => "Объект забронирован за вами на 2 дня вперёд"]);
    }
    if(strripos($clickedButton, "fail") !== false) {
        $objectId = str_replace("fail", "", $clickedButton);
        $database->delete($objectId);
    }
}




