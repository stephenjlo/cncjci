<?php
namespace App\Controller\Admin;

use App\Entity\Specialty;
use App\Repository\SpecialtyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/specialties')]
#[IsGranted('ROLE_SUPER_ADMIN')]
class SpecialtyAdminController extends AbstractController
{
    public function __construct(
        private SpecialtyRepository $repository,
        private EntityManagerInterface $em,
        private SluggerInterface $slugger
    ) {}

    #[Route('', name: 'admin_specialty_index')]
    public function index(): Response
    {
        $specialties = $this->repository->findBy([], ['name' => 'ASC']);

        return $this->render('admin/specialty/index.html.twig', [
            'specialties' => $specialties,
        ]);
    }

    #[Route('/new', name: 'admin_specialty_new', methods: ['POST'])]
    public function new(Request $request): Response
    {
        $name = $request->request->get('name');
        $description = $request->request->get('description');

        if (!$name) {
            $this->addFlash('error', 'Le nom est obligatoire');
            return $this->redirectToRoute('admin_specialty_index');
        }

        $specialty = new Specialty();
        $specialty->setName($name);
        $specialty->setSlug(strtolower($this->slugger->slug($name)));
        $specialty->setDescription($description);

        $this->em->persist($specialty);
        $this->em->flush();

        $this->addFlash('success', 'Spécialité créée avec succès');
        return $this->redirectToRoute('admin_specialty_index');
    }

    #[Route('/{id}/edit', name: 'admin_specialty_edit', methods: ['POST'])]
    public function edit(Specialty $specialty, Request $request): Response
    {
        $name = $request->request->get('name');
        $description = $request->request->get('description');

        if (!$name) {
            $this->addFlash('error', 'Le nom est obligatoire');
            return $this->redirectToRoute('admin_specialty_index');
        }

        $specialty->setName($name);
        $specialty->setSlug(strtolower($this->slugger->slug($name)));
        $specialty->setDescription($description);

        $this->em->flush();

        $this->addFlash('success', 'Spécialité modifiée avec succès');
        return $this->redirectToRoute('admin_specialty_index');
    }

    #[Route('/{id}/delete', name: 'admin_specialty_delete', methods: ['POST'])]
    public function delete(Specialty $specialty): Response
    {
        try {
            $name = $specialty->getName();
            $this->em->remove($specialty);
            $this->em->flush();

            $this->addFlash('success', sprintf('Spécialité "%s" supprimée avec succès', $name));
        } catch (\Exception $e) {
            $this->addFlash('error', 'Impossible de supprimer cette spécialité (elle est peut-être utilisée par des conseils juridiques)');
        }

        return $this->redirectToRoute('admin_specialty_index');
    }
}
