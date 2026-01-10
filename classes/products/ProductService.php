<?php
require_once __DIR__ . '/ProductRepository.php';

// Application-facing service for product retrieval.
class ProductService
{
    private ProductRepository $repository;

    public function __construct(ProductRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get products for featured section (defaults to 4 items).
     */
    public function getFeaturedProducts(int $limit = 4): array
    {
        return $this->repository->getFeatured($limit);
    }

    /**
     * Get all products for collections.
     */
    public function getAllProducts(): array
    {
        return $this->repository->getAll();
    }

    /**
     * Get one product by id.
     */
    public function getProduct(string $id): ?array
    {
        return $this->repository->getById($id);
    }

    /**
     * Get related products (same category, excluding the current id).
     */
    public function getRelatedProducts(string $category, string $excludeId, int $limit = 4): array
    {
        return $this->repository->getRelated($category, $excludeId, $limit);
    }
}
