<?php

include_once("ajax.php");

/*
#
#		u1
#
*/

$katalog = "/var/www/labtool/section";

if (isset($_POST['input'])) {
	$nazwa = $_POST['name'];
	$val = $_POST['value'];
	$idd = $_POST['idd'];

	$result = $mysqli->query("SHOW COLUMNS FROM geo_raport LIKE '$nazwa'");
	$exists = (mysqli_num_rows($result))?TRUE:FALSE;
	if(!$exists) {
	   $mysqli->query("ALTER TABLE geo_raport ADD `$nazwa` VARCHAR(150) NULL");
	}

	$r_create = date("Y-m-d");

	$sql = $mysqli->query("INSERT INTO geo_raport (`idd`, `$nazwa`, `r_create`) VALUES ('$idd', '$val', '$r_create') ON DUPLICATE KEY UPDATE `$nazwa` = '$val' ");

	$info[] = $mysqli->affected_rows;
	echo json_encode($info);
}

if ($_FILES['file']['size'] > 0) {
    if ( 0 < $_FILES['file']['error'] ) {
        echo 'Error: ' . $_FILES['file']['error'] . '<br>';
    }
    else {
		do {
			$new_id = bin2hex(random_bytes(5));
			$sql = $mysqli->query("SELECT IF(COUNT(*) > 0, 1, 0) as a FROM geo_section WHERE img_id = '$new_id'");
			$sql = $sql->fetch_object();
		} while ($sql->a);
		//$section = $_POST['section'];
		//$idd = $_POST['idd'];
		$plik_rozmiar = $_FILES['file']['size'];
		$ext = strtolower(end(explode(".", $_FILES['file']['name'])));
		//$ext = pathinfo($_FILES['file']['name'])['extension'];

		$mysqli->query("INSERT INTO geo_section (img_id, size, ext) VALUES ('$new_id', '$plik_rozmiar', '$ext')");
		$rec_id = $mysqli->insert_id;
        move_uploaded_file($_FILES['file']['tmp_name'], $katalog . "/" . $new_id.".".$ext);
		$info[] = $rec_id;
		$info[] = $ext;
		$info[] = $new_id;
		echo json_encode($info);
		//print_r($_FILES);
    }
}

if (isset($_POST['section'])) {
	$id = $_POST['id'];
	$img_id = $_POST['img'];
	$nazwa = $img_id.".".$_POST['ext'];
	create_thumbnail("$katalog/$nazwa", "$katalog/$nazwa", 1024, 95);
	$size = getimagesize("$katalog/$nazwa");
	$fsize = filesize("$katalog/$nazwa");
	$imgw = $size[0];
	$imgh = $size[1];
	$idd = $_POST['report'];
	$section = $_POST['section'];

	# kasowanie poprzednich
	$sql = $mysqli->query("SELECT id, img_id, ext FROM geo_section WHERE report_id = '$idd' AND section = '$section'");
	while ($ww = $sql->fetch_object()) {
		unlink($katalog . "/" . $ww->img_id . "." . $ww->ext);
		$mysqli->query("DELETE FROM geo_section WHERE id = '$ww->id'");
	}

	$mysqli->query("UPDATE geo_section SET report_id = '$idd', section = '$section', w = '$imgw', h = '$imgh', size = '$fsize' WHERE img_id = '$img_id'");
	$info[] = "OK";
	echo json_encode($info);
}

if (isset($_POST['fProjekt'])) {
	$proj = $_POST['fProjekt'];
	$ref  = $_POST['fRef'];
	$date = $_POST['fDate'];
	$order= $_POST['fOrder'];

	$sql = $mysqli->query("SELECT a.idd, a.decyzja, a.order, a.order_date, a.tool, b.nazwa, c.ref_numb FROM geo_raport a left OUTER JOIN projekt b ON a.projekt_id = b.id_projekt left outer join ref c on a.ref_id = c.id_ref WHERE c.ref_numb LIKE '%".$ref."%' AND a.order_date LIKE '".$date."%' AND a.order LIKE '".$order."%' AND b.nazwa LIKE '".$proj."%' ORDER BY a.order_date DESC");

	while($obj = $sql->fetch_row()) {
		$json[] = array('idd' => $obj[0], 'decyzja' => $obj[1], 'order' => $obj[2], 'date' => $obj[3], 'tool' => $obj[4], 'nazwa' => $obj[5], 'ref' => $obj[6]);
	}
	echo json_encode($json);
}


/*
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
			#case "0": {  // sunday
			#	$lastWorkingDay = date("Y-m-d", strtotime("-2 day"));
			#	break;
			#}
			default: {  //all other days
				$lastWorkingDay = date("Y-m-d", strtotime("-1 day"));
				break;
			}
		}
		$ref_arr = array("projekt" => 0, "reff" => 0, "data" => $lastWorkingDay, "zmiana" => 0);
	}

	echo json_encode($ref_arr);
}
*/


	function create_thumbnail( $source_file, $destination_file, $max_dimension, $quality = NULL)
	{
		list($img_width,$img_height) = getimagesize($source_file); // pobranie ori wymiarów
		$aspect_ratio = $img_width / $img_height; // stosunek długości do szerokości

		if ( ($img_width > $max_dimension) || ($img_height > $max_dimension) ) {	// jeżeli zdjecie jest większe niż założenie

			if ( $img_width > $img_height ) { // dla wysokich zdjęć...
				$new_width = $max_dimension;
				$new_height = $new_width / $aspect_ratio;
			}
			elseif ( $img_width < $img_height ) { // dla długich zdjęć...
				$new_height = $max_dimension;
				$new_width = $new_height * $aspect_ratio;
			}
			elseif ( $img_width == $img_height ) { // dla kwadratowych zdjęć...
				$new_width = $max_dimension;
				$new_height = $max_dimension;
			}
			else { echo "Error reading image size."; return FALSE; }
		}
		else { $new_width = $img_width; $new_height = $img_height; } // jeśli jest mniejsze wymiar się nie zmieni.

		// Make sure these are integers.
		$new_width = intval($new_width);
		$new_height = intval($new_height);

		$thumbnail = imagecreatetruecolor($new_width,$new_height); // Creates a new image in memory.

		// The following block retrieves the source file.  It assumes the filename extensions match the file's format.
		if ( strpos($source_file,".gif") ) { $img_source = @imagecreatefromgif($source_file); }
		if ( (strpos($source_file,".jpg")) || (strpos($source_file,".jpeg")) )
		{ $img_source = @imagecreatefromjpeg($source_file); }
		if ( strpos($source_file,".bmp") ) { $img_source = @imagecreatefromwbmp($source_file); }
		if ( strpos($source_file,".png") ) { $img_source = @imagecreatefrompng($source_file); }

		// Here we resample and create the new jpeg.
		@imagecopyresampled($thumbnail, $img_source, 0, 0, 0, 0, $new_width, $new_height, $img_width, $img_height);
		if ($quality > 0) {
			@imagejpeg( $thumbnail, $destination_file, $quality );
		} else {
			@imagejpeg( $thumbnail, $destination_file, 75 );
		}

		// Finally, we destroy the two images in memory.
		@imagedestroy($img_source);
		@imagedestroy($thumbnail);
	}
?>
