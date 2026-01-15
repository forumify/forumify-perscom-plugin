<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Service;

use Forumify\Core\Entity\SortableEntityInterface;
use Forumify\Core\Entity\User;
use Forumify\Core\Repository\SettingRepository;
use Forumify\PerscomPlugin\Forum\Form\Enlistment;
use Forumify\PerscomPlugin\Perscom\Entity\PerscomUser;
use Forumify\PerscomPlugin\Perscom\Repository\PerscomUserRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\PropertyAccess\PropertyAccess;

class PerscomUserService
{
    private array $userIdToPerscomUser = [];

    public function __construct(
        private readonly PerscomUserRepository $perscomUserRepository,
        private readonly SettingRepository $settingRepository,
        private readonly Security $security,
    ) {
    }

    public function getLoggedInPerscomUser(): ?PerscomUser
    {
        $user = $this->security->getUser();
        if (!$user instanceof User) {
            return null;
        }

        return $this->getPerscomUser($user);
    }

    public function getPerscomUser(User $user): ?PerscomUser
    {
        $userId = $user->getId();
        if (isset($this->userIdToPerscomUser[$userId])) {
            return $this->userIdToPerscomUser[$userId];
        }

        $this->userIdToPerscomUser[$userId] = $this->perscomUserRepository->findOneBy(['user' => $user]);
        return $this->userIdToPerscomUser[$userId];
    }

    public function createUser(Enlistment $enlistment): PerscomUser
    {
        /** @var User $user */
        $user = $this->security->getUser();
        $perscomUser = new PerscomUser();
        $perscomUser->setUser($user);
        $perscomUser->setName($user->getDisplayName());

        if (!empty($enlistment->firstName) && !empty($enlistment->lastName)) {
            $name = ucfirst($enlistment->firstName) . ' ' . ucfirst($enlistment->lastName);
            $perscomUser->setName($name);
        }

        $this->perscomUserRepository->save($perscomUser);
        return $perscomUser;
    }

    public function sortPerscomUsers(&$users): void
    {
        $sortOrder = $this->settingRepository->get('perscom.roster.user_sort_order');
        $sortOrder = empty($sortOrder)
            ? ['rank', 'position', 'specialty']
            : array_map('trim', explode(',', $sortOrder));

        $propertyAccessor = PropertyAccess::createPropertyAccessorBuilder()
            ->disableExceptionOnInvalidPropertyPath()
            ->getPropertyAccessor();

        usort($users, static function (PerscomUser $a, PerscomUser $b) use ($propertyAccessor, $sortOrder): int {
            foreach ($sortOrder as $sortField) {
                $valA = $propertyAccessor->getValue($a, $sortField);
                $valB = $propertyAccessor->getValue($b, $sortField);

                $aIsSortable = $valA instanceof SortableEntityInterface;
                $bIsSortable = $valB instanceof SortableEntityInterface;

                if ($aIsSortable && $bIsSortable) {
                    $valA = $valA->getPosition();
                    $valB = $valB->getPosition();
                    if ($valA === $valB) {
                        continue;
                    }
                    return $valA - $valB;
                }

                $diff = (int)$bIsSortable - (int)$aIsSortable;
                if ($diff !== 0) {
                    return $diff;
                }
            }
            return strcmp($a->getName(), $b->getName());
        });
    }
}
