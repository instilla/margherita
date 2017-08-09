# margherita
Crawler con finalità SEO per comparare le url di un nuovo e di un vecchio sito web a fini di migrazione.

la versione 0.4 del software:
- fa crawling delle url del vecchio sito e del nuovo sito
- alternativamente accetta due file tsv (TAB Separated Value) di url (la prima riga è la homepage; la prima colonna è la url di partenza; la seconda colonna, se presente, è la stringa per cui effettuare il confronto, se questa non è la url)
- cerca un matching per vicinanza tra stringhe di confronto (quando la % di somiglianza è inferiore alla treshold, viene suggerita la nuova homepage)
- genera un tsv con i match tra nuove e vecchie url

l'uso da linea di comando è il seguente:

```
php margherita.php -crawl oldHomepageUrl oldUrlsHint newHomepageUrl newUrlsHint depth homepageTreshold
```
```
php margherita.php -compare oldUrlsFile newUrlsFile homepageTreshold
```
