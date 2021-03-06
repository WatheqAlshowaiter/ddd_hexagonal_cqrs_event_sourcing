<?php

declare(strict_types=1);

namespace App\Port\Adapter\Http\Rest\Controller\Post;

use App\Application\Exception\PostNotFoundException;
use App\Application\Query\Post\PostQuery;
use App\Application\Query\Post\PostQueryHandler;
use App\Common\Application\Representation\Error;
use App\Common\Application\Representation\Errors;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class PostsController.
 *
 * @Route("/post/{id}", methods={"GET"}, name="fetch_a_post")
 */
class PostController
{
    private PostQueryHandler $handler;
    private SerializerInterface $serializer;

    public function __construct(PostQueryHandler $handler, SerializerInterface $serializer)
    {
        $this->handler = $handler;
        $this->serializer = $serializer;
    }

    public function __invoke(string $id): Response
    {
        try {
            return JsonResponse::fromJsonString(
                $this->serializer->serialize(
                    $this->handler->byId(new PostQuery($id)),
                    'json'
                )
            );
        } catch (PostNotFoundException $exception) {
            return JsonResponse::fromJsonString(
                $this->serializer->serialize(
                    new Errors(
                        [new Error($exception->getMessage(), Response::HTTP_NOT_FOUND)]
                    ),
                    'json'
                ),
                Response::HTTP_NOT_FOUND
            );
        }
    }
}
