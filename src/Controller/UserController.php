<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// #[Route('admin/user')]
#[Route('/user')]
class UserController extends AbstractController
{

    function generateRandomPassword($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()_+[]{}|;:,.<>?';
        $password = '';
        $characterListLength = strlen($characters) - 1;
        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[random_int(0, $characterListLength)];
        }
        return $password;
    }

    private function sendPasswordEmail($to, $password)
    {
        $subject = 'Votre mot de passe';
        $message = "Bonjour,\n\nVotre mot de passe est : $password\n\nCordialement,\nL'équipe";
        $headers = 'From: no-reply@votre-domaine.com' . "\r\n" .
            'Reply-To: no-reply@votre-domaine.com' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();

        mail($to, $subject, $message, $headers);
    }




    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
        $userCount = count($users);

        return $this->render('user/index.html.twig', [
            'users' => $users,
            'userCount' => $userCount,
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            
            // Génération du mot de passe aléatoire
            $password = $this->generateRandomPassword();
            // Encodez le mot de passe si vous utilisez un encodeur de mot de passe
            $encodedPassword = password_hash($password, PASSWORD_BCRYPT); // Utilisez PASSWORD_BCRYPT ou une autre méthode selon votre configuration
            $user->setPassword($encodedPassword);

            // Ensure the user has at least ROLE_USER
            $roles = $user->getRoles();
            if (empty($roles)) {
                $user->setRoles(['ROLE_USER']);
            }

            // Gestion du fichier image
            $file = $form->get('image')->getData();
            if ($file) {
                $fileName = md5(uniqid()) . '-' . str_replace(' ', '_', $file->getClientOriginalName()) . '.' . $file->guessExtension();
                $file->move($this->getParameter('user_directory'), $fileName);
                $user->setImage($fileName);
            }

            // Enregistrer l'utilisateur en base de données
            $entityManager->persist($user);
            $entityManager->flush();

            // Envoyer l'email avec le mot de passe
            $this->sendPasswordEmail($user->getEmail(), $password);

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }



        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        // Conserver l'image actuelle pour la réutiliser si aucune nouvelle image n'est téléchargée
        $currentImage = $user->getImage();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifier si une nouvelle image a été téléchargée
            $file = $form->get('image')->getData();

            if ($file) {
                // Supprimer l'ancienne image si elle existe
                if ($currentImage) {
                    $oldImagePath = $this->getParameter('user_directory') . '/' . $currentImage;
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }

                // Sauvegarder la nouvelle image
                $fileName = md5(uniqid()) . '-' . str_replace(' ', '_', $file->getClientOriginalName()) . '.' . $file->guessExtension();
                $file->move($this->getParameter('user_directory'), $fileName);
                $user->setImage($fileName);
            } else {
                // Conserver l'ancienne image si aucune nouvelle image n'est téléchargée
                $user->setImage($currentImage);
            }

            // Ne pas écraser le mot de passe si aucune nouvelle valeur n'est fournie
            if (empty($user->getPassword())) {
                $user->setPassword($user->getPassword());
            } else {
                // Encoder le nouveau mot de passe si fourni
                // Assurez-vous d'utiliser l'encodeur de mot de passe approprié ici
            }

            // Gérer les rôles
            $roles = $form->get('roles')->getData(); // Récupérer les rôles depuis le formulaire

            // Assurer que ROLE_USER est inclus uniquement si aucun autre rôle n'est sélectionné
            if (empty($roles)) {
                $roles = ['ROLE_USER'];
            } else {
                // Si d'autres rôles sont sélectionnés, retirer ROLE_USER s'il est présent
                if (in_array('ROLE_USER', $roles)) {
                    $roles = array_filter($roles, function ($role) {
                        return $role !== 'ROLE_USER';
                    });
                }
            }

            $user->setRoles($roles);

            // Enregistrer les modifications
            $entityManager->flush();

            // Redirection après succès
            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        // Afficher le formulaire
        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(), // Assurez-vous d'utiliser createView()
        ]);
    }


    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $image = $user->getImage();
            if ($image) {
                $imagePath = $this->getParameter('user_directory') . '/' . $image;
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/generate-password', name: 'app_user_generate_password', methods: ['GET'])]
    public function generatePassword(User $user, EntityManagerInterface $entityManager): Response
    {
        // Génération du nouveau mot de passe
        $newPassword = $this->generateRandomPassword();
        $encodedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        $user->setPassword($encodedPassword);

        // Enregistrer les modifications en base de données
        $entityManager->flush();

        // Envoyer le mot de passe par e-mail
        $this->sendPasswordEmail($user->getEmail(), $newPassword);

        // Redirection avec message de succès
        $this->addFlash('success', 'Un nouveau mot de passe a été généré et envoyé par e-mail.');

        return $this->redirectToRoute('app_user_edit', ['id' => $user->getId()]);
    }
}
