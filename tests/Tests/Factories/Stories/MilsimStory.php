<?php

declare(strict_types=1);

namespace PluginTests\Factories\Stories;

use Forumify\Core\Entity\MenuItem;
use Forumify\Core\Repository\MenuItemRepository;
use Forumify\Core\Repository\SettingRepository;
use Forumify\PerscomPlugin\Perscom\Entity;
use PluginTests\Factories\Forumify\ForumFactory;
use PluginTests\Factories\Perscom\FormFactory;
use PluginTests\Factories\Perscom\FormFieldFactory;
use PluginTests\Factories\Perscom\PositionFactory;
use PluginTests\Factories\Perscom\RankFactory;
use PluginTests\Factories\Perscom\SpecialtyFactory;
use PluginTests\Factories\Perscom\StatusFactory;
use PluginTests\Factories\Perscom\UnitFactory;
use PluginTests\Factories\Perscom\UserFactory;
use Zenstruck\Foundry\Story;

/**
 * This story sets up PERSCOM organizational resources for a standard milsim unit.
 *
 * @method static Entity\Status statusActiveDuty()
 * @method static Entity\Status statusRetired()
 * @method static Entity\Status statusPending()
 * @method static Entity\Status statusApproved()
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
        $this->addState('statusPending', $pending);
        $approved = StatusFactory::createOne(['name' => 'Approved']);
        $this->addState('statusApproved', $approved);
        StatusFactory::createOne(['name' => 'Denied']);

        $this->settingRepository->set('perscom.enlistment.status', [$retired->getPerscomId()]);

        // Forms
        $enlistmentForm = FormFactory::createOne([
            'defaultStatus' => $pending,
            'instructions' => 'Enlistment Instructions',
            'name' => 'Enlistment',
            'successMessage' => 'Enlistment Success',
        ]);
        FormFieldFactory::createOne([
            'form' => $enlistmentForm,
            'key' => 'reason',
            'label' => 'Why would you like to join our unit',
            'required' => true,
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
        $squadLeader = PositionFactory::createOne(['name' => 'Squad Leader']);
        $teamLeader = PositionFactory::createOne(['name' => 'Team Leader']);
        $riflemanAT = PositionFactory::createOne(['name' => 'Rifleman AT']);
        $this->addState('positionRiflemanAT', $riflemanAT);

        // Specialties
        $infantry = SpecialtyFactory::createOne(['name' => 'Infantryman', 'abbreviation' => '11B']);
        $this->addState('specialtyInfantry', $infantry);

        // Ranks
        $sgt = RankFactory::createOne(['name' => 'Sergeant', 'abbreviation' => 'SGT', 'paygrade' => 'E5']);
        $cpl = RankFactory::createOne(['name' => 'Corporal', 'abbreviation' => 'CPL', 'paygrade' => 'E4']);
        $spc = RankFactory::createOne(['name' => 'Specialist', 'abbreviation' => 'SPC', 'paygrade' => 'E4']);
        $pfc = RankFactory::createOne(['name' => 'Private First Class', 'abbreviation' => 'PFC', 'paygrade' => 'E3']);
        $pv2 = RankFactory::createOne(['name' => 'Private Second Class', 'abbreviation' => 'PV2', 'paygrade' => 'E2']);
        $pvt = RankFactory::createOne(['name' => 'Private Trainee', 'abbreviation' => 'PVT', 'paygrade' => 'E1']);
        $this->addState('rankPVT', $pvt);

        // Users
        $this->createUser($sgt, $firstSquad, $squadLeader, $infantry, $activeDuty);
        $this->createUser($cpl, $firstSquad, $teamLeader, $infantry, $activeDuty);
        $this->createUser($cpl, $firstSquad, $teamLeader, $infantry, $activeDuty);
        $this->createUser($spc, $firstSquad, $riflemanAT, $infantry, $activeDuty);
        $this->createUser($spc, $firstSquad, $riflemanAT, $infantry, $activeDuty);
        $this->createUser($pfc, $firstSquad, $riflemanAT, $infantry, $activeDuty);
        $this->createUser($pfc, $firstSquad, $riflemanAT, $infantry, $activeDuty);
        $this->createUser($pv2, $firstSquad, $riflemanAT, $infantry, $activeDuty);
        $this->createUser($pv2, $firstSquad, $riflemanAT, $infantry, $activeDuty);

        $this->createUser($sgt, $secondSquad, $squadLeader, $infantry, $activeDuty);
        $this->createUser($cpl, $secondSquad, $teamLeader, $infantry, $activeDuty);
        $this->createUser($cpl, $secondSquad, $teamLeader, $infantry, $activeDuty);
        $this->createUser($spc, $secondSquad, $riflemanAT, $infantry, $activeDuty);
        $this->createUser($spc, $secondSquad, $riflemanAT, $infantry, $activeDuty);
        $this->createUser($pfc, $secondSquad, $riflemanAT, $infantry, $activeDuty);
        $this->createUser($pfc, $secondSquad, $riflemanAT, $infantry, $activeDuty);
        $this->createUser($pv2, $secondSquad, $riflemanAT, $infantry, $activeDuty);
        $this->createUser($pv2, $secondSquad, $riflemanAT, $infantry, $activeDuty);
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

    private function createUser($rank, $unit, $position, $specialty, $status): Entity\PerscomUser
    {
        return UserFactory::createOne([
            'rank' => $rank,
            'unit' => $unit,
            'position' => $position,
            'specialty' => $specialty,
            'status' => $status,
        ]);
    }
}
