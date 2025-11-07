<?php
namespace App\Controller\Admin;

use App\Entity\CabinetType;
use App\Repository\CabinetTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/cabinet-types')]
#[IsGranted('ROLE_SUPER_ADMIN')]
class CabinetTypeAdminController extends AbstractController
{
    public function __construct(
        private CabinetTypeRepository $repository,
        private EntityManagerInterface $em,
        private SluggerInterface $slugger
    ) {}

    #[Route('', name: 'admin_cabinet_type_index')]
    public function index(): Response
    {
        $types = $this->repository->findBy([], ['name' => 'ASC']);

        return $this->render('admin/cabinet_type/index.html.twig', [
            'types' => $types,
        ]);
    }

    #[Route('/new', name: 'admin_cabinet_type_new', methods: ['POST'])]
    public function new(Request $request): Response
    {
        $name = $request->request->get('name');

        if (!$name) {
            $this->addFlash('error', 'Le nom est obligatoire');
            return $this->redirectToRoute('admin_cabinet_type_index');
        }

        $type = new CabinetType();
        $type->setName($name);
        $type->setSlug(strtolower($this->slugger->slug($name)));

        $this->em->persist($type);
        $this->em->flush();

        $this->addFlash('success', 'Type de cabinet créé avec succès');
        return $this->redirectToRoute('admin_cabinet_type_index');
    }

    #[Route('/{id}/delete', name: 'admin_cabinet_type_delete', methods: ['POST'])]
    public function delete(CabinetType $type): Response
    {
        $this->em->remove($type);
        $this->em->flush();

        $this->addFlash('success', 'Type de cabinet supprimé avec succès');
        return $this->redirectToRoute('admin_cabinet_type_index');
    }
}
