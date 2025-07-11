<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\Kids;
use App\Service\KidsService;
use App\Form\KidsForm;

#[Route('/api/kids', name: 'Kids_')]
class KidsController extends AbstractController
{
    private $KidsService;

    public function __construct(KidsService $KidsService)
    {
        $this->KidsService = $KidsService;
    }

    /**
     * List of all categories.    
     */
    #[OA\Response(
        response: 200,
        description: 'Returns list of all Kidss',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Kids::class))
        )
    )]
    #[OA\Tag(name: 'Kids')]
    #[Route('/list', name: '_list', methods: ['GET'])]
    public function index()
    {
        $list = $this->KidsService->getKids();

        $jsonKids = [];
        foreach ($list as $key => $Kids) {
            $jsonKids[$key] = $this->KidsService->toJson($Kids);
        }

        return new JsonResponse($jsonKids);
    }
// /**
//      * List of active Categories.    
//      */
//     #[OA\Response(
//         response: 200,
//         description: 'Returns list of active Categories',
//         content: new OA\JsonContent(
//             type: 'array',
//             items: new OA\Items(ref: new Model(type: Kids::class))
//         )
//     )]
//     #[OA\Tag(name: 'Kids')]
//     #[Route('/list_by_intitule', name: 'list_by_intitule', methods: ['GET'])]
//     public function byIntitule()
//     {
//         $list = $this->KidsService->getKids(['intitule'=>true]);

//         $jsonKids = [];
//         foreach ($list as $key => $Kids) {
//             $jsonKids[$key] = $this->KidsService->toJson($Kids);
//         }

//         return new JsonResponse($jsonKids);
//     }

    #[OA\Response(
        response: 200,
        description: 'Creates a new Kids object',
        content: new OA\JsonContent(ref: new Model(type: Kids::class))
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
    #[OA\Tag(name: 'Kids')]
    #[Route('/add', name: '_add', methods: ['POST'])]
    public function createKids(Request $request, UserPasswordHasherInterface $hasher)
    {
        $data = json_decode($request->getContent(), true);

        $Kids = new Kids();
        $form = $this->createForm(KidsForm::class, $Kids);
        $form->submit($data);

        // Hasher le mot de passe
        $hashedPassword = $hasher->hashPassword($Kids, $data['password']);
        $Kids->setPassword($hashedPassword);

        // Définir le rôle par défaut
        $Kids->setRoles(['ROLE_KIDS']);

        $Kids = $this->KidsService->persist($Kids);

        return new JsonResponse($this->KidsService->toJson($Kids), JsonResponse::HTTP_CREATED);
    }

    #[OA\Response(
        response: 200,
        description: 'Updates an Kids object',
        content: new OA\JsonContent(ref: new Model(type: Kids::class))
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
    #[OA\Tag(name: 'Kids')]
    #[Route('/{id}/edit', name: '_edit', methods: ['PATCH'])]
    public function updateKids(Kids $Kids, Request $request, UserPasswordHasherInterface $hasher)
    {
        $data = json_decode($request->getContent(), true);

        $form = $this->createForm(KidsForm::class, $Kids);
        $form->submit($data, false);
        $hashedPassword = $hasher->hashPassword($Kids, $data['password']);
        $Kids->setPassword($hashedPassword);

        $Kids = $this->KidsService->persist($Kids);
        return new JsonResponse($this->KidsService->toJson($Kids));
    } 

    // #[OA\Response(
    //     response: 200,
    //     description: 'update intitule of a Kids',
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
    // #[OA\Tag(name: 'Kids')]
    // #[Route('/{id}/intitule', name: '_intitule', methods: ['PUT'])]
    // public function updateIntituleOfKids(Kids $Kids, Request $request)
    // {
    //     $data = json_decode($request->getContent(), true);
    //     $Kids->setIntitule($data['intitule']);
    //     $Kids = $this->KidsService->persist($Kids);

    //     return new JsonResponse($this->KidsService->toJson($Kids));
    // }

    #[OA\Response(
        response: 200,
        description: 'Deletes an Kids',
        content: new OA\JsonContent(
            type: 'string',
        )
    )]
    #[OA\Tag(name: 'Kids')]
    #[Route('/{id}/delete', name: '_delete', methods: ['DELETE'])]
    public function deleteKids($id, Request $request)
    {
        $Kids = $this->KidsService->get($id);
        try {
            $this->KidsService->remove($Kids);
            //$request->getSession()->getFlashBag()->add('success', 'évènement supprimé avec succès !');
        } catch (\Exception $exception) {
            //$request->getSession()->getFlashBag()->add('danger', 'un ou plusieurs objets liés  à cette entité !');
        }

        return new JsonResponse($request);
    }
}