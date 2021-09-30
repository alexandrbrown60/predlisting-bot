<?php 

class Telegram {
	public function sendRequest($method, $params = []) {
	    if(!empty($params)) {
	        $url = BASE_URL . $method . '?' . http_build_query($params);
	    }
	    else {
	        $url = BASE_URL . $method;
	    }
	    
	    return json_decode(file_get_contents($url), JSON_OBJECT_AS_ARRAY);
	}

	public function setInlineKeyboard($keyboardButtons) {
		$inlineKeyboard = [];
		foreach($keyboardButtons as $button) {
			array_push($inlineKeyboard, ['text' => $button->text, 'callback_data' => $button->data]);
		}
		$keyboard = ['inline_keyboard' => $inlineKeyboard];
		$replyMarkup = json_encode($keyboard);
		return $replyMarkup;
	}
}