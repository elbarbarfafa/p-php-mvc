<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ODM\MongoDB\DocumentManager;
use App\Document\Reservation;
use App\Form\ReservationType;
use Knp\Component\Pager\PaginatorInterface;

class ReservationController extends AbstractController
{
    /**
     * Récupère l'ensemble des réservations sous forme de pagination
     *
     * @param DocumentManager $dm
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/admin/reservations', name: 'admin_reservations')]
    public function listReservations(DocumentManager $dm, PaginatorInterface $paginator, Request $request): Response
    {
        $queryBuilder = $dm->getRepository(Reservation::class)->createQueryBuilder();

        $search = $request->query->get('search');
        if ($search) {
            $queryBuilder
                ->addOr($queryBuilder->expr()->field('id')->equals($search));
        }

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10 // Nombre d'éléments par page
        );

        return $this->render('reservation/reservations.html.twig', [
            'pagination' => $pagination,
            'search' => $search,
        ]);
    }
    /**
     * Détaille une seule réservation
     */
    #[Route('/admin/reservation/show/{id}', name: 'admin_reservation_show')]
    public function adminShowReservation(DocumentManager $dm, $id): Response
    {
        $reservation = $dm->getRepository(Reservation::class)->find($id);

        if (!$reservation) {
            throw $this->createNotFoundException(message: 'La réservation n\'existe pas.');
        }

        return $this->render('reservation/reservation_show.html.twig', [
            'reservation' => $reservation,
        ]);
    }

    /**
     * Route de création d'une réservation
     *
     * @param Request $request
     * @param DocumentManager $dm
     * @return Response
     */
    #[Route('/admin/reservation/new', name: 'admin_reservation_new')]
    public function adminNewReservation(Request $request, DocumentManager $dm): Response
    {
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $dm->persist($reservation);
            $dm->flush();

            return $this->redirectToRoute('admin_reservations');
        }

        return $this->render('reservation/reservation_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * 
     */
    #[Route('/admin/reservation/edit/{id}', name: 'admin_reservation_edit')]
    public function adminEditReservation(Request $request, DocumentManager $dm, $id): Response
    {
        $reservation = $dm->getRepository(Reservation::class)->find($id);

        if (!$reservation) {
            throw $this->createNotFoundException('La réservation n\'existe pas.');
        }

        $form = $this->createForm(ReservationType::class, $reservation, ['chambres' => $reservation->chambres]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dm->flush();

            return $this->redirectToRoute('admin_reservations');
        }

        return $this->render('reservation/reservation_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Route de suppression d'une réservation
     */
    #[Route('/admin/reservation/delete/{id}', name: 'admin_reservation_delete')]
    public function adminDeleteReservation(DocumentManager $dm, $id): Response
    {
        $reservation = $dm->getRepository(Reservation::class)->find($id);

        if ($reservation) {
            $dm->remove($reservation);
            $dm->flush();
        }

        return $this->redirectToRoute('admin_reservations');
    }


    #[Route('/user/reservations', name: 'mes_reservations')]
    public function userListReservations(DocumentManager $dm): Response
    {
        $client = $this->getUser();
        $reservations = $dm->getRepository(Reservation::class)->findBy(['client' => $client]);

        return $this->render('client/reservations.html.twig', [
            'reservations' => $reservations,
        ]);
    }

    #[Route('/user/reservation/{id}', name: 'reservation_show')]
    public function userShowReservation(DocumentManager $dm, $id): Response
    {
        $reservation = $dm->getRepository(Reservation::class)->find($id);

        if (!$reservation) {
            throw $this->createNotFoundException('La réservation n\'existe pas.');
        }

        return $this->render('reservation/reservation_show.html.twig', [
            'reservation' => $reservation,
        ]);
    }

    #[Route('/user/reservation/edit/{id}', name: 'reservation_edit')]
    public function userEditReservation(Request $request, DocumentManager $dm, $id): Response
    {
        $reservation = $dm->getRepository(Reservation::class)->find($id);

        if (!$reservation) {
            throw $this->createNotFoundException('La réservation n\'existe pas.');
        }

        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dm->flush();

            return $this->redirectToRoute('mes_reservations');
        }

        return $this->render('reservation/reservation_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/user/reservation/delete/{id}', name: 'reservation_delete')]
    public function userDeleteReservation(DocumentManager $dm, $id): Response
    {
        $reservation = $dm->getRepository(Reservation::class)->find($id);

        if ($reservation) {
            $dm->remove($reservation);
            $dm->flush();
        }

        return $this->redirectToRoute('mes_reservations');
    }

}
