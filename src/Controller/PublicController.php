<?php

namespace App\Controller;

use App\Document\Chambre;
use App\Document\Reservation;
use App\Repository\ChambreRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PublicController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function home(): Response
    {
        // Logique pour afficher la page d'accueil
        return $this->render('public/home.html.twig');
    }

    #[Route('/search', name: 'search_room')]
    public function searchRoom(Request $request, DocumentManager $dm): Response
    {
        $dateDebut = $request->query->get('date_debut');
        $dateFin = $request->query->get('date_fin');

        $availableRooms = [];

        if ($dateDebut && $dateFin) {
            $dateDebut = new \DateTime($dateDebut);
            $dateFin = new \DateTime($dateFin);

            // Récupérer toutes les chambres
            $rooms = $dm->getRepository(Chambre::class)->findAll();

            // Filtrer les chambres disponibles
            foreach ($rooms as $room) {
                $reservations = $dm->getRepository(Reservation::class)->createQueryBuilder()
                    ->field('chambres')->equals($room)
                    ->field('date_debut')->lte($dateFin)
                    ->field('date_fin')->gte($dateDebut)
                    ->getQuery()
                    ->execute();

                if (count($reservations) === 0) {
                    if (!isset($availableRooms[$room->hotel->nom_hotel])) {
                        $availableRooms[$room->hotel->nom_hotel] = [];
                    }
                    array_push($availableRooms[$room->hotel->nom_hotel], $room);
                }
            }
        }

        return $this->render('public/search.html.twig', [
            'availableRooms' => $availableRooms,
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin,
        ]);
    }

    #[Route('/user/reserve', name: 'reserve_rooms')]
    public function reserveRoom(Request $request, DocumentManager $dm, LoggerInterface $logger): Response
    {
        $roomIds = $request->request->all('room_ids');
        $logger->debug($roomIds[0]);
        $logger->debug($request->request->get('date_debut'));
        $logger->debug($request->request->get('date_fin'));
        $dateDebut = new \DateTime($request->request->get('date_debut'));
        $dateFin = new \DateTime($request->request->get('date_fin'));
        $comment = $request->request->get('comment');
        if (!is_array($roomIds)) {
            $roomIds = [$roomIds];
        }
        $logger->info($roomIds[0]);
        $rooms = $dm->createQueryBuilder(Chambre::class)
            ->field('code_chambre')->in($roomIds)
            ->getQuery()
            ->execute()->toArray();
        $client = $this->getUser(); // Supposons que l'utilisateur connecté est le client
        if (sizeof($rooms) < 1) {
            $logger->info("Pas de chambre sélectionné ou trouvé");
            return $this->redirectToRoute('search_room');
        }
        $hotel = $rooms[0]->hotel; // Assurez-vous que toutes les chambres appartiennent au même hôtel
        foreach ($rooms as $room) {
            if ($room->hotel != $hotel) {
                // Si les chambres ne sont pas du même hôtel, rediriger avec un message d'erreur
                $this->addFlash('error', 'Toutes les chambres doivent appartenir au même hôtel.');
                return $this->redirectToRoute('search_room');
            }
        }

        $reservation = new Reservation();
        $reservation->hotel = $hotel;
        $reservation->client = $client;
        $reservation->chambres = $rooms;
        $reservation->date_debut = $dateDebut;
        $reservation->date_fin = $dateFin;
        $reservation->comment = $comment;

        $dm->persist($reservation);
        $dm->flush();

        return $this->redirectToRoute('mes_reservations');

        return $this->redirectToRoute('search_room');
    }
}
