<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\ORM\Query\Parameter;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Post::class);
    }

    /**
     * @return Post[]
     */
    public function findByFilters($query = null, $datele = null, $datege = null)
    {
        $qb = $this->createQueryBuilder('p')
            ->join('p.createdBy', 'u');

        $parameters = array();

        if ($query !== null && strlen($query) > 0) {
            $query = strtolower($query);
            $qb->andWhere('LOWER(p.title) LIKE :query')
                ->orWhere('LOWER(u.username) LIKE :query');
            $parameters[] = new Parameter('query', $query);
        }

        if ($datele !== null && strlen($datele) > 0) {
            $dle = new \DateTime(date('Y-m-d',strtotime($datele))." 23:59:59");
            $qb->andWhere('p.createdAt <= :datele');
            $parameters[] = new Parameter('datele', $dle);
        
        } 
        if ($datege !== null && strlen($datege) > 0) { 
            $dge = new \DateTime(date('Y-m-d',strtotime($datege))." 00:00:00");
            $qb->andWhere('p.createdAt >= :datege');
            $parameters[] = new Parameter('datege', $dge);
        }

        $qb->setParameters(new ArrayCollection($parameters));
        return $qb->getQuery()->getResult();
    }

    /*
    public function findOneBySomeField($value): ?Post
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
