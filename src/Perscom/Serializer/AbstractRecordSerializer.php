<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Serializer;

use DateTime;
use Forumify\PerscomPlugin\Perscom\Entity\Record\RecordInterface;
use Forumify\PerscomPlugin\Perscom\Perscom;
use RuntimeException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

abstract class AbstractRecordSerializer implements NormalizerInterface, DenormalizerInterface
{
    /**
     * @param RecordInterface $object
     * @return array
     */
    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {
        $data = [];

        $data['user_id'] = $object->getUser()->getPerscomId();
        $data['author_id'] = $object->getAuthor()?->getPerscomId();
        $data['document_id'] = $object->getDocument()?->getId();
        $data['text'] = $object->getText();
        $data['created_at'] = $object->getCreatedAt()->format(Perscom::DATE_FORMAT);

        return array_filter($data);
    }

    public function supportsNormalization(mixed $data, ?string $format = null)
    {
        return $data instanceof RecordInterface && $format === 'perscom_array';
    }

    /**
     * @template T of RecordInterface
     * @param class-string<T> $type
     * @return T
     */
    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): RecordInterface
    {
        /** @var RecordInterface $object */
        $object = $context[AbstractNormalizer::OBJECT_TO_POPULATE] ?? new $type();
        if (!is_array($data)) {
            throw new RuntimeException('Unable to denormalize record.');
        }

        $user = $context['users'][$data['user_id'] ?? 0] ?? null;
        if ($user === null) {
            throw new RuntimeException('No user found for record.');
        }

        $object->setPerscomId($data['id']);
        $object->setUser($user);
        $object->setAuthor($context['users'][$data['author_id'] ?? 0] ?? null);
        $object->setDocument($context['documents'][$data['document_id'] ?? 0] ?? null);
        $object->setText($data['text'] ?? '');
        $object->setCreatedAt(new DateTime($data['created_at']));

        return $object;
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null): bool
    {
        return is_array($data) && is_a($type, RecordInterface::class, true);
    }
}
