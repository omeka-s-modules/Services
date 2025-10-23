<?php
namespace Services\Services\Mino;

use Exception;

/**
 * The Mino server.
 */
class Mino
{
    const PENDING_JOB_STATES = ['created', 'active', 'suspended'];
    const FAILED_JOB_STATES = ['failed', 'retry', 'cancelled'];
    const COMPLETED_JOB_STATES = ['completed'];

    protected $services;

    public function __construct($services)
    {
        $this->services = $services;
    }

    /**
     * Submit an upload request to Mino.
     */
    public function upload(string $imageUrl, string $accessToken): string
    {
        $logger = $this->services->get('Omeka\Logger');
        $logger->notice('Submitting upload request ...');

        $body = file_get_contents($imageUrl);
        $checksum = md5_file($imageUrl);
        $contentMd5 = base64_encode(md5_file($imageUrl, true));

        $client = $this->services->get('Omeka\HttpClient')
            ->setMethod('GET')
            ->setUri(sprintf('https://mino.tropy.org/uploads/%s.jpeg', $checksum));
        $headers = $client->getRequest()->getHeaders();
        $headers->addHeaders([
            'Authorization' => sprintf('Bearer %s', $accessToken),
            'X-Content-Length' => strlen($body),
            'X-Content-MD5' => $contentMd5,
            'X-Content-Type' => 'image/jpeg',
        ]);
        $response = $client->send();

        switch ($response->getStatusCode()) {
            case 204:
                $logger->notice('Image already uploaded');
                return sprintf('%s.jpeg', $checksum);
                break;
            case 202:
                $logger->notice('Uploading image to cache ...');
                $imageCacheUrl = $response->getHeaders()->get('Location')->getFieldValue();
                $client = $this->services->get('Omeka\HttpClient')
                    ->setMethod('PUT')
                    ->setUri($imageCacheUrl)
                    ->setRawBody($body);
                $headers = $client->getRequest()->getHeaders();
                $headers->addHeaders([
                    'Content-MD5' => $contentMd5,
                    'Content-Type' => 'image/jpeg',
                ]);
                $response = $client->send();
                if (!$response->isSuccess()) {
                    throw new Exception(sprintf(
                        'Image upload failed with status "%s": %s',
                        $response->getStatusCode(),
                        $response->getContent()
                    ));
                }
                $logger->notice('Image successfully uploaded to cache');
                return sprintf('%s.jpeg', $checksum);
            default:
                throw new Exception(sprintf(
                    'Image upload failed with status "%s": %s',
                    $response->getStatusCode(),
                    $response->getContent()
                ));
        }
    }

    /**
     * Submit a transcription request to Mino.
     */
    public function transcribe(string $image, int $modelId, string $accessToken): array
    {
        $logger = $this->services->get('Omeka\Logger');
        $logger->notice('Submitting transcription request ...');

        $client = $this->services->get('Omeka\HttpClient')
            ->setMethod('POST')
            ->setUri('https://mino.tropy.org/transcription')
            ->setRawBody(json_encode([
                'config' => ['model' => $modelId],
                'images' => [$image],
            ]));
        $headers = $client->getRequest()->getHeaders();
        $headers->addHeaders([
            'Authorization' => sprintf('Bearer %s', $accessToken),
            'Content-Type' => 'application/json',
        ]);
        $response = $client->send();
        if (!$response->isSuccess()) {
            throw new Exception(sprintf(
                'Transcription request failed with status "%s": %s',
                $response->getStatusCode(),
                $response->getContent()
            ));
        }
        $logger->notice('Transcription request successfully submitted');
        return json_decode($response->getContent(), true);
    }

    /**
     * Poll Mino for transcription status.
     */
    public function poll(string $jobId, string $accessToken): array
    {
        $logger = $this->services->get('Omeka\Logger');
        $logger->notice('Polling for transcription ...');

        $client = $this->services->get('Omeka\HttpClient')
            ->setMethod('GET')
            ->setUri(sprintf('https://mino.tropy.org/transcription/%s', $jobId));
        $headers = $client->getRequest()->getHeaders();
        $headers->addHeaders([
            'Authorization' => sprintf('Bearer %s', $accessToken),
        ]);
        $response = $client->send();
        if (!$response->isSuccess()) {
            throw new Exception(sprintf(
                'Poll failed with status "%s": %s',
                $response->getStatusCode(),
                $response->getContent()
            ));
        }
        $content = json_decode($response->getContent(), true);

        $logger->notice(sprintf('Polling done with state "%s"', $content['state']));
        return $content;
    }
}
