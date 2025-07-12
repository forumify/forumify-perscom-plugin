<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Serializer;

use Forumify\PerscomPlugin\Perscom\Entity\Roster;
use Forumify\PerscomPlugin\Perscom\Entity\Unit;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class RosterSerializer implements DenormalizerInterface, NormalizerInterface
{
    /**
     * @param Roster $object
     */
    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {
        $data = [];

        $data['name'] = $object->getName();
        $data['description'] = $object->getDescription();
        $data['order'] = $object->getPosition();

        // NOTE: units are not synced with PERSCOM.io until we can update them from the parent roster

        return $data;
    }

    public function supportsNormalization(mixed $data, ?string $format = null): bool
    {
        return $data instanceof Roster && $format === 'perscom_array';
    }

    public function getSupportedTypes(): array
    {
        return [
            'perscom_array' => true,
            Roster::class => true,
        ];
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): Roster
    {
        $isNew = false;
        /** @var Roster|null $roster */
        $roster = $context[AbstractNormalizer::OBJECT_TO_POPULATE] ?? null;
        if ($roster === null) {
            $isNew = true;
            $roster = new Roster();
        }

        $roster->setPerscomId($data['id']);
        $roster->setName($data['name'] ?? '');
        $roster->setDescription($data['description'] ?? '');
        $roster->setPosition($data['order'] ?? 0);

        // NOTE: since we don't send updates for roster units to PERSCOM.io, we also shouldn't sync them anymore.
        if (!$isNew) {
            return $roster;
        }

        /** @var array<Unit> $allUnits */
        $allUnits = $context['units'] ?? [];
        $rosterUnits = array_column($data['units'] ?? [], 'id');

        foreach ($roster->getUnits() as $unit) {
            if (!in_array($unit->getPerscomId(), $rosterUnits)) {
                $roster->getUnits()->removeElement($unit);
            }
        }

        foreach ($rosterUnits as $rosterUnitId) {
            $unit = $allUnits[$rosterUnitId] ?? null;
            if ($unit === null) {
                continue;
            }

            if (!$roster->getUnits()->contains($unit)) {
                $roster->getUnits()->add($unit);
            }
        }

        return $roster;
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null): bool
    {
        return is_array($data) && $type === Roster::class;
    }
}
