<?php

namespace App\Service;

use App\Entity\Cours;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class CoursService
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /**
     * CoursService constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param int $id
     *
     * @return Cours|null
     */
    public function get(int $id): ?Cours
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
    public function getCours(array $params = [], int $limit = null, $offset = null): ?array
    {
        return $this->getRepository()->findBy($params, ['id' => 'DESC'], $limit, $offset);
    }

    /**
     * @return EntityRepository
     */
    protected function getRepository(): EntityRepository
    {
        return $this->entityManager->getRepository(Cours::class);
    }

    /**
     * @param Cours $cours
     * @return Cours
     * @throws \Exception
     */
    public function persist(Cours $cours): Cours
    {
        try {
            $this->entityManager->persist($cours);
            $this->entityManager->flush();
        } catch (\Exception $exception) {
            throw new \Exception($exception);
        }

        return $cours;
    }

    /**
     * @param Cours $cours
     * @return bool
     * @throws \Exception
     */
    public function remove(Cours $cours): bool
    {
        $this->entityManager->remove($cours);
        $this->entityManager->flush();

        return true;
    }

    public function save(Cours $cours): Cours
    {
        $this->entityManager->persist($cours);
        $this->entityManager->flush();

        return $cours;
    }

    public function toJson(Cours $cours): array
    {
        return [
            'id' => $cours->getId(),
            'title' => $cours->getTitle(),
            'description' => $cours->getDescription(),
            'category' => $cours-> getCategory(),
        ];
    }
}
