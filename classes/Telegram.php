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
}