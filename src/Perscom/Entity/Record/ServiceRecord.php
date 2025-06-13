<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Entity\Record;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table('perscom_record_service')]
class ServiceRecord
{
    use RecordFields;
}
