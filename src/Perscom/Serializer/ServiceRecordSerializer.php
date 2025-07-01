<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Serializer;

use Forumify\PerscomPlugin\Perscom\Entity\Record\ServiceRecord;

class ServiceRecordSerializer extends AbstractRecordSerializer
{
    public function getSupportedTypes(): array
    {
        return [
            'perscom_array' => true,
            ServiceRecord::class => true,
        ];
    }

    public function supportsNormalization(mixed $data, ?string $format = null)
    {
        return parent::supportsNormalization($data, $format) && $data instanceof ServiceRecord;
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null): bool
    {
        return parent::supportsDenormalization($data, $type, $format) && $type === ServiceRecord::class;
    }
}
