<?php

class Types {
    const PROBLEM = 1;
    const TRACKS = 2;
}

class Constants {
    const MAX_LIMIT = 1000;
    const TRACK_URL_CONST = "/rest/contests/master/tracks/";
    const CHALLENGES_URL_CONST = "/challenges/";
    const PROBLEM_URL_CONST = "/problem/";
    const URL_PREFIX = "https://www.hackerrank.com/";
    const REST_URL_SUFFIX = "?offset=0&limit=" . Constants::MAX_LIMIT;
}

$domains = ['algorithms', 'data-structures'];
$track_urls = [];


foreach($domains as $domain) {
    $url = Constants::URL_PREFIX . Constants::TRACK_URL_CONST . $domain . Constants::CHALLENGES_URL_CONST . Constants::REST_URL_SUFFIX;
    array_push($track_urls, $url);
}

// print_r($domain_urls);

// $url = 'https://www.hackerrank.com/challenges/java-datatypes/problem';
// $url = 'https://www.hackerrank.com/challenges/alternating-characters/problem';

// $url = 'https://www.hackerrank.com/challenges/reverse-a-linked-list';

function getResponse($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:59.0) Gecko/20100101 Firefox/59.0');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_ENCODING, true);
    $response = curl_exec($ch);
    curl_close($ch);

    return $response;
}


function getElementsByClass(&$parentNode, $tagName, $className) {
    $nodes=array();

    $childNodeList = $parentNode->getElementsByTagName($tagName);
    for($i = 0; $i < $childNodeList->length; $i++) {
        $temp = $childNodeList->item($i);
        if(stripos($temp->getAttribute('class'), $className) !== false) {
            $nodes[]=$temp;
            // $node_length = sizeof($nodes);
            // for($j = 0; $j < $node_length; $j++) {
            //     $innerHTML .= $
            // }
        }
    }

    return $nodes;
}


function getProblemStatement($html) {
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML($html);
    $content = $dom->getElementById("content");
    
    // $p = $content->getElementsByTagName("p");
    // $total_p = sizeof($p);
    // $problem_statement = "";
    // for($i = 0; $i < $total_p; $i++) {
    //     $problem_statement = $problem_statement . $p[$i]->nodeValue . "\n";
    // }
    // return $problem_statement;
    
    // $div_challenge_body_html_nodes = getElementsByClass($content, "div", "challenge-body-html");
    // return $div_challenge_body_html_nodes[0]->nodeValue;
    
    $innerHTML = "";
    $children = $content->childNodes;
    foreach($children as $child) {
        $innerHTML .= $child->ownerDocument->saveHTML($child);
    }

    return $innerHTML;
}

function getProblemCode($html) {
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML($html);
    $data = $dom->getElementById("initialData");
    $json = urldecode($data->nodeValue);
    $json_obj = json_decode($json);
        
    return $json_obj;
}

function getProblemURLs($response) {
    $json_obj = json_decode($response);

    $problem_urls = [];

    $models = $json_obj->models;
    $problem_count = sizeof($models);

    for($i = 0; $i < $problem_count; $i++) {
        $model = $models[$i];
        $slug = $model->slug;
        $name = $model->name;
        $problem_url = Constants::URL_PREFIX . Constants::CHALLENGES_URL_CONST . $slug . Constants::PROBLEM_URL_CONST;
        // echo $problem_url;
        array_push($problem_urls, $problem_url);
    }

    // return $json_obj;

    return $problem_urls;
}

$response = getResponse($track_urls[0]);
$problem_urls = getProblemURLs($response);

$problem_count = 1; //sizeof($problem_urls);

for($i = 0; $i < $problem_count; $i++) {
    echo getProblemStatement(getResponse($problem_urls[$i]));
}

// $json_obj = getJsonFromURL("https://www.hackerrank.com/challenges/java-datatypes/problem", Types::PROBLEM);

// print_r($problem_urls);
// echo $json_obj->models[0]->max_score;

//echo $json_obj->community->challenges->challenge->{'master/reverse-a-linked-list'}->detail->java8_template_head;
//echo $json_obj->community->challenges->challenge->{'master/reverse-a-linked-list'}->detail->java8_template;
//echo $json_obj->community->challenges->challenge->{'master/reverse-a-linked-list'}->detail->java8_template_tail;

?>

