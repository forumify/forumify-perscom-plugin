<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Entity\Record;

use Doctrine\ORM\Mapping as ORM;
use Forumify\PerscomPlugin\Perscom\Entity\Rank;
use Forumify\PerscomPlugin\Perscom\Repository\RankRecordRepository;

#[ORM\Entity(repositoryClass: RankRecordRepository::class)]
#[ORM\Table('perscom_record_rank')]
class RankRecord implements RecordInterface
{
    use RecordFields;

    #[ORM\ManyToOne(targetEntity: Rank::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private Rank $rank;

    #[ORM\Column]
    private string $type = 'promotion';

    public function getRank(): Rank
    {
        return $this->rank;
    }

    public function setRank(Rank $rank): void
    {
        $this->rank = $rank;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }
}
