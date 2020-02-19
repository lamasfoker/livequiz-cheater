<?php
declare(strict_types=1);
require __DIR__ . '/../vendor/autoload.php';

use LamasFoker\LiveQuiz\Exception\LiveQuizException;
use LamasFoker\LiveQuiz\Service\GoogleGuru;
use LamasFoker\LiveQuiz\Service\InformationExtractor;
use LamasFoker\LiveQuiz\Service\TextDetector;
use LamasFoker\LiveQuiz\Service\TextTranslator;
use LamasFoker\LiveQuiz\Service\WolframAlphaGuru;

$path = $_FILES['question']['tmp_name'] ?? __DIR__ . '/../test/test.jpg';
$textDetector = new TextDetector();
$textTranslator = new TextTranslator();
$wolframAlphaGuru = new WolframAlphaGuru();
$informationExtractor = new InformationExtractor();
$googleGuru = new GoogleGuru();

try {
    $text = $textDetector->detect($path);
    $question = $informationExtractor->extractQuestion($text);
    $question = $textTranslator->translate($question, 'en');
    $answer = $wolframAlphaGuru->respond($question);
    if (is_null($answer)) {
        $answers = $informationExtractor->extractAnswers($text);
        $answers = array_map(function ($answer) use ($textTranslator) {
            return $textTranslator->translate($answer, 'en');
        }, $answers);
        $answer = $googleGuru->respond($question, $answers);
        if (is_null($answer)) {
            $answer = $answers[array_rand($answers, 1)];
        }
    }
} catch (LiveQuizException $exception) {
    $answer = $exception->getMessage();
}
require_once('response.phtml');
