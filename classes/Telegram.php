<?php 

class Telegram {
	public $userMessage;
	public $userName;
	public $userId;
	public $clickedButton;

	public function getUpdate($data) {
		//если прислано текстовое сообщение
		if(isset($data['message']['text'])) {
			$this->userName = $data['message']['from']['first_name'];
			$this->userMessage = $data['message']['text'];
			$this->userId = $data['message']['from']['id'];
		}
		else {
			$this->userId = $data['callback_query']['from']['id'];
			$this->clickedButton = $data['callback_query']['data'];
		}
	}

	public function sendMessage($params = []) {
	    if(!empty($params)) {
	        $url = BASE_URL . "sendMessage" . '?' . http_build_query($params);
	    }
	    else {
	        $url = BASE_URL . "sendMessage";
	    }
	    
	    return json_decode(file_get_contents($url), JSON_OBJECT_AS_ARRAY);
	}
	
	public function isMessage() {
		if(isset($this->userMessage)) {
			return true;
		}
		return false;
	}
}