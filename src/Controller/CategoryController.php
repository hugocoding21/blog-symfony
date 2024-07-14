<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    #[Route("/categories", name: "app_category")]
    public function index(
        CategoryRepository $categoryRepository,
        Request $request
    ): Response
    {
        $categories = $categoryRepository->findAll();
        $selectedCategory = $request->query->get("category");
        $eventFilter = $request->query->get("filter");

        $events = [];

        if ($selectedCategory)
        {
            $category = $categoryRepository->findOneBy([
                "name" => $selectedCategory,
            ]);
            if ($category)
            {
                $events = $category->getEvents();
            }
        }
        else
        {
            foreach ($categories as $category)
            {
                foreach ($category->getEvents() as $event)
                {
                    $events[] = $event;
                }
            }
        }
        if ($eventFilter)
        {
            $now = new \DateTime();
            if ($eventFilter === "past")
            {
                $events = array_filter(
                    $events,
                    fn ($event) => $event->getEndAt() < $now
                );
            }
            elseif ($eventFilter === "current")
            {
                $events = array_filter(
                    $events,
                    fn ($event) => $event->getStartAt() <= $now &&
                        $event->getEndAt() > $now
                );
            }
            elseif ($eventFilter === "future")
            {
                $events = array_filter(
                    $events,
                    fn ($event) => $event->getStartAt() > $now
                );
            }
        }

        return $this->render("category/index.html.twig", [
            "categories" => $categories,
            "events" => $events,
            "selectedCategory" => $selectedCategory,
            "selectedFilter" => $eventFilter,
        ]);
    }

    #[Route('/category/new', name: 'category_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute('app_category');
        }

        return $this->render('category/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/category/{id}/edit', name: 'category_edit')]
    public function edit(Category $category, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $em->flush();

            return $this->redirectToRoute('app_category');
        }

        return $this->render('category/edit.html.twig', [
            'form' => $form->createView(),
            'category' => $category,
        ]);
    }
}
