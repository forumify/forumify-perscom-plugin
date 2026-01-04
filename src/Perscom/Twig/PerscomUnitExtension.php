<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Twig;

use Forumify\PerscomPlugin\Perscom\Entity\Position;
use Forumify\PerscomPlugin\Perscom\Entity\Unit;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class PerscomUnitExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('perscom_unit_is_supervisor', $this->isSupervisor(...)),
        ];
    }

    public function isSupervisor(Unit $unit, Position $position): bool
    {
        foreach ($unit->supervisors as $supervisor) {
            if ($supervisor->getId() === $position->getId()) {
                return true;
            }
        }
        return false;
    }
}
