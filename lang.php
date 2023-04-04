<?
	/*

		Language generator ;)

	*/


	function __autoload($class) {
		include_once("../class/class.{$class}.php");
		if (file_exists("../class/style.{$class}.php")) include("../class/style.{$class}.php");
	}

	include_once("globals.php");

	$mysqli = new log_mysqli(host, user, pass, db);
	$mysqli->debug(true, "lims.lang");
	/* check connection */
	if (mysqli_connect_errno()) {
		printf("Connect failed: %s\n", mysqli_connect_error());
		exit();
	}


/* wersja 0.3 */

	$sql = $mysqli->query("SELECT * FROM w_lang_pivot");

	$t = array("pl", "en", "de", "fr");

	for ($i = 0; $i < count($t); $i++) {
		$string[$i] = "<?\n\t/*\n\t#\tLang file:\t\t".$t[$i]."\n\t#\n\t#\tLast update:\t".date("d-m-Y H:i")."\n\t*/\n\n\t".'$lang = new stdClass;'."\n\n";
	}

	while ($tr = $sql->fetch_object()) {
		for ($i = 0; $i < count($t); $i++) {
			$string[$i] .= "\t".'$lang->'.$tr->string.((strlen($tr->string)>8)?"\t":"\t\t").' = "'.
			(
				(empty($tr->{$t[$i]})) ? ((empty($tr->en)) ? $tr->pl : $tr->en) : $tr->{$t[$i]}
			)
			.'";'."\n";
		}
	}

	for ($i = 0; $i < count($t); $i++) {
		
		$string[$i] .= "?>";

		//echo "<p><pre>".htmlspecialchars($string[$i])."</pre></p>";

		$file = fopen("lang/lang_".$t[$i].".php", 'w');
		fwrite($file, $string[$i]);
		fclose($file);
	}

/**/
?>