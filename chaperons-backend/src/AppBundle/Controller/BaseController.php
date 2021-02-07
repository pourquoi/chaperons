<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class BaseController extends FOSRestController
{
    /**
     * @return EntityManager
     */
    protected function getEm() {
        return $this->getDoctrine()->getManager();
    }

    /**
     * If the authenticated user is allowed access to the user id, return the user.
     *
     * @param $id
     *
     * @return User
     */
    protected function getAuthorizedUser($id) {
        $this->denyAccessUnlessGranted('ROLE_USER');

        if( ! ($this->getUser() instanceof User) ) {
            throw new AccessDeniedException();
        }

        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);

        if( !$user ) {
            throw new NotFoundHttpException();
        }

        // here add logic for admin user
        // ...

        if( $user->getUsername() != $this->getUser()->getUsername() ) {
            throw new AccessDeniedException();
        }

        return $user;
    }
}