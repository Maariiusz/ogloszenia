<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>index Praca Trojmiasto.pl</title>
    <link rel="stylesheet" href="/pro/style.css">
	 <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<link rel="stylesheet" href="/resources/demos/style.css">
	<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<script>
	( function( factory ) {
		if ( typeof define === "function" && define.amd ) {

			// AMD. Register as an anonymous module.
			define( [ "../widgets/datepicker" ], factory );
		} else {

			// Browser globals
			factory( jQuery.datepicker );
		}
	}( function( datepicker ) {

	datepicker.regional.pl = {
		closeText: "Zamknij",
		prevText: "&#x3C;Poprzedni",
		nextText: "Następny&#x3E;",
		currentText: "Dziś",
		monthNames: [ "Styczeń","Luty","Marzec","Kwiecień","Maj","Czerwiec",
		"Lipiec","Sierpień","Wrzesień","Październik","Listopad","Grudzień" ],
		monthNamesShort: [ "Sty","Lu","Mar","Kw","Maj","Cze",
		"Lip","Sie","Wrz","Pa","Lis","Gru" ],
		dayNames: [ "Niedziela","Poniedziałek","Wtorek","Środa","Czwartek","Piątek","Sobota" ],
		dayNamesShort: [ "Nie","Pn","Wt","Śr","Czw","Pt","So" ],
		dayNamesMin: [ "N","Pn","Wt","Śr","Cz","Pt","So" ],
		weekHeader: "Tydz",
		dateFormat: "yy-mm-dd",
		maxDate: "+0d",
		firstDay: 1,
		isRTL: false,
		showMonthAfterYear: false,
		yearSuffix: "" };
	datepicker.setDefaults( datepicker.regional.pl );

	return datepicker.regional.pl;

	} ) );
	$( function() {
		$( "#datepicker" ).datepicker();
	} );
	</script>

	
