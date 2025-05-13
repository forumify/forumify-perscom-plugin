<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Components;

use DateTime;
use Forumify\PerscomPlugin\Perscom\Form\UnitType;
use Forumify\PerscomPlugin\Perscom\Repository\AfterActionReportRepository;
use Forumify\PerscomPlugin\Perscom\Service\AfterActionReportService;
use Forumify\Plugin\Attribute\PluginVersion;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('Perscom\\AttendanceSheet', '@ForumifyPerscomPlugin/frontend/components/attendance_sheet.html.twig')]
#[PluginVersion('forumify/forumify-perscom-plugin', 'premium')]
#[IsGranted('perscom-io.frontend.attendance_sheet.view')]
class AttendanceSheet extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    public ?string $error = null;
    public ?array $units = null;
    public ?array $missions = null;
    public ?array $users = null;
    public ?array $sheet = null;

    public function __construct(
        private readonly AfterActionReportRepository $aarRepository,
        private readonly AfterActionReportService $aarService,
    ) {
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createFormBuilder()
            ->add('from', DateType::class, [
                'widget' => 'single_text',
                'data' => (new DateTime())->sub(new \DateInterval('P1M')),
            ])
            ->add('to', DateType::class, [
                'widget' => 'single_text',
                'data' => new DateTime(),
            ])
            ->add('unit', UnitType::class, [
                'autocomplete' => true,
                'multiple' => true,
                'required' => false,
                'help' => 'Leave empty to calculate attendance for all units that have at least 1 after action report in the selected time period.',
            ])
            ->getForm()
        ;
    }

    // @phpstan-ignore-next-line
    private function getDataModelValue(): ?string
    {
        return 'norender|*';
    }

    #[LiveAction]
    public function calculate(): void
    {
        $this->submitForm();

        /** @var array{from: DateTime, to: DateTime, unit: int[]} $data */
        $data = $this->getForm()->getData();

        $data['from']->setTime(0, 0, 0);
        $data['to']->setTime(23, 59, 59);

        $diff = (int)$data['from']->diff($data['to'])->format('%r%a');
        if ($diff <= 0) {
            $this->error = 'You have selected an invalid from/to range. It can not be negative.';
            return;
        }

        if ($diff > 6 * 31) {
            $this->error = 'You have selected an invalid from/to range. It can not be larger than 6 months.';
            return;
        }

        $aars = $this->aarRepository->findByMissionStartAndUnit($data['from'], $data['to'], $data['unit']);
        $missions = [];
        $units = [];

        foreach ($aars as $aar) {
            $mission = $aar->getMission();
            $missions[$mission->getId()] = $mission;

            $unitId = $aar->getUnitId();
            $units[$unitId] = [
                'id' => $unitId,
                'name' => $aar->getUnitName(),
                'position' => $aar->getUnitPosition(),
            ];
        }
        uasort($units, fn (array $a, array $b): int => $a['position'] <=> $b['position']);

        $users = [];
        foreach ($units as $unit) {
            $users[$unit['id']] = $this->aarService->findUsersByUnit($unit['id']);
        }

        $sheetData = [];
        foreach ($missions as $missionId => $mission) {
            $sheetData[$missionId] = [];
            foreach ($units as $unitId => $unit) {
                $sheetData[$missionId][$unitId] = [];
                foreach (($users[$unitId] ?? []) as $user) {
                    $sheetData[$missionId][$unitId][$user['id']] = '';
                }
            }
        }

        foreach ($aars as $aar) {
            $missionId = $aar->getMission()->getId();
            $unitId = $aar->getUnitId();

            foreach ($aar->getAttendance() as $state => $userIds) {
                foreach ($userIds as $userId) {
                    if (isset($sheetData[$missionId][$unitId][$userId])) {
                        $sheetData[$missionId][$unitId][$userId] = $state;
                    } else {
                        // The user changed combat units, let's see if we can find them in a different unit
                        foreach ($sheetData[$missionId] as $mUnitId => $mUserIds) {
                            if (in_array($userId, array_keys($mUserIds))) {
                                $sheetData[$missionId][$mUnitId][$userId] = $state;
                                break;
                            }
                        }
                    }
                }
            }
        }

        $this->missions = $missions;
        $this->units = $units;
        $this->users = $users;
        $this->sheet = $sheetData;
    }

    #[LiveAction]
    public function reset(): void
    {
        $this->error = null;
        $this->missions = null;
        $this->units = null;
        $this->users = null;
        $this->sheet = null;
        $this->resetForm();
    }

    public function userAttendance(int $userId): ?int
    {
        return $this->getUserPercentage($userId, ['present']) ?? 0;
    }

    public function userAccountability(int $userId): ?int
    {
        return $this->getUserPercentage($userId, ['present', 'excused']) ?? 0;
    }

    private function getUserPercentage(int $userId, array $states): ?int
    {
        $total = 0;
        $count = null;

        foreach ($this->sheet as $units) {
            foreach ($units as $users) {
                foreach ($users as $uid => $state) {
                    if (empty($state)) {
                        continue;
                    }

                    if ($userId === $uid) {
                        $total++;
                        if ($count === null) {
                            $count = 0;
                        }

                        if (in_array($state, $states, true)) {
                            $count++;
                        }
                    }
                }
            }
        }

        if ($count === null || $total <= 0) {
            return null;
        }

        return (int)($count / $total * 100);
    }

    public function missionTotalPresent(int $missionId): ?int
    {
        return $this->getMissionTotal($missionId, 'present');
    }

    public function missionTotalExcused(int $missionId): ?int
    {
        return $this->getMissionTotal($missionId, 'excused');
    }

    public function missionTotalAbsent(int $missionId): ?int
    {
        return $this->getMissionTotal($missionId, 'absent');
    }

    private function getMissionTotal(int $missionId, string $tState): int
    {
        $count = 0;
        foreach ($this->sheet[$missionId] as $users) {
            foreach ($users as $state) {
                if ($state === $tState) {
                    $count++;
                }
            }
        }

        return $count;
    }

    public function missionPercentageAttended(int $missionId): ?int
    {
        return $this->getMissionPercentage($missionId, ['present']);
    }

    public function missionPercentageAccountable(int $missionId): ?int
    {
        return $this->getMissionPercentage($missionId, ['present', 'excused']);
    }

    private function getMissionPercentage(int $missionId, array $states): ?int
    {
        $total = 0;
        $count = null;

        foreach ($this->sheet[$missionId] as $users) {
            foreach ($users as $state) {
                if (empty($state)) {
                    continue;
                }

                $total++;
                if ($count === null) {
                    $count = 0;
                }

                if (in_array($state, $states, true)) {
                    $count++;
                }
            }
        }

        if ($count === null || $total <= 0) {
            return null;
        }

        return (int)($count / $total * 100);
    }
}
