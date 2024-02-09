<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/users', 'user')]
class UserController extends AbstractController
{
    #[Route('', '_list')]
    public function list(): Response
    {
        return $this->render('@ForumifyPerscomPlugin/admin/users/list/list.html.twig');
    }

    #[Route('/{id}', '_edit')]
    public function edit(): Response
    {
        return $this->render('@ForumifyPerscomPlugin/admin/users/list/list.html.twig');
    }
}
