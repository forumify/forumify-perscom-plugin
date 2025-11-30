<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Serializer;

use Forumify\PerscomPlugin\Perscom\Entity\Qualification;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class QualificationSerializer implements DenormalizerInterface, NormalizerInterface
{
    use SortableNormalizerTrait;

    public function __construct(
        private readonly PerscomImageNormalizer $imageNormalizer,
    ) {
    }

    /**
     * @param Qualification $object
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
        return $data instanceof Qualification && $format === 'perscom_array';
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            'perscom_array' => true,
            Qualification::class => true,
        ];
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): Qualification
    {
        /** @var Qualification $qualification */
        $qualification = $context[AbstractNormalizer::OBJECT_TO_POPULATE] ?? new Qualification;

        $qualification->setPerscomId($data['id']);
        $qualification->setName($data['name'] ?? '');
        $qualification->setDescription($data['description'] ?? '');
        $qualification->setPosition($data['order'] ?? 0);

        $this->imageNormalizer->denormalize($data['image'] ?? null, $type, context: [
            AbstractNormalizer::OBJECT_TO_POPULATE => $qualification,
        ]);

        return $qualification;
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return is_array($data) && $type === Qualification::class;
    }
}
