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
    public function findAllPendingFirst(): array
    {
        return $this->createQueryBuilder('t')
            // This CASE sets 0 if status = 'pending', else 1 => so pending appear first
            ->addSelect("CASE WHEN t.status = 'pending' THEN 0 ELSE 1 END AS HIDDEN status_priority")
            ->orderBy('status_priority', 'ASC')
            ->addOrderBy('t.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }
}
