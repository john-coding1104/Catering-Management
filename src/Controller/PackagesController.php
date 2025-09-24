<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PackagesController extends AbstractController
{
    #[Route('/packages', name: 'app_packages')]
    public function index(): Response
    {
        $packages = [
            [
                'name' => 'Wedding Package',
                'price' => 80000,
                'details' => 'Good for 100 pax',
            ],
            [
                'name' => 'Corporate Package',
                'price' => 50000,
                'details' => 'Good for 100 pax',
            ],
            [
                'name' => 'Birthday Package',
                'price' => 40000,
                'details' => 'Good for 100 pax',
            ],
            [
                'name' => 'Casual Event Package',
                'price' => 30000,
                'details' => 'Good for 100 pax',
            ],
        ];

        return $this->render('packages/index.html.twig', [
            'packages' => $packages,
        ]);
    }
}
