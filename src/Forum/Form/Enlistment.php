<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Form;

use Symfony\Component\Validator\Constraints as Assert;

class Enlistment
{
    public string $email;

    #[Assert\NotBlank]
    public string $firstName;

    #[Assert\NotBlank]
    public string $lastName;

    public array $additionalFormData = [];
}
