<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Serializer;

use Forumify\PerscomPlugin\Perscom\Entity\Position;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class PositionSerializer implements DenormalizerInterface
{
    public function getSupportedTypes(): array
    {
        return [
            Position::class => true,
            'perscom_array' => true,
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

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null): bool
    {
        return is_array($data) && $type === Position::class;
    }
}
