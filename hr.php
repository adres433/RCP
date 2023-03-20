<!DOCTYPE html>
<?php
	@include 'connecting.php';
?>
<html>
	<head> 
		<script>
			var ser = '<?php echo $hr; ?>';
		</script>
		<script src="jquery.js"></script>
		<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
		<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
		<script src='script.js'></script>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" type="text/css" href="style.css">
		<title>KADRY</title>
	</head>
	<body>
		<div id='przyciemnij'></div>
		<div class='komunikaty' id='komunikat'>
			<h3>Wybierz pracownika do edycji:</h3>
			<select id='lista'>
			</select>
		</div>
		<div class='komunikaty' id='komunikat1'>
			<h3>Zbliż kartę do czytnika.</h3><br/><br/><br/><br/>
			<h3 id='czas'></h3>
		</div>
		<div class='komunikaty' id='komunikat2' style='padding: 15px; height: 245px'>
			<table>
				<tr>
					<td>
						<input type='hidden' value='one' id='whereSend'/>
						<select id='raportUser' name='raportUser' style='border: none;'>
						</select>
					</td>
				</tr>
				<tr>
					<th><br/>Początek:</th>
					<th><br/>Koniec:</th>
				</tr>
				<tr>
					<td>
						<input type='date' id='dataP' name='dataP'/>
					</td>
					<td>
						<input type='date' id='dataK' name='dataK'/>
					</td>
				</tr>
				<tr>
					<td colspan='2' style='text-align: left;'>
						<input style='width: 20px' type='checkbox' id='czasy' name='czasy' value='1' checked/>Wykaz czasu pracy
					<br/>
						<input style='width: 20px' type='checkbox' id='logi' name='logi' value='1' checked/>Zdarzenia czytników RFID
					</td>
				</tr>
				<tr>
					<th colspan='2'><br/><br/><button id='raportGen' type='button'>Generuj raport</button>&nbsp;<button id='raportGenPDF' type='button'>Generuj PDF</button><button id='raportGenXLS' type='button'>Generuj XLS</button></th>
				</tr>
			</table>
		</div>
		<div class='komunikaty' id='komunikat3'>
			<h3>Wybierz pracownika do usunięcia:</h3>
			<select id='idWorker'>
			</select>
			<br/><br/>
			<button id='delWS'>Usuń</button>
			<br/><br/>
		</div>
				
		<div id="naglowek">
				<div id='logo'>
				</div>
				<div id="tytul">
					KADRY
				</div>
				<div id="czasAkt"> <!-- ZEGAR CZASU RZECZ.  -->
				</div>
			</div>
			<div id="center">
			<form id='formularz' action='return false;'>
	<!-- nrpracownika -->	<input type='hidden' name='nrpracownika' id='nrpracownika' value='0'/>
				<div class="zdjecie" id="img">
				</div>
				<div id="nazwisko">
	<!-- imie_nazwisko -->				<input name="nazwisko" id="imie_nazwisko" placeholder='IMIĘ I NAZWISKO' type="text">
				</div><div id="operacja">
					<table>
						<tbody>
						<tr>
							<td>PESEL:</td>
	<!-- PESEL -->						<td><input name="pesel" id="pesel" type="number"></td>
						</tr>
						<tr>
							<td>Stanowisko:</td>
	<!-- stanowisko -->						<td><input name="stanowisko" id="stanowisko" type="text"></td>
						</tr>
						<tr>
							<td>Status:</td>
							<td>
	<!-- status -->							
								<input class='valid' name="status" id='status1' value="1" style="width: 25px; border: none;" type="radio">PRACUJE
								<input name="status" class='valid' id='status0' value="0" style="width: 25px; border: none;" type="radio" checked>NIE PRACUJE
							</td>
						</tr>
						<tr>
							<td colspan='2'>Bramka dla niepełnosprawnych: <input type='checkbox' name='sprawnosc' id='sprawnosc' value='1'/></td>
						</tr>
						<tr>
							<td>System pracy:</td>
	<!-- plan -->						<td><select name="plan" id="plan"></select></td>
						</tr>
						<tr>
							<td>Brygada:</td>
	<!-- brygada -->						<td><select name="brygada" id="brygada" style='border: none;'></select></td>
						</tr>
						<tr>
							<td>Dział / grupa:</td>
	<!-- grupa -->						<td>
											<input name="grupa" id="grupa" list='podpowiedzi'>
											<datalist id='podpowiedzi'></datalist>
										</td>
						</tr>
						<tr>
							<td>Nr karty:</td>
	<!-- karta -->						<td><input placeholder='Przyłóż kartę do czytnika aby pobrać identyfikator' name="karta" id="karta" disabled type="text"></td>
						</tr>
					</tbody></table>
				</div>
			</form>
			</div>
			<div id="bottom">
				<button class="przycisk" id='read' disabled='true'>Zapisz <br/> kartę</button>
				<button class="przycisk" id='add'>Dodaj pracownika</button>
				<button class="przycisk" id='edit'>Edytuj pracownika</button>
				<button class="przycisk" id='delWorker'>Usuń pracownika</button>
				<button class="przycisk" id='save'>Zapisz<br/>zmiany</button>
				<button class="przycisk" id='cancel'>Porzuć zmiany</button>
				<BR/>
				<BR/>
				<button class="przycisk" id='rapGroup'>Raporty grupowe</button>
				<button class="przycisk" id='listy'>Raporty indywidualne</button>
				<button class="przycisk" id='plany'>Plany<br/>zmian</button>
				<button class="przycisk" id='logiAll'>LOG<br/>&nbsp;</button>
			</div>
			<div id="foother">
				© 2018 by adres433
			</div>	
	</body>
</html>