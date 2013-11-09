<?php

namespace Tracker\Bundle\BackBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Tracker\Bundle\BackBundle\Entity\Series;
use Tracker\Bundle\BackBundle\Form\SeriesType;

/**
 * Series controller.
 *
 */
class SeriesController extends Controller
{

    /**
     * Lists all Series entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('TrackerBackBundle:Series')->findBy(
            array(),
            array(
                'name' => 'ASC'
            )
        );

        return $this->render('TrackerBackBundle:Series:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Series entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity  = new Series();
        $form = $this->createForm(new SeriesType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('series_show', array('id' => $entity->getId())));
        }

        return $this->render('TrackerBackBundle:Series:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to create a new Series entity.
     *
     */
    public function newAction()
    {
        $entity = new Series();
        $form   = $this->createForm(new SeriesType(), $entity);

        return $this->render('TrackerBackBundle:Series:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Series entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('TrackerBackBundle:Series')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Series entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('TrackerBackBundle:Series:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    public function feedAction(Series $series, $format = 'rss')
    {
        $em = $this->getDoctrine()->getManager();
        $torrentRepo = $em->getRepository('TrackerBackBundle:Torrent');
        $torrents = $torrentRepo->findBySeries($series);

        $feed = $this->get('eko_feed.feed.manager')->get('torrents');
        $feed->addFromArray($torrents);

        return new Response($feed->render($format));
    }

    /**
     * Displays a form to edit an existing Series entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('TrackerBackBundle:Series')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Series entity.');
        }

        $editForm = $this->createForm(new SeriesType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('TrackerBackBundle:Series:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing Series entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('TrackerBackBundle:Series')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Series entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new SeriesType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $entity->preUpdate();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('series_edit', array('id' => $id)));
        }

        return $this->render('TrackerBackBundle:Series:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Series entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('TrackerBackBundle:Series')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Series entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('series'));
    }

    /**
     * Creates a form to delete a Series entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
