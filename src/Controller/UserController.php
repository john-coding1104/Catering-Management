<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Service\ActivityLogService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/users')]
#[IsGranted('ROLE_ADMIN')]
class UserController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private ActivityLogService $activityLogService,
        private UserPasswordHasherInterface $passwordHasher,
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route('', name: 'app_user_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $search = $request->query->get('search');
        $role = $request->query->get('role');
        $status = $request->query->get('status');
        $page = $request->query->getInt('page', 1);
        $limit = 25;
        $offset = ($page - 1) * $limit;

        $queryBuilder = $this->userRepository->createQueryBuilder('u');

        if ($search) {
            $queryBuilder->andWhere('u.email LIKE :search OR u.username LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        if ($role) {
            $queryBuilder->andWhere('JSON_CONTAINS(u.roles, :role) = 1')
                ->setParameter('role', '\"' . $role . '\"');
        }

        if ($status !== null && $status !== '') {
            $queryBuilder->andWhere('u.status = :status')
                ->setParameter('status', $status);
        }

        $totalUsers = (clone $queryBuilder)->select('COUNT(u.id)')->getQuery()->getSingleScalarResult();
        $totalPages = ceil($totalUsers / $limit);

        $users = $queryBuilder
            ->orderBy('u.createdAt', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        return $this->render('admin/users/index.html.twig', [
            'users' => $users,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalUsers' => $totalUsers,
            'current_search' => $search,
            'current_role' => $role,
            'current_status' => $status,
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hash the password
            $plainPassword = $form->get('plainPassword')->getData();
            if ($plainPassword) {
                $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
            }

            $user->setCreatedAt(new \DateTimeImmutable());
            $user->setUpdatedAt(new \DateTimeImmutable());
            $user->setStatus('active');

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            // Log the user creation
            $currentUser = $this->getUser();
            $this->activityLogService->logUserCreation($currentUser, $user);

            $this->addFlash('success', sprintf('User "%s" has been created successfully.', $user->getUsername()));

            return $this->redirectToRoute('app_user_index');
        }

        return $this->render('admin/users/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('admin/users/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user): Response
    {
        $changes = [];
        $form = $this->createForm(UserType::class, $user, ['password_required' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Track changes
            if ($form->get('username')->getData() !== $user->getUsername()) {
                $changes['username'] = [
                    'old' => $user->getUsername(),
                    'new' => $form->get('username')->getData(),
                ];
            }

            if ($form->get('email')->getData() !== $user->getEmail()) {
                $changes['email'] = [
                    'old' => $user->getEmail(),
                    'new' => $form->get('email')->getData(),
                ];
            }

            $newRoles = $form->get('roles')->getData();
            if ($newRoles !== $user->getRoles()) {
                $changes['roles'] = [
                    'old' => $user->getRoles(),
                    'new' => $newRoles,
                ];
            }

            // Hash password if provided
            $plainPassword = $form->get('plainPassword')->getData();
            if ($plainPassword) {
                $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
                $changes['password'] = 'changed';
            }

            $user->setUpdatedAt(new \DateTimeImmutable());

            $this->entityManager->flush();

            // Log the user edit
            $currentUser = $this->getUser();
            if (!empty($changes)) {
                $this->activityLogService->logUserEdit($currentUser, $user, $changes);
            }

            $this->addFlash('success', sprintf('User "%s" has been updated successfully.', $user->getUsername()));

            return $this->redirectToRoute('app_user_index');
        }

        return $this->render('admin/users/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->getPayload()->getString('_token'))) {
            // Log the user deletion
            $currentUser = $this->getUser();
            $this->activityLogService->logUserDeletion($currentUser, $user);

            $this->entityManager->remove($user);
            $this->entityManager->flush();

            $this->addFlash('success', sprintf('User "%s" has been deleted successfully.', $user->getUsername()));
        }

        return $this->redirectToRoute('app_user_index');
    }

    #[Route('/{id}/status', name: 'app_user_status', methods: ['POST'])]
    public function toggleStatus(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('status' . $user->getId(), $request->getPayload()->getString('_token'))) {
            $oldStatus = $user->getStatus();
            $newStatus = $oldStatus === 'active' ? 'inactive' : 'active';
            
            $user->setStatus($newStatus);
            $user->setUpdatedAt(new \DateTimeImmutable());
            
            $this->entityManager->flush();

            // Log the status change
            $currentUser = $this->getUser();
            $this->activityLogService->logStatusChange($currentUser, $user, $oldStatus, $newStatus);

            $this->addFlash('success', sprintf('User "%s" status has been changed to %s.', $user->getUsername(), $newStatus));
        }

        return $this->redirectToRoute('app_user_index');
    }

    #[Route('/{id}/reset-password', name: 'app_user_reset_password', methods: ['POST'])]
    public function resetPassword(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('reset' . $user->getId(), $request->getPayload()->getString('_token'))) {
            // Generate a random temporary password
            $temporaryPassword = bin2hex(random_bytes(8));
            
            $hashedPassword = $this->passwordHasher->hashPassword($user, $temporaryPassword);
            $user->setPassword($hashedPassword);
            $user->setUpdatedAt(new \DateTimeImmutable());
            
            $this->entityManager->flush();

            // Log the password reset
            $currentUser = $this->getUser();
            $this->activityLogService->log($currentUser, 'RESET_PASSWORD', sprintf('User: %s (ID: %d)', $user->getUsername(), $user->getId()));

            $this->addFlash('success', sprintf(
                'Password for "%s" has been reset. Temporary password: %s',
                $user->getUsername(),
                $temporaryPassword
            ));
        }

        return $this->redirectToRoute('app_user_show', ['id' => $user->getId()]);
    }
}
