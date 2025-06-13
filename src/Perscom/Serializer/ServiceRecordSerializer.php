<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Serializer;

use Forumify\PerscomPlugin\Perscom\Entity\Record\ServiceRecord;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class ServiceRecordSerializer extends AbstractRecordSerializer implements DenormalizerInterface
{
    public function getSupportedTypes(): array
    {
        return [
            ServiceRecord::class => true,
            'perscom_array' => true,
        ];
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): ServiceRecord
    {
        $record = parent::denormalize($data, $type, $format, $context);

        return $record;
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null): bool
    {
        return parent::supportsDenormalization($data, $type, $format) && $type === ServiceRecord::class;
    }
}
