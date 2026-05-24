## Kurzbeschreibung des Projekts

* **Modul:** Interaktive Medien 4 an der Fachhochschule Graubünden (FS26)  
* **Themenfeld:** IoT-Applikation zum Thema Eltern mit kleinen Kindern  
* **Name des Projekts:** Eckzahn Eddy  
* **Team Physical Computing:** Marina Lampert, Laura Seger  
* **Team WebApp:** Max Hutmacher, Saskia Lerf
 
Eckzahn Eddy soll ein Produkt bzw. eine Webapp rund ums Zähneputzen sein in Kombination mit einer Zahnbürste, die über ein Touch- und Lagessensor verfügt. Ziel ist es, Kinder spielerisch dazu zu motivieren, ihre Zähne besser und regelmässiger zu putzen.

### UX & Konzeption

Mit folgendem Figma-Link erhältst du einen Einblick in den UX- und Konzeptentwicklungsprozess von Eddy Eckzahn – von ersten Ideen und Wireframes bis hin zur gestalterischen Ausarbeitung.

* **Figma:** (https://www.figma.com/design/7dYuJ7zHd2DgF7CtyG6XbB/im4-eckzahn-eddy?m=auto&t=oUmAeBcnwc444ZSt-1)

Hier erhältst du einen Einblick in unseren geplanten User Flow und die gedachte Nutzerführung innerhalb des Projekts:

* **User Flow \+ Screen Flow**
![Alternativtext](img/Screen%20Flow.jpg)  

### Setup

Wenn du nun neugierig geworden bist, kannst du über die folgenden Links sowohl unsere Website besuchen als auch ein Video zur Nutzung der Website in Kombination mit der Zahnbürste ansehen:

* **WebApp:** (https://eckzahneddy.marina-lampert.ch/login.html)  
* **Video-Dokumentation:** Link in interner Google Tabelle abgelegt (keine Veröffentlichung)

#### Installationsanleitung WebApp

 **Schritt-für-Schritt-Anleitung für Aussenstehende:**

1. *Was benötige ich an Infrastruktur?*
- Einen Webserver mit PHP-Unterstützung (z.B. Hosttech, Cyon, XAMPP lokal)
- Eine MySQL-Datenbank
- Eine Domain oder lokale URL mit HTTPS (damit der ESP32 Daten senden kann)

2. *Was muss ich auf meinem Webserver installieren?* 
Nichts installieren. Einfach die Dateien hochladen. Der Server braucht nur:
- PHP 8.x (mit mysql)
- MySQL 5.7+ oder MariaDB

Danach kann unser GitHub Repository geklont werden:

Bash Command:
git clone https://github.com/lostmarushka/IM4-Eckzahn-Eddy.git

Dann alle Dateien in den öffentlichen Ordner des Servers kopieren (public_html/ oder htdocs/).

3. *Wie kann ich die Datenbank importieren?* 
Neue Datenbank anlegen, dann importieren per PhpMyAdmin:
Datenbank auswählen → Importieren → system/db.sql hochladen

4. *Wo muss ich die DB-Credentials eintragen?*
Die Datei api/config.php ist absichtlich nicht im Repository (steht im .gitignore). Auf dem Server manuell erstellen:

<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'dein_datenbankname');
define('DB_USER', 'dein_benutzer');
define('DB_PASS', 'dein_passwort');

5. *Wie rufe ich die WebApp auf?*
Browser öffnen:

https://eckzahneddy.marina-lampert.ch/index.html
Account registrieren → Familie anlegen → Kindprofile erstellen → fertig.

6. *Wie nehme ich das physische Artefakt in Betrieb?*

const char* ssid      = "DEIN_WLAN_NAME";
const char* password  = "DEIN_WLAN_PASSWORT";
const char* serverUrl = "https://deine-domain.ch/api/load.php";

1. Arduino IDE öffnen
2. Im Board Manager ESP32C6 Dev Module hinzufügen
3. Richtigen Port auswählen
4. Code auf den ESP32 hochladen
5. Serial Monitor öffnen → Sensoren kalibrieren sich automatisch
6. Zahnbürste in Betrieb nehmen → Daten fliessen direkt in die Datenbank


#### Bauanleitung Physical Computing

Du möchtest nun Eddy Eckzahn selber nachbauen? Wir zeigen dir wie es geht:

 Du brauchst folgende Sachen:

- 1× Steckplatte  
- 1× Microcontroller ESP32-C6-WROOM-1  
- 1× Kapazitiver Touchsensor TTB223B  
- 1× 6DOF Lagesensor MPU6500  
- 1× USB-Kabel  
- 7× Jumper-Kabel (male)  
- 4× Jumper-Kabel (female)  
- 1× langes leitfähiges Kabel  
- 1× Zahnbürste  
- Klebeband  

Stecke alle Komponenten wie im Steckplan beschrieben zusammen.
Den Touchsensor haben wir an einem Ende eines langen Kabels angelötet. Das andere Ende des Kabels wurde durch die Rückseite des Bürstenkopfs geführt. Anschliessend haben wir die Kabelenden aufgefranst, sodass sie sich mit den Borsten der Zahnbürste vermischen. Dadurch wird das Kabel selbst zu einem Teil der Bürstenoberfläche der Zahnbürste.

* **Steckplan**

![Alternativtext](img/Steckplan.jpeg)
![Alternativtext](img/Zahnbürste%20mit%20Sensoren.jpeg)


Wenn dein Setup bereit ist, öffne das Arduino-Programm und füge den folgenden Code hinzu.  
Den vollständigen Code findest du hier: [mc.ino](mc.ino)


Anschliessend öffnest du in Arduino den Board Manager und fügst das „ESP32C6 Dev Module“ hinzu.
Wähle danach den richtigen Port aus und lade den Code auf den Microcontroller hoch.
Sobald der Upload abgeschlossen ist, öffnest du den **Serial Monitor** und wählst dort erneut den passenden Port aus. Voilà – nun kannst du beobachten, wie sich die Sensoren kalibrieren. Nach der Kalibrierung werden bei Bewegungen der Zahnbürste sowie bei Berührungen am Bürstenkopf die Daten erfasst und für die Speicherung in der Datenbank vorbereitet.

* **Komponentenplan**
![Alternativtext](img/komponentenplan%202.jpg)

| Komponente | Funktion |
| :--- | :--- |
| ESP32 Dev Board | Microcontroller, führt Hauptprogramm aus, kommuniziert mit WLAN |
| Kap. Touchsensor TTB223B | Erfasst Daten im Zahnbürstenkopf (Berührung mit Zähnen) |
| 6DOF Lagesensor MPU6500 | Erfasst Daten von der Zahnbürste (ob sich Zahnbürste bewegt, Kind Zähne putzt) |

| Verbindung/Protokoll | Funktion |
| :--- | :--- |
| WLAN | Verbindung des ESP32 mit Webserver |
| HTTP (POST) | Sendet JSON-Daten vom ESP32 an den Server (load.php) |
| SQL (MySQL) | Datenübertragung zwischen PHP und Datenbank |

| Datei/Modul | Funktion |
| :--- | :--- |
| mc.ino | Arduino-Hauptprogramm: Bewegungs- und Berührungsmessung |
| load.php| Serverlogik: Empfängt Daten und schreibt sie in die Datenbank |
| sensor-status.php | Serverlogik: Stellt gespeicherte Trinkdaten als JSON bereit |

| Komponente Webapp | Funktion |
| :--- | :--- |
| index.html | Grundstruktur für Webseite |
| style.css| Visuelles Styling der Webseite |
| auth.js | Schutz: Ohne Login kommt man nicht in die Webapp rein |
| logout.js| Führt zurück zum Login, kein Zugriff mehr |
| img | Statische Inhalte zur Darstellung |^

## Technische Details

* **Projektstruktur / Code-Struktur:**
Alle Codes und ihre Verlinkungen sind auf dem GitHub Repository einsehbar.  

* **Datenschnittstelle [zwischen WebApp und Physical Computing]:**  
Die Datenschnittstelle zwischen Physical Computing und WebApp basiert auf der Entität "Sensordata".  
Die Sensoren an der Zahnbürste senden ihre Messwerte (Touch- und Bewegungssensor) über WLAN an die Datenbank, wo sie in der Entität "Sensordata" gespeichert werden.  
Das WebApp-Team greift anschließend auf diese Daten zu, liest sie aus und verarbeitet sie weiter für die Darstellung und Logik innerhalb der Webanwendung.

* **ERM:** ![Alternativtext](img/ERM%20Eckzahn%20Eddy.png)

Im Mittelpunkt des Systems stehen die Entitäten User*innen, Familien, Kinder, Sensordaten und Events. Eine Userin repräsentiert eine Benutzerin der App, beispielsweise ein Elternteil oder eine erziehungsberechtigte Person. Zu jeder Userin werden Informationen wie Name, E-Mail-Adresse und Passwort gespeichert. Ausserdem ist jede Userin genau einer Familie zugeordnet. Eine Familie kann dabei mehrere User*innen besitzen.

Die Entität „Familie“ dient dazu, mehrere Kinder und User*innen organisatorisch zusammenzufassen. Zu jeder Familie werden eine eindeutige ID sowie der Familienname gespeichert. Einer Familie können mehrere Kinder zugeordnet werden, während jedes Kind immer nur zu einer Familie gehört.

Die Entität „Kinder“ enthält die Daten der Kinder, deren Zahnputzverhalten überwacht wird. Für jedes Kind werden eine eindeutige ID, der Name sowie die Zugehörigkeit zur Familie gespeichert. Die Kinder stehen im direkten Zusammenhang mit den von der Zahnbürste erfassten Sensordaten und Ereignissen.

Die intelligenten Zahnbürsten senden kontinuierlich Sensordaten an die App. Diese werden in der Entität „Sensordaten“ gespeichert. Dazu gehören beispielsweise Zeitstempel, Aktivitätswerte sowie Informationen des Lage- oder Touchsensors. Jedes Kind kann dabei viele Sensordatensätze besitzen, während jeder einzelne Datensatz genau einem Kind zugeordnet ist. Dadurch lässt sich nachvollziehen, wie lange und wie häufig ein Kind seine Zähne putzt.

Zusätzlich werden besondere Ereignisse in der Entität „Events“ gespeichert. Dazu zählen beispielsweise der Start oder das Ende eines Putzvorgangs oder andere relevante Aktionen der Zahnbürste. Zu jedem Event werden eine ID, ein Zeitstempel, die Art des Ereignisses sowie die Zuordnung zum entsprechenden Kind gespeichert. Auch hier gilt, dass ein Kind mehrere Events besitzen kann, ein Event jedoch immer nur zu einem Kind gehört.

* **Authentifizierung:** Bei der Webapp können die Eltern ein Login erstellen (Mail und Passwort) und anschliessend ihre Kinder zu ihrem Profil hinzufügen. Es besteht die Möglichkeit die Kinder aus dem Profil zu entfernen.

## Known bugs

* **Was noch nicht einwandfrei funktioniert** 
Leider können wir die Daten nur etwas zeitverzögert auf der Website anzeigen. Wenn das Kind aufhört die Zähne zu putzen (also beide Sensoren sind in diesem Zustand nicht mehr aktiv), dann braucht es ein paar Sekunden auf der Website, bis der Timer stehen bleibt. 100% live kann das aber ja auch gar nicht funktionieren. Vielleicht könnte man die Verzögerungszeit aber noch etwas optimieren.

Die Familien_ID wurde auf 1 gehardcodet. Das heisst konkret: Es gibt nur eine Familie und alle Kinder gehören dieser Familie an. Auch bei neu erstellen Familien gehören die bereits vordefinierten Kinder der Familie 1 der neuen Familie direkt an. Jeder eingeloggte User sieht die Kinderprofile aller anderen User. Das ist natürlich noch falsch. Die Logik, die familie_id des eingeloggten Users aus der DB zu lesen, wurde aus Zeitgründen noch nicht implementiert.


* **Was könnte noch verbessert werden**
Um das Zahnputz-Erlebnis für Kinder noch spannender und motivierender zu gestalten, könnte künftig eine animierte Version von Eddy Eckzahn integriert werden. Aufgrund des begrenzten Zeitrahmens dieses Moduls konnte diese Funktion noch nicht umgesetzt werden.

Die Idee dahinter wäre, dass Eddy während des Zähneputzens lebendig auf die Aktionen des Kindes reagiert. Beispielsweise könnten seine kleinen T-Rex-Arme mit der Putzdauer langsam wachsen, bis sie gross genug sind, um gemeinsam mit dem Kind die Zähne zu putzen. Hört das Kind vorzeitig auf, würden die Arme wieder schrumpfen und Eddy traurig reagieren. Wird jedoch die empfohlene Putzzeit von zwei Minuten erreicht, könnten verschiedene kleine Freudentänze oder Animationen abgespielt werden, um das Kind spielerisch zu belohnen.

Aus Zeitgründen habe ich zudem auf die Profilübersichtsseite verzichtet. Dabei fehlen nun die Anpassungseinstellungen des Elternprofils (Löschen & Namen ändern). Stattdessen habe ich die Lösch und Editierfunktionen der Kinderprofile direkt bei der Kinderprofilauswahl nach dem Login implementiert. Das ist zwar etwas weniger umfangreich als geplant, zeigt aber dass ich mich mit den Einstellungsbedürfnissen der User durchaus auseinandergesetzt habe.

Zukünftig könnte man auch eine Vergleichsstatistikseite für die Eltern bauen um auf einen Blick alle Daten der Kinder einzusehen. Wir haben das jetzt weggelassen, weil wir der Meinung sind, dass diese Vergleiche den Kindern vielleicht sogar schaden könnte. Man wird im Leben ja sonst schon oft verglichen. Vielleicht ist das also in der Erziehung noch nicht notwendig. 

## Umsetzungsprozess

* **Reflexion / Erfahrung / Lernfortschritt:**

Physical Computing:

Zum ersten Mal kamen wir mit Physical Computing in Kontakt. In so kurzer Zeit und ohne Vorerfahrung eine Zahnbürste mit Sensoren auszustatten, war eine sehr spannende, aber auch intensive und teilweise anstrengende Erfahrung.
Wenn alles wie geplant funktionierte, war die Freude entsprechend gross und sehr motivierend. Gleichzeitig gab es jedoch auch einige Tiefpunkte, da wir mit den Programmen und der gesamten Umgebung zum ersten Mal gearbeitet haben und Fehler oft nicht schnell oder selbstständig finden konnten.
Wir sind überzeugt, dass solche Prozesse mit zunehmender Erfahrung deutlich effizienter und sicherer werden. Durch mehr Übung und einfaches Ausprobieren würde die Umsetzung in Zukunft sicherlich schneller und reibungsloser verlaufen.

Webapp:

Wir haben zum ersten Mal Figma Make in Kombination mit dem regulären Figma verwendet und dabei den gesamten Workflow kennengelernt. Auch den GitHub Connector für Perplexity haben wir erstmals im Coding-Prozess eingesetzt. Insgesamt haben wir damit den kompletten Workflow mit KI-Unterstützung zum ersten Mal ausprobiert.
Zudem konnten wir einen besseren Einblick in das Zusammenspiel von PHP, JavaScript und HTML gewinnen, da beide Personen im Webapp-Team während dem 3. Semester im Ausland waren und deshalb das Modul IM3 nicht besucht haben.

Der Zeitrahmen war aber wirklich ziemlich eng. 

* **Herausforderungen & Lösungen:** 

Physical Computing: 

Die erste Herausforderung bestand darin, geeignete Sensoren für unsere Zahnbürste auszuwählen. Zunächst haben wir lediglich einen Bewegungssensor in Betracht gezogen. Um jedoch zu verhindern, dass die Kinder die Zahnbürste nur schütteln, ohne tatsächlich zu putzen, entschieden wir uns zusätzlich für einen zweiten Sensor. Erst wenn beide Sensoren gleichzeitig aktiviert sind, werden die Daten aufgezeichnet.
Als zweiten Sensor wählten wir einen Touchsensor. Dieser wurde mithilfe eines leitfähigen Kabels verlängert, sodass er im Bürstenkopf integriert werden konnte. Eine besondere Schwierigkeit bestand dabei darin, das ausgefranste Kabel so im Bürstenkopf zu verlegen, dass die Touch-Funktion zuverlässig erhalten bleibt.
Heute funktionieren beide Sensoren grundsätzlich gut im Zusammenspiel von Bewegung und Berührung. Dennoch bleibt die Aktivierung des Touchsensors gelegentlich etwas unberechenbar, sodass es manchmal überraschend ist, ob er tatsächlich auslöst oder nicht.

Webapp: 

Bei der Implementierung der Sensoren des Physical-Computing-Teams wurde versucht, eine Verbindung zwischen Hardware und Webapp herzustellen. Dabei bestand die Herausforderung darin, die Sensordaten korrekt in die bestehende Codebasis einzubinden und diese zuverlässig in der WebApp verfügbar zu machen. Ich denke da vor allem an die Einbindung des Timers die ich zuerst versucht habe mit einer Videoeinbindung zu lösen. Die Animation gelang dann aber durch Empfehlung von Nick schlussendlich mit CSS (keine Ladezeiten und einfacher für Start & Stopp durch Triggerung der Sensordaten). Es war schwierig mit den vielen php und javascript files den Überblick zu behalten und strukturiert zu arbeiten. Dabei lag der Fokus vor allem auf der Fehleranalyse und dem schrittweisen Zusammenführen der unterschiedlichen Sprachen.

* **KI-Einsatz:** Mit Hilfe von Figma Make konnten wir unseren Prototypen relativ schnell und effizient ausarbeiten. Das UX Design konnten wir dann teilweise mit Claude umsetzen, wobei Anpassungen natürlich nötig waren. Kleinere Anpassungen konnte ich zuverlässig mit KI lösen wobei grössere Veränderungen oft neue Probleme mit sich brachten.

Auch für das Physical-Computing-Team war KI unverzichtbar. Mithilfe von ChatGPT wurden die von den Dozierenden zur Verfügung gestellten Codes an unsere eigenen Anforderungen angepasst und entsprechend weiterentwickelt bzw. umgeschrieben.
 
* **Fazit:** Das Projekt bot uns einen spannenden Einblick in die Welt der interaktiven Medien. Besonders interessant war es, nicht nur digital am Laptop zu arbeiten, sondern auch physisch etwas mit den eigenen Händen zu bauen und dieses mit der digitalen Welt zu verbinden. Leider war die Zeit im Modul sehr begrenzt, wodurch vieles eher oberflächlich behandelt werden musste und zudem stark auf den Einsatz von KI zurückgegriffen wurde.
Trotz dieser Einschränkungen sind wir mit dem Ergebnis zufrieden und hoffen, dass Eddy Eckzahn vielen Kindern Freude beim Zähneputzen bereiten wird.

