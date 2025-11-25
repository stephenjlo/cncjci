<?php
namespace App\Repository;
use App\Entity\Cabinet;
use App\Model\Pagination;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CabinetRepository extends ServiceEntityRepository {
  public function __construct(ManagerRegistry $registry){ parent::__construct($registry, Cabinet::class); }
    public function search(array $q): Pagination {
      $page = max(1, (int)($q['page'] ?? 1));
      $pageSize = min(50, max(1, (int)($q['pageSize'] ?? 12)));

      $qb = $this->createQueryBuilder('c')
          ->andWhere('c.isActive = :active')
          ->setParameter('active', true);

      if (!empty($q['name'])) {
          $qb->andWhere('LOWER(c.name) LIKE :n')
            ->setParameter('n', '%' . mb_strtolower($q['name']) . '%');
      }
      if (!empty($q['type'])) {
          $qb->andWhere('c.type = :t')->setParameter('t', $q['type']);
      }
      if (!empty($q['city'])) {
          $qb->andWhere('LOWER(c.city) LIKE :city')
            ->setParameter('city', '%' . mb_strtolower($q['city']) . '%');
      }

      $qb->orderBy('c.name', 'ASC')
        ->setFirstResult(($page - 1) * $pageSize)
        ->setMaxResults($pageSize);

      $items = $qb->getQuery()->getResult();

      $tq = $this->createQueryBuilder('c')
          ->select('COUNT(c.id)')
          ->andWhere('c.isActive = :active')
          ->setParameter('active', true);
      if (!empty($q['name'])) {
          $tq->andWhere('LOWER(c.name) LIKE :n')
            ->setParameter('n', '%' . mb_strtolower($q['name']) . '%');
      }
      if (!empty($q['type'])) {
          $tq->andWhere('c.type = :t')->setParameter('t', $q['type']);
      }
      if (!empty($q['city'])) {
          $tq->andWhere('LOWER(c.city) LIKE :city')
            ->setParameter('city', '%' . mb_strtolower($q['city']) . '%');
      }

      $total = (int)$tq->getQuery()->getSingleScalarResult();
      return new Pagination($items, $total, $page, $pageSize);
  }

}
