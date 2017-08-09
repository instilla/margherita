<?php
    // Margherita v0.4
    // by Instilla Srl

    // libraries
    require_once('helpers.php');

    // initialization
    set_time_limit(0);
    if (PHP_SAPI == 'cli') {
        ob_implicit_flush(true);
        clearScreen();
        echo "Margherita v0.4".PHP_EOL;
        clearScreen();
    }

    // get mode
    if (!empty($argv[1])) {
        $mode = $argv[1];
    } elseif (!empty($_POST['mode'])) {
        $mode = '-'.$_POST['mode'];
    } elseif (!empty($_GET['mode'])) {
        $mode = '-'.$_GET['mode'];
    } else {
        echo "no mode provided";
        clearScreen();
        return;
    }

    switch ($mode) {
        case '-crawl':
            if (PHP_SAPI == 'cli') {
                echo "Crawling routes...".PHP_EOL;
            }
    
            // get old homepage option
            if (!empty($argv[2])) {
                $oldHomepage = $argv[2];
            } elseif (!empty($_GET['oldHomepage'])) {
                $oldHomepage = $_GET['oldHomepage'];
            } else {
                $oldHomepage = 'instilla.it';
            }
            if (strpos($oldHomepage, 'http') === 0) {
                $oldHomepageUrl = $oldHomepage;
            } else {
                $oldHomepageUrl = 'http://'.$oldHomepage;
            }
            
            // get old homepage hint
            if (!empty($argv[3])) {
                $oldUrlsHint = $argv[3];
            } elseif (!empty($_GET['oldUrlsHint'])) {
                $oldUrlsHint = $_GET['oldUrlsHint'];
            } else {
                $oldUrlsHint = 'url';
            }

            // get new homepage option
            if (!empty($argv[4])) {
                $newHomepage = $argv[4];
            } elseif (!empty($_GET['newHomepage'])) {
                $newHomepage = $_GET['newHomepage'];
            } else {
                $newHomepage = 'instilla.it';
            }
            if (strpos($newHomepage, 'http') === 0) {
                $neweHomepageUrl = $newHomepage;
            } else {
                $newHomepageUrl = 'http://'.$newHomepage;
            }

            // get new homepage hint
            if (!empty($argv[5])) {
                $newUrlsHint = $argv[5];
            } elseif (!empty($_GET['newUrlsHint'])) {
                $newUrlsHint = $_GET['newUrlsHint'];
            } else {
                $newUrlsHint = 'url';
            }
            
            // get depth homepage option
            if (!empty($argv[6])) {
                $depth = $argv[6];
            } elseif (!empty($_GET['depth'])) {
                $depth = $_GET['depth'];
            } else {
                $depth = 2;
            }

            // get home treshold option
            if (!empty($argv[7])) {
                $homeTreshold = $argv[7];
            } elseif (!empty($_GET['homeTreshold'])) {
                $homeTreshold = $_GET['homeTreshold'];
            } else {
                $homeTreshold = 25;
            }

            // crawling old and new routes
            $oldRoutes = crawl_page($oldHomepageUrl, $oldHomepageUrl, $depth, $oldUrlsHint);
            $newRoutes = crawl_page($newHomepageUrl, $newHomepageUrl, $depth, $newUrlsHint);
            
            break;
        case '-compare':
            if (PHP_SAPI == 'cli') {
                echo "loading routes".PHP_EOL;
            } else {

            }
            // load old urls
            if (!empty($argv[2])) {
                $oldUrlsFile = file_get_contents($argv[2]);
            } elseif (!empty($_FILES['oldUrlsFile'])) {
                $oldUrlsFile = file_get_contents($_FILES['oldUrlsFile']['tmp_name']);
            }

            // load new urls
            if (!empty($argv[3])) {
                $newUrlsFile = file_get_contents($argv[3]);
            } elseif (!empty($_FILES['newUrlsFile'])) {
                $newUrlsFile = file_get_contents($_FILES['newUrlsFile']['tmp_name']);                
            }

            // get home treshold
            if (!empty($argv[4])) {
                $homeTreshold = $argv[4];
            } elseif (!empty($_POST['homeTreshold'])) {
                $homeTreshold = $_POST['homeTreshold'];
            } else {
                $homeTreshold = 25;
            }

            // start preparing routes
            if (PHP_SAPI == 'cli') {
                echo "Preparing routes...", PHP_EOL;
            }

            // preparing old routes
            $oldRows = preg_split("/\\r\\n|\\r|\\n/", $oldUrlsFile);
            $oldHomepageUrl = preg_split("/[\t]/", $oldRows[0])[0];
            $oldHomepageUrlLength = strlen($oldHomepageUrl);
            $oldRoutes = [];
            foreach ($oldRows as $index => $row) {
                if (PHP_SAPI != 'cli' && $index>50) break; // to prevent timeout
                $fields = preg_split("/[\t]/", $row);
                if (!empty($fields[0])) {
                    if (count($fields) == 1) {
                        $shortenedRoute = substr($fields[0], $oldHomepageUrlLength);
                        $oldRoutes[clearUrl($fields[0])] = clearUrl($shortenedRoute);
                    } elseif (count($fields) > 1) {
                        $oldRoutes[clearUrl($fields[0])] = $fields[1];
                    }
                }
            }

            // preparing new routes
            $newRows = preg_split("/\\r\\n|\\r|\\n/", $newUrlsFile);
            $newHomepageUrl = preg_split("/[\t]/", $newRows[0])[0];
            $newHomepageUrlLength = strlen($newHomepageUrl);
            $newRoutes = [];
            foreach ($newRows as $index => $row) {
                if (PHP_SAPI != 'cli' && $index>50) break; // to prevent timeout
                $fields = preg_split("/[\t]/", $row);
                if (!empty($fields[0])) {
                    if (count($fields) == 1) {
                        $shortenedRoute = substr($fields[0], $newHomepageUrlLength);
                        $newRoutes[clearUrl($fields[0])] = clearUrl($shortenedRoute);
                    } elseif (count($fields) > 1) {
                        $newRoutes[clearUrl($fields[0])] = $fields[1];
                    }
                }
            }
            break;
        default:
            echo "mode not recognized";
            return;
            break;
    }

    // start comparing routes
    if (PHP_SAPI == 'cli') {
        clearScreen();
        echo "Comparing routes...", PHP_EOL;
    }
    
    $string = "";
    foreach ($oldRoutes as $oldRoute => $oldRouteString) {
        $scores = [];
        foreach ($newRoutes as $newRoute => $newRouteString) {
            similar_text($oldRouteString, $newRouteString, $scores[$newRoute]);
            if (empty($oldRouteString)&&empty($newRouteString)) {
                $scores[$newRoute] = 100; // fix for similarity of two empty strings = 100
            }
        }
        asort($scores);
        $newRoute = array_keys($scores)[count($scores)-1];
        if ($scores[$newRoute] < $homeTreshold) {
            $match = $oldRoute . "\t" . $newHomepageUrl ."\t".'-';
        } else {
            $match = $oldRoute . "\t" . $newRoute ."\t".number_format($scores[$newRoute],2).'%';    
        }
        
        $string .= $match . PHP_EOL;
        if (PHP_SAPI == 'cli') {
            echo "MATCH: ", $match, PHP_EOL;
        }
    }

    
    if (PHP_SAPI == 'cli') {
        file_put_contents('redirects.tsv', $string);
        clearScreen();
        echo "comparing process completed!".PHP_EOL;
        echo "compared ", count($oldRoutes) , " old routes with ", count($newRoutes) , " new routes";
        clearScreen();
    } else {
        header('Content-type: text/tab-separated-values');
        header('Content-Disposition: attachment; filename="redirects.tsv"');
        echo $string;
    }