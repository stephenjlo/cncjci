<?php
namespace App\Controller\Admin;

use App\Entity\News;
use App\Form\NewsType;
use App\Repository\NewsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/news')]
#[IsGranted('ROLE_SUPER_ADMIN')]
class NewsAdminController extends AbstractController
{
    public function __construct(
        private NewsRepository $repository,
        private EntityManagerInterface $em
    ) {}

    #[Route('', name: 'admin_news_index')]
    public function index(): Response
    {
        $newsList = $this->repository->findBy([], ['createdAt' => 'DESC']);

        return $this->render('admin/news/index.html.twig', [
            'newsList' => $newsList,
        ]);
    }

    #[Route('/new', name: 'admin_news_new')]
    public function new(Request $request): Response
    {
        $news = new News();
        $form = $this->createForm(NewsType::class, $news);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($news);
            $this->em->flush();

            $this->addFlash('success', 'News créée avec succès');
            return $this->redirectToRoute('admin_news_index');
        }

        return $this->render('admin/news/form.html.twig', [
            'form' => $form,
            'news' => $news,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_news_edit')]
    public function edit(News $news, Request $request): Response
    {
        $form = $this->createForm(NewsType::class, $news);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $news->setUpdatedAt(new \DateTime());
            $this->em->flush();

            $this->addFlash('success', 'News modifiée avec succès');
            return $this->redirectToRoute('admin_news_index');
        }

        return $this->render('admin/news/form.html.twig', [
            'form' => $form,
            'news' => $news,
        ]);
    }

    #[Route('/{id}/toggle', name: 'admin_news_toggle', methods: ['POST'])]
    public function toggle(News $news): Response
    {
        $news->setIsActive(!$news->isActive());
        $news->setUpdatedAt(new \DateTime());
        $this->em->flush();

        $status = $news->isActive() ? 'activée' : 'désactivée';
        $this->addFlash('success', "News {$status} avec succès");

        return $this->redirectToRoute('admin_news_index');
    }

    #[Route('/{id}/delete', name: 'admin_news_delete', methods: ['POST'])]
    public function delete(News $news): Response
    {
        $this->em->remove($news);
        $this->em->flush();

        $this->addFlash('success', 'News supprimée avec succès');

        return $this->redirectToRoute('admin_news_index');
    }
}
