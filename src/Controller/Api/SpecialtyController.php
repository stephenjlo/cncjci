<?php
namespace App\Controller\Api;
use App\Repository\SpecialtyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;

#[Route('/api/specialties')]
class SpecialtyController extends AbstractController {
  #[Route('', name:'api_specialties', methods:['GET'])]
  public function list(SpecialtyRepository $repo): Response {
    $items = array_map(fn($s)=>['id'=>$s->getId(),'name'=>$s->getName(),'slug'=>$s->getSlug()], $repo->findBy([], ['name'=>'ASC']));
    return $this->json($items);
  }
}
