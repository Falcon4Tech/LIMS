<?php
header('Content-type: application/json');
	function __autoload($class) {
		include_once("../class/class.{$class}.php");
		if (file_exists("../class/style.{$class}.php")) include("../class/style.{$class}.php");
	}

	include_once("globals.php");

	$mysqli = new log_mysqli(host, user, pass, db);
	$mysqli->debug(true, "lims.ajax");
	/* check connection */
	if (mysqli_connect_errno()) {
		printf("Connect failed: %s\n", mysqli_connect_error());
		exit();
	}


################
#
#	pomiary

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
#
#

################
#
#	tolerancje

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
#
#



################
#
#	a2 referencja

	if (isset($_POST['a2Projekt'])) {
		$projekt = $_POST['a2Projest'];
		$ref = $_POST['a2Ref'];
		$indeks = $_POST['a2indeks'];
		$linia = $_POST['a2linia'];

	}

#-	a2 zmiana referencji

	if (isset($_POST['a2RefChange'])) {
		$projekt = $_POST['a2projekt'];
		$ref = $_POST['a2ref'];

		$sql = $mysqli->query("SELECT id_ref as id, linia, indeks FROM ref WHERE projekt_id = '$projekt' AND ref_numb = '$ref' LIMIT 1");

		$info[] = $sql->num_rows;
		$info[] = $sql->fetch_assoc();
		echo json_encode($info);
	}

##

################
#
#	translator

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
#
#

if (isset($_POST['test'])) {
	//$piana = $mysqli->query("SELECT 
	$test_arr = true;
	echo json_encode($test_arr);
}
?>