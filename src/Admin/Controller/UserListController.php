<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('perscom-io.admin.users.view')]
class UserListController extends AbstractController
{
    #[Route('/users', 'user_list')]
    public function __invoke(): Response
    {
        return $this->render('@ForumifyPerscomPlugin/admin/users/list/list.html.twig');
    }
}
