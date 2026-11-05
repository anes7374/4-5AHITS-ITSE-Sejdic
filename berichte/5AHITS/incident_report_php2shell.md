---
title: "Incident-Report: PHP2Shell (CVE-2026-109618 / CVE-2026-109619)"
layout: default
---

# Incident-Report: PHP2Shell

**CVE-2026-109618 · CVE-2026-109619**

| | |
|---|---|
| **Team** | Anes Sejdic, Aldin Mukic |
| **Klasse** | 5AHITS |
| **Fach** | ITSE (Theorie), Incident-Briefing |
| **Interaktive Erklärung aus dem Briefing:**| [PHP2Shell Schritt für Schritt](PHP2Shell_Animation.html)|


---

## 1. Zusammenfassung

PHP2Shell ist der Spitzname für zwei kritische Lücken in der PHP-Engine selbst (CVSS 4.0: 9.2), die am 21. Oktober 2026 mit Notfall-Updates geschlossen wurden. Ein Angreifer ohne Login lässt mit einem präparierten Datei-Upload den Speicher eines PHP-Prozesses volllaufen; PHP bricht ab, ohne aufzuräumen, und hinterlässt Zeiger auf freigegebenen Speicher. Daraus entstehen ein Datenleck und Remote Code Execution – egal, wie sicher die Website selbst programmiert ist. Weil Details vorab durchsickerten, gab es Angriffe schon vor dem Patch; am 26. Oktober bestätigte die US-Behörde CISA die aktive Ausnutzung.

## 2. Zeitablauf

| Datum | Ereignis |
|---|---|
| 2024 | Ein PHP-Entwickler bemerkt den Fehler, ein Exploit ist nicht nachweisbar – der Fix bleibt liegen („structural failure“, PHP Foundation). |
| Aug. 2026 | Calif, OpenAI und Wiz finden die Lücken, teils mit KI-Hilfe, und melden sie an PHP. |
| 17. Okt. | Amazons Honeypots registrieren einen ersten Ausnutzungsversuch. |
| 20. Okt. | Die CVE-Nummern sickern mit falschem Score (10.0) durch, Calif veröffentlicht versehentlich ein Exploit-Video. PHP zieht das Release um einen Tag vor. |
| 21. Okt. | Patch erscheint (8.5.12, 8.4.27, 8.3.36, 8.2.35); Hoster und Cloudflare melden schon vorher Angriffe. Regierungen nehmen ihre Portale vorsorglich offline, z. B. in den Niederlanden und in Finnland. |
| 24. Okt. | Erste bestätigte Remote Code Execution auf Honeypots. |
| 26. Okt. | CISA nimmt beide CVEs in den Katalog aktiv ausgenutzter Schwachstellen (KEV) auf. |

## 3. Betroffene Organisationen und Systeme

- **Alle PHP-Server**, die Web-Anfragen bedienen: PHP 5.4 bis 8.5 vor dem 21.10.2026.
- **Alle Anwendungen darauf**, z. B. WordPress, Drupal und Joomla – der Fehler tritt auf, bevor der Website-Code läuft.
- **Ausmaß:** Rund 69 % aller Websites mit bekannter Server-Sprache laufen auf PHP; die meisten Server, die ihre Version verraten, nutzen Versionen ohne offiziellen Fix (≤ 8.1).
- **Bestätigte Opfer:** 25 übernommene Server, darunter Firewalls von Palo Alto Networks und drei Systeme mit Bezug zur US-Regierung (Ziele in USA, Japan, Taiwan, Südkorea).

## 4. Angriffsvektor und ausgenutzte Schwachstellen

**Einstieg:** eine HTTP-Anfrage mit großem, verschachteltem Datei-Upload, ohne Login. Läuft dabei das `memory_limit` voll, springt PHP sofort ans Ende der Anfrage und überspringt das Aufräumen.

| | CVE-2026-109618 | CVE-2026-109619 |
|---|---|---|
| **Typ** | Speicherkorruption nach Out-of-Memory | Use-after-free durch veralteten globalen Zustand |
| **Kern** | Eine halb gefüllte Tabelle wird beim Abbau trotzdem verarbeitet – samt Restdaten des Angreifers. | Globale Zeiger zeigen weiter in freigegebenen Speicher; die nächste Anfrage im selben Prozess benutzt sie. |

**Folgen:** Ein veralteter Zeiger landet als Session-ID im `PHPSESSID`-Cookie – so gelangt fremder Speicher (Passwörter, Tokens, Adressen) zum Angreifer. Mit diesem Wissen und der Speicherkorruption ist Codeausführung möglich. In der Dream-Kampagne wurde das mit der Linux-Lücke „Fragnesia“ (CVE-2026-46300) kombiniert, um root-Rechte zu bekommen.

## 5. Täter und Motiv

Nach dem Leak wurden die Lücken schnell aktiv angegriffen. Hinter der gezielten Kampagne gegen Firewalls und US-Regierungssysteme vermutet Dream Security eine China-nahe Operation mit KI-gestützten Werkzeugen; ein konkreter Akteur wurde nicht genannt, und die Zuordnung ist umstritten. Ein Motiv ist nicht bestätigt; die Ziele – Firewalls und Regierungsnetze – sprechen eher für Spionage.

## 6. Auswirkungen

