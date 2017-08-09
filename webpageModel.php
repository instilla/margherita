<?php
    // Margherita v.0.4
    // by Instilla Srl

class webpage {
    function __construct($url) {
        @$this->url = $url;
        @$this->htmlString = file_get_contents($this->url);
        @$this->dom = new \DOMDocument();
        @$this->dom->loadHTML($this->htmlString);
        @$this->xPath = new DomXPath($this->dom);
    }

	function getHrefs() {
        $nodes = $this->dom->getElementsByTagName('a');
        $hrefs = [];
        foreach (iterator_to_array($nodes) as $node) {
            array_push($hrefs, $node->getAttribute('href'));
        }
        return $hrefs;
    }

    function getTextByTag($tag) {
        $nodes = $this->dom->getElementsByTagName($tag);
        $texts = [];
        foreach (iterator_to_array($nodes) as $node) {
            array_push($texts, $node->textContent);
        }
        return $texts;
    }

    function getTextByClass($class) {
        $nodes = $this->xPath->query("//*[contains(@class, '".$class."')]");
        $texts = [];
        foreach (iterator_to_array($nodes) as $node) {
            array_push($texts, $node->textContent);
        }
        return $texts;
    }

    function getByClass($class) {
        $nodes = $this->xPath->query("//*[contains(@class, '".$class."')]");
        return $nodes;
    }

}