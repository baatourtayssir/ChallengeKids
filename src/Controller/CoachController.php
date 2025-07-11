<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\Coach;
use App\Service\CoachService;
use App\Form\CoachForm;

#[Route('/api/coach', name: 'Coach_')]
class CoachController extends AbstractController
{
    private $CoachService;

    public function __construct(CoachService $CoachService)
    {
        $this->CoachService = $CoachService;
    }

    /**
     * List of all categories.    
     */
    #[OA\Response(
        response: 200,
        description: 'Returns list of all Coachs',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Coach::class))
        )
    )]
    #[OA\Tag(name: 'Coach')]
    #[Route('/list', name: '_list', methods: ['GET'])]
    public function index()
    {
        $list = $this->CoachService->getCoach();

        $jsonCoach = [];
        foreach ($list as $key => $Coach) {
            $jsonCoach[$key] = $this->CoachService->toJson($Coach);
        }

        return new JsonResponse($jsonCoach);
    }
// /**
//      * List of active Categories.    
//      */
//     #[OA\Response(
//         response: 200,
//         description: 'Returns list of active Categories',
//         content: new OA\JsonContent(
//             type: 'array',
//             items: new OA\Items(ref: new Model(type: Coach::class))
//         )
//     )]
//     #[OA\Tag(name: 'Coach')]
//     #[Route('/list_by_intitule', name: 'list_by_intitule', methods: ['GET'])]
//     public function byIntitule()
//     {
//         $list = $this->CoachService->getCoach(['intitule'=>true]);

//         $jsonCoach = [];
//         foreach ($list as $key => $Coach) {
//             $jsonCoach[$key] = $this->CoachService->toJson($Coach);
//         }

//         return new JsonResponse($jsonCoach);
//     }

    #[OA\Response(
        response: 200,
        description: 'Creates a new Coach object',
        content: new OA\JsonContent(ref: new Model(type: Coach::class))
    )]
    #[OA\Response(
        response: 300,
        description: 'Invalid formType',
    )]
    #[OA\Response(
        response: 400,
        description: 'Bad request',
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: Object::class,
            example: [
                'email' => 'tayssir@gmail.com',
                'password' => 'tayssir',
                'firstName' => 'baatour',
                'lastName' => 'tayssir',
        
            ]
        )
    )]
    #[OA\Tag(name: 'Coach')]
    #[Route('/add', name: '_add', methods: ['POST'])]
    public function createCoach(Request $request, UserPasswordHasherInterface $hasher)
    {
        $data = json_decode($request->getContent(), true);

        $Coach = new Coach();
        $form = $this->createForm(CoachForm::class, $Coach);
        $form->submit($data);

        // Hasher le mot de passe
        $hashedPassword = $hasher->hashPassword($Coach, $data['password']);
        $Coach->setPassword($hashedPassword);

        // Définir le rôle par défaut
        $Coach->setRoles(['ROLE_COACH']);

        $Coach = $this->CoachService->persist($Coach);

        return new JsonResponse($this->CoachService->toJson($Coach), JsonResponse::HTTP_CREATED);
    }

    #[OA\Response(
        response: 200,
        description: 'Updates an Coach object',
        content: new OA\JsonContent(ref: new Model(type: Coach::class))
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: Object::class,
            example: [
                'email' => 'email',
                'password' => 'password',
                'firstName' => 'firstName',
                'lastName' => 'lastName',

                
            ]
        )
    )]
    #[OA\Tag(name: 'Coach')]
    #[Route('/{id}/edit', name: '_edit', methods: ['PATCH'])]
    public function updateCoach(Coach $Coach, Request $request, UserPasswordHasherInterface $hasher)
    {
        $data = json_decode($request->getContent(), true);

        $form = $this->createForm(CoachForm::class, $Coach);
        $form->submit($data, false);
        $hashedPassword = $hasher->hashPassword($Coach, $data['password']);
        $Coach->setPassword($hashedPassword);

        $Coach = $this->CoachService->persist($Coach);
        return new JsonResponse($this->CoachService->toJson($Coach));
    } 

    // #[OA\Response(
    //     response: 200,
    //     description: 'update intitule of a Coach',
    //     content: new OA\JsonContent(
    //         type: 'string',
    //     )
    // )]
    // #[OA\RequestBody(
    //     required: true,
    //     content: new OA\JsonContent(
    //         type: Object::class,
    //         example: [
    //             'intitule' => 'intitule',
    //         ]
    //     )
    // )]
    // #[OA\Tag(name: 'Coach')]
    // #[Route('/{id}/intitule', name: '_intitule', methods: ['PUT'])]
    // public function updateIntituleOfCoach(Coach $Coach, Request $request)
    // {
    //     $data = json_decode($request->getContent(), true);
    //     $Coach->setIntitule($data['intitule']);
    //     $Coach = $this->CoachService->persist($Coach);

    //     return new JsonResponse($this->CoachService->toJson($Coach));
    // }

    #[OA\Response(
        response: 200,
        description: 'Deletes an Coach',
        content: new OA\JsonContent(
            type: 'string',
        )
    )]
    #[OA\Tag(name: 'Coach')]
    #[Route('/{id}/delete', name: '_delete', methods: ['DELETE'])]
    public function deleteCoach($id, Request $request)
    {
        $Coach = $this->CoachService->get($id);
        try {
            $this->CoachService->remove($Coach);
            //$request->getSession()->getFlashBag()->add('success', 'évènement supprimé avec succès !');
        } catch (\Exception $exception) {
            //$request->getSession()->getFlashBag()->add('danger', 'un ou plusieurs objets liés  à cette entité !');
        }

        return new JsonResponse($request);
    }
}