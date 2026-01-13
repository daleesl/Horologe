<?php
require_once __DIR__ . '/ProductRepository.php';

class ProductService
{
    private ProductRepository $repository;

    public function __construct(ProductRepository $repository)
    {
        $this->repository = $repository;
    }

    
    public function getFeaturedProducts(int $limit = 4): array
    {
        return $this->repository->getFeatured($limit);
    }

    
    public function getAllProducts(): array
    {
        return $this->repository->getAll();
    }

    
    public function getProduct(string $id): ?array
    {
        return $this->repository->getById($id);
    }

    
    public function getRelatedProducts(string $category, string $excludeId, int $limit = 4): array
    {
        return $this->repository->getRelated($category, $excludeId, $limit);
    }
}
