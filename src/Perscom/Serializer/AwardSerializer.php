<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Serializer;

use Forumify\PerscomPlugin\Perscom\Entity\Award;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class AwardSerializer implements DenormalizerInterface
{
    public function __construct(
        private readonly PerscomImageNormalizer $imageNormalizer,
    ) {
    }

    public function getSupportedTypes(): array
    {
        return [
            Award::class => true,
            'perscom_array' => true,
        ];
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): Award
    {
        /** @var Award $award */
        $award = $context[AbstractNormalizer::OBJECT_TO_POPULATE] ?? new Award();

        $award->setPerscomId($data['id']);
        $award->setName($data['name'] ?? '');
        $award->setDescription($data['description'] ?? '');
        $award->setPosition($data['order'] ?? 0);

        $this->imageNormalizer->denormalize($data['image'] ?? null, $type, context: [
            AbstractNormalizer::OBJECT_TO_POPULATE => $award,
        ]);

        return $award;
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null): bool
    {
        return is_array($data) && $type === Award::class;
    }
}
