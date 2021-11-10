# Cookie-Banner der über die Contao config.yml konfiguriert wird

Diese Modul erzeugt einen Cookie-Banner der rein über die "config.yml" von contao konfiguriert und eingebunden wird.

## Scripte & Styles

Cokiban erfordert Alpine.js (<https://alpinejs.dev>). Im Seitenlayout kann Alpine.js mittels des **js_alpine** bei den JavaScript-Templates aktiviert werden. Außerdem muss das JavaScript-Template **js_cokiban** aktiviert werden, dieses importiert das JavaScript und die Stylesheets für das Banner.

Nutzt man gulp oder webpack können die Dateien über diese Pfade inkludiert werden:

```
__DOCUMENTROOT__/web/bundles/cokiban/alpine.js
__DOCUMENTROOT__/web/bundles/cokiban/cokiban.js
__DOCUMENTROOT__/web/bundles/cokiban/cokiban.min.css
__DOCUMENTROOT__/web/bundles/cokiban/cokiban.scss
```

Für das **ce\_cokiban\_replacement.html** Template verwende ich diesen CSS-Code:

```
.ce_cokiban_replacement {
  position: relative;
  display: flex; justify-content: center; align-items: center;
  background-color: #000;
}
.ce_cokiban_replacement .background_container {
  position: absolute; left: -5px; top: -5px; right: -5px; bottom: -5px;
  pointer-events: none; user-select: none;
  background-size: cover;
  opacity: 0.5;
}
.ce_cokiban_replacement .text_container {
  position: relative;
  max-width: 600px; padding: 1em;
  color: #fff; text-align: center;
}
```

## Konfiguration

Die Konfiguration wird der **config.yml** hinzugefügt. Tipp: Der übersichtshalber kann man eine zusätzliche **cokiban.yml** erstellen und die Konfiguration in diese auslagern. Mittels `imports: - { resource: cokiban.yml }` kann diese importiert werden.

```
cokiban:
  banners:
    alias:                        ##### Alias oder ID des Startpunktes für diese Konfiguration #####
      version: 1                  ##### Durch das erhöhen wird das Banner beim Besucher erzwungen #####
        days: 30                  ##### Tage bis das Banner erneut angezeigt wird, 0 für nie #####
         groups:                  ##### Gruppierungen der Cookie-Auswahl #####
           tracking:              ##### Alias der Gruppe #####
             analytics:           ##### Alias des Cookies, Array der Tempaltes die nicht ohne akzeoptieren ##### des Cookis angezeigt werden sollen
               - 'analytics_google'
           media:
             googlemaps:
               - 'ce_googlemaps_embed'
             youtube:
               - 'ce_youtube'
         pages:                   ##### Alias oder ID der Seiten auf denen der Banner nicht angezeigt werden soll #####
           - 'impressum'
           - 'datenschutz'
    translations:                 ##### Übersetzungen
        de:                       ##### Kürzel der Sprache, als fallback wird die erste angegeben Sprache verwendent #####
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
            button:              ##### Übersetzungen Inserttags #####
                text: 'Zeige Cookiebanner!'
                title: 'Cookiebanner öffnen'
            groups:               ##### Übersetzungen der Gruppen #####
                mandotory:
                    headline: 'Notwendige Cookies'
                    text: 'Diese Cookies ermöglichen grundlegende Funktionen und sind für die einwandfreie Funktion der Website erforderlich.'
                tracking:
                    headline: 'Statistiken'
                    text: 'Wir nutzen Google Analytics um zu verstehen, wie unsere Website genutzt wird und sie entsprechend zu verbessern.'
                media:
                    headline: 'Externe Medien'
                    text: 'Um Ihre Nutzererfahrung zu verbessern, greifen wir auf externe Services und Medien zurück. Ich willige ein, dass verschiedene Daten (insbesondere gekürzte IP-Adresse, Informationen zum Browser und Betriebssystem) an Unternehmen in Ländern ohne angemessenes Datenschutzniveau übermittelt werden. Sie können die Services hier einzeln aktivieren oder deaktivieren:'
            cookies:              ##### Übersetzungen der einzelnen Cookies #####
                analytics:
                    headline: 'Google Analytics'
                    text: '<a href="{{link_url::datenschutz}}#google-analytics" title="Google Analytics Datenschutzerklärung" target="_blank" rel="noopener">Google Analytics Datenschutzerklärung</a>'
                googlemaps:
                    headline: 'Google Maps'
                    text: '<a href="{{link_url::datenschutz}}#google-maps" title="Google Maps Datenschutzerklärung" target="_blank" rel="noopener">Google Maps Datenschutzerklärung</a>'
                youtube:
                    headline: 'YouTube'
                    text: '<a href="{{link_url::datenschutz}}#youtube" title="Youtube Datenschutzerklärung" target="_blank" rel="noopener">Youtube Datenschutzerklärung</a>'
            replacements:         ##### Übersetzungen der Replacement Elemente nach Template, wird kein Replacement angegeben wird keins erzeugt #####
                ce_youtube:
                    button: '{{cokiban::button}}'
                    text: 'Um diesen Ihnalt zu sehen akzeptieren Sie bitte den Cookie für YouTube.'
                    background: 'f74e15a7-7ec3-13eb-8a1d-ef99f142b2bc'          ##### Datei UUID oder ein Pfad
```

## Link zum Cookiebanner / Inserttag

Um einen Link zu erzeugen der das Cookiebanner öffnet gibt es zwei Inserttags:

```
{{cokiban::button}} -> <a href="#" class="button" title="Cookiebanner öffnen">Zeige Cookiebanner!</a>
{{cokiban}}         -> <a href="#" title="Cookiebanner öffnen">Zeige Cookiebanner!</a>
```
und:

```
{{cokiban_open::cssClass}} -> <a href="#" class="cssClass" title="Cookiebanner öffnen">
{{cokiban_open}}           -> <a href="#" title="Cookiebanner öffnen">
{{cokiban_close}}          -> </a>
```
