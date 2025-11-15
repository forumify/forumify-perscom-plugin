<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin\Perscom\Repository;

use Doctrine\Persistence\ManagerRegistry;
use Forumify\Core\Repository\SettingRepository;
use Forumify\PerscomPlugin\Perscom\Entity\Form;

/**
 * @extends AbstractPerscomRepository<Form>
 */
class FormRepository extends AbstractPerscomRepository
{
    public function __construct(
        ManagerRegistry $managerRegistry,
        private readonly SettingRepository $settingRepository,
    ) {
        parent::__construct($managerRegistry);
    }

    public static function getEntityClass(): string
    {
        return Form::class;
    }

    /**
     * @return array<Form>
     */
    public function findAllSubmissionsAllowed(): array
    {
        $qb = $this->createQueryBuilder('e');
        $this->addACLToQuery($qb, 'create_submissions');

        $enlistmentFormId = $this->settingRepository->get('perscom.enlistment.form');
        if ($enlistmentFormId !== null) {
            $qb->andWhere('e.id != :enlistmentForm')->setParameter('enlistmentForm', $enlistmentFormId);
        }

        return $qb->getQuery()->getResult();
    }
}
