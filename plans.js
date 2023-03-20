const days = ["Niedziela", "Poniedziałek", "Wtorek", "Środa", "Czwartek", "Piątek", "Sobota"];
$('document').ready(function()
{
	czas();
	showHours();
	
	$('#back').click(function()
	{
		window.location.replace('hr.php');
	});
	
	$('#addPlan').click(function()
	{
		$('#komunikat').css('display', 'none');
		var szerokosc = document.documentElement.clientWidth;
		var wysokosc = document.documentElement.clientHeight;
		var x,y;
		x = 20 + (47 *  $('#brygady').val());
		y = 234 + (28 *  $('#dni').val());
		if(y > 520)
			y = 520;
		if(x > 850)
			x = 850;
		if(y < 182)
			y = 182;
		if(x < 497)
			x = 497;
		$('#komunikat2').css('width', x);
		$('#komunikat2').css('height', y);
		wysokosc = wysokosc/2;
		wysokosc = wysokosc - (y/2);
		szerokosc = szerokosc/2;
		szerokosc = szerokosc - (x/2);
		var margin = (szerokosc/2)-((47 *  $('#brygady').val())/2);
		$('#planPola').css('padding-left', margin-20);
		var i =0;
		var j =0;									
		var tabelka;
		
		$('#planZmiany').css('width', $('#komunikat2').css('width'));
		tabelka = "<tr class='addRecord' ><td class='addRecord' >DZIEŃ</td>";
		while(j < $('#brygady').val())
		{
			tabelka += "<td class='addRecord' style='border-left: 1px solid silver; '>BR. "+(j+1)+"</td>";
			++j;
		}
		tabelka += "</tr>";
		j=0;

		while(i < $('#dni').val())
		{
			var data = new Date();
			var data1;
			var dzien = (i)*1000*60*60*24;
			dzien = data.getTime()+dzien;
			data1 = new Date(dzien);
			
			tabelka += "<tr class='addRecord' ><th style='text-align: left;'>"+zeraWiodace(data1.getDate())+'-'+zeraWiodace(data1.getMonth()+1)+'-'+data1.getFullYear()+' '+days[data1.getDay()]+"</th>";
			while(j < $('#brygady').val())
			{
				tabelka += "<td style='border-left: 1px solid silver'><input type='number' style='width: 30px' id='in"+i+"_"+j+"' name='"+i+"_"+j+"'/></td>";
				++j;
			}
			j=0;
			tabelka += "<tr/>";
			++i;
		}
		$('#planPola').html(tabelka);
		
		$('#komunikat2').css('display', 'block').css('top', wysokosc).css('left', szerokosc);
		$('#przyciemnij').css('display', 'block');
	});


	$('#addPlanSave').click(function()
	{		
		var i = 0;
		var j = 0;
		var valid = true;

		while(i < $('#dni').val())
		{
			while(j < $('#brygady').val())
			{
				if(($('#in'+i+'_'+j).val() == '' || $('#in'+i+'_'+j).val() == ' ' )&& $('#in'+i+'_'+j).val() <= 0)
				{
					$('#in'+i+'_'+j).css('outline', '1px solid red');
					valid = false;;
				}
				else					
					$('#in'+i+'_'+j).css('outline', '1px solid green');
				++j;
			}
			j = 0;
			++i;
		}
		if(!valid)
		{
			alert('Najpierw wypełnij wszystkie pola');
			return;
		}
		var nazwaPlanu = $('#nazwa').val();
		nazwaPlanu = nazwaPlanu.replace(' ', '_');
		$.post('dbase.php?addPlan', $('#komunikat2 form').serialize()+"&dni="+$('#dni').val()+"&brygady="+$('#brygady').val()+"&nazwa="+nazwaPlanu, function(e)
		{
			alert("Plan \""+$('#nazwa').val()+"\" został pomyślnie dodany do bazy.");
			window.location.replace('plany.php');
		});
	});
	
	$('#editPlanSave').click(function()
	{		
		var i = 0;
		var j = 0;
		var valid = true;
		while(i < $('#planPolaUpdate tr').length-2)
		{
			while(j < $('.kolBryg').length)
			{
				if(($('#'+i+'.'+j).val() == '' || $('#'+i+'.'+j).val() == ' ' )&& $('#'+i+'.'+j).val() <= 0)
				{
					$('#'+i+'.'+j).css('outline', '1px solid red');
					valid = false;;
				}
				else					
					$('#'+i+'.'+j).css('outline', '1px solid green');
				++j;
			}
			j = 0;
			++i;
		}
		if(!valid)
		{
			alert('Najpierw wypełnij wszystkie pola');
			return;
		}
		
		var z = 0;
		var w = $('#planPolaUpdate td').children('input');
		console.log(w);
		console.log($('#planPolaUpdate td').children('input').length);
		while(z < $('#planPolaUpdate td').children('input').length)
		{	
			w.slice(z, z+1).prop('name', 'input_'+z);
			z++;			
			console.log(w);
			console.log(z);
		}
		
		
		$.post('dbase.php?updatePlan', $('#komunikat4 form').serialize()+"&dni="+($('#planPolaUpdate tr').length-2)+"&brygady="+$('.kolBryg').length+"&nazwa="+$('#wyborPlanu').val()+"&pocz="+$('#planZmianyUpdate span').text(), function(e)
		{
			if(e == 'OK')
				alert("Plan \""+$('#wyborPlanu').val()+"\" został pomyślnie zaktualizowany.");
			else
				alert("Błąd plan nie został zaktualizowany.");
			window.location.replace('plany.php');
		});
	});

	$('#add').click(function()
	{		
		var szerokosc = document.documentElement.clientWidth;
		var wysokosc = document.documentElement.clientHeight;
		$('#komunikat').css('width', '480px');
		$('#komunikat').css('height', '180px');
		$('#komunikat').css('padding-left', '15px');
		wysokosc = wysokosc/2;
		wysokosc = wysokosc - 90;
		szerokosc = szerokosc/2;
		szerokosc = szerokosc - 232;
		
		
		$('#komunikat').css('display', 'block').css('top', wysokosc).css('left', szerokosc);
		$('#przyciemnij').css('display', 'block');

	});

	$('#del').click(function()
	{	
		if($('#wyborPlanu').val() == '')
		{
			alert("Porszę wybrać plan z listy powyżej.");
			return;
		}
		if(confirm("Czy napewno usunąć wskazany plan \""+$('#wyborPlanu').val()+"\"? Cofnięcie zmiany nie będzie możliwe."))
		{
			$.post('dbase.php?delPlan', 'nazwa='+$('#wyborPlanu').val(), function()
			{
				alert("Plan usunięto z powodzeniem.");
				window.location.replace('plany.php');
			});
		}
	});


	$('#delHours').click(function()
	{	
		if($('#id').val() == '')
		{
			alert("Poroszę wpisać identyfikator zestawu godzin.");
			return;
		}
		if(confirm("Czy napewno usunąć wskazany zestaw \""+$('#id').val()+"\"? Cofnięcie zmiany nie będzie możliwe."))
		{
			$.post('dbase.php?delHours', 'id='+$('#id').val(), function()
			{
				alert("Zestaw gdzin usunięto z powodzeniem.");
				window.location.replace('plany.php');
			});
		}
	});
	
	$('#hoursAdd').click(function()
	{	
		if($('#in').val() < 0 || $('#in').val() > 23.59 || $('#out').val() < 0 || $('#out').val() > 23.59)
		{
			alert("Wprowadzono nieprawidłowe godziny.");
			return;
		}
		$.post('dbase.php?hoursAdd', 'in='+$('#in').val()+'&out='+$('#out').val()+'&opis='+$('#opis').val()+'&symbol='+$('#symbol').val(), function(e)
			{
				alert("Pomyślnie dodano nowe godziny pracy do bazy.\n Możesz je wykorzystać podając w planach nr: "+e);
				$('#przyciemnij').click();
				showHours();
			});
	});
	
	$('#hours').click(function()
	{		
		var szerokosc = document.documentElement.clientWidth;
		var wysokosc = document.documentElement.clientHeight;
		wysokosc = wysokosc/2;
		wysokosc = wysokosc - 110;
		szerokosc = szerokosc/2;
		szerokosc = szerokosc - 232;
		
		
		$('#komunikat3').fadeIn(1000).css('top', wysokosc).css('left', szerokosc);
		$('#przyciemnij').css('display', 'block');

	});

	$('#przyciemnij').click(function()
	{
		if($('#przyciemnij').css('display') == 'block')
		{			
			$('.addRecord').remove();
			$('#przyciemnij').css('display', 'none');
			$('.komunikaty').css('display', 'none');
		}
	});

	$('#modify').click(function()
	{
		if($('#wyborPlanu').val() == '')
		{
			alert("Proszę wybrać plan z listy powyżej.");
			return;
		}
		$.post('dbase.php?readPlan', 'nazwa='+$('#wyborPlanu').val(), function(e)
		{
			$('#planPolaUpdate').html(e);
			var poczatek = $('.poczateczek td').text();
			$('#planZmianyUpdate h5').html($('#planZmianyUpdate h5').text()+"<span class='addRecord'>"+poczatek+"</span>");
			$('.poczateczek td').remove();
		
			var szerokosc = document.documentElement.clientWidth;
			var wysokosc = document.documentElement.clientHeight;
			var x,y;
			x = 20 + (47 *  ($('#planZmianyUpdate tr').length - 3));
			y = 234 + (28 * ($('.kolBryg').length +1));
			if(y > 520)
				y = 520;
			if(x > 850)
				x = 850;
			if(y < 182)
				y = 182;
			if(x < 497)
				x = 497;
			$('#komunikat4').css('width', x);
			$('#komunikat4').css('height', y+120);
			wysokosc = wysokosc/2;
			wysokosc = wysokosc - (y/2);
			szerokosc = szerokosc/2;
			szerokosc = szerokosc - (x/2);
			var margin = (szerokosc/2)- ((50 * ($('.kolBryg').length+1))/2);
			var margin = (szerokosc/2)- ((50 * ($('.kolBryg').length+1))/2);
			$('#planPolaUpdate').css('padding-left', margin);		
			$('#planZmianyUpdate').css('padding-left', margin/2);
			
			$('#planPolaUpdate span').click(function()
			{
				$(this).parent('td').parent('tr').remove();
				$('#planPolaUpdate tr').children('th, td').css('background-color', 'white');
				var i = 0;
				//console.log($('#planPolaUpdate tr').slice(1));
				while(i < $('#planPolaUpdate tr').slice(1).children('th').length)
				{
					var data = new Date(poczatek);
					data.setDate(data.getDate()+i);
					$('#planPolaUpdate tr').slice(1).children('th').slice(i, i+1).text(zeraWiodace(data.getDate())+'-'+zeraWiodace(data.getMonth()+1)+'-'+data.getFullYear()+' '+days[data.getDay()]);
					++i;
				}
			});	
			
			$('#komunikat4').fadeIn(1000).css('top', wysokosc).css('left', szerokosc);
			
		});
		$('#przyciemnij').css('display', 'block');
		
	});

	$('#addPlanDay').click(function()
	{
		var brygad;
		var daySelect = $('#numDays').val();
		var max = $('#planPolaUpdate').children('tr').length-2;
		var day = $('#planPolaUpdate tr').slice(daySelect,daySelect+1);
		
		if($('#numDays').val() > max || $('#numDays').val() < 1)
		{
			alert("Nieprawidłowa wartość - numer dnia planu.");
			return;
		}
		$('#planPolaUpdate tr').children('th, td').css('background-color', 'white');
		
		if($('#whereAdd').val() == 0)	//przed
		{
			var days = $(day[0]).clone();
			var i = 1;
			days.insertBefore(day[0]).find('input').val('');
			brygad = days.insertBefore(day[0]).find('input').length;
			
			
			while((brygad+2)*i < ($('#planPolaUpdate').children('tr').length)*(brygad+2))
			{
				$('#planPolaUpdate tr').children().slice(i*(brygad+2),(i*(brygad+2))+1).text(i++);
			}
			i = 0;
			while(i < day.length)
			{
				
				var inputs = day.find('input');
				var inputs = inputs[i];
				++i;
			}
		}
		if($('#whereAdd').val() == 1)	//po
		{
			var days = $(day[0]).clone();
			var i = 1;
			days.insertAfter(day[0]).find('input').val('');
			brygad = days.insertAfter(day[0]).find('input').length;
			
			while((brygad+2)*i < ($('#planPolaUpdate').children('tr').length)*(brygad+2))
			{
				$('#planPolaUpdate tr').children().slice(i*(brygad+2),(i*(brygad+2))+1).text(i++);
			}
		}
		
		$('#planPolaUpdate span').off('click');
		
		$('#planPolaUpdate span').click(function()
			{
				$(this).parent('td').parent('tr').remove();
				$('#planPolaUpdate tr').children('th, td').css('background-color', 'white');
				var i = 0;
				console.log($('#planPolaUpdate tr').slice(1));
				while(i < $('#planPolaUpdate tr').slice(1).children('th').length)
				{
					$('#planPolaUpdate tr').slice(1).children('th').slice(i, i+1).text(++i);
				}
			});
	});

	function showHours()
	{
		$.post('dbase.php?godzinyShow', function(e)
		{
			$('#legenda').html(e);
		});
	}

	function zeraWiodace(i)
	{
		return (i<10)? '0'+i:i;
	}

	function czas(i)
	{
			var newdate = new Date();
			var dzis = zeraWiodace(newdate.getDate());
			dzis += "."+zeraWiodace(newdate.getMonth()+1);
			dzis += "."+newdate.getFullYear();

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
}
);