</head>
<?php
	$ilosc_wynikow = 20;
	$dbconn = pg_connect("host=localhost dbname=trojmiasto user=trojmiasto password=trojmiasto") or die('Nie można nawiązać połączenia: ' . pg_last_error());
	function menu(){
	echo "<div id='menu'><div id='link_menu'><a href='/pro/index.php?strona=wszystko&nr_strony=1'>Wyświetl wszystkie ogłoszenia</a></div>
			<div id='link_menu'><a href='/pro/index.php?strona=data'>Wyszukaj ogłoszenia po dacie utworzenia</a></div>
			<div id='link_menu'><a href='/pro/index.php?strona=firma'>Wyszukaj ogłoszenia danej firmy</a></div>
			<div id='link_menu'><a href='/pro/index.php?strona=kategorie'>Wyszukaj ogłoszenia z danej kategorii</a></div></div>";}
	function link_wsystkie_rekordy($nr_strony, $nazwa_strony, $info){
		echo "<a href='/pro/index.php?strona=wszystko&nr_strony=". $nr_strony ."'>". $nazwa_strony ."</a>";
	}
	function link_data($nr_strony, $nazwa_strony, $data){
		echo "<a href='/pro/index.php?strona=data&data=".$data."&nr_strony=". $nr_strony ."'>". $nazwa_strony ."</a>";
	}
	function link_kategorie($nr_strony, $nazwa_strony, $kategoria){
		echo "<a href='/pro/index.php?strona=kategorie&kategoria=".$kategoria."&nr_strony=". $nr_strony ."'>". $nazwa_strony ."</a>";
	}
	function link_firma($nr_strony, $nazwa_strony, $firma){
		echo "<a href='/pro/index.php?strona=firma&nr=".$firma."&nr_strony=". $nr_strony ."'>". $nazwa_strony ."</a>";
	}
	function link_alfabet($nr_strony, $nazwa_strony, $litera){
		echo "<a href='/pro/index.php?strona=firma&alfabet=".$litera."&nr_strony=". $nr_strony ."'>". $nazwa_strony ."</a>";
	}
	function aktualna_strona($nazwa_strony){
		echo " ". $nazwa_strony ." ";
	}
	function nawigacja ($strona, $zapytanie, $link, $ilosc_wynikow, $info){
		$query = $zapytanie;
		$result = pg_query($query) or die('Nieprawidłowe zapytanie: ' . pg_last_error());
		$line = pg_fetch_array($result, null, PGSQL_ASSOC);
		$ilosc_stron = ceil($line['count']/$ilosc_wynikow);
		echo "<div id='nawigacja'><div id='odstep'>  </div><div id='sterowanie'>";
		if ($ilosc_stron <= 1){
		
		}else if ($ilosc_stron == 2){
			if ($strona == 1){	
					echo aktualna_strona(1) . $link(2, 2, $info) . $link($strona + 1, 'Następna strona', $info);
				} else if ($strona == 2){	
					echo $link($strona - 1, 'Porzednia strona', $info) . $link(1, 1, $info) . aktualna_strona(2);
				}
		}
		else if ($ilosc_stron == 3){
			if ($strona == 1){	
					echo aktualna_strona(1) . $link(2, 2, $info) . $link(3, 3, $info) . $link($strona + 1, 'Następna strona', $info);
				} else if ($strona == 2){	
					echo $link($strona - 1, 'Porzednia strona', $info) . $link(1, 1, $info) . aktualna_strona(2) . $link(3, 3, $info) . $link($strona + 1, 'Następna strona', $info);
				} else if ($strona == 3){	
					echo $link($strona - 1, 'Porzednia strona', $info) . $link(1, 1, $info) . $link(2, 2, $info) . aktualna_strona(3);
				}
				}
		else if ($ilosc_stron == 4){
			if ($strona == 1){	
					echo aktualna_strona(1) . $link(2, 2, $info) . $link(3, 3, $info) . $link(4, 4, $info) . $link($strona + 1, 'Następna strona', $info);
				} else if ($strona == 2){	
					echo $link($strona - 1, 'Porzednia strona', $info) . $link(1, 1, $info) . aktualna_strona(2) . $link(3, 3, $info) . $link(4, 4, $info). $link($strona + 1, 'Następna strona', $info);
				} else if ($strona == 3){	
					echo $link($strona - 1, 'Porzednia strona', $info) . $link(1, 1, $info) . $link(2, 2, $info) . aktualna_strona(3) . $link(4, 4, $info). $link($strona + 1, 'Następna strona', $info);
				} else if ($strona == 4){	
					echo $link($strona - 1, 'Porzednia strona', $info) . $link(1, 1, $info) . $link(2, 2, $info) . $link(3, 3, $info) . aktualna_strona(4);
				}
				
		}else {
			if ($strona <= 3){
				if ($strona == 1){	
					echo aktualna_strona(1) . $link(2, 2, $info) . $link(3, 3, $info) . $link(4, 4, $info) . aktualna_strona(" ... ") .$link($ilosc_stron, $ilosc_stron, $info). $link($strona + 1, 'Następna strona', $info);
				} else if ($strona == 2){	
					echo $link($strona - 1, 'Porzednia strona', $info) . $link(1, 1, $info) . aktualna_strona(2) . $link(3, 3, $info) . $link(4, 4, $info) . aktualna_strona(" ... ") .$link($ilosc_stron, $ilosc_stron, $info). $link($strona + 1, 'Następna strona', $info);
				} else {
					echo $link($strona - 1, 'Porzednia strona', $info) . $link(1, 1, $info) . $link(2, 2, $info) . aktualna_strona(3) . $link(4, 4, $info) . aktualna_strona(" ... ") .$link($ilosc_stron, $ilosc_stron, $info). $link($strona + 1, 'Następna strona', $info);
				}
			} else if ($strona >= $ilosc_stron-2){
				if ($strona == $ilosc_stron){
					echo $link($strona - 1, 'Porzednia strona', $info) . $link(1, 1, $info) . aktualna_strona(" ... ").$link($ilosc_stron -3, $ilosc_stron -3, $info) . " " .$link($ilosc_stron -2, $ilosc_stron -2, $info) . " ". $link($ilosc_stron -1, $ilosc_stron -1, $info) . " ". aktualna_strona($ilosc_stron);
				} else if ($strona == $ilosc_stron-1){
					echo $link($strona - 1, 'Porzednia strona', $info) . $link(1, 1, $info) . aktualna_strona(" ... ").$link($ilosc_stron -3, $ilosc_stron -3, $info) . " " .$link($ilosc_stron -2, $ilosc_stron -2, $info) . " ". aktualna_strona($ilosc_stron -1) . " ".$link($ilosc_stron, $ilosc_stron, $info). $link($strona + 1, 'Następna strona', $info);
				} else {
					echo $link($strona - 1, 'Porzednia strona', $info). $link(1, 1, $info) . aktualna_strona(" ... ").$link($ilosc_stron -3, $ilosc_stron -3, $info) . " " .aktualna_strona($ilosc_stron -2) . " ". $link($ilosc_stron -1, $ilosc_stron -1, $info) . " ".$link($ilosc_stron, $ilosc_stron, $info). $link($strona + 1, 'Następna strona', $info);
				}
			} else {
				echo $link($strona - 1, 'Porzednia strona', $info) . $link(1, 1, $info) . aktualna_strona(" ... ") . $link($strona-1, $strona-1, $info) . " " . aktualna_strona($strona) . " ". $link($strona+1, $strona+1, $info). aktualna_strona(" ... ") .$link($ilosc_stron, $ilosc_stron, $info). $link($strona + 1, 'Następna strona', $info);
			}
		}
		echo "</div><div id='odstep'>  </div></div>";
	}
	function pobieranie_wszystkich_rekordów($strona){
		global $ilosc_wynikow;
		$strona_sql = ($strona - 1) * $ilosc_wynikow;
		$query = "select data, tytul, nazwa_firmy, miejscowosc, zarobki_min, zarobki_max, opis from ogloszenia order by data desc limit " . $ilosc_wynikow ." offset " . $strona_sql;
		$result = pg_query($query) or die('Nieprawidłowe zapytanie: ' . pg_last_error());
		while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)){
			echo $line['data'] . " " . $line['tytul'] . "<br />";
			echo $line['nazwa_firmy'] . " " . $line['miejscowosc'] . "<br />";
			if ($line['zarobki_min'] != 0){
				echo "Od: " . $line['zarobki_min'] . "zł Do: " . $line['zarobki_max'] . "zł<br />";
			}
			echo $line['opis'] . "<br /><br />";
		}
		$zapytanie = "select count(*) from ogloszenia";
		nawigacja($strona, $zapytanie, 'link_wsystkie_rekordy', $ilosc_wynikow, 0);
	}
	
	function pobieranie_kategorii(){
		$query = "select distinct nazwa_kategori from kategorie order by nazwa_kategori";
		$result = pg_query($query) or die('Nieprawidłowe zapytanie: ' . pg_last_error());
		while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)){
			echo "<div id='link'><a href='/pro/index.php?strona=kategorie&kategoria=". $line['nazwa_kategori'] ."&nr_strony=1'>". $line['nazwa_kategori'] ."</a></div>";
		}
	}
	
	function pobieranie_po_dacie(){
		$query = "select distinct data from ogloszenia order by data desc";
		$result = pg_query($query) or die('Nieprawidłowe zapytanie: ' . pg_last_error());
		while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)){
			echo "<div id='link'><a href='/pro/index.php?strona=data&data=". $line['data'] ."&nr_strony=1'>". $line['data'] ."</a></div>";
		}
	}
	
	function pobieranie_po_nazwie_firmy($wyszukiwanie){
		if ($wyszukiwanie){
			$query = "select id, nazwa_firmy from ogloszenia where lower(nazwa_firmy) like lower('%".  addslashes($_GET['wfirma']) ."%') order by nazwa_firmy";
		} else {
			$query = "select id, nazwa_firmy from ogloszenia order by nazwa_firmy";
		}
		$result = pg_query($query) or die('Nieprawidłowe zapytanie: ' . pg_last_error());
		$poprzednia_firma = " ";
		while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)){
			if ($poprzednia_firma != $line['nazwa_firmy']){
				echo "<div id='link'><a href='/pro/index.php?strona=firma&nr=". $line['id'] ."&nr_strony=1'>". $line['nazwa_firmy'] ."</a></div>";
				$poprzednia_firma = $line['nazwa_firmy'];
			}
		}
	}
	function alfabet_firma(){
		$alfabet = array('a', 'ą', 'b', 'c', 'ć', 'd', 'e', 'ę', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'ł', 'm', 'n', 'ń', 'o', 'ó', 'p', 'r', 's', 'ś', 't', 'u', 'w', 'y', 'z', 'ź', 'ż', 0, 1, 2, 3, 4, 5, 6, 7, 8, 9);
		foreach ($alfabet as $wartsc) {
			echo "<div id='link'><a href='/pro/index.php?strona=firma&alfabet=". $wartsc ."&nr_strony=1'>". $wartsc ."</a></div>";
		}
	}
	function alfabet_firma_litera($litera, $strona){
		$ilosc_wynikow_alfabet = 40;
		$strona_sql = ($strona - 1) * $ilosc_wynikow_alfabet;
		$query = "select id, nazwa_firmy from ogloszenia where lower(nazwa_firmy) like ('". $litera ."%') order by nazwa_firmy limit " . $ilosc_wynikow_alfabet ." offset " . $strona_sql;
		$result = pg_query($query) or die('Nieprawidłowe zapytanie: ' . pg_last_error());
		$poprzednia_firma = " ";
		while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)){
			if ($poprzednia_firma != $line['nazwa_firmy']){
				echo "<div id='link'><a href='/pro/index.php?strona=firma&nr=". $line['id'] ."&nr_strony=1'>". $line['nazwa_firmy'] ."</a></div>";
				$poprzednia_firma = $line['nazwa_firmy'];
			}
		}
		$zapytanie = "select count(*) from ogloszenia where lower(nazwa_firmy) like ('". $litera ."%')";
		nawigacja($strona, $zapytanie, 'link_alfabet', $ilosc_wynikow_alfabet, $litera);
	}
	function pobieranie_rekordow_firmy($id, $strona){
		global $ilosc_wynikow;
		$strona_sql = ($strona - 1) * $ilosc_wynikow;
		$query = "select nazwa_firmy from ogloszenia where id =" .  addslashes($id);
		$result = pg_query($query) or die('Nieprawidłowe zapytanie: ' . pg_last_error());
		$linen = pg_fetch_array($result, null, PGSQL_ASSOC);
		$query = "select data, tytul, nazwa_firmy, miejscowosc, zarobki_min, zarobki_max, opis from ogloszenia where nazwa_firmy = '". $linen['nazwa_firmy'] . "' order by data desc limit " . $ilosc_wynikow ." offset " . $strona_sql;
		$result = pg_query($query) or die('Nieprawidłowe zapytanie: ' . pg_last_error());
		while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)){
			echo $line['data'] . " " . $line['tytul'] . "<br />";
			echo $line['nazwa_firmy'] . " " . $line['miejscowosc'] . "<br />";
			if ($line['zarobki_min'] != 0){
				echo "Od: " . $line['zarobki_min'] . "zł Do: " . $line['zarobki_max'] . "zł<br />";
			}
			echo $line['opis'] . "<br /><br />";
		}
		$zapytanie = "select count(*) from ogloszenia where nazwa_firmy = '". $linen['nazwa_firmy'] . "'";
		nawigacja($strona, $zapytanie, 'link_firma', $ilosc_wynikow, $id);
	}
	
	function pobieranie_rekordow_data($data, $strona){
		global $ilosc_wynikow;
		$strona_sql = ($strona - 1) * $ilosc_wynikow;
		$query = "select data, tytul, nazwa_firmy, miejscowosc, zarobki_min, zarobki_max, opis from ogloszenia where data = '".  addslashes($data) . "' limit " . $ilosc_wynikow ." offset " . $strona_sql;
		$result = pg_query($query) or die('Nieprawidłowe zapytanie: ' . pg_last_error());
		$line = pg_fetch_array($result, null, PGSQL_ASSOC);
		if (!$line){
			echo "Brak ogłoszeń z dnia " . $data;
		} else {
			echo $line['data'] . " " . $line['tytul'] . "<br />";
			echo $line['nazwa_firmy'] . " " . $line['miejscowosc'] . "<br />";
			if ($line['zarobki_min'] != 0){
				echo "Od: " . $line['zarobki_min'] . "zł Do: " . $line['zarobki_max'] . "zł<br />";
			}
			echo $line['opis'] . "<br /><br />";
			while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)){
				echo $line['data'] . " " . $line['tytul'] . "<br />";
				echo $line['nazwa_firmy'] . " " . $line['miejscowosc'] . "<br />";
				if ($line['zarobki_min'] != 0){
					echo "Od: " . $line['zarobki_min'] . "zł Do: " . $line['zarobki_max'] . "zł<br />";
				}
				echo $line['opis'] . "<br /><br />";
			}
		}
		$zapytanie = "select count(*) from ogloszenia where data = '".  addslashes($data) . "'";
		nawigacja($strona, $zapytanie, 'link_data', $ilosc_wynikow, $data);
	}
	
	function pobieranie_rekordow_kategria($kategoria, $strona){
		global $ilosc_wynikow;
		$strona_sql = ($strona - 1) * $ilosc_wynikow;
		$query = "select id_ogloszenia from kategorie where nazwa_kategori = '".  addslashes($kategoria) . "' order by id_ogloszenia desc limit " . $ilosc_wynikow ." offset " . $strona_sql;
		$result = pg_query($query) or die('Nieprawidłowe zapytanie: ' . pg_last_error());
		while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)){
			$query_1 = "select data, tytul, nazwa_firmy, miejscowosc, zarobki_min, zarobki_max, opis from ogloszenia where id = '". $line['id_ogloszenia'] . "'";
			$result_1 = pg_query($query_1) or die('Nieprawidłowe zapytanie: ' . pg_last_error());
			$line = pg_fetch_array($result_1, null, PGSQL_ASSOC);
			echo $line['data'] . " " . $line['tytul'] . "<br />";
			echo $line['nazwa_firmy'] . " " . $line['miejscowosc'] . "<br />";
			if ($line['zarobki_min'] != 0){
				echo "Od: " . $line['zarobki_min'] . "zł Do: " . $line['zarobki_max'] . "zł<br />";
			}
			echo $line['opis'] . "<br /><br />";
		}
		$zapytanie = "select count(*) from kategorie where nazwa_kategori = '".  addslashes($kategoria) . "'";
		nawigacja($strona, $zapytanie, 'link_kategorie', $ilosc_wynikow, $kategoria);
	}
	
	function wyszukiwanie(){
		 echo'<form action="" method="get"><input type="hidden" name="strona" value="firma" /> <input name="wfirma" value="" /><input type="submit" value="Szukaj" name="submit" />';
	}
	
	function kalendarz(){
		echo'<form action="" method="get"><input type="hidden" name="strona" value="data" /> <input type="text" id="datepicker" name="kalendarz_data"/><input type="submit" value="Szukaj" name="submit" />';
	}

