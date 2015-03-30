<?php

require __dir__ . '/../3rdparty/autoload.php';
use fkooman\OAuth\ResourceServer\ApisResourceServer;
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
        kill("getCurrentUser called");
        return $this->currentUser;
    }

    public function authenticate(\Sabre\DAV\Server $server, $realm)
    {
        $config = array(
            "introspectionEndpoint" => $this->introspectionEndpoint,
            "realm" => $realm
        );

        try {
            $resourceServer = new ApisResourceServer(
                new Client($this->introspectionEndpoint));
            $requestHeaders = apache_request_headers();
            $authenticationHeader = isset($requestHeaders['Authorization']) ? $requestHeaders['Authorization'] : null;
            $username = OCP\Config::getAppValue('user_oauth', 'username', '');
            $password = OCP\Config::getAppValue('user_oauth', 'password', '');
            $credentials = base64_encode($username . ':' . $password);

            $resourceServer->setAuthenticationHeader('Basic: ' . $credentials);

            //get the query parameter
            $acessTokenQueryParameter = isset($_GET['access_token']) ? $_GET['access_token'] : null;
            $resourceServer->setAccessTokenQueryParameter($acessTokenQueryParameter);
            $tokenIntrospection = $resourceServer->verifyToken();
            $this->currentUser = $this->persistentId2LoginName($tokenIntrospection->getSub());
            if(!OC_User::userExists($this->currentUser)) {
                throw new ResourceServerException("User_doesnt_exist", "User doesn't exist, please log in through shibboleth first");
            }
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

    private function persistentId2LoginName($persistentId) {
        return hash('sha256', $persistentId);
    }
}
