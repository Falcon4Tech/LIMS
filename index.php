<?php

	//header('HTTP/1.0 403 Forbidden');
    //header('WWW-Authenticate: Basic realm="FTs"');
	if ($_GET['page'] == 'a9') {
		header('HTTP/1.1 401 Unauthorized');
		echo "<meta http-equiv='Refresh' content='2;url=/' />";
		die('<Please close tab.');
	}


	function __autoload($class) {
		include_once("../class/class.{$class}.php");
		if (file_exists("../class/style.{$class}.php")) include("../class/style.{$class}.php");
	}


	class loadItem {
		private $d_dir;
		function __construct($d_dir) {
			$this->d_dir = $d_dir;
		}
		function load($mod, $ext, $post = NULL) {
			if (isset($post)) $_POST['post'] = $post;
			include_once($this->d_dir . "/" . $mod . "." . $ext);
		}
	}

	class Input {
		function get($name) {
			return isset($_GET[$name]) ? $_GET[$name] : null;
		}
		function post($name) {
			return isset($_POST[$name]) ? $_POST[$name] : null;
		}
		function get_post($name) {
			return $this->get($name) ? $this->get($name) : $this->post($name);
		}
	}
	$input = new Input;
	//$page = $input->get_post('page');

	$mod = new loadItem("mod");

	$cache = new cache("lims", 60*60);

	$option = new optionList("geo_raport");

	$sql_user = "lims_".$_SERVER['REMOTE_USER'];
	$sql_pass = "dB".$_SERVER['REMOTE_USER']."!#";

	$mysqli = new log_mysqli('localhost', $sql_user, $sql_pass, 'lims');
	$mysqli->debug(true, "lims.index");
	/* check connection */
	if (mysqli_connect_errno()) {
		printf("Connect failed: %s\n", mysqli_connect_error());
		exit();
	}

	if (empty($input->get('lang'))) header("Location:pl.lims");
	$lan = $input->get('lang');
	require_once("lang/lang_".$input->get('lang').".php");
	$ramka = new ramkaDIV();
	$modul = new stdClass;
#

	function langMe($text) {
		global $lang;

		if (empty($lang->{$text})) $string = $text; else $string = $lang->{$text};

		$return = "<div class='tooltip' onclick=\"$('#".$text."').focus()\">".$string.
					"<span id='s_".$text."' class='tooltiptext' style='bottom:-250%;letter-spacing:normal;'>".$text."<input class='langMe' type='text' value='".$string."' id='".$text."'/></span></div>";
		return $return;
	}

switch($input->get('page')[0]) {
	case "a":
		$poziom = "#FF0000";
		break;
	default:
		$poziom = "#345792";
}

ob_start();				# u1 referencja

	$sql = $mysqli->query("SELECT id_projekt, nazwa FROM projekt ORDER BY nazwa ASC");

	$ramka->poziom($poziom);
	//$ramka->head($lang->M_REFERENCJA);
	$ramka->head(langMe("M_REFERENCJA"));
	$ramka->body = "<p id='p_piana'><input class='input piana' id='piana' placeholder='Numer piany' type='number' name='piana' /></p>";
	$ramka->body .= "<p><label class='custom-select'><select name='projekt' id='projekt'>";
	$ramka->body .= "<option disabled selected value='0'>Projekt</option>\n";
	while($op = $sql->fetch_object()) {
		$ramka->body .= "<option value='".$op->id_projekt."'><strong>".$op->nazwa."</strong></option>\n";
	}
	$ramka->body .= "</select></label></p>";

	$ramka->body .= "<p><label class='custom-select'><select name='ref' id='ref'>".
					"<option disabled selected value='0'>Referencja</option>".
					"</select></label></p>";
	$ramka->body .= "<p>";
	$ramka->body .= "<label class='custom-select'><select name='linia' id='linia' style='width:97px'>".
					"<option disabled selected value='0'>Linia</option>".
					"</select></label>&nbsp;";
	$ramka->body .= "<label class='custom-select'><select name='indeks' id='indeks' style='width:97px'>".
					"<option disabled selected value='0'>Index</option>";
		for ($i = 1; $i < 20; $i++) {
			if ($i<15) $o = 0; else $o = 1;
			$ramka->body .= "<option value='$i'>$i &#".(9397+$i+$o).";</option>";
		}
	$ramka->body .= "</select></label></p>";

	$ramka->body .= "<p><input class='input' type='date' name='data' id='data' value='".date('Y-m-d')."' /></p>";

	$ramka->body .= "<p>$lang->L_SHIFT: ".
		"<label><input type='radio' name='zmiana' id='zm1' value='1' style='display:none' /><span>&#9312;</span></label>".
		"<label><input type='radio' name='zmiana' id='zm2' value='2' style='display:none' /><span>&#9313;</span></label>".
		"<label><input type='radio' name='zmiana' id='zm3' value='3' style='display:none' /><span>&#9314;</span></label>".
		"<input type='radio' name='zmiana' id='zm0' value='0' style='display:none' /></p>";

	$ramka->drukuj();
$modul->referencja = ob_get_clean();



ob_start();				# u1 Pomiary
	$ramka->poziom($poziom);
	$ramka->head("Pomiary");
	$ramka->body = "<div class='pomiary'></div>";
	$ramka->body .= "<span id='pomiary'>---</span>";
	$ramka->drukuj();
