<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use App\Entity\Challenge;
use App\Service\ChallengeService;
use App\Form\ChallengeForm;

#[Route('/api/challenge', name: 'challenge_')]
class ChallengeController extends AbstractController
{
    private $challengeService;

    public function __construct(ChallengeService $challengeService)
    {
        $this->challengeService = $challengeService;
    }

    /**
     * List of all challenges.
     */
    #[OA\Response(
        response: 200,
        description: 'Returns list of all challenges',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Challenge::class))
        )
    )]
    #[OA\Tag(name: 'Challenge')]
    #[Route('/list', name: '_list', methods: ['GET'])]
    public function index()
    {
        $list = $this->challengeService->getChallenge();

        $jsonChallenge = [];
        foreach ($list as $key => $challenge) {
            $jsonChallenge[$key] = $this->challengeService->toJson($challenge);
        }

        return new JsonResponse($jsonChallenge);
    }

    #[OA\Response(
        response: 200,
        description: 'Creates a new challenge object',
        content: new OA\JsonContent(ref: new Model(type: Challenge::class))
    )]
    #[OA\Response(response: 300, description: 'Invalid formType')]
    #[OA\Response(response: 400, description: 'Bad request')]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: 'object',
            example: [
                'title' => 'Titre du challenge',
                'description' => 'Description du challenge',
                'visibility' => 'public',
                'cours' => 1 // ID du cours
            ]
        )
    )]
    #[OA\Tag(name: 'Challenge')]
    #[Route('/add', name: '_add', methods: ['POST'])]
    public function createChallenge(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $challenge = new Challenge();
        $form = $this->createForm(ChallengeForm::class, $challenge);
        $form->submit($data);

        $challenge = $this->challengeService->persist($challenge);
        return new JsonResponse($this->challengeService->toJson($challenge));
    }

    #[OA\Response(
        response: 200,
        description: 'Updates a challenge object',
        content: new OA\JsonContent(ref: new Model(type: Challenge::class))
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: 'object',
            example: [
                'title' => 'Titre mis à jour',
                'description' => 'Nouvelle description',
                'visibility' => 'private'
            ]
        )
    )]
    #[OA\Tag(name: 'Challenge')]
    #[Route('/{id}/edit', name: '_edit', methods: ['PUT'])]
    public function updateChallenge(Challenge $challenge, Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $form = $this->createForm(ChallengeForm::class, $challenge);
        $form->submit($data, false);

        $challenge = $this->challengeService->persist($challenge);
        return new JsonResponse($this->challengeService->toJson($challenge));
    }

    #[OA\Response(
        response: 200,
        description: 'Deletes a challenge',
        content: new OA\JsonContent(type: 'string')
    )]
    #[OA\Tag(name: 'Challenge')]
    #[Route('/{id}/delete', name: '_delete', methods: ['DELETE'])]
    public function deleteChallenge($id)
    {
        $challenge = $this->challengeService->get($id);
        try {
            $this->challengeService->remove($challenge);
        } catch (\Exception $exception) {
            // gérer l'erreur
        }

        return new JsonResponse(['success' => true]);
    }
}
