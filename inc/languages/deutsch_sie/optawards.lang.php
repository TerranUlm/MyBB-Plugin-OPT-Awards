<?php

// Awards ACP

$l['optawards_title'] = 'OPT Awards';
$l['optawards_task_description'] = 'verarbeite alle ausstehenden und angenommenen Auszeichnungen';

$l['optawards_list_awards'] = 'Auszeichungen';
$l['optawards_list_awards_description'] = 'Auszeichnungen konfigurieren';
$l['optawards_table_awards'] = 'Verfügbare Auszeichnungen';

$l['optawards_add_award'] = 'Neue Auszeichnung';
$l['optawards_add_award_description'] = 'Neue Auszeichnung hinzufügen';
$l['optawards_award_added'] = 'Auszeichnungen hinzugefügt';

$l['optawards_edit_award'] = 'Auszeichnung bearbeiten';
$l['optawards_edit_award_description'] = 'Eine Auszeichnung bearbeiten';
$l['optawards_award_edited'] = 'Auszeichnung bearbeitet';

$l['optawards_delete_award'] = 'Auszeichnung löschen';
$l['optawards_delete_award_description'] = 'Eine Auszeichnung löschen';
$l['optawards_confirm_deleteaward'] = 'Wollen Sie wirklich die Auszeichnung löschen?';

$l['optawards_error_no_award_name'] = 'kein Name für die Auszeichnung angegeben';
$l['optawards_error_no_award_icon'] = 'fehlende URL für das kleine Bild';
$l['optawards_error_no_award_iconlarge'] = 'fehlende URL für das große Bild';

$l['optawards_award_name'] = 'Name';
$l['optawards_award_description'] = 'Beschreibung';
$l['optawards_award_class'] = 'Auszeichnungsklasse';
$l['optawards_award_icon'] = 'Kleines Bild';
$l['optawards_award_iconlarge'] = 'Großes Bild';
$l['optawards_award_usergroups'] = 'Benutzergruppen';
$l['optawards_award_usergroups2'] = 'Verfügbar für';
$l['optawards_award_pm_template'] = 'PM Vorlage<p>Sie können MyBB-Code und diese Platzhalter verwenden:
</p>
Standardvorlage:<ul>
<li>{default} - Verwende die globale PM Vorlage</li>
</ul>
Names:<ul>
<li>{recipient} - Name des Empfängers der Auszeichnung</li>
<li>{requestor} - Name des Antragstellers der Auszeichnung</li>
<li>{processor} - Name des Bearbeiters der Auszeichnung</li>
</ul>
Daten der Auszeichnung:<ul>
<li>{award} - Name der Auszeichnung</li>
<li>{description} - Beschreibung der Auszeichnung</li>
<li>{reason} - Der Grund für die Auszeichnung?</li>
<li>{icon} - großes Bild der Auszeichnung</li>
<li>{date} - Wann wurde die Auszeichnung beantragt?</li>
<li>{awardinfo} - Link zur Informationsseite der Auszeichnung</li>
</ul>';
$l['optawards_award_recipients'] = '# Empfänger';
$l['optawards_award_visibility'] = 'Sichtbarkeit';
$l['optawards_award_displayorder'] = 'Anzeigereihenfolge';

$l['optawards_visibility_everywhere'] = 'überall';
$l['optawards_visibility_usercp'] = 'Nur im UserCP';
$l['optawards_visibility_postbit'] = 'Nur bei Postings';
$l['optawards_visibility_invisible'] = 'nicht sichtbar';


// Award Classes ACP

$l['optawards_list_classes'] = 'Auszeichnungsklassen';
$l['optawards_list_classes_description'] = 'verfügbare Award Classes';
$l['optawards_table_award_classes'] = 'Verfügbare Award Classes';

$l['optawards_award_class_add'] = 'Neue Auszeichnungsklasse';
$l['optawards_add_class_description'] = 'Eine neue Auszeichnungsklasse hinzufügen';
$l['optawards_award_class_added'] = 'Neue Auszeichnungsklasse hinzugefügt';

$l['optawards_edit_class'] = 'Auszeichnungsklasse bearbeiten';
$l['optawards_edit_class_description'] = 'Die Auszeichnungsklasse bearbeiten';
$l['optawards_award_class_edited'] = 'Auszeichnungsklasse bearbeitet';

$l['optawards_delete_class'] = 'Auszeichnungsklasse löschen';
$l['optawards_delete_class_description'] = 'Auszeichnungsklasse löschen';
$l['optawards_confirm_deleteclass'] = 'Wollen Sie diese Auszeichnungsklasse wirklich löschen?';

$l['optawards_error_no_class_name'] = 'Name der Auszeichnungsklasse fehlt';
$l['optawards_error_no_class_singular'] = 'singular Name der Auszeichnungsklasse fehlt';