$modul->pomiary = ob_get_clean();

$l_menu_pomiary = array("u1", "a1", "a2", "a3");
$l_menu_raporty = array("u3");

ob_start();				# menu

	$grupa = array(
					1 => explode(' ', 'u1 a1 a2 a3'),
					2 => explode(' ', 'u3 u4 u5')
				);

	$menu = array(
		"up" => array(
						"B_DASHBOARD"		=> explode('/', 'u0//0'),
						"B_MEASURE"			=> explode('/', 'u1//0'),
						"B_PROJEKTY"		=> explode('/', 'a1/adm/1'),
						"B_REFERENCJE"		=> explode('/', 'a2/adm/1'),
						"B_TOLERANCJE"		=> explode('/', 'a3/adm/1'),
						"B_REPORTS"			=> explode('/', 'u3//0'),
						"B_GEO"				=> explode('/', 'u3/adm/2'),
						"B_PULLOUT"			=> explode('/', 'u4/adm/2'),
						"B_OVERFLOW"		=> explode('/', 'u5/adm/2'),
						"B_FIRSTPART_PAGE"	=> explode('/', 'fp//0'),
						"B_ORDERS"			=> explode('/', 'u2//0'),
		),
		"down" => array(
						"B_LOGOUT"			=> explode('/', 'a9//0')
		)
	);

	$ramka->poziom($poziom);
	$ramka->head("Menu");
	$ramka->extraStyle("min-height:91%");
	$ramka->body = "<div style='margin-bottom: 155px'>";

	foreach($menu['up'] as $k => $v) {
		if ($v[2] > 0)
		if (!in_array($input->get('page'), $grupa[$v[2]])) continue;
		
		$ramka->body .=  "<button class='butn ".$v[1]."' onclick=\"window.location='$lan-".$v[0].".lims'\">".$lang->{$k}."</button><br/>";
	}

	$ramka->body .= "</div>";
		$redir = substr($_SERVER['REQUEST_URI'], 3);
	$ramka->body .= "<div style='position:absolute; bottom:30px; width:100%'>".
		"<button class='butn adm' onclick=\"window.location='$lan-a7.lims'\">$lang->B_ADMIN</button><br/>".
		"<button class='butn' onclick=\"window.location='$lan-a8-01.lims'\">$lang->B_TRANSLATE</button><br/>".
		"<button class='butn' onclick=\"window.location='$lan-a9.lims'\">".$lang->B_LOGOUT."</button><br/><br/>".
		"\n\t\t<img src='flag/pl.png' onclick=\"window.location='pl$redir'\" title='$lang->L_POLSKI' width='25px' class='flag".(($lan=='pl')?" act":"")."'/>".
		"\n\t\t<img src='flag/en.png' onclick=\"window.location='en$redir'\" title='$lang->L_ANGIELSKI' width='25px' class='flag".(($lan=='en')?" act":"")."'/>".
		"\n\t\t<img src='flag/de.png' onclick=\"window.location='de$redir'\" title='$lang->L_NIEMIECKI' width='25px' class='flag".(($lan=='de')?" act":"")."'/>".
		"\n\t\t<img src='flag/fr.png' onclick=\"window.location='fr$redir'\" title='$lang->L_FRANCUSKI' width='25px' class='flag".(($lan=='fr')?" act":"")."'/>\n\t</div>";
	$ramka->drukuj();
$modul->menu = ob_get_clean();

ob_start();				# u1 badanie

	//$sql = $mysqli->query("SELECT id_test, nazwa_testu FROM testy");

	$ramka->poziom($poziom);
	$ramka->head(langMe("M_TESTING"));
	$ramka->body = "<p><label class='custom-select'><select name='test' id='test'>";
	$ramka->body .= "<option value='0'>Test:</option>\n";
	/*while($op = $sql->fetch_object()) {
		$ramka->body .= "<option value='".$op->id_test."'><strong>".$op->nazwa_testu."</strong></option>\n";
	}*/
		$ramka->body .= "<option value='1'><strong>Twardość</strong></option>\n";
		$ramka->body .= "<option value='2'><strong>kPa</strong></option>\n";
		$ramka->body .= "<option value='3'><strong>Pullout</strong></option>\n";
		$ramka->body .= "<option value='4'><strong>Overflow</strong></option>\n";
		$ramka->body .= "<option value='5'><strong>Geometria</strong></option>\n";
	$ramka->body .= "</select></label></p>";
	$ramka->drukuj();
$modul->badanie = ob_get_clean();

