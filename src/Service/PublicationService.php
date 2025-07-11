<?php

namespace App\Service;

use App\Entity\Publication;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class PublicationService
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /**
     * PublicationService constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param int $id
     * @return Publication|null
     */
    public function get(int $id): ?Publication
    {
        return $this->getRepository()->find($id);
    }

    /**
     * @param array $params
     * @param int|null $limit
     * @param null $offset
     * @return array|null
     */
    public function getPublication(array $params = [], int $limit = null, $offset = null): ?array
    {
        return $this->getRepository()->findBy($params, ['id' => 'DESC'], $limit, $offset);
    }

    /**
     * @return EntityRepository
     */
    protected function getRepository(): EntityRepository
    {
        return $this->entityManager->getRepository(Publication::class);
    }

    /**
     * @param Publication $publication
     * @return Publication
     * @throws \Exception
     */
    public function persist(Publication $publication): Publication
    {
        try {
            $this->entityManager->persist($publication);
            $this->entityManager->flush();
        } catch (\Exception $exception) {
            throw new \Exception($exception);
        }

        return $publication;
    }

    /**
     * @param Publication $publication
     * @return bool
     * @throws \Exception
     */
    public function remove(Publication $publication): bool
    {
        $this->entityManager->remove($publication);
        $this->entityManager->flush();

        return true;
    }

    public function save(Publication $publication): Publication
    {
        $this->entityManager->persist($publication);
        $this->entityManager->flush();

        return $publication;
    }

    public function toJson($publication): array
    {
        return [
            'id' => $publication->getId(),
            'title' => $publication->getTitle(),
            'content' => $publication->getContent(),
            'datePublication' => $publication->getDatePublication()?->format('Y-m-d H:i:s'),
            'visibility' => $publication->getVisibility(),
            'nbVues' => $publication->getNbVues(),
            'auteur_id' => $publication->getAuteur()?->getId(),
        ];
    }
}
