<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Task>
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    public function findOnlyPending(): array
    {
        return $this->createQueryBuilder('t')
            ->where('t.status = :status')
            ->setParameter('status', 'pending')
            ->orderBy('t.updatedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByMultipleTags(array $tagIds): array
    {
        return $this->createQueryBuilder('t')
            ->join('t.tags', 'tg')
            ->where('tg.id IN (:tagIds)')
            ->setParameter('tagIds', $tagIds, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY)
            ->orderBy('t.updatedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

}
