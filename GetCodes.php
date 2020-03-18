<?php

class Constants
{
    const MASTER = "master";
    const TRACK_URL_CONST = "/rest/contests/" . Constants::MASTER . "/tracks/";
    const CHALLENGES_URL_CONST = "/challenges/";
    const PROBLEM_URL_CONST = "/problem/";
    const URL_PREFIX = "https://www.hackerrank.com/";
    const URL_PARAM_OFFSET = "offset";
    const URL_PARAM_LIMIT = "limit";
    const CONTENT = "/content/";
    const CURL_AGENT = 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:59.0) Gecko/20100101 Firefox/59.0';

    const QUESTION_MARK = "?";
    const AMPERSAND = "&";
    const EQUALS = "=";
    const FORWARD_SLASH = "/";
    const SPACE = " ";
    const EMPTY_STRING = "";
    const NEW_LINE = "\n";

    const URL_PARAM_START = "0";
    const URL_PARAM_LIMIT_VAL = "50";

    const SLUG = "slug";
    const URL = "url";
    const HTML_EXTENSION = ".html";
    const PDF_EXTENSION = ".pdf";
    const SOLUTION_FILENAME = "Solution.java";
    const NO_SOLUTION_SLUG_FILENAME = "no-solution.out";
    const SOLUTION_ID = "initialData";
    const CHALLENGE_BODY_CLASS_NAME = "challenge-body-html";
    const HOME_DIR_CONST = "HOME";
    const DOMAINS = ['algorithms', 'data-structures'];
    const LOG_FILE = "debug.log";
    const FINDER_QUERY_START = "//*[contains(concat(' ', normalize-space(@class), ' '), ' ";
    const FINDER_QUERY_END = " ')]";
    const HTML_DIV_START = "<html><body><div style=\"display: block;
            margin: 100px auto;
            padding: 30px;
            background: rgb(250, 250, 250);
            box-shadow: 0 6px 16px 0 rgba(0,0,0,.2);
            width: 90%;
            position: relative;
            transition: all 5s ease-in-out;\">";
    const HTML_DIV_END = "</div></body></html>";
    const JAVA_TEMPLATE_HEAD = "java_template_head";
    const JAVA_TEMPLATE = "java_template";
    const JAVA_TEMPLATE_TAIL = "java_template_tail";
    const WKHTMLTOPDF_PATH = "/usr/bin/wkhtmltopdf";
    const WKHTMLTOPDF_QUIET = "--quiet";
    const WKHTMLTOPDF_WRONG_URL_CONST = "=\"//";
    const WKHTMLTOPDF_CORRECT_URL_CONST = "=\"http://";
}

/**
 * @param array $domains
 * @return array
 */
function getTrackURLs(array $domains): array
{
    $track_urls = [];

    foreach ($domains as $domain) {
        $url = array();
        $url[] = Constants::URL_PREFIX . Constants::TRACK_URL_CONST . $domain
            . Constants::CHALLENGES_URL_CONST . Constants::QUESTION_MARK
            . Constants::URL_PARAM_OFFSET . Constants::EQUALS;
        $url[] = Constants::AMPERSAND . Constants::URL_PARAM_LIMIT
            . Constants::EQUALS . Constants::URL_PARAM_LIMIT_VAL;
        array_push($track_urls, $url);
    }
    return $track_urls;
}


function getResponse($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
    curl_setopt($ch, CURLOPT_USERAGENT, Constants::CURL_AGENT);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_ENCODING, true);
    $response = curl_exec($ch);
    curl_close($ch);

    return $response;
}

function getProblemStatement($html)
{
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML($html);

    $finder = new DomXPath($dom);
    $classname = Constants::CHALLENGE_BODY_CLASS_NAME;
    $nodes = $finder->query(Constants::FINDER_QUERY_START . $classname . Constants::FINDER_QUERY_END);

    $out_dom = new DOMDocument();

    foreach ($nodes as $node) {
        $out_dom->appendChild($out_dom->importNode($node, true));
    }

    $innerHTML = "";
    $innerHTML .= trim($out_dom->saveHTML());

    $innerHTML = Constants::HTML_DIV_START . $innerHTML . Constants::HTML_DIV_END;

    return $innerHTML;
}

