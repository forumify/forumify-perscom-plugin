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
            $settingRepository->set('perscom.endpoint', $data['endpoint']);
            $settingRepository->set('perscom.perscom_id', $data['perscom_id']);
            $settingRepository->set('perscom.api_key', $data['api_key']);

            $this->addFlash('success', 'saved');
            return $this->redirectToRoute('perscom_admin_settings');
        }

        return $this->render('@ForumifyPerscomPlugin/admin/settings.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
