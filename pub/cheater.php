<?php
declare(strict_types=1);
require __DIR__ . '/../vendor/autoload.php';

use LamasFoker\LiveQuiz\Exception\GoogleException;
use LamasFoker\LiveQuiz\Exception\VisionApiException;
use LamasFoker\LiveQuiz\Exception\WolframAlphaException;
use LamasFoker\LiveQuiz\Service\GoogleGuru;
use LamasFoker\LiveQuiz\Service\InformationExtractor;
use LamasFoker\LiveQuiz\Service\TextDetector;
use LamasFoker\LiveQuiz\Service\TextTranslator;
use LamasFoker\LiveQuiz\Service\WolframAlphaGuru;

$path = $_FILES['question']['tmp_name'] ?? __DIR__ . '/test.jpg';
header('Content-Type: text/html');
$textDetector = new TextDetector();
$textTranslator = new TextTranslator();
$wolframAlphaGuru = new WolframAlphaGuru();
$informationExtractor = new InformationExtractor();
$googleGuru = new GoogleGuru();

try {
    $text = $textDetector->detect($path);
    $question = $informationExtractor->extractQuestion($text);
    $question = $textTranslator->translate($question, 'en');
} catch (VisionApiException $exception) {
    $answer = $exception->getMessage();
    echo "<!doctype html><html><h3>" . $answer . "</h3></html>";
    return;
}
try {
    $answer = $wolframAlphaGuru->respond($question);
} catch (WolframAlphaException $exception) {
    $answers = $informationExtractor->extractAnswers($text);
    $answers = array_map(function ($answer) use ($textTranslator) {
        return $textTranslator->translate($answer, 'en');
    }, $answers);
    try {
        $answer = $googleGuru->respond($question, $answers);
    } catch (GoogleException $e) {
        $answer = $answers[array_rand($answers, 1)];
    }
}
//$answer = $textTranslator->translate($answer, 'it');
echo "<!doctype html><html><h3>" . $answer . "</h3></html>";
