<?php
namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-user',
    description: 'Créer un nouvel utilisateur',
)]
class CreateUserCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::OPTIONAL, 'Email de l\'utilisateur')
            ->addOption('super-admin', null, InputOption::VALUE_NONE, 'Créer un super admin')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $helper = $this->getHelper('question');

        // Email
        $email = $input->getArgument('email');
        if (!$email) {
            $question = new Question('Email: ');
            $email = $helper->ask($input, $output, $question);
        }

        // Vérifier si l'email existe déjà
        $existingUser = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($existingUser) {
            $io->error('Un utilisateur avec cet email existe déjà!');
            return Command::FAILURE;
        }

        // Mot de passe
        $question = new Question('Mot de passe: ');
        $question->setHidden(true);
        $password = $helper->ask($input, $output, $question);

        // Prénom
        $question = new Question('Prénom: ');
        $firstName = $helper->ask($input, $output, $question);

        // Nom
        $question = new Question('Nom: ');
        $lastName = $helper->ask($input, $output, $question);

        // Rôle
        if ($input->getOption('super-admin')) {
            $role = 'ROLE_SUPER_ADMIN';
        } else {
            $question = new ChoiceQuestion(
                'Rôle (default: ROLE_LAWYER):',
                ['ROLE_LAWYER', 'ROLE_RESPO_CABINET', 'ROLE_SUPER_ADMIN'],
                0
            );
            $role = $helper->ask($input, $output, $question);
        }

        // Créer l'utilisateur
        $user = new User();
        $user->setEmail($email);
        $user->setFirstName($firstName);
        $user->setLastName($lastName);
        $user->setRoles([$role]);

        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);

        $this->em->persist($user);
        $this->em->flush();

        $io->success(sprintf('Utilisateur "%s" créé avec succès! Rôle: %s', $email, $role));

        return Command::SUCCESS;
    }
}