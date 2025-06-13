<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Serializer;

use Forumify\PerscomPlugin\Perscom\Entity\Specialty;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class SpecialtySerializer implements DenormalizerInterface
{
    public function getSupportedTypes(): array
    {
        return [
            Specialty::class => true,
            'perscom_array' => true,
        ];
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): Specialty
    {
        /** @var Specialty $specialty */
        $specialty = $context[AbstractNormalizer::OBJECT_TO_POPULATE] ?? new Specialty();

        $specialty->setPerscomId($data['id']);
        $specialty->setName($data['name'] ?? '');
        $specialty->setDescription($data['description'] ?? '');
        $specialty->setAbbreviation($data['abbreviation'] ?? '');
        $specialty->setPosition($data['order'] ?? 0);

        return $specialty;
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null): bool
    {
        return is_array($data) && $type === Specialty::class;
    }
}
