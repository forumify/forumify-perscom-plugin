<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Controller;

use Forumify\Core\Entity\User;
use Forumify\Core\Security\VoterAttribute;
use Forumify\PerscomPlugin\Forum\Form\AfterActionReportType;
use Forumify\PerscomPlugin\Perscom\Entity\AfterActionReport;
use Forumify\PerscomPlugin\Perscom\Entity\MissionRSVP;
use Forumify\PerscomPlugin\Perscom\Exception\AfterActionReportAlreadyExistsException;
use Forumify\PerscomPlugin\Perscom\PerscomFactory;
use Forumify\PerscomPlugin\Perscom\Repository\AfterActionReportRepository;
use Forumify\PerscomPlugin\Perscom\Repository\MissionRepository;
use Forumify\PerscomPlugin\Perscom\Repository\MissionRSVPRepository;
use Forumify\PerscomPlugin\Perscom\Service\AfterActionReportService;
use Forumify\Plugin\Attribute\PluginVersion;
use Perscom\Data\FilterObject;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
        private readonly AfterActionReportService $afterActionReportService,
    ) {
    }

    #[Route('/{id<\d+>}', 'view')]
    public function view(AfterActionReport $aar): Response
    {
        $this->denyAccessUnlessGranted(VoterAttribute::ACL->value, [
            'entity' => $aar->getMission()->getOperation(),
            'permission' => 'manage_after_action_reports',
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
            $list = array_filter($list);
            $this->afterActionReportService->sortUsers($list);

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
            'attendanceStates' => $this->afterActionReportService->getAttendanceStates(),
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
            'permission' => 'manage_after_action_reports',
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
        $form = $this->createForm(AfterActionReportType::class, $aar, ['is_new' => $isNew]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var AfterActionReport $aar */
            $aar = $form->getData();
            $attendance = $form->get('attendanceJson')->getData();

            try {
                $this->afterActionReportService->createOrUpdate($aar, $attendance, $isNew);
                $this->addFlash('success', $isNew ? 'perscom.aar.created' : 'perscom.aar.edited');
                return $this->redirectToRoute('perscom_aar_view', ['id' => $aar->getId()]);
            } catch (AfterActionReportAlreadyExistsException) {
                $this->addFlash('error', 'perscom.aar.already_exists');
            }
        }

        return $this->render('@ForumifyPerscomPlugin/frontend/aar/form.html.twig', [
            'aar' => $aar,
            'form' => $form->createView(),
            'title' => $isNew ? 'perscom.aar.create' : 'perscom.aar.edit',
            'cancelPath' => $isNew
                ? $this->generateUrl('perscom_missions_view', ['id' => $aar->getMission()->getId()])
                : $this->generateUrl('perscom_aar_view', ['id' => $aar->getId()]),
            'attendanceStatus' => $this->afterActionReportService->getAttendanceStates(),
        ]);
    }

    #[Route('/unit/{id}', 'unit')]
    public function getUnit(int $id, Request $request, MissionRSVPRepository $missionRSVPRepository): JsonResponse
    {
        $users = $this->afterActionReportService->findUsersByUnit($id);

        $missionId = $request->get('mission');
        $rsvps = $missionId === null 
            ? []
            : $missionRSVPRepository->findBy([
                'mission' => $missionId,
                'perscomUserId' => array_column($users, 'id'),
            ]);

        $usersToRsvp = [];
        /** @var MissionRSVP $rsvp */
        foreach ($rsvps as $rsvp) {
            $usersToRsvp[$rsvp->getPerscomUserId()] = $rsvp->isGoing();
        }

        $response = [];
        foreach ($users as $user) {
            $response[] = [
                'id' => $user['id'],
                'name' => $user['name'],
                'rankImage' => !empty($user['rank']['image']) ? $user['rank']['image']['image_url'] : null,
                'rsvp' => $usersToRsvp[$user['id']] ?? null,
            ];
        }

        return new JsonResponse($response);
    }
}
