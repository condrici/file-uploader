<?php

namespace App\Repository;

use App\Entity\Image;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;

/**
 * @method Image|null find($id, $lockMode = null, $lockVersion = null)
 * @method Image|null findOneBy(array $criteria, array $orderBy = null)
 * @method Image[]    findAll()
 * @method Image[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Image::class);
    }

    /**
     * @param int|null $offset
     * @param int|null $limit
     * @return Image[]
     */
    public function findAllWithPagination(?int $offset, ?int $limit): array
    {
        return $this->createQueryBuilder('i')
            ->getQuery()
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getResult(Query::HYDRATE_OBJECT);
    }

    /**
     * @param int $id
     * @return Image|null
     * @throws NonUniqueResultException
     */
    public function findById(int $id): ?Image
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.id = :val')
            ->setParameter('val', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param string $string
     * @return Image|null
     * @throws NonUniqueResultException
     */
    public function findByPictureTitle(string $string): ?Image
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.picture_title = :val')
            ->setParameter('val', $string)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
