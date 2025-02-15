<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class QualificationController extends AbstractController
{
    #[Route('/qualifications', 'qualifications')]
    public function __invoke(): Response
    {
        return $this->render('@ForumifyPerscomPlugin/frontend/qualification/qualification.html.twig');
    }
}
