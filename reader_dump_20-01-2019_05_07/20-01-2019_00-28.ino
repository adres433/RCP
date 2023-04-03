/*
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *    SYSTEM REJESTRACJI CZASU PRACY I KONTROLI DOSTĘPU    *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * 
 * 
 * |-------------------------------|
 * |         ESP32 DEVKIT V1       |
 * |-------------------------------|
 * |EN -          |D23 - MOSI-RFID |
 * |VP -          |D22 - BUZZER    |
 * |VN -          |TX - UART0-USB  |
 * |D34-          |RX - UART0-USB  |
 * |D35-          |D21 - RESET-RFID|
 * |D32-          |D19 - MISO- RFID|
 * |D33-          |D18 - SCK - RFID|
 * |D25-          |D05 - SS - RFID |
 * |D26-          |TX2 - RELAY IN2 |
 * |D27-          |RX2 - RELAY IN4 |
 * |D14-          |D04 - RELAY IN3 |
 * |D12-          |D02 - LED       |
 * |D13-          |D15-  RELAY IN1 |
 * |--------------|----------------|
 */
#include <HTTPClient.h>
#include <MFRC522.h>
#include <WebSocketsServer.h>
#include <SPI.h>
#include <WiFi.h>
#include <EEPROM.h>


/* * * * * * * * * * * * *
 *  DEKLARACJE FUNKCJI   *
 * * * * * * * * * * * * * 
 */
bool send_data(String cardId);    //wysylanie danych do przegladarki
String read_tag();                //odczytywanie identyfikatora RFID
void LED(bool stan);              //obsluga LED w zaleznosci od wyniku operacji wysylania danych
void serialEvent();               //obsluga zdarzenia na UART
void cleanScreen();               //obsluga zdarzenia na UART
bool conn_ws(uint8_t num, WStype_t type, uint8_t * payload, size_t length);   //obsługa zdarzeń WebSocket
bool saveCard(String textID, String nrCardSave, int num);                     //zapisanie ID pracownika na karcie
String configRS(int what, String value);         //zapis i odczyt EEPROM
bool openGate(bool how, bool disabled);                        //otwarcie bramki w odpowiednim kierunku - uwzgledniona bramka dla niepelnosparnych
/* * * * * * * * * * * * *
 *    STAŁE I ZMIENNE    *
 * * * * * * * * * * * * * 
 */
  /*
   * ADRESY PAMIĘCI - EEPROM
   */
   int eepromIP[] = {0, 100};        //IP -> począwszy od adr. 0
   int eepromDNS[] = {101, 200};       //DNS
   int eepromGAT[] =  {201, 300};      //BRAMA
   int eepromMAS[] =  {301, 400};      //MASKA
   int eepromSBD[] = {401, 500};       //SERVER BD
   int eepromIDR[] =  {501, 600};      //ID CZYTNIKA
   int eepromDHC[] =  {601, 700};      //DHCP
   int eepromESS[] =  {701, 801};     //ESSID
   int eepromPWD[] =  {802, 902};      //PASSWORD
   int eepromPWS[] =  {903, 1003};     //PORT WEBSOCKET
   int eepromHR[] =  {1004, 1100};     //ZAPIS SKANU DO BD

//stałe dla statycznego IP
IPAddress ip;
IPAddress dns;
IPAddress gateway;
IPAddress mask;
String idReader;                        //unikalny nr czytnika
String oldId;                        //id poprednio skanowanej karty - dla spr. waznosci odbicia
bool DHCP;                              //czy korzystamy z DHCP czy ustawiamy statyczny IP
char ssid[100];                         //nazwa sieci punktu dostepowego WIFI max 100 znakow
char password[100];                     //haslo punktu dostepowego WIFI max 100 znakow
int wsPort = 2;                        //port na ktorym ma dzialac serwer webSocket
byte block = 2;                        //blok pamięci karty w którym zapisujemy dane
byte blockkey = 3;                     //blok pamięci karty w którym zapisujemy dane
char serverDB[16];                      //adres serwera www - dla polaczenie z BD
String tag = "";                        //zmienna pomocnicza przy odczycie karty RFID
bool hrReader;                        //czy czytnik ma zapisywać dane do rejestru

bool saveMode = false;                       //zmienna ustawiająca czytnik w ryb zapisu karty
unsigned long tempTime[4] = {0L};            //zmienna pomocnicza do obsługi glównej pętli
byte keyDef [17][MFRC522::MF_KEY_SIZE] =     //tablica klucza dostępu karty - fabryczne klucz różnych producentów kart
    {      
      {0xff, 0xff, 0xff, 0xff, 0xff, 0xff},
      {0xa0, 0xb0, 0xc0, 0xd0, 0xe0, 0xf0},
      {0xa1, 0xb1, 0xc1, 0xd1, 0xe1, 0xf1},
      {0xa0, 0xa1, 0xa2, 0xa3, 0xa4, 0xa5},
      {0xb0, 0xb1, 0xb2, 0xb3, 0xb4, 0xb5},
      {0x4d, 0x3a, 0x99, 0xc3, 0x51, 0xdd},
      {0x1a, 0x98, 0x2c, 0x7e, 0x45, 0x9a},
      {0x00, 0x00, 0x00, 0x00, 0x00, 0x00},
      {0xA1, 0xB2, 0xC3, 0xD4, 0xE5, 0xE5}, //klucz PLASTWIL 8 - klucz dla kart zapisanych w firmie
      {0xaa, 0xbb, 0xcc, 0xdd, 0xee, 0xff},
      {0xd3, 0xf7, 0xd3, 0xf7, 0xd3, 0xf7},
      {0xaa, 0xbb, 0xcc, 0xdd, 0xee, 0xff},
      {0x71, 0x4c, 0x5c, 0x88, 0x6e, 0x97},
      {0x58, 0x7e, 0xe5, 0xf9, 0x35, 0x0f},
      {0xa0, 0x47, 0x8c, 0xc3, 0x90, 0x91},
      {0x53, 0x3c, 0xb6, 0xc7, 0x23, 0xf6},
      {0x8f, 0xd0, 0xa4, 0xf2, 0x56, 0xe9}
    };           
