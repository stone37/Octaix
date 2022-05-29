<?php

namespace App\Controller\Admin;

use App\Entity\Reference;
use App\Event\AdminCRUDEvent;
use App\Form\Admin\ReferenceType;
use App\Repository\ReferenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ReferenceController extends AbstractController
{
    public function index(
        Request $request,
        ReferenceRepository $referenceRepository,
        PaginatorInterface $paginator)
    {
        $qb = $referenceRepository->getAdmins();

        $references = $paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('admin/reference/index.html.twig', [
            'references' => $references,
        ]);
    }

    public function create(
        Request $request,
        EntityManagerInterface $em,
        EventDispatcherInterface $dispatcher)
    {
        $reference = new Reference();
        $form = $this->createForm(ReferenceType::class, $reference);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event = new AdminCRUDEvent($reference);

            $dispatcher->dispatch($event, AdminCRUDEvent::PRE_CREATE);

            $em->persist($reference);
            $em->flush();

            $dispatcher->dispatch($event, AdminCRUDEvent::POST_CREATE);

            $this->addFlash('success', 'Une reference a été crée');

            return $this->redirectToRoute('app_admin_reference_index');
        }

        return $this->render('admin/reference/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function edit(
        Request $request,
        EntityManagerInterface $em,
        EventDispatcherInterface $dispatcher,
        Reference $reference): Response
    {
        $form = $this->createForm(ReferenceType::class, $reference);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event = new AdminCRUDEvent($reference);

            $dispatcher->dispatch($event, AdminCRUDEvent::PRE_EDIT);

            $em->flush();

            $dispatcher->dispatch($event, AdminCRUDEvent::POST_EDIT);

            $this->addFlash('success', 'Une reference a été mise à jour');

            return $this->redirectToRoute('app_admin_reference_index');
        }

        return $this->render('admin/reference/edit.html.twig', [
            'form' => $form->createView(),
            'reference' => $reference,
        ]);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param $id
     * @return RedirectResponse
     */
    public function move(
        Request $request,
        EntityManagerInterface $em,
        Reference $reference
    )
    {
        if ($request->query->has('pos')) {
            $pos = ($reference->getPosition() + (int)$request->query->get('pos'));

            if ($pos >= 0) {
                $reference->setPosition($pos);
                $em->flush();

                $this->addFlash('success', 'La position de la reference a été modifier');
            }
        }

        return  $this->redirectToRoute('app_admin_reference_index');
    }

    public function delete(
        Request $request,
        EntityManagerInterface $em,
        EventDispatcherInterface $dispatcher,
        Reference $reference)
    {
        $form = $this->deleteForm($reference);

        if ($request->getMethod() == 'DELETE') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $event = new AdminCRUDEvent($reference);

                $dispatcher->dispatch($event, AdminCRUDEvent::PRE_DELETE);

                $em->remove($reference);
                $em->flush();

                $dispatcher->dispatch($event, AdminCRUDEvent::POST_DELETE);

                $this->addFlash('success', 'Une reference a été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, la reference n\'a pas pu être supprimée!');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        $message = 'Être vous sur de vouloir supprimer cette reference ?';

        $render = $this->render('Ui/Modal/_delete.html.twig', [
            'form' => $form->createView(),
            'data' => $reference,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    public function deleteBulk(
        Request $request,
        EntityManagerInterface $em,
        SessionInterface $session,
        EventDispatcherInterface $dispatcher
    )
    {
        $ids = $request->query->get('data');

        if ($request->query->has('data'))
            $session->set('data', $request->query->get('data'));

        $form = $this->deleteMultiForm();

        if ($request->getMethod() == 'DELETE') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $ids = $session->get('data');
                $session->remove('data');

                foreach ($ids as $id) {
                    $reference = $em->getRepository(Reference::class)->find($id);
                    $dispatcher->dispatch(new AdminCRUDEvent($reference), AdminCRUDEvent::PRE_DELETE);

                    $em->remove($reference);
                }

                $em->flush();

                $this->addFlash('success', 'Les references ont été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, les references n\'ont pas pu être supprimée !');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        if (count($ids) > 1)
            $message = 'Être vous sur de vouloir supprimer ces '.count($ids).' references ?';
        else
            $message = 'Être vous sur de vouloir supprimer cette reference ?';

        $render = $this->render('Ui/Modal/_delete_multi.html.twig', [
            'form' => $form->createView(),
            'data' => $ids,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    private function deleteForm(Reference $reference)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_reference_delete', ['id' => $reference->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }

    private function deleteMultiForm()
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_reference_bulk_delete'))
            ->setMethod('DELETE')
            ->getForm();
    }

    private function configuration()
    {
        return [
            'modal' => [
                'delete' => [
                    'type' => 'modal-danger',
                    'icon' => 'fas fa-times',
                    'yes_class' => 'btn-outline-danger',
                    'no_class' => 'btn-danger'
                ]
            ]
        ];
    }
}


