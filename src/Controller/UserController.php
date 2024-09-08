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

#[Route('admin/user')]
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

            
            if($file != 'ok') {
                $path = '/';
                $fileName = str_replace(' ','_',$file->getClientOriginalName());

                $file->move(
                    $this->getParameter('user_directory').$path,
                    $fileName
                );
                $user->setUser($fileName);
            }

            $password = $this->generateRandomPassword();
            
            $encodedPassword = password_hash($password, PASSWORD_BCRYPT); // Utilisez PASSWORD_BCRYPT ou une autre méthode selon votre configuration
            $user->setPassword($encodedPassword);

            $roles = $user->getRoles();
            if (empty($roles)) {
                $user->setRoles(['ROLE_USER']);
            }

           
            $entityManager->persist($user);
            $entityManager->flush();

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
        $oldImage = $user->getImage();  
    
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('image')->getData();
    
            if ($file && $file != 'ok') {
                if ($oldImage && $oldImage != 'ok') {
                    $oldImagePath = $this->getParameter('user_directory') . '/' . $oldImage;
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }

                $fileName = str_replace(' ', '_', $file->getClientOriginalName());
                $file->move(
                    $this->getParameter('user_directory'),
                    $fileName
                );
                $user->setImage($fileName);
            } else {
                $user->setImage($oldImage);
            }


            $submittedRoles = $form->get('roles')->getData(); 
    
            $specificRoles = [
                'ROLE_ADMIN',
                'ROLE_DIRECTEURRH',
                'ROLE_RESPONSABLE_HIERA',
                'ROLE_REFERENT_FRAIS',
                'ROLE_RTT'
            ];
    
            $hasSpecificRole = false;
            foreach ($submittedRoles as $role) {
                if (in_array($role, $specificRoles)) {
                    $hasSpecificRole = true;
                    break;
                }
            }
    
            if (!$hasSpecificRole) {
                $submittedRoles[] = 'ROLE_USER';
            }
    
            $user->setRoles(array_values(array_unique($submittedRoles)));
            $entityManager->flush();
    
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
            if($user->getImage() and $user->getImage() != "ok")
            {
                $lien = '../public/uploads/user/'.$user->getImage();
                unlink($lien);
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
