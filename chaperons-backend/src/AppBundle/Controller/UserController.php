<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\User\UserProviderInterface;


class UserController extends BaseController
{
    /**
     * @ApiDoc(
     *   section="User"
     * )
     */
    public function postUserLoginAction(Request $request)
    {


        // ici on peut ajouter le sso et créér l'utilisateur s'il existe pas


        /** @var UserProviderInterface $provider */
        $provider = $this->getDoctrine()->getRepository('AppBundle:User');
        $user = $provider->loadUserByUsername($request->request->get('username'));

        if( !$user ) {
            throw new BadCredentialsException(sprintf('user %s not found', $request->request->get('username')));
        }

        /** @var UserPasswordEncoder $encoder */
        $encoder = $this->container->get('security.password_encoder');
        if( !$encoder->isPasswordValid($user, $request->request->get('password')) ) {
            throw new BadCredentialsException('wrong password');
        }

        $view = $this->view($user, 201);

        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="User"
     * )
     */
    public function getUserAction($id)
    {
        $user = $this->getAuthorizedUser($id);
        $view = $this->view($user);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="User"
     * )
     */
    public function postUserAction(Request $request)
    {
        $user = new User();

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if( $form->isValid() ) {
            $user->setApiKey(User::generateApiKey());

            /** @var UserPasswordEncoder $encoder */
            $encoder = $this->container->get('security.password_encoder');
            $encoded = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($encoded);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $view = $this->view($user, 201);
            return $this->handleView($view);
        }

        $view = $this->view($form, 400);
        return $this->handleView($view);
    }
}