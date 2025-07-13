<?php

declare(strict_types=1);

namespace PluginTests\Factories\Stories;

use Forumify\Core\Entity\MenuItem;
use Forumify\Core\Repository\MenuItemRepository;
use Forumify\Core\Repository\SettingRepository;
use Forumify\PerscomPlugin\Perscom\Entity;
use PluginTests\Factories\Forumify\ForumFactory;
use PluginTests\Factories\Perscom\FormFactory;
use PluginTests\Factories\Perscom\PositionFactory;
use PluginTests\Factories\Perscom\RankFactory;
use PluginTests\Factories\Perscom\SpecialtyFactory;
use PluginTests\Factories\Perscom\StatusFactory;
use PluginTests\Factories\Perscom\UnitFactory;
use Zenstruck\Foundry\Story;

/**
 * This story sets up PERSCOM organizational resources for a standard milsim unit.
 *
 * @method static Entity\Status statusActiveDuty()
 * @method static Entity\Status statusRetired()
 * @method static Entity\Form formEnlistment()
 * @method static Entity\Unit unitFirstSquad()
 * @method static Entity\Unit unitSecondSquad()
 * @method static Entity\Position positionRiflemanAT()
 * @method static Entity\Specialty specialtyInfantry()
 * @method static Entity\Rank rankPVT()
 */
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

        // Statuses
        $activeDuty = StatusFactory::createOne(['name' => 'Active Duty']);
        $this->addState('statusActiveDuty', $activeDuty);
        $retired = StatusFactory::createOne(['name' => 'Retired']);
        $this->addState('statusRetired', $retired);
        $pending = StatusFactory::createOne(['name' => 'Pending']);
        StatusFactory::createOne(['name' => 'Approved']);
        StatusFactory::createOne(['name' => 'Denied']);

        $this->settingRepository->set('perscom.enlistment.status', [$retired->getPerscomId()]);

        // Forms
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
        $this->addState('formEnlistment', $enlistmentForm);

        $enlistmentForum = ForumFactory::createOne(['title' => 'Enlistments']);
        $this->settingRepository->set('perscom.enlistment.forum', $enlistmentForum->getId());

        // Units
        $firstSquad = UnitFactory::createOne(['name' => 'First Squad']);
        $this->addState('unitFirstSquad', $firstSquad);
        $secondSquad = UnitFactory::createOne(['name' => 'Second Squad']);
        $this->addState('unitSecondSquad', $secondSquad);

        // Positions
        $riflemanAT = PositionFactory::createOne(['name' => 'Rifleman AT']);
        $this->addState('positionRiflemanAT', $riflemanAT);

        // Specialties
        $infantry = SpecialtyFactory::createOne(['name' => 'Infantryman', 'abbreviation' => '11B']);
        $this->addState('specialtyInfantry', $infantry);

        // Ranks
        $pvt = RankFactory::createOne(['name' => 'Private Trainee', 'abbreviation' => 'PVT', 'paygrade' => 'E-1']);
        $this->addState('rankPVT', $pvt);
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
