<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Serializer;

use Forumify\PerscomPlugin\Perscom\Entity\Record\CombatRecord;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class CombatRecordSerializer extends AbstractRecordSerializer implements DenormalizerInterface
{
    public function getSupportedTypes(): array
    {
        return [
            CombatRecord::class => true,
            'perscom_array' => true,
        ];
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): CombatRecord
    {
        $record = parent::denormalize($data, $type, $format, $context);

        return $record;
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null): bool
    {
        return parent::supportsDenormalization($data, $type, $format) && $type === CombatRecord::class;
    }
}
