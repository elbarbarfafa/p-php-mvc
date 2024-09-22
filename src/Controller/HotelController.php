<?php 
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ODM\MongoDB\DocumentManager;
use App\Document\Hotel;
use App\Form\HotelType;
use Knp\Component\Pager\PaginatorInterface;

class HotelController extends AbstractController
{
    #[Route('/admin/hotels', name: 'admin_hotels')]
    public function listHotels(DocumentManager $dm, PaginatorInterface $paginator, Request $request): Response
    {
        $queryBuilder = $dm->getRepository(Hotel::class)->createQueryBuilder();

        $search = $request->query->get('search');
        if ($search) {
            $queryBuilder
                ->addOr($queryBuilder->expr()->field('nom_hotel')->equals($search));
        }

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10 // Nombre d'éléments par page
        );

        return $this->render('hotel/hotels.html.twig', [
            'pagination' => $pagination,
            'search' => $search,
        ]);
    }

    #[Route('/admin/hotel/new', name: 'admin_hotel_new')]
    public function newHotel(Request $request, DocumentManager $dm): Response
    {
        $hotel = new Hotel();
        $form = $this->createForm(HotelType::class, $hotel);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $dm->persist($hotel);
            $dm->flush();

            return $this->redirectToRoute('admin_hotels');
        }

        return $this->render('hotel/hotel_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/hotel/edit/{id}', name: 'admin_hotel_edit')]
    public function editHotel(Request $request, DocumentManager $dm, $id): Response
    {
        $hotel = $dm->getRepository(Hotel::class)->find($id);

        if (!$hotel) {
            throw $this->createNotFoundException('L\'hôtel n\'existe pas.');
        }

        $form = $this->createForm(HotelType::class, $hotel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dm->flush();

            return $this->redirectToRoute('admin_hotels');
        }

        return $this->render('hotel/hotel_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/hotel/delete/{id}', name: 'admin_hotel_delete')]
    public function deleteHotel(DocumentManager $dm, $id): Response
    {
        $hotel = $dm->getRepository(Hotel::class)->find($id);

        if ($hotel) {
            $dm->remove($hotel);
            $dm->flush();
        }

        return $this->redirectToRoute('admin_hotels');
    }
}
