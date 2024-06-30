<?php
/**
 * Security Controller.
 */

namespace App\Controller;

use App\Form\Type\ChangePasswordFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Service\UserServiceInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Controller handling security operations like login, logout, and password change.
 */
class SecurityController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param TranslatorInterface $translator Translator
     */
    public function __construct(private readonly TranslatorInterface $translator)
    {
    }

    /**
     * Handles the login functionality.
     *
     * @Route(path: '/login', name: 'app_login')
     *
     * @param AuthenticationUtils $authenticationUtils Utility to get the authentication error and last username
     *
     * @return Response The rendered login page
     */
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * Handles the logout functionality.
     *
     * @Route(path: '/logout', name: 'app_logout')
     *
     * @throws \LogicException this method can be blank - it will be intercepted by the logout key on your firewall
     */
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * Handles the change password functionality.
     *
     * @Route(path: '/change-password', name: 'app_change_password')
     *
     * @param Request                     $request        HTTP request
     * @param UserServiceInterface        $userService    User service for handling user operations
     * @param UserPasswordHasherInterface $passwordHasher Password hasher service for hashing passwords
     *
     * @return Response The response for changing password
     */
    #[Route(path: '/change-password', name: 'app_change_password')]
    public function changePassword(Request $request, UserServiceInterface $userService, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $this->getUser();

        if (!$user instanceof PasswordAuthenticatedUserInterface) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $currentPassword = $form->get('currentPassword')->getData();
            $newPassword = $form->get('newPassword')->getData();

            if ($passwordHasher->isPasswordValid($user, $currentPassword)) {
                $userService->changePassword($user, $newPassword);

                $this->addFlash('success', $this->translator->trans('message.password_changed_successfully'));

                return $this->redirectToRoute('article_index');
            }

            $form->get('currentPassword')->addError(new FormError($this->translator->trans('message.incorrect_password')));
        }

        return $this->render('security/change_password.html.twig', [
            'changePasswordForm' => $form->createView(),
        ]);
    }
}