if ($input->get('page') == "a1") {# projekt
	ob_start();				# a1 projekt

		$sql = $mysqli->query("SELECT id_projekt, nazwa FROM projekt ORDER BY nazwa ASC");

		$ramka->poziom($poziom);
		$ramka->head($lang->B_PROJEKTY);

		$ramka->body = "<p><label class='custom-select'><select name='projekt' id='a1_projekt'>";
		$ramka->body .= "<option disabled selected value='0'>Projekt</option>\n";
		while($op = $sql->fetch_object()) {
			$ramka->body .= "<option value='".$op->id_projekt."'><strong>".$op->nazwa."</strong></option>\n";
		}
		$ramka->body .= "</select></label></p>";

		$ramka->body .= "<p>&nbsp;</p><span>$lang->S_ADD_NEW:</span><p id='nowy' style='padding:8px'><input class='input piana' name='nowy_projekt' id='nowy_projekt' placeholder='$lang->L_NAME' /><input class='input send' type='submit' id='nowy_projekt_zapisz' /></p>";

		$ramka->drukuj();
	$modul->a1_projekt = ob_get_clean();

	ob_start();				# a1 info
		$ramka->poziom($poziom);
		$ramka->head($lang->L_INFO);
		$ramka->body = "<table id='a1Projekt' class='a3Tolerancje'>".
			"<tr id='tr_info_aktywny'><th>Aktywny:</th><td><label><input type='checkbox' id='projekt_info_aktywny' value='1' /><span>●</span></label></td></tr>".
			"<tr><th>Klient:</th><td><input class='input piana' id='projekt_info_klient' /></td></tr>".
			"<tr id='tr_info_zapisz'><td colspan='2' style='text-align:center'><input class='input send' type='submit' id='projekt_info_zapisz' /></td></tr>".
			"</table>";
		$ramka->drukuj();
	$modul->a1_info = ob_get_clean();
}

if ($input->get('page') == "a2") { # referencje

	ob_start();				# a2 referencja

		$sql = $mysqli->query("SELECT id_projekt, nazwa FROM projekt ORDER BY nazwa ASC");

		$ramka->poziom($poziom);
		$ramka->head(langMe("M_REFERENCJA"));
		$ramka->body = "<div style='display: inline-block'><p><label class='custom-select'><select name='projekt' id='a2_projekt'>";
		$ramka->body .= "<option disabled selected value='0'>Projekt</option>\n";
		while($op = $sql->fetch_object()) {
			$ramka->body .= "<option value='".$op->id_projekt."'><strong>".$op->nazwa."</strong></option>\n";
		}
		$ramka->body .= "</select></label></p>";

		#$ramka->body .= "<p><label class='custom-select'><select name='ref' id='a2_ref'>".
		#				"<option disabled selected value='0'>Referencja</option>".
		#				"</select></label></p>";

		$ramka->body .= "<p><input class='input piana' list='a2Referencje' id='a2_ref' value='' autocomplete='off' /></p>";
		$ramka->body .= "<datalist id='a2Referencje'></datalist>";

		$ramka->body .= "<p>";
		$ramka->body .= "<label><input type='checkbox' id='line_1'><span>L1</span></label>";
		$ramka->body .= "<label><input type='checkbox' id='line_2'><span>L2</span></label>";
		$ramka->body .= "<label><input type='checkbox' id='line_3'><span>L3</span></label>";
		$ramka->body .= "<label><input type='checkbox' id='line_4'><span>L4</span></label>";
		$ramka->body .= "</p>";

		$ramka->body .= "<p style='text-align: left;padding: 4px'>Indeks:";
		$ramka->body .= "<label class='custom-select' style='float: right'><select name='indeks' id='a2_indeks' style='width:97px'>".
						"<option disabled selected value='0'>Index</option>";
			for ($i = 1; $i < 20; $i++) {
				if ($i<15) $o = 0; else $o = 1;
				$ramka->body .= "<option value='$i'>$i &#".(9397+$i+$o).";</option>";
			}
		$ramka->body .= "</select></label></p>";

		$ramka->body .= "<table style='width:100%'><tr id='tr_info_aktywny'><th>Aktywny:</th><td style='text-align:center'><label><input type='checkbox' id='ref_info_aktywny' value='1' /><span>●</span></label></td></tr></table>";

		$ramka->body .= "<p id='a2_kom_projekt' style='padding:4px 0px'>&nbsp;</p><p><input class='input send' type='submit' id='a2_projekt_zapisz' /></p>";

		$ramka->body .= "</div>";
		$ramka->drukuj();
	$modul->a2_projekt = ob_get_clean();

	ob_start();				# a2 strefy
		$ramka->poziom($poziom);
		$ramka->head("Ilość stref");
		$ramka->body = "<div style='display: inline-block'>";

		$lista = array("Twardość", "kPa", "Pullout", "Geo", "Overflow");

		for ($ii = 0; $ii < count($lista); $ii++) {
			$ramka->body .= "<p style='text-align: left;padding: 4px'>".$lista[$ii].":&nbsp;&nbsp;&nbsp;";
			$ramka->body .= "<label class='custom-select' style='float: right'><select name='stref_".$ii."' id='stref_".$ii."' style='width:97px'>".
							"<option disabled selected value='0'>Brak</option>";
				for ($i = 1; $i < 20; $i++) {
					if ($i<15) $o = 0; else $o = 1;
					$ramka->body .= "<option value='$i'>$i</option>";
				}
			$ramka->body .= "</select></label></p>";
		}

		$ramka->body .= "<br/><br/><p><input class='input send' type='submit' id='a2_strefy_zapisz' /></p>";

		$ramka->body .= "</div>";

		$ramka->drukuj();
	$modul->a2_strefy = ob_get_clean();

	ob_start();				# a2 info

		$sql = $mysqli->query("SELECT id_projekt, nazwa FROM projekt ORDER BY nazwa ASC");

		$ramka->poziom($poziom);
		$ramka->head($lang->M_REFERENCJA);
		$ramka->body = "<p></p>";

		$ramka->drukuj();
	$modul->a2_indo = ob_get_clean();
}

