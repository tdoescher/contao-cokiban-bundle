# Cookie-Banner der über die Contao config.yml konfiguriert wird

Diese Modul erzeugt einen Cookie-Banner der rein über die "config.yml" von contao konfiguriert und eingebunden wird.

## Twig-Support

Um den Twig-Support zu gewährleisten gibt es in diesem Bundle eine angepaste Version des **content\_element/\_base.html.twig** Tempaltes, dies wird automatisch geladen.

```
{% extends "@Contao/content_element/_base.html.twig" %}

{% block wrapper %}
  {{ cokiban_open() }}
    {{ parent() }}
  {{ cokiban_close() }}
{% endblock %}
```

Um Templates hinter den Cookie-Banner zu verstecken die das **content\_element/\_base.html.twig** Template nicht verwenden kann der Inhalte des Templates manuell mittels ``{{ cokiban_open() }}`` und ``{{ cokiban_close() }}`` umschlossen werden.

## Scripte & Styles

Cokiban erfordert Alpine.js (<https://alpinejs.dev>). Um dies zu integrieren, kann man zum Beispiel das Bundle <http://github.com/tdoescher/contao-alpine-bundle> verwenden. Außerdem muss das JavaScript-Template **js_cokiban** aktiviert werden, dieses importiert das JavaScript und die Stylesheets für das Banner.

Nutzt man gulp oder webpack können die Dateien über diese Pfade inkludiert werden:

```
__DOCUMENTROOT__/web/bundles/cokiban/cokiban.min.js
__DOCUMENTROOT__/web/bundles/cokiban/cokiban.min.css
__DOCUMENTROOT__/web/bundles/cokiban/cokiban.js
__DOCUMENTROOT__/web/bundles/cokiban/cokiban.scss
```

Für das **content\_element/cokiban\_replacement.html.twig** Template verwende ich diesen CSS-Code:

```
.content-cokiban-replacement__replacement {
  position: relative;
  display: flex;
  justify-content: center;
  align-items: center;
  background-color: $black;
}
.content-cokiban-replacement__background {
  position: absolute;
  inset: -1%;
  pointer-events: none;
  user-select: none;
  opacity: 0.5;
  background-size: cover;
}
.content-cokiban-replacement__text {
  position: relative;
  max-width: 600px;
  padding: 1em;
  color: #fff;
  text-align: center;
}
```

## Konfiguration

Die Konfiguration wird der **config.yml** hinzugefügt. Tipp: Der übersichtshalber kann man eine zusätzliche **cokiban.yml** erstellen und die Konfiguration in diese auslagern. Mittels `imports: - { resource: cokiban.yml }` kann diese importiert werden.

```
cokiban:
  disable_token: 'custom-token'  ##### Optional - Funktion zum deaktiviren des Plugins mittels GET-Parameter(https://website.com/?custom-token)
  hide_token: 'custom-token'     ##### Optional - Funktion zum ausblenden des Cokiban mittels GET-Parameter(https://website.com/?custom-token)
  banners:
    alias:                       ##### Alias oder ID des Startpunktes für diese Konfiguration, alternativ kann mal "global" angeben für einen globalen Banner
      id: 'main'                 ##### Optional - Manuel setzen einer id für das Cookiebanner, ohne angabe wird die RootPageId verwendent
      version: 1                 ##### Durch das erhöhen wird das Banner beim Besucher erzwungen
      days: 30                   ##### Tage bis das Banner erneut angezeigt wird, 0 für nie
      google_consent_mode: false ##### Wenn true, wird die Funktion gtag('consent','update',{...}) für den Google Consent Mode v2 ausgeführt
      groups:                    ##### Gruppierungen der Cookie-Auswahl
        tracking:                ##### Alias der Gruppe
          analytics:             ##### Alias des Cookies, Array der Tempaltes die nicht ohne akzeptieren des Cookis angezeigt werden sollen
            - 'analytics_google'
        media:
          googlemaps:
            - 'content_element/googlemaps_embed'
            - 'content_element/googlemaps_html'
          youtube:
            - 'content_element/youtube'
      pages:                       ##### Alias oder ID der Seiten auf denen der Banner nicht angezeigt werden soll
        - 'impressum'
        - 'datenschutz'
      tempalte: 'cokiban'          ##### Optional - Angabe des zu verwendenden Templates, ohne Angabe wird "cokiban" verwendet
    translations:                  ##### Übersetzungen
        de:                        ##### Kürzel der Sprache, als fallback wird die erste angegeben Sprache verwendent 
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
            button:                ##### Übersetzungen Inserttags
                text: 'Zeige Cookiebanner!'
                title: 'Cookiebanner öffnen'
            groups:                ##### Übersetzungen der Gruppen
                mandatory:
                    headline: 'Notwendige Cookies'
                    text: 'Diese Cookies ermöglichen grundlegende Funktionen und sind für die einwandfreie Funktion der Website erforderlich.'
                tracking:
                    headline: 'Statistiken'
                    text: 'Wir nutzen Google Analytics um zu verstehen, wie unsere Website genutzt wird und sie entsprechend zu verbessern.'
                media:
                    headline: 'Externe Medien'
                    text: 'Um Ihre Nutzererfahrung zu verbessern, greifen wir auf externe Services und Medien zurück. Ich willige ein, dass verschiedene Daten (insbesondere gekürzte IP-Adresse, Informationen zum Browser und Betriebssystem) an Unternehmen in Ländern ohne angemessenes Datenschutzniveau übermittelt werden. Sie können die Services hier einzeln aktivieren oder deaktivieren:'
            cookies:                ##### Übersetzungen der einzelnen Cookies
                analytics:
                    headline: 'Google Analytics'
                    text: '<a href="{{link_url::datenschutz}}#google-analytics" title="Google Analytics Datenschutzerklärung" target="_blank" rel="noopener">Google Analytics Datenschutzerklärung</a>'
                googlemaps:
                    headline: 'Google Maps'
                    text: '<a href="{{link_url::datenschutz}}#google-maps" title="Google Maps Datenschutzerklärung" target="_blank" rel="noopener">Google Maps Datenschutzerklärung</a>'
                youtube:
                    headline: 'YouTube'
                    text: '<a href="{{link_url::datenschutz}}#youtube" title="Youtube Datenschutzerklärung" target="_blank" rel="noopener">Youtube Datenschutzerklärung</a>'
            replacements:                                                       ##### Übersetzungen der Replacement Elemente nach Template, wird kein Replacement angegeben wird keins erzeugt
                content_element/googlemaps_embed:
                    button: '{{cokiban::button}}'
                    text: 'Um diesen Ihnalt zu sehen akzeptieren Sie bitte den Cookie für GoogleMaps.'
                    background: 'files/map.webp'                                 ##### UUID oder Pfad einer Bild-Datei
                content_element/youtube:
                    button: '{{cokiban::button}}'
                    text: 'Um diesen Ihnalt zu sehen akzeptieren Sie bitte den Cookie für YouTube.'
                    background: 'f74e15a7-7ec3-13eb-8a1d-ef99f142b2bc'          ##### UUID oder Pfad einer Bild-Datei
                    tempalte: 'content_element/cokiban_replacement_youtube'                          ##### Optional - Angabe des zu verwendenden Templates, ohne Angabe "content_element/cokiban_replacement" verwendet

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
