<?php
declare(strict_types=1);

namespace LamasFoker\LiveQuiz\Service;

use LamasFoker\LiveQuiz\Exception\TextExtractionException;

class InformationExtractor
{
    /**
     * @param string $text
     * @return string
     * @throws TextExtractionException
     */
    public function extractQuestion(string $text): string
    {
        $start = mb_strpos($text, '€' . PHP_EOL);
        if ($start === false) {
            throw new TextExtractionException('Char "€" not found in: '.PHP_EOL.$text);
        }
        $text = mb_substr($text, ++$start);
        $end = mb_strpos($text, '?' . PHP_EOL)?:strpos($text, ':' . PHP_EOL);
        if ($end === false) {
            throw new TextExtractionException('Char "?" or ":" not found in: '.PHP_EOL.$text);
        }
        $text = mb_substr($text, 0, ++$end);
        $text = str_replace(PHP_EOL, ' ', $text);
        $text = str_replace('"', '', $text);
        return $text;
    }


    /**
     * @param string $text
     * @return array
     * @throws TextExtractionException
     */
    public function extractAnswers(string $text): array
    {
        $start = mb_strpos($text, '?' . PHP_EOL)?:strpos($text, ':' . PHP_EOL);
        if ($start === false) {
            throw new TextExtractionException('Char "?" or ":" not found in: '.PHP_EOL.$text);
        }
        $text = mb_substr($text, --$start);
        $answers = explode(PHP_EOL, $text);
        $answers = array_filter($answers, function($value) {
            return mb_strlen($value) > 2;
        });
        $answers = array_slice($answers, 0, 3);
        return $answers;
    }
}
