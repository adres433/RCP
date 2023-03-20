<!DOCTYPE html>
<?php

@include 'connecting.php';

$sql = "SELECT `nazwa` FROM  `plany`";

$result = mysql_query($sql, $connect) or die("Błąd zapisu do BD - proszę o kontakt z informatykiem.</br></br>".mysql_error());
mysql_close($connect) or die("Błąd, połączenie BD nie zakończone. - Proszę o kontakt z informatykiem.");

?>
<html>
	<head> 
		<script src="jquery.js"></script>
		<script src="plans.js"></script>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" type="text/css" href="style.css">
		<title>KADRY - PLANY ZMIAN</title>
	</head>
	
	<body>
		<div id='przyciemnij'></div>
		<div class='komunikaty' id='komunikat'>
			<table><form action='javascript: return false' id='nowyP'>
				<tr>
					<th>NOWY PLAN ZMIANY</th>
				</tr>
				<tr>
					<td><input placeholder='Nazwa' type='text' id='nazwa' name='nazwa'/></td>
				</tr>
				<tr>
					<td><input autocomplete='off' placeholder='Dni na cały cykl' type='number' id='dni' name='dni'/></td>
				</tr>
				<tr>
					<td><input autocomplete='off' placeholder='Ilość brygad' type='number' id='brygady' name='brygady'/></td>
				</tr>
				<tr>
					<td><button id='addPlan'>Dodaj nowy</button></td>
				</tr></form>
			</table>
		</div>
		<div class='komunikaty' id='komunikat2'>
			<table id='planZmiany'>
				<tr>
					<th>Wprowadź numery zmian dla poszczególnych brygad:</th>
				</tr><tr>
					<td><h5>Począwszy od dnia dzisiejszego.</h5></td>
				</tr>
			</table>
			<form>
				<table id='planPola'>
				</table> 
			</form> 
			<br/><br/><button id='addPlanSave'>Zapisz</button>
		</div>
		<div class='komunikaty' id='komunikat3' style='height: 350px;'>
			<table style='padding-left: 30px; padding-top: 20px;'>
				<tr>
					<th style='border-bottom: 1px solid #8b8b8b; border-top: 1px solid #8b8b8b; width: auto' colspan='2'>Dodaj godzinę</th>
				</tr>
				<tr>
					<th colspan ='2'>Podaj godziny wejścia i wyjścia dla nowego czasu pracy.</th>
				</tr>
				<tr>
					<td colspan ='2'>TYLKO PEŁNE GODZINY</td>
				</tr>
				<tr>
					<th>WEJŚCIE</th>
					<th>WYJŚCIE</th>
				</tr>
				<tr>
					<td><input placeholder='Godzina wejścia' type='number'  max='24' name='in' id='in' style='width: auto;'/></td>
					<td><input placeholder='Godzina wyjścia'type='number'  max='24' name='out' id='out' style='width: auto;'/></td>
				</tr>
				<tr>
					<td><input placeholder='Opis' type='text'  max='255' name='opis' id='opis' style='width: auto;'/></td>
					<td><input placeholder='Symbol' type='text'  max='255' name='symbol' id='symbol' style='width: auto;'/></td>
				</tr>
				<tr>
					<td colspan='2'><br/><button id='hoursAdd'>Zapisz</button><br/><br/></td>
				</tr>
				<tr>
					<th style='border-bottom: 1px solid #8b8b8b; border-top: 1px solid #8b8b8b;' colspan='2'>Usuń godzinę</th>
				</tr>
				<tr>
					<td><input type='number' name='id' id='id' style='width:auto;' placeholder='Nr zestawu godzin'/></td>
					<td><br/><button id='delHours'>Usuń</button></td>
				</tr>
			</table>
		</div>
		<div class='komunikaty' id='komunikat4'><br/>
			<table id='planZmianyUpdate'>
				<tr>
					<th>Wprowadź numery zmian dla poszczególnych brygad:</th>
				</tr><tr>
					<td><h5>Począwszy od dnia: </h5></td>
				</tr>
			</table>
			<form>
				<table id='planPolaUpdate'>
				</table> 
			</form> 
			<input style="width: 50px" type='number' min='1' name='numDays' id='numDays'/>
			<select id='whereAdd'>
				<option value='0'>Przed</option>
				<option value='1'>Po</option>				
			</select>
			<button id='addPlanDay'>Dodaj dzień</button> 
			<br/><br/><button id='editPlanSave'>Zapisz</button>
			<br/>
		</div>
			<div id="naglowek">
				<div id='logo'>
				</div>
				<div id="tytul">
					KADRY - PLANY ZMIAN
				</div>
				<div id="czasAkt"> <!-- ZEGAR CZASU RZECZ.  -->
				</div>
			</div>
			<div id="center">
				<div class="zdjecie" style="background: none; width: 199px; border-left: 1px solid #b8b8b8">
				<br/>LEGENDA:<br/><br/>
				<span id='legenda'>
				Wybierz plan</br>
				Z listy po lewej stronie.
				</span>
				</div>
				<div id="operacjaPlan">
					<h1>ISTNIEJĄCE PLANY:</h1>
					<select multiple name='wyborPlanu' id='wyborPlanu'>
					<?php
					while($wiersz = mysql_fetch_row($result))
					{
						echo "<option value='".$wiersz[0]."'>".$wiersz[0]."</option>";
					}
					?>
					</select>
				</div>
			</div>
			<div id="bottom">
				<button class="przycisk" id='add'>Dodaj plan</button>
				<button class="przycisk" id='modify'>Zobacz plan</button>
				<button class="przycisk" id='del'>Usuń plan</button>
				<button class="przycisk" id='hours'>Godziny</button>
				<button class="przycisk" id='back'>Wróć</button>
			</div>
			<div id="foother">
				© 2018 by adres433
			</div>	
	</body>
</html>