<!DOCTYPE html>
<?php
	@include 'connecting.php';
?>
<html>
	<head> 
		<script>
			var ser = '<?php echo $sec; ?>';
		</script>
		<script src="jquery.js"></script>
		<script src='sec.js'></script>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" type="text/css" href="style.css">
		<title>PORTIERNIA</title>
	</head>
	<body>
		<div id='przyciemnij'></div>
		<div class='komunikaty' id='komunikat'>
		
			<table id='presentTable'>
				<tr>
					<th colspan='5' class='naglowekTable' id='naglowekTable'>
						
					</th>
				</tr>
				<tr>
					<th>
						Lp.
					</th>
					<th>
						Imię i nazwisko:
					</th>
					<th>
						Nr Karty:
					</th>
					<th>
						Wejście:
					</th>
					<th id='nbsp'>
						&nbsp;
					</th>
				</tr>
			</table>
		</div>
		<div class='komunikaty' id='komunikat1'>
		<!-- KOMUNIKAT DO WYKORZYSTANIA -->
		</div>
			<div id="naglowek">
				<div id='logo'>
				</div>
				<div id="tytul">
					PORTIERNIA
				</div>
				<div id="czasAkt"> <!-- ZEGAR CZASU RZECZ.  -->
				</div>
			</div>
			<div id="center">
			<form id='formularz' action='return false;'>
	<!-- nrpracownika -->	<input type='hidden' name='nrpracownika' id='nrpracownika' value='0'/>
				<div class="zdjecie" id="img">
				</div>
				<div id="logDiv">
					<textarea name="log" id="log" disabled></textarea>
				</div><div id="operacja">
					<table>
						<tbody>
						<tr>
							<td>Imię i nazwisko:</td>
	<!-- imie_nazwisko -->						<td><input name="nazwisko" id="nazwisko" type="text"></td>
						</tr>
						<tr>
							<td>Stanowisko:</td>
	<!-- stanowisko -->						<td><input name="stanowisko" id="stanowisko" type="text" style='font-size 30px;'></td>
						</tr>
						<tr>
							<td>Status:</td>
							<td>
	<!-- status zatrudnienia -->							<input name="status" id="status" type="text">
							</td>
						</tr>
						<tr>
							<td>Nr karty:</td>
	<!-- karta -->						<td><input name="karta" id="karta" disabled type="text"></td>
						</tr>
						<tr>
							<td>Wejście:</td>
	<!-- wejscie -->						<td><input name="wejscie" id="wejscie" type="text"></td>
						</tr>
						<tr>
							<td>Wyjście:</td>
	<!-- wyjscie -->						<td><input name="wyjscie" id="wyjscie" type="text"></td>
						</tr>
					</tbody></table>
				</div>
			</form>
			</div>
			<div id="bottom">
				<button class="przycisk" id='uphours'>Nadgodziny</button>
				<button class="przycisk" id='present'>Obecni</button>
				<button class="przycisk" id='absent'>Nieobecni</button>
				<button class="przycisk" id='logi'>LOG</button>
			</div>
			<div id="foother">
				© 2018 by adres433
			</div>	
	</body>
</html>