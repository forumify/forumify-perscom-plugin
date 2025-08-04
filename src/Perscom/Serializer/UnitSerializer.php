<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Serializer;

use Forumify\PerscomPlugin\Perscom\Entity\Unit;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class UnitSerializer implements DenormalizerInterface, NormalizerInterface
{
    use SortableNormalizerTrait;

    /**
     * @param Unit $object
     */
    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {
        $data = [];

        $data['name'] = $object->getName();
        $data['description'] = $object->getDescription();
        $this->normalizePosition($object, $data);

        return $data;
    }

    public function supportsNormalization(mixed $data, ?string $format = null): bool
    {
        return $data instanceof Unit && $format === 'perscom_array';
    }

    public function getSupportedTypes(): array
    {
        return [
            'perscom_array' => true,
            Unit::class => true,
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
