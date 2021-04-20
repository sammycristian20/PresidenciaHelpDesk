<?php


namespace App\Plugins\AzureActiveDirectory\Controllers;

use App\Plugins\AzureActiveDirectory\Microsoft\Exception\GraphException;
use App\Plugins\AzureActiveDirectory\Model\AzureAd;
use App\Plugins\AzureActiveDirectory\Microsoft\Graph;
use App\Plugins\AzureActiveDirectory\Microsoft\Model\Event;

/**
 * Contains all logics specific to microsoft azure active directory.
 * @internal there must not be even a single logic specific to "microsoft azure active directory" outside this class,
 * same class makes developers life easier to migrate to newer versions of APIs or accomodating any third party changes
 * Class AzureConnector
 * @package App\Plugins\AzureActiveDirectory\Controllers
 */
class AzureConnector
{

    /**
     * @var AzureAd
     */
    private $azureAdSettings;

    public function setAzureAdSettings(AzureAd $azureAd)
    {
        $this->azureAdSettings = $azureAd;
    }

    /**
     * Gets access code in client credentials mode (2-legged authentication without resource owner's involvement)
     * @see https://docs.microsoft.com/en-us/azure/active-directory/develop/v2-oauth2-client-creds-grant-flow
     * @return mixed
     */
    public function getAccessCodeInClientCredentialsMode()
    {
        $client = new \GuzzleHttp\Client();

        $body = [
            'client_id'=> $this->azureAdSettings->app_id,
            'scope'=> $this->getOauthScopes(),
            'client_secret'=> $this->azureAdSettings->app_secret,
            'grant_type'=> 'client_credentials'
        ];

        $request = $client->post(
            "https://login.microsoftonline.com/".$this->azureAdSettings->tenant_id."/oauth2/v2.0/token",
            ['form_params'=> $body]
        );

        // can store this access token and use it next time, else regenerate every time

        return json_decode($request->getBody()->getContents())->access_token;
    }

    /**
     * Gets access code in Auth code mode (3-legged authentication with App, resource owner and identity providers involvement)
     * @see https://docs.microsoft.com/en-us/linkedin/shared/authentication/authorization-code-flow
     * @param string $authCode
     * @return \League\OAuth2\Client\Token\AccessToken|\League\OAuth2\Client\Token\AccessTokenInterface
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function getAccessCodeInAuthorizationCodeMode(string $authCode)
    {
        $oauthClient = $this->getAzureProvider();

        return $oauthClient->getAccessToken('authorization_code', [
          'code' => $authCode
        ])->getToken();
    }

    /**
     * Gives a redirect to azure ad identity platform with required parameters
     * @return \Illuminate\Http\RedirectResponse
     */
    public function authenticate()
    {
        $oauthClient = $this->getAzureProvider();

        $authUrl = $oauthClient->getAuthorizationUrl();

        // Save client state so we can validate in callback
        session(['oauthState' => $oauthClient->getState(), 'azureAdId'=> $this->azureAdSettings->id]);

        // Redirect to AAD signin page
        return redirect()->away($authUrl);
    }

    /**
     * Gets list of users in the AD
     * @param $accessToken
     * @throws GraphException
     */
    public function getUsers($accessToken)
    {
        $graph = new Graph();
        $graph->setAccessToken($accessToken);

        /*
         * Microsoft Graph APIs returns limited records by default and it has a paging implementation to
         * get the rest of the records. As long as `getNextLink` is giving the next url, we will keep fetching
         * as soon as next page comes as null, it will terminate
         * @see https://docs.microsoft.com/en-us/graph/paging
         * @see https://github.com/ladybirdweb/faveo-helpdesk-advance/issues/7912
         */
        $nextPageUrl = '/users?$top=500';
        $users = [];
        while ($nextPageUrl) {
            $response = $graph->createCollectionRequest('GET', $nextPageUrl)
                ->execute();
            $nextPageUrl = $response->getNextLink();
            $body = $response->getBody();
            isset($body['value']) && $users = array_merge($users, $body['value']);
        }
        return $users;
    }

    /**
     * Gets logged in users data
     * @param $accessToken
     * @throws GraphException
     */
    public function getMyData($accessToken)
    {
        $graph = new Graph();
        $graph->setAccessToken($accessToken);

        // Append query parameters to the '/me/events' url
        $getEventsUrl = '/me';

        return $graph->createRequest('GET', $getEventsUrl)
            ->setReturnType(Event::class)
            ->execute()->getProperties();
    }

    /**
     * Gets azure Ad instance
     * @return \League\OAuth2\Client\Provider\GenericProvider
     */
    private function getAzureProvider()
    {
         return new \League\OAuth2\Client\Provider\GenericProvider([
            'clientId'                => $this->azureAdSettings->app_id,
            'clientSecret'            => $this->azureAdSettings->app_secret,
            'redirectUri'             => $this->getRedirectUri(),
            'urlAuthorize'            => $this->getOauthAuthority().$this->getOauthAuthorizeEndpoint(),
            'urlAccessToken'          => $this->getOauthAuthority().$this->getOauthTokenEndpoint(),
            'urlResourceOwnerDetails' => '',
            'scopes'                  => $this->getOauthScopes()
         ]);
    }

    private function createUsers()
    {
        // how to set mapping
    }

    private function getOauthAuthority()
    {
        return 'https://login.microsoftonline.com/'. $this->azureAdSettings->tenant_id;
    }

    private function getOauthAuthorizeEndpoint()
    {
        return '/oauth2/v2.0/authorize';
    }

    private function getOauthTokenEndpoint()
    {
        return '/oauth2/v2.0/token';
    }

    private function getOauthScopes()
    {
        return 'https://graph.microsoft.com/.default';
    }

    private function getRedirectUri()
    {
        return config('app.url').'/azure-active-directory/auth-token/callback';
    }
}
