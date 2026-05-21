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
* **Video-Dokumentation:** [Link zum Video auf Youtube](http://link.zum.video) 

#### Installationsanleitung WebApp

***verständliche** Schritt-für-Schritt-Anleitung für Aussenstehende, um das Projekt zu klonen und auf einem eigenen Server zu installieren*

1. *Was benötige ich an Infrastruktur?*  
2. *Was muss ich auf meinem Webserver installieren?*  
3. *Wie kann ich die Datenbank importieren?*  
4. *Wo muss ich die DB-Credentials eintragen?*  
5. *…*  
6. *Wie nehme ich das physische Artefakt in Betrieb?*

#### Bauanleitung Physical Computing

* Du möchtest nun Eddy Eckzahn selber nachbauen? Wir zeigen dir wie es geht:) 

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

* **Komponentenplan** (betrifft Physical Computing, vgl. Slides Kapitel 15): Schaubild enthält*

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
| style.php| Visuelles Styling der Webseite |
| img | Statische Inhalte zur Darstellung |
| img | Statische Inhalte zur Darstellung |



## technische Details

// Hier sollte das Verständnis ersichtlich sein / Wie stehen die Dateien in Beziehung zueinander, Wie reden Die Dateien miteinander, Wie ist der Weg der Daten

* **Projektstruktur / Code-Struktur:**
Alle Codes und ihre Verlinkungen sind auf dem GitHub Repository einsehbar.  
Hier ein Beispiel:  
[js/stats-daily.js](stats-daily.js) 

* **Datenschnittstelle: \[***zwischen WebApp und Physical Computing*\]  
* **ERM:** ![Alternativtext](img/ERM%20Eckzahn%20Eddy.png)

Im Mittelpunkt des Systems stehen die Entitäten User*innen, Familien, Kinder, Sensordaten und Events. Eine Userin repräsentiert eine Benutzerin der App, beispielsweise ein Elternteil oder eine erziehungsberechtigte Person. Zu jeder Userin werden Informationen wie Name, E-Mail-Adresse und Passwort gespeichert. Ausserdem ist jede Userin genau einer Familie zugeordnet. Eine Familie kann dabei mehrere User*innen besitzen.

Die Entität „Familie“ dient dazu, mehrere Kinder und User*innen organisatorisch zusammenzufassen. Zu jeder Familie werden eine eindeutige ID sowie der Familienname gespeichert. Einer Familie können mehrere Kinder zugeordnet werden, während jedes Kind immer nur zu einer Familie gehört.

Die Entität „Kinder“ enthält die Daten der Kinder, deren Zahnputzverhalten überwacht wird. Für jedes Kind werden eine eindeutige ID, der Name sowie die Zugehörigkeit zur Familie gespeichert. Die Kinder stehen im direkten Zusammenhang mit den von der Zahnbürste erfassten Sensordaten und Ereignissen.

Die intelligenten Zahnbürsten senden kontinuierlich Sensordaten an die App. Diese werden in der Entität „Sensordaten“ gespeichert. Dazu gehören beispielsweise Zeitstempel, Aktivitätswerte sowie Informationen des Lage- oder Touchsensors. Jedes Kind kann dabei viele Sensordatensätze besitzen, während jeder einzelne Datensatz genau einem Kind zugeordnet ist. Dadurch lässt sich nachvollziehen, wie lange und wie häufig ein Kind seine Zähne putzt.

Zusätzlich werden besondere Ereignisse in der Entität „Events“ gespeichert. Dazu zählen beispielsweise der Start oder das Ende eines Putzvorgangs oder andere relevante Aktionen der Zahnbürste. Zu jedem Event werden eine ID, ein Zeitstempel, die Art des Ereignisses sowie die Zuordnung zum entsprechenden Kind gespeichert. Auch hier gilt, dass ein Kind mehrere Events besitzen kann, ein Event jedoch immer nur zu einem Kind gehört.

* **Authentifizierung:** \[*Erklärung*\]

## Known bugs

