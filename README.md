# RCP
Rejestracja Czasu Pracy


### System elektronicznej rejestracji czasu pracy ###


System rejestracjii czasu opiera sie na czytnikach odczytach z czytników
RFID umieszczonych przy wejściach do zakładu.

Użytkownicy skanują swoje karty RFID i rejestrują czas wejścia bądź wyjścia z zakładu.
Każde zdarzenie czytnika jest rejestrowane w bazie danych oraz wyświetlane na ekranie 
monitora np. w portierni.
Portier otrzymuje informację na temat wchodzacego i wychodzącego pracownika takie jak:

- Imię i nazwisko
- Nr karty
- Aktualna zmiana
- Aktualny dostęp

Dodatkowo portier ma możliwość wyświetlenia listy wszystkich osób będących aktualnie w zakładzie
oraz osób przebywających w zakładzie poza swoją zmianą - nadgodziny i osób nieobecnych - wg. harmonogramu.
Równocześnie posiada możliwość wprowadzenia ręcznych zdarzeń do rejestru - np. w przypadku zgubienia karty 
przez pracownika lub w przypadku nie odbicia się pracownika i stwierdzeniu jesgo nieobecności w zakładzie
- co jest odnotowywane w rejestrze jako zdarzenie manualne.

U góry interfejsu widnieje log wszystkich zdarzeń systemu aktualizowany na bierząco.

Czytniki współpracują z bramkami wejściowymi i po przyznaniu dostępu otwierają bramkę pracownikowi.

Część interfejsu przeznaczona dla działu HR:

- Edycja, usuwanie i dodawanie nowych pracowników do systemu i przypisanie karty RFID
- Wyświetlanie rejestrów zdarzeń z czytników
- Wprowadzanie, edycja i usuwanie harmonogramów pracy - praca zmianowa
- Generowanie raportów godzinowych dla poszczególnych pracowników i całych działów
- Generowanie raportów i logów z różnych przedziałów czsowych
- Każdy raport zawiera informację o godzinie wejścia i wyjścia, zarejestrowanym czasie pracy - wg. harmonogramu
- W raportach wyrównione są informację na temat spóźnień (rególowanych ustawieniami), nadgodzin, wcześniejszych wyjść
- Raporty można generować do przeglądarki, pliku pdf oraz xls.

Czytniki RFID:

Presonalizowana obudowa czytników przystosowana do montażu na elewacji bramki oraz na ścianie.
Czytnik można również pozostawić na każdej płaskiej powierzchni jako samodzielne urządzenie np. na blacie biórka.

Kazdy czytnik komunikuje się z siecią zakładową przy pomocy WIFI.
Czytniki posiadają interfejs komunikacyjny w celu ich konfiguracji oraz diagnostyki,
aby z niego skorzystać potrzebujemy podłaczyć czytnik do komputera poprzez złącze micro usb umieszczone
na boku czytnika oraz zainicjalizować komunikację USART.