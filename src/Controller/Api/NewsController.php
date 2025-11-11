<?php
namespace App\Controller\Api;

use App\Repository\NewsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class NewsController extends AbstractController
{
    public function __construct(
        private NewsRepository $newsRepository
    ) {}

    #[Route('/news', name: 'api_news', methods: ['GET'])]
    public function getAllActive(): JsonResponse
    {
        $news = $this->newsRepository->findAllActive();

        $data = array_map(function($item) {
            return [
                'id' => $item->getId(),
                'title' => $item->getTitle(),
                'description' => $item->getDescription(),
                'url' => $item->getUrl(),
            ];
        }, $news);

        return $this->json([
            'news' => $data
        ]);
    }
}
