<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Serializer;

use Forumify\PerscomPlugin\Perscom\Entity\Unit;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class UnitSerializer implements DenormalizerInterface
{
    public function getSupportedTypes(): array
    {
        return [
            Unit::class => true,
            'perscom_array' => true,
        ];
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): Unit
    {
        /** @var Unit $unit */
        $unit = $context[AbstractNormalizer::OBJECT_TO_POPULATE] ?? new Unit();

        $unit->setPerscomId($data['id']);
        $unit->setName($data['name'] ?? '');
        $unit->setDescription($data['description'] ?? '');
        $unit->setPosition($data['order'] ?? 0);

        return $unit;
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null): bool
    {
        return is_array($data) && $type === Unit::class;
    }
}
