<?php 

class DatabaseManager extends DatabaseConnection {
	public function check($object, $name) {
		$sql = "SELECT * FROM listing WHERE street = :street AND house = :house AND price = :price";
		$inputs = [
			':street' => $object->street,
			':house' => $object->house,
			':price' => $object->price
		];

		$query = PDO::prepare($sql);
		$query->execute($inputs) or die(print_r($query->errorInfo(), true));
		$result = $query->fetchAll();
		if (count($result) > 0 ) {
			$currentDate = date('Y-m-d');
			if($result[0]['date'] >= $currentDate) {
				$message = "Объект забронирован пользователем ".$result[0]['name'];
				return $message;
			}
			else {
				$message = "Объект свободен, однако ранее был забронирован пользователем ".$name.". Можете узнать у него подробности работы с данным объектом.";
				return $message;
			}
		}
		else {
			$message = "Объект свободен и будет забронирован за вами на последующие 2 дня";
			$this->add($object, $name);
			return $message;
		}
	}

	private function add($object, $name) {
		$date = date('Y-m-d', strtotime('+2 day'));
		$sql = "INSERT INTO listing (street, house, rooms, floor, area, landArea, price, name, date) VALUES (
			:street,
			:house,
			:rooms,
			:floor,
			:area,
			:landArea,
			:price,
			:name,
			:date
		)";
		$inputs = [
			":street" => $object->street,
			":house" => $object->house,
			":rooms" => $object->rooms,
			":floor" => $object->floor,
			":area" => $object->area,
			":landArea" => $object->landArea,
			":price" => $object->price,
			":name" => $name,
			":date" => $date
		];
		$query = PDO::prepare($sql);
		$query->execute($inputs) or die(print_r($query->errorInfo(), true));
	}
}