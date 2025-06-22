<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Serializer;

use DateTime;
use Forumify\PerscomPlugin\Perscom\Entity\Form;
use Forumify\PerscomPlugin\Perscom\Entity\FormSubmission;
use RuntimeException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class FormSubmissionSerializer implements NormalizerInterface, DenormalizerInterface
{
    public function getSupportedTypes(): array
    {
        return [FormSubmission::class => true, 'perscom_array' => true];
    }

    /**
     * @param FormSubmission $object
     */
    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {
        $data = [...($object->getData() ?? [])];

        $data['form_id'] = $object->getForm()->getId();
        $data['user_id'] = $object->getUser()->getId();

        // NOTE: statuses are not synced with PERSCOM.io until we can update them from the parent submission

        return $data;
    }

    public function supportsNormalization(mixed $data, ?string $format = null): bool
    {
        return $data instanceof FormSubmission && $format === 'perscom_array';
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): FormSubmission
    {
        $isNew = false;
        /** @var FormSubmission|null $submission */
        $submission = $context[AbstractNormalizer::OBJECT_TO_POPULATE] ?? null;
        if ($submission === null) {
            $isNew = true;
            $submission = new FormSubmission();
        }

        /** @var Form|null $form */
        $form = $context['forms'][$data['form_id'] ?? 0] ?? null;
        if ($form === null) {
            throw new RuntimeException("No form could not be found for submission.");
        }

        $user = $context['users'][$data['user_id'] ?? 0] ?? null;
        if ($user === null) {
            throw new RuntimeException('No user could be found for submission.');
        }

        $submission->setPerscomId($data['id']);
        $submission->setCreatedAt(new DateTime($data['created_at']));
        $submission->setForm($form);
        $submission->setUser($user);

        $this->setFormData($submission, $form, $data);

        // NOTE: statuses are only attached in the initial sync, after that, we run detached
        if (!$isNew) {
            return $submission;
        }

        usort($data['statuses'], static fn ($a, $b) => $b['record']['updated_at'] <=> $a['record']['updated_at']);
        $status = $data['statuses'][0] ?? null;
        if ($status !== null) {
            $statusObj = $context['statuses'][$status['id'] ?? 0] ?? null;
            $submission->setStatus($statusObj);
            $submission->setStatusReason($status['record']['text'] ?? null);
        }

        return $submission;
    }

    private function setFormData(FormSubmission $submission, Form $form, array $data): void
    {
        $formData = [];
        foreach ($form->getFields() as $field) {
            $key = $field['key'];
            $fieldData = $data[$key] ?? null;
            if ($fieldData) {
                $formData[$key] = $fieldData;
            }
        }
        $submission->setData($formData);
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null): bool
    {
        return is_array($data) && $type === FormSubmission::class;
    }
}
