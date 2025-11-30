<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Serializer;

use Forumify\PerscomPlugin\Perscom\Entity\Specialty;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class SpecialtySerializer implements DenormalizerInterface, NormalizerInterface
{
    use SortableNormalizerTrait;

    /**
     * @param Specialty $object
     */
    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {
        $data = [];

        $data['name'] = $object->getName();
        $data['description'] = $object->getDescription();
        $data['abbreviation'] = $object->getAbbreviation();
        $this->normalizePosition($object, $data);

        return $data;
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof Specialty && $format === 'perscom_array';
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            'perscom_array' => true,
            Specialty::class => true,
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

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return is_array($data) && $type === Specialty::class;
    }
}
