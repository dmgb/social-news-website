<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Message;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;

class MessageRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry);
    }

    public function findByReceiver(User $receiver): array
    {
        $qb = $this->createQueryBuilder('m');
        $qb->andWhere('m.receiver = :receiver');

        return $qb
            ->setParameter('receiver', $receiver)
            ->orderBy('m.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findBySender(User $sender): array
    {
        $qb = $this->createQueryBuilder('m');
        $qb->andWhere('m.sender = :sender');

        return $qb
            ->setParameter('sender', $sender)
            ->orderBy('m.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getEntityClass(): string
    {
        return Message::class;
    }
}
