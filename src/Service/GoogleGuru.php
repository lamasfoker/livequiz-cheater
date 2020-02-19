<?php
declare(strict_types=1);

namespace LamasFoker\LiveQuiz\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ServerException;

class GoogleGuru
{
    const GOOGLE_ENDPOINT = 'https://www.google.com/search';

    /**
     * @param string $question
     * @param array $answers
     * @return string|null
     */
    public function respond(string $question, array $answers): ?string
    {
        $client = new Client();
        $isNegativeQuestion = preg_match('/\b(not)\b/i', $question) === 1;
        if ($isNegativeQuestion) {
            $question = preg_replace('/\b(not)\b/i', '', $question)?:$question;
        }
        $url = self::GOOGLE_ENDPOINT . '?' .
            http_build_query([
                'q' => $question
            ]);
        try {
            $response = $client->request('GET', $url);
        } catch (ServerException $exception) {
            return null;
        }
        if ($response->getStatusCode() === 200) {
            $body = (string) $response->getBody();
            return $this->getRightAnswer($body, $answers, $isNegativeQuestion);
        }
        return null;
    }

    /**
     * @param string $body
     * @param array $answers
     * @param bool $isNegativeQuestion
     * @return string
     */
    private function getRightAnswer(string $body, array $answers, bool $isNegativeQuestion = false): string
    {
        $answersScore = array_map(function ($answer) use ($body) {
            $explodedAnswer = array_filter(explode(' ', $answer), function($word) {
                return strlen($word) > 3;
            });
            $occurrences = array_map(function ($word) use ($body) {
                return preg_match_all('/\b'.$word.'\b/i', $body);
            }, $explodedAnswer);
            return array_sum($occurrences);
        }, $answers);
        if ($isNegativeQuestion) {
            $keyOfTheMaxScore = array_keys($answersScore, min($answersScore))[0];
        } else {
            $keyOfTheMaxScore = array_keys($answersScore, max($answersScore))[0];
        }
        return $answers[$keyOfTheMaxScore];
    }
}
