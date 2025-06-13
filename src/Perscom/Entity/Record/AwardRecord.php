<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Entity\Record;

use Doctrine\ORM\Mapping as ORM;
use Forumify\PerscomPlugin\Perscom\Entity\Award;

#[ORM\Entity]
#[ORM\Table('perscom_record_award')]
class AwardRecord
{
    use RecordFields;

    #[ORM\ManyToOne(targetEntity: Award::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private Award $award;

    public function getAward(): Award
    {
        return $this->award;
    }

    public function setAward(Award $award): void
    {
        $this->award = $award;
    }
}
