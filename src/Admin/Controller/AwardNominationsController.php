<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Controller;

use Forumify\PerscomPlugin\Perscom\Repository\AwardNominationRepository;
use Forumify\PerscomPlugin\Perscom\Service\AwardNominationService;
use Forumify\PerscomPlugin\Perscom\Service\PerscomUserService;
use Forumify\PerscomPlugin\Admin\Form\AwardNominationAdminFormData;
use Forumify\PerscomPlugin\Admin\Service\RecordService;
use Forumify\PerscomPlugin\Perscom\Entity\AwardNomination;
use Forumify\PerscomPlugin\Admin\Form\AwardNominationAdminForm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/awardnominations', 'awardnominations_')]
#[IsGranted('perscom-io.admin.organization.view')]
class AwardNominationsController extends AbstractController
{
    public function __construct(
        private readonly AwardNominationRepository $awardNominationRepository,
        private readonly AwardNominationService $awardNominationService,
        private readonly PerscomUserService $perscomUserService,
        private readonly RecordService $recordService
        ) {}

    #[Route('', 'list')]
    public function list(): Response
    {
        return $this->render('@ForumifyPerscomPlugin/admin/pages/table.html.twig', [
            'title' => 'perscom.admin.awardnominations.list.title',
            'table' => 'AwardNominationsTable'
        ]);
    }

    #[Route('/{id<\d+>}', 'view')]
    public function view(AwardNomination $nomination, Request $request): Response
    {
        $data = $this->awardNominationRepository->getAwardNomination($nomination->getId());

        $record = new AwardNominationAdminFormData();
        $record->data = $data;
        $form = $this->createForm(AwardNominationAdminForm::class, $record);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $nominationRecord = $form->getData();
            $nomination->setStatus($nominationRecord->status);
            $currentUserId = $this->perscomUserService->getLoggedInPerscomUser()['id'];
            $nomination->setUpdatedByUserId($currentUserId);
            $this->awardNominationService->update($nomination);

            if ($nominationRecord->status == 6) {// Get this from config
                $recordData = [
                    'users' => [$nominationRecord->data->receiverItem['id']], 
                    'sendNotification' => true,
                    'award_id' => $nominationRecord->data->nomination->getAwardId(),
                    'author_id' => $currentUserId,
                    'text' => $nominationRecord->data->nomination->getReason()
                ];
                $this->recordService->createRecord('award', $recordData);
            }

            $this->addFlash('success', 'perscom.admin.submissions.view.status_created');
            return $this->redirectToRoute('perscom_admin_awardnominations_view', ['id' => $nomination->getId()]);
        }

        return $this->render('@ForumifyPerscomPlugin/admin/awardnominations/view.html.twig', [
            'form' => $form,
            'data' => $data
        ]);
    }
}
