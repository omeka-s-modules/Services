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
            ['services_transcription_project_id' => $this->getProject()->getId()],
            ['returnScalar' => 'id']
        )->getContent();

        foreach (array_chunk($pageIds, 100) as $pageIdsChunk) {
            // Iterate pages.
            foreach ($pageIdsChunk as $pageId) {
                $page = $entityManager
                    ->getRepository(ServicesTranscriptionPage::class)
                    ->find($pageId);

                $logger->notice(sprintf(
                    'Initiating transcription for media ID %s page ID %s',
                    $page->getMedia()->getId(),
                    $page->getId()
                ));

                $transcription = $entityManager
                    ->getRepository('Services\Transcription\Entity\ServicesTranscriptionTranscription')
                    ->findOneBy(['project' => $this->getProject(), 'page' => $page]);
                if (!$transcription) {
                    // The transcription record does not exist. Create it.
                    $transcription = new ServicesTranscriptionTranscription;
                    $transcription->setProject($this->getProject());
                    $transcription->setPage($page);
                    $entityManager->persist($transcription);
                }

                if (null !== $transcription->getJobState()) {
                    $logger->notice(sprintf(
                        'Transcription already initiated with state "%s"',
                        $transcription->getJobState()
                    ));
                    continue;
                }

                // Submit upload and transcription requests to Mino.
                $imageUrl = $fileStore->getUri(sprintf('large/%s.jpg', $page->getStorageId()));
                $image = $this->upload($imageUrl);
                if (false === $image) {
                    continue;
                }
                $job = $this->transcribe($image);
                if (false === $job) {
                    continue;
                }
                $transcription->setJobId($job['id']);
                $transcription->setJobState($job['state']);
            }
            $entityManager->flush();
        }
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
                $logger->notice('Uploading image to cache...');
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
                    $logger->err(sprintf(
                        'Image upload failed with status "%s": %s',
                        $response->getStatusCode(),
                        $response->getContent()
                    ));
                    return false;
                }
                $logger->notice('Image successfully uploaded to cache');
                return sprintf('%s.jpeg', $checksum);
            default:
                $logger->err(sprintf(
                    'Image upload failed with status "%s": %s',
                    $response->getStatusCode(),
                    $response->getContent()
                ));
                return false;
        }
    }

    /**
     * Submit a transcription request to Mino.
     */
    public function transcribe($image)
    {
        $logger = $this->get('Omeka\Logger');
        $logger->notice('Submitting transcription request...');

        // Transcribe
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
            $logger->err(sprintf(
                'Transcription request failed with status "%s": %s',
                $response->getStatusCode(),
                $response->getContent()
            ));
            return false;
        }
        $logger->notice('Transcription request successfully submitted');
        return json_decode($response->getContent(), true);
    }
}
