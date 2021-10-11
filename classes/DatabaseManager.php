<?php 

class DatabaseManager extends DatabaseConnection {
	public function check($object) {
		$sql = "SELECT * FROM listing WHERE street = :street AND house = :house AND price = :price AND owner = :owner";
		$inputs = [
			':street' => $object->street,
			':house' => $object->house,
			':price' => $object->price,
			':owner' => $object->owner
		];

		$query = PDO::prepare($sql);
		$query->execute($inputs) or die(print_r($query->errorInfo(), true));
		$result = $query->fetchAll();
		if (count($result) > 0 ) {
			$currentDate = date('Y-m-d');
			if($result[0]['date'] >= $currentDate) {
				return false;
			}
			else {
				$this->delete($result[0]['id']);
				return true;
			}
		}
		else {
			return true;
		}
	}

	public function add($object, $name) {
		$sql = "INSERT INTO listing (street, house, rooms, floor, area, landArea, price, owner, name) VALUES (
			:street,
			:house,
			:rooms,
			:floor,
			:area,
			:landArea,
			:price,
			:owner,
			:name
		)";
		$inputs = [
			":street" => $object->street,
			":house" => $object->house,
			":rooms" => $object->rooms,
			":floor" => $object->floor,
			":area" => $object->area,
			":landArea" => $object->landArea,
			":price" => $object->price,
			":owner" => $object->owner,
			":name" => $name
		];
		$query = PDO::prepare($sql);
		$query->execute($inputs) or die(print_r($query->errorInfo(), true));
		return PDO::lastInsertId();
	}

	public function delete($id) {
		$sql = "DELETE FROM listing WHERE id = :id";
		$query = PDO::prepare($sql);
		$query->execute(array(":id" => $id)) or die(print_r($query->errorInfo(), true));
	}

	public function setDate($id, $numberOfDays) {
		$date = date('Y-m-d', strtotime("+$numberOfDays day"));
		$sql = "UPDATE listing SET date = :date WHERE id = :id";
		$query = PDO::prepare($sql);
		$query->execute(array(":date" => $date, ":id" => $id)) or die(print_r($query->errorInfo(), true));
	}
}