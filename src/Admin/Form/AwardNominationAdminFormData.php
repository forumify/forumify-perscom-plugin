<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Form;

use Forumify\PerscomPlugin\Perscom\Entity\AwardNominationData;

class AwardNominationAdminFormData
{
    public AwardNominationData $data;
    public int $status;
}
