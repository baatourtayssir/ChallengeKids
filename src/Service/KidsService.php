<?php

namespace App\Service;

use App\Entity\Kids;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class KidsService
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /**
     * KidsService constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param int $id
     *
     * @return Kids|null
     */
    public function get(int $id): ?Kids
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
    public function getKids(Array $params = array(), int $limit = null, $offset = null): ?array
    {
        return $this->getRepository()->findBy($params, ['id' => 'DESC'], $limit, $offset);
    }

    /**
     * @return EntityRepository
     */
    protected function getRepository(): EntityRepository
    {
        return $this->entityManager->getRepository(Kids::class);
    }

    /**
     * @param Kids $Kids
     * @return Kids
     * @throws \Exception
     */
    public function persist(Kids $Kids): Kids
    {
        try {
            $this->entityManager->persist($Kids);
            $this->entityManager->flush();
        } catch (\Exception $exception) {
            throw new \Exception($exception);
        }

        return $Kids;
    }

    /**
     * @param Kids $Kids
     * @return bool
     * @throws \Exception
     */
    public function remove(Kids $Kids): bool
    {
        $this->entityManager->remove($Kids);
        $this->entityManager->flush();

        return true;
    }

    public function save(Kids $Kids): Kids
    {
        $this->entityManager->persist($Kids);
        $this->entityManager->flush();

        return $Kids;
    }

    public function toJson($Kids)
    {
        $jsonKids['id'] = $Kids->getId();
        $jsonKids['email'] = $Kids->getEmail();
        $jsonKids['password'] = $Kids->getPassword();
        $jsonKids['firstName'] = $Kids->getFirstName();
        $jsonKids['lastName'] = $Kids->getLastName();
        $jsonKids['interests'] = $Kids->getInterests();
        $jsonKids['roles'] = $Kids->getRoles();

        return $jsonKids;
    }
}
