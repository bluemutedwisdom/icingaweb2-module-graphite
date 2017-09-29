<?php

namespace Icinga\Module\Graphite\Graphing;

use Icinga\Web\Url;

/**
 * HTTP interface to Graphite Web
 */
class GraphiteWebClient
{
    /**
     * Base URL of every Graphite Web HTTP request
     *
     * @var Url
     */
    protected $baseUrl;

    /**
     * HTTP basic auth user for every Graphite Web HTTP request
     *
     * @var string|null
     */
    protected $user;

    /**
     * The above user's password
     *
     * @var string|null
     */
    protected $password;

    /**
     * Constructor
     *
     * @param   Url $baseUrl    Base URL of every Graphite Web HTTP request
     */
    public function __construct(Url $baseUrl)
    {
        $this->setBaseUrl($baseUrl);
    }

    /**
     * Send an HTTP request to the configured Graphite Web and return the response's body
     *
     * @param   Url         $url
     * @param   string      $method
     * @param   string[]    $headers
     * @param   string      $body
     *
     * @return  string
     */
    public function request(Url $url, $method = 'GET', array $headers = [], $body = null)
    {
        $httpOptions = ['method' => $method, 'header' => ''];

        $headers['User-Agent'] = 'icingaweb2-module-graphite';
        if ($this->user !== null) {
            $headers['Authorization'] = 'Basic ' . base64_encode("{$this->user}:{$this->password}");
        }

        foreach ($headers as $header => $headerValue) {
            $httpOptions['header'] .= "$header: $headerValue\r\n";
        }

        if ($body !== null) {
            $httpOptions['content'] = $body;
        }

        // TODO(ak): use our CurlClient (one nice day)
        // TODO(ak): keep connections alive (TCP handshakes are a bit expensive and TLS handshakes are very expensive)
        return file_get_contents(
            Url::fromPath(rtrim($this->baseUrl->getAbsoluteUrl(), '/') . '/' . ltrim($url->getPath(), '/'))
                ->setParams($url->getParams())
                ->getAbsoluteUrl(),
            false,
            stream_context_create(['http' => $httpOptions])
        );
    }

    /**
     * Get the base URL of every Graphite Web HTTP request
     *
     * @return Url
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * Set the base URL of every Graphite Web HTTP request
     *
     * @param Url $baseUrl
     *
     * @return $this
     */
    public function setBaseUrl(Url $baseUrl)
    {
        $this->baseUrl = $baseUrl;

        return $this;
    }

    /**
     * Get the HTTP basic auth user
     *
     * @return null|string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set the HTTP basic auth user
     *
     * @param null|string $user
     *
     * @return $this
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get the HTTP basic auth password
     *
     * @return null|string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set the HTTP basic auth password
     *
     * @param null|string $password
     *
     * @return $this
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }
}