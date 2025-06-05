<?php 


namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function displayRegister(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response
    {
        $user = new User();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);

            
            $user->setRoles(['ROLE_USER']);
            
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Inscription réussie !');
            
            return $this->redirectToRoute('app_home');
        }

        return $this->render('register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}