function getSolution($html, $slug)
{

    $solution = array();

    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML($html);

    $response = $dom->getElementById(Constants::SOLUTION_ID);
    $json_obj = json_decode(urldecode($response->nodeValue));
    $master_slug = Constants::MASTER . Constants::FORWARD_SLASH . $slug;
    $detail = $json_obj->community->challenges->challenge->{$master_slug}->detail;

    if (array_key_exists(Constants::JAVA_TEMPLATE_HEAD, $detail)) {
        $solution[] = $detail->{Constants::JAVA_TEMPLATE_HEAD};
    } else {
        $solution[] = "";
    }

    if (array_key_exists(Constants::JAVA_TEMPLATE, $detail)) {
        $solution[] = $detail->{Constants::JAVA_TEMPLATE};
    } else {
        $solution[] = "";
    }

    if (array_key_exists(Constants::JAVA_TEMPLATE_TAIL, $detail)) {
        $solution[] = $detail->{Constants::JAVA_TEMPLATE_TAIL};
    } else {
        $solution[] = "";
    }

    return $solution;
}

function getModels($response)
{
    $json_obj = json_decode($response);
    return $json_obj->models;
}

function getProblemURLs($models)
{
    $problem_count = sizeof($models);

    $problem_urls = array();
    for ($i = 0; $i < $problem_count; $i++) {
        $model = $models[$i];
        $slug = $model->slug;
        $problem_url = Constants::URL_PREFIX . Constants::CHALLENGES_URL_CONST . $slug . Constants::PROBLEM_URL_CONST;
        $problem_urls[] = array(Constants::SLUG => $slug, Constants::URL => $problem_url);
    }

    return $problem_urls;
}

/**
 * This method writes the given text to a given fileName.
 * @param $text
 * @param $fileName
 */
function saveToFile($text, $fileName)
{
    $myFile = fopen($fileName, "w") or die("Unable to open file!");
    fwrite($myFile, $text);
    fclose($myFile);
}

/**
 * This function will take a json response and gets problem urls and for each problem
 * creates and save problem statement and solution in its respective folder/file.
 * @param $models
 * @param $domain
 * @param $no_solution_slugs
 */
function saveProblemsAndSolutions($models, $domain, &$no_solution_slugs): void
{
    $problem_urls = getProblemURLs($models);
    $problem_count = sizeof($problem_urls);

    $content_dir = $_SERVER[Constants::HOME_DIR_CONST] . Constants::CONTENT . $domain . Constants::FORWARD_SLASH;

    $problemFileNamesHTML = array();
    $problemFileNamesPDF = array();
    $solutionFileNames = array();

    for ($i = 0; $i < $problem_count; $i++) {
        $slug = $problem_urls[$i][Constants::SLUG];
        if (!file_exists($content_dir . "${slug}")) {
            mkdir($content_dir . "${slug}", 0777, true);
        }
        $problemFileNamesHTML[] = $content_dir . "${slug}" . Constants::FORWARD_SLASH . "${slug}" . Constants::HTML_EXTENSION;
        $problemFileNamesPDF[] = $content_dir . "${slug}" . Constants::FORWARD_SLASH . "${slug}" . Constants::PDF_EXTENSION;
        $solutionFileNames[] = $content_dir . "${slug}" . Constants::FORWARD_SLASH . Constants::SOLUTION_FILENAME;
    }


    //    $no_solution_slugs = array();
    for ($i = 0; $i < $problem_count; $i++) {
        $slug = $problem_urls[$i][Constants::SLUG];
        $problem_url = $problem_urls[$i][Constants::URL];
        $response = getResponse($problem_url);

        // Hack to avoid infinite wait issues with wkhtmltopdf. It first occurred for problem with slug "longest-increasing-subsequent".
        $response = str_replace(Constants::WKHTMLTOPDF_WRONG_URL_CONST, Constants::WKHTMLTOPDF_CORRECT_URL_CONST, $response);

        $problemFileNameHTML = $problemFileNamesHTML[$i];
        $problemFileNamePDF = $problemFileNamesPDF[$i];
        $solutionFileName = $solutionFileNames[$i];
        echo $problemFileNameHTML . Constants::SPACE . $solutionFileName . Constants::NEW_LINE;
        saveToFile(getProblemStatement($response), $problemFileNameHTML);
        exec(Constants::WKHTMLTOPDF_PATH
            . Constants::SPACE . Constants::WKHTMLTOPDF_QUIET
            . Constants::SPACE . $problemFileNameHTML
            . Constants::SPACE . $problemFileNamePDF);
        $solution = getSolution($response, $slug);
        $code = $solution[0] . $solution[1] . $solution[2];
        saveToFile($code, $solutionFileName);
        if (strcmp($code, Constants::EMPTY_STRING) == 0) {
            $no_solution_slugs[] = $slug;
        }
    }

}

