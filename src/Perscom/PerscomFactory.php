<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom;

use Forumify\Core\Repository\SettingRepository;

class PerscomFactory
{
    public function __construct(private readonly SettingRepository $settingRepository)
    {
    }

    public function getPerscom(): Perscom
    {
        return new Perscom(
            $this->settingRepository->get('perscom.endpoint'),
            $this->settingRepository->get('perscom.api_key'),
            $this->settingRepository->get('perscom.perscom_id'),
        );
    }
}
