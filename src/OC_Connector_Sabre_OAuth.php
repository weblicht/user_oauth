<?php

require __dir__ . '/../3rdparty/autoload.php';
use fkooman\OAuth\ResourceServer\ResourceServer;
use fkooman\OAuth\ResourceServer\ResourceServerException;
use Sabre\DAV\Auth\Backend\BackendInterface;

use Guzzle\Http\Client;

class OC_Connector_Sabre_OAuth implements BackendInterface
{
    private $currentUser;
    private $introspectionEndpoint;

    public function __construct($introspectionEndpoint)
    {
        $this->introspectionEndpoint = $introspectionEndpoint;
        $this->currentUser = null;
    }

    public function getCurrentUser()
    {
        return $this->currentUser;
    }

    public function authenticate(\Sabre\DAV\Server $server, $realm)
    {
        $config = array(
            "introspectionEndpoint" => $this->introspectionEndpoint,
            "realm" => $realm
        );

        try {
            $resourceServer = new ResourceServer(
                new Client($this->introspectionEndpoint));
            $requestHeaders = apache_request_headers();
            $authorizationHeader = isset($requestHeaders['Authorization']) ? $requestHeaders['Authorization'] : null;
            $resourceServer->setAuthorizationHeader($authorizationHeader);

            //get the query parameter
            $acessTokenQueryParameter = isset($_GET['access_token']) ? $_GET['access_token'] : null;
            $resourceServer->setAccessTokenQueryParameter($acessTokenQueryParameter);
            $tokenIntrospection = $resourceServer->verifyToken();
            $this->currentUser = $tokenIntrospection->getSub();

            OC_User::setUserid($this->currentUser);
            OC_Util::setupFS($this->currentUser);

            return true;
        } catch (ResourceServerException $e) {
            $e->setRealm("owncloud");
            header("HTTP/1.1" . $e->getStatusCode());
            if (null !== $e->getAuthenticateHeader()) {
                header("WWW-Authenticate: " . $e->getAuthenticateHeader());
            }

            $output = array(
                "error" => $e->getMessage(),
                "code" => $e->getStatusCode(),
                "error_description" => $e->getDescription()
            );
            header("Content-Type: application/json");
            echo json_encode($output);
        } catch (Exception $e) {
            header("Content-Type: application/json");
            echo json_encode($e->getMessage());
        }
    }
}
