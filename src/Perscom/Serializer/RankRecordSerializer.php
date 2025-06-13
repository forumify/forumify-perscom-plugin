<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Serializer;

use Forumify\PerscomPlugin\Perscom\Entity\Record\RankRecord;
use RuntimeException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class RankRecordSerializer extends AbstractRecordSerializer implements DenormalizerInterface
{
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
