<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom;

use Forumify\Core\Repository\SettingRepository;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class PerscomFactory
{
    private ?Perscom $perscom = null;

    public function __construct(
        private readonly SettingRepository $settingRepository,
        #[Autowire('%kernel.environment%')]
        private readonly string $env
    ) {
    }

    public function getPerscom(bool $bypassCache = false): Perscom
    {
        if ($this->perscom !== null) {
            return $this->perscom;
        }

        $this->perscom = new Perscom(
            (string)$this->settingRepository->get('perscom.endpoint'),
            (string)$this->settingRepository->get('perscom.api_key'),
            (string)$this->settingRepository->get('perscom.perscom_id'),
            $this->env === 'dev' ? true : $bypassCache,
        );
        return $this->perscom;
    }
}
