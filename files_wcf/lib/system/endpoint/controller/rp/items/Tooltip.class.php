<?php

namespace wcf\system\endpoint\controller\rp\items;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use rp\data\item\Item;
use wcf\http\Helper;
use wcf\system\endpoint\GetRequest;
use wcf\system\endpoint\IController;
use wcf\system\WCF;

/**
 * API endpoint tooltip for item.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
#[GetRequest('/rp/items/{id:\d+}/tooltip')]
final class Tooltip implements IController
{
    #[\Override]
    public function __invoke(ServerRequestInterface $request, array $variables): ResponseInterface
    {
        $item = Helper::fetchObjectFromRequestParameter($variables['id'], Item::class);

        return new JsonResponse([
            'tooltip' => WCF::getTPL()->render(
                'rp',
                'itemTooltip',
                [
                    'item' => $item,
                ],
                true
            )
        ]);
    }
}
