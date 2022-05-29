<?php

namespace App\Controller\Admin;

use App\Entity\Team;
use App\Event\AdminCRUDEvent;
use App\Form\Admin\TeamType;
use App\Repository\TeamRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class TeamController extends AbstractController
{
    public function index(
        Request $request,
        TeamRepository $teamRepository,
        PaginatorInterface $paginator)
    {
        $qb = $teamRepository->getAdmins();

        $teams = $paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('admin/team/index.html.twig', [
            'teams' => $teams,
        ]);
    }

    public function create(
    Request $request,
        EntityManagerInterface $em,
        EventDispatcherInterface $dispatcher)
    {
        $team = new Team();
        $form = $this->createForm(TeamType::class, $team);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event = new AdminCRUDEvent($team);

            $dispatcher->dispatch($event, AdminCRUDEvent::PRE_CREATE);

            $em->persist($team);
            $em->flush();

            $dispatcher->dispatch($event, AdminCRUDEvent::POST_CREATE);

            $this->addFlash('success', 'Un membre a été crée');

            return $this->redirectToRoute('app_admin_team_index');
        }

        return $this->render('admin/team/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function edit(
        Request $request,
        EntityManagerInterface $em,
        EventDispatcherInterface $dispatcher,
        Team $team): Response
    {
        $form = $this->createForm(TeamType::class, $team);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event = new AdminCRUDEvent($team);

            $dispatcher->dispatch($event, AdminCRUDEvent::PRE_EDIT);

            $em->flush();

            $dispatcher->dispatch($event, AdminCRUDEvent::POST_EDIT);

            $this->addFlash('success', 'Un membre a été mise à jour');

            return $this->redirectToRoute('app_admin_team_index');
        }

        return $this->render('admin/team/edit.html.twig', [
            'form' => $form->createView(),
            'team' => $team,
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
        Team $team
    )
    {
        if ($request->query->has('pos')) {
            $pos = ($team->getPosition() + (int)$request->query->get('pos'));

            if ($pos >= 0) {
                $team->setPosition($pos);
                $em->flush();

                $this->addFlash('success', 'La position du témoignage a été modifier');
            }
        }

        return  $this->redirectToRoute('app_admin_team_index');
    }

    public function delete(
        Request $request,
        EntityManagerInterface $em,
        EventDispatcherInterface $dispatcher,
        Team $team)
    {
        $form = $this->deleteForm($team);

        if ($request->getMethod() == 'DELETE') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $event = new AdminCRUDEvent($team);

                $dispatcher->dispatch($event, AdminCRUDEvent::PRE_DELETE);

                $em->remove($team);
                $em->flush();

                $dispatcher->dispatch($event, AdminCRUDEvent::POST_DELETE);

                $this->addFlash('success', 'Un membre a été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, le membre n\'a pas pu être supprimée!');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        $message = 'Être vous sur de vouloir supprimer cet membre ?';

        $render = $this->render('Ui/Modal/_delete.html.twig', [
            'form' => $form->createView(),
            'data' => $team,
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
                    $team = $em->getRepository(Team::class)->find($id);
                    $dispatcher->dispatch(new AdminCRUDEvent($team), AdminCRUDEvent::PRE_DELETE);

                    $em->remove($team);
                }

                $em->flush();

                $this->addFlash('success', 'Les membres ont été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, les membres n\'ont pas pu être supprimée !');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        if (count($ids) > 1)
            $message = 'Être vous sur de vouloir supprimer ces '.count($ids).' membres ?';
        else
            $message = 'Être vous sur de vouloir supprimer cet membre ?';

        $render = $this->render('Ui/Modal/_delete_multi.html.twig', [
            'form' => $form->createView(),
            'data' => $ids,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    private function deleteForm(Team $team)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_team_delete', ['id' => $team->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }

    private function deleteMultiForm()
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_team_bulk_delete'))
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


