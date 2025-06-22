<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Serializer;

use DateTime;
use Forumify\PerscomPlugin\Perscom\Entity\Form;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class FormSerializer implements NormalizerInterface, DenormalizerInterface
{
    public function getSupportedTypes(): array
    {
        return [Form::class => true, 'perscom_array' => true];
    }

    /**
     * @param Form $object
     */
    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {
        $data = [];

        $data['name'] = $object->getName();
        $data['description'] = $object->getDescription();
        $data['success_message'] = $object->getSuccessMessage();
        $data['submission_status_id'] = $object->getDefaultStatus()?->getId();
        $data['is_public'] = false;
        $data['instructions'] = $object->getInstructions();
        $data['fields'] = $object->getFields();

        return $data;
    }

    public function supportsNormalization(mixed $data, ?string $format = null)
    {
        return $data instanceof Form && $format === 'perscom_array';
    }

    /**
     * @param array<string, mixed> $data
     */
    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): Form
    {
        /** @var Form $form */
        $form = $context[AbstractNormalizer::OBJECT_TO_POPULATE] ?? new Form();

        $form->setPerscomId($data['id']);
        $form->setCreatedAt(new DateTime($data['created_at']));
        $form->setName($data['name']);
        $form->setDescription($data['description'] ?? '');
        $form->setSuccessMessage($data['success_message'] ?? '');
        $form->setInstructions($data['instructions'] ?? '');
        $form->setFields($data['fields'] ?? []);

        if ($data['submission_status_id']) {
            $form->setDefaultStatus($context['statuses'][$data['submission_status_id']] ?? null);
        }

        return $form;
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null)
    {
        return is_array($data) && $type === Form::class;
    }
}