$l['optawards_award_class_name'] = 'Name';
$l['optawards_award_class_singular'] = 'Singular Name';
$l['optawards_award_class_description'] = 'Beschreibung';
$l['optawards_award_class_icon'] = 'Bild';
$l['optawards_award_class_displayorder'] = 'Anzeigereihenfolge';


// General

$l['optawards_update_order'] = 'Aktualisiere Anzeigereihenfolge';
$l['optawards_delete_not_implemented'] = 'Die Löschfunktion ist nicht implementiert';
$l['optawards_all_user_groups'] = 'Alle Benutzergruppen';
$l['optawards_no_groups'] = 'Keine Benutzergruppe';

// PMs
$l['optawards_pm_noreason'] = 'kein Grund angegeben';
$l['optawards_pm_given_subject'] = '!Ihnen wurde die Auszeichnung "{award}" verliehen!';
$l['optawards_pm_unknown_requestor'] = 'unbekannt';
$l['optawards_pm_mybb_engine'] = 'MyBB Engine';

// process awards task
$l['optawards_error_award_not_accepted'] = 'Die angefragte Auszeichung "{arid}" wurde nicht akzeptiert"';
$l['optawards_error_award_action_unknown'] = 'unbekannte Auszeichnungsaktion "{action}"';
$l['optawards_error_award_pm_failed'] = 'Auszeichnungsanfrage "{action}" fehlgeschlagen';
$l['optawards_error_invalid_recipient'] = 'Die Auszeichnungsanfrage für UID {uid} ist fehlgeschlagen: UID unbekannt';


// misc.php award pages
$l['optawards_page_title'] = 'Auezeichnungen';
$l['optawards_page_list_award'] = 'Auezeichnung';
$l['optawards_page_list_name'] = 'Name';
$l['optawards_page_list_description'] = 'Beschreibung';
$l['optawards_page_list_empty'] = 'keine Auszeichnungen in der Datenbank gefunden';
$l['optawards_page_list_recipients'] = '# Empfänger';
$l['optawards_page_list_show_recipients'] = 'Zeige Empfänger';
$l['optawards_page_list_actions'] = 'Aktionen';
$l['optawards_page_list_action_request'] = 'Auszeichnung beantragen';
$l['optawards_page_list_action_recommend'] = 'Auszeichnung vorschlagen';
$l['optawards_page_list_action_grant'] = '<span class="awardaction_grant">Auszeichnung verleihen</span>';
$l['optawards_page_list_action_none'] = '-';

$l['optawards_username'] = 'Empfänger';
$l['optawards_reason'] = 'Grund für die Auszeichnung';
$l['optawards_page_view_date'] = 'Gewährt am';
$l['optawards_page_view_empty'] = 'keine Empfänger gefunden';
$l['optawards_username_recipient'] = 'Geben Sie den Benutzernamen des Empfängers ein, welcher die Auszeichnung verliehen/aberkannt werden soll.';
$l['optawards_reason_description'] = 'Geben Sie den Grund an, warum die Auszeichnung verliehen werden soll, es kann MyBB-Code verwendet werden:';
$l['optawards_award_data'] = 'Auszeichnung beantragen';
$l['optawards_award_request_added'] = 'Der Antrag wurde aufgenommen';
$l['optawards_requests_empty'] = 'Keine Auszeichnungsanfragen gefunden';
$l['optawards_requests_pending'] = 'Offene Auszeichnungsanfragen';

$l['optawards_show_awards'] = 'Zeige Auszeichnungen';
$l['optawards_view_award'] = 'Zeige Auszeichnung';
$l['optawards_request_award'] = 'Beantrage Auszeichnung';
$l['optawards_recommend_award'] = 'Empfehle Auszeichnung';
$l['optawards_grant_award'] = 'Verleihe Auszeichnung';
$l['optawards_deny_award'] = 'Verweigere Auszeichnung';
$l['optawards_deny_reason_description'] = 'Geben Sie den Grund an, warum die Auszeichung angelehnt wird. Es kann MyBB-Code verwendet werden:';
$l['optawards_process_award_requests'] = 'Verarbeite die Auszeichnungsanfrage';
$l['optawards_unknown_award_processing_action'] = 'unbekannte Auszeichnungsanfrageaktion: ';
$l['optawards_award_processing'] = 'Auszeichungsanfragen bearbeiten';
$l['optawards_award_processed'] = 'Die Auszeichnungsanfrage wurde bearbeitet';
$l['optawards_award_request_denied'] = 'Ihre Auszeichungsanfrage wurde abgelehnt';

$l['optawards_error'] = 'Fehler';
$l['optawards_username_empty'] = 'Der Benutzername ist leer!';
$l['optawards_username_not_found'] = 'Der Benutzername wurde nicht gefunden: ';

$l['optawards_pending_award_requests'] = 'Es gibt {awardrequests} offenen Anfragen für Auszeichnungen, für welche Sie zuständig sind, bitte bearbeiten!';

