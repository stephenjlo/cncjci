<?php
namespace App\Controller\Admin;

use App\Entity\Cabinet;
use App\Form\CabinetType;
use App\Repository\CabinetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/cabinets')]
#[IsGranted('ROLE_SUPER_ADMIN')]
class CabinetAdminController extends AbstractController
{
    public function __construct(
        private CabinetRepository $repository,
        private EntityManagerInterface $em
    ) {}

    #[Route('', name: 'admin_cabinet_index')]
    public function index(Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $search = $request->query->get('search', '');

        $qb = $this->repository->createQueryBuilder('c');

        if ($search) {
            $qb->where('c.name LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        $qb->orderBy('c.name', 'ASC')
            ->setMaxResults(20)
            ->setFirstResult(($page - 1) * 20);

        $cabinets = $qb->getQuery()->getResult();

        return $this->render('admin/cabinet/index.html.twig', [
            'cabinets' => $cabinets,
            'search' => $search,
            'page' => $page,
        ]);
    }

    #[Route('/new', name: 'admin_cabinet_new')]
    public function new(Request $request): Response
    {
        $cabinet = new Cabinet();
        $form = $this->createForm(CabinetType::class, $cabinet);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($cabinet);
            $this->em->flush();

            $this->addFlash('success', 'Cabinet créé avec succès');
            return $this->redirectToRoute('admin_cabinet_index');
        }

        return $this->render('admin/cabinet/form.html.twig', [
            'form' => $form,
            'cabinet' => $cabinet,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_cabinet_edit')]
    public function edit(Cabinet $cabinet, Request $request): Response
    {
        $this->denyAccessUnlessGranted('CABINET_EDIT', $cabinet);

        $form = $this->createForm(CabinetType::class, $cabinet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            $this->addFlash('success', 'Cabinet modifié avec succès');
            return $this->redirectToRoute('admin_cabinet_index');
        }

        return $this->render('admin/cabinet/form.html.twig', [
            'form' => $form,
            'cabinet' => $cabinet,
        ]);
    }

    #[Route('/{id}/toggle', name: 'admin_cabinet_toggle', methods: ['POST'])]
    public function toggle(Cabinet $cabinet): Response
    {
        $this->denyAccessUnlessGranted('CABINET_DELETE', $cabinet);

        $cabinet->setIsActive(!$cabinet->isActive());
        $this->em->flush();

        $status = $cabinet->isActive() ? 'activé' : 'désactivé';
        $this->addFlash('success', "Cabinet {$status} avec succès");

        return $this->redirectToRoute('admin_cabinet_index');
    }
}