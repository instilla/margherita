<html>
<head>
    <title> Instilla - Margherita 0.4 </title>
    <meta name="description" content="Crawler con finalità SEO per comparare le url di un nuovo e di un vecchio sito web a fini di migrazione.">
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link href='https://fonts.googleapis.com/css?family=Roboto+Slab:400,700' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" type="text/css" href="grids.css">
    <link rel="stylesheet" type="text/css" href="custom.css">
    <link rel="icon" href="img/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" href="img/favicon.png" type="image/png">
</head>
<body>
    <section id="header">
        <div id="logo">
            <img src="img/instilla-technologies.png">
            <h1>Margherita</h1> 
        </div>
    </section>
    <section>
        <h2>Il miglior tool per migrare le URL</h2>
        
        <p>Margherita è un software sviluppato da <a href="http://instilla.it">Instilla</a>, crawler con finalità SEO che ha scopo di comparare le url di un nuovo e di un vecchio sito web a fini di migrazione.</p>
        <br/>
        <p>La versione 0.4 del software:</p>
        <ul>
            <li>fa crawling delle url del vecchio sito e del nuovo sito</li>
            <li>alternativamente accetta due file csv di url</li>

            <li>cerca un matching per vicinanza tra vecchie e nuove url</li>
            <li>genera un csv con i match tra nuove e vecchie url (propone in automatico la homepage se la somiglianza è sotto la soglia di somiglianza)</li>
        </ul>
    </section>
    <section id="forms">
        <div class="row">
            <div class="col-md-6">
                <form id="form1" action="/margherita.php" method="get">
                    <input name="mode" type="hidden" value="crawl"/>
                    <h3> Esegue il crawling e compara le url</h3>
                    <p>Vecchia homepage <br/><input placeholder="url della vecchia homepage" name="oldHomepage" type="text"/></p>
                    <p>Stringa vecchia url per il confronto<br/>
                        <select name="oldUrlsHint">
                            <option value="url">Url</option>
                            <option value="title">Title</option>
                            <option value="h1">h1</option>
                        </select>
                    </p>
                    <p>Nuova homepage <br/><input placeholder="url della nuova homepage" name="newHomepage" type="text"/></p>
                    <p>Stringa nuova url per il confronto<br/>
                        <select name="newUrlsHint">
                            <option value="url">Url</option>
                            <option value="title">Title</option>
                            <option value="h1">h1</option>
                        </select>
                    </p>
                    <p>Profondità del crawling <br/><input placeholder="profondità del crawling" name="depth" type="number" value="2" readonly="readonly" /></p>

                    <p>Massimo url <br/><input placeholder="massimo di url analizzabili" type="number" value="8" readonly="readonly" /></p>
                    <p>Soglia (default 25%)<br/><input placeholder="soglia di somiglianza" name="homeTreshold" type="text"/> %</p>
                    <div id="pulsantone1">
                        <input id="submit" type="submit" value="Trova e compara le url" onclick="document.getElementById('pulsantone1').innerHTML = '<p>calcolando...<p>'; document.getElementById('form2').style.visibility = 'hidden'; document.getElementById('form1').submit()" />
                    </div>
                </form>
            </div>
            <div class="col-md-6">
                <form id="form2" action="/margherita.php" method="post" enctype='multipart/form-data'>
                    <input name="mode" type="hidden" value="compare"/>
                    <h3>Compara le url caricate</h3>
                    <p>Vecchie url <br/><input name="oldUrlsFile" type="file"/></p>
                    <p>Nuove url <br/><input name="newUrlsFile" type="file"/></p>
                    <p>Massimo url <br/><input placeholder="massimo di url analizzabili" type="number" value="50" readonly="readonly" /></p>
                    <p>Soglia (default 25%)<br/><input placeholder="soglia di somiglianza" name="homeTreshold" type="text"/> %</p>
                    <div id="pulsantone2">
                        <input id="submit" type="submit" value="Compara le url" onclick="document.getElementById('pulsantone2').innerHTML = '<p>calcolando...<p>'; document.getElementById('form1').style.visibility = 'hidden'; document.getElementById('form2').submit()" />
                    </div>
                </form>
            </div>
        </div>
    </section>
    <section>
        <p>Il tool online ha delle limitazioni. Poiché il server è impostato per andare in timeout dopo 30sec, la profondità del crawler è fissata a 2 (ovvero vengono analizzate solo le url in homepage) e il numero massimo di url analizzabili per dominio è 8 e 50.</p>
        <p>Per superare queste limitazioni, è consigliabile scaricare il tool dal <a href="https://github.com/instilla/margherita">repository su Github</a> e utilizzarlo da riga di comando con la seguente sintassi:<p>
        <div id="php-comando">
            <p><i>php margherita.php -crawl oldHomepageUrl oldUrlsHint newHomepageUrl newUrlsHint depth homepageTreshold</i></p>
            <p><i>php margherita.php -compare oldUrlsFile newUrlsFile homepageTreshold</i></p>
        </div>
    </section>
</body>
</html>