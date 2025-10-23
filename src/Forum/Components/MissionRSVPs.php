<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Components;

use Forumify\PerscomPlugin\Perscom\Entity\Mission;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('Perscom\\MissionStats', '@ForumifyPerscomPlugin/frontend/components/mission_stats.html.twig')]
class MissionRSVPs
{
    public Mission $mission;

    public function getGoing(): int
    {
        $count = 0;

        foreach ($this->mission->getRsvps() as $rsvp) {
            if ($rsvp->isGoing() === true) {
                $count++;
            }
        }

        return $count;
    }

    public function getAbsent(): int
    {
        $count = 0;

        foreach ($this->mission->getRsvps() as $rsvp) {
            if ($rsvp->isGoing() === false) {
                $count++;
            }
        }

        return $count;
    }
}
