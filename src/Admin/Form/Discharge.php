<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Form;

use Forumify\PerscomPlugin\Perscom\Entity\Position;
use Forumify\PerscomPlugin\Perscom\Entity\Rank;
use Forumify\PerscomPlugin\Perscom\Entity\Status;
use Forumify\PerscomPlugin\Perscom\Entity\Unit;

class Discharge
{
    public string $type;

    public ?string $reason = null;

    public ?rank $rank = null;

    public ?unit $unit = null;

    public ?position $position = null;

    public ?status $status = null;
}
