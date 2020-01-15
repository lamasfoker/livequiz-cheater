<?php
declare(strict_types=1);

namespace LamasFoker\LiveQuiz\Service;

use Google\ApiCore\ApiException;
use Google\ApiCore\ValidationException;
use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use LamasFoker\LiveQuiz\Exception\VisionApiException;

class TextDetector
{
    /**
     * @param string $filepath
     * @return string
     * @throws VisionApiException
     */
    public function detect(string $filepath): string
    {
        try {
            $imageAnnotator = new ImageAnnotatorClient();
            $image = file_get_contents($filepath);
            $response = $imageAnnotator->textDetection($image);
            $imageAnnotator->close();
            return $response->getFullTextAnnotation()->getText();
        } catch (ValidationException $exception) {
            throw new VisionApiException('Vision API can\'t recognizes text');
        } catch (ApiException $exception) {
            throw new VisionApiException('Vision API can\'t recognizes text');
        }
    }
}