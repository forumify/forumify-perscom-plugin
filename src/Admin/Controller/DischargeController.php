<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Controller;

use Forumify\PerscomPlugin\Admin\Form\Discharge;
use Forumify\PerscomPlugin\Admin\Form\DischargeType;
use Forumify\PerscomPlugin\Admin\Service\RecordService;
use Forumify\PerscomPlugin\Perscom\Entity\PerscomUser;
use Forumify\PerscomPlugin\Perscom\Entity\Record\AssignmentRecord;
use Forumify\PerscomPlugin\Perscom\Entity\Record\RankRecord;
use Forumify\PerscomPlugin\Perscom\Entity\Record\ServiceRecord;
use Forumify\PerscomPlugin\Perscom\Repository\PerscomUserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DischargeController extends AbstractController
{
    public function __construct(
        private readonly PerscomUserRepository $perscomUserRepository,
        private readonly RecordService $recordService,
    ) {
    }
    
    #[Route('/users/{id}/discharge', 'user_discharge')]
    #[IsGranted('perscom-io.admin.user.discharge')]
    public function __invoke(Request $request, PerscomUser $perscomUser): Response
    {

        $discharge = new Discharge();
        $discharge->status = $perscomUser->getStatus();
        $discharge->rank = $perscomUser->getRank();
        $discharge->unit = $perscomUser->getUnit();
        $discharge->position = $perscomUser->getPosition();

        $currentRank = $discharge->rank;

        $form = $this->createForm(DischargeType::class, $discharge);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $discharge = $form->getData();

            $records = [];

            $assignmentRecord = new AssignmentRecord();
            $assignmentRecord->setUser($perscomUser);
            $assignmentRecord->setStatus($discharge->status);
            $assignmentRecord->setUnit($discharge->unit);
            $assignmentRecord->setPosition($discharge->position);
            $records[] = $assignmentRecord;

            $serviceRecord = new ServiceRecord();
            $serviceRecord->setUser($perscomUser);
            if ($discharge->reason == null) {
                $serviceRecord->setText("$discharge->type");
            } else {
                $serviceRecord->setText("$discharge->type: $discharge->reason");
            }
            $records[] = $serviceRecord;

            if ($discharge->rank->getId() !== $currentRank->getId()) {
                $rankRecord = new RankRecord();
                $rankRecord->setUser($perscomUser);

                if ($discharge->rank->getPosition() > $currentRank->getPosition()) {
                    $type = "promotion";
                } else {
                    $type = "demotion";
                }

                $rankRecord->setType($type);
                $rankRecord->setRank($discharge->rank);
                $records[] = $rankRecord;
            }

            $this->recordService->createRecords($records, sendNotification: true);

            $perscomUser->setSpecialty(null);

            $this->perscomUserRepository->save($perscomUser);

            $this->addFlash('success', 'Discharged user.');
            return $this->redirectToRoute('perscom_admin_user_list');
        }

        return $this->render('@ForumifyPerscomPlugin/admin/users/edit/discharge.html.twig', [
            'form' => $form,
            'user' => $perscomUser,
        ]);
    }
}