* Was noch nicht einwandfrei funktioniert: 
Leider können wir die Daten nur etwas zeitverzögert auf der Website anzeigen. Wenn das Kind aufhört die Zähne zu putzen (also beide Sensoren sind in diesem Zustand nicht mehr aktiv), dann bruacht es ein paar Sekunden auf der Website, bis der Timer stehen bleibt. Wir haben leider keine Live-Übertragung der Daten 
* Was ist uns aufgefallen bei der Entwicklung:
----

* Was könnte noch verbessert werden:
Um das Zahnputz-Erlebnis für Kinder noch spannender und motivierender zu gestalten, könnte künftig eine animierte Version von Eddy Eckzahn integriert werden. Aufgrund des begrenzten Zeitrahmens dieses Moduls konnte diese Funktion noch nicht umgesetzt werden.
Die Idee dahinter wäre, dass Eddy während des Zähneputzens lebendig auf die Aktionen des Kindes reagiert. Beispielsweise könnten seine kleinen T-Rex-Arme mit der Putzdauer langsam wachsen, bis sie gross genug sind, um gemeinsam mit dem Kind die Zähne zu putzen. Hört das Kind vorzeitig auf, würden die Arme wieder schrumpfen und Eddy traurig reagieren. Wird jedoch die empfohlene Putzzeit von zwei Minuten erreicht, könnten verschiedene kleine Freudentänze oder Animationen abgespielt werden, um das Kind spielerisch zu belohnen.


## Umsetzungsprozess

* **Reflexion / Erfahrung / Lernfortschritt:**
Zum ersten Mal kamen wir mit Physical Computing in Kontakt. In so kurzer Zeit und ohne Vorerfahrung eine Zahnbürste mit Sensoren auszustatten, war eine sehr spannende, aber auch intensive und teilweise anstrengende Erfahrung.
Wenn alles wie geplant funktionierte, war die Freude entsprechend gross und sehr motivierend. Gleichzeitig gab es jedoch auch einige Tiefpunkte, da wir mit den Programmen und der gesamten Umgebung zum ersten Mal gearbeitet haben und Fehler oft nicht schnell oder selbstständig finden konnten.
Wir sind überzeugt, dass solche Prozesse mit zunehmender Erfahrung deutlich effizienter und sicherer werden. Durch mehr Übung und einfaches Ausprobieren würde die Umsetzung in Zukunft sicherlich schneller und reibungsloser verlaufen.

* **Herausforderungen & Lösungen:** 
Die erste Herausforderung bestand darin, geeignete Sensoren für unsere Zahnbürste auszuwählen. Zunächst haben wir lediglich einen Bewegungssensor in Betracht gezogen. Um jedoch zu verhindern, dass die Kinder die Zahnbürste nur schütteln, ohne tatsächlich zu putzen, entschieden wir uns zusätzlich für einen zweiten Sensor. Erst wenn beide Sensoren gleichzeitig aktiviert sind, werden die Daten aufgezeichnet.
Als zweiten Sensor wählten wir einen Touchsensor. Dieser wurde mithilfe eines leitfähigen Kabels verlängert, sodass er im Bürstenkopf integriert werden konnte. Eine besondere Schwierigkeit bestand dabei darin, das ausgefranste Kabel so im Bürstenkopf zu verlegen, dass die Touch-Funktion zuverlässig erhalten bleibt.
Heute funktionieren beide Sensoren grundsätzlich gut im Zusammenspiel von Bewegung und Berührung. Dennoch bleibt die Aktivierung des Touchsensors gelegentlich etwas unberechenbar, sodass es manchmal überraschend ist, ob er tatsächlich auslöst oder nicht.


* **KI-Einsatz:** Mit Hilfe von Figma Make konnten wir unseren Prototypen relativ schnell und effizient ausarbeiten.
Auch der Einsatz von KI war für das Physical-Computing-Team unverzichtbar. Mithilfe von ChatGPT wurden die von den Dozierenden zur Verfügung gestellten Codes an unsere eigenen Anforderungen angepasst und entsprechend weiterentwickelt bzw. umgeschrieben.
 
* **Fazit:** …
