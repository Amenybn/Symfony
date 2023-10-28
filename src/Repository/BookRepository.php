<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 *
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
    public function searchByRef($ref){
        return $this->createQueryBuilder('b')
        ->where('b.ref LIKE :reference')
        ->setParameter('reference',$ref)
        ->getQuery()
        ->getResult();
    }
    public function listBooksByAuthor()
{
    $query = $this->createQueryBuilder('b')
        ->join('b.author', 'a')
        ->orderBy('a.username', 'ASC')
        ->getQuery();

    return $query->getResult();
}
public function getBooksByDate()
{
    $qb = $this->createQueryBuilder('b');
    $qb->select('b')
        ->join('b.author', 'a')
        ->setParameter('nb',35)
        ->Where('a.nbBooks > :nb');


    return $qb->getQuery()
    ->getResult();
}
public function updateShakespeareBooks()
{
    $qb = $this->createQueryBuilder('b')
        ->update()
        ->set('b.category', ':UpdateCategory')
        ->where('b.author IN (SELECT a.id FROM App\Entity\Author a WHERE a.username = :authorName)')
        ->setParameter('UpdateCategory', 'Romance')
        ->setParameter('authorName', 'William Shakespeare')
        ->getQuery()
        ->execute();
}
public function findByAuthorUsername($authorUsername)
{
    return $this->createQueryBuilder('b')
        ->join('b.author', 'a')
        ->where('a.username = :authorUsername')
        ->setParameter('authorUsername', $authorUsername)
        ->getQuery()
        ->getResult();
}
public function sumBooks()
{
    $entityManager = $this->getEntityManager();

    $dql = "SELECT 
                SUM(CASE WHEN b.published = 1 THEN 1 ELSE 0 END) AS publishedCount,
                SUM(CASE WHEN b.published = 0 THEN 1 ELSE 0 END) AS unpublishedCount
            FROM App\Entity\Book b";

    $query = $entityManager->createQuery($dql);
    $result = $query->getSingleResult();

    return [
        'publishedCount' => $result['publishedCount'],
        'unpublishedCount' => $result['unpublishedCount'],
    ];
}
public function sumScienceFictionBooks()
{
    $entityManager = $this->getEntityManager();

    $dql = "SELECT COUNT(b.id) AS scienceFictionCount
            FROM App\Entity\Book b
            WHERE b.category = 'Science Fiction'";

    $query = $entityManager->createQuery($dql);
    $result = $query->getSingleResult();

    return $result['scienceFictionCount'];
}

public function findBooksBetweenDates($startDate, $endDate)
{
    $entityManager = $this->getEntityManager();

    $dql = "SELECT b
            FROM App\Entity\Book b
            WHERE b.published = 1
            AND b.publicationDate BETWEEN :startDate AND :endDate";

    $query = $entityManager->createQuery($dql);
    $query->setParameter('startDate', $startDate);
    $query->setParameter('endDate', $endDate);

    return $query->getResult();
}

//    /**
//     * @return Book[] Returns an array of Book objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Book
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