MFRC522::MIFARE_Key keyA = {0xFF, 0xFF, 0xFF, 0xFF, 0xFF, 0xFF};      //zmienna klucza dostępu karty 

MFRC522 rfidd(SS, 21);                                                //obiekt obsługi czytnika RFID Pin CS/SS oraz RESET
//WiFiClient client;                                                    //obiekt połączenia WIFI
WebSocketsServer socket;                                              //w bibliotece nalezy dodac pusty konstruktor
HTTPClient client;
/* * * * * * * * * * * * *
 *  DEFINICJE FUNKCJI    *
 * * * * * * * * * * * * * 
 */

void setup() 
{
  //WiFi.hostname();
  pinMode(15, OUTPUT);            //WYJSCIE PRZEKAZNIKA BRAMKI 1
  pinMode(22, OUTPUT);            //WYJSCIE BUZZERA
  pinMode(TX_2, OUTPUT);            //WYJSCIE PRZEKAZNIKA BRAMKI 1
  pinMode(4, OUTPUT);            //WYJSCIE PRZEKAZNIKA BRAMKI 2
  pinMode(RX_2, OUTPUT);            //WYJSCIE PRZEKAZNIKA BRAMKI 2
  digitalWrite(22, LOW);           //BUZZER WYLACZONY
  digitalWrite(15, HIGH);         //BRAMKI ZABLOKOWANE
  digitalWrite(4, HIGH);
  digitalWrite(TX_2, HIGH);
  digitalWrite(RX_2, HIGH);
  Serial.begin(115200);             //inicjalizacja portu szeregowego dla diagnostyki i konfiguracji
  Serial.setTimeout(10);          //czas oczekiwania na odpowiedz UART
  delay(200);
  cleanScreen();
  Serial.print("Wczytywanie konfiguracji...\n\r");
   DHCP = configRS(6, "").toInt();
   hrReader = configRS(10, "").toInt();
   
    Serial.print("Wczytano ustawienia połączenia sieciowego.\n\r");
    if(!DHCP)
    {
      if(!ip.fromString(configRS(0, "")))
        ip = {192,168,43,8};
      else
        Serial.print("Ustawiono IP.\n\r");
      if(!dns.fromString(configRS(1, "")))
        dns = {192,168,0,1};
      else
        Serial.print("Ustawiono DNS.\n\r");
      if(!gateway.fromString(configRS(2, "")))
        gateway = {192,168,0,1};
      else
        Serial.print("Ustawiono bramę domyślną.\n\r");
      if(!mask.fromString(configRS(3, "")))
        mask = {255,255,255,0};
      else
        Serial.print("Ustawiono maskę podsieci.\n\r");
    }
  configRS(4, "").toCharArray(serverDB, 16);
    Serial.print("Ustawiono adres bazy danych.\n\r");
  if(idReader = configRS(5, ""))
    Serial.print("Wczytano ID czytnika.\n\r");
  configRS(7, "").toCharArray(ssid, 100);
    Serial.print("Wczytano nazwę sieci WIFI.\n\r");
  configRS(8, "").toCharArray(password, 100);
    Serial.print("Wczytano hasło sieci WIFI.\n\r");
    wsPort = configRS(9, "").toInt();
    socket = WebSocketsServer(wsPort);   //obiekt połączenia WebSocket - wczytanie z EEPROM
    Serial.print("Wczytano ustawienia serwera WebSocket.\n\r");
    delay(1000*2);
    
  cleanScreen();
  serialEvent();                  //wywołanie funkcji konfiguracji czytnika przez UART
  cleanScreen();
  Serial.print("\n\r\n\rUstanowiono klienta HTTP\n\r\n\r");
  SPI.begin();                    //inicjalizacja komunikacji SPI
  rfidd.PCD_Init();               //inicjalizacja RFID
  Serial.print("Start SPI\n\r");
  Serial.print("Start RFID\n\r");
  pinMode(LED_PIN, OUTPUT);       //inicjalizacja pinu LED jako wyjscie
  digitalWrite(LED_PIN, HIGH);     //ustawienie pinu LED w stan wysoki - swieci - czytnik zajety
  if(!DHCP)                       //jeżeli ustawiono statyczne IP
  {
    Serial.println("Ustawienie statycznego IP.");
    WiFi.config(ip, dns, gateway, mask);            //zapis statycznej konfiracji sieci
  }
  WiFi.begin(ssid, password);                       //inicjalizacja wifi
  Serial.print("Łączenie z WIFI: ");
  Serial.print(ssid);
  Serial.print("\n\r");
  tempTime[0] = millis();                           //"zerowanie" licznika czasu obsługi pętli dla zadań wykonywanych tylko co określony czas
  while (WiFi.status() != WL_CONNECTED)             //próby nawiązania łączności WIFI
  {
        LED(0);                                     //sygnalizacja błędu na czytniku
        Serial.print(".");
        if(millis() - tempTime[0] >= 10000L)        //czekamy 10s na nawiązanie połączenia - jeżeli nie połączono restart ESP
          ESP.restart();
  }
  tempTime[0] = 0;
  cleanScreen();
  Serial.println("WiFi polączone,\n\r");
  Serial.println("IP adres: ");
  Serial.print(WiFi.localIP());
  Serial.println("\n\r");
  Serial.println("MAC adres: ");
  Serial.print(WiFi.macAddress());
  Serial.println("\n\r\n\r");
  delay(1000);
  
  Serial.println("Połączenie webSocket......\n\r");  
  socket.begin();                                                 //inicjalizacja połączenia WebSocket

  client.begin(String("http://")+serverDB+"/rfid/dbase.php");
  Serial.print("Nawiązywanie połączenia z bazą danych.\n\r\n\r");
  
  if(client.GET() > 0)   //polaczenie z serwerem BD   //sprawdzenie łączności z bazą danych
  {
    Serial.print("Połączenie z serwerem BD - pomyślne.");
    Serial.println();
  }
  else
  {
    Serial.println("\n\rPołączenie z serwerem BD nie było możliwe. Kolejna próba za 1m.");
    tempTime[0] = millis();
    while(true)
    {
      LED(0);
      Serial.print(".");
      if(millis() - tempTime[0] <= 1000*60L)        //jeżeli nie połączono odkładamy próby na 1m
        continue;
    
      if(client.GET() > 0)            //polaczenie z serwerem BD
      {
        Serial.print("Połączenie z serwerem BD - pomyślne.");
        Serial.println();
        break;
      }
      else
      {
        Serial.println("\n\rPołączenie z serwerem BD nie było możliwe. Kolejna próba za 1m.");
        tempTime[0] = millis();
      }
    }
    client.end();              //zakończenie połączenia z BD po sprawdzeniu komunikacji
  }

  socket.onEvent(conn_ws);      //przypisanie funkcji dla zdarzeń WebSocket
  tempTime[2] = millis();       //"zerowanie" kolejnego timera
  digitalWrite(LED_PIN, LOW);   //wylaczenie sygnalizacji zajetosci czynnika
}
void loop() 
{  
  if(WiFi.status() != WL_CONNECTED)     //każdorazowe sprawdzenie połączenia WIFI
  {
    LED(0);
    Serial.print("Ponawianie połączenia WiFi");
    tempTime[0] = millis();
    while (WiFi.status() != WL_CONNECTED) 
    {
          LED(0);
          Serial.print(".");
          if(millis() - tempTime[0] >= 30000L)        //czekamy 30s na nawiązanie połączenia - jeżeli nie połączono restart ESP
            ESP.restart();
    }
    tempTime[0] = 0;
  }
  socket.loop();                                          //cykliczna obsługa WebSocket  
    
  if(millis() - tempTime[0] >= 800L || tempTime[0] == 0)  //odczyt czytnika RFID tylko co określony czas
  {
    tempTime[0] = millis();
    tag = read_tag();
    rfidd.PICC_HaltA();           //zatrzymanie czytnika
    rfidd.PCD_StopCrypto1();      //zatrzymanie funkcji krypto1 czytnika
    if(tag == "DONE")             //jeżeli nic nie odczytaliśmy próbujemy ponownie po 100ms
    { 
      delay(50);
      return;
    }
    else                          //jeżeli odczytaliśmy to sprawdzamy czy poprawnie i czy odpowiednią kartę
    {
      tempTime[2] = millis();
      LED(send_data(tag));        //obsluga LED zaleznie od wyniku wyslania danych
      digitalWrite(LED_PIN, LOW);
    }
  }
  else
    delay(10);
}

