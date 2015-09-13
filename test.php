<?php
//try to translate the phrase into other languages using google translator
function translateGoogle($phrase) {
	//supported lang: de, it, fr, tr, es, en
	$escapedPhrase = escapeshellarg($phrase);
	exec("echo $escapedPhrase | java -jar translator.jar 2>&1", $output);
	
	//output style=language, phrase
	$result = array();
	foreach ($output as $translation) {
		//just split off the first comma. Everything else should be a part of the translation
		$translation = split(",", $translation);
		$result[$translation[0]] = $translation[1];
	}
	
	var_dump($result);
}
?>