if ($input->get('page') == "a3") {# tolerancje
	ob_start();				# a3 tol_referencja

		$sql = $mysqli->query("SELECT id_projekt, nazwa FROM projekt ORDER BY nazwa ASC");

		$ramka->poziom($poziom);
		$ramka->head($lang->M_REFERENCJA);

		$ramka->body = "<p><label class='custom-select'><select class='info' name='projekt' id='tol_projekt'>";
		$ramka->body .= "<option disabled selected value='0'>Projekt</option>\n";
		while($op = $sql->fetch_object()) {
			$ramka->body .= "<option value='".$op->id_projekt."'><strong>".$op->nazwa."</strong></option>\n";
		}
		$ramka->body .= "</select></label></p>";

		$ramka->body .= "<p><label class='custom-select'><select class='info' name='ref' id='tol_ref'>".
						"<option disabled selected value='0'>Referencja</option>".
						"</select></label></p>";
		$ramka->body .= "<p>";

		$ramka->body .= "<label class='custom-select'><select class='info' name='indeks' id='indeks' style='width:97px'>".
						"<option disabled selected value='0'>Index</option>";
			for ($i = 1; $i < 20; $i++) {
				if ($i<15) $o = 0; else $o = 1;
				$ramka->body .= "<option value='$i'>$i &#".(9397+$i+$o).";</option>";
			}
		$ramka->body .= "</select></label></p>";

		$ramka->drukuj();
	$modul->tol_referencja = ob_get_clean();

	ob_start();				# a3 tolerancje
		$ramka->poziom($poziom);
		$ramka->head($lang->B_TOLERANCJE);
		$ramka->body = "<table id='a3Tolerancje' class='a3Tolerancje'>".
			"<tr><th>$lang->TH_STREFA</th><th>Nominał</th><th>Odchylenie</th><th>Zapisz</th></tr>".
			"</table>";
		$ramka->drukuj();
	$modul->tolerancje = ob_get_clean();
}

