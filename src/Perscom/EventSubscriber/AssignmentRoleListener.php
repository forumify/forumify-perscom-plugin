<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\EventSubscriber;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Forumify\Core\Entity\Role;
use Forumify\Core\Entity\User;
use Forumify\PerscomPlugin\Perscom\Entity\PerscomUser;
use Forumify\PerscomPlugin\Perscom\Entity\Record\AssignmentRecord;
use Forumify\PerscomPlugin\Perscom\Repository\AssignmentRecordRepository;

#[AsEntityListener(Events::prePersist, 'prePersist', entity: AssignmentRecord::class, priority: 100)]
#[AsEntityListener(Events::preRemove, 'preRemove', entity: AssignmentRecord::class, priority: -100)]
class AssignmentRoleListener
{
    public function __construct(
        private readonly AssignmentRecordRepository $assignmentRecordRepository,
    ) {
    }

    public function prePersist(AssignmentRecord $addedRecord): void
    {
        $perscomUser = $addedRecord->getUser();
        $forumUser = $perscomUser->getUser();
        if ($forumUser === null) {
            return;
        }

        $rolesToAdd = $this->getRoles($addedRecord);
        if ($addedRecord->getType() === AssignmentRecord::TYPE_SECONDARY) {
            $this->addRoles($forumUser, $rolesToAdd);
            return;
        }

        $rolesToRemove = [];
        if ($perscomUser->getStatus()?->role && $addedRecord->getStatus() !== null) {
            $rolesToRemove[] = $perscomUser->getStatus()->role;
        }
        if ($perscomUser->getUnit()?->role && $addedRecord->getUnit() !== null) {
            $rolesToRemove[] = $perscomUser->getUnit()->role;
        }
        if ($perscomUser->getPosition()?->role && $addedRecord->getPosition() !== null) {
            $rolesToRemove[] = $perscomUser->getPosition()->role;
        }
        if ($perscomUser->getSpecialty()?->role && $addedRecord->getSpecialty() !== null) {
            $rolesToRemove[] = $perscomUser->getSpecialty()->role;
        }

        $rolesToKeep = $this->getRolesFromSecondaryAssignments($perscomUser);
        foreach ($rolesToRemove as $i => $toRemove) {
            if (isset($rolesToKeep[$toRemove->getId()])) {
                unset($rolesToRemove[$i]);
            }
        }

        $this->removeRoles($forumUser, $rolesToRemove);
        $this->addRoles($forumUser, $rolesToAdd);
    }

    public function preRemove(AssignmentRecord $deletedRecord): void
    {
        $perscomUser = $deletedRecord->getUser();
        $forumUser = $perscomUser->getUser();
        if ($forumUser === null) {
            return;
        }

        $rolesToKeep = $this->getRolesFromSecondaryAssignments($perscomUser, $deletedRecord);
        foreach ($this->getRoles($perscomUser) as $role) {
            $rolesToKeep[$role->getId()] = $role;
        }

        $rolesToRemove = $this->getRoles($deletedRecord);
        foreach ($rolesToRemove as $i => $toRemove) {
            if (isset($rolesToKeep[$toRemove->getId()])) {
                unset($rolesToRemove[$i]);
            }
        }
        $this->removeRoles($forumUser, $rolesToRemove);
    }

    /**
     * @return array<Role>
     */
    private function getRoles(AssignmentRecord|PerscomUser $thing): array
    {
        return array_values(array_filter([
            $thing->getStatus()?->role,
            $thing->getUnit()?->role,
            $thing->getPosition()?->role,
            $thing->getSpecialty()?->role,
        ]));
    }

    /**
     * @param array<Role> $roles
     */
    private function addRoles(User $user, array $roles): void
    {
        $userRoles = $user->getRoleEntities();
        foreach ($roles as $role) {
            if (!$userRoles->contains($role)) {
                $userRoles->add($role);
            }
        }
    }

    /**
     * @param array<Role> $roles
     */
    private function removeRoles(User $user, array $roles): void
    {
        $userRoles = $user->getRoleEntities();
        foreach ($roles as $role) {
            if ($userRoles->contains($role)) {
                $userRoles->removeElement($role);
            }
        }
    }

    /**
     * @return array<Role>
     */
    private function getRolesFromSecondaryAssignments(PerscomUser $user, ?AssignmentRecord $ignore = null): array
    {
        $qb = $this
            ->assignmentRecordRepository
            ->createQueryBuilder('ar')
            ->where('ar.type = :type')
            ->andWhere('ar.user = :user')
            ->setParameter('type', AssignmentRecord::TYPE_SECONDARY)
            ->setParameter('user', $user)
        ;

        if ($ignore !== null) {
            $qb
                ->andWhere('ar != :record')
                ->setParameter('record', $ignore)
            ;
        }

        $roles = array_merge(...array_map($this->getRoles(...), $qb->getQuery()->getResult()));
        $roleMap = [];
        foreach ($roles as $role) {
            $roleMap[$role->getId()] = $role;
        }
        return $roleMap;
    }
}
