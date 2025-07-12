<?php

declare(strict_types=1);

namespace PluginTests\Factories\Stories;

use Forumify\Core\Entity\MenuItem;
use Forumify\Core\Repository\MenuItemRepository;
use Forumify\Core\Repository\SettingRepository;
use PluginTests\Factories\Perscom\FormFactory;
use PluginTests\Factories\Perscom\StatusFactory;
use Zenstruck\Foundry\Story;

class MilsimStory extends Story
{
    public function __construct(
        private readonly SettingRepository $settingRepository,
        private readonly MenuItemRepository $menuItemRepository,
    ) {
    }

    public function build(): void
    {
        $this->createPerscomMenu();

        // Soldier Statuses
        StatusFactory::createOne(['name' => 'Active Duty']);
        StatusFactory::createOne(['name' => 'Retired']);

        // Form Statuses
        $pending = StatusFactory::createOne(['name' => 'Pending']);
        StatusFactory::createOne(['name' => 'Approved']);
        StatusFactory::createOne(['name' => 'Denied']);

        $enlistmentForm = FormFactory::createOne([
            'defaultStatus' => $pending,
            'fields' => [[
                'help' => '',
                'key' => 'reason',
                'name' => 'Why would you like to join our unit?',
                'readonly' => false,
                'required' => true,
                'type' => 'text',
            ]],
            'instructions' => 'Enlistment Instructions',
            'successMessage' => 'Enlistment Success',
        ]);
        $this->settingRepository->set('perscom.enlistment.form', $enlistmentForm->getPerscomId());
    }

    private function createPerscomMenu(): void
    {
        $perscomMenu = new MenuItem();
        $perscomMenu->setName('PERSCOM');
        $perscomMenu->setType('perscom');
        $perscomMenu->setPayload([
            'awards_active_duty' => true,
            'awards_guests' => true,
            'courses_active_duty' => true,
            'courses_guests' => true,
            'operations_active_duty' => true,
            'operations_guests' => true,
            'qualifications_active_duty' => true,
            'qualifications_guests' => true,
            'ranks_active_duty' => true,
            'ranks_guests' => true,
            'roster_active_duty' => true,
            'roster_guests' => true,
        ]);
        $this->menuItemRepository->save($perscomMenu);
    }
}
