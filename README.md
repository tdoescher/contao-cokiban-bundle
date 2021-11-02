# Cookie-Banner der über die Contao config.yml konfiguriert wird

Diese Modul erzeugt einen Cookie-Banner, dieser wird komplett über die "config.yml" konfiguriert und eingebunden.

```
cokiban:
  banners:
    alias:							# Alias oder ID des Startpunktes für diese Konfiguration
      version: 1					# Durch das erhöhen wird das Banner beim Besucher erzwungen
        days: 30					# Tage bis das Banner erneut angezeigt wird, 0 für nie
         groups:					# Gruppierungen der Cookie-Auswahl
           tracking:				# Alias der Gruppe
             analytics:				# Alias des Cookies, Array der Tempaltes die nicht ohne akzeoptieren des Cookis angezeigt werden sollen
               - 'analytics_google'
           media:
             googlemaps:
               - 'ce_googlemaps_embed'
             youtube:
               - 'ce_youtube'
         pages:						# Alias oder ID der Seiten auf denen der Banner nicht angezeigt werden soll
           - 'impressum'
           - 'datenschutz'
    translations:					# Übersetzungen
        de:							# Kürzel der Sprache, als fallback wird die erste angegeben Sprache verwendent
            banner:
                main_headline: 'Wir verwenden Cookies'
                main_text: 'Um unsere Website für Sie optimal zu gestalten, verwenden wir Cookies. Weitere Informationen zu Cookies und Ihren Möglichkeiten, Ihre Privacy-Einstellungen anzupassen, finden Sie in unserer {{link_open::datenschutz}}Datenschutzerklärung{{link_close}}.'
                save_button_text: 'Speichern & schließen'
                save_button_title: 'Cookie Einstellungen Speichern'
                accept_button_text: 'Alle akzeptieren'
                accept_button_title: 'Alle Cookies akzeptieren'
                details_headline: 'Cookie Details'
                details_link_text: 'Cookie Details'
                details_link_title: 'Details anzeigen'
                details_back_link_text: 'zurück'
                details_back_link_title: 'zurück zur Übersicht'
                footer_text: '{{link::datenschutz}} {{link::impressum}}'
                switch_true: 'aktiviert'
                switch_false: 'deaktiviert'
            button:
                text: 'Zeige Cookiebanner!'
                title: 'Cookiebanner öffnen'
            groups:					# Übersetzungen der Gruppen
                mandotory:
                    headline: 'Notwendige Cookies'
                    text: 'Diese Cookies ermöglichen grundlegende Funktionen und sind für die einwandfreie Funktion der Website erforderlich.'
                tracking:
                    headline: 'Statistiken'
                    text: 'Wir nutzen Google Analytics um zu verstehen, wie unsere Website genutzt wird und sie entsprechend zu verbessern.'
                media:
                    headline: 'Externe Medien'
                    text: 'Um Ihre Nutzererfahrung zu verbessern, greifen wir auf externe Services und Medien zurück. Ich willige ein, dass verschiedene Daten (insbesondere gekürzte IP-Adresse, Informationen zum Browser und Betriebssystem) an Unternehmen in Ländern ohne angemessenes Datenschutzniveau übermittelt werden. Sie können die Services hier einzeln aktivieren oder deaktivieren:'
            cookies:				# Übersetzungen der Cookies
                analytics:
                    headline: 'Google Analytics'
                    text: '<a href="{{link_url::datenschutz}}#google-analytics" title="Google Analytics Datenschutzerklärung" target="_blank" rel="noopener">Google Analytics Datenschutzerklärung</a>'
                googlemaps:
                    headline: 'Google Maps'
                    text: '<a href="{{link_url::datenschutz}}#google-maps" title="Google Maps Datenschutzerklärung" target="_blank" rel="noopener">Google Maps Datenschutzerklärung</a>'
                youtube:
                    headline: 'YouTube'
                    text: '<a href="{{link_url::datenschutz}}#youtube" title="Youtube Datenschutzerklärung" target="_blank" rel="noopener">Youtube Datenschutzerklärung</a>'
            replacements: 			# Übersetzungen der Replacement Elemente nach Template
                ce_youtube:
                    button: '{{cokiban::button}}'
                    text: 'Um diesen Ihnalt zu sehen akzeptieren Sie bitte den Cookie für YouTube.'
                    background: 'f94e15a7-7dc3-11eb-8a2d-ef93f142c2bc'
```
