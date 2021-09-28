<?php
header("Content-type: text/html; charset=utf-8");
//ошибки
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

//подлюкчение классов
require '/classes/Object.php';

$ini = parse_ini_file('config.ini');

//подключение к Телеграм
const TOKEN = $ini['token'];
const BASE_URL = 'https://api.telegram.org/bot' . TOKEN . '/';
const CRM_API_KEY = $ini['crm_api'];

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

function checkInCrm() {
	$url = "http://kluch.intrumnet.com:81/sharedapi/stock/filter";
	$params=array(  
            'type'=>1,  
            'limit'=10,  
            'fields' => array(  
                array('id'=>470,'value'=>"6000000"),  
                array('id'=>485,'value'=>"Алтуфьево")  
            ),  
            'order_field' => 470,  
            'order'=> "desc"  
        ); 
    $post = array(  
        'apikey' =>CRM_API_KEY,  
         'params'=>$params  
    );  
          
	$ch = curl_init();  
	curl_setopt($ch, CURLOPT_URL, $url);  
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
	curl_setopt($ch, CURLOPT_POST, 1);  
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
	$result = json_decode(curl_exec($ch));  
	curl_close ($ch);

	if($result) {
		//send message?	
	}  
}

function checkInDatabase() {

}
