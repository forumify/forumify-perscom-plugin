<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Admin\Controller;

use Forumify\Admin\Crud\AbstractCrudController;
use Forumify\PerscomPlugin\Admin\Form\UserType;
use Forumify\PerscomPlugin\Perscom\Entity\PerscomUser;
use Forumify\PerscomPlugin\Perscom\Repository\AssignmentRecordRepository;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/users', 'user')]
class UserController extends AbstractCrudController
{
    public function __construct(
        private readonly AssignmentRecordRepository $assignmentRecordRepository,
    ) {
    }

    protected string $listTemplate = '@ForumifyPerscomPlugin/admin/users/list/list.html.twig';
    protected string $formTemplate = '@ForumifyPerscomPlugin/admin/users/edit/edit.html.twig';

    protected ?string $permissionView = 'perscom-io.admin.users.view';
    protected ?string $permissionCreate = 'perscom-io.admin.users.manage';
    protected ?string $permissionEdit = 'perscom-io.admin.users.manage';
    protected ?string $permissionDelete = 'perscom-io.admin.users.delete';

    protected function getTranslationPrefix(): string
    {
        return 'perscom.' . parent::getTranslationPrefix();
    }

    protected function getEntityClass(): string
    {
        return PerscomUser::class;
    }

    protected function getTableName(): string
    {
        return 'PerscomUserTable';
    }

    protected function getForm(?object $data): FormInterface
    {
        return $this->createForm(UserType::class, $data);
    }

    protected function templateParams(array $params = []): array
    {
        $params = parent::templateParams($params);
        if (empty($params['data'])) {
            return $params;
        }

        /** @var PerscomUser $user */
        $user = $params['data'];
        $params['secondaryAssignmentRecords'] = $this->assignmentRecordRepository->findBy([
            'type' => 'secondary',
            'user' => $user,
        ]);

        return $params;
    }
}