- **Daten:** Zugangsdaten und Tokens aus fremden Anfragen konnten abfließen – auch ohne Codeausführung.
- **Betrieb:** weltweite Notfall-Updates, Behörden-Websites vorsorglich offline, 25 Server vollständig übernommen.
- **Finanziell:** keine belastbaren Zahlen veröffentlicht; Kosten vor allem durch Patching, Ausfälle und Forensik.

## 7. Reaktion und Behebung

- **PHP:** Notfall-Release am 21.10. Der Fix ersetzt den harten Abbruch durch eine sichere Behandlung und setzt globale Zeiger zuverlässig zurück.
- **Infrastruktur:** Cloudflare aktivierte Schutzregeln für alle Kunden, auch gratis; Hoster lieferten Hotfixes.
- **Behörden:** Die US-Behörde CISA nahm die Lücken in ihren Katalog aktiv ausgenutzter Schwachstellen auf – Pflicht-Patch für US-Ämter.
- **Für Betreiber:** PHP updaten, **alle Worker neu starten**, Zugangsdaten tauschen, Logs ab Mitte Oktober prüfen.

## 8. Lessons Learned und Gegenmaßnahmen

1. **Schnell patchen** – zwischen Leak und ersten Angriffen lagen nur Stunden.
2. **Alte Versionen ersetzen** – PHP 8.1 und älter bekommt keine offiziellen Fixes mehr.
3. **Defense in Depth** – WAF/Cloudflare als Puffer, bis der Patch da ist.
4. **Admin-Zugänge abschotten** – Management-Oberflächen gehören nicht offen ins Internet.
5. **Logs rückwirkend prüfen** – Angriffe liefen schon vor dem Patch; Secrets tauschen.
6. **KI verändert das Spiel** – der Bug war seit 2024 bekannt; KI hat vor allem das Bauen eines Exploits stark beschleunigt, für Angreifer wie Verteidiger.

## 9. Mögliche Testfragen

**1. Warum schützt ein WordPress-Update nicht vor PHP2Shell?**
Die Lücke liegt in PHP selbst und wird ausgelöst, bevor der Website-Code läuft. Nur ein PHP-Update plus Neustart der Worker hilft.

**2. Was ist die gemeinsame Ursache der beiden CVEs?**
Bei Speichermangel bricht PHP hart ab und überspringt das Aufräumen. Zurück bleiben eine halb fertige Tabelle (109618) bzw. Zeiger auf freigegebenen Speicher (109619).

**3. Warum reicht es nicht, nur den Patch einzuspielen?**
Weil die Lücke Speicher mit Passwörtern und Tokens nach außen leakt. Diese Zugangsdaten könnten schon vor dem Patch abgeflossen sein. Deshalb muss man zusätzlich die Zugangsdaten tauschen und die Logs rückwirkend prüfen.

## 10. Quellen

- heise online: [php2shell: Critical flaws allow code injection in PHP](https://www.heise.de/en/news/php2shell-Critical-flaws-allow-code-injection-in-PHP-11494897.html)
- Recorded Future: [PHP2Shell Exploitation: Two Critical PHP Flaws Explained](https://www.recordedfuture.com/blog/php2shell-vulnerability-exploitation)
- CSO Online: [PHP2Shell plus Fragnesia](https://www.csoonline.com/article/4242454/php2shell-plus-fragnesia-a-dangerous-new-route-from-web-server-to-root.html)
- Cybernews: [China-linked hackers hit US government network](https://cybernews.com/security/china-linked-hackers-exploit-php2shell-breach-us-government/)
- Hostinger: [php2shell: What it is and why you don't need to panic](https://www.hostinger.com/blog/what-is-php2shell/)
- BSI: [Cybersicherheitswarnung PHP2Shell (CVE-2026-109618/109619)](https://www.bsi.bund.de/SharedDocs/Cybersicherheitswarnungen/DE/2026/2026-297981-1032.pdf)
- calif.io: [PHP2Shell](https://calif.io/php2shell/)
- The Register: [CISA confirms PHP2Shell under attack as researchers clock successful exploits](https://www.theregister.com/cyber-crime/2026/10/27/cisa-confirms-php2shell-under-attack-as-researchers-clock-successful-exploits/5305681)
- SecurityWeek: [China-linked hackers exploit PHP2Shell to root firewalls](https://www.securityweek.com/china-linked-hackers-exploit-php2shell-to-root-firewalls)
- Salzburg24: [Uni Salzburg und Cineplexx vorsorglich offline](https://www.salzburg24.at/news/salzburg/cyber-sicherheitsluecke-bedroht-weltweit-millionen-websites-uni-salzburg-und-cineplexx-vorsorglich-offline-art-342097)
- W3Techs: [Usage statistics of PHP for websites](https://w3techs.com/technologies/details/pl-php)
- Video im Briefing: [PHP2Shell – The PHP Flaw That Took Government Websites Offline, Explained](https://www.youtube.com/watch?v=v5NjeVJiF7Q)

## 11. KI-Unterstützung

> - **Verwendetes Modell:** Claude (Anthropic), Modell: Opus 5.5
> - **Anteil:** Powerpointentwurf- und design + die interaktive Animation wurden mit KI-Unterstützung erstellt.  Recherche, Gliederung und Faktenprüfung sind Eigenleistung.
