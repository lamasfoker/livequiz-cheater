<?php
declare(strict_types=1);

namespace LamasFoker\LiveQuiz\Service;

use Google\Cloud\Translate\V2\TranslateClient;
use InvalidArgumentException;
use LamasFoker\LiveQuiz\Exception\TranslationApiException;

class TextTranslator
{
    /**
     * @param string $text
     * @param string $language
     * @return string
     * @throws TranslationApiException
     */
    public function translate(string $text, string $language): string
    {
        try {
            $translate = new TranslateClient();
        } catch (InvalidArgumentException $exception) {
            throw new TranslationApiException('Translation API error');
        }
        $result = $translate->translate($text, ['target' => $language,]);
        return $result['text'];
    }
}
