<?php 
namespace App\Controller;

// Importation des classes nécessaires
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ODM\MongoDB\DocumentManager;
use App\Document\Hotel;
use App\Form\HotelType;
use Knp\Component\Pager\PaginatorInterface;

// Déclaration de la classe HotelController qui hérite de AbstractController
class HotelController extends AbstractController
{

    #[Route('/admin/hotels', name: 'admin_hotels')]
    public function listHotels(DocumentManager $dm, PaginatorInterface $paginator, Request $request): Response
    {
        // Crée un QueryBuilder pour récupérer les hôtels depuis le dépôt de Hotel
        $queryBuilder = $dm->getRepository(Hotel::class)->createQueryBuilder();

        // Récupère le terme de recherche depuis la requête
        $search = $request->query->get('search');
        if ($search) {
            // Ajoute une condition de recherche pour le nom de l'hôtel
            $queryBuilder
                ->addOr($queryBuilder->expr()->field('nom_hotel')->equals($search));
        }

        // Paginer les résultats
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10 // Nombre d'éléments par page
        );

        // Rendre la vue avec les résultats paginés et le terme de recherche
        return $this->render('hotel/hotels.html.twig', [
            'pagination' => $pagination,
            'search' => $search,
        ]);
    }

    #[Route('/admin/hotel/new', name: 'admin_hotel_new')]
    public function newHotel(Request $request, DocumentManager $dm): Response
    {
        // Crée une nouvelle instance de Hotel
        $hotel = new Hotel();
        // Crée un formulaire pour l'hôtel
        $form = $this->createForm(HotelType::class, $hotel);

        // Gère la requête du formulaire
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Persiste le nouvel hôtel dans la base de données
            $dm->persist($hotel);
            // Sauvegarde les changements
            $dm->flush();

            // Redirige vers la liste des hôtels
            return $this->redirectToRoute('admin_hotels');
        }

        // Rendre la vue du formulaire
        return $this->render('hotel/hotel_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/hotel/edit/{id}', name: 'admin_hotel_edit')]
    public function editHotel(Request $request, DocumentManager $dm, $id): Response
    {
        // Récupère l'hôtel à éditer par son identifiant
        $hotel = $dm->getRepository(Hotel::class)->find($id);

        // Si l'hôtel n'existe pas, lance une exception
        if (!$hotel) {
            throw $this->createNotFoundException('L\'hôtel n\'existe pas.');
        }

        // Crée un formulaire pour l'hôtel
        $form = $this->createForm(HotelType::class, $hotel);
        // Gère la requête du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Sauvegarde les changements
            $dm->flush();

            // Redirige vers la liste des hôtels
            return $this->redirectToRoute('admin_hotels');
        }

        // Rendre la vue du formulaire
        return $this->render('hotel/hotel_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/hotel/delete/{id}', name: 'admin_hotel_delete')]
    public function deleteHotel(DocumentManager $dm, $id): Response
    {
        // Récupère l'hôtel à supprimer par son identifiant
        $hotel = $dm->getRepository(Hotel::class)->find($id);

        // Si l'hôtel existe, le supprime de la base de données
        if ($hotel) {
            $dm->remove($hotel);
            $dm->flush();
        }

        // Redirige vers la liste des hôtels
        return $this->redirectToRoute('admin_hotels');
    }
}
