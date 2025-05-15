<?php

namespace wcf\system\endpoint\controller\rp\items;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use rp\system\item\ItemHandler;
use rp\util\RPUtil;
use wcf\http\Helper;
use wcf\system\endpoint\GetRequest;
use wcf\system\endpoint\IController;
use wcf\system\exception\SystemException;

/**
 * API endpoint for search items for item.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
#[GetRequest('/rp/items/search')]
final class SearchItem implements IController
{
    #[\Override]
    public function __invoke(ServerRequestInterface $request, array $variables): ResponseInterface
    {
        $parameters = Helper::mapApiParameters($request, SearchItemParameters::class);

        $item = ItemHandler::getInstance()->getSearchItem(
            $parameters->itemName,
            0,
            false,
            $parameters->additionalData
        );

        if ($item === null) {
            throw new SystemException('Item not found.');
        }

        return new JsonResponse([
            'itemID' => $item->getObjectID(),
            'itemName' => $item->getTitle(),
        ]);
    }
}

/** @internal */
final class SearchItemParameters
{
    public function __construct(
        /** @var non-empty-string **/
        public readonly string $itemName,

        public readonly string $additionalData,
    ) {}
}
