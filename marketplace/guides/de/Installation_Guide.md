---
author: axytos GmbH
title: "Installationsanleitung"
subtitle: "axytos Kauf auf Rechnung, Shopware5"
header-right: axytos Kauf auf Rechnung, Shopware5
lang: "de"
titlepage: true
titlepage-rule-height: 2
toc-own-page: true
linkcolor: blue
---

## Installationsanleitung

Das Plugin stellt die Bezahlmethode __Kauf Auf Rechnung__ für den Einkauf in Ihrem Shopware Shop bereit.

Einkäufe mit dieser Bezahlmethode werden von axytos ggf. bis zum Forderungsmanagement übernommen.

Alle relevanten Änderungen an Bestellungen mit dieser Bezahlmethode werden automatisch an axytos übermittelt.

Anpassungen über die Installation hinaus, z.B. von Rechnungs- und E-Mail-Templates, sind nicht notwendig.

Weitere Informationen erhalten Sie unter [https://www.axytos.com/](https://www.axytos.com/).


## Voraussetzungen

1. Vertragsbeziehung mit [https://www.axytos.com/](https://www.axytos.com/).

2. Verbindungsdaten, um das Plugin mit [https://portal.axytos.com/](https://portal.axytos.com/) zu verbinden.

3. Unterstützung von [Cronjobs](https://docs.shopware.com/de/shopware-5-de/einstellungen/system-cronjobs#wie-starte-ich-einen-cronjob)

Um dieses Plugin nutzen zu können benötigen Sie zunächst eine Vertragsbeziehung mit [https://www.axytos.com/](https://www.axytos.com/).

Während des Onboarding erhalten Sie die notwendigen Verbindungsdaten, um das Plugin mit [https://portal.axytos.com/](https://portal.axytos.com/) zu verbinden.


## Plugin-Installation über den Plugin Manager

1. Plugin im Plugin Manager innerhalb Ihrer Shopware Distribution kostenlos kaufen und hinzufügen.

2. Zur Übersichtsseite des Plugins wechseln. Das Plugin __axytos Kauf auf Rechnung__ ist unter _Einstellungen > Plugin Manager > Installiert_ aufgeführt.

3. __Plugin installieren__ ausführen.

4. __Aktivieren__ ausführen

Sie können das Plugin kostenlos über den Plugin Manager innerhalb Ihrer Shopware Distribution kaufen und hinzufügen.

Wenn Sie es hinzugefügt haben, wird es im _Plugin Manager_ aufgeführt.

Führen Sie __Installieren__ aus. (Das grüne Plus-Icon klicken)

Im neu geöffneten Fenster klicken Sie auf __Aktivieren__.

Das Plugin ist jetzt installiert und aktiviert und kann konfiguriert werden.

Um das Plugin nutzen zu können, benötigen Sie valide Verbindungsdaten zu [https://portal.axytos.com/](https://portal.axytos.com/) (siehe Voraussetzungen).


## Plugin- und Shop-Konfiguration in Shopware

1. Zur Administration Ihrer Shopware Distribution wechseln. Das Plugin _axytos Kauf auf Rechnung_ ist unter _Einstellungen > Plugin Manager > Installiert_ aufgeführt.

2. Stift-Icon von __axytos Kauf auf Rechnung__ drücken um die Konfiguration zu öffnen.

3. __API Host__ eintragen. Entweder [https://api.axytos.com/](https://api.axytos.com/) oder [https://api-sandbox.axytos.com/](https://api-sandbox.axytos.com/), die korrekten Werte werden Ihnen von axytos während des Onboarding mitgeteilt (siehe Voraussetzungen)

4. __API Key__ eintragen. Der korrekte Wert wird Ihnen während des Onboarding von axytos mitgeteilt (siehe Voraussetzungen).

5. __Client Secret__ eintragen. Der korrekte Wert wird Ihnen ebenfalls im Onboarding mitgeteilt (siehe Voraussetzungen).

6. __Speichern__ ausführen.

7. __API-Verbindung testen__ ausführen.

8. Falls der Verbindungstest fehlschlägt, leeren Sie alle Caches und versuchen Sie es erneut. Falls das Problem nicht gelöst ist, wenden Sie sich bitte an Ihren Ansprechpartner bei axytos.

9. Wenn der Verbindungstest erfolgreich beendet, sind Sie hier fertig.

10. Die Bezahlmethode einer Versandart unter _Einstellungen > Versandkosten > (Ausgewählte Versandart) > Zahlart Auswahl_ zuordnen.

Zur Konfiguration müssen Sie valide Verbindungsdaten zu [https://portal.axytos.com/](https://portal.axytos.com/) (siehe Voraussetzungen), d.h. __API Host__, __API Key__ und __Client Secret__ für das Plugin speichern.

Führen Sie danach __API-Verbindung testen__ aus.

Falls der Verbindungstest fehlschlägt, wenden Sie sich bitte an Ihren Ansprechpartner bei axytos, falls nicht sind Sie hier fertig.

## Kauf auf Rechnung kann nicht für Einkäufe ausgewählt werden?

Überprüfen Sie folgende Punkte:

1. Das Plugin __axytos Kauf auf Rechnung__ ist installiert.

2. Das Plugin __axytos Kauf auf Rechnung__ ist aktiviert.

3. Das Plugin __axytos Kauf auf Rechnung__ ist mit korrekten Verbindungsdaten (__API Host__ & __API Key__) konfiguriert.

4. Das Plugin __axytos Kauf auf Rechnung__ ist mit mindestens einer Versandart zugeordnet.

5. Alle Caches sind geleert.

Überprüfen Sie die Korrektheit der Verbindungsdaten mit __API-Verbindung Testen__.

Fehlerhafte Verbindungsdaten führen dazu, dass das Plugin nicht für Einkäufe ausgewählt werden kann.
