<?php

declare(strict_types=1);

namespace App\Application\Command\Post;

use App\Application\Exception\PostNotFoundException;
use App\Common\Domain\Projector;
use App\Domain\Model\Post\DoctrinePostRepository;
use App\Domain\Model\Post\Post;
use App\Domain\Model\Post\PostWasDeletedProjection;
use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class DeletePostCommandHandler implements MessageHandlerInterface
{
    private DoctrinePostRepository $repository;
    private Projector $projector;
    private Client $client;

    public function __construct(
        DoctrinePostRepository $repository,
        Projector $projector,
        ClientBuilder $clientBuilder
    ) {
        $this->repository = $repository;
        $this->projector = $projector;
        $this->client = $clientBuilder->build();
        $this->registerProjection();
    }

    /**
     * @throws PostNotFoundException
     */
    public function __invoke(DeletePostCommand $command): void
    {
        $post = $this->repository->byId($command->getId());
        if ($post) {
            $this->repository->delete(Post::deletePost($post));

            return;
        }
        throw new PostNotFoundException($command->getId());
    }

    private function registerProjection(): void
    {
        $this->projector->register([new PostWasDeletedProjection($this->client)]);
    }
}
