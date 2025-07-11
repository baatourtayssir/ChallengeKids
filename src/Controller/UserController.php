<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;
use App\Service\UserService;
use App\Form\UserForm;

#[Route('/api/user', name: 'User_')]
class UserController extends AbstractController
{
    private $UserService;

    public function __construct(UserService $UserService)
    {
        $this->UserService = $UserService;
    }

    /**
     * List of all categories.    
     */
    #[OA\Response(
        response: 200,
        description: 'Returns list of all Users',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: User::class))
        )
    )]
    #[OA\Tag(name: 'User')]
    #[Route('/list', name: '_list', methods: ['GET'])]
    public function index()
    {
        $list = $this->UserService->getUser();

        $jsonUser = [];
        foreach ($list as $key => $User) {
            $jsonUser[$key] = $this->UserService->toJson($User);
        }

        return new JsonResponse($jsonUser);
    }
// /**
//      * List of active Categories.    
//      */
//     #[OA\Response(
//         response: 200,
//         description: 'Returns list of active Categories',
//         content: new OA\JsonContent(
//             type: 'array',
//             items: new OA\Items(ref: new Model(type: User::class))
//         )
//     )]
//     #[OA\Tag(name: 'User')]
//     #[Route('/list_by_intitule', name: 'list_by_intitule', methods: ['GET'])]
//     public function byIntitule()
//     {
//         $list = $this->UserService->getUser(['intitule'=>true]);

//         $jsonUser = [];
//         foreach ($list as $key => $User) {
//             $jsonUser[$key] = $this->UserService->toJson($User);
//         }

//         return new JsonResponse($jsonUser);
//     }

    #[OA\Response(
        response: 200,
        description: 'Creates a new User object',
        content: new OA\JsonContent(ref: new Model(type: User::class))
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
                'roles' => ['ROLE_USER']
            ]
        )
    )]
    #[OA\Tag(name: 'User')]
    #[Route('/add', name: '_add', methods: ['POST'])]
    public function createUser(Request $request, UserPasswordHasherInterface $hasher)
    {
        $data = json_decode($request->getContent(), true);

        $user = new User();
        $form = $this->createForm(UserForm::class, $user);
        $form->submit($data);

        // Hasher le mot de passe
        $hashedPassword = $hasher->hashPassword($user, $data['password']);
        $user->setPassword($hashedPassword);

        // Définir le rôle par défaut
        $user->setRoles(['ROLE_ADMIN']);

        $user = $this->UserService->persist($user);

        return new JsonResponse($this->UserService->toJson($user), JsonResponse::HTTP_CREATED);
    }

    #[OA\Response(
        response: 200,
        description: 'Updates an User object',
        content: new OA\JsonContent(ref: new Model(type: User::class))
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
                'roles' => ['ROLE_USER'],
                
            ]
        )
    )]
    #[OA\Tag(name: 'User')]
    #[Route('/{id}/edit', name: '_edit', methods: ['PATCH'])]
    public function updateUser(User $User, Request $request , UserPasswordHasherInterface $hasher)
    {
        $data = json_decode($request->getContent(), true);

        $form = $this->createForm(UserForm::class, $User);
        $form->submit($data, false);
        $hashedPassword = $hasher->hashPassword($User, $data['password']);
        $User->setPassword($hashedPassword);

        $User = $this->UserService->persist($User);
        return new JsonResponse($this->UserService->toJson($User));
    }

    // #[OA\Response(
    //     response: 200,
    //     description: 'update intitule of a User',
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
    // #[OA\Tag(name: 'User')]
    // #[Route('/{id}/intitule', name: '_intitule', methods: ['PUT'])]
    // public function updateIntituleOfUser(User $User, Request $request)
    // {
    //     $data = json_decode($request->getContent(), true);
    //     $User->setIntitule($data['intitule']);
    //     $User = $this->UserService->persist($User);

    //     return new JsonResponse($this->UserService->toJson($User));
    // }

    #[OA\Response(
        response: 200,
        description: 'Deletes an User',
        content: new OA\JsonContent(
            type: 'string',
        )
    )]
    #[OA\Tag(name: 'User')]
    #[Route('/{id}/delete', name: '_delete', methods: ['DELETE'])]
    public function deleteUser($id, Request $request)
    {
        $User = $this->UserService->get($id);
        try {
            $this->UserService->remove($User);
            //$request->getSession()->getFlashBag()->add('success', 'évènement supprimé avec succès !');
        } catch (\Exception $exception) {
            //$request->getSession()->getFlashBag()->add('danger', 'un ou plusieurs objets liés  à cette entité !');
        }

        return new JsonResponse($request);
    }

    #[OA\Response(
        response: 200,
        description: 'Add a new user friend',
        content: new OA\JsonContent(ref: new Model(type: User::class))
    )]
    #[OA\Response(
        response: 400,
        description: 'Bad request',
    )]
    #[OA\Response(
        response: 404,
        description: 'User not found',
    )]
    #[OA\Tag(name: 'User')]
    #[Route('/{id}/add-friend/{friendId}', name: '_add_friend', methods: ['POST'])]
    public function addFriend(int $id, int $friendId)
    {
        $user = $this->UserService->get($id);
        $friend = $this->UserService->get($friendId);

        if (!$user || !$friend) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        $user->addAmi($friend);
        $this->UserService->persist($user);

        return new JsonResponse(['success' => 'Friend added successfully']);
    }

}