// display @ postbit, profile etc
$l['optawards_postbit_label'] = 'Neueste Auszeichnungen';
$l['optawards_profile_title'] = '{username}\'s Auszeichungen.';
$l['optawards_profile_viewall'] = '[alle anzeigen]';
$l['optawards_profile_empty'] = 'Dieser Benutzer hat derzeit keine Auszeichnugen.';

// ACP settings
$l['optawards_pmuser_description'] = 'Soll der vergebende Benutzer auch Absender der PM sein?';
$l['optawards_pmuserid_description'] = 'Wer soll als Absender der PM verwendet werden? -1 = MyBB Engine. (Funktioniert nur, wenn die obige Einstellung auf [Nein] steht)';
$l['optawards_pmicon_description'] = 'Wählen Sie das PM Icon. -1 = kein Icon';
$l['optawards_pm_default_description'] = 'Sie können MyBB-Code und diese Platzhalter verwenden:
</p>
Names:<ul>
<li>{recipient} - Name des Empfängers der Auszeichnung</li>
<li>{requestor} - Name des Antragstellers der Auszeichnung</li>
<li>{processor} - Name des Bearbeiters der Auszeichnung</li>
</ul>
Daten der Auszeichnung:<ul>
<li>{award} - Name der Auszeichnung</li>
<li>{description} - Beschreibung der Auszeichnung</li>
<li>{reason} - Der Grund für die Auszeichnung?</li>
<li>{icon} - großes Bild der Auszeichnung</li>
<li>{date} - Wann wurde die Auszeichnung beantragt?</li>
<li>{awardinfo} - Link zur Informationsseite der Auszeichnung</li>
</ul>';
$l['optawards_granters_description'] = 'Eine mit Kommata getrennte Liste von <em>Benutzergruppen</em> welche unabhängig von den Benutzergruppen einer Auszeichnung diese vergeben dürfen.<br>
Standardmäßig dürfen Benutzer Auszeichnungen verleihen, für welche sie als Gruppenleiter eingetragen sind.<br>
Diese Einstellung ist nötig für alle Auszeichnungen, welche für "Alle Benutzergruppen" verfügbar sind, sowie für Gruppen ohne Gruppenleiter.';
$l['optawards_pm_deny_description'] = 'Sie können MyBB-Code und diese Platzhalter verwenden:
</p>
Names:<ul>
<li>{recipient} - Name des Empfängers der Auszeichnung</li>
<li>{requestor} - Name des Antragstellers der Auszeichnung</li>
<li>{processor} - Name des Bearbeiters der Auszeichnung</li>
</ul>
Daten der Auszeichnung:<ul>
<li>{award} - Name der Auszeichnung</li>
<li>{description} - Beschreibung der Auszeichnung</li>
<li>{reason} - Der Grund für die Auszeichnung?</li>
<li>{denyreason} - Der Grund für die Ablehnung der Auszeichnung?</li>
<li>{icon} - großes Bild der Auszeichnung</li>
<li>{date} - Wann wurde die Auszeichnung beantragt?</li>
<li>{awardinfo} - Link zur Informationsseite der Auszeichnung</li>
</ul>';
$l['optawards_profile_description'] = 'Die maximale Anzahl an Auszeichungen, welche im Benutzerprofil angezeigt werden soll. 0 = keine, -1 = unbegrenzt.';
$l['optawards_postbit_description'] = 'Die maximale Anzahl an Auszeichungen, welche in der Threadansicht angezeigt werden soll. 0 = keine, -1 = unbegrenzt.';

// ACP setting defaults
$l['optawards_pmuser_defaults'] = '1';
$l['optawards_pmuserid_defaults'] = '-1';
$l['optawards_pmicon_defaults'] = '1';
$l['optawards_pm_default_defaults'] = "Vorgetreten {recipient}!

Hiermit erhalten Sie die Auszeichnung ''[url={awardinfo}]{award}[/url]''[font=Courier][size=x-small][1][/size][/font] Aufgrund folgender Verdienste:
[i]{reason}

{date}, {requestor}[/i]

Tragen Sie die Auszeichnung mit dem angemessenen Stolz!
[img]{icon}[/img]

Processed by: {processor}

Requested by: {requestor}

Die OPT Maintainer


[1]: {description}";
$l['optawards_granters_defaults'] = '4';
$l['optawards_pm_deny_defaults'] = "Hallo {requestor}!

Die Auszeichnung ''[url={awardinfo}]{award}[/url]'' für {recipient} mit der Begründung
[i]{reason}[/i]
wurde abgelehnt.

Begründung der Ablehnung:
[i]{denyreason}[/i]


Grüße {processor}";
$l['optawards_profile_defaults'] = '-1';
$l['optawards_postbit_defaults'] = '-1';


?>
