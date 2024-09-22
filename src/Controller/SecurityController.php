<?php

namespace App\Controller;

use App\Document\Client;
use App\Form\RegistrationType;
use App\Form\ResetPasswordRequestType;
use App\Form\ResetPasswordType;
use Doctrine\ODM\MongoDB\DocumentManager;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

class SecurityController extends AbstractController
{
    
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, DocumentManager $dm): Response
    {
        $user = new Client();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hash the password
            $user->setPassword(
                $passwordHasher->hashPassword(
                    $user,
                    plainPassword: $form->get('password')->getData()
                )
            );

            $dm->persist($user);
            $dm->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('security/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
    
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }



    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    private $resetPasswordHelper;

    public function __construct(ResetPasswordHelperInterface $resetPasswordHelper)
    {
        $this->resetPasswordHelper = $resetPasswordHelper;
    }

    #[Route('/reset-password', name: 'app_forgot_password_request')]
    public function request(Request $request, DocumentManager $dm, LoggerInterface $logger): Response
    {
        $form = $this->createForm(ResetPasswordRequestType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $user = $dm->getRepository(Client::class)->findOneBy(['email' => $email]);
            if ($user) {
                $resetToken = $this->resetPasswordHelper->generateFakeResetToken();
                /**
                 * Ici nous avons mis l'user ID car le fake token ne nous permet de récupérer l'utilisateur rattaché à ce token de réinitialisation.
                 */
                return $this->redirectToRoute('app_reset_password', ['resetToken' => $resetToken->getToken(), 'userId' => $user->code_client]);
            } else {
                
                $logger->error('Aucun utilisateur est rattaché à cet email.');
            }

            return $this->redirectToRoute('app_forgot_password_request');
        }

        return $this->render('security/reset_password_request.html.twig', [
            'requestForm' => $form->createView(),
        ]);
    }

    #[Route('/reset-password/{resetToken}/{userId}', name: 'app_reset_password')]
    public function reset(Request $request, string $resetToken, string $userId, UserPasswordHasherInterface $passwordHasher, DocumentManager $dm): Response
    {
        ////$user = $this->resetPasswordHelper->($resetToken);
        $user = $dm->getRepository(Client::class)->findOneBy(['code_client'=>$userId]);

        if (!$user) {
            $this->addFlash('error', 'Invalid or expired reset token.');
            return $this->redirectToRoute('app_forgot_password_request');
        }

        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $encodedPassword = $passwordHasher->hashPassword($user, $form->get('plainPassword')->getData());
            $user->setPassword($encodedPassword);
            $dm->flush();

            $this->addFlash('success', 'Mot de passe modifié.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/reset_password.html.twig', [
            'resetForm' => $form->createView(),
        ]);
    }

}
