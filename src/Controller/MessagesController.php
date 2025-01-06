<?php
// src/Controller/MessagesController.php

namespace App\Controller;

use App\Entity\Freelance;
use App\Entity\Gestionnaire;
use App\Entity\Messages;
use App\Entity\Personel;
use App\Entity\User;
use App\Repository\MessagesRepository;
use App\Repository\UserRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/message', name: 'app_notification')]
class MessagesController extends AbstractController
{
    /**
     * @throws Exception
     * @throws \DateMalformedStringException
     */
    // src/Controller/MessagesController.php

// src/Controller/MessagesController.php

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(Request $request, MessagesRepository $messagesRepo): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login'); // Redirection si non connecté
        }

        $search = $request->query->get('search', '');
        $conversations = $messagesRepo->findConversationsForUserSearch($user, $search);

        // Remove duplicates
        $uniqueConversations = [];
        $seen = [];
        foreach ($conversations as $conversation) {
            if (!in_array($conversation['conversationId'], $seen)) {
                $seen[] = $conversation['conversationId'];
                $uniqueConversations[] = $conversation;
            }
        }

        foreach ($uniqueConversations as &$conversation) {
            $conversation['unreadCount'] = $messagesRepo->countUnreadMessagesForUser($conversation['conversationId'], $user);
            $receiver = $messagesRepo->findReceiverForConversation($conversation['conversationId'], $user);
            $conversation['receiverEmail'] = $receiver->getEmail(); // Add receiver email to conversation data
            $conversation['lastMessage'] = $messagesRepo->findLastMessageForConversation($conversation['conversationId']); // Add last message to conversation data
            $lastMessageDate = $messagesRepo->findLastMessageForConversationCreatedAt($conversation['conversationId']);
            $conversation['lastMessageDate'] = $lastMessageDate ? (new \DateTime($lastMessageDate))->format('d/m/Y H:i') : null;

        }

        $role = $messagesRepo->getRoleCurrentUser($user);
        $template = $role === 'freelance' ? 'website/freelance/notification/notifications.html.twig' : 'website/admin/notification/notifications.html.twig';

        return $this->render($template, [
            'conversations' => $uniqueConversations,
        ]);
    }

    /**
     * @throws Exception
     */


    #[Route('/conversation/{conversationId}', name: 'conversation_details', methods: ['GET'])]
    public function conversationDetails(int $conversationId, MessagesRepository $messagesRepo, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login'); // Redirect if not logged in
        }

        $messages = $messagesRepo->findMessagesByConversationId($conversationId, $user);
        $receiver = $messagesRepo->findReceiverForConversation($conversationId, $user);

        if (!$receiver) {
            throw $this->createNotFoundException('Receiver not found for this conversation');
        }

        $receiverDetails = $messagesRepo->getReceiverDetails($receiver->getId());

        // Mark messages as read
        foreach ($messages as $message) {
            if (!$message->isRead() && $message->getReceiver() === $user) {
                $message->setIsRead(true);
                $entityManager->persist($message);
            }
        }
        $entityManager->flush();

        // Pass messages, receiver details, and conversation ID to the view


        $role = $messagesRepo->getRoleCurrentUser($user);
        $template = $role === 'freelance' ? 'website/freelance/notification/conversation_details.html.twig' : 'website/admin/notification/conversation_details.html.twig';

        return $this->render($template, [
            'messages' => $messages,
            'receiverDetails' => $receiverDetails,
            'conversationId' => $conversationId,
        ]);

    }
    /**
     * @throws Exception
     */



     #[Route('/send-message', name: 'send_message', methods: ['POST'])]
     public function sendMessage(Request $request, EntityManagerInterface $entityManager, MessagesRepository $messageRepo, UserRepository $userRepo): Response
     {
         $user = $this->getUser();
     
         if (!$user) {
             return $this->redirectToRoute('app_login'); // Redirection si non connecté
         }
     
         // Récupération des données de la requête
         $content = $request->request->get('message');
         $conversationId = $request->request->get('conversation_id');
     
         if ($conversationId) {
             // Trouver l'ID du destinataire pour une conversation existante
             $receiverId = $messageRepo->findReceiverForConversation($conversationId, $user);
             if (!$receiverId) {
                 throw $this->createNotFoundException("Aucun destinataire trouvé pour la conversation $conversationId.");
             }
         } else {
             // Récupérer l'ID du destinataire à partir de la requête
             $receiverId = $request->request->get('receiver_id'); // Assurez-vous que ce champ est inclus dans la requête
             if (!$receiverId || !is_numeric($receiverId)) {
                 throw new \InvalidArgumentException("L'ID du destinataire est manquant ou invalide.");
             }
     
             // Vérifier si une conversation existe déjà entre les deux utilisateurs
             $existingConversationId = $messageRepo->getConversationIdBetweenUsers($user->getId(), (int)$receiverId);
             if ($existingConversationId) {
                 $conversationId = $existingConversationId;
             } else {
                 // Générer un nouvel ID de conversation si aucune n'existe
                 $existingConversationIds = $messageRepo->findAllConversationIds();
                 do {
                     $conversationId = random_int(100000, 999999);
                 } while (in_array($conversationId, $existingConversationIds));
             }
         }
     
         // Convertir l'ID du destinataire en objet `User`
         $receiver = $userRepo->find($receiverId);
         if (!$receiver) {
             throw $this->createNotFoundException("Utilisateur avec l'ID $receiverId non trouvé.");
         }
     
         // Création du message
         $message = new Messages();
         $message->setSender($user);
         $message->setReceiver($receiver); // Utiliser l'objet `User`
         $message->setContent($content);
         $message->setCreatedAt(new \DateTime());
         $message->setIsRead(false);
         $message->setConversationId($conversationId);
     
         // Persistance du message
         $entityManager->persist($message);
         $entityManager->flush();
     
         // Redirection vers les détails de la conversation
         return $this->redirectToRoute('app_notificationconversation_details', ['conversationId' => $conversationId]);
     }
     
}

