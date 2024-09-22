<?php
namespace App\Controller;

use App\Document\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ODM\MongoDB\DocumentManager;
use App\Document\Reservation;
use App\Form\ClientType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

class ClientController extends AbstractController
{

    
    #[Route('/admin/clients', name: 'admin_clients')]
    public function listClients(DocumentManager $dm, PaginatorInterface $paginator, Request $request): Response
    {
        $queryBuilder = $dm->getRepository(Client::class)->createQueryBuilder();

        $search = $request->query->get('search');
        if ($search) {
            $queryBuilder
                ->addOr($queryBuilder->expr()->field('nom_client')->equals($search))
                ->addOr($queryBuilder->expr()->field('email')->equals($search));
        }

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10 // Nombre d'éléments par page
        );

        return $this->render('client/clients.html.twig', [
            'pagination' => $pagination,
            'search' => $search,
        ]);
    }

    #[Route('/admin/client/new', name: 'admin_client_new')]
    public function newClient(Request $request, DocumentManager $dm): Response
    {
        $client = new Client();
        $form = $this->createForm(ClientType::class, $client);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $dm->persist($client);
            $dm->flush();

            return $this->redirectToRoute('admin_clients');
        }

        return $this->render('client/client_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/client/edit/{id}', name: 'admin_client_edit')]
    public function editClient(Request $request, DocumentManager $dm, $id): Response
    {
        $client = $dm->getRepository(Client::class)->find($id);

        if (!$client) {
            throw $this->createNotFoundException('Le client n\'existe pas.');
        }

        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dm->flush();

            return $this->redirectToRoute('admin_clients');
        }

        return $this->render('client/client_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/client/delete/{id}', name: 'admin_client_delete')]
    public function deleteClient(DocumentManager $dm, $id): Response
    {
        $client = $dm->getRepository(Client::class)->find($id);

        if ($client) {
            $dm->remove($client);
            $dm->flush();
        }

        return $this->redirectToRoute('admin_clients');
    }
}