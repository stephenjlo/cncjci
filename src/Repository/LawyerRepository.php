<?php
namespace App\Repository;
use App\Entity\Lawyer;
use App\Model\Pagination;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class LawyerRepository extends ServiceEntityRepository {
  public function __construct(ManagerRegistry $registry){ parent::__construct($registry, Lawyer::class); }

  public function search(array $q): Pagination {
      $page = max(1, (int)($q['page'] ?? 1));
      $pageSize = min(50, max(1, (int)($q['pageSize'] ?? 12)));

      $qb = $this->createQueryBuilder('l')
          ->leftJoin('l.cabinet', 'c')->addSelect('c')
          ->leftJoin('l.specialties', 's')->addSelect('s')
          ->andWhere('l.isActive = :active')
          ->setParameter('active', true);

      if (!empty($q['name'])) {
          $qb->andWhere("LOWER(CONCAT(l.firstName, ' ', l.lastName)) LIKE :q")
            ->setParameter('q', '%' . mb_strtolower($q['name']) . '%');
      }
      if (!empty($q['cabinet'])) {
          $qb->andWhere('LOWER(c.name) LIKE :cab')
            ->setParameter('cab', '%' . mb_strtolower($q['cabinet']) . '%');
      }
      if (!empty($q['city'])) {
          $qb->andWhere('LOWER(l.city) LIKE :city')
            ->setParameter('city', '%' . mb_strtolower($q['city']) . '%');
      }
      if (!empty($q['specialty'])) {
          $qb->andWhere('s.id = :sid')->setParameter('sid', (int)$q['specialty']);
      }

      $qb->orderBy('l.lastName', 'ASC')
        ->setFirstResult(($page - 1) * $pageSize)
        ->setMaxResults($pageSize);

      $items = $qb->getQuery()->getResult();

      // total
      $tq = $this->createQueryBuilder('l')
          ->select('COUNT(DISTINCT l.id)')
          ->leftJoin('l.cabinet', 'c')
          ->leftJoin('l.specialties', 's')
          ->andWhere('l.isActive = :active')
          ->setParameter('active', true);

      if (!empty($q['name'])) {
          $tq->andWhere("LOWER(CONCAT(l.firstName, ' ', l.lastName)) LIKE :q")
            ->setParameter('q', '%' . mb_strtolower($q['name']) . '%');
      }
      if (!empty($q['cabinet'])) {
          $tq->andWhere('LOWER(c.name) LIKE :cab')
            ->setParameter('cab', '%' . mb_strtolower($q['cabinet']) . '%');
      }
      if (!empty($q['city'])) {
          $tq->andWhere('LOWER(l.city) LIKE :city')
            ->setParameter('city', '%' . mb_strtolower($q['city']) . '%');
      }
      if (!empty($q['specialty'])) {
          $tq->andWhere('s.id = :sid')->setParameter('sid', (int)$q['specialty']);
      }

      $total = (int)$tq->getQuery()->getSingleScalarResult();
      return new Pagination($items, $total, $page, $pageSize);
  }

}
