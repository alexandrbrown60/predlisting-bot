<?php

class Object {
	private $type;
	private $city;
	private $street;
	private $house;
	private $floor;
	private $area;
	private $rooms;
	private $price;

	public function __construct($array) {
		foreach ($array as $value) {
			if(strripos($value, "ком") !== false) {
				$this->rooms = trim(str_replace("ком", "", $value));
			}
			if(strripos($value, "м2") !== false || strripos($value, "квм") || strripos($value, "сот")) {
				$areaData = ["м2", "квм", "сот"];
				$this->area = trim(str_replace($areaData, "", $value));
			}
			if(strripos($value, "эт") !== false) {
				$this->floor = trim(str_replace("эт", "", $value));
			}
			if(is_numeric($value) && strlen($value) > 3) {
				$this->price = $value;
			}
		}
		if($array[0] == 'Дом' || $array[0] == 'Участок') {
			$this->type = 2;
			$this->street = $array[1];
			$this->house = $array[2];
		}
		elseif ($array[0] == 'Коммерция') {
			$this->type = 3;
			$this->street = $array[1];
			$this->house = $array[2];
		}
		else {
			$this->type = 1;
			$this->street = $array[0];
			$this->house = $array[1];
		}
	
	}

	public function getText() {
		$text = "Вы ввели:\nулица $this->street, дом: $this->house, этаж: $this->floor, площадь: $this->area, комнат: $this->rooms, цена: $this->price";
		return $text;
	}

}