<?php

include_once("ajax.php");

	if (isset($_POST['projekt'])) {
		$projekt = $_POST['projekt'];

		$sql = $mysqli->query("SELECT id_ref as id, ref_numb as num FROM ref WHERE projekt_id = '$projekt' ORDER BY RIGHT(ref_numb, 4) ASC");

		$ref_arr[] = array("id" => 0, "num" => "Referencja", "option" => "disabled selected");
		while($ref = $sql->fetch_object()) {
			$ref_arr[] = array("id" => $ref->id, "num" => $ref->num, "option" => "");
		}
		echo json_encode($ref_arr);
	}

	if (isset($_POST['a2Projekt'])) {
		$projekt = $_POST['a2Projekt'];
		$ref = $_POST['a2Ref'];
		$indeks = $_POST['a2indeks'];
		$linia = str_replace('0','', $_POST['a2linia']);
		$aktywny = (($_POST['infoProjekt'])=='true'?1:0);

		$mysqli->query("INSERT INTO ref (projekt_id, ref_numb, linia, indeks, aktywny) VALUES ('$projekt', '$ref', '$linia', '$indeks', '$aktywny') ON DUPLICATE KEY UPDATE linia = '$linia', indeks = '$indeks', aktywny = '$aktywny'");
		if ($mysqli->affected_rows) $ref_arr['err'] = false;
		else $ref_arr['err'] = true;
		//$ref_arr['ts'] = $projekt;
		echo json_encode($ref_arr);

	}


	if (isset($_POST['a2RefChange'])) {
		$projekt = $_POST['a2projekt'];
		$ref = $_POST['a2ref'];

		$sql = $mysqli->query("SELECT id_ref as id, linia, indeks, aktywny FROM ref WHERE projekt_id = '$projekt' AND ref_numb = '$ref' LIMIT 1");

		$info[] = $sql->num_rows;
		$info[] = $sql->fetch_assoc();
		echo json_encode($info);
	}

?>