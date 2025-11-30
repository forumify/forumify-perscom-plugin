<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Serializer;

use Forumify\PerscomPlugin\Perscom\Entity\Position;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class PositionSerializer implements DenormalizerInterface, NormalizerInterface
{
    use SortableNormalizerTrait;

    /**
     * @param Position $object
     */
    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {
        $data = [];

        $data['name'] = $object->getName();
        $data['description'] = $object->getDescription();
        $this->normalizePosition($object, $data);

        return $data;
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof Position && $format === 'perscom_array';
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            'perscom_array' => true,
            Position::class => true,
        ];
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): Position
    {
        /** @var Position $position */
        $position = $context[AbstractNormalizer::OBJECT_TO_POPULATE] ?? new Position();

        $position->setPerscomId($data['id']);
        $position->setName($data['name'] ?? '');
        $position->setDescription($data['description'] ?? '');
        $position->setPosition($data['order'] ?? 0);

        return $position;
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return is_array($data) && $type === Position::class;
    }
}
