<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Serializer;

use Forumify\PerscomPlugin\Perscom\Entity\Document;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class DocumentSerializer implements DenormalizerInterface
{
    public function getSupportedTypes(): array
    {
        return [
            Document::class => true,
            'perscom_array' => true,
        ];
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): Document
    {
        /** @var Document $document */
        $document = $context[AbstractNormalizer::OBJECT_TO_POPULATE] ?? new Document();

        $document->setPerscomId($data['id']);
        $document->setName($data['name'] ?? '');
        $document->setDescription($data['description'] ?? '');
        $document->setContent($data['dontent'] ?? '');

        return $document;
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null)
    {
        return is_array($data) && $type === Document::class;
    }
}
