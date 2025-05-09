<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Entity;

class AwardNominationData
{
    public AwardNomination $nomination;
    public Array $requesterItem;
    public Array $receiverItem;
    public Array $awardItem;
    public Array $statusItem;
    public ?Array $updatedByItem = null;
}
