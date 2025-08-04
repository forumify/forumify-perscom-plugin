<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Serializer;

use Forumify\PerscomPlugin\Perscom\Entity\Record\CombatRecord;

class CombatRecordSerializer extends AbstractRecordSerializer
{
    public function getSupportedTypes(): array
    {
        return [
            'perscom_array' => true,
            CombatRecord::class => true,
        ];
    }

    public function supportsNormalization(mixed $data, ?string $format = null)
    {
        return parent::supportsNormalization($data, $format) && $data instanceof CombatRecord;
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null): bool
    {
        return parent::supportsDenormalization($data, $type, $format) && $type === CombatRecord::class;
    }
}
