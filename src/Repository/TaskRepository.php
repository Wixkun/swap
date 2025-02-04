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
            ->orderBy('t.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

}
