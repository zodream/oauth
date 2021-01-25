<?php
namespace Zodream\Module\OAuth\Service;

use Zodream\Route\Controller\Controller as BaseController;
use Zodream\Service\Http\Request;

abstract class Controller extends BaseController {

    protected function validateClient() {
        /** @var Request $request */
        $request = $this->httpContext()->make('request');
        list($basicAuthUser, $basicAuthPassword) = $request->basicToken();
        $clientId = $request->get('client_id', $basicAuthUser);
        if (is_null($clientId)) {
            throw new \Exception('client_id');
        }

        // If the client is confidential require the client secret
        $clientSecret = $request->get('client_secret', $basicAuthPassword);

        // If a redirect URI is provided ensure it matches what is pre-registered
        $redirectUri = $request->get('redirect_uri', null);
    }



}