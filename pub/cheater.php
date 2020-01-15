<?php
declare(strict_types=1);
require __DIR__ . '/../vendor/autoload.php';

use Google\ApiCore\ApiException;
use Google\ApiCore\ValidationException;
use Google\Cloud\Translate\V2\TranslateClient;
use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ServerException;

const WOLFRAMALPHA_SHORT_ANSWER_API_ENDPOINT = 'http://api.wolframalpha.com/v1/result';

$path = $_FILES['question']['tmp_name'] ?? __DIR__ . '/test.jpg';

try {
    $text = detect_text($path);
    $query = extract_query($text);
    $query = translate_text_to($query, 'en');
    $answer = get_answer($query);
    $answer = translate_text_to($answer, 'it');
} catch (ServerException $exception) {
    $text = detect_text($path);
    $query = extract_query($text);
    //TODO: make a call to google with query and search for the 3 answer, return the one with more matches
    $answers = extract_answers($text);
} catch (Exception $exception) {
    $answer = $exception->getMessage();
}

header('Content-Type: text/html');
echo "<!doctype html><html><h3>" . $answer . "</h3></html>";

/**
 * @param string $path
 * @return string
 * @throws Exception
 */
function detect_text(string $path): string
{
    try {
        $imageAnnotator = new ImageAnnotatorClient();
        $image = file_get_contents($path);
        $response = $imageAnnotator->textDetection($image);
    } catch (ValidationException $exception) {
        //TODO: console log something
        //TODO: change exception
        throw new Exception('Vision API can\'t recognizes text');
    } catch (ApiException $exception) {
        //TODO: console log something
        //TODO: change exception
        throw new Exception('Vision API can\'t recognizes text');
    }
    $imageAnnotator->close();
    return $response->getFullTextAnnotation()->getText();
}

/**
 * @param string $text
 * @return string
 */
function extract_query(string $text): string
{
    $start = strpos($text, '€');
    if ($start !== false) {
        $text = mb_substr($text, ++$start);
    }
    $end = strpos($text, '?');
    if ($start !== false) {
        $text = mb_substr($text, 0, $end);
    }
    return $text;
}

/**
 * @param string $text
 * @return string[]
 */
function extract_answers(string $text): array
{
    $start = strpos($text, '?');
    if ($start !== false) {
        $text = mb_substr($text, --$start);
    }
    $answers = explode(PHP_EOL, $text);
    $answers = array_filter($answers, function($key) {
        return $key<3;
    }, ARRAY_FILTER_USE_KEY);
    return $answers;
}

/**
 * @param string $text
 * @param string $language
 * @return string
 * @throws Exception
 */
function translate_text_to(string $text, string $language): string
{
    try {
        $translate = new TranslateClient();
    } catch (InvalidArgumentException $exception) {
        //TODO: console log something
        //TODO: change exception
        throw new Exception('Translation API error');
    }
    $result = $translate->translate($text, [
        'target' => $language,
    ]);
    return $result['text'];
}

/**
 * @param string $query
 * @return string
 * @throws ServerException
 */
function get_answer(string $query): string
{
    //TODO: change exception
    $client = new Client();
    $url = WOLFRAMALPHA_SHORT_ANSWER_API_ENDPOINT . '?' .
        http_build_query([
            'appid' => getenv('WOLFRAMALPHA_APPID'),
            'i' => $query,
            'timeout' => 3
        ]);
    $response = $client->request('GET', $url);
    if ($response->getStatusCode() === 200) {
        return (string) $response->getBody();
    }
}
