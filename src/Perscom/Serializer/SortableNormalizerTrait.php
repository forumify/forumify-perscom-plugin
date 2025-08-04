<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Serializer;

use Forumify\Core\Entity\SortableEntityInterface;
use Forumify\PerscomPlugin\Perscom\Entity\PerscomEntityInterface;

trait SortableNormalizerTrait
{
    private function normalizePosition(SortableEntityInterface&PerscomEntityInterface $object, array &$data): void
    {
        if ($object->getPerscomId() !== null) {
            $data['order'] = $object->getPosition();
        }
    }
}
