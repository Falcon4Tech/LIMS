<?php

include_once("ajax.php");
	
	if (isset($_POST['translate'])) {
		$lang = $_POST['translate'];
		$string = $_POST['string'];
		$text = $_POST['text'];

		$sql = $mysqli->query("INSERT INTO lang (id, lang, string, text) VALUES (NULL, '$lang', '$string', '$text') ON DUPLICATE KEY UPDATE text = '$text'");

		if ($mysqli->affected_rows) $ref_arr['err'] = false;
		else $ref_arr['err'] = true;
		echo json_encode($ref_arr);
	}
	
	if (isset($_POST['sString'])) {
		$string = $_POST['sString'];
		$text = $_POST['sText'];

		$sql = $mysqli->query("INSERT INTO lang (id, lang, string, text) VALUES (NULL, 'pl', '$string', '$text')");

		if ($mysqli->insert_id > 0) $ref_arr['err'] = false;
		else $ref_arr['err'] = true;
		echo json_encode($ref_arr);
	}

?>