void LED(bool stan)
{
  if(stan)  // jezeli operacja sie powiodla to odczekaj 1s i przelacz LED
  {    
    digitalWrite(22, HIGH);       //wlaczenie buzzera
    delay(100);
    digitalRead(LED_PIN)? digitalWrite(LED_PIN, LOW) : digitalWrite(LED_PIN, HIGH);   //zmiana aktualnego stanu LED
    delay(100);
    digitalWrite(22, LOW);
  }
  else      //jesli operacja sie nie powiodla mrugaj LED
  {
    for(int i = 0; i < 5;++i)
    {
      delay(100);
      digitalWrite(22, HIGH);       //wlaczenie buzzera
      digitalWrite(LED_PIN, HIGH);
      delay(100);
      digitalWrite(22, LOW);       //wylaczenie buzzera
      digitalWrite(LED_PIN, LOW);
    }
  }
}

String read_tag()
{  
  String tempStringDB;
  String id;
  Serial.print("Sprawdzanie obecnosci karty: ");
  if(rfidd.PICC_IsNewCardPresent() && rfidd.PICC_ReadCardSerial())         //jezeli wykryto karte
  {    
    digitalWrite(LED_PIN, HIGH);    //zapal LED
    cleanScreen();
    Serial.print("Karta wykryta. \n\r\n\r");
    if(true)       //pobieranie nr seryjnego karty
    {
      if (rfidd.PICC_GetType(rfidd.uid.sak) != MFRC522::PICC_TYPE_MIFARE_1K)      //Jeżeli użyto karty innej niż MIFARE Classic 1K
      {
        Serial.println(F("Nieprawidłowy typ karty. Możliwe tylko karty MFC 1K."));
        LED(0);
        return "DONE";
      }
      for(int i = 0; i < rfidd.uid.size; ++i)
      {
        id += (String) rfidd.uid.uidByte[i];
        i < rfidd.uid.size-1 ? id+=":" : id += "";
      }

      Serial.print("Nr seryjny -> ");
      Serial.print(id);
      if(id == oldId && millis() - tempTime[3] <= 1000*30)
      {
        Serial.print("\n\r");
        Serial.print(String("Skanowanie powtórzone - kolejne możliwe za ")+(30-((millis() - tempTime[3])/1000))+String("s.\n\r"));
        socket.broadcastTXT(String("Skanowanie powtórzone - ")+id);
        socket.broadcastTXT(String("Kolejne możliwe za ")+(30-((millis() - tempTime[3])/1000))+String("s."));
        LED(0);
        return "DONE";
      }
      else
      {
        oldId = id;
        tempTime[3] = millis();
      }
      Serial.print("\n\r");
      Serial.print("Pobieranie nr pracownika.\n\r");      
      MFRC522::StatusCode statusRfid;
      String tempA;
      for (byte i = 0; i < MFRC522::MF_KEY_SIZE; i++) 
          {
            keyA.keyByte[i] = keyDef[8][i];
          }
      statusRfid = rfidd.PCD_Authenticate(MFRC522::PICC_CMD_MF_AUTH_KEY_A, block, &keyA, &(rfidd.uid));   //próba autoryzacji karty
      if(statusRfid == MFRC522::STATUS_OK)
      {       
        byte buferRead[18];
        byte sizeBuffer = 18;
        statusRfid = rfidd.MIFARE_Read(block, buferRead, &sizeBuffer);
        if(MFRC522::STATUS_OK == statusRfid)
        {
          Serial.print("Nr pracownika: ");
          for (uint8_t i = 0; i < 16; i++)
          {
            if (buferRead[i] != 32 && buferRead[i] != 0xFF)
            {
              tempA += char(buferRead[i]);
            }
          }
          Serial.print(tempA);
          Serial.println();
        }
        else
        {
          Serial.print("Błąd odczytu danych z karty.\n\r");
          Serial.print(rfidd.GetStatusCodeName(statusRfid));
          Serial.println();
          LED(0);
          return "DONE";
        }
      }  
      else
      {
        Serial.print("Autoryzacja się nie powiodła.\n\r");
        return "id->"+id+"blokada";
      }

      /*
       * POTWIERDZENIE DOSTĘPU W BD
       */

      client.begin(String("http://")+serverDB+"/rfid/dbase.php");
      if(client.GET() <= 0)
      {
        Serial.println("Połączenie z BD zostało utracone. Ponawianie połączenia.");
        tempTime[0] = millis();
        while(true)
        {
          LED(0);
          Serial.print(".");
          if(millis() - tempTime[0] <= 1000*60)        //czekamy 1m na nawiązanie połączenia 
            continue;
          if(client.GET() > 0)   //polaczenie z serwerem BD
          {
            Serial.println();
            Serial.print("Połączenie z serwerem BD - pomyślne.");
            Serial.println();
            break;
          }
          else
          {            
            Serial.println("Połączenie z serwerem BD nie było możliwe. Kolejna próba za 1m.");
            tempTime[0] = millis();
          }
        }
      }
      client.end();
      Serial.println();
      Serial.print("Wysyłanie danych do bazy MYSQL w celu sprawdzenia dostępu.\r\n");
      String tempDB = "id="+id+"&nr="+tempA;
      Serial.println();
      Serial.print(tempDB);
      client.begin(String("http://")+serverDB+"/rfid/dbase.php?check");
      client.addHeader("Content-Type", "application/x-www-form-urlencoded");
      client.addHeader("Host", serverDB);
      client.addHeader("User-Agent", String("ESP32 / RFID-JobControl v1 by adres433 / ")+idReader);
      int errCode;      
      String odp = "";
      
      if((errCode = client.POST(tempDB)) > 0)
      {
        //Serial.print(errCode);
        if(errCode == HTTP_CODE_OK)
        {
          Serial.print("\r\nPrzesłano pomyślnie dane do serwera MYSQL - sprawdzanie dostępu.\n\r");
          odp = client.getString();
        }
        else
          Serial.print(String("Wystąpił błąd przy komunikacji z BD. ")+errCode+"\n\r");
      }
      client.end();
      
      Serial.print(String("Odpowiedz z serwera: ")+odp+"\n\r");
      //client.flush();
      if(odp.indexOf("TRUE") != -1)     //czy mamy dostep 
      {
        Serial.print("Przyznano dostęp.\r\n");
        if(odp.indexOf("->1") != -1 )
        {
          if(odp.indexOf("->disabled") == -1)   //czy otworzyc bramke uchylna czy kolowrot
          {
            openGate(false, false); 
            socket.broadcastTXT("Kołowrót - WYJŚCIE");
          }
          else
          {
            openGate(false, true); 
            socket.broadcastTXT("Bramka uchylna - WYJŚCIE");
          }
        }
        else
        {
          if(odp.indexOf("->disabled") == -1)
          {
            openGate(true, false);       //jeżeli mamy dostęp to otwieramy bramkę w odp. kierunku
            socket.broadcastTXT("Kołowrót - WEJŚCIE");
          }
          else
          {
            openGate(true, true);
            socket.broadcastTXT("Bramka uchylna - WEJŚCIE");
          }
        }
        return ("worker->"+tempA+"##"+id);
      }
      else
     {
        Serial.print("Odmowa dostępu: id->"+id);
        Serial.print("\n\r");
        return ("nowork->"+tempA+"##"+id);
     }
    }
    else
    {
      Serial.print("Błąd odczytu id karty.\n\r\n\r");
      LED(0);
      return "DONE";
    }
  }
  else
  {
    Serial.print("Karta nie wykryta.\n\r\n\r");
    if(millis()-tempTime[2] > (2*60*1000))
      {
        rfidd.PCD_Reset();
        delay(1000);
        rfidd.PCD_Init();
        tempTime[2] = millis();
        Serial.print("Restart czytnika - bezczynność.\n\r");
        socket.broadcastTXT("Bezczynność.");
      }
    return "DONE";
  }
}

