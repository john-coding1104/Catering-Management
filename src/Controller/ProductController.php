<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\User;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/product')]
final class ProductController extends AbstractController
{
    #[Route(name: 'app_product_index', methods: ['GET'])]
    public function index(Request $request, ProductRepository $productRepository): Response
    {
        $search = $request->query->get('search');
        $category = $request->query->get('category');

        $queryBuilder = $productRepository->createQueryBuilder('p');

        if ($search) {
            $queryBuilder->andWhere('p.name LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        if ($category) {
            $queryBuilder->andWhere('p.category = :category')
                ->setParameter('category', $category);
        }

        $products = $queryBuilder
            ->orderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult();

        // Get unique categories for filter
        $categories = $productRepository->createQueryBuilder('p')
            ->select('DISTINCT p.category')
            ->orderBy('p.category', 'ASC')
            ->where('p.category IS NOT NULL')
            ->getQuery()
            ->getScalarResult();

        $categoryChoices = array_map(fn($row) => $row['category'], $categories);

        return $this->render('product/index.html.twig', [
            'products' => $products,
            'categories' => $categoryChoices,
            'current_search' => $search,
            'current_category' => $category,
        ]);
    }

    #[Route('/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imagePath')->getData();
            
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('kernel.project_dir').'/public/uploads/products',
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Failed to upload image');
                }

                $product->setImagePath('/uploads/products/'.$newFilename);
            }
            
            $product->setCreatedBy($this->getUser());
            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $this->assertCanManageProduct($product);

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imagePath')->getData();
            
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('kernel.project_dir').'/public/uploads/products',
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Failed to upload image');
                }

                $product->setImagePath('/uploads/products/'.$newFilename);
            }
            
            $entityManager->flush();

            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_product_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        $this->assertCanManageProduct($product);

        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/api/details', name: 'app_product_api_details', methods: ['GET'])]
    public function getProductDetails(Product $product): JsonResponse
    {
        return new JsonResponse([
            'id' => $product->getId(),
            'name' => $product->getName(),
            'unitPrice' => $product->getUnitPrice(),
            'category' => $product->getCategory(),
        ]);
    }

    private function assertCanManageProduct(Product $product): void
    {
        $user = $this->getUser();
        if ($this->isGranted('ROLE_ADMIN')) {
            return;
        }

        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        $owner = $product->getCreatedBy();
        if (!$owner || $owner->getId() !== $user->getId()) {
            throw $this->createAccessDeniedException('You can only manage your own products.');
        }

        if (in_array('ROLE_ADMIN', $owner->getRoles(), true)) {
            throw $this->createAccessDeniedException('You cannot manage admin records.');
        }
    }
}
