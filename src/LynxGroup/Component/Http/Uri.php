<?php namespace LynxGroup\Component\Http;

use Psr\Http\Message\UriInterface;

use InvalidArgumentException;

class Uri implements UriInterface
{
	protected $scheme;

	protected $username;

	protected $password;

	protected $host;

	protected $port;

	protected $path;

	protected $query;

	protected $fragment;

	public function __construct(
		$scheme = '',
		$username = '',
		$password = '',
		$host = 'localhost',
		$port = 80,
		$path = '',
		$query = '', 
		$fragment = ''
	)
	{
		$this->scheme = $scheme;

		$this->username = $username;

		$this->password = $password;

		$this->host = $host;

		$this->port = $port;

		$this->path = $path;

		$this->query = $query;

		$this->fragment = $fragment;
	}

	public function getScheme()
	{
		return strtolower($this->scheme);
	}

	public function getAuthority()
	{
		$userInfo = $this->getUserInfo();
		$host = $this->getHost();
		$port = $this->getPort();

		return ($userInfo ? $userInfo . '@' : '') . $host . ($port !== null ? ':' . $port : '');
	}

	public function getUserInfo()
	{
		return empty($this->password) ? $this->username : "{$this->username}:{$this->password}";
	}

	public function getHost()
	{
		return strtolower($this->host);
	}

	public function getPort()
	{
		$port = $this->port;

		$scheme = $this->getScheme();

		return ($scheme === 'http' && $port === 80) || ($scheme === 'https' && $port === 443) ? null : $port;
	}

	public function getPath()
	{
		return preg_replace_callback(
			'/(?:[^a-zA-Z0-9_\-\.~:@&=\+\$,\/;%]+|%(?![A-Fa-f0-9]{2}))/',
			function ($match) {
				return rawurlencode($match[0]);
			},
			$this->path
		);
	}

	public function getQuery()
	{
		return preg_replace_callback(
			'/(?:[^a-zA-Z0-9_\-\.~!\$&\'\(\)\*\+,;=%:@\/\?]+|%(?![A-Fa-f0-9]{2}))/',
			function ($match) {
				return rawurlencode($match[0]);
			},
			$this->query
		);
	}

	public function getFragment()
	{
		return preg_replace_callback(
			'/(?:[^a-zA-Z0-9_\-\.~!\$&\'\(\)\*\+,;=%:@\/\?]+|%(?![A-Fa-f0-9]{2}))/',
			function ($match) {
				return rawurlencode($match[0]);
			},
			$this->fragment
		);
	}

	public function withScheme($scheme)
	{
		if( !in_array(strtolower($scheme), ['', 'http', 'https']) )
		{
			throw new InvalidArgumentException();
		}

		$clone = clone $this;

		$clone->scheme = $scheme;

		return $clone;
	}

	public function withUserInfo($user, $password = null)
	{
		if( !is_string($user) || ($password !== null && !is_string($password)) )
		{
			throw new InvalidArgumentException();
		}

		$clone = clone $this;

		$clone->user = $user;

		$clone->password = $password;

		return $clone;
	}

	public function withHost($host)
	{
		if( !is_string($user) )
		{
			throw new InvalidArgumentException();
		}

		$clone = clone $this;

		$clone->host = $host;

		return $clone;
	}

	public function withPort($port)
	{
		if( $port !== null && ($port < 0 || $port > 65535) )
		{
			throw new InvalidArgumentException();
		}
		
		$clone = clone $this;

		$clone->port = (int)$port;

		return $clone;
	}

	public function withPath($path)
	{
		if( !is_string($path) )
		{
			throw new InvalidArgumentException();
		}

		$clone = clone $this;

		$clone->path = $path;

		return $clone;
	}

	public function withQuery($query)
	{
		if( !is_string($query) )
		{
			throw new InvalidArgumentException();
		}

		$clone = clone $this;

		$clone->query = $query;

		return $clone;
	}

	public function withFragment($fragment)
	{
		if( !is_string($fragment) )
		{
			throw new InvalidArgumentException();
		}

		$clone = clone $this;

		$clone->fragment = $fragment;

		return $clone;
	}

	public function __toString()
	{
		return
			(!empty($this->getScheme()) ? $this->getScheme() . '://' : '').
			$this->getAuthority().
			$this->getPath().
			(!empty($this->getQuery()) ? '?' . $this->getQuery() : '').
			(!empty($this->getFragment()) ? '#' . $this->getFragment() : '');
	}
}
