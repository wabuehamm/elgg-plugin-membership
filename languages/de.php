<?php

/**
 * German localization
 *
 * Check out http://learn.elgg.org/en/stable/guides/i18n.html for details
 */


return [

    'membership:title' => 'Mitgliederverwaltung',
    'membership:overview:tabs:reports' => 'Berichte',
    'membership:overview:tabs:seasons' => 'Saisons',

    'membership:seasons' => 'Saisons',
    'membership:season:add' => 'Saison hinzufügen',
    'membership:season:edit' => 'Saison bearbeiten',

    'membership:season:title' => 'Saison %s',

    'membership:no_seasons' => 'Keine Saisons wurden hinzugefügt',

    'membership:season:form:year:label' => 'Jahr',
    'membership:season:form:year:help' => 'Gib das Jahr der Saison ein',
    'membership:season:form:lockdate:label' => 'Sperrdatum',
    'membership:season:form:lockdate:help' => 'Das Datum, nach dem die Teilnahme gesperrt wird und nicht teilnehmende Mitglieder gesperrt werden',
    'membership:season:form:enddate:label' => 'Enddatum',
    'membership:season:form:enddate:help' => 'Datum, an dem die Saison endet und alle Mitglieder entsperrt werden',
    'membership:season:form:participationtypes:label' => 'Teilnahmebereiche',
    'membership:season:form:participationtypes:help' => 'Bereiche in den Gewerken in dieser Saison (ein Bereich pro Zeile in der Form Schlüssel:Bezeichnung)',

    'membership:productions' => 'Produktionen',
    'membership:productions:all' => 'Alle Produktionen',
    'membership:season:no_productions' => 'Es wurden noch keine Produktionen dieser Saison hinzugefügt',
    'membership:season:production:add' => 'Produktion hinzufügen',

    'membership:production:form:label' => 'Titel',
    'membership:production:form:help' => 'Titel der Produktion',
    'membership:production:form:participationtypes:label' => 'Teilnahmebereiche',
    'membership:production:form:participationtypes:help' => 'Teilnahmebereiche für diese Produktion (ein Bereich pro Zeile in der Form Schlüssel:Bezeichnung)',

    'membership:departments' => 'Gewerke',
    'membership:departments:title' => 'Gewerke',

    'membership:settings:departments:participations:label' => 'Teilnahmebereiche (Gewerke)',
    'membership:settings:departments:participations:help' => 'Standard-Teilnahmebereiche in den Gewerken einer neuen Saison (ein Bereich pro Zeile in der Form Schlüssel:Bezeichnung)',
    'membership:settings:production:participations:label' => 'Teilnahmebereiche (Produktion)',
    'membership:settings:production:participations:help' => 'Standard-Teilnahmebereiche für eine Produktion in einer Saison (ein Bereich pro Zeile in der Form Schlüssel:Bezeichnung)',
    'membership:settings:acl:label' => 'Zugriff zu Teilnahmeberichten',
    'membership:settings:acl:help' => 'Regeln für den Zugriff auf die Teilnahmeberichte (vgl. ReportGatekeeper-Quelltext)',

    'membership:errors:wrongParticipationTypes' => 'Die angegebene Liste von Teilnahmebereichen ist nicht gültig. Bitte überprüfe, dass diese in der Form Schlüssel:Bezeichnung eingegeben worden ist',

    'membership:participations:button' => 'Teilnahmen',
    'membership:participations:title' => 'Teilnahmen',

    'membership:participations:departments' => 'Teilnahmen in Gewerken',
    'membership:participations:productions' => 'Teilnahmen in Produktionen',
    'membership:participations:none' => 'Keine Teilnahmen',

    'membership:participations:participate' => 'Teilnehmen',

    'membership:participations:season:title' => 'In der Saision %s teilnehmen',

    'membership:participations:saved' => 'Teilnahmen gespeichert',

    'membership:reports:profileFields:name' => 'Name',
    'membership:reports:profileFields:displayname' => 'Anzeigename',
    'membership:reports:profileFields:givenname' => 'Vorname',
    'membership:reports:profileFields:username' => 'Benutzername',
    'membership:reports:profileFields:email' => 'E-Mail',

    'membership:reports:export:csv' => 'Exportieren',

    'membership:reports:members' => 'Mitglieder',
    'membership:reports:title' => 'Bericht',
    'membership:reports:gatekeeper:error' => 'Du hast keinen Zugriff auf diesen Bericht',
    'membership:reports:gatekeeper:errorredirect' => 'Du hast keinen Zugriff auf die Mitgliederberichte. Schau Dir doch vielleicht Deine <a href="%s">Teilnahmen</a> stattdessen an.',


    'membership:reports:completeseason' => 'Teilnahmen für die gesamte Saison anzeigen',

    'membership:reports:profileFields:street' => 'Straße',
    'membership:reports:profileFields:zip' => 'PLZ',
    'membership:reports:profileFields:city' => 'Stadt',
    'membership:reports:profileFields:telephone' => 'Telefon',
    'membership:reports:profileFields:mobile' => 'Mobil',
    'membership:reports:profileFields:birthday' => 'Geburtstag',
    'membership:reports:profileFields:anniversary' => 'Hochzeitstag',
    'membership:reports:profileFields:years' => 'Jahre',

    'membership:reports:common' => 'Allgemein',
    'membership:reports:jubilees' => 'Jubiläen',
    'membership:reports:anniversary' => 'Hochzeitstag',
    'membership:reports:young' => 'Junge Mitglieder',
    'membership:reports:adult' => 'Erwachsene Mitglieder',
    'membership:reports:birthdayjubilees' => 'Geburtstagsjubiläen',

    'membership:reports:age' => 'Alter',

    'membership:simpleReport:noData' => 'Keine Einträge gefunden',

    'membership:season:batch' => 'Masseneintrag',
    'membership:season:batch:title' => 'Masseneintrag für Saison %s',
    'membership:season:batch:member' => 'Mitglied',
    'membership:season:batch:save' => 'Speichern',
    'membership:season:batch:add' => 'Zeile hinzufügen',

    'membership:profile:member_since:label' => 'Mitglied seit',
    'membership:profile:away_years:label' => 'Aussetzjahre',
    'membership:profile:active_years:label' => 'Aktive Jahre',
    'membership:profile:title' => 'Mitgliedschaft',

    'membership:membercard' => 'Mitgliederausweis',
    'membership:membercard:notfound' => 'Es wurde kein Mitglied mti dem Benutzernamen %s gefunden',
    'membership:membercard:noseason' => 'Keine Saison gefunden',
    'membership:membercard:participation:invalid' => 'Dieses Mitglied nimmt derzeit nicht an einer Saison teil',
    'membership:membercard:participation:valid' => 'Die Mitgliedschaft ist gültig',
    'membership:membercard:forbidden' => 'Der Aufruf eines fremden Mitgliederausweises ist nicht erlaubt',

    'membership:anniversaryReport:noAnniversaries' => 'Keine Jubiläen gefunden',
    'membership:birthdayjubilees:noJubilees' => 'Keine Jubiläen gefunden',
    'membership:jubileesReport:noJubilees' => 'Keine Jubliare gefunden',

    'membership:reports:insurance:title' => 'Versicherungsbericht',
    'membership:reports:insurance:date' => 'Stichtagmeldung per %s',
    'membership:reports:insurance:year' => 'Jahr',
    'membership:reports:insurance:common' => 'Allgemein',
    'membership:reports:insurance:teens' => 'Jugendliche',
    'membership:reports:insurance:board' => 'Vorstandsmitglieder',
    'membership:reports:insurance:total' => 'Gesamt',
    'membership:reports:insurance' => 'Versicherung',
    'membership:settings:insuranceTheatre:label' => 'Theater',
    'membership:settings:insuranceTheatre:help' => 'Name des Theaters',
    'membership:settings:insuranceAddress:label' => 'Versicherung',
    'membership:settings:insuranceAddress:help' => 'Adresse der Versicherung',
    'membership:settings:insuranceMember:label' => 'Mitgliedschaft',
    'membership:settings:insuranceMember:help' => 'Mitgliedschaftsdaten der Versicherung',

];
