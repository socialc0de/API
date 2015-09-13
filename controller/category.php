<?php
include 'database.php';

/** 
* GET /category/{language}
* GET /category/{language}/{ID}
*/
class CategoryController {

	//const EXCLUDED_COLUMNS = ['id'];

	public function getAction(array $data) {
		$db = getDatabaseCon();
		if (!isset($data[0])) {
			throw new Exception("Missing language parameter", 400);
		} 
		
		$language = $data[0];
		if (isset($data[1])) {
			$categoryId = $data[1];
			
			$stmt = $db->prepare("SELECT * FROM category WHERE Language=? AND Catid=?");
			$stmt->bind_param("si", $language, $categoryId);
		} else {
			$stmt = $db->prepare("SELECT * FROM category WHERE Language=?");
			$stmt->bind_param("s", $language);
		}

		$stmt->execute();
		$result = $stmt->get_result();
		
		$rows = array();
		while ($row = $result->fetch_assoc()) {
			//filter output from unwanted privacy things
			//foreach (self::EXCLUDED_COLUMNS as $column) { //doesn't work in PHP 5.5
				//unset($row[$column]);
			//}
			$row['name'] = str_replace(" ", '_', $row['name']);
			
			
			$rows[] = $row;
		}
		
		if (empty($rows)) {
			//notify the client that this is an empty array to detect it on the response code not the input
			http_response_code(204);
		}
		
		echo json_encode($rows);
	}
}
?>