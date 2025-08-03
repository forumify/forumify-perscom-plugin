<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom;

use Forumify\Core\Repository\SettingRepository;

class PerscomFactory
{
    private ?Perscom $perscom = null;

    public function __construct(
        private readonly SettingRepository $settingRepository,
    ) {
    }

    public function getPerscom(): Perscom
    {
        if ($this->perscom !== null) {
            return $this->perscom;
        }

        $this->perscom = new Perscom(
            (string)$this->settingRepository->get('perscom.api_key'),
            (string)$this->settingRepository->get('perscom.endpoint') ?: null,
        );
        return $this->perscom;
    }
}
