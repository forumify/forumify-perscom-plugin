<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Serializer;

use Forumify\PerscomPlugin\Perscom\Entity\Qualification;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class QualificationSerializer implements DenormalizerInterface
{
    public function __construct(
        private readonly PerscomImageNormalizer $imageNormalizer,
    ) {
    }

    public function getSupportedTypes(): array
    {
        return [
            Qualification::class => true,
            'perscom_array' => true,
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

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null): bool
    {
        return is_array($data) && $type === Qualification::class;
    }
}
