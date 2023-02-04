<?php

namespace OAuth\OAuth2\Service;

use OAuth\OAuth2\Token\StdOAuth2Token;
use OAuth\Common\Http\Exception\TokenResponseException;
use OAuth\Common\Http\Uri\Uri;
use OAuth\Common\Consumer\CredentialsInterface;
use OAuth\Common\Http\Client\ClientInterface;
use OAuth\Common\Storage\TokenStorageInterface;
use OAuth\Common\Http\Uri\UriInterface;

class Todoist extends AbstractService
{
    /*
     * Defined scopes, see https://developer.todoist.com/#oauth for definitions.
     */

    /**
     * Grants permission to add tasks to the Inbox project (The application cannot read tasks data).
     */
    const SCOPE_TASK_ADD = 'task:add';

    /**
     * Grants read-only access to application data, including tasks, projects, labels, and filters.
     */
    const SCOPE_DATA_READ = 'data:read';

    /**
     * Grants read and write access to application data, including tasks, projects, labels, and filters.
     *
     * This scope includes task:add and data:read scopes.
     */
    const SCOPE_DATA_READ_WRITE = 'data:read_write';

    /**
     * Grants permission to delete application data, including tasks, labels, and filters.
     */
    const SCOPE_DATA_DELETE = 'data:delete';

    /**
     * Grants permission to delete projects.
     */
    const SCOPE_PROJECT_DELETE = 'project:delete';


    public function __construct(
        CredentialsInterface $credentials,
        ClientInterface $httpClient,
        TokenStorageInterface $storage,
        $scopes = array(),
        UriInterface $baseApiUri = null
    )
    {
        parent::__construct($credentials, $httpClient, $storage, $scopes, $baseApiUri);

        if (null === $baseApiUri) {
            $this->baseApiUri = new Uri('https://api.todoist.com/sync/v9/');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthorizationEndpoint()
    {
        return new Uri('https://todoist.com/oauth/authorize');
    }

    /**
     * {@inheritdoc}
     */
    public function getAccessTokenEndpoint()
    {
        return new Uri('https://todoist.com/oauth/access_token');
    }

    /**
     * {@inheritdoc}
     */
    protected function getAuthorizationMethod()
    {
        return static::AUTHORIZATION_METHOD_HEADER_BEARER;
    }

    /**
     * {@inheritdoc}
     */
    protected function parseAccessTokenResponse($responseBody)
    {
        $data = json_decode($responseBody, true);

        if (null === $data || !is_array($data)) {
            throw new TokenResponseException('Unable to parse response.');
        } elseif (isset($data['error'])) {
            throw new TokenResponseException('Error in retrieving token: "' . $data['error'] . '"');
        }

        $token = new StdOAuth2Token();
        $token->setAccessToken($data['access_token']);
        // Todoist tokens evidently never expire...
        $token->setEndOfLife(StdOAuth2Token::EOL_NEVER_EXPIRES);
        unset($data['access_token']);

        $token->setExtraParams($data);

        return $token;
    }

    /**
     * {@inheritdoc}
     */
    protected function getScopesDelimiter()
    {
        return ',';
    }
}
