<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class CreateAdminController extends AbstractController
{
    #[Route('/reset-admin', name: 'reset_admin')]
    public function resetAdmin(
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        // Supprimer l'ancien admin s'il existe
        $existingAdmin = $entityManager->getRepository(User::class)
            ->findOneBy(['email' => 'admin@gmail.com']);
        
        if ($existingAdmin) {
            $entityManager->remove($existingAdmin);
            $entityManager->flush();
        }

        // Créer le nouveau admin
        $user = new User();
        $user->setEmail('admin@gmail.com');
        $user->setNom('Admin');
        $user->setPrenom('Super');
        $user->setRoles(['ROLE_ADMIN']);
        
        $hashedPassword = $passwordHasher->hashPassword($user, 'admin123');
        $user->setPassword($hashedPassword);

        $entityManager->persist($user);
        $entityManager->flush();

        return new Response('
            <h1>Admin réinitialisé avec succès !</h1>
            <p><strong>Email:</strong> admin@gmail.com</p>
            <p><strong>Mot de passe:</strong> admin123</p>
            <p><a href="/login">Se connecter</a></p>
        ');
    }
}