bool conn_ws(uint8_t num, WStype_t type, uint8_t * payload, size_t length)
{
  if(type == WStype_CONNECTED)    //obsluga zdarzenia polaczenie - wyswietlenie IP klienta i zapisanie jego id
  {
    Serial.print("Połączone WS: ");
    Serial.print(socket.remoteIP(num));
    Serial.print("\n\rNUM: ");
    Serial.print(num);
    Serial.print("\n\r");
    return true;  
  }
  if(type == WStype_TEXT)    //obsluga zdarzenia: odebano tekst
  {
    Serial.print("Odebrano dane WS\n\r\n\r");
    String a="";
    for(int i=0; i < length; ++i)       // złożenie ciągu znaków z poszczególnych bitów przesłanych od klienta
    {
      a += char(payload[i]);
    }
    if(saveMode == false && a.indexOf("zapisz->")  != -1)    //rozpoznanie przesłanego polecenia - zapisz
    {
      Serial.println("Ustanowiono tryb zapisu kart.\n\r");
      saveMode = 1;
      if(saveCard(a.substring(a.indexOf("->")+2, a.indexOf("##")), a.substring(a.indexOf("##")+2), (int)num))//wydobycie danych do zapisania 
      {
        Serial.println("Zapis pomyslny.");
        socket.sendTXT(num, "saveOK");
        saveMode = 0;
        digitalWrite(LED_PIN, LOW);
        return true;
      }
      else
      {
        socket.sendTXT(num, "saveDONE");
        LED(false);
        saveMode = false;
        digitalWrite(LED_PIN, LOW);
        return true;
      }
    }
  }
  if(type == WStype_DISCONNECTED)    //obsluga zdarenia polaczenie - wyswietlenie IP klienta i zapisanie jego id
  {
    saveMode = false;
    Serial.print("Połączenie zakończone: ");
    Serial.print("\n\r");
    return true;  
  }
}

