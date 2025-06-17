<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Serializer;

use Forumify\PerscomPlugin\Perscom\Entity\Rank;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class RankSerializer implements DenormalizerInterface
{
    public function __construct(
        private readonly PerscomImageNormalizer $imageNormalizer,
    ) {
    }

    public function getSupportedTypes(): array
    {
        return [
            Rank::class => true,
            'perscom_array' => true,
        ];
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): Rank
    {
        /** @var Rank $rank */
        $rank = $context[AbstractNormalizer::OBJECT_TO_POPULATE] ?? new Rank();

        $rank->setPerscomId($data['id']);
        $rank->setName($data['name'] ?? '');
        $rank->setDescription($data['description'] ?? '');
        $rank->setAbbreviation($data['abbreviation'] ?? '');
        $rank->setPaygrade($data['paygrade'] ?? '');
        $rank->setPosition($data['order'] ?? 0);

        $this->imageNormalizer->denormalize($data['image'] ?? null, $type, context: [
            AbstractNormalizer::OBJECT_TO_POPULATE => $rank,
        ]);

        return $rank;
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null): bool
    {
        return is_array($data) && $type === Rank::class;
    }
}