if ($input->get('page') == "a7") {# zarządzanie

	ob_start();		# profil
		$ramka->poziom($poziom);
		$ramka->head("Użytkownicy");

		$sql = $mysqli->query("SELECT * FROM users");

		/*$ramka->body = "<table id='geo_lista'><tr><th>Nazwisko i Imię</th><th>Login</th><th>Grupa</th><th></th></tr>";

		while($user = $sql->fetch_object()) {
			$ramka->body .= "<tr style='height:62px' id='id_".$user->id."'><td><span class='s_".$user->id."'>".$user->name."</span></td><td><span class='s_".$user->id."'>".$user->login."</span></td><td><span class='s_".$user->id."'>".
				"<label class='custom-select'>".
				"<select style='width:126px'>".
					"<option value='X' ".(($user->grupa == 'X')?'selected':'').">X</option>".
					"<option value='G' ".(($user->grupa == 'G')?'selected':'').">G #</option>".
					"<option value='C' ".(($user->grupa == 'C')?'selected':'').">C ##</option>".
					"<option value='U' ".(($user->grupa == 'U')?'selected':'').">U ###</option>".
					"<option value='A' ".(($user->grupa == 'A')?'selected':'').">A ####</option>".
					"<option value='S' ".(($user->grupa == 'S')?'selected':'').">S #####</option>".
				"</select></label></span>".
			"<td><button name='id_".$user->id."' class='butn user'><img src='/phpmyadmin/themes/original/img/b_edit.png' /></button></td></tr>";
		}
		$ramka->body .= "<tr style='height:15px'></tr>";
		$ramka->body .= "<tr><td colspan='4'><button class='butn' style='width:auto'>Dodaj użytkownika</button></td></tr>";
		$ramka->body .= "</table>";*/

		$ramka->body = "<div class='table'><div class='head-row'><div class='head'>Login</div><div class='head'>Nazwisko i Imię</div><div class='head'>Grupa</div><div class='head'></div></div>";
		while($user = $sql->fetch_object()) {
			$ramka->body .= "<div class='row' style='height:62px' id='id_".$user->id."'>".
				"<div class='cell'><span class='s_".$user->id."'>".$user->login."</span></div>".
				"<div class='cell'><span class='name_".$user->id."'>".$user->name."</span></div>".
				"<div class='cell'><span class='s_".$user->id."'>".
				"<label class='custom-select'>".
				"<select style='width:126px'>".
					"<option value='X' ".(($user->grupa == 'X')?'selected':'').">X</option>".
					"<option value='G' ".(($user->grupa == 'G')?'selected':'').">G #</option>".
					"<option value='C' ".(($user->grupa == 'C')?'selected':'').">C ##</option>".
					"<option value='U' ".(($user->grupa == 'U')?'selected':'').">U ###</option>".
					"<option value='A' ".(($user->grupa == 'A')?'selected':'').">A ####</option>".
					"<option value='S' ".(($user->grupa == 'S')?'selected':'').">S #####</option>".
				"</select></label></span>".
			"<button name='id_".$user->id."' class='butn user'><img src='/phpmyadmin/themes/original/img/b_edit.png' /></button></div></div>";
			$ramka->body .= "<div style='display:none' class='row row2 ic_".$user->id."'>".
					"<div class='id_".$user->id."'><span>e-mail:</span></div>".
					"<div class='id_".$user->id."'><input class='input' type='e-mail' autocomplete='false' /></div>".
					"<div class='id_".$user->id."'><button class='butn adm'>Uprawnienia niestandardowe</button></div>".
				"</div>".
				"<div style='display:none' class='row row2 ic_".$user->id."'>".
					"<div class='id_".$user->id."'><span>Hasło:</span></div>".
					"<div class='id_".$user->id."'><input class='input' readonly type='password' value='12345678' /></div>".
					"<div class='id_".$user->id."'><button class='butn adm'>Resetuj hasło</button></div>".
				"</div>";
		}
		$ramka->body .= "<div class='row'style='height:15px'></div></div>";
		$ramka->body .= "<div class='table'><div class='row'><div class='cell'><button class='butn' style='width:auto'>Dodaj użytkownika</button></div></div></div>";


		$ramka->drukuj();
	$modul->a7_profil = ob_get_clean();

	ob_start();		# legenda
		$ramka->poziom("#345792");
		$ramka->head("Legenda");
		$ramka->body = "<table id='legenda'><thead><tr><th>Grupa</th><th>Uprawnienia</th></thead></tr>".
						"<tbody>".
						"<tr><td>X</td><td>&middot; brak dostępu</td>".
						"<tr><td>Gość</td><td>&middot; statystyki<br/>&middot; zlecanie badań<br/>&middot; własne raporty</td>".
						"<tr><td>Customer</td><td>&middot; raporty<br/>&middot; analizy</td>".
						"<tr><td>Użytkownik</td><td>&middot; tworzenie wyników<br/>&middot; tworzenie raportów badań</td>".
						"<tr><td>Administrator</td><td>&middot; zarządzanie próbkami<br/>&middot; zarządzanie projektami<br/>&middot; dodawanie i edycja tolerancji</td>".
						"<tr><td>Super Admin</td><td>&middot; dodawanie użytkowników<br/>&middot; analizy wydajności<br/>&middot; modyfikacja danych<br/>&middot; wgląd do audytu</td>".
						"</tbody></table>";
		$ramka->drukuj();
	$modul->a7_legend = ob_get_clean();

	ob_start();		# audit
		$ramka->poziom($poziom);
		$ramka->head("Audit trail");
		$ramka->body = "<pre style='font-size: xx-small'>";

		//$audit = "geo_raport";

		//$audit_ext = array(
		//	"geo_raport" => "'id', 'idd', 'r_update', 'r_create'"
		//);

		//$sql = $mysqli->query("SHOW COLUMNS FROM $audit WHERE FIELD NOT IN ($audit_ext[$audit])");

		//$ramka->body .= "BEGIN\nSET @nUser = REPLACE(REPLACE(USER(), 'lims_', ''), '@localhost', '');\n\n";

		//# UPDATE
		///*	while ($col = $sql->fetch_object()) {
		//		$ramka->body .= "IF OLD.".$col->Field." <> NEW.".$col->Field." THEN\n";
		//		$ramka->body .= "\tINSERT INTO audit (user_name, idd, old_data, new_data, tbl_name, col_name)\n";
		//		$ramka->body .= "\tVALUES (@nUser, OLD.id, OLD.".$col->Field.", NEW.".$col->Field.", \"".$audit."\", \"".$col->Field."\");\n";
		//		$ramka->body .= "END IF;\n\n";
		//	}*/
		//# INSERT
		//	while ($col = $sql->fetch_object()) {
		//		$ramka->body .= "IF NEW.".$col->Field." <> '' THEN\n";
		//		$ramka->body .= "\tINSERT INTO audit (user_name, idd,  new_data, tbl_name, col_name)\n";
		//		$ramka->body .= "\tVALUES (@nUser, NEW.id, NEW.".$col->Field.", \"".$audit."\", \"".$col->Field."\");\n";
		//		$ramka->body .= "END IF;\n\n";
		//	}
		//# DELETE
		///*	while ($col = $sql->fetch_object()) {
		//		$ramka->body .= "IF OLD.".$col->Field." <> '' THEN\n";
		//		$ramka->body .= "\tINSERT INTO audit (user_name, idd, old_data, tbl_name, col_name)\n";
		//		$ramka->body .= "\tVALUES (@nUser, OLD.id, OLD.".$col->Field.", \"".$audit."\", \"".$col->Field."\");\n";
		//		$ramka->body .= "END IF;\n\n";
		//	}*/

		$ramka->body .= "END</pre>";
		$ramka->drukuj();
	$modul->audit = ob_get_clean();
}

