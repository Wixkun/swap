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

    /**
     * Finds all tasks, but ensures 'pending' tasks come first,
     * then sorts by createdAt descending.
     */
    public function findOnlyPending(): array
    {
        return $this->createQueryBuilder('t')
            ->where('t.status = :status')
            ->setParameter('status', 'pending')
            ->orderBy('CASE WHEN t.updatedAt <> t.createdAt THEN 1 ELSE 0 END', 'DESC')
            ->addOrderBy('CASE WHEN t.updatedAt <> t.createdAt THEN t.updatedAt ELSE t.createdAt END', 'DESC')
            ->getQuery()
            ->getResult();
    }


    public function findByMultipleTags(array $tagIds): array
    {
        return $this->createQueryBuilder('t')
            ->join('t.tags', 'tg')
            ->where('tg.id IN (:tagIds)')
            ->setParameter('tagIds', $tagIds, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY)
            ->orderBy('CASE WHEN t.updatedAt <> t.createdAt THEN 1 ELSE 0 END', 'DESC')
            ->addOrderBy('CASE WHEN t.updatedAt <> t.createdAt THEN t.updatedAt ELSE t.createdAt END', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
