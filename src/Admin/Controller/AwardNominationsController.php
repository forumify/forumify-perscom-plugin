<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Controller;

use Forumify\Core\Repository\SettingRepository;
use Forumify\PerscomPlugin\Perscom\Repository\AwardNominationRepository;
use Forumify\PerscomPlugin\Perscom\Service\AwardNominationService;
use Forumify\PerscomPlugin\Perscom\Service\PerscomUserService;
use Forumify\PerscomPlugin\Admin\Form\AwardNominationFormData;
use Forumify\PerscomPlugin\Admin\Service\RecordService;
use Forumify\PerscomPlugin\Perscom\Entity\AwardNomination;
use Forumify\PerscomPlugin\Admin\Form\AwardNominationForm;
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
        private readonly RecordService $recordService,
        private readonly SettingRepository $settingRepository
        ) {}

    #[Route('', 'list')]
    public function list(): Response
    {
        return $this->render('@ForumifyPerscomPlugin/admin/pages/table.html.twig', [
            'title' => 'perscom.admin.award_nominations.list.title',
            'table' => 'AwardNominationsTable'
        ]);
    }

    #[Route('/{id<\d+>}', 'view')]
    public function view(AwardNomination $nomination, Request $request): Response
    {
        $data = $this->awardNominationRepository->getAwardNomination($nomination->getId());

        $approvedStatus = intVal($this->settingRepository->get('perscom.award_nominations.approved_status_id'));
        $form = null;
        if ($data->nomination->getStatus() != $approvedStatus)
        {
            $record = new AwardNominationFormData();
            $record->data = $data;
            $form = $this->createForm(AwardNominationForm::class, $record);

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $nominationRecord = $form->getData();
                $nomination->setStatus($nominationRecord->status);
                $currentUserId = $this->perscomUserService->getLoggedInPerscomUser()['id'];
                $nomination->setUpdatedByUserId($currentUserId);
                $this->awardNominationService->update($nomination);

                $approvedStatus = intVal($this->settingRepository->get('perscom.award_nominations.approved_status_id'));
                if ($nominationRecord->status == $approvedStatus) {
                    $recordData = [
                        'users' => [$nominationRecord->data->receiverItem['id']],
                        'sendNotification' => true,
                        'award_id' => $nominationRecord->data->nomination->getAwardId(),
                        'author_id' => $currentUserId,
                        'text' => $nominationRecord->data->nomination->getReason()
                    ];
                    $this->recordService->createRecord('award', $recordData);
                }

                return $this->redirectToRoute('perscom_admin_awardnominations_view', ['id' => $nomination->getId()]);
            }
        }

        return $this->render('@ForumifyPerscomPlugin/admin/awardnominations/view.html.twig', [
            'form' => $form,
            'data' => $data
        ]);
    }
}
