<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Serializer;

use Forumify\PerscomPlugin\Perscom\Entity\Record\RankRecord;
use RuntimeException;

class RankRecordSerializer extends AbstractRecordSerializer
{
    /**
     * @param RankRecord $object
     * @return array
     */
    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {
        $data = parent::normalize($object, $format, $context);

        $data['type'] = $object->getType() === 'promotion' ? 0 : 1;
        $data['rank_id'] = $object->getRank()->getPerscomId();

        return $data;
    }

    public function supportsNormalization(mixed $data, ?string $format = null)
    {
        return parent::supportsNormalization($data, $format) && $data instanceof RankRecord;
    }

    public function getSupportedTypes(): array
    {
        return [
            RankRecord::class => true,
            'perscom_array' => true,
        ];
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): RankRecord
    {
        /** @var RankRecord $record */
        $record = parent::denormalize($data, $type, $format, $context);

        $rank = $context['ranks'][$data['rank_id'] ?? 0] ?? null;
        if ($rank === null) {
            throw new RuntimeException('No rank found for rank record.');
        }
        $record->setRank($rank);
        $record->setType(($data['type'] ?? 0) === 0 ? 'promotion' : 'demotion');

        return $record;
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null): bool
    {
        return parent::supportsDenormalization($data, $type, $format) && $type === RankRecord::class;
    }
}
