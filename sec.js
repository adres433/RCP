var okienka = true;
var connection ;
var ws_timeout;

$('document').ready(function()
{
	ws();
	$('input').attr('disabled', true);
	czas();



	$('#przyciemnij').click(function()
	{
		if($('#komunikat').css('display') == 'block')
		{
			$('#przyciemnij').fadeOut(500);
			$('#komunikat').fadeOut(500);
		}
	});
	$('#present').click(function()
	{
		var szerokosc = document.documentElement.clientWidth;
		var wysokosc = document.documentElement.clientHeight;
		$('#komunikat').css('width', '465px');
		$('#komunikat').css('height', '360px');
		$('#komunikat').css('padding-left', '15px');
		wysokosc = wysokosc/2;
		wysokosc = wysokosc - 180;
		szerokosc = szerokosc/2;
		szerokosc = szerokosc - 232;
		$('.addRecord').remove();
		$('#naglowekTable').text('PRACOWNICY OBECNI W ZAKŁADZIE.');
		
		$.post('dbase.php?readPresent', function(e)
		{
			setTimeout(function()
			{
				wrazliweHide();
				$('.wrazliwe').hover(function()
				{
					$('.wrazliwe').css('color', 'black');
				}, 
				function()
				{
					wrazliweHide();
				});
			}, 5000);
			var dane = e.split('->');
			var i = 0;
			var j = 0;
			var colorRow = "#ebebeb;";
			while(j < (dane.length-1)/4)
			{				
				if(j%2 == 0)
					colorRow = "#ebebeb";
				else
					colorRow = "#c8c8c8";

				$('#presentTable tbody').html($('#presentTable tbody').html()+
					"<tr class='addRecord'>"+
						"<td style='background: "+colorRow+"; border-left: 1px solid gray; border-right: 1px solid gray;'>"+((i/4)+1)+".</td>"+
						"<td class='wrazliwe' style='background: "+colorRow+";' >"+
						dane[2+i]+
						"</td>"+
						"<td style='background: "+colorRow+"; border-left: 1px solid gray;'>"+
						dane[0+i]+
						"</td>"+
						"<td style='background: "+colorRow+"; border-left: 1px solid gray;'>"+
						dane[3+i]+
						"</td>"+
						"<td style='background: "+colorRow+"; border-left: 1px solid gray;'>"+
							"<button id='"+dane[0+i]+"' class='endJob'>Wyjście</button>"+
						"</td>"+
					"</tr>");
					i = i + 4;
					++j;
			}
			$('#naglowekTable').html("PRACOWNICY OBECNI W ZAKŁADZIE - "+(i/4)+" OSÓB");
			if(e.length == 0)
			{
				$('#presentTable tbody').html($('#presentTable tbody').html()+
					"<tr class='addRecord'>"+
						"<td colspan='4'>"+
						"Obecnie w zakładzie nie przebywa żaden pracownik."+
						"</td>"+
					"</tr>");
				$('#komunikat').css('height', '150px');
			}
			
			$('.endJob').click(function()
			{
				var id = this.id;
				var powod = prompt("Powód zmiany: ");
				$.post('dbase.php?endJob', "id="+id+"&powod="+powod, function(e)
				{
					if(e == "Operacja pomyślna.")
						$('#log').html($('#log').html()+czas(true)+"Ręczna zmiana statusu pracownika [WYJŚCIE]["+id+"].\n");
					else						
						$('#log').html($('#log').html()+czas(true)+"Ręczna zmiana statusu pracownika [WYJŚCIE] - niepowodzenie.\n");
					przewinDol();
					$('#przyciemnij').click();
					$('#present').click();
				});
			});
			$('#komunikat').fadeIn(1000).css('top', wysokosc).css('left', szerokosc);
		});
		
		$('#przyciemnij').css('display', 'block');

	});


	$('#absent').click(function()
	{
		var szerokosc = document.documentElement.clientWidth;
		var wysokosc = document.documentElement.clientHeight;
		$('#komunikat').css('width', '465px');
		$('#komunikat').css('height', '360px');
		$('#komunikat').css('padding-left', '15px');
		wysokosc = wysokosc/2;
		wysokosc = wysokosc - 180;
		szerokosc = szerokosc/2;
		szerokosc = szerokosc - 232;
		$('.addRecord').remove();
		$('#naglowekTable').text('PRACOWNICY NIE OBECNI Z OBECNEJ BRYGADY.');
		
		$.post('dbase.php?readAbsent', function(e)
		{			
			setTimeout(function()
			{
				wrazliweHide();
				$('.wrazliwe').hover(function()
				{
					$('.wrazliwe').css('color', 'black');
				}, 
				function()
				{
					wrazliweHide();
				});
			}, 5000);
			var dane = e.split('->');
			var i = 0;
			var j = 0;
			var colorRow = "#ebebeb;";
			while(j < (dane.length-1)/4)
			{				
				if(j%2 == 0)
					colorRow = "#ebebeb";
				else
					colorRow = "#c8c8c8";

				$('#presentTable tbody').html($('#presentTable tbody').html()+
					"<tr class='addRecord'>"+
						"<td style='background: "+colorRow+"; border-left: 1px solid gray; border-right: 1px solid gray;'>"+((i/4)+1)+".</td>"+
						"<td class='wrazliwe' style='background: "+colorRow+";' >"+
						dane[2+i]+
						"</td>"+
						"<td style='background: "+colorRow+"; border-left: 1px solid gray;'>"+
						dane[0+i]+
						"</td>"+
						"<td style='background: "+colorRow+"; border-left: 1px solid gray;'>"+
						dane[3+i]+
						"</td>"+
						"<td style='background: "+colorRow+"; border-left: 1px solid gray;'>"+
							"<button id='"+dane[0+i]+"' class='endJob'>Wyjście</button>"+
						"</td>"+
					"</tr>");
					i = i + 4;
					++j;
			}
			if(e.length == 0)
			{
				$('#presentTable tbody').html($('#presentTable tbody').html()+
					"<tr class='addRecord'>"+
						"<td colspan='4'>"+
						"W zakładzie przebywają wszyscy pracownicy obecnej brygady."+
						"</td>"+
					"</tr>");
				$('#komunikat').css('height', '150px');
			}
			
			$('.endJob').click(function()
			{
				var id = this.id;
				var powod = prompt("Powód zmiany: ");
				$.post('dbase.php?startJob', "id="+id+"&powod="+powod, function(e)
				{					
					if(e == "Operacja pomyślna.")
						$('#log').html($('#log').html()+czas(true)+"Ręczna zmiana statusu pracownika [WEJŚCIE]["+id+"].\n");
					else						
						$('#log').html($('#log').html()+czas(true)+"Ręczna zmiana statusu pracownika [WEJŚCIE]- niepowodzenie.\n");
					przewinDol();
					$('#przyciemnij').click();
					$('#absent').click();
				});
			});
			$('#komunikat').fadeIn(1000).css('top', wysokosc).css('left', szerokosc);
		});
		
		$('#przyciemnij').css('display', 'block');

	});

	$('#uphours').click(function()
	{
		var szerokosc = document.documentElement.clientWidth;
		var wysokosc = document.documentElement.clientHeight;
		$('#komunikat').css('width', '465px');
		$('#komunikat').css('height', '185px');
		$('#komunikat').css('padding-left', '15px');
		$('#komunikat').css('padding-top', '15px');
		wysokosc = wysokosc/2;
		wysokosc = wysokosc - 180;
		szerokosc = szerokosc/2;
		szerokosc = szerokosc - 232;
		$('.addRecord').remove();
		$('#naglowekTable').text('PRACOWNICY OBECNI - NADGODZINY');
		$('#naglowekTable').prop('colspan', '5');
		$('#nbsp').text("Ilość:");
		
		$.post('dbase.php?readUpHours', function(e)
		{
			setTimeout(function()
			{
				wrazliweHide();
				$('.wrazliwe').hover(function()
				{
					$('.wrazliwe').css('color', 'black');
				}, 
				function()
				{
					wrazliweHide();
				});
			}, 5000);
			var dane = e.split('->');
			var i = 0;
			var j = 0;
			var colorRow = "#ebebeb;";
			while(j < (dane.length-1)/5)
			{				
				if(j%2 == 0)
					colorRow = "#ebebeb";
				else
					colorRow = "#c8c8c8";
				var tempDane = dane[4+i].split(':');
				if(tempDane[1] > 30 || tempDane[0] > 0)
					tempDane = "border-left: 2px solid blue;";
				else
					tempDane='';

				$('#presentTable tbody').html($('#presentTable tbody').html()+
					"<tr class='addRecord'>"+
						"<td style='background: "+colorRow+"; border-left: 1px solid gray; border-right: 1px solid gray;'>"+((i/5)+1)+".</td>"+
						"<td class='wrazliwe' style='background: "+colorRow+";"+tempDane+"' >"+
						dane[2+i]+
						"</td>"+
						"<td style='background: "+colorRow+"; border-left: 1px solid gray;'>"+
						dane[0+i]+
						"</td>"+
						"<td style='background: "+colorRow+"; border-left: 1px solid gray;'>"+
						dane[3+i]+
						"</td>"+
						"<td style='background: "+colorRow+"; border-left: 1px solid gray;'>"+
						dane[4+i]+
						"</td>"+
					"</tr>");
					i = i + 5;
					++j;
			}
			if(e.length == 0)
			{
				$('#presentTable tbody').html($('#presentTable tbody').html()+
					"<tr class='addRecord'>"+
						"<td colspan='4'>"+
						"W zakładzie przebywają pracownicy tylko obecnej brygady."+
						"</td>"+
					"</tr>");
				$('#komunikat').css('height', '150px');
			}
			else			
				$('#komunikat').css('height', '300px').css('width', '480px');
			
			$('#komunikat').fadeIn(1000).css('top', wysokosc).css('left', szerokosc);
		});
		
		$('#przyciemnij').css('display', 'block');

	});	

	$('#logi').click(function()
	{
			$.post('dbase.php?secRap', function(e)
			{
				if(okienka)
				{
					okienka = false;
					const okienko = window.open('', 'LOG', 'toolbar=no,location=no,width=800,height=600');
					okienko.document.write(e);
				}
			});
	});
});

