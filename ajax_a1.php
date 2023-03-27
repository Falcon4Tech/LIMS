<?php

include_once("ajax.php");

	if (isset($_POST['a1Projekt'])) {
		$projekt = $_POST['a1Projekt'];

		$sql = $mysqli->query("SELECT * FROM projekt WHERE id_projekt = '$projekt' LIMIT 1");

		$info = $sql->fetch_assoc();
		echo json_encode($info);
	}

	if (isset($_POST['nowyProjekt'])) {
		$projekt = $_POST['nowyProjekt'];

		$mysqli->query("INSERT INTO projekt (nazwa, aktywny) VALUES ('$projekt', 0)");
		if ($mysqli->insert_id > 0) $ref_arr['err'] = false;
		else $ref_arr['err'] = true;
		echo json_encode($ref_arr);
	}

	if (isset($_POST['infoProjekt'])) {
		$projekt = $_POST['pNazwa'];
		$aktywny = (($_POST['infoProjekt'])=='true'?1:0);
		$klient = $_POST['pKlient'];

		$mysqli->query("UPDATE projekt SET aktywny = '$aktywny', klient = '$klient' WHERE id_projekt = '$projekt'");
		if ($mysqli->affected_rows) $ref_arr['err'] = false;
		else $ref_arr['err'] = true;
		//$ref_arr['ts'] = $projekt;
		echo json_encode($ref_arr);
	}

?>