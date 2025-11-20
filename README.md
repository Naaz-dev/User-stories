 Simple PHP CRUD – Leerdoelenproject

Dit project is gemaakt als oefening voor tweedejaars studenten webdevelopment. De applicatie laat je zien hoe je een kleine maar volledige PHP CRUD-applicatie opzet met Docker, MariaDB (MySQL-compatibel) en een eenvoudige UI.

 Wat kun je met deze applicatie?
 Inloggen met een veilige wachtwoordcontrole (`password_hash` + `password_verify`).
 Alleen na inloggen het gebruikersoverzicht bekijken.
 Nieuwe gebruikers toevoegen met validatie-eisen en een dropdown voor de rol.
 Gebruikers bewerken (inclusief wachtwoord wijzigen) en verwijderen met bevestiging.
 Feedback krijgen via duidelijke meldingen wanneer iets fout gaat.
 Rolgebaseerde toegang: admins kunnen accounts beheren, gewone gebruikers hebben alleen-lezen toegang.

 Benodigde software
 Git
 Docker Desktop (inclusief Docker Compose)
 Een code editor zoals VS Code

 Snel starten
powershell
git clone <repo-url>
cd User-stories
docker compose up --build


Ga vervolgens naar <http://localhost:8080>. Log in met:

Gebruikersnaam `admin`
Wachtwoord `password`

 PhpMyAdmin is beschikbaar op <http://localhost:8081> (zelfde inlog als hierboven). Gebruik dit om de MariaDB-database te inspecteren en de leerdoelen rond SQL en "least privilege" te oefenen.

Werk je liever met MariaDB of MySQL Workbench? Gebruik dan:

- Host: `127.0.0.1`
- Poort: `3306`
- Gebruiker: `user_app`
- Wachtwoord: `user_secret`
- Schema: `user_management`

 Deze custom gebruiker heeft alleen de noodzakelijke rechten (SELECT/INSERT/UPDATE/DELETE). Gebruik het admin-account uit `database/init.sql` uitsluitend voor onderhoud.

> Kom je van een oudere versie met MySQL 8? Voer éénmalig `docker compose down -v` uit zodat MariaDB de datafolder opnieuw kan initialiseren.

> Root-toegang nodig in Workbench? Zie `docs/handleiding.md` voor het commando om root extern beschikbaar te maken en gebruik daarna `root` / `password` op `127.0.0.1:3306`.

> Let op: `database/init.sql` is nu slechts een voorbeeldscript. De container seedt niet meer automatisch; kopieer de SQL naar Workbench als je een nieuwe omgeving wilt opzetten.

 Projectstructuur


docker-compose.yml    definieert containers voor PHP, MariaDB en phpMyAdmin
Dockerfile            bouwt de PHP/Apache container met PDO-extensies
database/init.sql     maakt database, gebruiker en startdata aan
src/
	includes/           herbruikbare PHP bestanden (database, auth, validatie, helper)
	public/            publieke webroot met alle schermen en assets
		assets/           CSS en JavaScript voor styling en kleine interacties


 Belangrijke PHP-bestanden
includes/database.php – Maakt een PDO-verbinding aan via prepared statements.
includes/auth.php – Handige functies voor inloggen, sessiebeheer en afscherming.
includes/validation.php – Herbruikbare validatieregels voor formulieren.
public/index.php – Loginpagina met feedback bij fouten.
public/dashboard.php – Overzicht van gebruikers, alleen zichtbaar na inloggen.
public/add_user.php & public/edit_user.php – CRUD-schermen met validatie (alleen voor admins).
public/delete_user.php – Verwijdert een gebruiker via een POST-request en geeft feedback (alleen voor admins).

 Leerdoelen die je kunt afvinken

 Setup & Docker
 Repo clonen, Docker containers starten en inspecteren docker compose ps,docker exec.
 Logs bekijken docker compose logs app en bind mounts herkennen ./src → /var/www.
 Bestanden aanpassen op de host en direct resultaat zien in de container.

 PHP & CRUD
 Variabelen, arrays, foreach-loops en conditionals komen in elk scherm terug.
 Gebruik van `$_SESSION`, `$_GET`, `$_POST` wordt duidelijk in login-, edit- en delete-flows.
 Ontdek hoe redirects werken na succesvolle acties (`header('Location: ...')`).

 Database
`database/init.sql` laat zien hoe je een MariaDB-database en "least privilege" gebruiker maakt.
 Pas de query’s aan of voeg kolommen toe via phpMyAdmin of rechtstreeks met SQL.
 Prepared statements beschermen tegen SQL-injectie.

 HTTP & Security
 Inspecteer requests in de browser devtools (GET voor pagina’s, POST voor formulieren).
 Vergelijk verschillende HTTP-statuscodes door fouten te simuleren.
 Wachtwoorden worden gehasht opgeslagen; README legt uit waarom dat belangrijk is.
 Delete-knop stuurt POST en heeft een JavaScript confirm voor extra zekerheid.

 Proces & Reflectie
 Gebruik de user stories om taken op te knippen (UI, validatie, database, feedback).
 Maak een wireframe gebaseerd op de voorbeeldschermen in de bijlage.
 Denk na over Definition of Done (werkt lokaal, getest, code schoon, beveiliging geregeld).

 Extra opdrachten
 Voeg een filter of zoekfunctie toe aan de tabel.
 Breid validatie uit (bijv. controle op sterke wachtwoorden met regex).
 Vervang het eenvoudige flash-systeem door een component met meerdere berichten.

 Veiligheid & wachtwoorden
Geen platte tekst: wachtwoorden worden alleen als hash opgeslagen (`password_hash`).
Salt en rainbow tables: PHP voegt automatisch een unieke salt toe waardoor rainbow tables onbruikbaar zijn.
Encryptie vs hashing: encryptie kun je weer ontsleutelen, hashing niet – daarom is het geschikt voor wachtwoorden.
Gevoelige data buiten code: secrets staan in environment variabelen (`docker-compose.yml`).
Least privilege: de database-user heeft alleen SELECT/INSERT/UPDATE/DELETE rechten.

 Aanvullende documentatie
Wil je een diepere uitleg over alle onderdelen? Bekijk `docs/handleiding.md` voor een volledige walkthrough van de architectuur, beveiliging en uitbreidingsmogelijkheden.

 Containers stoppen
powershell
docker compose down


Gebruik `docker compose down -v` om ook de database-data te verwijderen en opnieuw te beginnen.

Veel succes en vooral: experimenteer! Pas de code aan, breid de formulieren uit en gebruik de leerdoelenlijst als checklist tijdens het bouwen.

