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
		<script>	
			czas();
			function zeraWiodace(i)
			{
				return (i<10)? '0'+i:i;
			}

			function czas()
			{
					var newdate = new Date();
					var dzis = zeraWiodace(newdate.getDate());
					const days = ["Niedziela", "Poniedziałek", "Wtorek", "Środa", "Czwartek", "Piątek", "Sobota"];

					dzis += "."+zeraWiodace(newdate.getMonth()+1);
					dzis += "."+newdate.getFullYear();


					if(newdate.getDay()%2 == 0 && $('#logo').css('background') != 'url(\'plastwil_red.png\')')
					{			
						$('#logo').css('background', 'url(\'plastwil_red.png\')');
					}

					var godzina = zeraWiodace(newdate.getHours());
					godzina += ":"+zeraWiodace(newdate.getMinutes());
					godzina += ":"+zeraWiodace(newdate.getSeconds());
					$('#czasAkt').html(godzina+"<br/>"+dzis+"<br/>"+days[newdate.getDay()]);
					
				setTimeout(function()
				{
					czas();
				}, 500)
			}
		</script>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" type="text/css" href="style.css">
		<title>RCP - <?php echo $nazwaFirma ?></title>
	</head>
	<body style="height: 300px;">
			<div id="naglowek">
				<div id='logo'>
				</div>
				<div id="tytul" style="font-size: 30px">
					REJESTRACJA CZASU PRACY
				</div>
				<div id="czasAkt"> <!-- ZEGAR CZASU RZECZ.  -->
				</div>
			</div>
			<div id="center" style='height: 90px; margin-top: 80px;'>
				<table style='margin: auto;'>
					<tr>
						<th><a href='hr.php'>Dział HR</a></th>
					<tr>
					</tr>
						<th><a href='sec.php'>Portiernia</a></th>
					</tr>
				</table>
			</div>
			<div id="bottom">
				&nbsp;
			</div>
			<div id="foother">
				© 2018 by adres433
			</div>	
	</body>
</html>