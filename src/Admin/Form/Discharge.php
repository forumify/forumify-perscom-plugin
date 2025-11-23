<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Form;

use Forumify\PerscomPlugin\Perscom\Entity\PerscomUser;
use Forumify\PerscomPlugin\Perscom\Entity\Position;
use Forumify\PerscomPlugin\Perscom\Entity\Rank;
use Forumify\PerscomPlugin\Perscom\Entity\Status;
use Forumify\PerscomPlugin\Perscom\Entity\Unit;

class Discharge
{
    public string $type;
    public ?string $reason = null;
    public ?Rank $rank = null;
    public ?Unit $unit = null;
    public ?Position $position = null;
    public ?Status $status = null;

    public function __construct(
        public readonly PerscomUser $user,
    ) {
        $this->status = $user->getStatus();
        $this->rank = $user->getRank();
        $this->unit = $user->getUnit();
        $this->position = $user->getPosition();
    }
}
