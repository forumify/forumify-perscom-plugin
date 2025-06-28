<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Sync\EventSubscriber;

use Forumify\Core\Repository\SettingRepository;
use Forumify\PerscomPlugin\Perscom\Entity\PerscomEntityInterface;
use Forumify\PerscomPlugin\Perscom\Entity\PerscomEntityWithImageInterface;
use Forumify\PerscomPlugin\Perscom\Perscom;
use Forumify\PerscomPlugin\Perscom\Sync\EventSubscriber\Event\PostSyncToPerscomEvent;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use JsonException;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use function Symfony\Component\String\u;

class SyncImageSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly SettingRepository $settingRepository,
        private readonly FilesystemOperator $perscomAssetStorage,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PostSyncToPerscomEvent::class => 'handlePostSync',
        ];
    }

    public function handlePostSync(PostSyncToPerscomEvent $event): void
    {
        $cs = $event->changeSet;
        foreach ($cs['create'] as $createdEntity) {
            $this->handleImageSync($createdEntity);
        }

        foreach ($cs['update'] as $updatedEntity) {
            $this->handleImageSync($updatedEntity);
        }
    }

    private function handleImageSync(PerscomEntityInterface $entity): void
    {
        if (!$entity instanceof PerscomEntityWithImageInterface || !$entity->isImageDirty()) {
            return;
        }

        $image = $entity->getImage();
        if ($image === null) {
            return;
        }

        $endpoint = $entity->getImageEndpoint();
        $imageId = $entity->getImageId();
        $imageName = u($image)->afterLast('/')->toString();
        $imageResource = $this->perscomAssetStorage->readStream($image);

        try {
            $imageId = $this->uploadImage($endpoint, $imageId, $imageName, $imageResource);
            $entity->setImageId($imageId);
        } catch (GuzzleException|JsonException) {
        }
        $entity->setImageDirty(false);
    }

    /**
     * If an image already exists, delete it first, then upload a new now.
     * For some reason PATCHing existing images isn't working.
     *
     * @param resource $image
     * @throws GuzzleException|JsonException
     */
    private function uploadImage(string $endpoint, ?int $imageId, string $imageName, $image): ?int
    {
        $client = $this->getClient();

        if ($imageId !== null) {
            $deleteUrl = $endpoint . '/' . $imageId;
            $client->delete($deleteUrl);
            $imageId = null;
        }

        $response = $client
            ->post(
                $endpoint,
                [
                    'multipart' => [
                        [
                            'name' => 'name',
                            'contents' => $imageName,
                        ],
                        [
                            'name' => 'image',
                            'contents' => $image,
                            'filename' => $imageName,
                        ]
                    ],
                ]
            )
            ->getBody()
            ->getContents()
        ;
        return json_decode($response, true, 512, JSON_THROW_ON_ERROR)['data']['id'] ?? null;
    }

    private function getClient(): Client
    {
        $apiUrl = $this->settingRepository->get('perscom.endpoint') ?? Perscom::$apiUrl;
        $apiUrl = u($apiUrl)->ensureEnd('/')->toString();
        $apiKey = $this->settingRepository->get('perscom.api_key');
        $perscomId = $this->settingRepository->get('perscom.perscom_id');

        $headers = ['Authorization' => "Bearer $apiKey"];
        if ($perscomId) {
            $headers['X-Perscom-Id'] = $perscomId;
        }

        return new Client([
            'base_uri' => $apiUrl,
            'headers' => $headers,
        ]);
    }
}
