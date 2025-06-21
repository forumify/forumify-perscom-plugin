<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Serializer;

use Forumify\PerscomPlugin\Perscom\Entity\Record\QualificationRecord;
use RuntimeException;

class QualificationRecordSerializer extends AbstractRecordSerializer
{
    /**
     * @param QualificationRecord $object
     * @return array
     */
    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {
        $data = parent::normalize($object, $format, $context);

        $data['qualification_id'] = $object->getQualification()->getPerscomId();

        return $data;
    }

    public function supportsNormalization(mixed $data, ?string $format = null)
    {
        return parent::supportsNormalization($data, $format) && $data instanceof QualificationRecord;
    }

    public function getSupportedTypes(): array
    {
        return [
            QualificationRecord::class => true,
            'perscom_array' => true,
        ];
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): QualificationRecord
    {
        /** @var QualificationRecord $record */
        $record = parent::denormalize($data, $type, $format, $context);

        $qualification = $context['qualifications'][$data['qualification_id']] ?? null;
        if ($qualification === null) {
            throw new RuntimeException('No qualification found for qualification record');
        }

        $record->setQualification($qualification);

        return $record;
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null): bool
    {
        return parent::supportsDenormalization($data, $type, $format) && $type === QualificationRecord::class;
    }
}