?>
<body>

    <div id="wrapper">
        <section>
            <nav>
				<?php
					menu(); 
				?>
            </nav>
            <article>
                <?php
					if (isset ($_GET['strona'])){
						switch ($_GET['strona'])
						{
						case "wszystko":
							pobieranie_wszystkich_rekordów($_GET['nr_strony']);
							
							break;

						case "data":
							if (isset ($_GET['data'])){
									pobieranie_rekordow_data($_GET['data'], $_GET['nr_strony']);
								} else if (isset ($_GET['kalendarz_data'])) {
									pobieranie_rekordow_data($_GET['kalendarz_data'], $_GET['nr_strony']);
								} else {
									kalendarz();
									pobieranie_po_dacie();
								}
							break;

						case "firma":
							
							if (isset ($_GET['nr'])){
								pobieranie_rekordow_firmy($_GET['nr'], $_GET['nr_strony']);
							} else if (isset ($_GET['wfirma'])){
								wyszukiwanie();
								pobieranie_po_nazwie_firmy(1);
							
							} else if (isset ($_GET['alfabet'])){
								wyszukiwanie();
								alfabet_firma_litera($_GET['alfabet'], $_GET['nr_strony']);
							
							} else {
								wyszukiwanie();
								alfabet_firma();
							}
							break;

						case "kategorie":
							if (isset ($_GET['kategoria'])){
									pobieranie_rekordow_kategria($_GET['kategoria'], $_GET['nr_strony']);
								} else {
									pobieranie_kategorii();
								}
							break;

						default:
							echo "Niema takiej strony";
							break;
						}
					} else {
						pobieranie_wszystkich_rekordów(1);
					}
					pg_close($dbconn);
				?>
            </article>
        </section>
        <footer>
            
        </footer>
    </div>

</body>

</html>