bool send_data(String cardId)
{
  bool blokada = false;
  if(cardId.indexOf("blokada") != -1)
  {
    blokada = true;
    cardId.replace("blokada", ""); 
  }
  Serial.println();
  Serial.print("Wysyłanie pakietów z danymi: \n\r");
/*
 * DO BAZY DANYCH
*/
if(!hrReader)
  {
    client.begin(String("http://")+serverDB+"/rfid/dbase.php");
    if(client.GET() <= 0)
    {
      Serial.println("Połączenie z BD zostało utracone. Ponawianie połączenia.");
      tempTime[0] = millis();
      while(true)
      {
        LED(0);
        Serial.print(".");
        if(millis() - tempTime[0] <= 1000*60)        //czekamy 1m na nawiązanie połączenia
          continue;        
        if(client.GET() > 0)   //polaczenie z serwerem BD
        {
          Serial.println();
          Serial.print("Połączenie z serwerem BD - pomyślne.");
          Serial.println();
          break;
        }
        else
        {
          Serial.println("Połączenie z serwerem BD nie było możliwe. Kolejna próba za 1m.");
          tempTime[0] = millis();        
        }
      }
    }
    client.end();
    
    Serial.println();
    Serial.println("Wysyłanie danych do bazy MYSQL.");
    Serial.println();
    
    String tempDB = cardId;  
    tempDB.replace("Odmowa dostępu.", ""); 
    tempDB.replace("##", "&id=");
    tempDB.replace("->", "=");
    tempDB += "&reader="+idReader;
    if(blokada)
      tempDB += "&blokada=true";
    Serial.print(tempDB);
    client.begin(String("http://")+serverDB+"/rfid/dbase.php?insertReader");
    client.addHeader("Content-Type", "application/x-www-form-urlencoded");
    client.addHeader("Host", serverDB);
    client.addHeader("User-Agent", String("ESP32 / RFID-JobControl v1 by adres433 / ")+idReader);
    int errCode;      
    String odp = "";
    
    if((errCode = client.POST(tempDB)) > 0)
    {
      //Serial.print(errCode);
      if(errCode == HTTP_CODE_OK)
      {
        Serial.print("\r\nPrzesłano pomyślnie dane do serwera MYSQL.\n\r");
        odp = client.getString();
        if(odp != "Operacja pomyślna.")
          Serial.print(odp+"\r\n");
      }
      else
        Serial.print(String("Wystąpił błąd przy komunikacji z BD. \r\n\r\n")+errCode+"\n\r");
    }
    client.end();
  }
  
/*
 * PRZEZ WEBSOCKET
 */
  
  int wsNum = socket.connectedClients(true);
  Serial.print("\n\r");
  if(wsNum < 0)
  {
    Serial.println("Brak podłączonych klientów WebSocket\n\r Pakiet nie wysłany.");
    return true;
  }
  
    if(socket.broadcastTXT(cardId))
    {
      Serial.print("Wysłany pakiet. - WebSocket");
      Serial.print("\n\r\n\r");
      Serial.print(cardId);
      Serial.print("\n\r\n\r");
    }
    else
    {
      Serial.print("Nie wysłano pakietu. - błąd");
      delay(1000);
      return false;
    }      
  return true;
}

