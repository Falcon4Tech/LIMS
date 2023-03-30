<?php

include_once("ajax.php");

/*
#
#		u1
#
*/

if (isset($_POST['projekt'])) {
	$projekt = $_POST['projekt'];

	$sql = $mysqli->query("SELECT id_ref as id, ref_numb as num FROM ref WHERE projekt_id = '$projekt' ORDER BY RIGHT(ref_numb, 4) ASC");

	$ref_arr[] = array("id" => 0, "num" => "Referencja", "option" => "disabled selected");
	while($ref = $sql->fetch_object()) {
		$ref_arr[] = array("id" => $ref->id, "num" => $ref->num, "option" => "");
	}
	echo json_encode($ref_arr);
}

if (isset($_POST['refe'])) {
	$refe = $_POST['refe'];

	$sql = $mysqli->query("SELECT linia as num, indeks FROM ref WHERE id_ref = '$refe' LIMIT 1");

	$ref = $sql->fetch_object();
	foreach (str_split($ref->num) as $l)
		$ref_arr[] = array("id" => $l, "num" => "L".$l, "indeks" => $ref->indeks);
	echo json_encode($ref_arr);
}



if (isset($_POST['tol'])) {
	$tol = $_POST['tol'];

	$sql = $mysqli->query("SELECT stref_id, nom FROM tolerancje WHERE ref_id = '$tol' and test_id = 3 ORDER BY stref_id ASC");

	while($tol = $sql->fetch_object()) {
		$ref_arr[] = array("id" => $tol->stref_id, "nom" => $tol->nom);
	}
	echo json_encode($ref_arr);
}

if (isset($_POST['piana'])) {
	$piana = $_POST['piana'];

	$sql = $mysqli->query("SELECT projekt_id as projekt, id_ref as reff, prod_date as data, zmiana, piana.indeks FROM piana LEFT JOIN ref ON ref_id = id_ref WHERE id_piana = '$piana'");

	if ($sql->num_rows) { 
		//$pian = $sql->fetch_object();
		//$ref_arr = array("projekt" => $pian->projekt_id, "reff" => $pian->id_ref, "data" => $pian->prod_date, "zmiana" => $pian->zmiana);
		$ref_arr = $sql->fetch_assoc();
	} else {
		$currentWeekDay = date( "w" );
		switch ($currentWeekDay) {
			case "1": {  // monday
				$lastWorkingDay = date("Y-m-d", strtotime("-2 day"));
				break;
			}
			/*case "0": {  // sunday
				$lastWorkingDay = date("Y-m-d", strtotime("-2 day"));
				break;
			}*/
			default: {  //all other days
				$lastWorkingDay = date("Y-m-d", strtotime("-1 day"));
				break;
			}
		}
		$ref_arr = array("projekt" => 0, "reff" => 0, "data" => $lastWorkingDay, "zmiana" => 0);
	}

	echo json_encode($ref_arr);
}

?>