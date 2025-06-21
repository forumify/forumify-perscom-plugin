<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Serializer;

use Forumify\PerscomPlugin\Perscom\Entity\Record\AwardRecord;
use RuntimeException;

class AwardRecordSerializer extends AbstractRecordSerializer
{
    /**
     * @param AwardRecord $object
     * @return array
     */
    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {
        $data = parent::normalize($object, $format, $context);

        $data['award_id'] = $object->getAward()->getPerscomId();

        return $data;
    }

    public function supportsNormalization(mixed $data, ?string $format = null)
    {
        return parent::supportsNormalization($data, $format) && $data instanceof AwardRecord;
    }


    public function getSupportedTypes(): array
    {
        return [
            AwardRecord::class => true,
            'perscom_array' => true,
        ];
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): AwardRecord
    {
        /** @var AwardRecord $record */
        $record = parent::denormalize($data, $type, $format, $context);

        $award = $context['awards'][$data['award_id']] ?? null;
        if ($award === null) {
            throw new RuntimeException('No award found for award record.');
        }

        $record->setAward($award);

        return $record;
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null): bool
    {
        return parent::supportsDenormalization($data, $type, $format) && $type === AwardRecord::class;
    }
}
