<?php
namespace App\Repository;

use App\Entity\Image;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Image>
 */
class ImageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Image::class);
    }

    /**
     * Récupère toutes les images d'une entité
     *
     * @param string $entityType Type de l'entité (Cabinet, Lawyer, etc.)
     * @param int $entityId ID de l'entité
     * @return Image[]
     */
    public function findByEntity(string $entityType, int $entityId): array
    {
        return $this->createQueryBuilder('i')
            ->where('i.entityType = :type')
            ->andWhere('i.entityId = :id')
            ->setParameter('type', $entityType)
            ->setParameter('id', $entityId)
            ->orderBy('i.position', 'ASC')
            ->addOrderBy('i.uploadedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère l'image principale d'une entité
     *
     * @param string $entityType Type de l'entité
     * @param int $entityId ID de l'entité
     * @return Image|null
     */
    public function findPrimaryImage(string $entityType, int $entityId): ?Image
    {
        return $this->createQueryBuilder('i')
            ->where('i.entityType = :type')
            ->andWhere('i.entityId = :id')
            ->andWhere('i.isPrimary = :primary')
            ->setParameter('type', $entityType)
            ->setParameter('id', $entityId)
            ->setParameter('primary', true)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Récupère les images d'une entité par catégorie
     *
     * @param string $entityType Type de l'entité
     * @param int $entityId ID de l'entité
     * @param string $category Catégorie
     * @return Image[]
     */
    public function findByEntityAndCategory(string $entityType, int $entityId, string $category): array
    {
        return $this->createQueryBuilder('i')
            ->where('i.entityType = :type')
            ->andWhere('i.entityId = :id')
            ->andWhere('i.category = :category')
            ->setParameter('type', $entityType)
            ->setParameter('id', $entityId)
            ->setParameter('category', $category)
            ->orderBy('i.position', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Compte le nombre d'images d'une entité
     *
     * @param string $entityType Type de l'entité
     * @param int $entityId ID de l'entité
     * @return int
     */
    public function countByEntity(string $entityType, int $entityId): int
    {
        return (int) $this->createQueryBuilder('i')
            ->select('COUNT(i.id)')
            ->where('i.entityType = :type')
            ->andWhere('i.entityId = :id')
            ->setParameter('type', $entityType)
            ->setParameter('id', $entityId)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
