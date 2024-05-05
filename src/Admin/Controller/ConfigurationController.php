<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Controller;

use Forumify\Core\Repository\SettingRepository;
use Forumify\PerscomPlugin\Admin\Form\ConfigurationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ConfigurationController extends AbstractController
{
    #[Route('/configuration', 'configuration')]
    public function __invoke(Request $request, SettingRepository $settingRepository): Response
    {
        $form = $this->createForm(ConfigurationType::class, $settingRepository->toFormData('perscom'));

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $settingRepository->handleFormData($data);

            return $this->redirectToRoute('perscom_admin_configuration');
        }

        return $this->render('@ForumifyPerscomPlugin/admin/configuration.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
