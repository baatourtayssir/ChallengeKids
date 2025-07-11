<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use App\Entity\Publication;
use App\Service\PublicationService;
use App\Form\PublicationForm;

#[Route('/api/publication', name: 'publication_')]
class PublicationController extends AbstractController
{
    private $publicationService;
    private $tokenStorage;

    public function __construct(PublicationService $publicationService, TokenStorageInterface $tokenStorage)
    {
        $this->publicationService = $publicationService;
        $this->tokenStorage = $tokenStorage;
    }

    #[OA\Response(
        response: 200,
        description: 'Returns list of all publications',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Publication::class))
        )
    )]
    #[OA\Tag(name: 'Publication')]
    #[Route('/list', name: '_list', methods: ['GET'])]
    public function index()
    {
        $list = $this->publicationService->getPublication();

        $jsonPublications = [];
        foreach ($list as $key => $pub) {
            $jsonPublications[$key] = $this->publicationService->toJson($pub);
        }

        return new JsonResponse($jsonPublications);
    }

    #[OA\Response(
        response: 200,
        description: 'Creates a new publication object',
        content: new OA\JsonContent(ref: new Model(type: Publication::class))
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: "object",
            example: [
                "title" => "Titre de la publication",
                "content" => "Contenu",
                "visibility" => "public",
                "nbVues" => 0
            ]
        )
    )]
    #[OA\Tag(name: 'Publication')]
    #[Route('/add', name: '_add', methods: ['POST'])]
    public function createPublication(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $publication = new Publication();
        $form = $this->createForm(PublicationForm::class, $publication);
        $form->submit($data);

        $user = $this->tokenStorage->getToken()?->getUser();
        $publication->setAuteur($user);
        $publication->setDatePublication(new \DateTime());

        $publication = $this->publicationService->persist($publication);

        return new JsonResponse($this->publicationService->toJson($publication), JsonResponse::HTTP_CREATED);
    }

    #[OA\Response(
        response: 200,
        description: 'Updates a publication object',
        content: new OA\JsonContent(ref: new Model(type: Publication::class))
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: "object",
            example: [
                "title" => "Titre mis à jour",
                "content" => "Contenu mis à jour",
                "visibility" => "private"
            ]
        )
    )]
    #[OA\Tag(name: 'Publication')]
    #[Route('/{id}/edit', name: '_edit', methods: ['PUT'])]
    public function updatePublication(Publication $publication, Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $form = $this->createForm(PublicationForm::class, $publication);
        $form->submit($data, false);

        $user = $this->tokenStorage->getToken()?->getUser();
        $publication->setAuteur($user);

        $publication = $this->publicationService->persist($publication);

        return new JsonResponse($this->publicationService->toJson($publication));
    }

    #[OA\Response(
        response: 200,
        description: 'Deletes a publication',
        content: new OA\JsonContent(type: 'string')
    )]
    #[OA\Tag(name: 'Publication')]
    #[Route('/{id}/delete', name: '_delete', methods: ['DELETE'])]
    public function deletePublication($id)
    {
        $publication = $this->publicationService->get($id);
        try {
            $this->publicationService->remove($publication);
        } catch (\Exception $exception) {
            // gérer l’erreur si besoin
        }

        return new JsonResponse(['success' => true]);
    }
}
