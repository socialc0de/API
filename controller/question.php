<?php
include 'database.php';

/** 
* GET /question/{language}
* GET /question/{language}/{requested question IDs} - Comma separated
*/
class QuestionController {
	
	//const EXCLUDED_COLUMNS = ['id'];
	
	public function getAction(array $data) {
		$db = getDatabaseCon();
		if (!isset($data[0])) {
			throw new Exception("Missing parameters", 400);
		} 
		
		$language = $data[0];
		if (isset($data[1])) {
			if (preg_match('/[^0-9,]/i', $data[1])) {
				//prevent sqli so that we just allow integer values and commas
				throw new Exception("Invalid id(s) parameter", 400);
			}
			
			$ids = $data[1];
			
			$stmt = $db->prepare("SELECT * FROM question JOIN translation ON translation.question=question.id "
			//Select localized language name
			. "JOIN category ON question.category=category.catid "
			. "WHERE translation.language=? AND question.ID IN (?)");
			$stmt->bind_param("ii", $language, $ids);
		} else {
			$stmt = $db->prepare("SELECT * FROM question JOIN translation ON translation.question=question.id "
			//Select localized language name
			. "JOIN category ON question.category=category.catid "
			. "WHERE translation.language=?");
			$stmt->bind_param("i", $language);
		}
		
		$stmt->execute();
		$result = $stmt->get_result();
		
		$rows = array();
		while ($row = $result->fetch_assoc()) {
			//filter output from unwanted privacy things
		//	foreach (self::EXCLUDED_COLUMNS as $column) { //doesn't work in PHP 5.5
			//	unset($row[$column]);
		//	}
			//var_dump($row);
			
			//so we doa whitelist
			$apiOutput = array();
			
			$apiOutput['id'] = $row['id'];
			$apiOutput['active'] = $row['active'];
			$apiOutput['question_translation'] = utf8_encode($row['question_translation']);
			$apiOutput['question_answer'] = utf8_encode($row['question_answer']);
			$apiOutput['category_name'] = utf8_encode($row['name']);
			
			$rows[] = $apiOutput;
			
			//var_dump($row);
		}
		
		if (empty($rows)) {
			//notify the client that this is an empty array to detect it on the response code not the input
			http_response_code(204);
		}
		
		//var_dump($rows);
		$json = json_encode($rows);
		//var_dump($json);
		print $json;
	}
}
?>