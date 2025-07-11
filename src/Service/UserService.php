<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class UserService
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /**
     * UserService constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param int $id
     *
     * @return User|null
     */
    public function get(int $id): ?User
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
    public function getUser(Array $params = array(), int $limit = null, $offset = null): ?array
    {
        return $this->getRepository()->findBy($params, ['id' => 'DESC'], $limit, $offset);
    }

    /**
     * @return EntityRepository
     */
    protected function getRepository(): EntityRepository
    {
        return $this->entityManager->getRepository(User::class);
    }

    /**
     * @param User $User
     * @return User
     * @throws \Exception
     */
    public function persist(User $User): User
    {
        try {
            $this->entityManager->persist($User);
            $this->entityManager->flush();
        } catch (\Exception $exception) {
            throw new \Exception($exception);
        }

        return $User;
    }

    /**
     * @param User $User
     * @return bool
     * @throws \Exception
     */
    public function remove(User $User): bool
    {
        $this->entityManager->remove($User);
        $this->entityManager->flush();

        return true;
    }

    public function save(User $User): User
    {
        $this->entityManager->persist($User);
        $this->entityManager->flush();

        return $User;
    }

    public function toJson($User)
    {
        $jsonUser['id'] = $User->getId();
        $jsonUser['email'] = $User->getEmail();
        $jsonUser['password'] = $User->getPassword();
        $jsonUser['firstName'] = $User->getFirstName();
        $jsonUser['lastName'] = $User->getLastName();
        $jsonUser['roles'] = $User->getRoles();

        return $jsonUser;
    }
}
