<?php
date_default_timezone_set('Europe/Warsaw');
$dniTygodnia = array('Monday' => 'poniedziałek', 'Tuesday' => 'wtorek', 'Wednesday' => 'środa', 'Thursday' => 'czwartek', 'Friday' => 'piątek', 'Saturday' => 'sobota', 'Sunday' => 'niedziela');

	if(isset($_GET['add']))
	{
		@include 'connecting.php';
		
		$query = "SELECT `nr_karty` FROM `pracownicy` WHERE `NR_KARTY` = '".$_POST['karta']."';";
		$result = mysql_query($query, $connect) or die("Błąd zapisu do BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());
		//$data = mysql_fetch_assoc($result);
		if(mysql_affected_rows() >= 1)
		{
			mysql_close($connect) or die("Błąd, połączenie BD nie zakończone. - Proszę o kontakt z informatykiem.");
			echo "istnieje";
		}
		else
		{
			$query = "SET @d = (SELECT MAX(`id`)+1 FROM `pracownicy`);";
			$result = mysql_query($query, $connect) or die("Błąd zapisu do BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());

			@$query = "INSERT INTO `PRACOWNICY` (`ID`, `NAZWISKO`, `PESEL`, `STANOWISKO`, `STATUS`, `NR_KARTY`, `PLAN`, `BRYGADA`, `grupa`) VALUES (@d, '".$_POST['nazwisko']."', '".$_POST['pesel']."', '".$_POST['stanowisko']."', '".$_POST['status']."', '".$_POST['karta']."', '".$_POST['plan']."', '".$_POST['brygada']."', '".$_POST['grupa']."')";
			
			$result = mysql_query($query, $connect) or die("Błąd zapisu do BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());

			mysql_close($connect) or die("Błąd, połączenie BD nie zakończone. - Proszę o kontakt z informatykiem.");
		
			if($result)
				echo "Operacja pomyślna.";
		}
	}

	if(isset($_GET['readEdit']))
	{
		include 'connecting.php';

		if(!isset($_POST['id']))
			@$query = "SELECT `ID`, `STANOWISKO`, `NAZWISKO`, `PESEL` FROM `pracownicy`";
		else
			@$query = "SELECT * FROM `pracownicy` WHERE `ID` = ".$_POST['id'];
		$result = mysql_query($query, $connect) or die("Błąd odczytu z BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());
		mysql_close($connect) or die("Błąd, połączenie BD nie zakończone. - Proszę o kontakt z informatykiem. <br/><br/>".mysql_error());

		if($result)
		{
			if(!isset($_POST['id']))
			{
				echo "<option value='' disabled selected>Wybierz pracownika</option>\n";
				while($wiersz = mysql_fetch_array($result))
					echo "<option value='".$wiersz['0']."'>".$wiersz['2']." - ".$wiersz['1']." - ".$wiersz['3']."</option>\n";
			}
			else
			{
				$wiersz = mysql_fetch_assoc($result);
				echo "".$wiersz['ID']."<-|->".$wiersz['NAZWISKO']."<-|->".$wiersz['STANOWISKO']."<-|->".$wiersz['STATUS']."<-|->".$wiersz['NR_KARTY']."<-|->".$wiersz['plan']."<-|->".$wiersz['brygada']."<-|->".$wiersz['SPRAWNOSC']."<-|->".$wiersz['PESEL']."<-|->".$wiersz['grupa'];
			}
		}
		else
			echo "Błąd BD.";
	}

	if(isset($_GET['saveEdit']))
	{
		@include 'connecting.php';
		if(isset($_POST['sprawnosc']))
			$sprawnosc = ", `SPRAWNOSC`='1'";
		else
			$sprawnosc = ", `SPRAWNOSC`='0'";
		$query = "UPDATE `pracownicy` SET `NAZWISKO`='".$_POST['nazwisko']."',`PESEL`='".$_POST['pesel']."',`STANOWISKO`='".$_POST['stanowisko']."',`STATUS`='".$_POST['status']."',`NR_KARTY`='".$_POST['karta']."',`BRYGADA`='".$_POST['brygada']."',`PLAN`='".$_POST['plan']."' ".$sprawnosc.", `grupa` = '".$_POST['grupa']."' WHERE `ID`='".$_POST['nrpracownika']."'";

		$result = mysql_query($query, $connect) or die("Błąd aktualizacji BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());

		mysql_close($connect) or die("Błąd, połączenie BD nie zakończone. - Proszę o kontakt z informatykiem.");
	
		if($result)
			echo "Operacja pomyślna.";
	}

	if(isset($_GET['readINsec']))
	{
		@include 'connecting.php';

		@$query = "SELECT `STANOWISKO`, `STATUS`, `NR_KARTY`, `nazwisko` FROM `pracownicy` WHERE `ID` = ".$_POST['id'];

		$result = mysql_query($query, $connect) or die("Błąd odczytu z BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());

		if($result)
		{
			$wiersz = mysql_fetch_row($result);
			$query = $wiersz['2'];
				echo $wiersz['0']."->".$wiersz['1']."->".$wiersz['2']."->".$wiersz['3'];
		}
		else
			echo "Błąd BD.";
		
		
		@$query1 = "SELECT `czas`, `zdarzenie` FROM `log` WHERE `id_karty`='".$query."' AND NOT `zdarzenie` = '3' ORDER BY `id` DESC, `zdarzenie` LIMIT 2";

		$result = mysql_query($query1, $connect) or die("Błąd odczytu z BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());
		mysql_close($connect) or die("Błąd, połączenie BD nie zakończone. - Proszę o kontakt z informatykiem. <br/><br/>".mysql_error());
		
		if($result)
		{
			$i = 0;
			while($wiersz = mysql_fetch_row($result))
			{
				if($wiersz['1'] == 1)	//wejscie
					echo "->w".$wiersz['0'];
				else
					echo "->".$wiersz['0'];
				++$i;
			}
			if($i == 1)
				echo "->BRAK";
		}
	}
	
	if(isset($_GET['delWorker']))
	{
		include 'connecting.php';
		$query = "DELETE FROM `".$baza."`.`pracownicy` WHERE `pracownicy`.`ID` = '".$_POST['id']."';";
		mysql_query($query, $connect) or die("Błąd usuwania rekordu z BD. - Proszę o kontakt z informatykiem <br/><br/>".mysql_error());
		mysql_close($connect) or die ("Błąd, połączenie BD nie zakończone. - Proszę o kontakt z informatykiem. <br/><br/>".mysql_error());
	}

	if(isset($_GET['insertReader']))
	{
		include 'connecting.php';

		echo $query = "SELECT `zdarzenie`, `status`, `pracownicy`.`id`, `plan`, `brygada` FROM `log` RIGHT JOIN `pracownicy` ON `log`.`id_karty` = `pracownicy`.`nr_karty` AND `log`.`id` = (SELECT max(`id`) FROM `log` WHERE `id_karty` = '".$_POST['id']."') AND NOT `log`.`czytnik` = 'SYSTEM' WHERE `nr_karty` = '".$_POST['id']."'";
		
		$result = mysql_query($query, $connect) or die("Błąd odczytu z BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());
		$wiersz = mysql_fetch_assoc($result);
		$wiersz1 = 0;
		if($wiersz['zdarzenie'] == '1' && $wiersz['status'] == '1')
			$wiersz1 = 0;
		else if($wiersz['zdarzenie'] == NULL && $wiersz['status'] == '1')
			$wiersz1 = 1;
		else if($wiersz['zdarzenie'] == '0' && $wiersz['status'] == '1')
			$wiersz1 = 1;
		else if($wiersz['zdarzenie'] == '3' && $wiersz['status'] == '1')
			$wiersz1 = 1;
		else if($wiersz['zdarzenie'] == '1' && $wiersz['status'] == '0')
			$wiersz1 = 0;
		else
			$wiersz1 = 3;
		if(isset($_POST['blokada']))
			$wiersz1 = 3;
		
		
		$sql = "START TRANSACTION;";
		mysql_query($sql, $connect) or die("0Błąd zapisu do BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());
		//pobranie danych do obliczenia aktualnej zmiany
		$sql = "SET @id = (SELECT DATEDIFF(NOW(), `poczatek`) - (MAX(`id`)*TRUNCATE(DATEDIFF(NOW(), `poczatek`)/MAX(`id`), 0))+1 FROM `".$wiersz['plan']."`);";		
		mysql_query($sql, $connect) or die("1Błąd zapisu do BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());
		//pobranie nr zmainy
		$sql = "SET @zmiana = (SELECT `".$wiersz['brygada']."` FROM `".$wiersz['plan']."` WHERE `id`= @id);";
		mysql_query($sql, $connect) or die("2Błąd zapisu do BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());
		//pobranie godzin danej zmiany
		$sql = "SET @zmWY = (SELECT `wyjscie` FROM `zmiany` WHERE `id`=@zmiana);";
		mysql_query($sql, $connect) or die("3Błąd zapisu do BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());
		$sql = "SET @zmWE = (SELECT `wejscie` FROM `zmiany` WHERE `id`=@zmiana);";		
		mysql_query($sql, $connect) or die("4Błąd zapisu do BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());
		//pobranie ostatniego id w logu i zwiększenie o 1
		$sql = "SET @d = (SELECT MAX(`id`)+1 FROM `log`);";		
		mysql_query($sql, $connect) or die("5Błąd zapisu do BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());
		//wstawienie danych do logu
		$sql = "INSERT INTO `log` (`czas`, `zdarzenie`, `id_karty`, `czytnik`, `id`, `zmiana_p`, `zmiana_k`, `id_pracownika`) VALUES (NOW(), '".$wiersz1."', '".$_POST['id']."', '".$_POST['reader']."', @d, @zmWE, @zmWY, ".$wiersz['id'].");";
		mysql_query($sql, $connect) or die("6Błąd zapisu do BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());
		$sql = "COMMIT;";
	
		$result = mysql_query($sql, $connect) or die("7Błąd zapisu do BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());
		mysql_close($connect) or die("Błąd, połączenie BD nie zakończone. - Proszę o kontakt z informatykiem.");
	
		if($result)
			echo "Operacja pomyślna.";
	}

	if(isset($_GET['readPresent']))
	{
		include 'connecting.php';

		$sql = "SELECT `id_karty`, `brygada`, `nazwisko`,`czas` FROM `log`INNER JOIN `pracownicy` ON `log`.`id_karty` = `pracownicy`.`nr_karty` WHERE `log`.`id` = (SELECT MAX(`id`) FROM `log` WHERE `id_karty` = `nr_karty`) AND `zdarzenie` = 1 AND `status` = 1";

		$result = mysql_query($sql, $connect) or die("Błąd odczytu z BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());
		mysql_close($connect) or die("Błąd, połączenie BD nie zakończone. - Proszę o kontakt z informatykiem.");
		

		while($wiersz = mysql_fetch_row($result))
		{			
			echo $wiersz['0']."->".$wiersz['1']."->".$wiersz['2']."->".$wiersz['3']."->";
		}	
	}

	if(isset($_GET['endJob']))
	{
		include 'connecting.php';

		$query = "SELECT MAX(`id`)+1 AS 'id' FROM `log` ";
		$result = mysql_query($query, $connect) or die("1Błąd odczytu do BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());
		$d = mysql_fetch_assoc($result);
		$d = $d['id'];
		
		$query = "SELECT `id`, `plan` ,`brygada` FROM `pracownicy` WHERE `nr_karty` = '".$_POST['id']."'; ";
		$result = mysql_query($query, $connect) or die("2Błąd odczytu BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());
		$wiersz = mysql_fetch_assoc($result);
		
		$sql = "SET @id = (SELECT DATEDIFF(NOW(), `poczatek`) - (MAX(`id`)*TRUNCATE(DATEDIFF(NOW(), `poczatek`)/MAX(`id`), 0)) FROM `".$wiersz['plan']."`);";
		$sql = mysql_query($sql, $connect) or die("3Błąd odczytu z BD ZMiANA- proszę o kontakt z informatykiem.</br></br>".mysql_error());	
		
		$sql = "SELECT `".$wiersz['brygada']."` FROM `".$wiersz['plan']."` WHERE `id`= (SELECT IF(@id > 0, @id, MAX(`id`)));";
		$sql = mysql_query($sql, $connect) or die("4Błąd odczytu z BD ZMiANA- proszę o kontakt z informatykiem.</br></br>".mysql_error());
		$sql = mysql_fetch_row($sql);
		$zmiana = $sql['0'];
		
		$sql = "SELECT * FROM `zmiany` WHERE `id`='".$zmiana."'";		
		$sql = mysql_query($sql, $connect) or die("5Błąd odczytu z BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());	
		$zmianaG = mysql_fetch_assoc($sql);
		
		$query = "INSERT INTO `log` (`czas`, `zdarzenie`, `id_karty`, `czytnik`, `powod`, `id`, `id_pracownika`, `zmiana_k`, `zmiana_p`) VALUES (NOW(), '0', '".$_POST['id']."', 'manual', '".$_POST['powod']."', '".$d."', '".$wiersz['id']."', '".$zmiana['wyjscie']."', '".$zmiana['wejscie']."')";
		
		$result = mysql_query($query, $connect) or die("Błąd zapisu do BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());

		mysql_close($connect) or die("Błąd, połączenie BD nie zakończone. - Proszę o kontakt z informatykiem.");
	
		if($result)
			echo "Operacja pomyślna.";
	}
	
	if(isset($_GET['startJob']))
	{
		include 'connecting.php';

		
		$query = "SELECT MAX(`id`)+1 AS 'id' FROM `log` ";
		$result = mysql_query($query, $connect) or die("1Błąd odczytu do BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());
		$d = mysql_fetch_assoc($result);
		$d = $d['id'];
		
		$query = "SELECT `id`, `plan` ,`brygada` FROM `pracownicy` WHERE `nr_karty` = '".$_POST['id']."'; ";
		$result = mysql_query($query, $connect) or die("2Błąd odczytu BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());
		$wiersz = mysql_fetch_assoc($result);
		
		$sql = "SET @id = (SELECT DATEDIFF(NOW(), `poczatek`) - (MAX(`id`)*TRUNCATE(DATEDIFF(NOW(), `poczatek`)/MAX(`id`), 0)) FROM `".$wiersz['plan']."`);";
		$sql = mysql_query($sql, $connect) or die("3Błąd odczytu z BD ZMiANA- proszę o kontakt z informatykiem.</br></br>".mysql_error());	
		
		$sql = "SELECT `".$wiersz['brygada']."` FROM `".$wiersz['plan']."` WHERE `id`= (SELECT IF(@id > 0, @id, MAX(`id`)));";
		$sql = mysql_query($sql, $connect) or die("4Błąd odczytu z BD ZMiANA- proszę o kontakt z informatykiem.</br></br>".mysql_error());
		$sql = mysql_fetch_row($sql);
		$zmiana = $sql['0'];
		
		$sql = "SELECT * FROM `zmiany` WHERE `id`='".$zmiana."'";		
		$sql = mysql_query($sql, $connect) or die("5Błąd odczytu z BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());	
		$zmianaG = mysql_fetch_assoc($sql);
		
		$query = "INSERT INTO `log` (`czas`, `zdarzenie`, `id_karty`, `czytnik`, `powod`, `id`, `id_pracownika`, `zmiana_k`, `zmiana_p`) VALUES (NOW(), '1', '".$_POST['id']."', 'manual', '".$_POST['powod']."', '".$d."', '".$wiersz['id']."', '".$zmiana['wyjscie']."', '".$zmiana['wejscie']."')";
		
		$result = mysql_query($query, $connect) or die("Błąd zapisu do BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());

		mysql_close($connect) or die("Błąd, połączenie BD nie zakończone. - Proszę o kontakt z informatykiem.");
	
		if($result)
			echo "Operacja pomyślna.";
	}
	
	if(isset($_GET['readAbsent']))
	{
		include 'connecting.php';
		//wybiermy wszystkich nieobecnych
		$sql = "SELECT `id_karty`, `brygada`, `nazwisko`, `czas`, `plan` FROM `pracownicy` LEFT JOIN `log` ON `log`.`id_pracownika` = `pracownicy`.`id` WHERE `status` = 1 AND `zdarzenie` = 0 AND `log`.`id` = (SELECT MAX(`id`) FROM `log` WHERE `id_pracownika` = `pracownicy`.`id`)";

		$result = mysql_query($sql, $connect) or die("Błąd odczytu z BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());


		while($wiersz = mysql_fetch_row($result))
		{	
			//sprawdzamy na jakiej wlasnie powinien byc zmianie pracownik
			date_default_timezone_set('Europe/Warsaw');
			// jeżeli trwa nocka musimy sprawdzic zmiane dnia poprzedniego
			if(date('H') < 6)	
			{
				$sql = "SELECT HOUR(NOW()),`".$wiersz['1']."` FROM `".$wiersz['4']."` WHERE `id`= (SELECT DATEDIFF(NOW() - INTERVAL 1 DAY, `poczatek`) - (MAX(`id`)*TRUNCATE((DATEDIFF(NOW(), `poczatek`)/MAX(`id`)), 0))+1 FROM `".$wiersz['4']."`) ";			
				$result1 = mysql_query($sql, $connect) or die("Błąd odczytu z BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());
				$wiersz1 = mysql_fetch_row($result1);
			}
			else
			{
				$sql = "SELECT HOUR(NOW()),`".$wiersz['1']."`, `id` FROM `".$wiersz['4']."` WHERE `id`= (SELECT DATEDIFF(NOW(), `poczatek`) - (MAX(`id`)*TRUNCATE((DATEDIFF(NOW(), `poczatek`)/MAX(`id`)), 0))+1 FROM `".$wiersz['4']."`)";			
				$result1 = mysql_query($sql, $connect) or die("Błąd odczytu z BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());
				$wiersz1 = mysql_fetch_row($result1);
			}
			//pobranie godzin dla powyzej pobranej zmiany
			$sql = "SELECT * FROM `zmiany` WHERE `id` = '".$wiersz1['1']."'";	
			$result2 = mysql_query($sql, $connect) or die("Błąd odczytu z BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());
			$wiersz2 = mysql_fetch_row($result2);
			$zgoda = false;
			
			if($wiersz2['1'] != $wiersz2['2'])	// nieob. uspr
			{
				if($wiersz2['1'] > $wiersz2['2'])	//nocka
				{
					if($wiersz1['0'] >= $wiersz2['1']+1 || $wiersz1['0'] < $wiersz2['2'])
						$zgoda = 1;
				}
				else
				{
					if($wiersz1['0'] >= $wiersz2['1']-1 && $wiersz1['0'] < $wiersz2['2'])
						$zgoda = 1;
				}

				if($zgoda)
				{
					echo $wiersz['0']."->".$wiersz['1']."->".$wiersz['2']."->".$wiersz['3']."->";
				}
			}
		}	

		mysql_close($connect) or die("Błąd, połączenie BD nie zakończone. - Proszę o kontakt z informatykiem.");
	}
	
	if(isset($_GET['readHR']))
	{
		include 'connecting.php';

		$sql = "SELECT `nazwa` FROM `plany`";

		$result = mysql_query($sql, $connect) or die("Błąd odczytu z BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());
		mysql_close($connect) or die("Błąd, połączenie BD nie zakończone. - Proszę o kontakt z informatykiem.");


		while($wiersz = mysql_fetch_row($result))
		{			
			echo $wiersz['0']."->";
		}	
	}

	if(isset($_GET['readHR2']))
	{
		include 'connecting.php';
		$text = preg_replace('/[^A-Z^a-z^0-9]+/','_', preg_replace('/([a-zd])([A-Z])/','1_2', preg_replace('/([A-Z]+)([A-Z][a-z])/','1_2',$_POST['etykieta'])));
		$sql = "SHOW COLUMNS FROM `".$text."` FROM `rcp` LIKE '%brygada'";

		$result = mysql_query($sql, $connect) or die("Błąd odczytu BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());
		mysql_close($connect) or die("Błąd, połączenie BD nie zakończone. - Proszę o kontakt z informatykiem.");

		
		while($wiersz = mysql_fetch_row($result))
		{			
			echo $wiersz['0']."->";
		}	
	}

	if(isset($_GET['updatePlan']))
	{
		include 'connecting.php';
		
		$sql = "TRUNCATE `".$baza."`.`".$_POST['nazwa']."`;";	
		@$result = mysql_query($sql, $connect) or die("Błąd czyszczenia tabeli w BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());
		
		$sql = "INSERT INTO  `".$baza."`.`".$_POST['nazwa']."` (";
		$i = 0;
		while($i < $_POST['brygady'])
		{
			$sql .= "`".++$i."_brygada`,";
		}
		$sql .= " `poczatek`) VALUES ";
		$i = 0;
		
		while($i < $_POST['dni']*$_POST['brygady'])
		{
			$sql .= '( ';
			$j = 0;
			while($j < $_POST['brygady'])
			{
				$sql .= "'".$_POST['input_'.($j+$i).'']."'";
				$sql .= ',';
				$j += 1;
			}
			$sql .= "'".$_POST['pocz']."')";
			
			if(($i+$_POST['brygady']) < ($_POST['dni']*$_POST['brygady']))
				$sql .= ',';
			$i += $_POST['brygady'];
		}	
		
		$sql .= ";";
		//echo $sql;
		@$result = mysql_query($sql, $connect) or die("Błąd zapisu do BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());
	
		@mysql_close($connect) or die("Błąd, połączenie BD nie zakończone. - Proszę o kontakt z informatykiem.");
		echo "OK";
	}

	if(isset($_GET['addPlan']))
	{
		include 'connecting.php';

		$sql = "CREATE TABLE IF NOT EXISTS `".$_POST['nazwa']."` (`id` int(11) NOT NULL AUTO_INCREMENT, ";
		$i = 0;
		$j = 0;
		while($i < $_POST['brygady'])
		{
					$sql .= "`".++$i."_brygada` int(11) NOT NULL,";
		}
		$sql .= " `poczatek` date NOT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci AUTO_INCREMENT=0;";		
		@$result = mysql_query($sql, $connect) or die("Błąd zapisu do BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());

		$sql = "SET @d = (SELECT MAX(`id`)+1 FROM `plany`);";
		@$result = mysql_query($sql, $connect) or die("Błąd zapisu do BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());
		$sql = "INSERT INTO `plany` (`id`, `nazwa`) VALUES (@d, '".$_POST['nazwa']."');";
		@$result = mysql_query($sql, $connect) or die("Błąd zapisu do BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());
		$i = 0;
		$sql = "SET @d = (SELECT MAX(`id`)+1 FROM `".$_POST['nazwa']."`);";
		@$result = mysql_query($sql, $connect) or die("Błąd zapisu do BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());
		while($i < $_POST['dni'])
		{
			$sql = "INSERT INTO  `".$_POST['nazwa']."` (`id`, ";
			$j=0;
			while($j < $_POST['brygady'])
			{				
				$sql .= "`".++$j."_brygada`,";
			}
			$sql .= " `poczatek`) VALUES (@d, ";
			$j=0;
			while($j < $_POST['brygady'])
			{
				
				$sql .= "'".$_POST[$i."_".$j++]."',";
			}
			++$i;
			$sql .= " NOW());";
			echo $sql."</br></br></br></br>";
			@$result = mysql_query($sql, $connect) or die("Błąd zapisu do BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());
		}
		mysql_close($connect) or die("Błąd, połączenie BD nie zakończone. - Proszę o kontakt z informatykiem.");
		
	}
	
	if(isset($_GET['delPlan']))
	{
		include 'connecting.php';

		$sql = "DROP TABLE `".$_POST['nazwa']."`;";
		@$result = mysql_query($sql, $connect) or die("Błąd usuwania tabeli z BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());
		$sql = "DELETE FROM `plany` WHERE `nazwa` = '".$_POST['nazwa']."';";
		@$result = mysql_query($sql, $connect) or die("Błąd usuwania rekordów z BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());
		mysql_close($connect) or die("Błąd, połączenie BD nie zakończone. - Proszę o kontakt z informatykiem.");
		
	}

	if(isset($_GET['delHours']))
	{
		include 'connecting.php';

		$sql = "DELETE FROM `zmiany` WHERE `id` = '".$_POST['id']."';";
		@$result = mysql_query($sql, $connect) or die("Błąd usuwania rekordów z BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());
		mysql_close($connect) or die("Błąd, połączenie BD nie zakończone. - Proszę o kontakt z informatykiem.");
		
	}

	if(isset($_GET['godzinyShow']))
	{
		include 'connecting.php';
		
		$sql = "SELECT * FROM `zmiany`;";

		$result = mysql_query($sql, $connect) or die("Błąd odczytu z BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());
		while($wiersz = mysql_fetch_array($result))
		{
			if($wiersz['1'] != $wiersz['2'])
			{
				echo $wiersz['0']." - ";
				if($wiersz['1'] < 10)
					echo "0";
				echo $wiersz['1'].":00-";

				if($wiersz['2'] < 10)
					echo "0";
				echo $wiersz['2'].":00<br/>";
			}
			else
				echo $wiersz['0']." - ".$wiersz['4']."<br/>";
		}
		mysql_close($connect) or die("Błąd, połączenie BD nie zakończone. - Proszę o kontakt z informatykiem.");
		
	}

	if(isset($_GET['hoursAdd']))
	{
		include 'connecting.php';

		if(isset($_POST['symbol']) && $_POST['symbol'] == '')
			$symbol = 'NULL';
		else
			$symbol = "'".strtoupper($_POST['symbol'])."'";
		if(isset($_POST['opis']) && $_POST['opis'] == '')
			$opis = 'NULL';
		else
			$opis = "'".$_POST['opis']."'";
		$sql = "SET @d = (SELECT `id` FROM `zmiany` ORDER BY `id` DESC LIMIT 1);";		
		$result = mysql_query($sql, $connect) or die("Błąd zapisu do BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());
		$sql = "INSERT INTO `zmiany` (`id`, `wejscie`, `wyjscie`, `opis`, `symbol`) VALUES (@d+1,'".$_POST['in']."', '".$_POST['out']."', ".$opis.", ".$symbol.")";
		$result = mysql_query($sql, $connect) or die("Błąd zapisu do BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());
		echo mysql_insert_id();
		mysql_close($connect) or die("Błąd, połączenie BD nie zakończone. - Proszę o kontakt z informatykiem.");
		
	}

	if(isset($_GET['readPlan']))
	{
		include 'connecting.php';

		$sql = "SELECT * FROM `".$_POST['nazwa']."`;";
		//odczytujemy cały plan wg. wybranej nazwy
		@$result = mysql_query($sql, $connect) or die("Błąd odczytu BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());
		$sql = "SHOW COLUMNS FROM `".$_POST['nazwa']."` LIKE '%brygada';";
		//pobieramy nazwy kolumn aby znać il. brygad
		@$result2 = mysql_query($sql, $connect) or die("Błąd odczytu BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());
		$sql = "SELECT DATEDIFF(DATE(NOW()), `POCZATEK`) FROM `".$_POST['nazwa']."` WHERE `id` = '1';";
		//sprawdzamy ile dni minęło od rozpoczęcia planu
		@$result3 = mysql_query($sql, $connect) or die("Błąd odczytu BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());
		mysql_close($connect) or die("Błąd, połączenie BD nie zakończone. - Proszę o kontakt z informatykiem.");
		$result3 = mysql_fetch_array($result3);
		$liczbaDni = $result3[0];
		$liczbaWierszy = mysql_num_rows($result); //liczba dni
		$il = mysql_num_rows($result2);		//liczba brygad
		
		$poczatek;
		if($liczbaDni >	$liczbaWierszy)
		{
			$wynik = (int)($liczbaDni / $liczbaWierszy);
			$wynik = $wynik*$liczbaWierszy;
			$liczbaDni = $liczbaDni - $wynik;
		}
		
		echo "<tr>";
		echo "<th>DZIEŃ</th>";
		$j = 1;
		while($wiersz2 = mysql_fetch_array($result2))
		{
			echo "<th class='kolBryg'>BR. ".$j++."</th>";			
		}		
		echo "<th>USUŃ</th>";
		echo "</tr>";
		
		while($wiersz = mysql_fetch_array($result))
		{
			$styl = '';
			echo $liczbaDni.":".$wiersz['id'].":";
			echo$danyDzien = ($liczbaDni+1-$wiersz['id']);
			$danyDzien *= (60*60*24);
			$roznicaDni = date("Y-m-d", (strtotime(date("Y-m-d")) - $danyDzien));
			echo "<tr>";
			if($liczbaDni+1 == $wiersz['id'])
				$styl = "background-color: #B6FFAA; ";
			if($liczbaDni+1 <= $wiersz['id'])
			{
				$roznicaDni = $wiersz['id']-($liczbaDni+1);
				$roznicaDni = date("Y-m-d", (strtotime(date("Y-m-d")) + ($roznicaDni*60*60*24)));
			}
			$x = date("l", strtotime($roznicaDni));
			$roznicaDni = $roznicaDni." ".$dniTygodnia[$x];
			//echo "<th style='".$styl."border-bottom: 1px solid #8b8b8b;'>".$wiersz['id']."</th>";
			echo "<th style='text-align: left; ".$styl."border-bottom: 1px solid #8b8b8b;'>".$roznicaDni."</th>";
			$i = 1;
			
			while($i <= $il)
			{
				echo "<td style='".$styl."border-bottom: 1px solid #8b8b8b; border-right: 1px solid #8b8b8b'><input style='width: 30px;' type='number' value='".$wiersz[$i.'_brygada']."' name='in_".$wiersz['id']."_".$i."'></td>";
				++$i;
			}
			$poczatek = $wiersz['poczatek'];			
			echo "<td style='".$styl."border-bottom: 1px solid #8b8b8b; color: red;'><span id='wierszId_".$wiersz['id']."'>X</span></td>";
			echo "</tr>";
		}
		echo "<tr class='poczateczek'><td class='poczateczek'>".$poczatek."</td></tr>";
	}

	if(isset($_GET['readUpHours']))
	{
		include 'connecting.php';
		//pobranie pracowników obecnych aktualnie w zakładzie
		$sql = "SELECT `id_karty`, `brygada`, `nazwisko`,`czas`, `plan`, TIMEDIFF(NOW(), `czas`) FROM `log` INNER JOIN `pracownicy` ON `log`.`id_pracownika` = `pracownicy`.`id` WHERE `log`.`id` = (SELECT MAX(`id`) FROM `log` WHERE `id_karty` = `nr_karty`) AND `zdarzenie` = 1 AND `status` = 1";

		$result = mysql_query($sql, $connect) or die("Błąd odczytu z BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());
		
		while($wiersz = mysql_fetch_row($result))
		{	
			//sprawdzamy na jakiej wlasnie powinien byc zmianie pracownik
			date_default_timezone_set('Europe/Warsaw');
			if(date('H') < 6)	// jeżeli trwa nocka musimy sprawdzic zmiane dnia poprzedniego
			{
				$sql = "SELECT HOUR(NOW()),`".$wiersz['1']."` FROM `".$wiersz['4']."` WHERE `id`= (SELECT DATEDIFF(NOW() - INTERVAL 1 DAY, `poczatek`) - (MAX(`id`)*TRUNCATE((DATEDIFF(NOW() - INTERVAL 1 DAY, `poczatek`)/MAX(`id`)), 0))+1 FROM `".$wiersz['4']."`)";			
				$result1 = mysql_query($sql, $connect) or die("Błąd odczytu z BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());
				$wiersz1 = mysql_fetch_row($result1);
			}
			else
			{
				$sql = "SELECT HOUR(NOW()),`".$wiersz['1']."` FROM `".$wiersz['4']."` WHERE `id`= (SELECT DATEDIFF(NOW(), `poczatek`) - (MAX(`".$wiersz['4']."`.`id`)*TRUNCATE((DATEDIFF(NOW(), `poczatek`)/MAX(`id`)), 0))+1 FROM `".$wiersz['4']."`)";			
				$result1 = mysql_query($sql, $connect) or die("Błąd odczytu z BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());
				$wiersz1 = mysql_fetch_row($result1);
			}
			//pobranie godzin dla powyzej pobranej zmiany
			$sql = "SELECT * FROM `zmiany` WHERE `id` = '".$wiersz1['1']."'";	
			$result2 = mysql_query($sql, $connect) or die("Błąd odczytu z BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());
			$wiersz2 = mysql_fetch_row($result2);
			$zgoda = 1;
			//echo "kto: ".$wiersz['2']."brygada: ".$wiersz['1']." plan: ".$wiersz['4']." godzina: ".$wiersz1['0']." zmiana: ".$wiersz1['1']." godziny: IN:".$wiersz2['1']." OUT:".$wiersz2['2'];
			if($wiersz2['1'] == $wiersz2['2'])	// nieob. uspr
			{
				$zgoda = 1;
			}
			if($wiersz2['1'] > $wiersz2['2'])	//nocka
			{
				//echo "NOCKA NADGODZINY";
				//echo $wiersz1['0']." >= ".$wiersz2['1']." || ".$wiersz1['0']." <= ".$wiersz2['2'];
				//jezeli aktualna godzina jest wyzsza lub równa niz godzina wejscia [22 dla nocki] i mniejsza lub równa niz wyjscia [6 dla nocki]
				if($wiersz1['0'] >= $wiersz2['1'] || $wiersz1['0'] <= $wiersz2['2'])
				{
					$zgoda = 0;
					//echo "NOCKA BRAK NADGODZIN";
				}
			}
			else
			{
				//echo "1/3 NADGODZINY";
				if($wiersz1['0'] >= $wiersz2['1'] && $wiersz1['0'] <= $wiersz2['2'])
				{
					$zgoda = 0;
					//echo "1/3 BRAK NADGODZIN";
				}
			}

			if($zgoda)
			{
				echo $wiersz['0']."->".$wiersz['1']."->".$wiersz['2']."->".$wiersz['3']."->".$wiersz['5']."->";
			}
		}		
		mysql_close($connect) or die("Błąd, połączenie BD nie zakończone. - Proszę o kontakt z informatykiem.");
	}

	if(isset($_GET['lista']))
	{
		include 'connecting.php';

		$sql = "SELECT `id`, `nazwisko`, `pesel`, `stanowisko`, `brygada` FROM `pracownicy`";
		$result = mysql_query($sql, $connect) or die("Błąd odczytu z BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());
		mysql_close($connect) or die("Błąd, połączenie BD nie zakończone. - Proszę o kontakt z informatykiem.");
		

		echo "<option value='end' disabled selected>Wybierz pracownika do raportu</option>\n";
		while($wiersz = mysql_fetch_assoc($result))
		{
			echo "<option value='".$wiersz['id']."'>".$wiersz['nazwisko']." - ".$wiersz['stanowisko']." - ".str_replace('_', ' ', $wiersz['brygada'])."-".$wiersz['pesel']."</option>\n";
		}
	}
	if(isset($_GET['grupy']))
	{
		include 'connecting.php';

		$sql = "SELECT DISTINCT `grupa` FROM `pracownicy`";
		$result = mysql_query($sql, $connect) or die("Błąd odczytu z BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());
		mysql_close($connect) or die("Błąd, połączenie BD nie zakończone. - Proszę o kontakt z informatykiem.");
		
		$lista = array();
		while($wiersz = mysql_fetch_assoc($result))
		{
			$arrayTemp = explode(", ", $wiersz['grupa']);
			
			foreach($arrayTemp AS $rekordTemp)
			{
				if(!array_search($rekordTemp, $lista))
					$lista[$rekordTemp] =$rekordTemp;
			}
		}		
		//echo "<option value='' disabled selected>Wybierz grupę do raportu</option>\n";
		foreach($lista AS $rekord)
		{
			echo "<option value='".$rekord."'>".$rekord."</option>\n";
		}
	}

	if(isset($_GET['raport']))
	{
		if(isset($_GET['xls']))
		{
			header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
			header("Content-type:   application/x-msexcel; charset=utf-8");
			header("Content-Disposition: attachment; filename='raport_".date('m_Y').".xls'"); 
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false);
		}
			
		$headerP ='';
		$headerK = '';
		$headerLogi = '';
		$headerCzasy = '';
		$headerID = '';

		if(isset($_GET['p']))
			$headerP = $_GET['p'];
		if(isset($_GET['k']))
			$headerK = $_GET['k'];
		if(isset($_GET['logi']))
			$headerLogi = $_GET['logi'];
		if(isset($_GET['czasy']))
			$headerCzasy = $_GET['czasy'];
		if(isset($_GET['id']))
			$headerID = $_GET['id'];
			
		if(isset($_POST['p']))
			$headerP = $_POST['p'];
		if(isset($_POST['k']))
			$headerK = $_POST['k'];
		if(isset($_POST['logi']))
			$headerLogi = $_POST['logi'];
		if(isset($_POST['czasy']))
			$headerCzasy = $_POST['czasy'];
		if(isset($_POST['id']))
			$headerID = $_POST['id'];
		

		$html = "<!DOCTYPE html><html>
				<head> 
				<meta charset=\"UTF-8\">
				<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />";
				
				
		if(isset($_GET['where']) && $_GET['where'] == 'one')
		$html .= "<script src='jquery.js'></script>
		<script>
		
		function up(id)
		{
			var h = prompt('Podaj ilośc zaliczonych godzin [tylko pełne godziny]');
			if(h != '' && h != null)
			{				
				$.post('dbase.php?addAH&update', 'id='+id+'&val='+h, function(e)		
				{	
					if(e == 'OK')
					{
						location.reload();
						location.href = location.href;
					}
					console.log(e);
				});
			}
		}
		
		function zmienPlan(id, data)
		{
			$.post('dbase.php?godzinyShow', function(e)
			{
				var rev = prompt('Wprowadź nowy identyfikator z listy: \\n\\n'+e.replace(/<br\/>/g, '\\n'));
				if(rev != null && rev != '')
				{	
					$.post('dbase.php?calendar', 'id='+id+'&pracownik=".$headerID."&nr='+rev+'&data='+data, function(e)
					{
						if(e == 'OK')
						{
							location.reload();
							location.href = location.href;
						}
						console.log(e);
					});
				}
			});
		}
		$('document').ready(function()
		{
			$('.xxx_zaliczone').click(function()
			{
				var h = prompt('Podaj ilośc zaliczonych godzin [tylko pełne godziny]');
				//console.log(h);
				if(h != '' && h != null)
				{
					var tempChild = $(this).parent().children().first();	
					$.post('dbase.php?addAH', 'data='+tempChild.text()+'&val='+h+'&id=".$headerID."&in='+$(this).attr('in-info')+'&out='+$(this).attr('out-info'), function(e)		
					{	
						if(e == 'OK')
						{
							location.reload();
							location.href = location.href;
						}
						console.log(e);
					});
				}
			});
		});
		</script>";
		if(isset($_GET['pdf']) || (isset($_GET['where']) && $_GET['where'] == 'many'))
			$html .= "</head><body>";
		include 'connecting.php';
		$resLoop = '';
		if(isset($_GET['where']) && $_GET['where'] == 'many')		
		{
			$sql = "SELECT `id` FROM `pracownicy` WHERE `grupa` LIKE  '%, ".$headerID."' OR  `grupa` LIKE  '".$headerID.",%' OR  `grupa` LIKE  '%, ".$headerID.", %' OR  `grupa` LIKE  '".$headerID."';";
			$resLoop = mysql_query($sql, $connect) or die("Błąd odczytu z BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());
		}
		$sql = "SELECT `id`, `symbol`, `opis` FROM `zmiany` WHERE `wejscie`=`wyjscie`;";
		$sql = mysql_query($sql, $connect) or die("Błąd odczytu z BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());
		$liczniki = array();
		$zmianyOpisy = array();
		$zmianySymbole = array();
		while($tableZmiany = mysql_fetch_assoc($sql))
		{
			$liczniki[$tableZmiany['id']] = 0;
			$zmianySymbole[$tableZmiany['id']] = $tableZmiany['symbol'];
			$zmianyOpisy[$tableZmiany['id']] = $tableZmiany['opis'];
		}
		
		while(true)
		{		
			if(isset($_GET['where']) && $_GET['where'] == 'many')
			{				
				if(!$headerID = mysql_fetch_assoc($resLoop))
					break;
				$headerID = $headerID['id'];
			}
			//pobranie danych pracownika do raportu
			$sql = "SELECT `id`, `nr_karty`, `stanowisko`, `plan`, `status`, `nazwisko`, `brygada`, `pesel` FROM `pracownicy` WHERE `id` = '".$headerID."'";
			$result = mysql_query($sql, $connect) or die("Błąd odczytu z BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());
			$wiersz = mysql_fetch_assoc($result);
						
			if(isset($_GET['where']) && $_GET['where'] == 'many')
				$tytul = "Raport grupowy";
			else
				$tytul = "Raport pracownika: ".$wiersz['nazwisko'];
			$brygada = $wiersz['brygada'];
			$plan = $wiersz['plan'];
			$karta = $wiersz['nr_karty'];
			$nr_id = $wiersz['id'];
			$color = "#e9e9e9";
			$kolor = "#ffffff";
			$pesel = $wiersz['pesel'];
			
			
			if(!isset($_GET['pdf']) || (isset($_GET['where']) && $_GET['where'] == 'one'))
			{	
				$html .=  "</&nnnnnn&;><title>Raport pracownika: ".$wiersz['nazwisko']."</title></head>";	
			}	
				$html .=  "\n\n<style>th, td {padding: 5px 4px 5px 4px;}</style>\n";
			
			if(!isset($_GET['pdf']) || (isset($_GET['where']) && $_GET['where'] == 'one'))
			{	
				$html .=  "<body>";	
			}	
				$html .=  "<center>\n";
			$html .=  "\t<table style='page-break-after: always'>\n";
				$html .=  "\t\t<tr>\n";
			if(!isset($_GET['pdf']))
			{
					$html .=  "\t\t\t<th style=\"background: url('plastwil.gif') no-repeat; width: 207px; height: 54px; border-left: 1px solid #b8b8b8;\">&nbsp;</th>\n";
					$html .=  "\t\t\t<th style='padding-right: 25px; padding-left: 25px; border-right: 1px solid #b8b8b8; font-size: 40px'>RAPORT PRACOWNIKA</th>\n";
			}
			else
			{
					$html .=  "\t\t\t<th colspan='2' align='center' style='padding-right: 25px; padding-left: 25px; border-right: 1px solid #b8b8b8; border-left: 1px solid #b8b8b8; font-size: 40px'>RAPORT PRACOWNIKA</th>\n";
			}
				$html .=  "\t\t</tr>\n";
				$html .=  "\t\t<tr>\n";
					$html .=  "\t\t\t<th style='border-top: 1px solid #b8b8b8; border-left: 1px solid #b8b8b8; text-align: center;'>Dane pracownika</th>\n";
					$html .=  "\t\t\t<th style='border-top: 1px solid #b8b8b8; border-right: 1px solid #b8b8b8; text-align: right; padding-right: 50px;'>".$nazwaFirma."</th>\n";
				$html .=  "\t\t</tr>\n";
				$html .=  "\t\t<tr>\n";
					$html .=  "\t\t\t<td style='border-left: 1px solid #b8b8b8; text-align: center;'><b>".$wiersz['nazwisko']."</b></td>\n";
					$html .=  "\t\t\t<td style='border-right: 1px solid #b8b8b8; text-align: right; padding-right: 50px;'>".$ulicaFirma."</td>\n";
				$html .=  "\t\t</tr>\n";
				$html .=  "\t\t<tr>\n";
					$html .=  "\t\t\t<td style='border-left: 1px solid #b8b8b8; text-align: left;'>PESEL: ".$pesel."</td>\n";
					$html .=  "\t\t\t<td style='border-right: 1px solid #b8b8b8; text-align: right; padding-right: 50px;'>".$kodMiejscowoscFirma."</td>\n";
				$html .=  "\t\t</tr>\n";
				$html .=  "\t\t<tr>\n";
					$html .=  "\t\t\t<td style='border-left: 1px solid #b8b8b8; text-align: left;'>Brygada: ".str_replace('_', ' ', $wiersz['brygada'])."</td>\n";
					$html .=  "\t\t\t<td style='border-right: 1px solid #b8b8b8; text-align: right; padding-right: 50px;'>NIP ".$nipFirma."</td>\n";
				$html .=  "\t\t</tr>\n";
				$html .=  "\t\t<tr>\n";
					$html .=  "\t\t\t<td style='border-left: 1px solid #b8b8b8; text-align: left;'>Stanowisko: ".$wiersz['stanowisko']."</td>\n";
					$html .=  "\t\t\t<td style='border-right: 1px solid #b8b8b8; text-align: right; padding-right: 50px;'>Tel. ".$telFirma."</td>\n";
				$html .=  "\t\t</tr>\n";
				$html .=  "\t\t<tr>\n";
					$html .=  "\t\t\t<td style='border-left: 1px solid #b8b8b8; text-align: left;'>ID kraty: ".$karta."</td>\n";
					$html .=  "\t\t\t<td style='border-right: 1px solid #b8b8b8; text-align: right; padding-right: 50px;'>Fax. ".$faxFirma."</td>\n";
				$html .=  "\t\t</tr>\n";
				$html .=  "\t\t<tr>\n";
					$html .=  "\t\t\t<th colspan='2' style='border-bottom: 1px solid #b8b8b8; border-top: 1px solid #b8b8b8; text-align: center;'><br/>WYBRANY OKRES CZASU</th>\n";
				$html .=  "\t\t</tr>\n	";
				$html .=  "\t\t<tr>\n";
					if($headerP != $headerK)
					{
						$html .=  "\t\t\t<td style='border-left: 1px solid #b8b8b8; text-align: center;'>Od: ".$headerP."</td>\n";
						$html .=  "\t\t\t<td style='border-right: 1px solid #b8b8b8;  text-align: left; padding-left: 50px;'>Do: ".$headerK."</td>\n";
					}
					else
						$html .=  "\t\t\t<td colspan='2' style='border-left: 1px solid #b8b8b8; text-align: center;'>".$headerP."</td>\n";
				$html .=  "\t\t</tr>\n";
			
			if($headerCzasy != '')         
			{
				$nocka = 0;
				$color = "#e9e9e9";
				$kolor = "#ffffff";
				$tydzien = array("Pn", "Wt", "Śr", "Cz", "Pt", "So", "Nd");
				$sumaZaliczona = $sumaSpodziewana = $sumaRzeczywistaGG = $sumaRzeczywistaMM = $sumaRzeczywistaSS = 0;
				//suma czasu zaliczonego, spodziewany czas pracy łącznie, rzeczywista łączna l. godzin, minut, sekund
				
				$html .=  "\t\t<tr>\n";
					$html .=  "\t\t\t<th colspan='2' style='border-bottom: 1px solid #b8b8b8; border-top: 1px solid #b8b8b8; text-align: center;'><br/>WYKAZ CZASU PRACY</th>\n";
				$html .=  "\t\t</tr>\n	";
				$html .=  "\t\t<tr>\n\t\t\t<td colspan='2' style='border-left: 1px solid #b8b8b8; border-right: 1px solid #b8b8b8; text-align: center; padding-left: 0px;'>\n";
					$html .=  "\t\t\t\t<table style='border-collapse: collapse;'>\n";
					$html .=  "\t\t\t\t\t<tr>\n";
					if(!isset($_GET['pdf']))
						$html .=  "\t\t\t\t\t\t<th colspan='2' rowspan='2' style='width: 130px;'>Data</th>\n";
					else
						$html .=  "\t\t\t\t\t\t<th colspan='2' rowspan='2' width='105px'>Data</th>\n";
					$html .=  "\t\t\t\t\t\t<th rowspan='2' style='border-left: 1px solid #b8b8b8;' class='wejscia'>Start<br/>zmiany</th>\n";
					$html .=  "\t\t\t\t\t\t<th rowspan='2' style='border-left: 1px solid #b8b8b8;' class='przyjscia'>Wejście</th>\n";
					$html .=  "\t\t\t\t\t\t<th rowspan='2' style='border-left: 1px solid #b8b8b8;' class='wyjscia'>Koniec<br/>zmiany</th>\n";
					$html .=  "\t\t\t\t\t\t<th rowspan='2' style='border-left: 1px solid #b8b8b8;' class='wyjsciowy'>Wyjście</th>\n";
					$html .=  "\t\t\t\t\t\t<th colspan='2' style='width: 140px; border-left: 1px solid #b8b8b8;' class='czasowy'>Czas pracy</th>\n";
					$html .=  "\t\t\t\t\t</tr>\n";
					$html .=  "\t\t\t\t\t<tr>\n";
					$html .=  "\t\t\t\t\t\t<th style='border-left: 1px solid #b8b8b8; border-top: 1px solid #b8b8b8;' class='zmiany'>S</th>\n";
					$html .=  "\t\t\t\t\t\t<th style='border-left: 1px solid #b8b8b8; border-top: 1px solid #b8b8b8;' class='zmiany'>R</th>\n";
					$html .=  "\t\t\t\t\t\t<th style='border-left: 1px solid #b8b8b8; border-top: 1px solid #b8b8b8;' class='zmiany'>Z</th>\n";
					$html .=  "\t\t\t\t\t</tr>\n";

				//pętla poszczegolnych dni
				$i_t = 0;
				$dataT;
				while(($dataT = strtotime($headerK)-($i_t*60*60*24)) >= strtotime($headerP))	//petla poszczegolnych dni
				{		
					$wiersz = array('data' => date('Y-m-d', $dataT), 'dzien' => date('w', $dataT-(60*60*24)));
					$doZapytania = '';	//pomocniczo przy nockach dodaje warunek przy odpytywaniu mysql
					$czas = $zmianaG = $pierwszeWE = $ostatnieWY  = $zdarzenie = $wejsciePom = $czasZmiany = $idWpisu =0;
					//czas pobytu danego dnia, nr zmainy, godziny zmiany, pierwsze wejscie, ostatnie wyjscie, dzien tygodnia, ostatnie wejscie jako zm. pomocnicza, przechowuje id wpisu z godzinami zmian
					//planowany czas zmiany
					//POBRANIE GODZIN PRCY DLA DANEGO DNIA
					$sql = "SELECT DISTINCT `id`, `zmiana_p`, `zmiana_k` FROM `log` WHERE DATE(`czas`) = DATE('".$wiersz['data']."') AND `id_pracownika` = ".$headerID." ORDER BY `id` DESC LIMIT 1;";
					$sql = mysql_query($sql, $connect) or die("Błąd odczytu z BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());	
					$sql = mysql_fetch_assoc($sql);
					$zmianaTemp[0] = $zmianaWejscie = $sql['zmiana_p'];
					$zmianaTemp[1] = $zmianaWyjscie = $sql['zmiana_k'];	
					if(isset($sql['id']))
						$idWpisu = $sql['id'];	
					else					
						$idWpisu = "0";
					
					//zmiana formatu godzin
	//ABSENCJE - DNI WOLNE	
					if($zmianaWejscie == $zmianaWyjscie)		//jeżeli godziny sa równe to pobieramy symbol nieobecnosci
					{
						$sql = "SELECT `symbol` FROM `zmiany` WHERE `wejscie` ='".$zmianaWejscie."' AND `wyjscie` = '".$zmianaWyjscie."' LIMIT 1";
						$sql = mysql_query($sql, $connect) or die("Błąd odczytu z BD [absencje 1] - proszę o kontakt z informatykiem.</br></br>".mysql_error());	
						$sql = mysql_fetch_assoc($sql);
						$zmianaWejscie = $zmianaWyjscie = $sql['symbol'];
					}					
					else
					{
						if($zmianaWejscie < 10)
							$zmianaWejscie = "0".$zmianaWejscie.":00";
						else
							$zmianaWejscie = $zmianaWejscie.":00";
						if($zmianaWyjscie < 10)
							$zmianaWyjscie = "0".$zmianaWyjscie.":00";
						else
							$zmianaWyjscie = $zmianaWyjscie.":00";
					}
					//pobranie wszystkich zdarzen z tego dnia dla danego pracownika
					
					if($nocka)	//jezeli poprzedniego dnia była nocka to pobieramy rekordy od g. 6
					{
						$doZapytania = "HOUR(`czas`) >= 6 AND";
						$nocka = 0;
					}
					$sql1 = "SELECT  * FROM `log` WHERE ".$doZapytania." STRCMP(DATE(`czas`), '".$wiersz['data']."') = 0 AND `id_pracownika`='".$nr_id."' AND NOT `czytnik` = 'SYSTEM'";
					$result1 = mysql_query($sql1, $connect) or die("Błąd odczytu z BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());
					
					while($wiersz1 = mysql_fetch_assoc($result1))		//petla poszczegolnych wpisow danego dnia
					{
						//segregacja zdarzeń i zliczanie czasu pracy danego dnia
						
						$zdarzenie  = $wiersz1['zdarzenie'];
						if($zdarzenie == '3')	//jeżeli zdarzenie jest BLOKADA to ignorujemy wpis
							continue;						
						else if($zdarzenie == '0' && $pierwszeWE == 0)		//jeżeli zdarzenie to wyjście i jeszcze nie było wejścia to ignorujemy wpis
							continue;	
						else if($zdarzenie == '1')	//jeżeli zdarzenie to WEJŚCIE to zapisujemy do raportu
						{
							$czas -= strtotime($wiersz1['czas']);	//odejmujemy od czasu spedzonego w pracy w danym dniu
							if($pierwszeWE == 0)					//jeżeli nie było jeszcze wejść
								$pierwszeWE = $wiersz1['czas'];		//zapisujemy pierwsze wejscie danego dnia do raportu
							$wejsciePom = $wiersz1['czas'];			//pomocniczo dla obliczen czasu [spr. czy ostatnie w danym dniu bylo wejscie czy wyjscie]
						}
						else if($zdarzenie == '0')	//jeżeli zdrzenie jest WYJŚCIEM i nie jest to pierwszy wpis danego dnia
						{
							$czas += strtotime($wiersz1['czas']);	//dodajemy czas do puli czasu pracy
							$ostatnieWY = $wiersz1['czas'];			//zapisujemy czas do zmiennej - na koniec zapisany bedzie czas ostatniego wyjscia
						}					
					}
					
					if($czas == 0)	//jeżeli danego dnia nie było wpisów to obliczmy nr zmiany wg. planu
					{
						if($idWpisu == 0)	//jeżeli nie było również wpisów systemowych
						{
							//NR ZMIANY
							$sqla = "SET @dd = (SELECT `".$brygada."` FROM `".$plan."` WHERE `id`= (SELECT DATEDIFF('".$wiersz['data']."', `poczatek`) - (MAX(`id`)*TRUNCATE((DATEDIFF('".$wiersz['data']."', `poczatek`)/MAX(`id`)), 0))+1 FROM `".$plan."`))";			
							$sqla = mysql_query($sqla, $connect) or die("Błąd odczytu z BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());
							//GODZINY
							$sqla = "SELECT `wejscie`, `wyjscie` FROM `zmiany` WHERE `id` = @dd;";
							$sqla = mysql_query($sqla, $connect) or die("Błąd odczytu z BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());
							
							$sqla = mysql_fetch_assoc($sqla);
								$zmianaTemp[0] = $zmianaWejscie = $sqla['wejscie'];
								$zmianaTemp[1] = $zmianaWyjscie = $sqla['wyjscie'];	
								
								
			//ABSENCJE - DNI WOLNE	
							if($zmianaWejscie == $zmianaWyjscie)		//jeżeli godziny sa równe to pobieramy symbol nieobecnosci
							{
								$sql = "SELECT `symbol` FROM `zmiany` WHERE `wejscie` ='".$zmianaWejscie."' AND `wyjscie` = '".$zmianaWyjscie."' LIMIT 1";
								$sql = mysql_query($sql, $connect) or die("Błąd odczytu z BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());	
								$sql = mysql_fetch_assoc($sql);
								$zmianaWejscie = $zmianaWyjscie = $sql['symbol'];
							}
							else
							{
								if($zmianaWejscie < 10)
									$zmianaWejscie = "0".$zmianaWejscie.":00";
								else
									$zmianaWejscie = $zmianaWejscie.":00";
								if($zmianaWyjscie < 10)
									$zmianaWyjscie = "0".$zmianaWyjscie.":00";
								else
									$zmianaWyjscie = $zmianaWyjscie.":00";
								
							}
						}
					}
						
					if(strtotime($wejsciePom) > strtotime($ostatnieWY))	//jeżeli ostatnie wyjscie nastąpilo przed ostatnim wejsciem 
					{
						//pobieramy kolejne wyjscie
						$sql = "SELECT * FROM `log` WHERE `id_pracownika` = '".$nr_id."' AND `czas` > '".$wejsciePom."' AND `zdarzenie` = 0  AND NOT `czytnik` = 'SYSTEM'  LIMIT 1";
						$sql = mysql_query($sql, $connect) or die("Błąd odczytu z BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());
						$sql = mysql_fetch_assoc($sql);
						
						if($sql)
						{
							$ostatnieWY = $sql['czas'];
							$czas += strtotime($sql['czas']);	//dodajemy czas do puli czasu pracy	
							
							//jeżeli ostatnie WYJŚCIE nastąpiło przed 06:00 kolejnego dnia
							if(strtotime($ostatnieWY) < (strtotime($wiersz['data']." 06:00:00")+(60*60*24)))	
							{
								//zakładamy że była nocka i pobieramy rekordy kolejnego dnia aż do 06:00
								$sql = "SELECT * FROM `log` WHERE `id_pracownika` = '".$nr_id."' AND DATE(`czas`) > '".$wiersz['data']."' AND TIMEDIFF(`czas`, '".$wiersz['data']." 06:00:00' + INTERVAL 1 DAY) < 0 AND NOT `czytnik` = 'SYSTEM' ";
								$sql = mysql_query($sql, $connect) or die("Błąd odczytu z BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());
								while($result2 = mysql_fetch_assoc($sql))
								{
									//kolejna segregacja zdarzeń i zliczanie czasu pracy danego dnia
									
									$zdarzenie  = $result2['zdarzenie'];
									if($zdarzenie == '3')	//jeżeli zdarzenie jest BLOKADA to ignorujemy wpis
										continue;		
									else if($zdarzenie == '1')	//jeżeli zdarzenie to WEJŚCIE to zapisujemy do raportu
									{
										//$czas -= strtotime($result['czas']);	//odejmujemy od czasu spedzonego w pracy w danym dniu
										$wejsciePom = $result2['czas'];			//pomocniczo dla obliczen czasu [spr. czy ostatnie w danym dniu bylo wejscie czy wyjscie]
									}
									else if($zdarzenie == '0')	//jeżeli zdrzenie jest WYJŚCIEM 
									{
										//echo $czas += strtotime($result['czas']);	//dodajemy czas do puli czasu pracy
										$ostatnieWY = $result2['czas'];			//zapisujemy czas do zmiennej - na koniec zapisany bedzie czas ostatniego wyjscia
									}	
									$nocka = 1;
								}
								if(strtotime($wejsciePom) > strtotime($ostatnieWY))	//jeżeli ostatnie wyjscie nastąpilo przed ostatnim wejsciem 
								{
									
									//pobieramy kolejne wyjscie
									$sql = "SELECT * FROM `log` WHERE `id_pracownika` = '".$nr_id."' AND `czas` > '".$wejsciePom."' AND `zdarzenie` = 0  AND NOT `czytnik` = 'SYSTEM' LIMIT 1";
									$sql = mysql_query($sql, $connect) or die("Błąd odczytu z BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());
									$sql = mysql_fetch_assoc($sql);
									$ostatnieWY = $sql['czas'];
									$czas += strtotime($wiersz1['czas']);	//dodajemy czas do puli czasu pracy		
								}	
							}
						}						
						else
						{
							$ostatnieWY = "W PRACY";
							$czas += strtotime("now");
						}					
					}

						$borderR = '';
	/*NIEBIESKI absencja*/	
						if($zmianaWejscie == $zmianaWyjscie)	//absencja
						{
							//$zmianaWejscie = $zmianaWyjscie = "NN";
							$borderR = "border-right: 2px solid blue; ";
							$czasZmiany = "0";
						}
						else
						{
							if($zmianaWyjscie > $zmianaWejscie)
								$czasZmiany = $zmianaWyjscie-$zmianaWejscie;
							else
								$czasZmiany = (24-$zmianaWejscie) + $zmianaWyjscie;

						}
						
						//zamieniamy uzyskany czas z powrotem na przejżyste godziny i minuty
						$gg = (int)($czas/(60*60));
						$czas -= ($gg*60*60);
						if($gg < 10)
							$gg = "0".$gg;
						$mm = (int)($czas/60);
						$czas -= ($mm*60);
						if($mm < 10)
							$mm = "0".$mm;
						$ss = (int)($czas);
						if($ss < 10)
							$ss = "0".$ss;
						if($gg > $czasZmiany || ($gg == $czasZmiany && ($mm > $nadgodziny || ($mm == $nadgodziny && $ss > '0'))))
						{
	/*ZIELONY czas w normie*/
							$borderR = "border-right: 2px solid green; ";						
						}
						else if($gg < $czasZmiany )
						{
	/*CZERWONY czas poniżej normy*/
							$borderR = "border-right: 2px solid red; ";
						}

						if($czasZmiany < 10)
							$czasZmiany = '0'.$czasZmiany.":00";
						else						
							$czasZmiany = $czasZmiany.":00";
						
					
						$sumaSpodziewana += $czasZmiany;
						$sumaRzeczywistaGG += $gg;
						$sumaRzeczywistaMM += $mm;
						$sumaRzeczywistaSS += $ss;
						
						$sqlb = "SELECT `id`, `zaliczone` FROM  `log` WHERE  `id_pracownika` =  '".$nr_id."' AND  `zdarzenie` =  '1' AND DATE(`czas`) = DATE('".$wiersz['data']."') ORDER BY  `id` ASC LIMIT 1";
						$sqlb = mysql_query($sqlb, $connect) or die("Błąd odczytu z BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());
						$sqlb = mysql_fetch_assoc($sqlb);
						
						
						$sqld = "SELECT `id`  FROM  `zmiany` WHERE `wejscie` = '".$zmianaTemp[0]."' AND `wyjscie` = '".$zmianaTemp[1]."';";
						$sqld = mysql_query($sqld, $connect) or die("Błąd odczytu z BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());
						$sqld = mysql_fetch_assoc($sqld);
						if(array_key_exists($sqld['id'], $liczniki))
							$liczniki[$sqld['id']] += 1;
						
						//wypisanie wiersza raportu dla danego dnia
						$html .= "\t\t\t\t\t<tr>\n";		
						if(!isset($_GET['pdf']))	//zmiana szerokości dla dokumentu pdf
							$html .=  "\t\t\t\t\t\t<td style='background: ".$color.";'>".$wiersz['data']."</td>\n";
						else
							$html .=  "\t\t\t\t\t\t<td width='80px' bgcolor='".$color."'>".$wiersz['data']."</td>\n";
						$html .=  "\t\t\t\t\t\t<td style='border-left: 1px solid ".$kolor."; background: ".$color.";' class='dni'>".$tydzien[$wiersz['dzien']]."</td>\n";
						$html .=  "\t\t\t\t\t\t<td style='border-left: 1px solid ".$kolor."; background: ".$color.";' class='wejscia' ";
						if(!isset($_GET['pdf']))
							$html .= "onclick=\"zmienPlan(".$idWpisu.", '".$wiersz['data']."')\"";
						$html .= ">".$zmianaWejscie."</td>\n";
/*CZERWONY - spóźnienie*/			
						if(strtotime($zmianaWejscie.":00") < strtotime(substr($pierwszeWE, -8)) && $pierwszeWE != 0 && $zmianaWyjscie != $zmianaWejscie)
							$html .=  "\t\t\t\t\t\t<td style='border-left: 1px solid ".$kolor."; background: #DB8A8A;' class='przyjscia'>".$pierwszeWE."</td>\n";
						else
							$html .=  "\t\t\t\t\t\t<td style='border-left: 1px solid ".$kolor."; background: ".$color.";' class='przyjscia'>".$pierwszeWE."</td>\n";
						$html .=  "\t\t\t\t\t\t<td style='border-left: 1px solid ".$kolor."; background: ".$color.";' class='wyjscia' ";
						if(!isset($_GET['pdf']))
							$html .= "onclick=\"zmienPlan(".$idWpisu.", '".$wiersz['data']."')\"";
						$html .= ">".$zmianaWyjscie."</td>\n";	
/*CZERWONY - wczesne wyjscie*/									
						if(strtotime($zmianaWyjscie.":00") > strtotime(substr($ostatnieWY, -8)) && $ostatnieWY != 0 && $zmianaWyjscie != $zmianaWejscie && !($pierwszeWE > $ostatnieWY && $zmianaWejscie > $zmianaWyjscie)) 
							$html .=  "\t\t\t\t\t\t<td style='border-left: 1px solid ".$kolor."; background: #DB8A8A;' class='wyjsciowy'>".$ostatnieWY."</td>\n";
						else
							$html .=  "\t\t\t\t\t\t<td style='border-left: 1px solid ".$kolor."; background: ".$color.";' class='wyjsciowy'>".$ostatnieWY."</td>\n";
						$html .=  "\t\t\t\t\t\t<td style='border-left: 1px solid ".$kolor."; background: ".$color.";' class='zmiany'>".$czasZmiany."</td>\n";
						$html .=  "\t\t\t\t\t\t<td style='border-left: 1px solid ".$kolor."; background: ".$color.";' class='zmiany'>".$gg.":".$mm."</td>\n";
						
						$tCzasZm =  (int)$gg;
						
						if($tCzasZm > 0 || (int)$mm > 0 || (int)$ss > 0)	//jeżeli mamy wpisy logowania 
						{
							//echo "JEST WPIS -> ";
							if(strcmp($ostatnieWY, "W PRACY") == 0)	//jeżeli nadal pracuje
							{
								//echo "PRACUJE";
								$html .=  "\t\t\t\t\t\t<td style='".$borderR." border-left: 1px solid ".$kolor."; background: ".$color.";' class='zmiany'>$gg:00</td>\n";
							}
							else if(count($sqlb) == 2 && !is_null($sqlb['zaliczone']))	//jeżeli jest wpis zaliczonego czasu i nie jest pusty
							{
								$a = '';
								//echo "NIE PUSTY";
								($sqlb['zaliczone'] < 10)?$a='0'.$sqlb['zaliczone']:$a=$sqlb['zaliczone'];
								$html .=  "\t\t\t\t\t\t<td style='".$borderR." border-left: 1px solid ".$kolor."; background: ".$color.";' id='id[".$sqlb['id']."]' class='zmiany'";
								if(!isset($_GET['pdf']))
									$html .= "onclick='up(".$sqlb['id'].")'";
								$html .= ">".$a.":00</td>\n";
								$sumaZaliczona += $sqlb['zaliczone'];
							}
							else if(count($sqlb) == 2 && empty($sqlb['zaliczone']))	//jeżeli wpis zaliczonego czasu jest pusty
							{
								//echo "PUSTY";
								$zaliczoneTemp = "00";
								$temp =  substr($czasZmiany, 0, 2);
								
								if($gg > 0 && $gg >= $temp)	//jeżeli czas rzeczywisty jest większy bądź równy planowanemu zaliczamy tyle ile jest zaplanowane
								{
									//echo " -> R>P ";
									$sqlc = "UPDATE `log` SET `zaliczone`='". $temp."' WHERE `id`=".$sqlb['id'].";";
									$sqlc = mysql_query($sqlc, $connect) or die("Błąd odczytu z BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());
									$zaliczoneTemp = $temp;
								}
								else if($gg > 0 && $gg < $temp) // jeżeli czas rzeczywisty jest mniejszy niż planowany to zaliczamy tylko pełne godziny
								{
									//echo " -> R<P";
									$sqlc = "UPDATE `log` SET `zaliczone`='".$gg."' WHERE `id`=".$sqlb['id'].";";
									$sqlc = mysql_query($sqlc, $connect) or die("Błąd odczytu z BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());
									$zaliczoneTemp = $gg;
								}
								$html .=  "\t\t\t\t\t\t<td style='".$borderR." border-left: 1px solid ".$kolor."; background: ".$color.";' id='id[".$sqlb['id']."]' class='zmiany'";
								if (!isset($_GET['pdf']))
									$html .= "onclick='up(".$sqlb['id'].")'";
								$html .= ">".$zaliczoneTemp.":00</td>\n";
								$sumaZaliczona += $zaliczoneTemp;
							}
						}
						else	//jeżeli niema wpisów logowania
						{
							//echo "BRAK WPISU -> ";
							if(is_null($sqlb['zaliczone']))
							{
								//echo "BRAK ZALICZONYCH";
								$html .=  "\t\t\t\t\t\t<td style='".$borderR." border-left: 1px solid ".$kolor."; background: ".$color.";' class='xxx_zaliczone' in-info='".$zmianaTemp[0]."' out-info='".$zmianaTemp[1]."' class='zmiany'>00:00</td>\n";
							}
							else if(!is_null($sqlb['zaliczone']))
							{
								$a='';
								//echo "ZALICZONE";
								($sqlb['zaliczone'] < 10)?$a='0'.$sqlb['zaliczone']:$a=$sqlb['zaliczone'];
								$html .=  "\t\t\t\t\t\t<td style='".$borderR." border-left: 1px solid ".$kolor."; background: ".$color.";' id='id[".$sqlb['id']."]' class='zmiany'";
								if (!isset($_GET['pdf']))
									$html .= "onclick='up(".$sqlb['id'].")'";
								$html .= ">".$a.":00</td>\n";
								$sumaZaliczona += $sqlb['zaliczone'];
							}
						}
						$html .=  "\t\t\t\t\t</tr>\n";
						
						
						
						//zmiana koloru wiersza i obramowania dla poprawy czytelności
						($color == "#e9e9e9")?$color = "#ffffff":$color = "#e9e9e9";
						($kolor == "#ffffff")?$kolor = "#b8b8b8":$kolor = "#ffffff";
						++$i_t;
				}	
				
				
				$gp = (int)($sumaRzeczywistaMM/60);
				$sumaRzeczywistaGG += $gp;
				$sumaRzeczywistaMM -= ($gp*60);
				$gp = (int)($sumaRzeczywistaSS/60);
				$sumaRzeczywistaMM += $gp;
				$sumaRzeczywistaSS -= ($gp*60);

				if($sumaZaliczona > $sumaSpodziewana)
				{
	//ZIELONY nadgodziny
					$borderR = "border-right: 2px solid green; ";						
				}
				else if($sumaZaliczona < $sumaSpodziewana)
				{
	//CZERWONY mało godzin				
					$borderR = "border-right: 2px solid red; ";
				}
				else
				{
					$borderR = '';
				}
					$html .=  "\t\t\t\t\t<tr>\n";
					$html .=  "\t\t\t\t\t\t<td colspan='6' style='text-align: right; background-color: silver;'>Razem:</td>\n";
					$html .=  "\t\t\t\t\t\t<td style='background-color: silver;'>";
						($sumaSpodziewana <10)? $sumaSpodziewana = "0".$sumaSpodziewana.":00</td>\n" : $sumaSpodziewana = $sumaSpodziewana.":00</td>\n";
						$html .=  $sumaSpodziewana;
						($sumaRzeczywistaGG <10)? $sumaRzeczywistaGG = "0".$sumaRzeczywistaGG :  NULL;
						($sumaRzeczywistaMM <10)? $sumaRzeczywistaMM = "0".$sumaRzeczywistaMM :  NULL;
						($sumaRzeczywistaSS <10)? $sumaRzeczywistaSS = "0".$sumaRzeczywistaSS :  NULL;

					$html .=  "\t\t\t\t\t\t<td style='background-color: silver; '>".$sumaRzeczywistaGG.":".$sumaRzeczywistaMM."</td>\n";
					$html .=  "\t\t\t\t\t\t<td style='background-color: silver; ".$borderR."'>".$sumaZaliczona.":00</td>\n";
					$html .=  "\t\t\t\t\t</tr>\n";

					$html .=  "\t\t\t\t</table>\n";
				$html .=  "\t\t\t</td>\n";	
				$html .=  "\t\t</tr>\n";	
				
				if(!isset($_GET['pdf']))
				{
					$html .=  "\t\t<tr>\n";	
					$html .=  "\t\t\t<td colspan='2'>LEGENDA:</br>";
					$html .=  "\t\t\t\t<table style='font-size: 10px;'>\t\t\t\t\t<tr>\n";
					$html .=  "\t\t\t\t\t\t<td style='border-left: 2px solid green;'> - NADGODZINY";
					$html .=  "\t\t\t\t\t\t</td>\n";
					$html .=  "\t\t\t\t\t\t<td style='border-left: 2px solid red;'> - ZA MAŁO GODZIN";
					$html .=  "\t\t\t\t\t\t</td>\n";
					$html .=  "\t\t\t\t\t\t<td style='border-left: 2px solid blue;'> - DZIEŃ WOLNY";
					$html .=  "\t\t\t\t\t\t</td>\n";
					$html .=  "\t\t\t\t\t\t<td style='background: #DB8A8A;'> &nbsp;";
					$html .=  "\t\t\t\t\t\t</td>\n";
					$html .=  "\t\t\t\t\t\t<td > - SPÓŹNIENIE / WYJŚCIE PRZED CZASEM";
					$html .=  "\t\t\t\t\t\t</td>\n";
					$html .=  "\t\t\t\t\t</tr>\n";
					$html .=  "\t\t\t\t\t<tr>\n";
					$html .=  "\t\t\t\t\t\t<td>S - SPODZIEWANY";
					$html .=  "\t\t\t\t\t\t</td>\n";
					$html .=  "\t\t\t\t\t\t<td>R - RZECZYWISTY";
					$html .=  "\t\t\t\t\t\t</td>\n";
					$html .=  "\t\t\t\t\t\t<td>Z - ZALICZONY";
					$html .=  "\t\t\t\t\t\t</td>\n";
					$html .=  "\t\t\t\t\t</tr>\n";
					$html .=  "\t\t\t\t\t<tr>\n";
					$html .=  "\t\t\t\t\t\t<td>SYMBOL:";
					$html .=  "\t\t\t\t\t\t</td>\n";
					$html .=  "\t\t\t\t\t\t<td>ILOŚĆ:";
					$html .=  "\t\t\t\t\t\t</td>\n";
					$html .=  "\t\t\t\t\t\t<td>OPIS";
					$html .=  "\t\t\t\t\t\t</td>\n";
					$html .=  "\t\t\t\t\t</tr>\n";
					foreach($liczniki as $klucz => $wartosc)
					{
						if($wartosc == 0)
							continue;
						$html .=  "\t\t\t\t\t<tr>\n";
						$html .=  "\t\t\t\t\t\t<td>".$zmianySymbole[$klucz];
						$html .=  "\t\t\t\t\t\t</td>\n";
						$html .=  "\t\t\t\t\t\t<td>".$wartosc;
						$html .=  "\t\t\t\t\t\t</td>\n";
						$html .=  "\t\t\t\t\t\t<td>".$zmianyOpisy[$klucz];
						$html .=  "\t\t\t\t\t\t</td>\n";
						$html .=  "\t\t\t\t\t</tr>\n";
					}
					$html .=  "\t\t\t\t</table>\n";	
					$html .=  "\t\t\t</td>\n";	
					$html .=  "\t\t</tr>\n";	
				}				
			}
			
			
			if($headerLogi != '')
			{
				if($headerP != $headerK)
					$sql = "SELECT * FROM `log` WHERE NOT `czytnik` = 'SYSTEM' AND `id_pracownika` = '".$nr_id."' AND DATE(`czas`) BETWEEN '".$headerP."' AND '".$headerK."' ORDER BY `id` DESC";
				else
					$sql = "SELECT * FROM `log` WHERE NOT `czytnik` = 'SYSTEM' AND `id_pracownika` = '".$nr_id."' AND STRCMP(DATE(`czas`), '".$headerP."') = 0 ORDER BY `ID` DESC";

				$result = mysql_query($sql, $connect) or die("Błąd odczytu z BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());

				$html .=  "\t\t<tr>\n";
					$html .=  "\t\t\t<th colspan='2' style='border-bottom: 1px solid #b8b8b8; border-top: 1px solid #b8b8b8; text-align: center;'><br/>ZDARZENIA CZYTNIKÓW RFID</th>\n";
				$html .=  "\t\t</tr>\n	";
				$html .=  "\t\t<tr>\n\t\t\t<td colspan='2' style='border-left: 1px solid #b8b8b8; border-bottom: 1px solid #b8b8b8; border-right: 1px solid #b8b8b8; text-align: center; padding-left: 10px'>\n";
					$html .=  "\t\t\t\t<table style='border-collapse: collapse'>\n";
					$html .=  "\t\t\t\t\t<tr>\n";
					
					$color = "#ffffff";
					$kolor = "#b8b8b8";
					$html .=  "\t\t\t\t\t\t<th style='background: ".$color.";'>Data i godzina</th>\n";
					$html .=  "\t\t\t\t\t\t<th style='background: ".$color."; border-left: 1px solid ".$kolor.";'>Zdarzenie</th>\n";
					$html .=  "\t\t\t\t\t\t<th style='background: ".$color."; border-left: 1px solid ".$kolor.";'>Skaner</th>\n";
					$html .=  "\t\t\t\t\t\t<th style='background: ".$color."; border-left: 1px solid ".$kolor.";'>ID karty</th>\n";
					$html .=  "\t\t\t\t\t\t<th style='background: ".$color."; border-left: 1px solid ".$kolor."; width: 240px;'>Uwagi</th>\n";
					$html .=  "\t\t\t\t\t</tr>\n";
					
				while($wiersz = mysql_fetch_assoc($result))
				{					
					($kolor == "#b8b8b8")?$kolor = "#ffffff":$kolor = "#b8b8b8";
					($color == "#ffffff")?$color = "#e9e9e9":$color = "#ffffff";

					$html .=  "\t\t\t\t\t<tr>\n";
						$html .=  "\t\t\t\t\t\t<td style='background: ".$color.";'>".$wiersz['czas']."</td>\n";
						$html .=  "\t\t\t\t\t\t<td style='border-left: 1px solid ".$kolor."; background: ".$color.";'>";
						if($wiersz['zdarzenie'] == '1')
							$html .= "WEJŚCIE</td>\n";
						if($wiersz['zdarzenie'] == '0')
							$html .= "WYJŚCIE</td>\n";
						if($wiersz['zdarzenie'] == '3')
							$html .= "BLOKADA</td>\n";
						$html .=  "\t\t\t\t\t\t<td style='border-left: 1px solid ".$kolor."; background: ".$color.";'>".substr($wiersz['czytnik'],0,8)."</td>\n";
						$html .=  "\t\t\t\t\t\t<td style='border-left: 1px solid ".$kolor."; background: ".$color.";'>".$wiersz['id_karty']."</td>\n";
						$html .=  "\t\t\t\t\t\t<td style='width: 240px; border-left: 1px solid ".$kolor."; background: ".$color.";'>".$wiersz['powod']."</td>\n";
					$html .=  "\t\t\t\t\t</tr>\n";
				}
					$html .=  "\t\t\t\t</table>\n";
				$html .=  "\t\t\t</td>\n\t\t</tr>\n";
			}

			$html .=  "\t</table>\n";
			$html .=  "</center>\n";
			
			if(!isset($_GET['where']) || $_GET['where'] == 'one')
				break;
			else
				$html .= "<br/><br/>";
		
		}
		
		
		$html .= "</body></html>";
		
		if(isset($_GET['pdf']))
		{
			$html = str_replace("style='width: 240px; border-left", "style='border-left", $html);
			$html = str_replace("; width: 240px;'>Uwagi", "'>Uwagi", $html);
			$html = str_replace('</tr>	<tr>', '</tr><tr>', $html);
			$html = str_replace('<br/>', '<br/>&nbsp;', $html);
			$html = str_replace('Rozpoczęcie<br/>zmiany', 'Początek<br/>zmiany', $html);
			$html = str_replace('<td></td>', '<td>&nbsp;</td>', $html);
			$html = str_replace('<tr></tr>', '<tr><td>&nbsp;</td></tr>', $html);
			$html = str_replace('<td style=\'background: #e9e9e9;\'>', '<td bgcolor=\'#e9e9e9\'>', $html);				// kolumna data ->data
			$html = str_replace('background: #e9e9e9;\'', '\' bgcolor="#e9e9e9"', $html);
			$html = str_replace('background: #ffffff;\'', '\' bgcolor="#ffffff"', $html);
			$html = str_replace('background: #DB8A8A;\'', '\' bgcolor="#DB8A8A"', $html);
			$html = str_replace('class=\'dni\'', 'width="25px"', $html);			// kolumna data -> dzień tygodnia
			$html = str_replace('class=\'wejscia\'', 'width="57px"', $html);		// kolumna poczatek zmiany
			$html = str_replace('class=\'wyjscia\'', 'width="57px"', $html);		// kolumna koniec zmiany
			$html = str_replace('class=\'przyjscia\'', 'width="85px"', $html);		// kolumna wejscie
			$html = str_replace('class=\'wyjsciowy\'', 'width="85px"', $html);		// kolumna wyjscie
			$html = str_replace('class=\'zmiany\'', 'width="53px"', $html);			// kolumna spodziewany/rzeczywisty/zaliczony
			$html = str_replace('class=\'czasowy\'', 'width="180px"', $html);		// kolumna czas pracy
			$html = str_replace('class="odejscia\'', 'width="90px"', $html);
			$html = str_replace('class="odejscia\'', 'width="90px"', $html);
			$html = str_replace('\'', '"', $html);
			
			//echo $html;
			//echo "<textarea>".$html."</textarea>";
			$htmlTemp = explode("</&nnnnnn&;>", $html);
			if(count($htmlTemp) > 1)
			{
				foreach($htmlTemp as $index => $dataHtml)
				{
					
					echo "<textarea>".$html."</textarea>";
					//print_pdf($dataHtml, $tytul."_".$index, True);
				}
			}
			else
			{
				print_pdf($html, $tytul, False);
			}
		}
		else 
			echo $html;
		
		mysql_close($connect) or die("Błąd, połączenie BD nie zakończone. - Proszę o kontakt z informatykiem.");
	}

	if(isset($_GET['secRap']))
	{		
		include 'connecting.php';
		$zapytanie='';
		$limit = 0;
		
		
		if(isset($_POST['i']) && $_POST['i']>0)
			$iloscNS = $_POST['i'];
		else
			$iloscNS = 40;
		if(isset($_POST['p']) && $_POST['p'] > 0)
		{
			$limit = $_POST['p']*$iloscNS;
		}
		
		$sql = "SELECT `log`.*, IFNULL(`nazwisko`, 'Pracownik nieznany') AS 'nazwisko' FROM `log` LEFT JOIN `pracownicy` ON `log`.`id_pracownika` = `pracownicy`.`id` WHERE ".$zapytanie." NOT `log`.`czytnik` = 'SYSTEM' ORDER BY `log`.`id` DESC LIMIT ".$limit.", ".$iloscNS;
		
		if(isset($_POST['od']) && $_POST['od'] != '')	
		{
			if(isset($_POST['do']) && $_POST['do'] != '')
				$sql = "SELECT `log`.*, IFNULL(`nazwisko`, 'Pracownik nieznany') AS 'nazwisko' FROM `log` LEFT JOIN `pracownicy` ON `log`.`id_pracownika` = `pracownicy`.`id` WHERE DATE(`czas`) BETWEEN DATE('".$_POST['od']."') AND DATE('".$_POST['do']."') AND NOT `log`.`czytnik` = 'SYSTEM' ORDER BY `log`.`id` DESC LIMIT ".$limit.", ".$iloscNS;
			else				
				$sql = "SELECT `log`.*, IFNULL(`nazwisko`, 'Pracownik nieznany') AS 'nazwisko' FROM `log` LEFT JOIN `pracownicy` ON `log`.`id_pracownika` = `pracownicy`.`id` WHERE DATE(`czas`) = DATE('".$_POST['od']."') AND NOT `log`.`czytnik` = 'SYSTEM' ORDER BY `log`.`id` DESC LIMIT ".$limit.", ".$iloscNS;
		}

		$result = mysql_query($sql, $connect) or die("Błąd odczytu z BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());

		$sql = "SELECT DISTINCT COUNT(*) AS ilosc FROM `log` ".$zapytanie."ORDER BY `log`.`id` DESC";
		$result1 = mysql_query($sql, $connect) or die("Błąd odczytu z BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());		
		$result1 = mysql_fetch_array($result1);
		mysql_close($connect) or die("Błąd, połączenie BD nie zakończone. - Proszę o kontakt z informatykiem.");

		$maxPage = $result1['0'];
		if($maxPage < $iloscNS)
			$maxPage = $iloscNS;

		$i = 0;
		$kolor = "#b8b8b8";
		$color = "#ffffff";
	
		echo "<!DOCTYPE html><html><head>";
		echo "<style>th, td {padding: 5px 5px 5px 5px;}</style>";
		echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />";
		echo "<title>LOG</title>";
		echo "<script src=\"jquery.js\"></script>";
		echo "<script>";
		echo "function okno(e){";
		echo "$('form').html($('form').html()+\"<br/><br/><textarea style='visibility: hidden;' name='dane'></textarea>\");";
		echo "$('textarea').text(\"<table>\"+e+\"</table>\");";
		echo "$('form').attr('action', 'dbase.php?pdf&title=LOGI');";
		echo "$('form').submit();";
		echo "}";
		echo "</script>";
		echo "</head><body onunload='opener.okienka = true;'>";
		echo "<center><form action='dbase.php?secRap' method='POST'>";
		echo "<br/><br/>";
		if(isset($_POST['od']) && $_POST['od'] != '')	
		{
			
			echo "OD: <input name='od' type='date' placeholder='Początek' value='".$_POST['od']."'/>";
			if(isset($_POST['do']))				
				echo " DO: <input name='do' type='date' placeholder='Koniec' value='".$_POST['do']."'/>";
			else
				echo " DO: <input name='do' type='date' placeholder='Koniec'/>";

		}
		else	
		{
			echo "OD: <input name='od' type='date' placeholder='Początek'/>";
			echo " DO: <input name='do' type='date' placeholder='Koniec'/>";
		}
		
		if(isset($_POST['p']))
		{
			if($_POST['p'] < 0)
				$_POST['p'] = 0;
			echo "<input type='hidden' name='p' id='p' value='".$_POST['p']."'/>";
		}
		else
		{
			echo "<input type='hidden' name='p' id='p' value='0'/>";
		}
		echo "<button type='button' onClick=\"$('#p').val(0); submit();\">Zmień</button>&nbsp;";
		echo "<button type='button' onClick=\"okno($('table').html())\">PDF</button>&nbsp;";
		echo "<br/><br/>";
		
		if(isset($_POST['i']))
		{
			if($_POST['i'] < 1)
				$_POST['i'] = 40;
			echo "Ilość rekordów na stronie: <input onchange=\"$('#p').val(0);\" type='number' min='1' max='100' name='i' id='i' value='".$_POST['i']."'/>";
		}
		else
		{
			echo "Ilość rekordów na stronie: <input onchange=\"$('#p').val(0);\" type='number' min='1' max='100' name='i' id='i' value='40'/>";
		}

		echo "<br/><br/>";
		echo "<table style='border-collapse: collapse' align='center'>";
			while($wiersz = mysql_fetch_assoc($result))
			{		
				if($i == 0 || $i%20 == 0)
				{
						echo "<tr>";
							echo "<th width='140px' bgcolor='".$color."' align='center'><b>Data i godzina</b></th>";
							echo "<th style='border-left: 1px solid #b8b8b8;' width='80px' bgcolor='".$color."' align='center'><b>Zdarzenie</b></th>";
							echo "<th style='border-left: 1px solid #b8b8b8;' width='70px' bgcolor='".$color."' align='center'><b>Skaner</b></th>";
							echo "<th style='border-left: 1px solid #b8b8b8;' bgcolor='".$color."' align='center'><b>Nr Karty</b></th>";
							echo "<th style='border-left: 1px solid #b8b8b8;' width='160px' bgcolor='".$color."' align='center'><b>Nazwisko</b></th>";
							echo "<th style='border-left: 1px solid #b8b8b8; white-space: pre-wrap' width='80px' bgcolor='".$color."' align='center'><b>Powod</b></th>";
						echo "</tr>";					
					($kolor == "#b8b8b8")?$kolor = '#ffffff':$kolor = "#b8b8b8";
					($color == "#ffffff")?$color = "#e9e9e9":$color = "#ffffff";
				}
				echo "<tr>";
					echo "<td style='background: ".$color.";' bgcolor=".$color." width='140px' align='center'>".$wiersz['czas']."</td>";
					echo "<td style='border-left: 1px solid ".$kolor."; background: ".$color.";' bgcolor='".$color."' align='center'>";
					if($wiersz['zdarzenie'] == '1')
						echo "WEJŚCIE</td>";
					else if($wiersz['zdarzenie'] == '0')
						echo "WYJŚCIE</td>";
					else						
						echo "BLOKADA</td>";
					echo "<td style='border-left: 1px solid ".$kolor."; background: ".$color.";' bgcolor='".$color."' align='center'>".$wiersz['czytnik']."</td>";
					echo "<td style='border-left: 1px solid ".$kolor."; background: ".$color.";' bgcolor='".$color."' align='center'>".$wiersz['id_karty']."</td>";
					echo "<td style='border-left: 1px solid ".$kolor."; background: ".$color.";' bgcolor='".$color."' align='center'>".$wiersz['nazwisko']."</td>";
					echo "<td style='border-left: 1px solid ".$kolor."; background: ".$color."; white-space: pre-wrap;' bgcolor='".$color."' align='center' >".$wiersz['powod']."</td>";
				echo "</tr>";

				($kolor == "#b8b8b8")?$kolor = '#ffffff':$kolor = "#b8b8b8";
				($color == "#ffffff")?$color = "#e9e9e9":$color = "#ffffff";
				
				$i++;
			}
		echo "</table>";
		echo "</td></tr><br/><br/>";
		if(!isset($_POST['p']) || $_POST['p'] <= 0)
			echo "<button disabled type='button' onClick=\"$('#p').val($('#p').val()-1); submit();\">Poprzednie</button>";
		else			
			echo "<button type='button' onClick=\"$('#p').val($('#p').val()-1); submit();\">Poprzednie</button>";
		if(isset($_POST['p']) && $_POST['p'] > 0)
			echo " ".($_POST['p']+1)."/".ceil($maxPage/$iloscNS)." ";
		else
			echo " 1/".ceil($maxPage/$iloscNS)." ";
		$przycisk = "";
		if(ceil($maxPage/$iloscNS) <= 1 || (isset($_POST['p']) && $_POST['p'] + 1 > ceil($maxPage/$iloscNS)))			//((isset($_POST['p']) && ceil($maxPage/$iloscNS) > 1) || ceil($maxPage/$iloscNS) > $_POST['p']+1)
			$przycisk = " disabled";
		echo "<button ".$przycisk." type='button' onClick=\"$('#p').val(parseInt($('#p').val())+1); submit();\">Następne</buton>";
		echo "</form>";
		echo "</center>";
		echo "</body></html>";
	}

	if(isset($_GET['pdf']) && isset($_POST['dane']))
	{		
		($_POST['do'] != '')? $do = $_POST['do']: $do = date("Y-m-d");
		($_POST['od'] != '')? $od = $_POST['od']: $od = "*";
		$pdf = "<center><h1>REJESTR ZDARZEŃ</h1><br/><br/><h3>".$od." - ".$do."</h3><br/><br/>";
		$pdf .= $_POST['dane'];
		$pdf .= "</center>";
		print_pdf($pdf, $_GET['title']);
	}

	if(isset($_GET['check']) && isset($_POST['id']) && isset($_POST['nr']))
	{
		include 'connecting.php';

		$sql = "SELECT `zdarzenie`, `status`, `sprawnosc` FROM `log` JOIN `pracownicy` ON `nr_karty` = `id_karty` WHERE `pracownicy`.`id` = '".$_POST['nr']."' ORDER BY `log`.`id` DESC LIMIT 1";
		$result = mysql_query($sql, $connect) or die("Błąd odczytu z BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());
		mysql_close($connect) or die("Błąd, połączenie BD nie zakończone. - Proszę o kontakt z informatykiem.");
		
		$wiersz = mysql_fetch_assoc($result);
		
			if($wiersz['status'] == '1')
				echo "TRUE->".$wiersz['zdarzenie'];
			else
				echo "FALSE->".$wiersz['zdarzenie'];

			if($wiersz['sprawnosc'] == '1')
				echo "->disabled";
	}

	if(isset($_GET['addAH']))
	{
		include 'connecting.php';
		if(!isset($_GET['update']))
		{
			$sqla = "SELECT MAX(`id`)+1 AS 'id' FROM `log`";
			$sqla = mysql_query($sqla, $connect) or die("Błąd odczytu z BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());
			$sqla = mysql_fetch_assoc($sqla);
			$sqla = $sqla['id'];
			
			
				$sqlb = "INSERT INTO  `log` (`id`, `czas`, `zaliczone`, `zdarzenie`, `czytnik`, `zmiana_p`, `zmiana_k`, `id_pracownika`) VALUES ('".$sqla."', '".$_POST['data']."', '".$_POST['val']."', '1', 'SYSTEM', '6', '6', '".$_POST['id']."')";
				
			$sqlb = mysql_query($sqlb, $connect) or die("Błąd odczytu z BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());
			
			
			if($_POST['in'] == $_POST['out'])			
				$sqlb = "INSERT INTO  `log` (`id`, `czas`, `zaliczone`, `zdarzenie`, `czytnik`, `zmiana_p`, `zmiana_k`, `id_pracownika`) VALUES ('".++$sqla."', '".$_POST['data']."', '".$_POST['val']."', '0', 'SYSTEM', '6', '6', '".$_POST['id']."')";
			else
				$sqlb = "INSERT INTO  `log` (`id`, `czas`, `zaliczone`, `zdarzenie`, `czytnik`, `zmiana_p`, `zmiana_k`, `id_pracownika`) VALUES ('".++$sqla."', '".$_POST['data']."', '".$_POST['val']."', '0', 'SYSTEM', '".substr($_POST['in'], 0, 2)."', '".substr($_POST['out'], 0, 2)."', '".$_POST['id']."')";
			$sqlb = mysql_query($sqlb, $connect) or die("Błąd odczytu z BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());
		}
		else
		{		
			$val;
			if(($_POST['val'] == '' || empty($_POST['val'])) && $_POST['val'] != 0)
				$val = 'NULL';
			else
				$val = "'".$_POST['val']."'";
			$sqlb = "UPDATE `log` SET `zaliczone` = ".$val." WHERE `id` = '".$_POST['id']."'";
			$sqlb = mysql_query($sqlb, $connect) or die("Błąd odczytu z BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());
		}
		mysql_close($connect) or die("Błąd, połączenie BD nie zakończone. - Proszę o kontakt z informatykiem.");
		echo "OK";
	}

	if(isset($_GET['calendar']))
	{
		//$.post('dbase.php?calendar', 'id=0&pracownik=".$headerID."&nr='+rev+'&data='+data, function(e){});
		include "connecting.php";
		
		
		$sql = "SELECT MAX(`id`)+1 AS 'id' FROM `log`";
		$sql = mysql_query($sql, $connect) or die("Błąd zapisu do BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());
		$sqla = mysql_fetch_row($sql);
		$sql = "SELECT `wejscie`, `wyjscie` FROM `zmiany` WHERE `id` = '".$_POST['nr']."';";
		$sql = mysql_query($sql, $connect) or die("Błąd odczytu z BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());
		$sql = mysql_fetch_assoc($sql);
		
		if($_POST['id'] == 0)	//jeżeli niema wpisu dodajemy nowy
		{
			$sql = "INSERT INTO `log` (`id`, `id_pracownika`, `zmiana_p`, `zmiana_k`, `czytnik`, `czas`) VALUES (".$sqla[0].", '".$_POST['pracownik']."', '".$sql['wejscie']."', '".$sql['wyjscie']."', 'SYSTEM', '".$_POST['data']."');";
		}
		else
		{
			$sql = "UPDATE `log` SET `zmiana_p` = ".$sql['wejscie'].", `zmiana_k`= ".$sql['wyjscie']." WHERE `id` = '".$_POST['id']."'";
		}
		
		$sql = mysql_query($sql, $connect) or die("Błąd zapisu do BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());
		//$sql = mysql_fetch_assoc($sql);
		mysql_close($connect) or die("Błąd, połączenie BD nie zakończone. - Proszę o kontakt z informatykiem.");
		echo "OK";
	}

	function print_pdf($daneHTML, $title, $zip)
	{
		include 'connecting.php';
		require_once('TCPDF/config/tcpdf_config.php');
		
		$original_mem = ini_get('memory_limit');
		// zmiana limitu pamięci aby generować większe zestawienia
		ini_set('memory_limit','640M');				

		// Include the main TCPDF library (search the library on the following directories).
		$tcpdf_include_dirs = array(
			realpath('TCPDF/tcpdf.php'),
			'/usr/share/php/tcpdf/tcpdf.php',
			'/usr/share/tcpdf/tcpdf.php',
			'/usr/share/php-tcpdf/tcpdf.php',
			'/var/www/tcpdf/tcpdf.php',
			'/var/www/html/tcpdf/tcpdf.php',
			'/usr/local/apache2/htdocs/tcpdf/tcpdf.php'
		);
		foreach ($tcpdf_include_dirs as $tcpdf_include_path) {
			if (@file_exists($tcpdf_include_path)) {
				require_once($tcpdf_include_path);
				break;
			}
		}

		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		
		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor($nazwaFirma);
		$pdf->SetTitle($title);

		$pdf->SetMargins(PDF_MARGIN_LEFT+10, 8, PDF_MARGIN_RIGHT+10); //marginesy. drugi - góra
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		if (@file_exists(dirname(__FILE__).'/TCPDF/lang/pol.php')) 
			{
				require_once(dirname(__FILE__).'/TCPDF/lang/pol.php');
				$pdf->setLanguageArray($l);
			}
		$pdf->setPrintHeader(false); // usunięcie stopki i nagłówka strony header/footer
		$pdf->setPrintFooter(false);
		$pdf->SetFont('dejavusans', '', 10); //polskie znaki - dejavusans lub freesans
		$pdf->AddPage();

		//$pdf->Write(0, $daneHTML, '', 0, '', true, 0, false, false, 0);
		$pdf->writeHTML($daneHTML, true, false, true, false, '');
		ob_end_clean();
		//ob_clean();	
		
		if($zip)
		{
			$pdf->Output('/pdf/'.$title.'.pdf', 'F');
		}
		$pdf->Output('/document.pdf', 'I');
		$pdf->Close();
		ini_set('memory_limit',$original_mem);	
			
		return;
	}
?>