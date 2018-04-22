<?php
	$licznik_niezgodnosci = 0;
	ignore_user_abort(); // ustawienie tego parametru powoduje, że skrypt nie przestaje się wykonywać po wyłączeniu klienta
	set_time_limit(9000); // ustawia czas wykonywania skryptu
	$time = time(); // czas uruchomienia skryptu
	// Zmiana nazwy miesiąca z tekstowej na liczbową
	function formatowanie_daty ($data){
		$miesiac = array( '', 'stycznia', 'lutego', 'marca', 'kwietnia', 'maja', 'czerwca', 'lipca', 'sierpnia', 'września', 'października', 'listopada', 'grudnia' );
		$tablica_data = explode(" ", $data);
		$klucz = array_search($tablica_data[1],  $miesiac);
		return date("d-m-Y", mktime(0, 0, 0, $klucz, $tablica_data[0], $tablica_data[2]));
	}
	function kontrola_tekstu ($tekst){
		$tekst = trim(strip_tags($tekst));
		$tekst = str_replace ( '"', '', $tekst);
		$tekst = str_replace ( "'", "", $tekst);
		$tekst = addslashes($tekst);
		$tekst = str_replace ( '&nbsp;', ' ', $tekst);
		$tekst = str_replace ( '&oacute;', 'ó', $tekst);
		$tekst = str_replace ( '&ndash;', '-', $tekst);
		$tekst = str_replace ( '&#8217;', ' ', $tekst);
		$tekst = str_replace ( '&rdquo;', '', $tekst);
		$tekst = str_replace ( '&bdquo;', '', $tekst);
		$tekst = str_replace ( '&#40;', '(', $tekst);
		$tekst = str_replace ( '&#41;', ')', $tekst);
		return $tekst;
	}
	function pobieranie_danych_z_ogloszenia ($adres, $ostatnia_data){
		// Nawiązywanie połączenia z baza danych
		$dbconn = pg_connect("host=localhost dbname=trojmiasto user=trojmiasto password=trojmiasto") or die('Nie można nawiązać połączenia: ' . pg_last_error());
		// Pobieranie źródła strony
		$file = file_get_contents($adres);
		// Wyszukiwanie daty dodania ogłoszenia
		preg_match('#<p class="panel__desc">Dodane (.*?)</p>#', $file, $data);
		$data = strip_tags($data[1]);
		$szukany_tekst = strpos($data, '|');
		if ($szukany_tekst == true){
			$data = substr($data, 0, ($szukany_tekst-1));
		}
		$data = formatowanie_daty ($data);
		global $licznik_niezgodnosci;
		if (strtotime($ostatnia_data) <= strtotime($data)){
			// Wyszukiwanie numeru ogłoszenia
			preg_match('#data-id-ogl="(.*?)"#', $file, $nr_oferty);
			$query = "select nr_oferty from ogloszenia where nr_oferty=". $nr_oferty[1];
			$result = pg_query($query);
			$line = pg_fetch_array($result, null, PGSQL_ASSOC);
			if ($line){
				$licznik_niezgodnosci++;
			} else {
				$licznik_niezgodnosci = 0;
				// Wyszukiwanie tytyłu ogłoszenia
				preg_match('#<title>(.*?)</title>#', $file, $tytul);
				$tytul = kontrola_tekstu ($tytul[1]);
				// Wyszukiwanie kategori ogłoszenia
				preg_match('#<div id="show-branza" class="details__field"><div class="ogl__details__desc"><div class="ogl__details__desc__name">Branża / kategoria</div>(.*?)\n#s', $file, $kategorie);
				preg_match_all('#<div>(.*?)</div>#', $kategorie[1], $kategoria, PREG_SET_ORDER);

				// Wyszukiwanie nazwy firmy z ogłoszenia
				preg_match('#<div class="ogl__details__user__name">(.*?)</a>#s', $file, $nazwa_firmy);
				$nazwa_firmy = kontrola_tekstu ($nazwa_firmy[1]);
				// Wyszukiwanie treści ogłoszenia
				if (strpos($file, '<div class="ogl__description">') == true){
					preg_match('#<div class="ogl__description">(.*?)</div>#s', $file, $opis);
					$opis = kontrola_tekstu($opis[1]);
					$szukany_tekst = strpos($opis, 'Wyświetl numer');
					if ($szukany_tekst == true){
						$opis = substr($opis, 0, ($szukany_tekst - 15));
					}
					$szukany_tekst = strpos($opis, 'Wyświetl e-mail');
					if ($szukany_tekst == true){
						$opis = substr($opis, 0, ($szukany_tekst - 19));
					}
				} else {
					$opis = "";
				}
				// Wyszukiwanie miejscowści z ogłoszenia
				if (strpos($file, '<div id="show-address" class="address details__field"><div class="ogl__details__desc">') == true){
					preg_match('#<div id="show-address" class="address details__field"><div class="ogl__details__desc"><div class="ogl__details__desc__name">Adres</div>(.*?)<#', $file, $miejscowosc);
					$miejscowosc = kontrola_tekstu($miejscowosc[1]);
				} else {
					$miejscowosc = "";
				}
				// Wyszukiwanie kwoty wynagordzenia z ogłoszenia
				if (strpos($file, 'id="show-wynagrodzenie_min"') == true && strpos($file, 'id="show-wynagrodzenie_max"') == true){
					preg_match('#<div id="show-wynagrodzenie_min" class="details__field"><div class="ogl__details__desc"><div class="ogl__details__desc__name ogl--details--desc--name--currency">(.*?)</span>#', $file, $wynagrodzenie);
					preg_match('#<div id="show-wynagrodzenie_max" class="details__field"><div class="ogl__details__desc"><div class="ogl__details__desc__name ogl--details--desc--name--currency">(.*?)</span>#', $file, $w1);
					$szukany_tekst = strpos(strip_tags($wynagrodzenie[1]), '(od)');
					$zarobki_min = substr($wynagrodzenie[1], $szukany_tekst+4, -4);
					$zarobki_min = str_replace (' ', '', trim(strip_tags($zarobki_min)));
					$zarobki_max = substr($w1[1], $szukany_tekst+4, -4);
					$zarobki_max = str_replace (' ', '', trim(strip_tags($zarobki_max)));
				} else {
					$zarobki_min = 0;
					$zarobki_max = 0;
				}
				// Blokowanie pojawiania się błędów z bazą danych
				error_reporting(0);
				// Dodawanie  ogłoszenia do bazy danych

				$query = "insert into ogloszenia (tytul, data, nr_oferty, nazwa_firmy, opis, miejscowosc, zarobki_min, zarobki_max) values ('". $tytul ."', '". $data ."', ". $nr_oferty[1] .", '". $nazwa_firmy ."', '". $opis ."', '". $miejscowosc ."', " . $zarobki_min . ", " . $zarobki_max . ")";
				$result = pg_query($query);
				// Sprawdzanie czy ogłoszenia dodało się do bazy danych jak nie to dodanie do tabeli błedy informacji o błędzie
				if (!$result){
					$query = "insert into bledy (informacja, adres_strony, data) values ('". pg_last_error($dbconn) ."', '". $adres ."', '". date('d-m-Y') ."')";
					$result = pg_query($query) or die('Nieprawidłowe zapytanie: ' . pg_last_error());
				} else {
					print pg_last_error($dbconn);
					// Pobierania id dodanego ołoszenia do bazy danych
					$query = "select id from ogloszenia where nr_oferty = ". $nr_oferty[1];
					$result = pg_query($query) or die('Nieprawidłowe zapytanie: ' . pg_last_error());
					$line = pg_fetch_array($result, null, PGSQL_ASSOC);
					// Dodawania kategori z ogłoszenia do tabeli kategorie
					for($i=1; $i<= count ($kategoria); $i++){
						$kat = str_replace ( ',', '', $kategoria[$i-1][1]);
						$kat = kontrola_tekstu($kat);
						$query = "insert into kategorie (nazwa_kategori, id_ogloszenia) values ('". $kat ."', '". $line['id'] ."')";
						$result = pg_query($query) or die('Nieprawidłowe zapytanie: ' . pg_last_error());
					}
				}
			}

		} else {
			print $ostatnia_data . '   ' . $data . '<br />';
			$licznik_niezgodnosci++;
		}

		// Zamykanie połączenia z baza danych
		pg_close($dbconn);
	}
	function pobieranie_danych_z_glownej_strony ($adres){
		$dbconn = pg_connect("host=localhost dbname=trojmiasto user=trojmiasto password=trojmiasto") or die('Nie można nawiązać połączenia: ' . pg_last_error());
		$query = "select count (id) from ogloszenia";
		$result = pg_query($query);
		$line = pg_fetch_array($result, null, PGSQL_ASSOC);
		global $licznik_niezgodnosci;
		if ($line['count'] == 0){
			for($j=0; $j < 2; $j++)
			{
				$file = file_get_contents($adres . $j);
				preg_match_all('#<a class="list__item__content__title__name link" href="(.*?)"#', $file, $ogloszenie, PREG_SET_ORDER);
				for($i=1; $i<= count ($ogloszenie); $i++){
					if ($licznik_niezgodnosci >= 10){
						$j = 200;
						break;
					} else {
						pobieranie_danych_z_ogloszenia ($ogloszenie[$i-1][1], date('d-m-Y'));
					}
				}
			}
			print $licznik_niezgodnosci;
		} else {
			$query = "select max (data) from ogloszenia";
			$result = pg_query($query);
			$line = pg_fetch_array($result, null, PGSQL_ASSOC);
			$data = $line['max'];
			pg_close($dbconn);
			for($j=0; $j < 2; $j++)
			{
				$file = file_get_contents($adres . $j);
				preg_match_all('#<a class="list__item__content__title__name link" href="(.*?)"#', $file, $ogloszenie, PREG_SET_ORDER);
				print_r ($ogloszenie);
				for($i=1; $i<= count ($ogloszenie); $i++){
					if ($licznik_niezgodnosci >= 10){
						$j = 200;
						break;
					} else {
						
						pobieranie_danych_z_ogloszenia ($ogloszenie[$i-1][1], $data);
					}
				}
			}
		}
		
	}
	while (true) {

		// Jeśli skrypt się wykonuje dłużej niż 25s, to nastąpi jego uruchomienie kolejnej kopii,
		// a ta się zakończy
		if(time()-$time>60) {
			$c = curl_init();
			curl_setopt($c, CURLOPT_URL, 'localhost/pro/ogloszenia.php');
			curl_setopt($c, CURLOPT_TIMEOUT, 0);
			$temp = curl_exec($c);
			curl_close($c);
			exit;
		}

		pobieranie_danych_z_glownej_strony ('https://ogloszenia.trojmiasto.pl/praca-zatrudnie/ikl,151,o3,1.html?strona=');
		break;
	}
?>