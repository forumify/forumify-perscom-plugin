<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Controller;

use DateTime;
use Forumify\Core\Repository\SettingRepository;
use Forumify\PerscomPlugin\Forum\Form\AwardNominationForm;
use Forumify\PerscomPlugin\Perscom\Service\AwardNominationService;
use Forumify\PerscomPlugin\Perscom\Service\PerscomUserService;
use Forumify\Plugin\Attribute\PluginVersion;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/nomination', 'nomination_')]
#[PluginVersion('forumify/forumify-perscom-plugin', 'premium')]
class AwardNominationController extends AbstractController
{
    public function __construct(
        private readonly AwardNominationService $awardNominationService,
        private readonly PerscomUserService $perscomUserService,
        private readonly SettingRepository $settingRepository
    ) {
    }

    #[Route('', 'create')]
    public function create(Request $request): Response
    {
        $form = $this->createForm(AwardNominationForm::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $data->setPerscomUserId($this->perscomUserService->getLoggedInPerscomUser()['id']);
            $data->setCreatedDate(new DateTime());
            $data->setStatus(intval($this->settingRepository->get('perscom.award_nominations.pending_status_id')));

            $this->awardNominationService->create($data);

            $this->addFlash('success', 'perscom.admin.users.record_form.created');
            return $this->redirectToRoute('perscom_operations_center');
        }

        return $this->render('@ForumifyPerscomPlugin/frontend/form/award_nomination.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
