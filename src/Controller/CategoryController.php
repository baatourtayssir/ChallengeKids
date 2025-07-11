<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use App\Entity\Category;
use App\Service\CategoryService;
use App\Form\CategoryForm;

#[Route('/api/category', name: 'category_')]
class CategoryController extends AbstractController
{
    private $CategoryService;

    public function __construct(CategoryService $CategoryService)
    {
        $this->CategoryService = $CategoryService;
    }

    /**
     * List of all categories.    
     */
    #[OA\Response(
        response: 200,
        description: 'Returns list of all categories',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Category::class))
        )
    )]
    #[OA\Tag(name: 'Category')]
    #[Route('/list', name: '_list', methods: ['GET'])]
    public function index()
    {
        $list = $this->CategoryService->getCategory();

        $jsonCategory = [];
        foreach ($list as $key => $category) {
            $jsonCategory[$key] = $this->CategoryService->toJson($category);
        }

        return new JsonResponse($jsonCategory);
    }
// /**
//      * List of active Categories.    
//      */
//     #[OA\Response(
//         response: 200,
//         description: 'Returns list of active Categories',
//         content: new OA\JsonContent(
//             type: 'array',
//             items: new OA\Items(ref: new Model(type: Category::class))
//         )
//     )]
//     #[OA\Tag(name: 'Category')]
//     #[Route('/list_by_intitule', name: 'list_by_intitule', methods: ['GET'])]
//     public function byIntitule()
//     {
//         $list = $this->CategoryService->getCategory(['intitule'=>true]);

//         $jsonCategory = [];
//         foreach ($list as $key => $category) {
//             $jsonCategory[$key] = $this->CategoryService->toJson($category);
//         }

//         return new JsonResponse($jsonCategory);
//     }

    #[OA\Response(
        response: 200,
        description: 'Creates a new category object',
        content: new OA\JsonContent(ref: new Model(type: Category::class))
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
                'intitule' => 'intitule'
            ]
        )
    )]
    #[OA\Tag(name: 'Category')]
    #[Route('/add', name: '_add', methods: ['POST'])]
    public function createCategory(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        
        $category = new Category();
        $form = $this->createForm(CategoryForm::class, $category);
        $form->submit($data);

        $category = $this->CategoryService->persist($category);
        return new JsonResponse($data);
    }

    #[OA\Response(
        response: 200,
        description: 'Updates an category object',
        content: new OA\JsonContent(ref: new Model(type: Category::class))
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: Object::class,
            example: [
                'intitule' => 'intitule',
            ]
        )
    )]
    #[OA\Tag(name: 'Category')]
    #[Route('/{id}/edit', name: '_edit', methods: ['PUT'])]
    public function updateCategory(Category $category, Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $form = $this->createForm(CategoryForm::class, $category);
        $form->submit($data, false);

        $category = $this->CategoryService->persist($category);
        return new JsonResponse($this->CategoryService->toJson($category));
    }

    // #[OA\Response(
    //     response: 200,
    //     description: 'update intitule of a category',
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
    // #[OA\Tag(name: 'Category')]
    // #[Route('/{id}/intitule', name: '_intitule', methods: ['PUT'])]
    // public function updateIntituleOfCategory(Category $category, Request $request)
    // {
    //     $data = json_decode($request->getContent(), true);
    //     $category->setIntitule($data['intitule']);
    //     $category = $this->CategoryService->persist($category);

    //     return new JsonResponse($this->CategoryService->toJson($category));
    // }

    #[OA\Response(
        response: 200,
        description: 'Deletes an category',
        content: new OA\JsonContent(
            type: 'string',
        )
    )]
    #[OA\Tag(name: 'Category')]
    #[Route('/{id}/delete', name: '_delete', methods: ['DELETE'])]
    public function deleteCategory($id, Request $request)
    {
        $Category = $this->CategoryService->get($id);
        try {
            $this->CategoryService->remove($Category);
            //$request->getSession()->getFlashBag()->add('success', 'évènement supprimé avec succès !');
        } catch (\Exception $exception) {
            //$request->getSession()->getFlashBag()->add('danger', 'un ou plusieurs objets liés  à cette entité !');
        }

        return new JsonResponse($request);
    }
}