<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Controller;

use Forumify\Core\Entity\User;
use Forumify\Core\Security\VoterAttribute;
use Forumify\PerscomPlugin\Forum\Form\AfterActionReportType;
use Forumify\PerscomPlugin\Perscom\Entity\AfterActionReport;
use Forumify\PerscomPlugin\Perscom\PerscomFactory;
use Forumify\PerscomPlugin\Perscom\Repository\AfterActionReportRepository;
use Forumify\PerscomPlugin\Perscom\Repository\MissionRepository;
use Forumify\Plugin\Attribute\PluginVersion;
use JsonException;
use Perscom\Data\FilterObject;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/aar', 'aar_')]
#[PluginVersion('forumify/forumify-perscom-plugin', 'premium')]
class AfterActionReportController extends AbstractController
{
    public function __construct(
        private readonly AfterActionReportRepository $afterActionReportRepository,
        private readonly MissionRepository $missionRepository,
        private readonly PerscomFactory $perscomFactory,
    ) {
    }

    #[Route('/{id<\d+>}', 'view')]
    public function view(AfterActionReport $aar): Response
    {
        $this->denyAccessUnlessGranted(VoterAttribute::ACL->value, [
            'entity' => $aar->getMission()->getOperation(),
            'permission' => 'create_after_action_reports',
        ]);

        $allUserIds = [];
        foreach ($aar->getAttendance() as $list) {
            foreach ($list as $userId) {
                $allUserIds[] = $userId;
            }
        }

        $users = $this->perscomFactory
            ->getPerscom()
            ->users()
            ->search(
                filter: new FilterObject('id', 'in', $allUserIds),
                include: [
                    'rank',
                    'rank.image',
                    'position',
                    'specialty',
                ])
            ->json('data');
        $users = array_combine(array_column($users, 'id'), $users);

        $attendance = $aar->getAttendance();
        foreach ($attendance as &$list) {
            foreach ($list as $k => $userId) {
                $list[$k] = $users[$userId] ?? null;
            }
        }
        unset($list);

        foreach ($attendance as &$list) {
            array_filter($list);
            $this->sortUsers($list);

            foreach ($list as $k => $user) {
                $list[$k] = [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'rankImage' => !empty($user['rank']['image']) ? $user['rank']['image']['image_url'] : null,
                ];
            }
        }
        unset($list);

        return $this->render('@ForumifyPerscomPlugin/frontend/aar/aar.html.twig', [
            'aar' => $aar,
            'attendance' => $attendance,
            'attendanceStates' => ['present', 'excused', 'absent'],
        ]);
    }

    #[Route('/new', 'create')]
    public function create(Request $request): Response
    {
        $mission = $this->missionRepository->find($request->get('mission'));
        if ($mission === null) {
            throw $this->createNotFoundException();
        }

        $this->denyAccessUnlessGranted(VoterAttribute::ACL->value, [
            'entity' => $mission->getOperation(),
            'permission' => 'create_after_action_reports',
        ]);

        $aar = new AfterActionReport();
        $aar->setMission($mission);
        $aar->setReport($mission->getOperation()->getAfterActionReportTemplate());

        return $this->handleAfterActionReportForm($aar, true, $request);
    }

    #[Route('/{id<\d+>}/edit', 'edit')]
    public function edit(AfterActionReport $aar, Request $request): Response
    {
        /** @var User|null $user */
        $user = $this->getUser();
        if ($aar->getCreatedBy()?->getId() !== $user?->getId()) {
            $this->denyAccessUnlessGranted(VoterAttribute::ACL->value, [
                'entity' => $aar->getMission()->getOperation(),
                'permission' => 'manage_after_action_reports',
            ]);
        }

        return $this->handleAfterActionReportForm($aar, false, $request);
    }

