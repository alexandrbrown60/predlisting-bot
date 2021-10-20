<?php
header("Content-type: text/html; charset=utf-8");
//ошибки
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require 'constants.php';
require 'classes/Telegram.php';
require 'classes/DatabaseConnection.php';
require 'classes/DatabaseManager.php';

//получить все объекты с сегодняшней датой брони
$database = new DatabaseManager('alexanb0_listing');
$objects = $database->getAllForToday();
$telegram = new Telegram();

//разослать каждому сообщение с кнопками
foreach ($objects as $key => $value) {
	$id = $objects[$key]['id'];
	$userId = $objects[$key]['userId'];
	$street = $objects[$key]['street'];
	$house = $objects[$key]['house'];
	$price = $objects[$key]['price'];
	$owner = $objects[$key]['owner'];
	$object = "$street, $house, $price, $owner";


	//отправляем сообщение
	if(isset($userId)) {
		$button1 = array("text" => "Взял в работу", "callback_data" => "hook$id");
		$button2 = array("text" => "Продлить бронь", "callback_data" => "book$id");
	    $button3 = array("text" => "Отказ", "callback_data" => "fail$id");
	    $inlineKeyboard = [[$button1], [$button2], [$button3]];
	    $keyboard = ["inline_keyboard" => $inlineKeyboard];
	    $replyMarkup = json_encode($keyboard);
	    $telegram->sendMessage(["chat_id" => $userId, "text" => "Ранее вы бронировали объект: $object. Обновите статус работы с ним:", "reply_markup" => $replyMarkup]);
	}
}