void serialEvent()
{
  long timeout = millis();
  while(millis()-timeout < (1000))    //czas na BREAK aby dostac sie do menu
  {
    Serial.print(".");              //kropka dla pokazania pracy układu
    while(Serial.available() > 0)   //sprawdzamy czy odebrano dane po UART
    {
      int bufor;                    //zmienna pomocnicza - przechowuje kod ASCII przeslanego znaku
      String uarttmp = "";          //zmienna przechowujaca przeslane polecenie
      bufor = Serial.read();        //przypisanie odebranego znaku z UART
      while(bufor == 26)            //oczekujemy znaku BREAK o nr 26 w kodzie ASCII, bez niego przechodzimy do standardowych procedur
      {
        cleanScreen();
        Serial.println("\rRFID-JobControl v1 by adres433");
        Serial.print("\rAktualne ustawienia: ");
        Serial.print("\n\r1. ESSID [max. 100 znaków]: ");
        Serial.print(ssid);
        Serial.print("|\n\r2. Hasło [max. 100 znaków]: ");
        Serial.print(password);
        Serial.print("|\n\r3. Port serwera WebSocket [xxxx]: ");
        Serial.print(wsPort);  
        Serial.print("|\n\r4. Adres serwera bazy danych [xxx.xxx.xxx.xxx]: ");
        Serial.print(serverDB);
        Serial.print("|\n\r5. Nr identyfikacyjny czytnika [xxxxxxxx]: ");
        Serial.print(idReader);
        Serial.print("|\n\r6. Czytnik dla HR [true|false]: ");     
        Serial.print((hrReader)?"TAK":"NIE"); 
        Serial.print("|\n\r7. DHCP [true|false]: ");  
        Serial.print((DHCP)?"Włączone":"Wyłączone");
        if(!DHCP)
        {
          Serial.print("\n\r8. IP - w przypadku wyłączonego DHCP [xxx.xxx.xxx.xxx] :");
          Serial.print(ip);
          Serial.print("\n\r9. DNS - w przypadku wyłączonego DHCP [xxx.xxx.xxx.xxx] :");
          Serial.print(dns);
          Serial.print("\n\r10. BRAMA - w przypadku wyłączonego DHCP [xxx.xxx.xxx.xxx] :");
          Serial.print(gateway);
          Serial.print("\n\r11. MASKA - w przypadku wyłączonego DHCP [xxx.xxx.xxx.xxx] :");
          Serial.println(mask);
        }
        if(!DHCP)
          Serial.print("\n\r\n\rCo chcesz zrobić: ([essid|passwd|port|bd|idreader|hr|dhcp|ip|dns|gateway|mask]->wartosc | exit)");
        else        
          Serial.print("\n\r\n\rCo chcesz zrobić: ([essid|passwd|port|bd|idreader|hr|dhcp]->wartosc | exit)");
        Serial.write("\r\n\r>");
        uarttmp = "";
        while(!Serial.available() && uarttmp.length() <= 0 && Serial.peek() != '13')   //jezeli nie odebrano zadnych danych oraz bufor polecenia jest pusty oczekujemy na dane z UART
        {
          delay(10);
        }
        while(Serial.available() || uarttmp.length() > 0)   //jezeli otrzymalismy dane lub jestesmy w trakcie odbiernia
         {
          int helpInt = Serial.peek();                      //zmienna pomocnicza przechowujaca kazdy odebrany znak po kolei
          if(helpInt != 13 && helpInt != 10 && Serial.available())  //jeżeli odebraliśmy jakis znak i nie jest to znak konca lini
          {
            uarttmp += char(Serial.read());                         //przypisujemy kolejne znaki tworzac lancuch i skladajac przeslane polecenie
          }
          else if(helpInt == 13 || helpInt == 10)                   //jezeli przeslany zostal znak konca lini
          {
            Serial.read();                                          //usuwamy znak konca lini ze strumienia
            uarttmp.trim();                                         //usuwamy biale znaki na poczatku i koncu przeslanego polecenia
            break;                                                  //przerywamy petle pobierajaca polecenie z UART
          }
          delay(10);  
         }        
          if(uarttmp.indexOf("essid->") != -1)                       //jezeli polecenie zawiera slowo kluczowe
            {
              uarttmp = uarttmp.substring(uarttmp.indexOf("->")+2);    //obcinamy polecenie pozostawiajac sama wartosc do zmiany
              Serial.print("\r\nPrzyjęto wartość: ");
              Serial.print(configRS(7, uarttmp));
              delay(200);
              configRS(7, "").toCharArray(ssid, 100);
            }
          if(uarttmp.indexOf("passwd->") != -1)
            {             
              uarttmp = uarttmp.substring(uarttmp.indexOf("->")+2);    //obcinamy polecenie pozostawiajac sama wartosc do zmiany
              Serial.print("\r\nPrzyjęto wartość: ");
              Serial.print(configRS(8, uarttmp));
              delay(200);
              configRS(8, "").toCharArray(password, 100);
            }
          if(uarttmp.indexOf("port->") != -1)
            {              
              uarttmp = uarttmp.substring(uarttmp.indexOf("->")+2);
              Serial.print("\r\nPrzyjęto wartość: ");
              Serial.print(configRS(9, uarttmp));
              delay(200);
              wsPort = configRS(9, "").toInt();
            }
          if(uarttmp.indexOf("dhcp->") != -1)
            {             
              if(uarttmp.indexOf("true") != -1)
                uarttmp = "1";
              else if (uarttmp.indexOf("false") != -1)
                uarttmp = "0";
              else     
              {           
                Serial.print("\r\nNieprawidłowa wartość [true|false].");
                delay(1000);
                continue;
              }
              Serial.print("\r\nPrzyjęto wartość: ");
              Serial.print(configRS(6, uarttmp));
              delay(200);
              DHCP = configRS(6, "");
            } 
            if(uarttmp.indexOf("hr->") != -1)
            {             
              if(uarttmp.indexOf("true") != -1)
                uarttmp = "1";
              else if (uarttmp.indexOf("false") != -1)
                uarttmp = "0";
              else     
              {           
                Serial.print("\r\nNieprawidłowa wartość [true|false].");
                delay(1000);
                continue;
              }
              Serial.print("\r\nPrzyjęto wartość: ");
              Serial.print(configRS(10, uarttmp));
              delay(200);
              hrReader = configRS(10, "");
            }
          if(uarttmp.indexOf("ip->") != -1 && !DHCP)
            {    
              uarttmp = uarttmp.substring(uarttmp.indexOf("->")+2);   
            
              Serial.print("\r\nPrzyjęto wartość: ");
              Serial.print(configRS(0, uarttmp));
              delay(200);
              ip.fromString(configRS(0, "")); 
            }              
          if(uarttmp.indexOf("dns->") != -1 && !DHCP)
            {     
              uarttmp = uarttmp.substring(uarttmp.indexOf("->")+2);  
              
              Serial.print("\r\nPrzyjęto wartość: ");
              Serial.print(configRS(1, uarttmp));
              delay(200);
              dns.fromString(configRS(1, ""));
            }
          if(uarttmp.indexOf("gateway->") != -1 && !DHCP)
            {    
              uarttmp = uarttmp.substring(uarttmp.indexOf("->")+2);     
               
              Serial.print("\r\nPrzyjęto wartość: ");
              Serial.print(configRS(2, uarttmp));
              delay(200);
              gateway.fromString(configRS(2, ""));
            }
          if(uarttmp.indexOf("mask->") != -1 && !DHCP)
            {    
              uarttmp = uarttmp.substring(uarttmp.indexOf("->")+2);    
               
              Serial.print("\r\nPrzyjęto wartość: ");
              Serial.print(configRS(3, uarttmp));
              delay(200);
              mask.fromString(configRS(3, ""));
            }
          if(uarttmp.indexOf("bd->") != -1)
            {    
              uarttmp = uarttmp.substring(uarttmp.indexOf("->")+2);    
               
              Serial.print("\r\nPrzyjęto wartość: ");
              Serial.print(configRS(4, uarttmp));
              delay(200);
              configRS(4, "").toCharArray(serverDB, 16);
            }
          if(uarttmp.indexOf("idreader->") != -1)
            {    
              uarttmp = uarttmp.substring(uarttmp.indexOf("->")+2);    
              if(uarttmp.length() != 8)
                {
                  Serial.print("Nr musi posiadać 8 cyfr i nie może się powtarzać.");
                  delay(1000);
                  continue;
                }
              Serial.print("\r\nPrzyjęto wartość: ");
              Serial.print(configRS(5, uarttmp));
              delay(200);
              idReader = configRS(5, "");
            }
          if(uarttmp.indexOf("exit") != -1)                //wyjscie z menu konfiguracji
            ESP.restart();
            
          uarttmp = "";                                       //czyscimy zmienna z poleceniem
          while(Serial.available())                           //dopuki bufor nie jest pusty
          {
            Serial.read();                                    //wybieramy wszystkie znajdujace sie w nim znaki
          }
          delay(1000);
      }
    }
  }
}
void cleanScreen()
{
  Serial.write(27); // ESC
  Serial.print("[2J"); // clear screen
  Serial.write(27);
  Serial.print("[H");     // cursor to home command
}

