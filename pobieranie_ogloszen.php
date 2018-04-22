<?php
	$c = curl_init();
	curl_setopt($c, CURLOPT_URL, 'localhost/pro/ogloszenia.php');
	curl_setopt($c, CURLOPT_TIMEOUT, 0);
	$temp = curl_exec($c);
	curl_close($c);
	echo 'uruchomiono';
?>

