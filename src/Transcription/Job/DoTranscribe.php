<?php
namespace Services\Transcription\Job;

use Omeka\Job\AbstractJob;
use Omeka\Job\Exception;
use Services\Transcription\Entity\ServicesTranscriptionPage;
use Services\Transcription\Entity\ServicesTranscriptionTranscription;

class DoTranscribe extends AbstractJob
{
    protected $project;

    public function perform()
    {
        $entityManager = $this->getServiceLocator()->get('Omeka\EntityManager');
        $apiManager = $this->getServiceLocator()->get('Omeka\ApiManager');
        $fileStore = $this->getServiceLocator()->get('Omeka\File\Store');
        $logger = $this->getServiceLocator()->get('Omeka\Logger');

        // Get the project.
        $project = $entityManager
            ->getRepository('Services\Transcription\Entity\ServicesTranscriptionProject')
            ->find($this->getArg('project_id'));

        $pageIds = $apiManager->search(
            'services_transcription_pages',
            ['services_transcription_project_id' => $project->getId()],
            ['returnScalar' => 'id']
        )->getContent();

        foreach (array_chunk($pageIds, 100) as $pageIdsChunk) {
            // Iterate pages.
            foreach ($pageIdsChunk as $pageId) {
                $page = $entityManager
                    ->getRepository(ServicesTranscriptionPage::class)
                    ->find($pageId);
                $transcription = $entityManager
                    ->getRepository('Services\Transcription\Entity\ServicesTranscriptionTranscription')
                    ->findBy(['project' => $project, 'page' => $page]);
                if (!$transcription) {
                    // The transcription does not exist. Create it.
                    $transcription = new ServicesTranscriptionTranscription;
                    $transcription->setProject($project);
                    $transcription->setPage($page);
                    $entityManager->persist($transcription);
                }

                $uploadImage = $this->upload(
                    $fileStore->getUri(sprintf('large/%s.jpg', $page->getStorageId())),
                    $project->getAccessToken()
                );
                if (false === $uploadImage) {
                    continue;
                }
                $logger->notice($uploadImage);

                // @todo: initiate transcription https://mino.tropy.org/transcription
            }
            $entityManager->flush();
        }
    }

    public function upload($imageUri, $accessToken)
    {
        $logger = $this->getServiceLocator()->get('Omeka\Logger');

        $body = file_get_contents($imageUri);
        $checksum = md5_file($imageUri);
        $contentMd5 = base64_encode(md5_file($imageUri, true));

        $client = $this->getServiceLocator()
            ->get('Omeka\HttpClient')
            ->setMethod('GET')
            ->setUri(sprintf('https://mino.tropy.org/uploads/%s.jpeg',  $checksum));
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
                $logger->notice('Image already cached');
                return sprintf('%s.jpeg', $checksum);
                break;
            case 202:
                $logger->notice('Uploading image to cache');
                $imageCacheUrl = $response->getHeaders()->get('Location')->getFieldValue();
                $client = $this->getServiceLocator()
                    ->get('Omeka\HttpClient')
                    ->setMethod('PUT')
                    ->setUri($imageCacheUrl)
                    ->setRawBody($body);
                $headers = $client->getRequest()->getHeaders();
                $headers->addHeaders([
                    'X-Content-MD5' => $contentMd5,
                    'X-Content-Type' => 'image/jpeg',
                ]);
                $response = $client->send();
                if ($response->isSuccess()) {
                    return sprintf('%s.jpeg', $checksum);
                } else {
                    $logger->err(sprintf(
                        'Image upload failed with status "%s": %s',
                        $response->getStatusCode(),
                        $response->getContent()
                    ));
                    return false;
                }
                break;
            default:
                $logger->err(sprintf(
                    'Image upload failed with status "%s": %s',
                    $response->getStatusCode(),
                    $response->getContent()
                ));
                return false;
        }

    }
}
