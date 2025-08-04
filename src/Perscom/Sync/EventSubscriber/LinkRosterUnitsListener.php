<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Sync\EventSubscriber;

use Forumify\PerscomPlugin\Perscom\Entity\Roster;
use Forumify\PerscomPlugin\Perscom\Entity\Unit;
use Forumify\PerscomPlugin\Perscom\PerscomFactory;
use Forumify\PerscomPlugin\Perscom\Sync\EventSubscriber\Event\PostSyncToPerscomEvent;
use Perscom\Data\ResourceObject;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener]
class LinkRosterUnitsListener
{
    public function __construct(private readonly PerscomFactory $perscomFactory)
    {
    }

    public function __invoke(PostSyncToPerscomEvent $event): void
    {
        ['create' => $created, 'update' => $updated] = $event->changeSet;
        foreach ($created as $item) {
            if (!$item instanceof Roster) {
                continue;
            }
            $this->handleRosterUnits($item);
        }

        foreach ($updated as $item) {
            if (!$item instanceof Roster) {
                continue;
            }
            $this->handleRosterUnits($item);
        }
    }

    private function handleRosterUnits(Roster $roster): void
    {
        $perscomId = $roster->getPerscomId();
        if ($perscomId === null) {
            return;
        }

        $newUnitIds = $roster
            ->getUnits()
            ->map(fn (Unit $unit) => $unit->getPerscomId())
            ->filter(fn (?int $id) => $id !== null)
            ->map(fn (int $id) => new ResourceObject($id))
            ->toArray()
        ;

        $this->perscomFactory->getPerscom()->groups()->units($perscomId)->sync($newUnitIds);
    }
}
