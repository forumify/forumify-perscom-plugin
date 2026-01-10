<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Entity;

interface AssignmentInterface
{
    public function getPosition(): ?Position;

    public function getStatus(): ?Status;

    public function getSpecialty(): ?Specialty;

    public function getUnit(): ?Unit;
}
