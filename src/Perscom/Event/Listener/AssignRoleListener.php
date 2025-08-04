<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Event\Listener;

use Forumify\Core\Entity\Role;
use Forumify\Core\Repository\RoleRepository;
use Forumify\Core\Repository\SettingRepository;
use Forumify\Core\Repository\UserRepository;
use Forumify\PerscomPlugin\Perscom\Event\UserEnlistedEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener]
class AssignRoleListener
{
    public function __construct(
        private readonly SettingRepository $settingRepository,
        private readonly RoleRepository $roleRepository,
        private readonly UserRepository $userRepository,
    ) {
    }

    public function __invoke(UserEnlistedEvent $event): void
    {
        $roleId = $this->settingRepository->get('perscom.enlistment.role');
        if (!$roleId) {
            return;
        }

        /** @var Role|null $role */
        $role = $this->roleRepository->find($roleId);
        if ($role === null) {
            return;
        }

        $user = $event->perscomUser->getUser();
        if ($user === null) {
            return;
        }

        foreach ($user->getRoleEntities() as $existingRole) {
            if ($role->getId() === $existingRole->getId()) {
                return;
            }
        }

        $user->addRoleEntity($role);
        $this->userRepository->save($user);
    }
}
