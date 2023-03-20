var okienka = true;
var mysqlcardid;
var connection = 'undefined';
var ws_timeout;
	

$('document').ready(function()
{	
	czas();
	ws();
	$('select').select2();
	$('#raportUser').select2({width: '300px'}).change(function(){$(this).addClass('valid');})
	$('#raportGenXLS').click(function(e)
	{
		if(!$('#dataP').hasClass('valid') || !$('#dataP').hasClass('valid'))
		{
			alert('Najpierw uzupełnij wszystkie pola');
			return;
		}
		
		var dane = '&p='+$('#dataP').val()+'&k='+$('#dataK').val()+'&id='+$('#raportUser').val();
		if($('#czasy').prop('checked'))
			dane += '&czasy=1';

		if($('#logi').prop('checked'))
			dane += '&logi=1';
		
		const okienko = window.open('dbase.php?raport&xls=1&where='+$('#whereSend').val()+dane, 'RAPORT', 'toolbar=no,location=no,width=800,height=600');
	});	
	$('#raportGenPDF').click(function(e)
	{
		if(!$('#dataP').hasClass('valid') || !$('#dataP').hasClass('valid'))
		{
			alert('Najpierw uzupełnij wszystkie pola');
			return;
		}
		
		var dane = '&p='+$('#dataP').val()+'&k='+$('#dataK').val()+'&id='+$('#raportUser').val();
		if($('#czasy').prop('checked'))
			dane += '&czasy=1';

		if($('#logi').prop('checked'))
			dane += '&logi=1';
		
		const okienko = window.open('dbase.php?raport&pdf&where='+$('#whereSend').val()+dane, 'RAPORT', 'toolbar=no,location=no,width=800,height=600');
	});	
	$('#logiAll').click(function()
	{
		if(okienka)
		{
			okienka = false;
			$.post('dbase.php?secRap', function(e)
			{
				const okienko = window.open('', 'LOG', 'toolbar=no,location=no,width=800,height=600');
				okienko.document.write(e);
			});
		}
	});
	$('#plany').click(function()
	{
		window.location.replace('plany.php');
	});
	$.post('dbase.php?readHR', function(e)
	{
		var dane = e.split('->');
		var i = 0;
		var daneWstaw;
		daneWstaw = "<option value='nullOption' id='nullOption' selected disabled>Wybierz plan</option>";
		while(i < (dane.length-1))
		{
			daneWstaw += "<option value='"+dane[i]+"' id='"+dane[i]+"'>"+dane[i].replace('_', " ")+"</option>";
			++i;
		}
		$('#plan').html(daneWstaw).change(function()
		{
			$.post('dbase.php?readHR2', "etykieta="+$('#plan').val(), function(e)
			{
				var dane = e.split('->');
				var i = 0;
				var daneWstaw;
				daneWstaw = "<option value='' disabled>Wybierz brygadę</option>";
				while(i < (dane.length-1))
				{
					daneWstaw += "<option value='"+dane[0+i]+"' id='"+dane[0+i]+"'>"+dane[0+i].replace('_', ' ')+"</option>";
					++i;
				}
				$('#brygada').html(daneWstaw);
			});
		});
	});
	$('input, select').on('blur', function()
			{
				if($(this).val() == '')
				{
					$(this).addClass('invalid').removeClass('valid');
				}
				else
				{	
					$(this).removeClass('invalid').addClass('valid');
				}
			});
	$('#add').click(function()
	{	
		var pola = $('#formularz input, #formularz select');
		var text = '';
		var i = 1;
		while(i < pola.length)
		{
			if($(pola[i]).val() == '' || $(pola[i]).val() == ' ' || $(pola[i]).val() == 'nullOption' || $(pola[i]).val() == null)
			{
				$(pola[i]).addClass('invalid').removeClass('valid');
				
				console.log(pola[i].tagName);
				console.log(pola[i].name);
				if(pola[i].tagName == 'SELECT' && text == '')
					text = '\nSprawdź listy rozwijane.';
			}
			else
			{
				$(pola[i]).addClass('valid').removeClass('invalid');
			}
				
			++i;
		}
		
		
		if($('.invalid').length <=1)
		{
			if($('#pesel').val().length != 11)
			{
				alert("Popraw nr PESEL.");
				return;
			}
			$.post('dbase.php?add', $('#formularz').serialize()+'&karta='+$('#karta').val(), function(e){if(e == "istnieje")alert("Pracownik o podanym nr karty istnieje."); else alert("Nowy pracownik dodany pomyślnie.");});
			$('#karta').val("").removeClass('valid').addClass('invalid');
		}
		else
		{
			console.log($('.invalid'));
			alert("Najpierw wypełnij wszystkie pola."+text);
		}
	});
	$('#delWorker').click(function()
	{
		$.post('dbase.php?readEdit', function(e)
		{
			$('#idWorker').html(e).select2({width: '350px'});
			$('#komunikat3').css('height', '150px');
			$('#komunikat3').fadeIn(1000).css('left', szerokosc).css('top', wysokosc);
		});
		
		var szerokosc = document.documentElement.clientWidth;
		var wysokosc = document.documentElement.clientHeight;
		wysokosc = wysokosc/2;
		wysokosc = wysokosc - 90;
		szerokosc = szerokosc/2;
		szerokosc = szerokosc - 225;
		$('#przyciemnij').fadeIn(1000);
	});
	$('#delWS').click(function()
	{
		if(confirm('Czy napewno chcesz usunąć wybranego pracownika?'))
		{
			var id = $('#idWorker').val();
			$.post('dbase.php?delWorker', 'id='+id, '');
		}
		$('#przyciemnij').click();
	});
	$('#cancel').click(function()
	{
		$('#save').css('display', 'none');
		$('#edit').css('display', 'inline');
		$('#cancel').css('display', 'none');
		$('#delWorker').css('display', 'inline');
		$('#add').css('display', 'inline');
		$('#operacja input').not('input[type=radio]').val("").removeClass('valid').addClass('invalid');
		$('#read').prop('disabled', true);
		$('#nrpracownika').val("");
		$('#imie_nazwisko').val("");
		$('#pesel').val("");
		//$('select').trigger('select2:unselecting');
		$('select').select2();
		
	});
	$('#edit').click(function()
	{
		
		$.post('dbase.php?readEdit', function(retdata)
			{
				$('#lista').html(retdata).on('change', function()
				{
					$('#save').css('display', 'inline');
					$('#edit').css('display', 'none');
					$('#delWorker').css('display', 'none');
					$('#cancel').css('display', 'inline');
					$('#add').css('display', 'none');
					$('#przyciemnij').css('display', 'none');
					$('#komunikat').css('display', 'none');	
					$.post('dbase.php?readEdit', "id="+$("#lista").val(), function(returnData)
					{
						var dane = returnData.split("<-|->");
						$.post('dbase.php?readHR', function(e)
						{
							var dane1 = e.split('->');
							var i = 0;
							var daneWstaw;
							daneWstaw = "<option value='nullOption' id='nullOption' disabled>Wybierz plan</option>";
							while(i < (dane1.length-1))
							{
								var selectedDane = '';
								if(dane1[i] == dane['5'])
									selectedDane = 'selected';
								daneWstaw += "<option value='"+dane1[i]+"' id='"+dane1[i]+"' "+selectedDane+">"+dane1[i].replace('_', " ")+"</option>";
				
								++i;
							}
							$('#plan').html(daneWstaw);
						$('#nrpracownika').val(dane['0']);
						$('#imie_nazwisko').val(dane['1']);
						$('#stanowisko').val(dane['2']);
						$('input[name="status"][value="'+dane[3]+'"]').prop('checked', true);			
						$('#karta').val(dane['4']);		
						$('#pesel').val(dane['8']);	
						$('#grupa').val(dane['9']);
						$('#formularz input').removeClass('invalid').addClass('valid');
						$('#read').prop('disabled', false);
							if(dane['7'] == 1)
						$('#sprawnosc').prop('checked', true);
						else
								$('#sprawnosc').prop('checked', false);
						mysqlcardid = dane['4'];
						$.post('dbase.php?readHR2', "etykieta="+dane['5'], function(e)
									{
										var dane1 = e.split('->');
										var i = 0;
										var daneWstaw;
										daneWstaw = "<option value='' disabled>Wybierz brygadę</option>";
										while(i < (dane1.length-1))
										{
											var selectedvar = '';
											if(dane1[0+i] == dane['6'])
												selectedvar = 'selected';
											daneWstaw += "<option "+selectedvar+" value='"+dane1[0+i]+"' id='"+dane1[0+i]+"'>"+dane1[0+i].replace('_', ' ')+"</option>";
											++i;
										}
										$('#brygada').html(daneWstaw);

										$('select').select2();										
									});	
						});		

					});
				});	
				$('#komunikat').fadeIn(1000).css('left', szerokosc).css('top', wysokosc);	
			})
		var szerokosc = document.documentElement.clientWidth;
		var wysokosc = document.documentElement.clientHeight;
		wysokosc = wysokosc/2;
		wysokosc = wysokosc - 110;
		szerokosc = szerokosc/2;
		szerokosc = szerokosc - 225;
		$('#przyciemnij').fadeIn(1000);
	});
	$('#save').click(function()
	{
		if(confirm("Czy napewno nadpisać istniejące dane?"))
		{				
			if($('.valid').length >= 8)
			{
				$.post('dbase.php?saveEdit', $('#formularz').serialize()+'&karta='+$('#karta').val())
				$('#save').css('display', 'none');
				$('#edit').css('display', 'inline');
				$('#cancel').css('display', 'none');
				$('#add').css('display', 'inline');
				$('#delWorker').css('display', 'inline');
				$('#operacja input').not('input[type=radio]').val("").removeClass('valid').addClass('invalid');
				$('#read').prop('disabled', true);
				$('#nrpracownika').val("");
				$('#imie_nazwisko').val("");
				$('#grupa').val("");
				$('#pesel').val("");
				$('select').select2();
			}
			else
				alert("Najpierw wypełnij wszystkie pola."+$('.invalid').length+" + "+$('.valid').length);
		}
	});
	$('#read').click(function()
	{
		if(connection.readyState != 1)
		{
			alert('Brak połączenia z czytnikiem.');
			return;
		}
		var szerokosc = document.documentElement.clientWidth;
		var wysokosc = document.documentElement.clientHeight;
		wysokosc = wysokosc/2;
		wysokosc = wysokosc - 90;
		szerokosc = szerokosc/2;
		szerokosc = szerokosc - 225;

		$('#komunikat1').css('top', wysokosc).css('left', szerokosc);
		$('#komunikat1').fadeIn(1000);
		$('#przyciemnij').fadeIn(800);

		if($('#nrpracownika').val() != "") 
			connection.send("zapisz->"+$('#nrpracownika').val()+"##"+mysqlcardid);
	});
	$('#przyciemnij').click(function()
	{
		if($('#przyciemnij').css('display') == 'block')
		{
			$('#przyciemnij').fadeOut(1000);
			$('.komunikaty').fadeOut(500);
		}
	});
	$('#listy.przycisk').click(function()
	{		
		var szerokosc = document.documentElement.clientWidth;
		var wysokosc = document.documentElement.clientHeight;
		wysokosc = wysokosc/2;
		wysokosc = wysokosc - 125;
		szerokosc = szerokosc/2;
		szerokosc = szerokosc - 225;

		$('#przyciemnij').fadeIn(1000);

		$.post('dbase.php?lista', function(e)
		{
			$('#whereSend').val('one');
			$('#raportUser').html(e).select2({ width: '300px'});			
			$('#komunikat2').fadeIn(1000).css('left', szerokosc).css('top', wysokosc);
		});
	});
	$('#rapGroup').click(function()
	{		
		var szerokosc = document.documentElement.clientWidth;
		var wysokosc = document.documentElement.clientHeight;
		wysokosc = wysokosc/2;
		wysokosc = wysokosc - 125;
		szerokosc = szerokosc/2;
		szerokosc = szerokosc - 225;

		$('#przyciemnij').fadeIn(1000);

		$.post('dbase.php?grupy', function(e)
		{
			$('#whereSend').val('many');
			$('#raportUser').html(e).select2({ width: '300px'});			
			$('#komunikat2').fadeIn(1000).css('left', szerokosc).css('top', wysokosc);
		});
	});
	$('#raportGen').click(function(e)
	{
		
		if($('#raportUser').val() != null && $('#raportUser').val().length >= 1)
			$('#raportUser').addClass('valid');
		
		if((!$('#dataP').hasClass('valid') || !$('#dataP').hasClass('valid') || !$('#raportUser').hasClass('valid')) || (!$('#czasy').prop('checked') && !$('#logi').prop('checked')))
		{
			alert('Najpierw uzupełnij wszystkie pola');
			return;
		}
		
		var dane = 'p='+$('#dataP').val()+'&k='+$('#dataK').val()+'&id='+$('#raportUser').val();
		if($('#czasy').prop('checked'))
			dane += '&czasy=1';

		if($('#logi').prop('checked'))
			dane += '&logi=1';

			//$.post('dbase.php?raport&where='+$('#whereSend').val(), dane, function(e)
			//{
				const okienko = window.open('dbase.php?raport&where='+$('#whereSend').val()+'&'+dane, 'RAPORT', 'toolbar=no,location=no,width=800,height=600');
			//	okienko.document.write(e);
			//});
			$('#przyciemnij').click();
			$('#raportUser').removeClass('valid');
			
	});	

	$.post('dbase.php?grupy', function(e)
	{
		$('#podpowiedzi').html(e);
	});		

});

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
	}, 1000)
}
function ws()
{
	if(window.WebSocket)
	{
		if(connection == 'undefined')
			connection = new WebSocket(ser);		
		connection.onopen = function() 
		{ 
			connection.send("hello");
			$('#img').css('background', 'url(\'rfid_green.png\') no-repeat');
		};
		connection.onclose = function () 
		{
			$('#img').css('background', 'url(\'rfid_red.png \') no-repeat');
			setTimeout(function()
			{
				ws();
			}, 1000*30);
		};
		connection.onerror = function () 
		{				
			$('#img').css('background', 'url(\'rfid_red.png\') no-repeat');
			setTimeout(function()
			{
				ws();
			}, 1000*30);
		};
		connection.onmessage = function (e) 
		{
			if(e.data.indexOf('saveOK') != -1)
			{
				$('#przyciemnij').css('display', 'none');
				$('#komunikat1').css('display', 'none');
				$('#save').css('display', 'none');
				$('#edit').css('display', 'inline');
				$('#cancel').css('display', 'none');
				$('#add').css('display', 'inline');
				$('#operacja input').not('input[type=radio]').val("").removeClass('valid');
				$('#read').prop('disabled', true);
				$('#nrpracownika').val("");
				alert("Zapis pomyślny.");
				
				location.reload();
				location.href = location.href;
			}
			else if(e.data.indexOf('czas->') != -1)
			{
				var tempText = e.data.substr(6);
				$('#czas').text("Pozostały czas: "+tempText);
			}
			else if(e.data.indexOf('saveDONE') != -1)
			{
				alert("Zapis na kartę nie powiódł się. \nUżyj innej karty i spróbuj ponownie. \nJeżeli problem będzie się powtarzał wezwij dział IT.");
				$('#przyciemnij').click();
			}
			else if(e.data.indexOf('id->') != -1)
			{
				if(e.data.indexOf('Odmowa dostępu.') != -1)
					$('#karta').val(e.data.substr(20));
				else
					$('#karta').val(e.data.substr(4));
				$('#karta').removeClass('invalid').addClass('valid');
			}
			else if(e.data.indexOf('worker->') != -1)
			{
				$('#karta').val(e.data.substr(e.data.indexOf('##')+2));
				$('#karta').removeClass('invalid').addClass('valid');
			}
			else if(e.data.indexOf('nowork->') != -1)
			{
				$('#karta').val(e.data.substr(e.data.indexOf('##')+2));
				$('#karta').removeClass('invalid').addClass('valid');
			}
		};
	}
	else
		alert("Twoja przeglądarka nie obsługuje protokołu WebSocket. Zmień przeglądarkę, aby móc korzystać ze wszystkich funkcji aplikacji.");
}
