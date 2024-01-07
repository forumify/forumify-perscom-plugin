<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Service;

use DateTime;
use Forumify\Core\Repository\SettingRepository;
use Forumify\PerscomPlugin\Perscom\PerscomFactory;

class SyncService
{
    public function __construct(
        private readonly PerscomFactory $perscomFactory,
        private readonly SettingRepository $settingRepository,
    ) { }

    public function sync(): void
    {

        $this->settingRepository->set('perscom.last_synced', (new DateTime())->format('D, d M Y H:i:s'));
    }
}
