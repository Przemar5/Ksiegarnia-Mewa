<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    public function notDeleted()
    {
        return $this
            ->findBy([
                'deletedAt' => null,
            ])
        ;
    }

    public function paginated(int $page = 1, int $quantity = 1)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.deletedAt IS NULL')
            ->setMaxResults($quantity)
            ->setFirstResult(($page - 1) * $quantity)
            ->orderBy('b.id', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function search(int $page = 1, int $quantity = 1, ?array $search)
    {
        $search['title'] = strtolower($search['title']);
        $qb = $this->createQueryBuilder('b');

        if (!empty($search['title'])) {
            $qb = $qb
                ->andWhere('LOWER(b.title) LIKE :title')
                ->setParameter('title', '%'.$search['title'].'%')
            ;
        }

        if (!empty($search['author'])) {
            $qb = $qb
                ->andWhere('b.author = :author')
                ->setParameter('author', $search['author'])
            ;
        }

        if (!empty($search['genres'])) {
            $qb = $qb
                ->innerJoin('b.bookGenres', 'bg')
                ->andWhere($qb->expr()->in('bg.genre', $search['genres']))
            ;
        }   

        if (!empty($search['tags'])) {
            $qb = $qb
                ->innerJoin('b.bookTags', 'bt')
                ->andWhere($qb->expr()->in('bt.tag', $search['tags']))
            ;
        }

        if (!empty($search['price']['min'])) {
            $qb = $qb->andWhere($qb->expr()->gte('b.price', $search['price']['min']));
        }

        if (!empty($search['price']['max'])) {
            $qb = $qb->andWhere($qb->expr()->lte('b.price', $search['price']['max']));
        }

        $qb = $qb->andWhere('b.deletedAt IS NULL');

        $count = $qb
            ->getQuery()
            ->getResult()
        ;

        $results = $qb
            ->setMaxResults($quantity)
            ->setFirstResult(($page - 1) * $quantity)
            ->getQuery()
            ->getResult()
        ;

        return [
            'books' => $results,
            'count' => count($count),
        ];
    }

    public function countExisting()
    {
        return $this->createQueryBuilder('b')
            ->select('count(b.id)')
            ->andWhere('b.deletedAt IS NULL')
            ->getQuery()
            ->getOneOrNullResult()[1]
        ;
    }

    public function findForIds(array $ids)
    {
        $qb = $this->createQueryBuilder('b');
        return $qb->andWhere('b.deletedAt IS NULL')
            ->andWhere($qb->expr()->in('b.id', $ids))
            ->getQuery()
            ->getResult()
        ;
    }

    // /**
    //  * @return Book[] Returns an array of Book objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Book
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
