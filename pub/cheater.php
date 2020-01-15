<?php
declare(strict_types=1);
require __DIR__ . '/../vendor/autoload.php';

use LamasFoker\LiveQuiz\Exception\VisionApiException;
use LamasFoker\LiveQuiz\Exception\WolframAlphaException;
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

try {
    $text = $textDetector->detect($path);
} catch (VisionApiException $exception) {
    $answer = $exception->getMessage();
    echo "<!doctype html><html><h3>" . $answer . "</h3></html>";
    return;
}
try {
    $question = $informationExtractor->extractQuestion($text);
    $question = $textTranslator->translate($question, 'en');
    $answer = $wolframAlphaGuru->respond($question);
    $answer = $textTranslator->translate($answer, 'it');
} catch (WolframAlphaException $exception) {
    //TODO: make a call to google with query and search for the 3 answer, return the one with more matches
    $answers = $informationExtractor->extractAnswers($text)[2];
}
echo "<!doctype html><html><h3>" . $answer . "</h3></html>";
