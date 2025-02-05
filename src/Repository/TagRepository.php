<?php

namespace App\Repository;

use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tag>
 */
class TagRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tag::class);
    }

    public function findByTag($tagId): array
    {
        return $this->createQueryBuilder('t')
            ->join('t.tasks', 'task')
            ->where('task.id = :tagId')
            ->setParameter('tagId', $tagId)
            ->orderBy('task.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

}
