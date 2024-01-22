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
        $form = $this->createForm(SettingsType::class, $settingRepository->toFormData('perscom'));

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $settingRepository->handleFormData($data);

            return $this->redirectToRoute('perscom_admin_settings');
        }

        return $this->render('@ForumifyPerscomPlugin/admin/settings.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
