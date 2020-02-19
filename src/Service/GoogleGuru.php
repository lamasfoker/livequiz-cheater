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
            return $this->getRightAnswer($body, $answers);
        }
        return null;
    }

    /**
     * @param string $body
     * @param array $answers
     * @return string
     */
    private function getRightAnswer(string $body, array $answers): string
    {
        $answersScore = array_map(function ($answer) use ($body) {
            $explodedAnswer = array_filter(explode(' ', $answer), function($word) {
                return strlen($word) > 3;
            });
            $occurrences = array_map(function ($word) use ($body) {
                return substr_count($body, $word);
            }, $explodedAnswer);
            return array_sum($occurrences);
        }, $answers);
        $keyOfTheMaxScore = array_keys($answersScore, max($answersScore))[0];
        return $answers[$keyOfTheMaxScore];
    }
}
