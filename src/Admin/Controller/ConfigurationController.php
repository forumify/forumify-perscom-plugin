<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Controller;

use Forumify\Core\Repository\SettingRepository;
use Forumify\Core\Twig\Extension\MenuRuntime;
use Forumify\PerscomPlugin\Admin\Form\ConfigurationType;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

#[IsGranted('perscom-io.admin.configuration.manage')]
class ConfigurationController extends AbstractController
{
    /**
     * @param TagAwareCacheInterface $cache
     */
    #[Route('/configuration', 'configuration')]
    public function __invoke(
        Request $request,
        SettingRepository $settingRepository,
        CacheInterface $cache
    ): Response {
        $oldEnlistmentStatus = $settingRepository->get('perscom.enlistment.status');
        $form = $this->createForm(ConfigurationType::class, $settingRepository->toFormData('perscom'));

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $settingRepository->handleFormData($data);

            $newEnlistmentStatus = $settingRepository->get('perscom.enlistment.status');
            if ($oldEnlistmentStatus !== $newEnlistmentStatus) {
                try {
                    $cache->invalidateTags([MenuRuntime::MENU_CACHE_TAG]);
                } catch (InvalidArgumentException) {
                }
            }

            return $this->redirectToRoute('perscom_admin_configuration');
        }

        return $this->render('@ForumifyPerscomPlugin/admin/configuration.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
