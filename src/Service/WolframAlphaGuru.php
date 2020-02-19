<?php
declare(strict_types=1);

namespace LamasFoker\LiveQuiz\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ServerException;

class WolframAlphaGuru
{
    const WOLFRAMALPHA_SHORT_ANSWER_API_ENDPOINT = 'http://api.wolframalpha.com/v1/result';

    /**
     * @param string $question
     * @return string|null
     */
    public function respond(string $question): ?string
    {
        $client = new Client();
        $url = self::WOLFRAMALPHA_SHORT_ANSWER_API_ENDPOINT . '?' .
            http_build_query([
                'appid' => getenv('WOLFRAMALPHA_APPID'),
                'i' => $question,
                'timeout' => 3
            ]);
        try {
            $response = $client->request('GET', $url);
        } catch (ServerException $exception) {
            return null;
        }
        if ($response->getStatusCode() === 200) {
            return (string) $response->getBody();
        }
        return null;
    }
}
