<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Serializer;

use Forumify\PerscomPlugin\Perscom\Entity\Status;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class StatusSerializer implements DenormalizerInterface
{
    public function getSupportedTypes(): array
    {
        return [
            Status::class => true,
            'perscom_array' => true,
        ];
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): Status
    {
        /** @var Status $status */
        $status = $context[AbstractNormalizer::OBJECT_TO_POPULATE] ?? new Status();

        $status->setPerscomId($data['id']);
        $status->setName($data['name'] ?? '');
        $status->setColor($data['color'] ?? '');
        $status->setPosition($data['order'] ?? 0);

        return $status;
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null): bool
    {
        return is_array($data) && $type === Status::class;
    }
}
