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

	public function add($object, $name, $userId) {
		$sql = "INSERT INTO listing (street, house, rooms, floor, area, landArea, price, owner, name, userId) VALUES (
			:street,
			:house,
			:rooms,
			:floor,
			:area,
			:landArea,
			:price,
			:owner,
			:name,
			:userId
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
			":name" => $name,
			":userId" => $userId
		];
		$query = PDO::prepare($sql);
		$query->execute($inputs) or die(print_r($query->errorInfo(), true));
		return PDO::lastInsertId();
	}

	public function get($byId) {
		$sql = "SELECT * FROM listing WHERE id = :id";
		$query = PDO::prepare($sql);
		$query->execute([":id" => $byId]) or die(print_r($query->errorInfo(), true));
		$result = $query->fetchAll();
		if (count($result) > 0 ) {
			$street = $result[0]['street'];
			$house = $result[0]['house'];
			$price = $result[0]['price'];
			$data = "$street, $house, $price";
			return $data;
		}
	}

	public function getAllForToday() {
		$currentDate = date('Y-m-d');
		$sql = "SELECT * FROM listing WHERE date = :date";
		$query = PDO::prepare($sql);
		$query->execute([":date" => $currentDate]) or die(print_r($query->errorInfo(), true));
		$result = $query->fetchAll();
		return $result;
	}

	public function deleteNotRelevant() {
		//delete with no date
		$lastMonth = date('Y-m-d', strtotime("-15 day"));
		$sql = "DELETE FROM listing WHERE date IS NULL OR date < :lastMonth";
		$query = PDO::prepare($sql);
		$query->execute([":lastMonth" => $lastMonth]) or die(print_r($query->errorInfo(), true));
	}

	public function delete($id) {
		$sql = "DELETE FROM listing WHERE id = :id";
		$query = PDO::prepare($sql);
		$query->execute(array(":id" => $id)) or die(print_r($query->errorInfo(), true));
	}

	public function setDate($id, $numberOfDays) {
		$date = date('Y-m-d', strtotime("+$numberOfDays day"));
		$sql = "UPDATE listing SET date = :date, orders = orders + 1 WHERE id = :id";
		$query = PDO::prepare($sql);
		$query->execute(array(":date" => $date, ":id" => $id)) or die(print_r($query->errorInfo(), true));
	}
}