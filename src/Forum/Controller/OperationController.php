<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Controller;

use Forumify\Core\Security\VoterAttribute;
use Forumify\PerscomPlugin\Forum\Form\MissionType;
use Forumify\PerscomPlugin\Perscom\Entity\Operation;
use Forumify\PerscomPlugin\Perscom\Repository\MissionRepository;
use Forumify\Plugin\Attribute\PluginVersion;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/operations', 'operations_')]
#[PluginVersion('forumify/forumify-perscom-plugin', 'premium')]
class OperationController extends AbstractController
{
    #[Route('/{slug}', 'view')]
    public function view(Operation $operation): Response
    {
        $this->denyAccessUnlessGranted(VoterAttribute::ACL->value, [
            'entity' => $operation,
            'permission' => 'view',
        ]);

        return $this->render('@ForumifyPerscomPlugin/frontend/operation/operation.html.twig', [
            'operation' => $operation,
        ]);
    }


}
