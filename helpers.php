<?php
    // Margherita v.0.4
    // by Instilla Srl

    // libraries
    require_once('webpageModel.php');

    function clearScreen() {
        for ($i = 0; $i < 3; $i++) echo "\r\n";
    }

    function saveXls($titles, $rows){
        $fp = fopen('outputMaMt.xls', 'w');

        fputcsv($fp, $titles, chr(9));
        foreach ($rows as $row) {
            fputcsv($fp, $row, chr(9));
        }

        fclose($fp);
    }

    function createRows($arrays){
        // arrays must be of the same length

        $rows = [];
        $columsNumber = count($arrays);
        $rowsNumber = count($arrays[0]);

        for ($iii = 0; $iii<$rowsNumber; $iii++) {
            $row = [];
            for ($jjj = 0; $jjj<$columsNumber; $jjj++) {
                array_push($row, $arrays[$jjj][$iii]);
            }
            array_push($rows, $row);
        }

        return $rows;
    }

    function getStringBetween($string, $start, $end){
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return null;
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return trim(substr($string, $ini, $len));
    }

    function crawl_page($url, $homepage, $depth, $hint = "url", $seen=[]) {
        $url = clearUrl($url);
        if (isset($seen[$url]) || $depth === 0) {
            return $seen;
        }

        if (PHP_SAPI != 'cli' && count($seen) > 8) return $seen; // to prevent timeout

        $webpage = new Webpage($url);
        switch ($hint) {
            case 'h1':
                $h1 = @$webpage->getTextByTag('h1')[0];
                $seen[$url] = $h1;
                break;
            case 'title':
                $title = @$webpage->getTextByTag('title')[0];
                $seen[$url] = $title;
                break;
            default: // url
                $shortenedRoute =  clearUrl(substr($url, strlen($homepage)));
                $seen[$url] = $shortenedRoute;
                break;
        }

        $hrefs = $webpage->getHrefs();
        foreach ($hrefs as $href) {
            if (strpos($href, "mailto:") === 0 | strpos($href, "tel:") === 0 | strpos($href, "skype:") === 0 | strpos($href, 'javascript') === 0) $href = $homepage;
            $href = enhanceHref($href, $url);
            if (strpos($href, $homepage) === 0) {
                $seen = crawl_page($href, $homepage, $depth - 1, $hint, $seen);
            }
        }
        if (PHP_SAPI == 'cli') {
            echo "URL:", $url, PHP_EOL;
        }
        return $seen;
    }

    function enhanceHref($href, $url) {
        if (0 !== strpos($href, 'http')) {
            $path = '/' . ltrim($href, '/');
            if (extension_loaded('http')) {
                $href = http_build_url($url, array('path' => $path));
            } else {
                $parts = parse_url($url);
                $href = $parts['scheme'] . '://';
                if (isset($parts['user']) && isset($parts['pass'])) {
                    $href .= $parts['user'] . ':' . $parts['pass'] . '@';
                }
                $href .= $parts['host'];
                if (isset($parts['port'])) {
                    $href .= ':' . $parts['port'];
                }
                $href .= $path;
            }
        }
        return $href;
    }

    function clearUrl($url) {
        if (empty($url)) return "";
        $url = explode('#', $url)[0];
        $url = explode('?', $url)[0];
        if (empty($url)) return "";
        return $url;
    }