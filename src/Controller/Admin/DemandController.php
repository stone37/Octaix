<?php

namespace App\Controller\Admin;

use App\Entity\Demand;
use App\Event\AdminCRUDEvent;
use App\Form\Filter\AdminDemandType;
use App\Model\Admin\DemandSearch;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class DemandController
 * @package App\Controller\Admin
 */
class DemandController extends AbstractController
{
    /**
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param PaginatorInterface $paginator
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(
        Request $request,
        EntityManagerInterface $em,
        PaginatorInterface $paginator)
    {
        $search = new DemandSearch();

        $form = $this->createForm(AdminDemandType::class, $search);

        $form->handleRequest($request);

        $qb = $em->getRepository(Demand::class)->getDemand($search);

        $demands = $paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('admin/demand/index.html.twig', [
            'demands' => $demands,
            'searchForm' => $form->createView(),
        ]);
    }

    /**
     * @param Demand $demand
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show(Demand $demand)
    {
        return $this->render('admin/demand/show.html.twig', [
            'demand' => $demand,
        ]);
    }

    /**
     * @param EntityManagerInterface $em
     * @param Request $request
     * @param Demand $demand
     * @return RedirectResponse
     */
    public function treat(
        EntityManagerInterface $em,
        Request $request,
        Demand $demand)
    {
        $demand->setEnabled(true);

        $em->flush();

        $this->addFlash('info', 'La demande a été marquer comme "traiter"');

        $url = $request->headers->get('referer');

        $response = new RedirectResponse($url);

        return $response;
    }

    /**
     * @param EntityManagerInterface $em
     * @param Request $request
     * @param Demand $demand
     * @return RedirectResponse
     */
    public function noTreat(
        EntityManagerInterface $em,
        Request $request,
        Demand $demand)
    {
        $demand->setEnabled(false);

        $em->flush();

        $this->addFlash('info', 'La demande a été marquer comme "non traiter"');

        $url = $request->headers->get('referer');

        $response = new RedirectResponse($url);

        return $response;
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param EventDispatcherInterface $dispatcher
     * @param Demand $demand
     * @return JsonResponse|RedirectResponse
     */
    public function delete(
        Request $request,
        EntityManagerInterface $em,
        EventDispatcherInterface $dispatcher,
        Demand $demand)
    {
        $form = $this->deleteForm($demand);

        if ($request->getMethod() == 'DELETE') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $event = new AdminCRUDEvent($demand);

                $dispatcher->dispatch($event, AdminCRUDEvent::PRE_DELETE);

                $em->remove($demand);
                $em->flush();

                $dispatcher->dispatch($event, AdminCRUDEvent::POST_DELETE);

                $this->addFlash('info', 'La demande a été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, la demande n\'a pas pu être supprimée!');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        $message = 'Être vous sur de vouloir supprimer cette demande ?';

        $render = $this->render('Ui/Modal/_delete.html.twig', [
            'form' => $form->createView(),
            'data' => $demand,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param SessionInterface $session
     * @param EventDispatcherInterface $dispatcher
     * @return JsonResponse|RedirectResponse
     */
    public function deleteBulk(
        Request $request,
        EntityManagerInterface $em,
        SessionInterface $session,
        EventDispatcherInterface $dispatcher
    )
    {
        $ids = (array)$request->query->get('data');

        if ($request->query->has('data'))
            $session->set('data', $request->query->get('data'));

        $form = $this->deleteMultiForm();

        if ($request->getMethod() == 'DELETE') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $ids = $session->get('data');
                $session->remove('data');

                foreach ($ids as $id) {
                    $demand = $em->getRepository(Demand::class)->find($id);
                    $dispatcher->dispatch(new AdminCRUDEvent($demand), AdminCRUDEvent::PRE_DELETE);

                    $em->remove($demand);
                }

                $em->flush();

                $this->addFlash('info', 'Les demandes ont été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, les demamdes n\'ont pas pu être supprimée!');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        if (count($ids) > 1)
            $message = 'Être vous sur de vouloir supprimer ces '.count($ids).' demandes ?';
        else
            $message = 'Être vous sur de vouloir supprimer cette demande ?';

        $render = $this->render('Ui/Modal/_delete_multi.html.twig', [
            'form' => $form->createView(),
            'data' => $ids,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    /**
     * @param Demand $demand
     * @return \Symfony\Component\Form\FormInterface
     */
    private function deleteForm(Demand $demand)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_demand_delete', ['id' => $demand->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * @return \Symfony\Component\Form\FormInterface
     */
    private function deleteMultiForm()
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_demand_bulk_delete'))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * @return array
     */
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

