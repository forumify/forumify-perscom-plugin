<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Message;

use Forumify\PerscomPlugin\Perscom\Service\SyncUserService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(handles: SyncUserMessage::class)]
class SyncUserMessageHandler
{
    public function __construct(
        private readonly SyncUserService $syncUserService,
    ) {
    }

    public function __invoke(SyncUserMessage $message): void
    {
        $this->syncUserService->doSync($message);
    }
}