bool saveCard(String textID, String nrCardSave, int num)
{
  cleanScreen();
  tempTime[1] = millis();
  MFRC522::StatusCode statusRfid;
  byte nr_key = 0;            //zmienna nr klucza - ustawiana kiedy zadziała klucz domyślny
  while(saveMode)
  {
    if((millis() - tempTime[1]) >= (30*1000))
    {
      saveMode = false;
      rfidd.PICC_HaltA();
      rfidd.PCD_StopCrypto1();
      LED(false);
      return false;
    }
    Serial.print("Pozostało czasu: ");
    int tempTimeCounter = 30-(((millis()-tempTime[1])/1000)+1);
    Serial.print(tempTimeCounter);
    Serial.print("s. \n\r");
    String textTemp = "czas->";
    textTemp += String(tempTimeCounter);
    socket.sendTXT(num, textTemp);
    digitalWrite(LED_PIN, HIGH);
    Serial.print("Tryb zapisu karty. Zbliż kartę.\n\r");
    
    if(rfidd.PICC_IsNewCardPresent() && rfidd.PICC_ReadCardSerial())
    {
      digitalWrite(LED_PIN, LOW);
      String id;
      for(int i = 0; i < rfidd.uid.size; ++i)
      {
        id += (String) rfidd.uid.uidByte[i];
        i < rfidd.uid.size-1 ? id+=":" : id += "";
      }

      if(id.compareTo(nrCardSave) != 0)
        {
          Serial.print("Nieprawidłowa karta. Przyłóż kartę o ID: ");
          Serial.print(nrCardSave);
          Serial.println();
          LED(false);
          continue;
        }
      byte  buffers[16] = {0xff, 0xff, 0xff, 0xff, 0xff, 0xff, 0xff, 0xff, 0xff, 0xff, 0xff, 0xff, 0xff, 0xff, 0xff, 0xff};     //bufor zapisu, przechowuje zpisywany ciag w postaci tablicy 16 bajtów / znaków
      Serial.print("#");
      Serial.print(textID.length());
      Serial.print("#");
      //textID.getBytes(buffers, 16);    //zapis cigu do tablicy w postaci pojedynczych bajtów
      for(int i = 0; i <= 15 ; ++i)
      {
        if(textID.length()-i <= 0)
          break;
        buffers[15-i] = char(textID[textID.length()-i-1]);
      }
      Serial.print("TextID: ");
      Serial.print(textID);
      Serial.print(" ID: ");
      for(int i =0; i <= 15; ++i)
      {
        Serial.print(buffers[i], HEX);
        (i != 15)?Serial.print(":"):Serial.print("");
      }
      Serial.println();
      statusRfid = rfidd.PCD_Authenticate(MFRC522::PICC_CMD_MF_AUTH_KEY_A, block, &keyA, &(rfidd.uid));   //próba autoryzacji karty
      if(statusRfid != 0)
      {
        if(nr_key < 16)
        {
          for (byte i = 0; i < MFRC522::MF_KEY_SIZE; i++) 
          {
            keyA.keyByte[i] = keyDef[nr_key][i];
          }
          ++nr_key;
          
          for(int i =0; i <= 5; ++i)
          {
            Serial.print(keyA.keyByte[i], HEX);
            (i != 5)?Serial.print(":"):Serial.print("\n\r");
          }
          Serial.println(rfidd.GetStatusCodeName(statusRfid));
          Serial.print("Autoryzacja - niepowodzenie.\n\r");
          rfidd.PICC_HaltA();
          rfidd.PCD_StopCrypto1();
          LED(false);
          continue;
        }
        else
        {
          nr_key = 0;
          Serial.println(rfidd.GetStatusCodeName(statusRfid));
          Serial.println();
          Serial.print("Autoryzacja - niepowodzenie.\n\r");
          rfidd.PICC_HaltA();
          rfidd.PCD_StopCrypto1();
          LED(false);
          return false;
        }
      }
      else
      {
        Serial.print("Autoryzacja - powodzenie.\n\r Klucz: ");
        for(int i =0; i <= 5; ++i)
        {
          Serial.print(keyA.keyByte[i], HEX);
          (i != 5)?Serial.print(":"):Serial.print("\n\r");
        }
        Serial.println();
         if((statusRfid = rfidd.MIFARE_Write(block, buffers, 16)) != MFRC522::STATUS_OK)     //zapis danych przy pomocy klucza fabrycznego
         {
          Serial.print("Niepowodzenie zapisu bloku danych.\n\r");
          Serial.println(rfidd.GetStatusCodeName(statusRfid));
          rfidd.PICC_HaltA();
          rfidd.PCD_StopCrypto1();
          LED(false);
          return false;
         }
         else       //jeżeli zapis się powiódł to zabezpieczamy kartę zmienionym kluczm
         {
          for(int i = 0; i < 6; ++i)     //przepisanie zmienionego klucza do bufora jako klucz A
          {
            buffers[i] = keyDef[8][i];
            buffers[10+i] = 0xFF;
          }
          buffers[6] = 0xFF;
          buffers[7] = 0x07;
          buffers[8] = 0x80;
          buffers[9] = 0x69;

          
          if((statusRfid = rfidd.MIFARE_Write(blockkey, buffers, 16)) != MFRC522::STATUS_OK)     //zapis nowych kluczy oraz modyfikatorów dostępu na kartę
          {
            Serial.print("Niepowodzenie zapisu bloku konfiguracyjnego\n\r");
            Serial.println(rfidd.GetStatusCodeName(statusRfid));
            rfidd.PICC_HaltA();
            rfidd.PCD_StopCrypto1();
            LED(false);
            return false;
          }
          else
            break;            
         }
      }
    } 
    delay(100);     
  }
  rfidd.PICC_HaltA();
  rfidd.PCD_StopCrypto1();
  LED(true);
  digitalWrite(LED_PIN, LOW);
  return true;
}

