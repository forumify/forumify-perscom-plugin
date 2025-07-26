<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Forum\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RosterController extends AbstractController
{
    #[Route('/roster', 'roster')]
    public function __invoke(): Response
    {
        return $this->render('@ForumifyPerscomPlugin/frontend/roster/roster.html.twig');
    }
}
