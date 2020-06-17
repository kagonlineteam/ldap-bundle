# Mitwirken an der API

In diesem Dokument werden die Richtlinien und Konventionen zur Kollaboration an diesem Repository festgelegt. 
**Dieses Dokument sollte unbedingt gelesen werden, bevor man an diesem Repository mitwirken möchte.**

## Inhaltsverzeichnis
- [In diesem Dokument erhaltene Namensgebungen](#in-diesem-dokument-erhaltene-namensgebungen) 
- [Welche Standard Branches hat das Projekt und wofür werden diese genutzt?](#welche-standard-branches-hat-das-projekt-und-wofür-werden-diese-genutzt?)
- [Programmierkonventionen](#programmierkonventionen)  
- [Git-Konventionen](#git-konventionen) 
- [Wie frage ich ein neues Feature an?](#wie-frage-ich-ein-neues-feature-an?) 
  - [Template für Feature-Issues](#template-für-feature-issues) 
- [Wie implementiere ich ein neues Feature?](#wie-implementiere-ich-ein-neues-feature?)
- [Wie melde ich einen Bug?](#wie-melde-ich-einen-bug?) 
  - [Template für Bug-Issues](#template-für-bug-issues) 
- [Wie implementiere ich einen Bugfix?](#wie-implementiere-ich-einen-bugfix?)
- ["Präziser Commit" Definition](#"präziser-commit"-definition)

## In diesem Dokument erhaltene Namensgebungen
- **"branch"**, engl. für Zweig. Beschreibt das Abzweigen eines bestimmten Standes des Projekts. Diese Abzweigung kann dann beliebig bearbeitet werden, ohne den ursprünglichen Stand zu ändern.
- **"merge"**, engl. für verschmelzen. Beim "merge" wird ein Branch A in einen Branch B "gemerged". Danach enthält der Branch B den ursprünglichen Stand von B plus alle Änderungen die in A gemacht wurden.
- **"pull request"**, kurz "PR". Bei einem PR wird eine Anforderung gestellt, einen gewissen Branch A in einen anderen Branch B zu "mergen". Dabei muss der PR von einem sogenannten "reviewer" kontrolliert werden. Mit der Zustimmung des "rewiewers" kann dann der Branch A in den Branch B "gemerged" werden. Dabei können auch mehrere "reviewer" angegeben werden.
- **"reviewer"**, engl. für Gutachter. Ein oder mehrere "reviewer" sind Personen mit der Berechtigung, Änderungen am Code zu bewerten. Diese müssen beim Erstellen eines "PRs" immer angegeben werden. Übliche Aufgaben des "rewievers" sind auf die Funktionalität des Codes zu achten, und zu kontrollieren ob vereinbarte Regeln beim Programmieren eingehalten wurden.
- **"deployment"**, engl. für Einsatz. Beim "deployment" wird ein Code/Programm auf einem Server eingesetzt. Dabei kann sich die Umgebung des Einsatzes erstmal unterscheiden. Dazu im Folgenden mehr.
- **"staging"**, engl. für Entwicklung. Wird auf "staging" "deployed", so redet man davon, dass der Code auf einer Entwicklungsumgebung eingesetzt wird. Das Programm wird dann in dieser Umgebung getestet, jedoch nie in "production" eingesetzt.
- **"production"**, engl. für Produktion (eher als "produktiv" gemeint). Wird ein Code von "staging" auf "production" "deployed", bedeutet das, dass der Code in der "staging" Umgebung alle Voraussetzungen und Tests erfüllt hat und bereit ist, produktiv eingesetzt zu werden. Code in "production" wird letztendlich von Endnutzern genutzt und darf aus diesem Grund keine Fehler ("bugs") enthalten und muss immer einsatzbereit sein.
- **"bug"**, engl. für Fliege (historisch für Fehler in Computern gemeint). Wenn man von einem "bug" spricht, dann spricht man von einem Fehler im Code. Diesen gilt es zu beheben damit die Qualität des Codes besser wird und dieser in "production" eingesetzt werden kann.


## Welche Standard Branches hat das Projekt und wofür werden diese genutzt?
Dieses Projekt hat zwei Branches. Einen "master" Branch und einen "stable" Branch.

Der "master" Branch spiegelt den aktuellen Entwicklungsstand des Projekts wieder. Hierraus werden "feature" und "bugfix" Branches erstellt und nach Bearbeitung wieder in den "master" "gemerged". Dazu aber in den jeweiligen Unterpunkten mehr.
Der "master" Branch wird in einer "staging"-Umgebung deployed wodurch dieser im Einsatz getestet werden kann.
Wenn die Tests erfolgreich sind wird ein "pull request" erstellt zum "merge" aus dem "master" in den "stable" Branch. 

Der "stable" Branch erfüllt die Anforderung, immer einsatzbereit zu sein. Das bedeutet dass man das Programm aus dem "stable" Branch immer einsetzen kann und es funktionieren wird. Aus diesem Grund wird auch zunächst auf dem "master" entwickelt und getestet, bevor die erfolgreichen Änderungen in den "stable" Branch "gemerged" werden.

## Programmierkonventionen
Sämtliche neuen Code-Beiträge müssen den offiziellen Standards des Symfony Frameworks folgen: https://symfony.com/doc/current/contributing/code/standards.html
Innerhalb des Codes werden so weit es möglich ist Klassen, Attribute, Methode, etc. englisch benannt.  In Ausnahmefällen können deutsche Begriffe verwendet werden, wenn diese zur Verständlichkeit des Codes beitragen. Auch Kommentare im Code werden auf Englisch verfasst.

## Git-Konventionen
- **Sprache**: Sämtliche Titel, Beschreibungen und Kommentare von Issues und PRs erfolgen auf Deutsch. Die Commits der nach den PRs werden allerdings auf Englisch formuliert. (Warum nicht einfach auch auf Deutsch?)
- **Merge**: Der "merge" wird mit der Option --squash ausgeführt und danach comittet. Dies bedeutet alle  Veränderungen werden in einen Commit gepackt, der auf die PR  verweist.

## Wie frage ich ein neues Feature an?
Um ein neues Feauture anzufragen wird ein Issue erstellt, dabei ist das GitHub Template für Features auszuwählen. Das Template enthält eine allgemeine Formatierung für die wichtigsten Informationen, die alle beantwortet werden müssen.

### Template für Feature-Issues
**Titel**: Kurze Zusammenfassung des Features
**Kommentar**:
- Detaillierte Beschreibung des Features
- Use-Case: Wie kann das Feature verwendet werden?
- Für wen ist das Feature und warum?
- Vorher zu erfüllende Voraussetzungen für das Feature
- Links/Referenzen

**Assignees**: Ggf. die Person die dieses Feature bearbeiten soll. Man kann sich auch selber angeben.
**Projects**: "Aufgabenübersicht" hinzufügen, GitHub sollte das Issue automatisch in die Todo  Spalte tun.
**Labels**: 
- Das "feature" Label setzen 
- Eine Priorität setzen, "prio 1" hat höchste Priorität und "prio 3" die niedrigste

**Milestone**: Ggf. das Feature einem Milestone hinzufügen, falls dieses Feature in einem nahen Release beinhalten sein soll

## Wie implementiere ich ein neues Feature?
Wenn du ein neues Feature implementieren willst beachte bitte folgende Schritte:

- Suche das zugehörige GitHub Issue und weise (Assignee) es dir selber zu.
- Suche die Issue Nummer heraus. Diese findest du in dem URL Bereich deines Browsers wenn du das Issue aufgerufen hast (z.B. https://github.com/kagonlineteam/sym-api/issues/43).
- Erstelle eine neue Branch ausgehend vom "master" Branch mit dem Namen "feature-ISSUE_NUMMER". Dabei ist es egal, wo der Branch liegt (eigener Nutzer oder kagonlineteam).

In diesem neuen Branch solltst du an der Implementierung des neuen Features arbeiten: 
- Die einzelnen Commits sollten eine englische Commit-Message haben. 
- Die Commits sollten zudem kleinschrittig gemacht werden (ggf. mit Emojis) und keine ausführliche Nachricht haben. 
- Der Branch sollte vor dem PR nur veröffentlicht werden, wenn du nicht alleine daran arbeitest.

Wenn du fertig mit der Implementierung bist, erstelle mit den folgenden Schritten einen Pull Request um deine Implementierung dem "master" Branch hinzuzufügen.


Erstelle eine Pull request mit folgenden Eigenschaften:
- **Base**: "master", Compare "feature-ISSUE_NUMMER"
- **Titel**: "feature-ISSUE_NUMMER" implementieren
- **Kommentar**: Kurze Beschreibung des Features das hinzugefügt werden soll
- **Reviewers**: Personen die deine Implementation kontrollieren sollen, bevor sie in den "master" Branch gemerged wird
- **Assignees**: Du selber, ggf. andere Personen die an dem Feature arbeiten.
- **Labels**: Entsprechende Labels die es erleichtern, die Pull Request einzuordnen.
  - Eine Priorität setzen, "prio 1" hat höchste Priorität und "prio 3" die niedrigste
- **Projects**: Aufgabenübersicht, Spalte "In Progress"
- **Milestones**: Den entsprechenden Milestone, zu dem diese Implementierung gehören soll
- **Linked Issues**: Das zugehörige Issue zum Feature

Der von dir angegebene Reviewer wird sich deinen Code nun angucken und ggf. Verbesserungsvorschläge in der Pull Request äußern. Nachdem du die Zustimmung des Reviewers hast, kann der Hauptverantwortliche der Repository die Änderungen in einem ausführlichen, präzisen Commit mergen (für die API: indecim).


## Wie melde ich einen Bug?
Um einen Bug zu melden wird ein Issue mit dem Template für Issues erstellt. Das Template enthält eine allgemeine Formatierung für die wichtigsten Informationen, die alle beantwortet werden müssen.

### Template für Bug-Issues
**Titel**: Kurze Zusammenfassung des Bugs
**Kommentar**: 
- Was funktioniert nicht?
- Wie sollte es eigentlich funktionieren?
- Schritte um den Fehler zu reproduzieren
- Fehlermeldung (ggf. inkl. fehlerhaftem Code)
- Erste Lösungsvorschläge
- Was sonst noch getan werden muss um diesen Bug zu lösen (z.B. Änderungen außerhalb des Codes)

**Assignees**: Ggf. die Person die diesen Bug bearbeiten soll. Man kann sich auch selber angeben.
**Projects**: "Aufgabenübersicht" hinzufügen, GitHub sollte das Issue automatisch in der Todo Spalte hinzufügen.
**Labels**: 
- Das "bug" Label setzen 
- Eine Priorität setzen, "prio 1" hat höchste Priorität und "prio 3" die niedrigste
- Falls der Bug in "production" auftritt, bitte zusätzlich das "production" Label setzen

**Milestone**: Den Bug einem der künftigen Releases hinzufügen (sicherlich abhängig von der Größe und Wichtigkeit des Bugs)


## Wie implementiere ich einen Bugfix?
Wenn du einen Bugfix implementieren willst beachte bitte folgende Schritte:

- Suche das zugehörige GitHub Issue und weise es dir selber zu.
- Suche die Issue Nummer heraus. Diese findest du in dem URL Bereich deines Browsers wenn du das Issue aufgerufen hast (z.B. https://github.com/kagonlineteam/sym-api/issues/43)
- Neuen Branch basierend auf dem Bug erstellen
- Falls das zugehörige Issue das Label "production" in kombination mit "bug" hat, erstelle eine neue Branch ausgehend vom "stable" Branch mit dem Namen "hotfix-ISSUE_NUMMER"
- Falls das zugehörige Issue das Label "production" nicht hat, erstelle eine neue Branch ausgehend vom "master" Branch mit dem Namen "bugfix-ISSUE_NUMMER"

In diesem neuen Branch sollst du an der Lösung des Bugs arbeiten:

- Die einzelnen Commits sollten eine englische Commit-Message haben. 
- Die Commits sollten zudem kleinschrittig gemacht werden (ggf. mit Emojis) und keine ausführliche Nachricht haben.

Wenn du fertig mit der Implementierung bist, erstelle mit den folgenden Schritten einen Pull Request um deine Implementierung dem "master" Branch hinzuzufügen.


Erstelle eine Pull request mit folgenden Eigenschaften:
- **Base**: "master", Compare "bugfix-ISSUE_NUMMER"
  - Falls der Bug in "production" aufgetreten ist, muss der Bugfix sowohl in den "master" als auch "stable" Branch gemerged werden. Erstelle dazu bitte einen zweiten Pull Request mit der Base: "stable" und, zusätzlich zu anderen Labels, dem Label: "production"
- **Titel**: "Fix bug ISSUE_NUMMER"
- **Kommentar**: Kurze Beschreibung des Lösungsansatzes der hinzugefügt werden soll.
- **Reviewers**: Personen die deine Implementation kontrollieren sollen, bevor sie in den "master" Branch gemerged wird
- **Assignees**: Du selber, ggf. andere Personen die an dem Bug arbeiten.
- **Labels**: Entsprechende Labels die es erleichtern, die Pull Request einzuordnen.
  - Eine Priorität setzen, "prio 1" hat höchste Priorität und "prio 3" die niedrigste
- **Projects**: Aufgabenübersicht, Spalte "In Progress"
- **Milestones**: Den entsprechenden Milestone, zu dem dieser Bugfix gehören soll
- **Linked Issues**: Das zugehörige Issue zum Bug


Der von dir angegebene Reviewer wird sich deinen Code nun angucken und ggf. Verbesserungsvorschläge in der Pull Request äußern. Nachdem du die Zustimmung des Reviewers hast, kann der Hauptverantwortliche der Repository die Änderungen in einem ausführlichen, präzisen Commit mergen (für die API: indecim).

### "Präziser Commit" Definition
- Überschrift: 
    - Für einen Bugfix: "Fix #ISSUE: " + Der Titel des Issues auf Englisch + "(#PULL_REQUEST)"
    - Für ein Feature: "Resolve #ISSUE " + Der Titel des Issues auf Englisch + "(#PULL_REQUEST)"
- Ausführlicher Teil des Commits fasst zuerst die Implementation zusammen (auf Englisch) und listet Änderungen auf, die die ganze API betreffen. Wichtig wären hierbei Änderungen an der Datenbank oder auch Änderungen an den Schnittstellen (Console, Web)
- Danach werden die Commits gelistet (ohne Emojis) nach dem Schema:
    Commits:
    * Commit-Message 1
    * Commit-Message 2
    * ...
