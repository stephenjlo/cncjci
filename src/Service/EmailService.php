<?php
namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Psr\Log\LoggerInterface;

class EmailService
{
    public function __construct(
        private MailerInterface $mailer,
        private LoggerInterface $logger,
        private string $fromEmail = 'info@cncj-ci.ci',
        private string $fromName = 'CNCJ CI - Conseil National des Cabinets Juridiques'
    ) {}

    /**
     * Envoie un email de bienvenue avec les identifiants de connexion
     *
     * @param string $recipientEmail Email du destinataire
     * @param string $recipientName Nom complet du destinataire
     * @param string $role Rôle attribué (ROLE_LAWYER ou ROLE_RESPO_CABINET)
     * @param string $password Mot de passe temporaire
     * @param string|null $cabinetName Nom du cabinet (optionnel)
     */
    public function sendWelcomeEmail(
        string $recipientEmail,
        string $recipientName,
        string $role,
        string $password,
        ?string $cabinetName = null
    ): bool {
        try {
            $roleLabel = match($role) {
                'ROLE_SUPER_ADMIN' => 'Super Administrateur',
                'ROLE_RESPO_CABINET' => 'Responsable de Cabinet',
                'ROLE_LAWYER' => 'Avocat',
                default => 'Utilisateur'
            };

            $email = (new TemplatedEmail())
                ->from(new Address($this->fromEmail, $this->fromName))
                ->to(new Address($recipientEmail, $recipientName))
                ->subject('Bienvenue sur la plateforme CNCJ-CI - Vos identifiants de connexion')
                ->htmlTemplate('emails/welcome.html.twig')
                ->context([
                    'recipientName' => $recipientName,
                    'role' => $roleLabel,
                    'email' => $recipientEmail,
                    'password' => $password,
                    'cabinetName' => $cabinetName,
                    'loginUrl' => 'https://admin.cncj-ci.ci/login', // Adapter selon votre domaine
                ]);

            $this->mailer->send($email);
            $this->logger->info('Email de bienvenue envoyé', [
                'recipient' => $recipientEmail,
                'role' => $role
            ]);

            return true;
        } catch (\Exception $e) {
            $this->logger->error('Erreur lors de l\'envoi de l\'email de bienvenue', [
                'recipient' => $recipientEmail,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Envoie une notification de promotion en RESPO_CABINET
     */
    public function sendPromotionEmail(
        string $recipientEmail,
        string $recipientName,
        string $cabinetName
    ): bool {
        try {
            $email = (new TemplatedEmail())
                ->from(new Address($this->fromEmail, $this->fromName))
                ->to(new Address($recipientEmail, $recipientName))
                ->subject('Vous avez été désigné comme Responsable de Cabinet')
                ->htmlTemplate('emails/promotion.html.twig')
                ->context([
                    'recipientName' => $recipientName,
                    'cabinetName' => $cabinetName,
                    'loginUrl' => 'https://admin.cncj-ci.ci/login',
                ]);

            $this->mailer->send($email);
            $this->logger->info('Email de promotion envoyé', [
                'recipient' => $recipientEmail,
                'cabinet' => $cabinetName
            ]);

            return true;
        } catch (\Exception $e) {
            $this->logger->error('Erreur lors de l\'envoi de l\'email de promotion', [
                'recipient' => $recipientEmail,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }
}
