<?php

namespace AppBundle\Controller;

use AppBundle\Entity\CalendarEvent;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Calendarevent controller.
 *
 * @Route("calendarevent")
 */
class CalendarEventController extends Controller
{
    /**
     * Lists all calendarEvent entities.
     *
     * @Route("/", name="calendarevent_index")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $calendarEvents = $em->getRepository('AppBundle:CalendarEvent')->findAll();
        $calendarEvent = new CalendarEvent();
        $form = $this->createForm('AppBundle\Form\CalendarEventType', $calendarEvent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($calendarEvent);
            $em->flush();

            return $this->redirectToRoute('calendarevent_show', array('id' => $calendarEvent->getId()));
        }

        return $this->render('calendarevent/index.html.twig', array(
            'calendarEvents' => $calendarEvents,
            'form' => $form->createView(),
        ));
    }

    /**
     * Creates a new calendarEvent entity.
     *
     * @Route("/new", name="calendarevent_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $calendarEvent = new CalendarEvent();
        $form = $this->createForm('AppBundle\Form\CalendarEventType', $calendarEvent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($calendarEvent);
            $em->flush();

            return $this->redirectToRoute('calendarevent_show', array('id' => $calendarEvent->getId()));
        }

        return $this->render('calendarevent/new.html.twig', array(
            'calendarEvent' => $calendarEvent,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a calendarEvent entity.
     *
     * @Route("/{id}", name="calendarevent_show")
     * @Method("GET")
     */
    public function showAction(CalendarEvent $calendarEvent)
    {
        $deleteForm = $this->createDeleteForm($calendarEvent);

        return $this->render('calendarevent/show.html.twig', array(
            'calendarEvent' => $calendarEvent,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing calendarEvent entity.
     *
     * @Route("/{id}/edit", name="calendarevent_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, CalendarEvent $calendarEvent)
    {
        $deleteForm = $this->createDeleteForm($calendarEvent);
        $editForm = $this->createForm('AppBundle\Form\CalendarEventType', $calendarEvent);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('calendarevent_edit', array('id' => $calendarEvent->getId()));
        }

        return $this->render('calendarevent/edit.html.twig', array(
            'calendarEvent' => $calendarEvent,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a calendarEvent entity.
     *
     * @Route("/{id}", name="calendarevent_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, CalendarEvent $calendarEvent)
    {
        $form = $this->createDeleteForm($calendarEvent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($calendarEvent);
            $em->flush();
        }

        return $this->redirectToRoute('calendarevent_index');
    }

    /**
     * Creates a form to delete a calendarEvent entity.
     *
     * @param CalendarEvent $calendarEvent The calendarEvent entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(CalendarEvent $calendarEvent)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('calendarevent_delete', array('id' => $calendarEvent->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * @Route("/loadCalendarEvent", name="load_calendar_event")
     * @Method("POST")
     */
    public function loadCalendarEventAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $calendarEvents = $em->getRepository('AppBundle:CalendarEvent')->findAll();

        $calendarEventsArray = [];
        foreach ($calendarEvents as $calendarEvent) {
            $calendarEventsArray[] = $calendarEvent->toArray();
        }
        $data = json_encode($calendarEventsArray);

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent($data);
        $response->setStatusCode(200);

        return $response;
    }

    /**
     * @Route("/newCalendarEvent", name="new_calendar_event")
     * @Method("POST")
     */
    public function newCalendarEventAction(Request $request)
    {
        $calendarEvent = new CalendarEvent();
        $calendarEvent->fromArray($request->get('newEvent'));
        $em = $this->getDoctrine()->getManager();
        $em->persist($calendarEvent);
        $em->flush();



        dump(array('result' => $calendarEvent));

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent("{'success': true}");
        $response->setStatusCode(200);

        return $response;
    }

    /**
     * @Route("/editCalendarEvent", name="edit_calendar_event")
     * @Method("POST")
     */
    public  function editCalendarEvent(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $calendarEvent = new CalendarEvent();
        $calendarEvent->setId($request->get('editEvent')['entityId']);
        $calendarEvent->fromArray($request->get('editEvent'));
        $em->merge($calendarEvent);
        $em->flush();

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent("{'success': true}");
        $response->setStatusCode(200);

        return $response;
    }

}