<?php

declare(strict_types=1);

namespace App\Port\Adapter\Http\Rest\Controller\Forum;

use App\Application\Exception\ForumNotFoundException;
use App\Application\Query\Forum\ForumQuery;
use App\Application\Query\Forum\ForumQueryHandler;
use App\Common\Application\Representation\Error;
use App\Common\Application\Representation\Errors;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class PostsController.
 *
 * @Route("/forum/{id}", methods={"GET"}, name="fetch_a_forum")
 */
class ForumController
{
    private ForumQueryHandler $handler;
    private SerializerInterface $serializer;

    public function __construct(ForumQueryHandler $handler, SerializerInterface $serializer)
    {
        $this->handler = $handler;
        $this->serializer = $serializer;
    }

    public function __invoke(string $id): Response
    {
        try {
            return JsonResponse::fromJsonString(
                $this->serializer->serialize(
                    $this->handler->byId(new ForumQuery($id)),
                    'json'
                )
            );
        } catch (ForumNotFoundException $exception) {
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
