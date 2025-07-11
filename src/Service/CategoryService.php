<?php

namespace App\Service;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class CategoryService
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /**
     * CategoryService constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param int $id
     *
     * @return Category|null
     */
    public function get(int $id): ?Category
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
    public function getCategory(Array $params = array(), int $limit = null, $offset = null): ?array
    {
        return $this->getRepository()->findBy($params, ['id' => 'DESC'], $limit, $offset);
    }

    /**
     * @return EntityRepository
     */
    protected function getRepository(): EntityRepository
    {
        return $this->entityManager->getRepository(Category::class);
    }

    /**
     * @param Category $Category
     * @return Category
     * @throws \Exception
     */
    public function persist(Category $Category): Category
    {
        try {
            $this->entityManager->persist($Category);
            $this->entityManager->flush();
        } catch (\Exception $exception) {
            throw new \Exception($exception);
        }

        return $Category;
    }

    /**
     * @param Category $Category
     * @return bool
     * @throws \Exception
     */
    public function remove(Category $Category): bool
    {
        $this->entityManager->remove($Category);
        $this->entityManager->flush();

        return true;
    }

    public function save(Category $Category): Category
    {
        $this->entityManager->persist($Category);
        $this->entityManager->flush();

        return $Category;
    }

    public function toJson($category)
    {
        $jsoncategory['id'] = $category->getId();
        $jsoncategory['intitule'] = $category->getIntitule();

        return $jsoncategory;
    }
}
