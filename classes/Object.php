<?php

class Object {
	public $type;
	public $city;
	public $street;
	public $house;
	public $floor;
	public $area;
	public $landArea;
	public $rooms;
	public $price;
	public $owner;

	public function __construct($array) {
		foreach ($array as $value) {
			if(strripos($value, "ком") !== false) {
				$this->rooms = trim(str_replace("ком", "", $value));
			}	
			if(strripos($value, "м2") !== false || strripos($value, "квм") !== false || (strripos($value, "м") !== false && strlen($value) <= 7)) {
				$areaData = ["м2", "квм", "м"];
				$this->area = trim(str_replace($areaData, "", $value));
			}
			if(strripos($value, "эт") !== false || (strripos($value, "э") !== false && strlen($value) < 5)) {
				$floorsData = ["эт", "э"];
				$this->floor = trim(str_replace($floorsData, "", $value));
				}
			if(strripos($value, "сот") !== false) {
					$this->landArea = trim(str_replace("сот", "", $value));
				}
			if(is_numeric($value) && strlen($value) > 3) {
				$this->price = $value * 1000;
			}
		}
		if($array[0] == 'Дом' || $array[0] == 'Участок') {
			$this->type = 3;
			$this->street = $array[1];
			$this->house = $array[2];
		}
		elseif ($array[0] == 'Коммерция') {
			$this->type = 2 ;
			$this->street = $array[1];
			$this->house = $array[2];
		}
		else {
			$this->type = 1;
			$this->street = $array[0];
			$this->house = $array[1];
		}
		$this->owner = end($array);
	
	}

	public function check() {
		if(empty($this->price) || empty($this->area)) {
			return false;
		}
		return true;
	}

	public function getInfo() {
		$objectString = "$this->street, $this->house";
		return $objectString;
	}


}