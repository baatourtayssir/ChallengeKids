<?php

namespace App\Service;

use App\Entity\Coach;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class CoachService
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /**
     * CoachService constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param int $id
     *
     * @return Coach|null
     */
    public function get(int $id): ?Coach
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
    public function getCoach(Array $params = array(), int $limit = null, $offset = null): ?array
    {
        return $this->getRepository()->findBy($params, ['id' => 'DESC'], $limit, $offset);
    }

    /**
     * @return EntityRepository
     */
    protected function getRepository(): EntityRepository
    {
        return $this->entityManager->getRepository(Coach::class);
    }

    /**
     * @param Coach $Coach
     * @return Coach
     * @throws \Exception
     */
    public function persist(Coach $Coach): Coach
    {
        try {
            $this->entityManager->persist($Coach);
            $this->entityManager->flush();
        } catch (\Exception $exception) {
            throw new \Exception($exception);
        }

        return $Coach;
    }

    /**
     * @param Coach $Coach
     * @return bool
     * @throws \Exception
     */
    public function remove(Coach $Coach): bool
    {
        $this->entityManager->remove($Coach);
        $this->entityManager->flush();

        return true;
    }

    public function save(Coach $Coach): Coach
    {
        $this->entityManager->persist($Coach);
        $this->entityManager->flush();

        return $Coach;
    }

    public function toJson($Coach)
    {
        $jsonCoach['id'] = $Coach->getId();
        $jsonCoach['email'] = $Coach->getEmail();
        $jsonCoach['password'] = $Coach->getPassword();
        $jsonCoach['firstName'] = $Coach->getFirstName();
        $jsonCoach['lastName'] = $Coach->getLastName();
        $jsonCoach['roles'] = $Coach->getRoles();

        return $jsonCoach;
    }
}