if ($input->get('page') == "a8") {# tłumaczenia
	ob_start();				# a8 menu
		$var = array($lang->L_POLSKI,$lang->L_ANGIELSKI,$lang->L_NIEMIECKI,$lang->L_FRANCUSKI);
		for($i = 0; $i<count($var); $i++) { // tworzymy wszystkie możliwe kombinacje.
			for ($n = 1; $n<count($var); $n++) {
				if ($i != $n) {
					$kom[] = array($var[$i],$var[$n],$i.$n); 
				}
			}
		}
		$ramka->poziom($poziom);
		$ramka->head($lang->L_TR_DIR); // kierunek tłumaczenia
		$ramka->body = "<p><label class='custom-select'><select name='$lan' id='trans' onchange=\"javascript:window.location='/$lan-a8-' + this.options[this.selectedIndex].value + '.lims';\">";
		$ramka->body .= "<option disabled selected value='0'>$lang->B_TRANSLATE</option>\n";
		foreach($kom as $l) {
			$ramka->body .= "<option ".(($_GET['tr']==$l[2])?"selected ":"")."value='".$l[2]."'><strong>".$l[0]." > ".$l[1]."</strong></option>\n";
		}
		$ramka->body .= "</select></label></p>";
		//$ramka->body .= substr($_SERVER[REQUEST_URI], 3);
		$ramka->drukuj();
	$modul->a8_menu = ob_get_clean();

	ob_start();				# a8 main
		$var = array("pl","en","de","fr");
		$lang1 = $var[$_GET['tr'][0]];
		$lang2 = $var[$_GET['tr'][1]];
		$sql = $mysqli->query("SELECT string, ".$lang1." as a, ".$lang2." as b FROM w_lang_pivot");
		$ramka->poziom($poziom);
		$ramka->head($lang->B_TRANSLATE);
		$ramka->body = "<table style='margin:auto;width:95%' id='translate'>";
		while ($res = $sql->fetch_object()) {
			if(empty($res->a)) continue;
			//$ramka->body .= "<p>$res->string > $res->a > $res->b</p>";
			$ramka->body .=  "\n\t<tr id='TR_$res->string'>\n\t\t<td><input readonly title='$res->string' class='input trans' name='$lang1' value='$res->a' /> ".
								"\n\t\t<input class='input trans' id='T_$res->string' name='$lang2' value='$res->b' /> ".
								"\n\t\t<th id='$res->string'><input class='input send' type='submit' id='Z_$res->string' /></td>\n\t</tr>";
		}
		$ramka->body .= "</table>";
		if ($lang1 == 'pl') $ramka->body .= "<p>&nbsp;</p><span>$lang->S_ADD_NEW:</span><p id='nowy' style='padding:8px'><input class='input piana' name='nowy_str' id='nowy_str' placeholder='STRING' /><input class='input piana' name='nowy_tr' id='nowy_tr' placeholder='text' /><input class='input send' type='submit' id='nowy_zapisz' /></p>";
		$ramka->drukuj();
	$modul->a8_main = ob_get_clean();
}
ob_start();				# a8 menu
$modul->a0_menu = ob_get_clean();

if ($input->get('page') == 'fp') {
	ob_start();				# fp
		$sql = $mysqli->query("SELECT id_ref as id, ref_numb as numb, linia, nazwa FROM ref LEFT JOIN projekt ON projekt.id_projekt = ref.projekt_id ORDER BY nazwa ASC, linia ASC, RIGHT(ref_numb, 4) ASC");

		$ramka->poziom($poziom);
		$ramka->head($lang->B_FIRSTPART_PAGE);
		$ramka->body = "<table id='fp'>";
		$proj = "";
		$szt  = 20;
		while ($ref = $sql->fetch_object()) {
			if ($ref->nazwa != $proj) {
				$proj = $ref->nazwa;
				$ramka->body .= "\n\t<tr><td colspan='10' style='text-align:center;background-color:darkseagreen'>".$ref->nazwa."</td></tr>";
			}
			foreach(str_split($ref->linia) as $linia) {
				$ramka->body .= "\n\t<tr id='tr_".$ref->id."L".$linia."' ".(($koloruj)?"style='background-color:silver'":"").">".
					"<td id='pr_".$ref->id."L".$linia."'>".$ref->nazwa."</td>".
					"<td id='re_".$ref->id."L".$linia."'>".substr($ref->numb, -4). "</td>".
					"<td id='ln_".$ref->id."L".$linia."'>L".$linia."</td>\n";
				$ramka->body .= "<td id='td_".$ref->id."L".$linia."' style='text-align:center;width:30px'>-</td>\n<td>";
				for ($i = 0; $i <= $szt; $i++) {
					//if ($i % 5 == 0) $ramka->body .= "</td><td>";
					$ramka->body .= "<input class='fp' type='radio' ".(($i)?"":"checked")." name='".$ref->id."L".$linia."' value='$i'/>";
					if ($i % 5 == 0) $ramka->body .= "</td><td>";
				}
				$koloruj = !$koloruj;
				$ramka->body .= "\n\t&laquo;</td></tr>";
			}
		}
		$ramka->body .= "\n</table>";
		$ramka->drukuj();
	$modul->fp = ob_get_clean();
}

$ax = array(7.35,7.64,7.25,7.45,7.62,7.05,7.91,7.12,7.54,8.85,8.62,8.41,9.09,9.17,6.28,6.35,6.71,6.47,7.10,5.92);
$ay = array(19.95,20.27,19.72,21.02,20.01,18.71,20.74,20.05,20.23,21.91,21.27,21.09,21.56,22.13,17.53,18.22,18.80,17.59,18.10,15.48);

$table = "";
for ($i = 0; $i < count($ax); $i++) {
	$table .= ", [".$ax[$i].",".$ay[$i]."]";
}

ob_start();
?>

