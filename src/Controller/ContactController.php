<?php

namespace App\Controller;

use App\Form\ContactFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ContactController extends AbstractController
{
    #[Route("/contactez-nous", name: "app_contact")]
    public function index(Request $request): Response
    {
        $form = $this->createForm(ContactFormType::class);

        $form->handleRequest($request);

        return $this->render("contact/index.html.twig", [
            "form" => $form->createView(),
        ]);
    }
}
