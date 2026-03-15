<?php

namespace App\Controller;

use App\Entity\Supplier;
use App\Entity\User;
use App\Form\SupplierType;
use App\Repository\SupplierRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File as HttpFile;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/supplier')]
final class SupplierController extends AbstractController
{
    #[Route(name: 'app_supplier_index', methods: ['GET'])]
    public function index(Request $request, SupplierRepository $supplierRepository, ProductRepository $productRepository): Response
    {
        $search = $request->query->get('search');
        $product = $request->query->get('product');

        $queryBuilder = $supplierRepository->createQueryBuilder('s');

        if ($search) {
            $queryBuilder->andWhere('s.name LIKE :search OR s.contact LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        if ($product) {
            $queryBuilder->join('s.products', 'p')
                ->andWhere('p.id = :product')
                ->setParameter('product', $product);
        }

        $suppliers = $queryBuilder
            ->orderBy('s.name', 'ASC')
            ->getQuery()
            ->getResult();

        $products = $productRepository->findAll();

        return $this->render('supplier/index.html.twig', [
            'suppliers' => $suppliers,
            'products' => $products,
            'current_search' => $search,
            'current_product' => $product,
        ]);
    }

    #[Route('/new', name: 'app_supplier_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $supplier = new Supplier();
        $form = $this->createForm(SupplierType::class, $supplier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move($this->getParameter('kernel.project_dir') . '/public/uploads/suppliers', $newFilename);
                    $supplier->setImagePath('/uploads/suppliers/' . $newFilename);
                } catch (FileException $e) {
                    // ignore or add flash
                }
            }

            $supplier->setCreatedBy($this->getUser());
            $entityManager->persist($supplier);
            $entityManager->flush();

            return $this->redirectToRoute('app_supplier_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('supplier/new.html.twig', [
            'supplier' => $supplier,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_supplier_show', methods: ['GET'])]
    public function show(Supplier $supplier): Response
    {
        return $this->render('supplier/show.html.twig', [
            'supplier' => $supplier,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_supplier_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Supplier $supplier, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $this->assertCanManageSupplier($supplier);

        $form = $this->createForm(SupplierType::class, $supplier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move($this->getParameter('kernel.project_dir') . '/public/uploads/suppliers', $newFilename);
                    $supplier->setImagePath('/uploads/suppliers/' . $newFilename);
                } catch (FileException $e) {
                    // ignore or set flash
                }
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_supplier_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('supplier/edit.html.twig', [
            'supplier' => $supplier,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_supplier_delete', methods: ['POST'])]
    public function delete(Request $request, Supplier $supplier, EntityManagerInterface $entityManager): Response
    {
        $this->assertCanManageSupplier($supplier);

        if ($this->isCsrfTokenValid('delete'.$supplier->getId(), $request->request->get('_token'))) {
            $entityManager->remove($supplier);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_supplier_index', [], Response::HTTP_SEE_OTHER);
    }

    private function assertCanManageSupplier(Supplier $supplier): void
    {
        $user = $this->getUser();
        if ($this->isGranted('ROLE_ADMIN')) {
            return;
        }

        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        $owner = $supplier->getCreatedBy();
        if (!$owner || $owner->getId() !== $user->getId()) {
            throw $this->createAccessDeniedException('You can only manage your own suppliers.');
        }

        if (in_array('ROLE_ADMIN', $owner->getRoles(), true)) {
            throw $this->createAccessDeniedException('You cannot manage admin records.');
        }
    }
}
