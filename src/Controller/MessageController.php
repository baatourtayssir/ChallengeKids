<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use App\Entity\Message;
use App\Entity\User;
use App\Service\MessageService;
use App\Service\UserService;
use App\Form\MessageForm;

#[Route('/api/message', name: 'message_')]
class MessageController extends AbstractController
{
    private $messageService;
    private  $userService;

    public function __construct(MessageService $messageService, UserService $userService)
    {
        $this->messageService = $messageService;
        $this->userService = $userService; 
    }

    #[OA\Response(
        response: 200,
        description: 'Returns list of all messages',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Message::class))
        )
    )]
    #[OA\Tag(name: 'Message')]
    #[Route('/list', name: '_list', methods: ['GET'])]
    public function index()
    {
        $list = $this->messageService->getMessages();

        $jsonMessages = [];
        foreach ($list as $key => $message) {
            $jsonMessages[$key] = $this->messageService->toJson($message);
        }

        return new JsonResponse($jsonMessages);
    }

    #[OA\Response(
        response: 200,
        description: 'Send a new message',
        content: new OA\JsonContent(ref: new Model(type: Message::class))
    )]
    #[OA\Response(response: 400, description: 'Bad request')]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: 'object',
            example: [
                'sender_id' => 1,
                'recipient_id' => 2,
                'content' => 'Hello!'
            ]
        )
    )]
    #[OA\Tag(name: 'Message')]
#[Route('/send', name: '_send', methods: ['POST'])]
public function send(Request $request): JsonResponse
{
    $data = json_decode($request->getContent(), true);

    $message = new Message();
    $message->setContent($data['content']);
    $message->setDate(new \DateTime());

    // Récupération de l’expéditeur (authentifié)
    $sender = $this->getUser(); // Assure-toi que JWT fonctionne

    // Récupération du destinataire via l’EntityManager (correctement injecté)
    $recipientId = $data['recipient_id'];
    $recipient = $this->userService->get($recipientId); // ❗ Assure-toi que tu as UserService ou fais autrement

    if (!$recipient) {
        return new JsonResponse(['error' => 'Recipient not found'], 404);
    }

    $message->setSender($sender);
    $message->setRecipient($recipient);

    $this->messageService->persist($message);

    return new JsonResponse($this->messageService->toJson($message));
}

    #[OA\Response(
        response: 200,
        description: 'Deletes a message',
        content: new OA\JsonContent(type: 'string')
    )]
    #[OA\Tag(name: 'Message')]
    #[Route('/{id}/delete', name: '_delete', methods: ['DELETE'])]
    public function delete($id)
    {
        $message = $this->messageService->get($id);
        if (!$message) {
            return new JsonResponse(['error' => 'Message not found'], 404);
        }

        $this->messageService->remove($message);

        return new JsonResponse(['success' => 'Message deleted']);
    }
}
