<?php

namespace rp\action;

use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use rp\system\cache\runtime\CharacterProfileRuntimeCache;
use wcf\http\Helper;
use wcf\system\WCF;

/**
 * Provides the popover content for a character.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */
final class CharacterPopoverAction implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $parameters = Helper::mapQueryParameters(
            $request->getQueryParams(),
            <<<'EOT'
                array {
                    id: positive-int
                }
                EOT,
        );

        $character = CharacterProfileRuntimeCache::getInstance()->getObject($parameters['id']);
        if (!$character) {
            return new EmptyResponse();
        }

        return new HtmlResponse(
            WCF::getTPL()->render('rp', 'characterCard', ['character' => $character])
        );
    }
}
