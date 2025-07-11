<?php 

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Attribute\Model;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use App\Entity\Cours;
use App\Service\CoursService;
use App\Form\CoursForm;

#[Route('/api/cours', name: 'cours_')]
class CoursController extends AbstractController
{
    private $coursService;

    public function __construct(CoursService $coursService)
    {
        $this->coursService = $coursService;
    }

    /**
     * List of all cours.
     */
    #[OA\Response(
        response: 200,
        description: 'Returns list of all cours',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Cours::class))
        )
    )]
    #[OA\Tag(name: 'Cours')]
    #[Route('/list', name: '_list', methods: ['GET'])]
    public function index()
    {
        $list = $this->coursService->getCours();

        $jsonCours = [];
        foreach ($list as $key => $cours) {
            $jsonCours[$key] = $this->coursService->toJson($cours);
        }

        return new JsonResponse($jsonCours);
    }

    #[OA\Response(
        response: 200,
        description: 'Creates a new cours object',
        content: new OA\JsonContent(ref: new Model(type: Cours::class))
    )]
    #[OA\Response(response: 300, description: 'Invalid formType')]
    #[OA\Response(response: 400, description: 'Bad request')]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: Object::class,
            example: [
                'title' => 'Nom du cours',
                'description' => 'Description du cours',
                'category' => 1
            ]
        )
    )]
    #[OA\Tag(name: 'Cours')]
    #[Route('/add', name: '_add', methods: ['POST'])]
    public function createCours(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $cours = new Cours();
        $form = $this->createForm(CoursForm::class, $cours);
        $form->submit($data);

        $cours = $this->coursService->persist($cours);
        return new JsonResponse($data);
    }

    #[OA\Response(
        response: 200,
        description: 'Updates a cours object',
        content: new OA\JsonContent(ref: new Model(type: Cours::class))
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: Object::class,
            example: [
                'title' => 'Titre mis à jour',
                'description' => 'Nouvelle description',
                'category' => 1
            ]
        )
    )]
    #[OA\Tag(name: 'Cours')]
    #[Route('/{id}/edit', name: '_edit', methods: ['PUT'])]
    public function updateCours(Cours $cours, Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $form = $this->createForm(CoursForm::class, $cours);
        $form->submit($data, false);

        $cours = $this->coursService->persist($cours);
        return new JsonResponse($this->coursService->toJson($cours));
    }

    #[OA\Response(
        response: 200,
        description: 'Deletes a cours',
        content: new OA\JsonContent(type: 'string')
    )]
    #[OA\Tag(name: 'Cours')]
    #[Route('/{id}/delete', name: '_delete', methods: ['DELETE'])]
    public function deleteCours($id, Request $request)
    {
        $cours = $this->coursService->get($id);
        try {
            $this->coursService->remove($cours);
        } catch (\Exception $exception) {
            // Optionally log or return error response
        }

        return new JsonResponse(['success' => true]);
    }
}
