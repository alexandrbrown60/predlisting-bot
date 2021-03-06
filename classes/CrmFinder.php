<?php

class CrmFinder {
	public function check($object) {
		$url = "http://kluch.intrumnet.com:81/sharedapi/stock/filter";

		$params=array(  
	            'type'=>$object->type,  
	            'limit'=>10,  
	            'fields' => $this->setParams($object),  
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

		if($result->data->list[0]) {
			$text = "Похоже, данный объект уже есть в нашей базе: ". $result->data->list[0]->id;
			return $text;
		}
		else {
			$text = "Объекта нет в CRM";
			return $text;
		}
	}

	private function setParams($object) {
			$areaTo = floatval($object->area) + 1;
			$areaFrom = floatval($object->area) - 1;
			$priceTo = intval($object->price) * 1.02;
			$priceFrom = intval($object->price) * 0.98;

			$flatFields = array(
				array('id' => 667, 'value' => $object->street . " ул"), 
				array('id' => 484, 'value' => $object->house),
				array('id' => 446, 'value' => $object->rooms),
				array('id' => 447, 'value' => "$areaFrom & $areaTo"),
				array('id' => 448, 'value' => $object->floor),
				array('id' => 470, 'value' => "$priceFrom & $priceTo"),
				array('id' => 1522, 'value' => 'Наша база')
			);
			$houseFields = array(
				array('id' => 1149, 'value' => $object->street . " ул"),
				array('id' => 556, 'value' => $object->house),
				array('id' => 530, 'value' => $object->rooms),
				array('id' => 526, 'value' => "$areaFrom & $areaTo"),
				array('id' => 527, 'value' => $object->landArea),
				array('id' => 528, 'value' => "$priceFrom & $priceTo"),
				array('id' => 1538, 'value' => 'Наша база')
			);
			$commercialFields = array(
				array('id' => 668, 'value' => $object->street . " ул"),
				array('id' => 515, 'value' => $object->house),
				array('id' => 488, 'value' => "$areaFrom & $areaTo"),
				array('id' => 506, 'value' => $object->floor),
				array('id' => 491, 'value' => "$priceFrom & $priceTo"),
				array('id' => 1530, 'value' => 'Наша база')
			);

			if ($object->type == 1) {
				return $flatFields;
			}
			elseif ($object->type == 2) {
				return $commercialFields;
			}
			else {
				return $houseFields;
			}

		}
}