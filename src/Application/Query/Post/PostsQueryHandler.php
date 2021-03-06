<?php

declare(strict_types=1);

namespace App\Application\Query\Post;

use App\Application\Representation\Post\AllPosts;
use App\Domain\Model\Post\ElasticSearchPostRepository;

class PostsQueryHandler
{
    private ElasticSearchPostRepository $repository;

    public function __construct(ElasticSearchPostRepository $repository)
    {
        $this->repository = $repository;
    }

    public function all(): AllPosts
    {
        return new AllPosts($this->repository->all());
    }
}