function zeraWiodace(i)
{
	return (i<10)? '0'+i:i;
}

function czas(i)
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
		if(i == true)
			return dzis+" "+godzina+" -> "; 
	setTimeout(function()
	{
		czas();
	}, 1000)
}

function przewinDol()
{
	var offsetBottom;
	while(true)
	{
		var ob = $('#log');
		offsetBottom = ob.scrollTop();
		ob.scrollTop(offsetBottom+1);
		if(offsetBottom >= ob.scrollTop())
			break;
	}
	return;
}

function ws()
{
	if(window.WebSocket)
	{
		if(typeof connection == 'undefined')
			connection = new WebSocket(ser);
		connection.onopen = function() 
			{ 
				$('#img').css('background', 'url(\'rfid_green.png\') no-repeat');
				$('#log').html(czas(true)+"Ustanowiono połączenie z czytnikiem kart RFID.\n");
				przewinDol();
				
			};
		connection.onclose = function() 
			{ 
				$('#img').css('background', 'url(\'rfid_red.png\') no-repeat');
				$('#log').html($('#log').html()+czas(true)+"Zakończono połączenie z czytnikiem kart RFID.\n");
				przewinDol();
				setTimeout(function()
				{
					$('#log').html($('#log').html()+czas(true)+"Ponawianie połączenia z czytnikiem kart RFID.\n");
					przewinDol();
					delete connection;
					ws();
				}, 1000*5);
				
			};
		connection.onerror = function() 
			{ 
				$('#img').css('background', 'url(\'rfid_red.png\') no-repeat');
				$('#log').html($('#log').html()+czas(true)+"Błąd połączenia z czytnikiem kart RFID.\n");
				przewinDol();
				setTimeout(function()
				{
					$('#log').html($('#log').html()+czas(true)+"Ponawianie połączenia z czytnikiem kart RFID.\n");
					przewinDol();
					delete connection;
					ws();
				}, 1000*5);
			};
		connection.onmessage = function (e) 
		{
			if(e.data.indexOf('worker->') != -1)
			{
				$('#log').html($('#log').html()+czas(true)+"Odczyt autoryzowanej karty: "+e.data.substr(e.data.indexOf('##')+2)+".\n");
				przewinDol();
				$('#nrpracownika').val(e.data.substr(e.data.indexOf('->')+2, e.data.indexOf('##')-(e.data.indexOf('->')+2)));
				$('#karta').val(e.data.substr(e.data.indexOf('##')+2));
				$.post('dbase.php?readINsec', 'id='+$('#nrpracownika').val(), function(retdata)
				{
					var wartosci = retdata.split('->');
					if($('#karta').val() == wartosci[2])
					{
						//$('#log').html($('#log').html()+czas(true)+retdata+"\n");
						$('#stanowisko').val(wartosci[0]);
						$('#nazwisko').val(wartosci[3]);
						$('#status').val((wartosci[1] == "1")?"PRACUJE": "NIEPRACUJE");
						if(wartosci[5].indexOf("w") != -1)
						{
							$('#wejscie').val(wartosci[5].replace('w', ''));
							$('#wyjscie').val(wartosci[4]);
						}
						else
						{
							$('#wejscie').val(wartosci[4].replace('w', ''));
							$('#wyjscie').val(wartosci[5]);
						}
					}
					else
						alert("Nieautoryzowana próba wejścia.\n"+retdata.substr(retdata.indexOf('$$')+2));
				});
			}
			else if(e.data.indexOf('nowork->') != -1)
			{
				$('#log').html($('#log').html()+czas(true)+"Odczyt autoryzowanej karty: "+e.data.substr(e.data.indexOf('##')+2)+".\n");
				przewinDol();
				$('#log').html($('#log').html()+czas(true)+"Odmowa dostępu - proszę o kontak z działem HR.\n");
				przewinDol();
				$('#nrpracownika').val(e.data.substr(e.data.indexOf('->')+2, e.data.indexOf('##')-(e.data.indexOf('->')+2)));
				$('#karta').val(e.data.substr(e.data.indexOf('##')+2));
				$.post('dbase.php?readINsec', 'id='+$('#nrpracownika').val(), function(retdata)
				{
					var wartosci = retdata.split('->');
					if($('#karta').val() == wartosci[2])
					{
						//$('#log').html($('#log').html()+czas(true)+retdata+"\n");
						$('#stanowisko').val(wartosci[0]);
						$('#status').val((wartosci[1] == "1")?"PRACUJE": "NIEPRACUJE");
					}
					else
						alert("Nieautoryzowana próba wejścia.\n"+retdata.substr(retdata.indexOf('$$')+2));
				});
			}
			else if(e.data.indexOf('id->') != -1)
			{
				$('input').val('');
				$('#log').html($('#log').html()+czas(true)+"Czytanie nieautoryzowanej karty: "+e.data+".\n");
				
			}
			else
			{
				if(e.data.indexOf('Bezczynn') == -1)
				{
					$('input').val('');
					$('#log').html($('#log').html()+czas(true)+"Odebrano komunikat z czytnika: "+e.data+".\n");
				}
			}
			przewinDol();
			
			setTimeout(function() 
				{
					$('input').val('');
				}, 5000);
		}
	}
	else
		alert("Twoja przeglądarka nie obsługuje protokołu WebSocket. Zmień przeglądarkę, aby móc korzystać ze wszystkich funkcji aplikacji.");
}

function wrazliweHide()
{
	var wrazliwe = $('.wrazliwe');
	var i = 0;
	while(i < $('.wrazliwe').length)
	{
		var tlo = $(wrazliwe[i]).css('background-color');
		$(wrazliwe[i]).css('color', tlo);
		++i;
	}
}
