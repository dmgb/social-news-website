<?php declare(strict_types=1);

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * @method object|null find($id, $lockMode = null, $lockVersion = null)
 * @method object|null findOneBy(array $criteria, array $orderBy = null)
 * @method object[]    findAll()
 * @method object[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
abstract class AbstractRepository extends ServiceEntityRepository
{
    /**
     * @throws Exception
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, $this->getEntityClass());
    }

    public function save(object $object): void
    {
        $this->_em->persist($object);
        $this->_em->flush();
    }

    abstract protected function getEntityClass(): string;
}
