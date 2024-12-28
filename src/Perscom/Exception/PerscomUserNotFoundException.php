<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Exception;

class PerscomUserNotFoundException extends PerscomException
{
    public function __construct(
        ?string $message = null,
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message ?? 'No perscom account found for the current logged in user.', $code, $previous);
    }
}
