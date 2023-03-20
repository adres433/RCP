<?php
	//echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />";

	$serwer = "localhost";				//serwer BD :3306
	$passwd = "t4tMUwzRRB8YTGxh";			//BD
	$user   = "rcp";				//BD
	$baza   = "rcp";				//BD
	$hr	= "ws://192.168.1.71:81/";		//adres IP czytnika kadr
	$sec	= "ws://192.168.1.74:81/";		//adres IP czytnika portierni


	$nadgodziny = '30';				//po ilu minutach liczymy nadgodziny
	$nazwaFirma = "PLASTWIL";
	$ulicaFirma = "Wierzbowa 2";
	$kodMiejscowoscFirma = "64-850 UJŚCIE";
	$nipFirma = "764 26 34 997";
	$telFirma = "+48 672 840 740";
	$faxFirma = "+48 672 840 378";

	$connect = mysql_connect($serwer, $user, $passwd) or die ("Błąd połączenia z serwerem BD - proszę o kontakt z informatykiem".mysql_error());
	mysql_select_db($baza, $connect) or die("Błąd wyboru BD - proszę o kontakt z informatykiem".mysql_error());
	mysql_set_charset('utf8', $connect) or die ("Błąd kodowania znaków BD - proszę o kontakt z informatykiem".mysql_error());
?>