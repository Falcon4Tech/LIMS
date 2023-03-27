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

	if (isset($_POST['tol_refe'])) {
		$refe = $_POST['tol_refe'];

		$sql = $mysqli->query("SELECT rindex as num, indeks as max FROM tolerancje JOIN ref ON tolerancje.ref_id = ref.id_ref WHERE ref_id = '$refe' ORDER BY rindex DESC LIMIT 1");

		$ref_arr = $sql->fetch_assoc();
		echo json_encode($ref_arr);
	}
	
	if (isset($_POST['tolerancje'])) {
		$refe = $_POST['tolerancje'];

		$sql = $mysqli->query("SELECT LEFT(tolerancje.stref_id,1) as str, tolerancje.stref_id as strefa, nazwa_strefy as nazwa, ucl, lcl, nom, dev, procent FROM strefy_ref JOIN tolerancje USING(ref_id) JOIN strefy ON tolerancje.stref_id = id_stref WHERE strefy_ref.ref_id = '$refe' AND tolerancje.test_id IN (1,3,4) GROUP BY FIELD(str, 'c', 't', 'f', 'b', 'i', 'p', 'g', 'w'), strefa ASC");

		while($ref = $sql->fetch_assoc()) {
			$ref_arr[] = $ref;
		}
		echo json_encode($ref_arr);
	}

?>