<!DOCTYPE html>
<html lang="pl-PL">
	<head>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
		<title>LIMS</title>

		<input type="hidden" id="pageLang" name="pageLang" value="<? echo $_GET['lang'] ?>" />

		<link rel="stylesheet" href="<?= noCache("style.css") ?>" type="text/css">
		<link rel="stylesheet" href="<?= noCache("style_radio.css") ?>" type="text/css">
		
		<script type="text/javascript" src="<?= noCache("ajax_script.js") ?>"></script>
		<script type="text/javascript" src="<?= noCache("script_".$input->get('page').".js") ?>"></script>

		<script type="text/javascript">
			$(document).ready(function(){
				$(".langMe").on('change',function() {
					var id = $(this).attr('id');
					var lang = $("#pageLang").val();
					var txt = $(this).val();
					$.ajax({
						url: 'ajax_a8.php',
						type: 'post',
						data: {translate:lang,string:id,text:txt},
						dataType: 'json',
						success:function(response){
							backBlink("s_"+id, "OK", "black");
							$.ajax({
								url: 'lang.php',
								type: 'post'
							});
						},
						error:function(){
							backBlink("s_"+id, "NG", "black");
						}
					});
				});
			});
		</script>

		<? if (empty($input->get('page')) OR $input->get('page') == 'u0'): ?>
			<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
			
			<script type="text/javascript">
				google.charts.load('current', {'packages':['corechart']});

				google.charts.setOnLoadCallback(drawChart);
				function drawChart() {
					var data = google.visualization.arrayToDataTable([
						['kPa', 'HSP']<?php echo $table; ?>]);

					var options = {
						chartArea:{width:'80%',height:'75%'},
						title: 'Correlation P21 598X Central: <?php echo round(Corr($ay, $ax)*100,1)."%"; ?>',
						hAxis: {title: 'kPa'},
						vAxis: {title: 'HSP'},
						legend: 'none',
						trendlines: {
							0: {
								showR2: false,
								lineWidth: 20,
								opacity: 0.2,
								visibleInLegend: false
							}
						}
					};

					var chart = new google.visualization.ScatterChart(document.getElementById('correlation'));
					chart.draw(data, options);
				}
			</script>
			<script type="text/javascript">
				google.charts.load('current', {'packages':['corechart']});
				google.charts.setOnLoadCallback(drawVisualization);

				function drawVisualization() {
				// Some raw data (not necessarily accurate)
				var data = google.visualization.arrayToDataTable([
					['Project', 'NOK', 'Pareto'],
					['P21',  256,      38.4],
					['B9',  203,      68.8],
					['VW',  101,      84],
					['P87',  95,      98.2],
					['Q7',  12,      100]
				]);

				var options = {
					chartArea:{width:'80%',height:'75%'},
					title : 'Pareto NOK results on projects',
					legend: 'none',
					seriesType: 'bars',
					series: {
						0: {targetAxisIndex: 0},
						1: {targetAxisIndex: 1, type: 'line'}
					},
					vAxis: {
						0: {title: 'NOK'},
						1: {title: '[%]'}
					},
					hAxis: {title: 'Projects'},
				};

				var chart = new google.visualization.ComboChart(document.getElementById('pareto'));
				chart.draw(data, options);
				}
			</script>

			<script type="text/javascript">
				google.charts.load('current', {'packages':['corechart']});

				google.charts.setOnLoadCallback(drawChart);
				function drawChart() {
				var data = google.visualization.arrayToDataTable([
					['Date', 'P21', 'P87', 'VW', 'A9','B9', 'R8', { role: 'annotation' } ],
					['01.07', 1, 0.5, 0.65, 1, 0.2, 0.35, ''],
					['02.07', 1, 0.5, 0.65, 1, 0.2, 0.35, ''],
					['03.07', 1, 0.5, 0.65, 1, 0.2, 0.35, ''],
					['04.07', 1, 0.5, 0.65, 1, 0.2, 0.35, ''],
					['05.07', 1, 0.5, 0.65, 1, 0.2, 0.35, ''],
					['06.07', 0.99, 0.26, 0.65, 0.62, 1, 0.1, ''],
					['07.07', 0.95, 0.33, 0.65, 0.59, 0.91, 0.15, '']
				]);

				var options = {
					chartArea:{width:'80%',height:'75%'},
					title : 'Performing weekly schedule',
					legend: { position: 'bottom', maxLines: 3 },
					bar: { groupWidth: '75%' },
					isStacked: true,
				};
				var chart = new google.visualization.ColumnChart(document.getElementById("plan"));
				chart.draw(data, options);
				}
			</script>
		<? endif; ?>
	</head>

	<body class='layout-boxed'>
	<?

	//print_r($_SERVER);

	echo
		"<div class='menu no-print'>".
			$modul->menu .
		"</div>";

		$strona = (object) array(

			"a1" => array(			# projekty
				0 => '',
				1 => explode(" ", "a1_projekt"),
				2 => explode(" ", "a1_info")
			),
			"a2" => array(			# referencje
				0 => '',
				1 => explode(" ", "a2_projekt"),
				2 => explode(" ", "a2_strefy")
			),
			"a3" => array(			# tolerancje
				0 => '',
				1 => explode(" ", "tol_referencja badanie"),
				2 => explode(" ", "tolerancje")
			),
			"a7" => array(			# zarządzanie
				0 => '',
				1 => explode(" ", "a7_profil"),
				2 => explode(" ", "a7_legend")
			),
			"a8" => array(			# tłumaczenia
				0 => explode(" ","a8_menu a8_main"),
				1 => '',
				2 => ''
			),
			"fp" => array(			# pierwsze sztuki
				0 => explode(" ","fp"),
				1 => '',
				2 => ''
			),
			"u1" => array(			# pomiary
				0 => '',
				1 => explode(" ", "referencja badanie"),
				2 => explode(" ", "pomiary")
			),
			"u3" => array(			# pomiary
				0 => '',
				1 => explode(" ", "referencja badanie"),
				2 => explode(" ", "pomiary")
			),
		);

		//$strona = json_decode(json_encode((object) $strona), FALSE);

		$strona = (object) $strona->a1;

		//print_r($strona);

	echo "<div id='box'>";
	switch ($input->get('page')) {

		case "a1":			# projekty
			echo
			"<div class='main'>".
				$modul->a1_projekt;
				//$mod->load("a1","php");
			echo "</div>".
			"<div class='main'>".
				$modul->a1_info .
			"</div>";
		break;
		case "a2":			# referencje
			echo
			"<div class='main'>".
				$modul->a2_projekt .
			"</div>".
			"<div class='main'>".
				$modul->a2_strefy .
			"</div>";
		break;
		case "a3":			# tolerancje
			echo
			"<div class='main'>".
				$modul->tol_referencja .
				$modul->badanie .
			"</div>".
			"<div class='main'>".
				$modul->tolerancje .
			"</div>";
		break;
		case "a7":			# zarządzanie
			//echo
			//"<div class='mainWidth'>".
			//	$modul->audit.
			//"</div>";
			echo
			"<div class='main'>".
				$modul->a7_profil .
			"</div>".
			"<div class='main'>".
				$modul->a7_legend .
			"</div>";
		break;
		case "a8":			# tłumaczenia
			echo
			"<div class='mainWidth'>".
				$modul->a8_menu.
				$modul->a8_main.
			"</div>";
		break;
		case "fp":
			echo
			"<div class='mainWidth'>".
				$modul->fp.
			"</div>";
		break;
		case "u1":			# pomiary
			echo
			"<div class='main'>".
				$modul->referencja .
				$modul->badanie .
			"</div>".
			"<div class='main'>".
				$modul->pomiary .
			"</div>";
		break;
		case "u3":
			if (isset($_GET['id'])) {
				include("geo_report.php");
				echo
				"<div class='main'>".
					$modul->geo_1 .
					$modul->geo_2 .
					$modul->geo_3 .
					$modul->geo_4 .
				"</div>".
				"<div class='main'>".
					$modul->geo_5 .
				"</div>";
			} else {
				echo
				"<div class='mainWidth'>";
				include("geo_lista.php");
				echo "</div>";
			}
		break;

		default:
		case "u0":
					# strona główna
			echo
			"<div class='main'>".
				"<div id='correlation' style='width:100%; height:35%'></div>".
				"<div id='pareto' style='width:100%; height:35%'></div>".
			"</div>".
			"<div class='main'>".
				"<div id='plan' style='width:100%; height:35%'></div>".
			"</div>";
		break;
	}

	if ($cache->start("foot")) {
		echo "</div>";
		echo "<div id='footer'>Copyright &copy; 2019-".date('Y')." :: Zalogowany: -".$_SERVER['REMOTE_USER']."-<br/>Raporty: ".dir_size("rep")." :: Sekcje: ".dir_size("section");
			//echo str_replace(".php", ".log", $_SERVER['SCRIPT_FILENAME']);
			echo " [ " . $input->get('page') . "  / " . $input->get('lang') . " ] ";
		echo "</div>";
	}
	echo $cache->stop();

	?>
	</body>
