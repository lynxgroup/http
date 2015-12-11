<?php namespace LynxGroup\Component\Http;

use Psr\Http\Message\MessageInterface;

use Psr\Http\Message\StreamInterface;

use InvalidArgumentException;

abstract class Message implements MessageInterface
{
	protected $headers = [];

	protected $version = '1.1';

	protected $body;

	public function __construct(
		array $headers = [],
		$body = null,
		$version = '1.1',
		$reasonPhrase = null
	)
	{
		foreach( $headers as $name => $value )
		{
			$this->headers[$name] = is_array($value) ? $value : [$value];
		}

		if( !is_string($body) && !is_resource($body) && !$body instanceof StreamInterface)
		{
			throw new InvalidArgumentException();
		}

		if( $body instanceof StreamInterface)
		{
			$this->body = $body;
		}
		else if( $body )
		{
			$this->body = new Stream($body);
		}

		$this->version = $version;

		$this->reasonPhrase = $reasonPhrase;
	}

	public function __clone()
	{
		$this->body = clone $this->body;
	}

	public function getProtocolVersion()
	{
		return $this->version;
	}

	public function withProtocolVersion($version)
	{
		if( !in_array($version, ['1.0', '1.1']) )
		{
			throw new InvalidArgumentException();
		}

		$clone = clone $this;

		$clone->version = $version;

		return $clone;
	}

	public function getHeaders()
	{
		return $this->headers;
	}

	public function hasHeader($name)
	{
		foreach( $this->headers as $_name => $value )
		{
			if( strtolower($name) === strtolower($_name) )
			{
				return true;
			}
		}

		return false;
	}

	public function getHeader($name)
	{
		foreach( $this->headers as $_name => $value )
		{
			if( strtolower($name) === strtolower($_name) )
			{
				return $value;
			}
		}

		return [];
	}

	public function getHeaderLine($name)
	{
		return implode(', ', $this->getHeader($header));
	}

	public function withHeader($name, $value)
	{
		$clone = clone $this;

		foreach( $clone->headers as $_name => $value )
		{
			if( strtolower($name) === strtolower($_name) )
			{
				unset($clone->headers[$_name]);
			}
		}

		$clone->headers[$name] = is_array($value) ? $value : [$value];

		return $clone;
	}

	public function withAddedHeader($name, $value)
	{
		if( !$this->hasHeader($name) )
		{
			return $this->withHeader($name, $value);
		}

		$value = array_merge( $this->getHeader($name), $value );

		$clone = clone $this;

		foreach( $clone->headers as $_name => $value )
		{
			if( strtolower($name) === strtolower($_name) )
			{
				unset($clone->headers[$_name]);
			}
		}

		$clone->headers[$name] = is_array($value) ? $value : [$value];

		return $clone;
	}

	public function withoutHeader($name)
	{
		$clone = clone $this;

		foreach( $clone->headers as $_name => $_value )
		{
			if( strtolower($name) === strtolower($_name) )
			{
				unset($clone->headers[$_name]);
			}
		}

		return $clone;
	}

	public function getBody()
	{
		return $this->body;
	}

	public function withBody(StreamInterface $body)
	{
		$clone = clone $this;

		$this->body = $body;

		return $clone;
	}
}