function debug_enable()
{
    $fdOut = fopen(Constants::LOG_FILE, 'w');

    eio_dup2($fdOut, STDOUT);
    eio_dup2($fdOut, STDERR);
    eio_event_loop();

    fclose($fdOut);
}

/**
 * This method internally scrapes and saves all problem statements and solutions from HackerRank portal and saves to machine.
 */
function main(): void
{

    debug_enable();

    $domains = Constants::DOMAINS;

    $track_urls = getTrackURLs($domains);

    $domain_count = sizeof($domains);
    for ($i = 0; $i < $domain_count; $i++) {

        $start = Constants::URL_PARAM_START;
        $url = $track_urls[$i][0] . $start . $track_urls[$i][1];
        $models = getModels(getResponse($url));
        $all_models = array();
        while (sizeof($models) > 0) {
            array_push($all_models, ...$models);
            $start = $start + Constants::URL_PARAM_LIMIT_VAL;
            $url = $track_urls[$i][0] . $start . $track_urls[$i][1];
            $models = getModels(getResponse($url));
        }

//         test data 1
//        $all_models = array((object)array('slug' => 'kth-ancestor'),
//            (object)array('slug' => 'dag-queries'),
//            (object)array('slug' => 'favourite-sequence'),
//            (object)array('slug' => 'cat-jogging'),
//            (object)array('slug' => 'training-the-army'),
//            (object)array('slug' => 'going-office'),
//            (object)array('slug' => 'ticket'),
//            (object)array('slug' => 'team-formation'),
//            (object)array('slug' => 'beautiful-3-set'),
//            (object)array('slug' => 'inverse-rmq'),
//            (object)array('slug' => 'superman-celebrates-diwali'),
//            (object)array('slug' => 'dorsey-thief'),
//            (object)array('slug' => 'zurikela'),
//            (object)array('slug' => 'hamming-distance'),
//            (object)array('slug' => 'repeat-k-sums'),
//            (object)array('slug' => 'move-the-coins'),
//            (object)array('slug' => 'walking-the-approximate-longest-path'),
//            (object)array('slug' => 'spies-revised')
//        );

//          test data 2
//        $all_models = array((object)array('slug' => 'longest-increasing-subsequent'));

        $no_solution_slugs = array();
        saveProblemsAndSolutions($all_models, $domains[$i], $no_solution_slugs);

        saveToFile(var_export($no_solution_slugs, true), Constants::NO_SOLUTION_SLUG_FILENAME);

    }
}



$time_pre = microtime(true);
main();
$time_post = microtime(true);

echo "Total time elapsed (in HH:MM:SS format) = " . gmdate("H:i:s", ($time_post - $time_pre)) . "\n";



?>

