<?php

namespace UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\User;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $users = $this->getDoctrine()->getRepository('UserBundle:User')->findAll();
        return $this->render('UserBundle:Default:index.html.twig', array('users' => $users));
    }
    public function userCreateAction(Request $request)
    {

        $user = new User;
        $form = $this->createFormBuilder($user)
        ->add('email', 'text')
        ->add('username', 'text')
        ->add('password', 'password')
        ->add('roles', 'collection', array(
            'type'   => 'choice',
            'options'  => array(
                'choices'   => array(
                    'ROLE_USER' => 'User',
                    'ROLE_ADMIN' => 'Administrator',
                ),
            ),
        ))
        ->add('save', 'submit')
        ->getForm();

        $form->handleRequest($request);

        $validator = $this->get('validator');
        $errors = $validator->validate($user);

        if($form->isValid()){
            $user->setSalt('');
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return $this->redirect($this->generateUrl('users_list'));
        }

        return $this->render('UserBundle:Default:user_create.html.twig', array(
            'form' => $form->createView(),
            'errors' => $errors,

        ));
    }
    public function userUpdateAction($id, Request $request)
    {
        if (is_null($id)) {
            $postData = $request->get('testimonial');
            $id = $postData['id'];
        }

        $em = $this->getDoctrine()->getEntityManager();
        $user = $em->getRepository('UserBundle:User')->find($id);
        $form = $this->createFormBuilder($user)
        ->add('email', 'text')
        ->add('username', 'text')
        ->add('password', 'password')
        ->add('roles', 'collection', array(
            'type'   => 'choice',
            'options'  => array(
                'choices'   => array(
                    'ROLE_USER' => 'User',
                    'ROLE_ADMIN' => 'Administrator',
                ),
            ),
            'required'  => true,
        ))
        ->add('isActive', 'checkbox', array(
            'label'     => 'Is active',
            'required'  => true,
        ))
        ->add('save', 'submit')
        ->getForm();

        $form->handleRequest($request);

        $validator = $this->get('validator');
        $errors = $validator->validate($user);

        if($form->isValid()){
            $user->setSalt('');
            $em->persist($user);
            $em->flush();
            return $this->redirect($this->generateUrl('users_list'));
        }

        return $this->render('UserBundle:Default:user_create.html.twig', array(
            'form' => $form->createView(),
            'errors' => $errors,

        ));
    }
}
