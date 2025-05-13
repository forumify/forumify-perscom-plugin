<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Repository;

use Doctrine\Persistence\ManagerRegistry;
use Forumify\Core\Entity\User;
use Forumify\Core\Repository\AbstractRepository;
use Forumify\Core\Repository\UserRepository;
use Forumify\PerscomPlugin\Perscom\Entity\PerscomUser;
use Forumify\PerscomPlugin\Perscom\PerscomFactory;
use Perscom\Data\FilterObject;

/**
 * @extends AbstractRepository<PerscomUser>
 */
class PerscomUserRepository extends AbstractRepository
{
    public function __construct(
        ManagerRegistry $managerRegistry,
        private readonly PerscomFactory $perscomFactory,
        private readonly UserRepository $userRepository,
    ) {
        parent::__construct($managerRegistry);
    }

    public static function getEntityClass(): string
    {
        return PerscomUser::class;
    }

    /**
     * @param array<int> $perscomIds
     * @return array<PerscomUser>
     */
    public function findByPerscomIds(array $perscomIds): array
    {
        /** @var array<PerscomUser> $result */
        $result = $this->findBy(['id' => $perscomIds]);
        if (count($result) === count($perscomIds)) {
            return $result;
        }

        $missing = [];
        foreach ($perscomIds as $perscomId) {
            $found = false;
            foreach ($result as $found) {
                if ($found->getId() === (int)$perscomId) {
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $missing[] = $perscomId;
            }
        }

        try {
            $perscomUsers = $this->perscomFactory
                ->getPerscom()
                ->users()
                ->search(filter: new FilterObject('id', 'in', $missing))
                ->json('data')
            ;
        } catch (\Exception) {
            return $result;
        }

        /** @var array<User> $users */
        $users = $this->userRepository->findBy(['email' => array_column($perscomUsers, 'email')]);
        foreach ($users as $user) {
            foreach ($perscomUsers as $perscomUser) {
                if (strtolower($perscomUser['email']) !== strtolower($user->getEmail())) {
                    continue;
                }

                $match = new PerscomUser();
                $match->setId($perscomUser['id']);
                $match->setUser($user);
                $this->save($match);

                $result[] = $match;
                break;
            }
        }

        return $result;
    }
}
