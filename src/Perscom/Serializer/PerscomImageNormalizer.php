<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Serializer;

use Forumify\PerscomPlugin\Perscom\Entity\PerscomEntityWithImageInterface;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

use function Symfony\Component\String\u;

/**
 * Perscom Image normalizer, should only be used directly, and not through Serializer.
 */
class PerscomImageNormalizer implements DenormalizerInterface
{
    public function __construct(private readonly FilesystemOperator $perscomAssetStorage)
    {
    }

    public function getSupportedTypes(?string $format): array
    {
        return [];
    }

    public function denormalize(
        mixed $data,
        string $type,
        ?string $format = null,
        array $context = [],
    ): ?PerscomEntityWithImageInterface {
        $object = $context[AbstractNormalizer::OBJECT_TO_POPULATE] ?? null;
        assert(is_array($data));
        assert($object instanceof PerscomEntityWithImageInterface);

        $imageId = $data['id'] ?? null;
        if ($object->getImageId() !== null && $imageId === null && $object->getImage()) {
            $this->perscomAssetStorage->delete($object->getImage());
            $object->setImageId(null);
            $object->setImage(null);
            return $object;
        }

        if ($object->getImageId() === $imageId) {
            return $object;
        }

        $source = $data['image_url'] ?? null;
        if (empty($source)) {
            return $object;
        }

        $imageData = file_get_contents($source);
        if ($imageData === false) {
            return $object;
        }

        $directory = u($type)->afterLast('\\')->snake()->toString();
        $path = u($source)->afterLast('/')->toString();
        $imagePath = $directory . '/' . $path;
        $this->perscomAssetStorage->write($imagePath, $imageData);

        $object->setImageId($imageId);
        $object->setImage($imagePath);
        $object->setImageDirty(false);

        return $object;
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return false;
    }
}
