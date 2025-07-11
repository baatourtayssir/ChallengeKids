<?php

namespace App\Service;

use App\Entity\Event;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class EventService
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /**
     * EventService constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param int $id
     *
     * @return Event|null
     */
    public function get(int $id): ?Event
    {
        return $this->getRepository()->find($id);
    }

    /**
     * @param array $params
     * @param int|null $limit
     * @param null $offset
     *
     * @return array|null
     */
    public function getEvents(array $params = [], int $limit = null, $offset = null): ?array
    {
        return $this->getRepository()->findBy($params, ['id' => 'DESC'], $limit, $offset);
    }

    /**
     * @return EntityRepository
     */
    protected function getRepository(): EntityRepository
    {
        return $this->entityManager->getRepository(Event::class);
    }

    /**
     * @param Event $event
     * @return Event
     * @throws \Exception
     */
    public function persist(Event $event): Event
    {
        try {
            $this->entityManager->persist($event);
            $this->entityManager->flush();
        } catch (\Exception $exception) {
            throw new \Exception($exception);
        }

        return $event;
    }

    /**
     * @param Event $event
     * @return bool
     * @throws \Exception
     */
    public function remove(Event $event): bool
    {
        $this->entityManager->remove($event);
        $this->entityManager->flush();

        return true;
    }

    public function save(Event $event): Event
    {
        $this->entityManager->persist($event);
        $this->entityManager->flush();

        return $event;
    }

    public function toJson(Event $event): array
    {
        return [
            'id' => $event->getId(),
            'title' => $event->getTitle(),
            'description' => $event->getDescription(),
            'date' => $event->getDate()?->format('Y-m-d H:i:s'),
            'category' => $event->getCategory()?->getId()
        ];
    }
}
