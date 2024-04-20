<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Form;

class StatusRecord
{
    public array $submission;
    public int $status;
    public string $text;
    public bool $sendNotification = false;
}
