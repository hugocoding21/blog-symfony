<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use App\Repository\EventRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EventController extends AbstractController
{
    #[Route("/", name: "app_event")]
    public function index(): Response
    {
        return $this->render("event/index.html.twig");
    }

    #[Route("/evenement", name: "app_eventList")]
    public function evenementList(
        Request $request,
        EventRepository $repo,
        PaginatorInterface $paginator
    ): Response
    {
        $searchTerm = $request->get("search", "");
        if ($searchTerm)
        {
            $events = $repo->search(trim($searchTerm));

            return $this->render("/event/evenement.html.twig", [
                "events" => $events,
            ]);
        }
        else
        {
            $events = $repo->findAll();

            $pagination = $paginator->paginate(
                $events,
                $request->query->getInt("page", 1),
                6,
                [
                    PaginatorInterface::DEFAULT_SORT_FIELD_NAME =>
                    "event.startAt",
                    PaginatorInterface::DEFAULT_SORT_DIRECTION => "Asc",
                ]
            );
            return $this->render("/event/evenement.html.twig", [
                "events" => $events,
                "pagination" => $pagination
            ]);
        }
    }

    #[Route("/event/{id}", requirements: ["id" => "\d+"])]
    #[
        Route(
            "/evenement/{id}",
            name: "app_eventId",
            requirements: ["id" => "\d+"]
        )
    ]
    public function evenement(Event $event): Response
    {
        return $this->render("/event/eventId.html.twig", ["event" => $event]);
    }

    /**
     * Formulaire create Event
     *
     * @param  Request  $request
     * @param  ManagerRegistry  $doctrine
     * @return void
     */
    #[Route("/evenement/creer", name: "event_create")]
    public function eventCreate(
        Request $request,
        ManagerRegistry $doctrine
    ): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $file = $event->getPosterFile();

            if ($file)
            {
                $filename = md5(uniqid()) . "." . $file->guessExtension();
                $file->move(
                    $this->getParameter("event_images_directory"),
                    $filename
                );
                $event->setPoster($filename);
            }

            if ($file === null)
            {
                $event->setPoster("no-image.webp");
            }

            $em = $doctrine->getManager();
            $em->persist($event);
            $em->flush();

            return $this->redirectToRoute("app_eventList");
        }

        return $this->render("/event/creer.html.twig", [
            "form" => $form,
        ]);
    }

    #[Route("/evenement/{id}/edit", name: "event_edit")]
    public function edit(
        Request $request,
        ManagerRegistry $doctrine,
        Event $event
    )
    {
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            if ($file = $event->getPosterFile())
            {
                if ($poster = $event->getPoster())
                {
                    @unlink(
                        $this->getParameter("event_images_directory") . $poster
                    );
                }
                $filename = md5(uniqid()) . "." . $file->guessExtension();
                $file->move(
                    $this->getParameter("event_images_directory"),
                    $filename
                );
                $event->setPoster($filename);
            }

            $em = $doctrine->getManager();
            $em->flush();

            return $this->redirectToRoute("app_eventList");
        }

        return $this->render("event/edit.html.twig", [
            "form" => $form->createView(),
            "event" => $event,
        ]);
    }

    #[Route("/evenement/{id}/delete", name: "event_delete")]
    public function delete(
        Request $request,
        ManagerRegistry $doctrine,
        Event $event
    ): Response
    {
        if (
            $this->isCsrfTokenValid(
                "delete-" . $event->getId(),
                $request->get("token")
            )
        )
        {
            if (
                $event->getPoster() &&
                $event->getPoster() !== "no-image.webp"
            )
            {
                @unlink(
                    $this->getParameter("event_images_directory") .
                        $event->getPoster()
                );
            }

            $em = $doctrine->getManager();
            $em->remove($event);
            $em->flush();
        }
        return $this->redirectToRoute("app_eventList");
    }
}
