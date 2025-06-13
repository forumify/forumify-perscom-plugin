<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Entity\Record;

use Doctrine\ORM\Mapping as ORM;
use Forumify\PerscomPlugin\Perscom\Repository\CombatRecordRepository;

#[ORM\Entity(repositoryClass: CombatRecordRepository::class)]
#[ORM\Table('perscom_record_combat')]
class CombatRecord implements RecordInterface
{
    use RecordFields;
}