</html>

<?
ob_end_flush();


// korelacja
function Corr($x, $y){
	    if(count($x)!==count($y)){return -1;}   
		$x=array_values($x);
		$y=array_values($y);    
		$xs=array_sum($x)/count($x);
		$ys=array_sum($y)/count($y);    
		$a=0;$bx=0;$by=0;
		for($i=0;$i<count($x);$i++){     
			$xr=$x[$i]-$xs;
			$yr=$y[$i]-$ys;     
			$a+=$xr*$yr;        
			$bx+=pow($xr,2);
			$by+=pow($yr,2);
		}   
		$b = sqrt($bx*$by);
		//return round(pow($a/$b,2)*100,1)."%";
		return pow($a/$b,2);
}
function noCache($file) {
	return $file."?".filemtime($file);
}
/*function randomID() {
    $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 8; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}*/

function dir_size($dir) {
    $dir = '/var/www/labtool/'.$dir;
	$units = explode(' ', 'B KiB MiB GiB TiB PiB');
	$size = 0;
	$files = -2;
    foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir)) as $file){
        $size+=$file->getSize();
		$files++;
    }

	$mod = 1024;
    for ($i = 0; $size > $mod; $i++) {
        $size /= $mod;
    }

    $endIndex = strpos($size, ".")+3;

    return $files . " (" . substr( $size, 0, $endIndex)." ".$units[$i] .")";
}


?>
