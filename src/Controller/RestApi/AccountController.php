<?php

declare(strict_types=1);

namespace App\Controller\RestApi;

use App\Transformer\AccountTransformer;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    private Manager $manager;

    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @Route("/api/account/current", name="api_account_current")
     * @return JsonResponse
     */
    public function getOne(): JsonResponse
    {
        $item = new Item($this->getUser(), new AccountTransformer());
        return new JsonResponse($this->manager->createData($item)->toArray());
    }
}