    #[Route('/{id<\d+>}/delete', 'delete')]
    public function delete(AfterActionReport $aar, Request $request): Response
    {
        /** @var User|null $user */
        $user = $this->getUser();
        if ($aar->getCreatedBy()?->getId() !== $user?->getId()) {
            $this->denyAccessUnlessGranted(VoterAttribute::ACL->value, [
                'entity' => $aar->getMission()->getOperation(),
                'permission' => 'manage_after_action_reports',
            ]);
        }

        if (!$request->get('confirmed')) {
            return $this->render('@ForumifyPerscomPlugin/frontend/aar/delete.html.twig', [
                'aar' => $aar,
            ]);
        }

        $missionId = $aar->getMission()->getId();
        $this->afterActionReportRepository->remove($aar);

        $this->addFlash('success', 'perscom.aar.deleted');
        return $this->redirectToRoute('perscom_missions_view', ['id' => $missionId]);
    }

    private function handleAfterActionReportForm(AfterActionReport $aar, bool $isNew, Request $request): Response
    {
        $form = $this->createForm(AfterActionReportType::class, $aar);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var AfterActionReport $aar */
            $aar = $form->getData();

            $existingAar = $this->afterActionReportRepository->findBy([
                'mission' => $aar->getMission(),
                'unitId' => $aar->getUnitId(),
            ]);

            if (empty($existingAar)) {
                $this->preSaveAAR($aar, $form);
                $this->afterActionReportRepository->save($aar);

                $this->addFlash('success', $isNew ? 'perscom.aar.created' : 'perscom.aar.edited');
                return $this->redirectToRoute('perscom_aar_view', ['id' => $aar->getId()]);
            }

            $this->addFlash('error', 'perscom.aar.already_exists');
        }

        return $this->render('@ForumifyPerscomPlugin/frontend/aar/form.html.twig', [
            'form' => $form->createView(),
            'title' => $isNew ? 'perscom.aar.create' : 'perscom.aar.edit',
            'cancelPath' => $isNew
                ? $this->generateUrl('perscom_missions_view', ['id' => $aar->getMission()->getId()])
                : $this->generateUrl('perscom_aar_view', ['id' => $aar->getId()]),
            'attendanceStatus' => ['present', 'excused', 'absent'],
        ]);
    }

    private function preSaveAAR(AfterActionReport $aar, FormInterface $form): void
    {
        try {
            $attendance = json_decode($form->get('attendanceJson')->getData(), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            $attendance = [];
        }
        $aar->setAttendance($attendance);

        $unit = $this->perscomFactory
            ->getPerscom()
            ->units()
            ->get($aar->getUnitId())
            ->json('data');

        $aar->setUnitName($unit['name']);
        $aar->setUnitPosition($unit['order'] ?? 100);
    }

    #[Route('/unit/{id}', 'unit')]
    public function getUnit(int $id): JsonResponse
    {
        $users = $this->perscomFactory
            ->getPerscom()
            ->units()
            ->get($id, [
                'users',
                'users.rank',
                'users.rank.image',
                'users.position',
                'users.specialty',
            ])
            ->json('data')['users'] ?? [];

        $response = [];

        $this->sortUsers($users);
        foreach ($users as $user) {
            $response[] = [
                'id' => $user['id'],
                'name' => $user['name'],
                'rankImage' => !empty($user['rank']['image']) ? $user['rank']['image']['image_url'] : null,
            ];
        }

        return new JsonResponse($response);
    }

    private function sortUsers(array &$users): void
    {
        usort($users, static function (array $a, array $b): int {
            $aRank = $a['rank']['order'] ?? 100;
            $bRank = $b['rank']['order'] ?? 100;
            if ($aRank !== $bRank) {
                return $aRank - $bRank;
            }

            $aPos = $a['position']['order'] ?? 100;
            $bPos = $b['position']['order'] ?? 100;
            if ($aPos !== $bPos) {
                return $aPos - $bPos;
            }

            $aSpec = $a['specialty']['order'] ?? 100;
            $bSpec = $b['specialty']['order'] ?? 100;
            if ($aSpec !== $bSpec) {
                return $aSpec - $bSpec;
            }

            return strcmp($a['name'], $b['name']);
        });
    }
}
