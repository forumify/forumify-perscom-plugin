<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Controller;

use Forumify\PerscomPlugin\Admin\Form\Discharge;
use Forumify\PerscomPlugin\Admin\Form\DischargeType;
use Forumify\PerscomPlugin\Perscom\Entity\PerscomUser;
use Forumify\PerscomPlugin\Perscom\Service\PerscomDischargeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DischargeController extends AbstractController
{
    public function __construct(
        private readonly PerscomDischargeService $dischargeService,
    ) {
    }

    #[Route('/users/{id}/discharge', 'user_discharge')]
    #[IsGranted('perscom-io.admin.user.discharge')]
    public function __invoke(Request $request, PerscomUser $perscomUser): Response
    {
        $discharge = new Discharge($perscomUser);
        $form = $this->createForm(DischargeType::class, $discharge);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $discharge = $form->getData();
            $this->dischargeService->discharge($discharge);

            $this->addFlash('success', 'Discharged user.');
            return $this->redirectToRoute('perscom_admin_user_list');
        }

        return $this->render('@ForumifyPerscomPlugin/admin/users/edit/discharge.html.twig', [
            'form' => $form,
            'user' => $perscomUser,
        ]);
    }
}
