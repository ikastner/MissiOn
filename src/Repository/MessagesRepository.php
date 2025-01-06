<?php
// src/Repository/MessagesRepository.php

namespace App\Repository;

use App\Entity\Freelance;
use App\Entity\Gestionnaire;
use App\Entity\Messages;
use App\Entity\Personel;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\DBAL\Connection;

/**
 * @extends ServiceEntityRepository<Messages>
 */
class MessagesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Messages::class);
    }

    public function findByUsers(int $user1, int $user2): array
    {
        return $this->createQueryBuilder('m')
            ->where('(m.sender = :user1 AND m.receiver = :user2) OR (m.sender = :user2 AND m.receiver = :user1)')
            ->setParameters(['user1' => $user1, 'user2' => $user2])
            ->orderBy('m.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findConversationsForUser($user)
    {
        $qb = $this->createQueryBuilder('m')
            ->select('m.conversationId', 'COUNT(m.id) as messageCount', 'SUM(CASE WHEN m.isRead = 0 THEN 1 ELSE 0 END) as unreadCount')
            ->where('m.receiver = :user OR m.sender = :user')
            ->groupBy('m.conversationId')
            ->setParameter('user', $user)
            ->orderBy('m.createdAt', 'DESC');

        return $qb->getQuery()->getResult();
    }

    public function findMessagesByConversationId($conversationId, $user)
    {
        $qb = $this->createQueryBuilder('m')
            ->where('m.conversationId = :conversationId')
            ->andWhere('m.receiver = :user OR m.sender = :user')
            ->setParameter('conversationId', $conversationId)
            ->setParameter('user', $user)
            ->orderBy('m.createdAt', 'ASC');

        return $qb->getQuery()->getResult();
    }

    /**
     * @throws Exception
     */
   public function findReceiverForConversation($conversationId, $user)
{
    $userId = $user->getId(); // Get the user ID

    $conn = $this->getEntityManager()->getConnection();
    $sql = '
        SELECT u.*
        FROM user u
        LEFT JOIN messages msg
          ON msg.receiver_id = u.id OR msg.sender_id = u.id
        WHERE msg.conversation_id = :conversationId
          AND u.id != :userId
        LIMIT 1;
    ';
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':conversationId', $conversationId);
    $stmt->bindParam(':userId', $userId); // Bind the user ID
    $result = $stmt->executeQuery()->fetchAssociative();

    return $result ? $this->getEntityManager()->getRepository(User::class)->find($result['id']) : null;
}

    /**
     * @throws Exception
     */
    public function getConversationIdBetweenUsers(int $userId1, int $userId2): ?int
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "
            SELECT DISTINCT msg.conversation_id
            FROM messages msg
            WHERE (msg.sender_id = :userId1 AND msg.receiver_id = :userId2)
               OR (msg.sender_id = :userId2 AND msg.receiver_id = :userId1)
            LIMIT 1
        ";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':userId1', $userId1);
        $stmt->bindParam(':userId2', $userId2);
        $result = $stmt->executeQuery()->fetchAssociative();

        return $result ? (int)$result['conversation_id'] : null;
    }
    public function findLastMessageForConversation(int $conversationId): ?string
    {
        $qb = $this->createQueryBuilder('m')
            ->select('m.content', 'm.createdAt')
            ->where('m.conversationId = :conversationId')
            ->setParameter('conversationId', $conversationId)
            ->orderBy('m.createdAt', 'DESC')
            ->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult()['content'] ?? null;
    }

    public function findLastMessageForConversationCreatedAt(int $conversationId): ?string
    {
        $qb = $this->createQueryBuilder('m')
            ->select('m.createdAt')
            ->where('m.conversationId = :conversationId')
            ->setParameter('conversationId', $conversationId)
            ->orderBy('m.createdAt', 'DESC')
            ->setMaxResults(1);

        $result = $qb->getQuery()->getOneOrNullResult();

        return $result ? $result['createdAt']->format('Y-m-d H:i:s') : null;
    }

   /* public function findLastMessageForConversationCreateAt(int $conversationId): ?array
    {
        $qb = $this->createQueryBuilder('m')
            ->select('m.content', 'm.createdAt')
            ->where('m.conversationId = :conversationId')
            ->setParameter('conversationId', $conversationId)
            ->orderBy('m.createdAt', 'DESC')
            ->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }*/

    public function findAllConversationIds(): array
    {
        $qb = $this->createQueryBuilder('m')
            ->select('DISTINCT m.conversationId');

        return array_column($qb->getQuery()->getResult(), 'conversationId');
    }

    public function countUnreadMessagesForUser(int $conversationId, User $user): int
    {
        $qb = $this->createQueryBuilder('m')
            ->select('COUNT(m.id)')
            ->where('m.conversationId = :conversationId')
            ->andWhere('m.receiver = :user')
            ->andWhere('m.isRead = false')
            ->setParameter('conversationId', $conversationId)
            ->setParameter('user', $user);

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
    public function save(Messages $message): void
    {
        $this->getEntityManager()->persist($message);
        $this->getEntityManager()->flush();
    }

    public function findUserIdByRoleId($roleId): ?int
    {
        // Check if the ID belongs to a Freelance
        $freelance = $this->getEntityManager()->getRepository(Freelance::class)->find($roleId);
        if ($freelance) {
            return $freelance->getUser()->getId();
        }

        // Check if the ID belongs to a Gestionnaire
        $gestionnaire = $this->getEntityManager()->getRepository(Gestionnaire::class)->find($roleId);
        if ($gestionnaire) {
            return $gestionnaire->getUser()->getId();
        }

        // Check if the ID belongs to a Personel
        $personel = $this->getEntityManager()->getRepository(Personel::class)->find($roleId);
        if ($personel) {
            return $personel->getUser()->getId();
        }

        // If no match is found, return null
        return null;
    }

    public function findUserIdByRoleIdd(int $roleId): ?int
    {
        $qb = $this->createQueryBuilder('u')
            ->select('u.id')
            ->innerJoin('u.roles', 'r')
            ->where('r.id = :roleId')
            ->setParameter('roleId', $roleId)
            ->setMaxResults(1);

        $result = $qb->getQuery()->getOneOrNullResult();

        return $result ? $result['id'] : null;
    }


    public function getReceiverDetails($receiverId)
    {
        $user = $this->getEntityManager()->getRepository(User::class)->find($receiverId);

        if (!$user) {
            return null;
        }

        if ($user->getFreelance()) {
            return $this->getEntityManager()->getRepository(Freelance::class)->find($user->getFreelance()->getId());
        } elseif ($user->getGestionnaire()) {
            return $this->getEntityManager()->getRepository(Gestionnaire::class)->find($user->getGestionnaire()->getId());
        } else if($user->getPersonel()) {
            return $this->getEntityManager()->getRepository(Personel::class)->find($user->getPersonel()->getId());
        }else{
            return null;
        }


    }

    public function getRoleCurrentUser($user): ?string
    {
        if ($user->getFreelance()) {
            return 'freelance';
        } elseif ($user->getGestionnaire()) {
            return 'gestionnaire';
        } else if($user->getPersonel()) {
            return 'personel';
        }else{
            return null;
        }
    }

    public function findConversationsForUserSearch(User $user, string $search = ''): array
    {
        $qb = $this->createQueryBuilder('m')
            ->select('m.conversationId')
            ->where('m.sender = :user OR m.receiver = :user')
            ->setParameter('user', $user);

        if ($search) {
            $qb->join('m.receiver', 'r')
                ->andWhere('r.email LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        return $qb->getQuery()->getResult();
    }





    public function findConversationId(int $senderId, int $receiverId): ?string
    {
        $qb = $this->createQueryBuilder('m')
            ->select('m.conversationId')
            ->where('(m.sender = :sender AND m.receiver = :receiver) OR (m.sender = :receiver AND m.receiver = :sender)')
            ->setParameter('sender', $senderId)
            ->setParameter('receiver', $receiverId)
            ->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult()['conversationId'] ?? null;
    }
}