String configRS(int what, String value)
{
  String saveString = "";
  if(!EEPROM.begin(1110))    //inicjalizacja pamieci EEPROM dla 256 Bajtow
  {
    Serial.print("Dostęp do pamięci trwałej nie możliwy.");
    return "";
  }
  else
  {
    int *p, *p2;
    switch(what)
    {
      case 0: //dla epromIP
        p = &eepromIP[0];
        p2 = &eepromIP[1];
      break;
      case 1: 
        p = &eepromDNS[0];
        p2 = &eepromDNS[1];
      break;
      case 2: 
        p = &eepromGAT[0];
        p2 = &eepromGAT[1];
      break;
      case 3: 
        p = &eepromMAS[0];
        p2 = &eepromMAS[1];
      break;
      case 4: 
        p = &eepromSBD[0];
        p2 = &eepromSBD[1];
      break;
      case 5: 
        p = &eepromIDR[0];
        p2 = &eepromIDR[1];
      break;
      case 6: 
        p = &eepromDHC[0];
        p2 = &eepromDHC[1];
      break;
      case 7: 
        p = &eepromESS[0];
        p2 = &eepromESS[1];
      break;
      case 8: 
        p = &eepromPWD[0];
        p2 = &eepromPWD[1];
      break;
      case 9: 
        p = &eepromPWS[0];
        p2 = &eepromPWS[1];
      break;
      case 10: 
        p = &eepromHR[0];
        p2 = &eepromHR[1];
      break;
      
    }
    
    if(value != "" && value !=" " && value.length() <= 100)
    {
      char point[100];
      int j = 0;
      value.toCharArray(point, 100);
      for(j = 0; j < 100 ; ++j)
      {
        char b = point[j];
        if(j >= value.length())
          b = 0;
        if(char(EEPROM.read(*p+j)) != b )
         {
            EEPROM.write(*p+j, b);
         }         
      }
    }
    saveString = "";
    for(int i=*p; i < *p2; ++i)
    {
      char b;
      b = char(EEPROM.read(i));
      saveString += b;
    }
  }
  EEPROM.commit();
  EEPROM.end();
  return saveString;
}

bool openGate(bool how, bool disabled)
{
  if(!disabled)
  {
    Serial.print("Otwarto kołowrót ");
    if(how)
    {
      Serial.print("w kierunku WEJŚCIA\r\n");
      digitalWrite(15, LOW);
    }
    else
    {
      Serial.print("w kierunku WYJŚCIA\r\n");
      digitalWrite(TX_2, LOW);
    }
  }
  else
  {
    Serial.print("Otwarto bramkę uchylną ");
    if(how)
    {
      Serial.print("w kierunku WEJŚCIA\r\n");
      digitalWrite(4, LOW);
    }
    else
    {
      Serial.print("w kierunku WYJŚCIA\r\n");
      digitalWrite(RX_2, LOW);
    }
  }
  
  delay(500);
  digitalWrite(15, HIGH);
  digitalWrite(TX_2, HIGH);
  digitalWrite(RX_2, HIGH);
  digitalWrite(4, HIGH);
}
