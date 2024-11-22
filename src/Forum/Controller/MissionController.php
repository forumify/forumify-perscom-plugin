<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Controller;

use Forumify\Core\Security\VoterAttribute;
use Forumify\PerscomPlugin\Forum\Form\MissionType;
use Forumify\PerscomPlugin\Perscom\Entity\Mission;
use Forumify\PerscomPlugin\Perscom\Repository\MissionRepository;
use Forumify\PerscomPlugin\Perscom\Repository\OperationRepository;
use Forumify\Plugin\Attribute\PluginVersion;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/missions', 'missions_')]
#[PluginVersion('forumify/forumify-perscom-plugin', 'premium')]
class MissionController extends AbstractController
{
    public function __construct(
        private readonly OperationRepository $operationRepository,
        private readonly MissionRepository $missionRepository,
    ) {
    }

    #[Route('/{id<\d+>}', 'view')]
    public function view(Mission $mission): Response
    {
        $this->denyAccessUnlessGranted(VoterAttribute::ACL->value, [
            'entity' => $mission->getOperation(),
            'permission' => 'view',
        ]);

        return $this->render('@ForumifyPerscomPlugin/frontend/mission/mission.html.twig', [
            'mission' => $mission,
        ]);
    }

    #[Route('/new', 'create')]
    public function create(Request $request): Response
    {
        $operation = $this->operationRepository->find($request->get('operation'));
        if ($operation === null) {
            throw $this->createNotFoundException();
        }

        $this->denyAccessUnlessGranted(VoterAttribute::ACL->value, [
            'entity' => $operation,
            'permission' => 'manage_missions',
        ]);

        $mission = new Mission();
        $mission->setOperation($operation);
        $mission->setBriefing($operation->getMissionBriefingTemplate());

        return $this->handleMissionForm($mission, true, $request);
    }

    #[Route('/{id<\d+>}/edit', 'edit')]
    public function edit(Mission $mission, Request $request): Response
    {
        $operation = $mission->getOperation();
        $this->denyAccessUnlessGranted(VoterAttribute::ACL->value, [
            'entity' => $operation,
            'permission' => 'manage_missions',
        ]);

        return $this->handleMissionForm($mission, false, $request);
    }

    private function handleMissionForm(Mission $mission, bool $isNew, Request $request): Response
    {
        $form = $this->createForm(MissionType::class, $mission);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $mission = $form->getData();
            $this->missionRepository->save($mission);

            $this->addFlash('success', $isNew ? 'perscom.mission.created' : 'perscom.mission.edited');
            return $this->redirectToRoute('perscom_missions_view', [
                'id' => $mission->getId(),
            ]);
        }

        return $this->render('@Forumify/form/simple_form_page.html.twig', [
            'form' => $form->createView(),
            'title' => $isNew ? 'perscom.mission.create' : 'perscom.mission.edit',
            'cancelPath' => $isNew
                ? $this->generateUrl('perscom_operations_view', [
                    'slug' => $mission->getOperation()->getSlug(),
                ])
                : $this->generateUrl('perscom_missions_view', ['id' => $mission->getId()]),
        ]);
    }
}
