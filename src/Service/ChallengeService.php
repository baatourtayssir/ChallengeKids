<?php

namespace App\Service;

use App\Entity\Challenge;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class ChallengeService
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /**
     * ChallengeService constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param int $id
     *
     * @return Challenge|null
     */
    public function get(int $id): ?Challenge
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
    public function getChallenge(array $params = [], int $limit = null, $offset = null): ?array
    {
        return $this->getRepository()->findBy($params, ['id' => 'DESC'], $limit, $offset);
    }

    /**
     * @return EntityRepository
     */
    protected function getRepository(): EntityRepository
    {
        return $this->entityManager->getRepository(Challenge::class);
    }

    /**
     * @param Challenge $challenge
     * @return Challenge
     * @throws \Exception
     */
    public function persist(Challenge $challenge): Challenge
    {
        try {
            $this->entityManager->persist($challenge);
            $this->entityManager->flush();
        } catch (\Exception $exception) {
            throw new \Exception($exception);
        }

        return $challenge;
    }

    /**
     * @param Challenge $challenge
     * @return bool
     * @throws \Exception
     */
    public function remove(Challenge $challenge): bool
    {
        $this->entityManager->remove($challenge);
        $this->entityManager->flush();

        return true;
    }

    public function save(Challenge $challenge): Challenge
    {
        $this->entityManager->persist($challenge);
        $this->entityManager->flush();

        return $challenge;
    }

    public function toJson($challenge): array
    {
        return [
            'id' => $challenge->getId(),
            'title' => $challenge->getTitle(),
            'description' => $challenge->getDescription(),
            'visibility' => $challenge->getVisibility(),
            'cours_id' => $challenge->getCours()?->getId(),
        ];
    }
}
