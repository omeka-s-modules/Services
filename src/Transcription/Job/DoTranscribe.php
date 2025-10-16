<?php
namespace Services\Transcription\Job;

use Services\Transcription\Entity\ServicesTranscriptionPage;
use Services\Transcription\Entity\ServicesTranscriptionTranscription;

class DoTranscribe extends AbstractTranscriptionJob
{
    public function perform()
    {
        $entityManager = $this->get('Omeka\EntityManager');
        $apiManager = $this->get('Omeka\ApiManager');
        $fileStore = $this->get('Omeka\File\Store');
        $logger = $this->get('Omeka\Logger');

        $pageIds = $apiManager->search(
            'services_transcription_pages',
            ['project_id' => $this->getProject()->getId()],
            ['returnScalar' => 'id']
        )->getContent();

        foreach (array_chunk($pageIds, 100) as $pageIdsChunk) {
            // Iterate pages.
            foreach ($pageIdsChunk as $pageId) {
                $page = $entityManager
                    ->getRepository(ServicesTranscriptionPage::class)
                    ->find($pageId);

                $logger->notice(sprintf(
                    'Requesting transcription for media %s page %s ...',
                    $page->getMedia()->getId(),
                    $page->getId()
                ));

                $transcription = $entityManager
                    ->getRepository('Services\Transcription\Entity\ServicesTranscriptionTranscription')
                    ->findOneBy(['project' => $this->getProject(), 'page' => $page]);

                if ($transcription) {
                    if ('completed' === $transcription->getJobState()) {
                        $logger->notice('Transcription already completed');
                        continue;
                    }
                    try {
                        $content = $this->poll($transcription->getJobId());
                    } catch (\Exception $e) {
                        $logger->err($e->getMessage());
                        continue;
                    }

                    $transcription->setJobState($content['state']);
                    $transcription->setText($content['output']['text']);
                    $transcription->setData($content['output']['alto']);

                } else {
                    // Submit upload and transcription requests to Mino.
                    $imageUrl = $fileStore->getUri($page->getStoragePath());
                    try {
                        $image = $this->upload($imageUrl);
                        $job = $this->transcribe($image);
                    } catch (\Exception $e) {
                        $logger->err($e->getMessage());
                        continue;
                    }

                    // Create the transcription record.
                    $transcription = new ServicesTranscriptionTranscription;
                    $transcription->setProject($this->getProject());
                    $transcription->setPage($page);
                    $transcription->setJobId($job['id']);
                    $transcription->setJobState($job['state']);

                    $entityManager->persist($transcription);

                    // Sleep for 2 seconds to account for Mino's rate limit.
                    sleep(2);
                }
            }
            $entityManager->flush();
        }
    }

    /**
     * Poll Mino for transcription status.
     */
    public function poll(string $jobId)
    {
        $logger = $this->get('Omeka\Logger');
        $logger->notice('Polling for transcription...');

        $client = $this->get('Omeka\HttpClient')
            ->setMethod('GET')
            ->setUri(sprintf('https://mino.tropy.org/transcription/%s', $jobId));
        $headers = $client->getRequest()->getHeaders();
        $headers->addHeaders([
            'Authorization' => sprintf('Bearer %s', $this->getProject()->getAccessToken()),
        ]);
        $response = $client->send();
        if (!$response->isSuccess()) {
            throw new \Exception(sprintf(
                'Poll failed with status "%s": %s',
                $response->getStatusCode(),
                $response->getContent()
            ));
        }
        $content = json_decode($response->getContent(), true);

        $logger->notice(sprintf('Polling done with state "%s"', $content['state']));
        return $content;
    }

    /**
     * Submit an upload request to Mino.
     */
    public function upload($imageUrl)
    {
        $logger = $this->get('Omeka\Logger');
        $logger->notice('Submitting upload request...');

        $body = file_get_contents($imageUrl);
        $checksum = md5_file($imageUrl);
        $contentMd5 = base64_encode(md5_file($imageUrl, true));

        $client = $this->get('Omeka\HttpClient')
            ->setMethod('GET')
            ->setUri(sprintf('https://mino.tropy.org/uploads/%s.jpeg', $checksum));
        $headers = $client->getRequest()->getHeaders();
        $headers->addHeaders([
            'Authorization' => sprintf('Bearer %s', $this->getProject()->getAccessToken()),
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
                $client = $this->get('Omeka\HttpClient')
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
                    throw new \Exception(sprintf(
                        'Image upload failed with status "%s": %s',
                        $response->getStatusCode(),
                        $response->getContent()
                    ));
                }
                $logger->notice('Image successfully uploaded to cache');
                return sprintf('%s.jpeg', $checksum);
            default:
                throw new \Exception(sprintf(
                    'Image upload failed with status "%s": %s',
                    $response->getStatusCode(),
                    $response->getContent()
                ));
        }
    }

    /**
     * Submit a transcription request to Mino.
     */
    public function transcribe($image)
    {
        $logger = $this->get('Omeka\Logger');
        $logger->notice('Submitting transcription request...');

        $client = $this->get('Omeka\HttpClient')
            ->setMethod('POST')
            ->setUri('https://mino.tropy.org/transcription')
            ->setRawBody(json_encode([
                'config' => ['model' => $this->getProject()->getModelId()],
                'images' => [$image],
            ]));
        $headers = $client->getRequest()->getHeaders();
        $headers->addHeaders([
            'Authorization' => sprintf('Bearer %s', $this->getProject()->getAccessToken()),
            'Content-Type' => 'application/json',
        ]);
        $response = $client->send();
        if (!$response->isSuccess()) {
            throw new \Exception(sprintf(
                'Transcription request failed with status "%s": %s',
                $response->getStatusCode(),
                $response->getContent()
            ));
        }
        $logger->notice('Transcription request successfully submitted');
        return json_decode($response->getContent(), true);
    }
}
