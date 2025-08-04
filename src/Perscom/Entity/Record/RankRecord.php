<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Entity\Record;

use Doctrine\ORM\Mapping as ORM;
use Forumify\PerscomPlugin\Perscom\Entity\PerscomUser;
use Forumify\PerscomPlugin\Perscom\Entity\Rank;
use Forumify\PerscomPlugin\Perscom\Perscom;
use Forumify\PerscomPlugin\Perscom\Repository\RankRecordRepository;
use Perscom\Contracts\Batchable;
use Perscom\Contracts\Crudable;

#[ORM\Entity(repositoryClass: RankRecordRepository::class)]
#[ORM\Table('perscom_record_rank')]
class RankRecord implements RecordInterface
{
    use RecordFields;

    #[ORM\ManyToOne(targetEntity: PerscomUser::class, inversedBy: 'rankRecords', fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private PerscomUser $user;

    #[ORM\ManyToOne(targetEntity: Rank::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private Rank $rank;

    #[ORM\Column(length: 16)]
    private string $type = 'promotion';

    public static function getPerscomResource(Perscom $perscom): Batchable|Crudable
    {
        return $perscom->rankRecords();
    }

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
