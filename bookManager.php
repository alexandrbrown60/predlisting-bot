<?php

require 'constants.php';
require 'classes/Telegram.php';
require 'classes/DatabaseConnection.php';
require 'classes/DatabaseManager';

//получить все объекты с сегодняшней датой брони
$database = new DatabaseManager('alexanb0_listing');
$objects = $database->getAllForToday();
$telegram = new Telegram();

//разослать каждому сообщение с кнопками
foreach ($objects as $key) {
	$id = $objects[$key]['id'];
	$userId = $objects[$key]['userId'];
	$street = $objects[$key]['street'];
	$house = $objects[$key]['house'];
	$price = $objects[$key]['price'];
	$owner = $object[$key]['owner'];
	$object = "$street, $house, $price, $owner";

	//отправляем сообщение
	$button1 = array("text" => "Взял в работу", "callback_data" => "hook$id")
	$button2 = array("text" => "Продлить бронь", "callback_data" => "book$id");
    $button3 = array("text" => "Отказ", "callback_data" => "fail$id");
    $inlineKeyboard = [[$button1], [$button2], [$button3]];
    $keyboard = ["inline_keyboard" => $inlineKeyboard];
    $replyMarkup = json_encode($keyboard);
    $telegram->sendMessage(["chat_id" => $userId, "text" => "Ранее вы бронировали объект: $object. Обновите статус работы с ним:", "reply_markup" => $replyMarkup]);
}

