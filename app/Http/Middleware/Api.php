<?php
/**
 * Validator class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Http\Middleware;

use App\Http\Request;
use App\Http\Response;

class Api
{
    /**
     * Method responsible for running the middleware
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle($request, $next)
    {
        $request->getRouter()->setContentType('application/json');

        // Runs the next level of middleware
        return $next($request);
    }
}
