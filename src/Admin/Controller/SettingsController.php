<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Controller;

use Forumify\Core\Repository\SettingRepository;
use Forumify\PerscomPlugin\Admin\Form\SettingsType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SettingsController extends AbstractController
{
    #[Route('/settings', 'settings')]
    public function __invoke(Request $request, SettingRepository $settingRepository): Response
    {
        $form = $this->createForm(SettingsType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $settingRepository->setBulk([
                'perscom.endpoint' => $data['settings__endpoint'],
                'perscom.perscom_id' => $data['settings__perscom_id'],
                'perscom.api_key' => $data['settings__api_key'],
                'perscom.activity_tracker.time_until_inactive' => $data['activity_tracker__time_until_inactive'],
                'perscom.activity_tracker.inactive_status' => $data['activity_tracker__inactive_status'],
            ]);
            $settingRepository->setJson('perscom.activity_tracker.status_to_track', $data['activity_tracker__status_to_track']);

            $this->addFlash('success', 'saved');
            return $this->redirectToRoute('perscom_admin_settings');
        }

        return $this->render('@ForumifyPerscomPlugin/admin/settings.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
