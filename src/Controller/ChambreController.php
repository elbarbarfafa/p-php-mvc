<?php 

namespace App\Controller;

use App\Document\Chambre;
use App\Form\ChambreType;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Routing\Annotation\Route;
class ChambreController extends AbstractController
{
    
    #[Route('/admin/rooms', name: 'admin_rooms')]
    public function listRooms(DocumentManager $dm, PaginatorInterface $paginator, Request $request): Response
    {
        // Crée un QueryBuilder pour récupérer les chambres depuis le dépôt de Chambre
        $queryBuilder = $dm->getRepository(Chambre::class)->createQueryBuilder();

        // Récupère le type recherché depuis la requête s'il est renseigné
        $search = $request->query->get('search');
        if ($search) {
            $queryBuilder
                ->addOr($queryBuilder->expr()->field('type')->equals($search));
        }
        
        // Paginer les résultats
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10 // Nombre d'éléments par page
        );

        // Retourne la vue avec les résultats
        return $this->render('chambre/rooms.html.twig', [
            'pagination' => $pagination,
            'search' => $search,
        ]);
    }

    #[Route('/admin/room/new', name: 'admin_room_new')]
    public function newRoom(Request $request, DocumentManager $dm): Response
    {
        $room = new Chambre();
        // Crée un formulaire pour la chambre
        $form = $this->createForm(ChambreType::class, $room);

        // Gère la requête du formulaire
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Persiste la nouvelle chambre dans la base de données
            $dm->persist($room);
            // Sauvegarde les changements
            $dm->flush();

            // Redirige vers la liste des chambres
            return $this->redirectToRoute('admin_rooms');
        }

        // Retourne la vue du formulaire par défaut
        return $this->render('chambre/room_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/room/edit/{id}', name: 'admin_room_edit')]
    public function editRoom(Request $request, DocumentManager $dm, $id): Response
    {
         // Récupère la chambre à éditer par son identifiant
        $room = $dm->getRepository(Chambre::class)->find($id);

         // Si la chambre n'existe pas, lance une exception
        if (!$room) {
            throw $this->createNotFoundException('La chambre n\'existe pas.');
        }

         // Crée un formulaire pour la chambre
        $form = $this->createForm(ChambreType::class, $room);
        // Gère la requête du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
             // Sauvegarde les changements
            $dm->flush();
            
            // Redirige vers la liste des chambres
            return $this->redirectToRoute('admin_rooms');
        }

        // Retourne la vue du formulaire
        return $this->render('chambre/room_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/admin/room/delete/{id}', name: 'admin_room_delete')]
    public function deleteRoom(DocumentManager $dm, $id): Response
    {
         // Récupère la chambre à supprimer par son identifiant
        $room = $dm->getRepository(Chambre::class)->find($id);

         // Si la chambre existe, la supprime de la base de données
        if ($room) {
            $dm->remove($room);
            $dm->flush();
        }

        // Redirige vers la liste des chambres
        return $this->redirectToRoute('admin_rooms');
    }
}