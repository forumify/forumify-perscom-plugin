<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Controller;

use Forumify\Core\Repository\UserRepository;
use Forumify\PerscomPlugin\Admin\Form\DischargeType;
use Forumify\PerscomPlugin\Perscom\Entity\Record\AssignmentRecord;
use Forumify\PerscomPlugin\Perscom\Entity\Record\RankRecord;
use Forumify\PerscomPlugin\Perscom\Entity\Record\ServiceRecord;
use Forumify\PerscomPlugin\Perscom\Repository\AssignmentRecordRepository;
use Forumify\PerscomPlugin\Perscom\Repository\PerscomUserRepository;
use Forumify\PerscomPlugin\Perscom\Repository\RankRecordRepository;
use Forumify\PerscomPlugin\Perscom\Repository\ServiceRecordRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DischargeController extends AbstractController
{
    public function __construct(
        private readonly PerscomUserRepository $perscomUserRepository,
        private readonly AssignmentRecordRepository $assignmentRecordRepository,
        private readonly ServiceRecordRepository $serviceRecordRepository,
        private readonly UserRepository $userRepository,
        private readonly RankRecordRepository $rankRecordRepository,
    ) {
    }
    
    #[Route('/users/{identifier}/discharge', 'user_discharge')]
    public function __invoke(Request $request, int $identifier): Response
    {
        $this->denyAccessUnlessGranted('perscom-io.admin.user.discharge');

        $user = $this->userRepository->find($identifier);

        $perscomUser = $this->perscomUserRepository->findOneBy(['user' => $user]);
        $oldRank = $perscomUser->getRank();

        $form = $this->createForm(DischargeType::class, $perscomUser);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dischargeLevel = $form->get('discharge_level')->getData();
            $reason = $form->get('reason')->getData();

            $newRank = $form->get('rank')->getData();
            $status = $form->get('status')->getData();
            $unit = $form->get('unit')->getData();
            $position = $form->get('position')->getData();

            $assignmentRecord = new AssignmentRecord();
            $assignmentRecord->setUser($perscomUser);
            $assignmentRecord->setStatus($status);
            $assignmentRecord->setUnit($unit);
            $assignmentRecord->setPosition($position);

            $serviceRecord = new ServiceRecord();
            $serviceRecord->setUser($perscomUser);
            if ($reason == null) {
                $serviceRecord->setText("$dischargeLevel");
            } else {
                $serviceRecord->setText("$dischargeLevel: $reason");
            }

            if ($newRank->getId() !== $oldRank->getId()) {
                $rankRecord = new RankRecord();
                $rankRecord->setUser($perscomUser);

                if ($newRank->getPosition() > $oldRank->getPosition()) {
                    $type = "promotion";
                } else {
                    $type = "demotion";
                }

                $rankRecord->setType($type);
                $rankRecord->setRank($newRank);

                $this->rankRecordRepository->save($rankRecord);
            }

            $perscomUser->setPosition(null);
            $perscomUser->setSpecialty(null);

            $this->assignmentRecordRepository->save($assignmentRecord);
            $this->serviceRecordRepository->save($serviceRecord);
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
