<?php
declare(strict_types=1);

namespace LamasFoker\LiveQuiz\Service;

class InformationExtractor
{
    /**
     * @param string $text
     * @return string
     */
    public function extractQuestion(string $text): string
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
    public function extractAnswers(string $text): array
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
}
