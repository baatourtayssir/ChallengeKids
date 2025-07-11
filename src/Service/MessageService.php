<?php

namespace App\Service;

use App\Entity\Message;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class MessageService
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /**
     * MessageService constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param int $id
     * @return Message|null
     */
    public function get(int $id): ?Message
    {
        return $this->getRepository()->find($id);
    }

    /**
     * @param array $params
     * @param int|null $limit
     * @param int|null $offset
     * @return array|null
     */
    public function getMessages(array $params = [], int $limit = null, int $offset = null): ?array
    {
        return $this->getRepository()->findBy($params, ['id' => 'DESC'], $limit, $offset);
    }

    /**
     * @return EntityRepository
     */
    protected function getRepository(): EntityRepository
    {
        return $this->entityManager->getRepository(Message::class);
    }

    /**
     * @param Message $message
     * @return Message
     * @throws \Exception
     */
    public function persist(Message $message): Message
    {
        try {
            $this->entityManager->persist($message);
            $this->entityManager->flush();
        } catch (\Exception $exception) {
            throw new \Exception($exception);
        }

        return $message;
    }

    /**
     * @param Message $message
     * @return bool
     * @throws \Exception
     */
    public function remove(Message $message): bool
    {
        $this->entityManager->remove($message);
        $this->entityManager->flush();

        return true;
    }

    public function save(Message $message): Message
    {
        $this->entityManager->persist($message);
        $this->entityManager->flush();

        return $message;
    }

    /**
     * @param Message $message
     * @return array
     */
    public function toJson(Message $message): array
    {
        return [
            'id' => $message->getId(),
            'content' => $message->getContent(),
            'date' => $message->getDate()->format('Y-m-d H:i:s'),
            'sender_id' => $message->getSender()?->getId(),
            'recipient_id' => $message->getRecipient()?->getId(),
        ];
    }
}
