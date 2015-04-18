<?php

namespace KDSM\ContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use KDSM\ContentBundle\Form\ParameterType;
use KDSM\ContentBundle\Entity\Parameter;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends Controller
{
    public function indexAction(Request $request)
    {
        $parameter = new Parameter();
        $form = $this->createForm(new ParameterType(), $parameter);

        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        $rep = $em->getRepository('KDSMContentBundle:Parameter');

        if ($form->isValid()) {
            $em->persist($parameter);
            $em->flush();

            return $this->redirectToRoute('admin');
        }

        return $this->render('KDSMContentBundle:Admin:index.html.twig', array(
                'form' => $form->createView(),
                'parameters' => $rep->getAllParameters()
            ));
    }

    public function changeAction(Request $request){

        $parameter = new Parameter();

        $parameter->setParameterName($request->request->get('id'));
        $parameter->setParameterValue($request->request->get('value'));

        $em = $this->getDoctrine()->getManager();
        $em->persist($parameter);
        $em->flush();

        return $this->render('KDSMContentBundle:Admin:postparameter.html.twig', array(
            'value' => $parameter->getParameterValue()
        ));
    }

    public function removeAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $rep= $em->getRepository('KDSMContentBundle:Parameter');

        $rep->removeParameter($request->request->get('parameter'));
    }

}
