<?php namespace LynxGroup\Component\Http;

use Psr\Http\Message\RequestInterface;

use Psr\Http\Message\UriInterface;

use InvalidArgumentException;

class Request extends Message implements RequestInterface
{
	protected $method;

	protected $requestTarget;

	protected $uri;

	public function __construct(
		$method,
		UriInterface $uri,
		array $headers = [],
		$body = null,
		$version = '1.1',
		$reasonPhrase = null
	)
	{
		parent::__construct(
			$headers,
			$body,
			$version,
			$reasonPhrase
		);

		$this->method = strtoupper($method);

		$this->uri = $uri;

		$host = $uri->getHost();

		if( $host && !$this->hasHeader('Host') )
		{
			if( $port = $uri->getPort() )
			{
				$host .= ':' . $port;
			}

			$this->headers['Host'] = $host;
		}
	}

    public function __clone()
    {
        $this->attributes = clone $this->attributes;

		parent::__clone();
    }

	public function getRequestTarget()
	{
		if( $this->requestTarget !== null )
		{
			return $this->requestTarget;
		}

		$target = $this->uri->getPath();

		if( $target == null )
		{
			$target = '/';
		}

		if( $this->uri->getQuery() )
		{
			$target .= '?' . $this->uri->getQuery();
		}

		return $target;
	}

	public function withRequestTarget($requestTarget)
	{
		$clone = clone $this;

		$clone->requestTarget = $requestTarget;

		return $clone;
	}

	public function getMethod()
	{
		return $this->method;
	}

	public function withMethod($method)
	{
		if( !in_array(strtoupper($method), ['GET', 'POST', 'PUT', 'DELETE', 'HEAD', 'PATCH', 'OPTIONS']) )
		{
			throw new InvalidArgumentException();
		}

		$clone = clone $this;

		$clone->method = $method;

		return $clone;
	}

	public function getUri()
	{
		return $this->uri;
	}

	public function withUri(UriInterface $uri, $preserveHost = false)
	{
		$clone = clone $this;

		$clone->uri = $uri;

		if( !$preserveHost )
		{
			if( $host = $uri->getHost() )
			{
				if( $port = $uri->getPort() )
				{
					$host .= ':' . $port;
				}

				return $clone->widthHeader('host', $host);
			}
		}

		return $clone;
	}
}
