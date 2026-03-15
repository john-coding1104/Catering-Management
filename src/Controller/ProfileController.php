<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Service\ActivityLogService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/profile')]
#[IsGranted('ROLE_USER')]
class ProfileController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
        private ActivityLogService $activityLogService
    ) {
    }

    #[Route('', name: 'app_profile_show', methods: ['GET'])]
    public function show(): Response
    {
        $user = $this->getUser();

        return $this->render('profile/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/change-password', name: 'app_profile_change_password', methods: ['GET', 'POST'])]
    public function changePassword(Request $request): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Verify current password
            $currentPassword = $form->get('currentPassword')->getData();
            
            if (!$this->passwordHasher->isPasswordValid($user, $currentPassword)) {
                $form->get('currentPassword')->addError(new FormError('Current password is incorrect.'));
            } else {
                // Hash and set new password
                $newPassword = $form->get('plainPassword')->getData();
                $hashedPassword = $this->passwordHasher->hashPassword($user, $newPassword);
                
                $user->setPassword($hashedPassword);
                $user->setUpdatedAt(new \DateTimeImmutable());
                
                $this->entityManager->flush();

                // Log the password change
                $this->activityLogService->logPasswordChange($user);

                $this->addFlash('success', 'Your password has been changed successfully.');

                return $this->redirectToRoute('app_profile_show');
            }
        }

        return $this->render('profile/change_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/edit', name: 'app_profile_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request): Response
    {
        $user = $this->getUser();

        if ($request->isMethod('POST')) {
            $email = $request->getPayload()->getString('email');
            $username = $request->getPayload()->getString('username');

            // Validate CSRF token
            if (!$this->isCsrfTokenValid('edit_profile', $request->getPayload()->getString('_token'))) {
                $this->addFlash('error', 'Invalid CSRF token.');
                return $this->redirectToRoute('app_profile_show');
            }

            $changes = [];

            if ($email && $email !== $user->getEmail()) {
                $user->setEmail($email);
                $changes['email'] = ['old' => $user->getEmail(), 'new' => $email];
            }

            if ($username && $username !== $user->getUsername()) {
                $user->setUsername($username);
                $changes['username'] = ['old' => $user->getUsername(), 'new' => $username];
            }

            if (!empty($changes)) {
                $user->setUpdatedAt(new \DateTimeImmutable());
                $this->entityManager->flush();

                $this->addFlash('success', 'Your profile has been updated successfully.');
            }

            return $this->redirectToRoute('app_profile_show');
        }

        return $this->render('profile/edit.html.twig', [
            'user' => $user,
        ]);
    }
}
