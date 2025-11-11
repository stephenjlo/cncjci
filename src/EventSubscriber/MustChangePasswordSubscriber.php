<?php
namespace App\EventSubscriber;

use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class MustChangePasswordSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private TokenStorageInterface $tokenStorage,
        private UrlGeneratorInterface $urlGenerator
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 0],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        // Seulement pour la requête principale
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $route = $request->attributes->get('_route');

        // Routes autorisées même si mustChangePassword = true
        $allowedRoutes = [
            'app_change_password',
            'app_logout',
            '_wdt', // Profiler
            '_profiler', // Profiler
        ];

        // Ne pas rediriger si déjà sur une route autorisée
        if (in_array($route, $allowedRoutes)) {
            return;
        }

        // Vérifier si l'utilisateur est connecté
        $token = $this->tokenStorage->getToken();
        if (!$token || !$token->getUser()) {
            return;
        }

        $user = $token->getUser();

        // Vérifier si c'est une instance de User et si mustChangePassword
        if ($user instanceof User && $user->mustChangePassword()) {
            // Rediriger vers la page de changement de mot de passe
            $url = $this->urlGenerator->generate('app_change_password');
            $event->setResponse(new RedirectResponse($url));
        }
    }
}
