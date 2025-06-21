<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Serializer;

use Forumify\PerscomPlugin\Perscom\Entity\Record\AssignmentRecord;

class AssignmentRecordSerializer extends AbstractRecordSerializer
{
    /**
     * @param AssignmentRecord $object
     * @return array
     */
    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {
        $data = parent::normalize($object, $format, $context);

        $data['type'] = $object->getType();
        $data['position_id'] = $object->getPosition()?->getPerscomId();
        $data['specialty_id'] = $object->getSpecialty()?->getPerscomId();
        $data['status_id'] = $object->getStatus()?->getPerscomId();
        $data['unit_id'] = $object->getUnit()?->getPerscomId();

        return $data;
    }

    public function supportsNormalization(mixed $data, ?string $format = null)
    {
        return parent::supportsNormalization($data, $format) && $data instanceof AssignmentRecord;
    }

    public function getSupportedTypes(): array
    {
        return [
            AssignmentRecord::class => true,
            'perscom_array' => true,
        ];
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): AssignmentRecord
    {
        /** @var AssignmentRecord $record */
        $record = parent::denormalize($data, $type, $format, $context);

        $record->setPosition($context['positions'][$data['position_id'] ?? 0] ?? null);
        $record->setSpecialty($context['specialties'][$data['specialty_id'] ?? 0] ?? null);
        $record->setStatus($context['statuses'][$data['status_id'] ?? 0] ?? null);
        $record->setUnit($context['units'][$data['unit_id'] ?? 0] ?? null);
        $record->setType($data['type'] ?? 'primary');

        return $record;
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null): bool
    {
        return parent::supportsDenormalization($data, $type, $format) && $type === AssignmentRecord::class;
    }
}
