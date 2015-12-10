<?php namespace LynxGroup\Component\Http;

use LynxGroup\Interfaces\Http\ServerRequestInterface;

use LynxGroup\Component\Http\Request;

use LynxGroup\Component\Http\Uri;

use InvalidArgumentException;

class ServerRequest extends Request implements ServerRequestInterface
{
	protected $serverParams = [];

	protected $cookiesParams = [];

	protected $queryParams = [];

	protected $uploadedFiles = [];

	protected $parsedBody = [];

	protected $attributes = [];

	public function __construct(
		$serverParams = [],
		$cookiesParams = [],
		$queryParams = [],
		$uploadedFiles = [],
		$parsedBody = [],
		$attributes = [],
		$headers = []
	)
	{
		$this->serverParams = $serverParams;

		$this->cookiesParams = $cookiesParams;

		$this->queryParams = $queryParams;

		$this->uploadedFiles = $uploadedFiles;

		$this->parsedBody = $parsedBody;

		$this->attributes = $attributes;

		parent::__construct(
			isset($this->serverParams['REQUEST_METHOD']) ? $this->serverParams['REQUEST_METHOD'] : 'GET',
			new Uri(
				isset($this->serverParams['REQUEST_SCHEME']) ? $this->serverParams['REQUEST_SCHEME'] : 'http',
				isset($this->serverParams['HTTP_HOST']) ? $this->serverParams['HTTP_HOST'] : 'http',
				isset($this->serverParams['SERVER_PORT']) ? $this->serverParams['SERVER_PORT'] : 80,
				isset($this->serverParams['PHP_AUTH_USER']) ? $this->serverParams['PHP_AUTH_USER'] : '',
				isset($this->serverParams['PHP_AUTH_PW']) ? $this->serverParams['PHP_AUTH_PW'] : '',
				parse_url(isset($this->serverParams['REQUEST_URI']) ? $this->serverParams['REQUEST_URI'] : '/', PHP_URL_PATH),
				isset($this->serverParams['QUERY_STRING']) ? $this->serverParams['QUERY_STRING'] : ''
			),
			$headers,
			'php://input'
		);
	}

	public function getServerParams()
	{
		return $this->serverParams;
	}

	public function getCookieParams()
	{
		return $this->cookiesParams;
	}

	public function withCookieParams(array $cookies)
	{
		$clone = clone $this;

		$clone->cookiesParams = $cookies;

		return $clone;
	}

	public function getQueryParams()
	{
		return $this->queryParams;
	}

	public function withQueryParams(array $query)
	{
		$clone = clone $this;

		$clone->queryParams = $query;

		return $clone;
	}

	public function getUploadedFiles()
	{
		return $this->uploadedFiles;
	}

	public function withUploadedFiles(array $uploadedFiles)
	{
		$clone = clone $this;

		$clone->uploadedFiles = $uploadedFiles;

		return $clone;
	}

	public function getParsedBody()
	{
		return $this->parsedBody;
	}

	public function withParsedBody($data)
	{
		$clone = clone $this;

		$clone->parsedBody = $data;

		return $clone;
	}

	public function getAttributes()
	{
		return $this->attributes;
	}

	public function getAttribute($name, $default = null)
	{
		return isset($this->attributes[$name]) ? $this->attributes[$name] : null;
	}

	public function withAttribute($name, $value)
	{
		$clone = clone $this;

		$clone->attributes[$name] = $value;

		return $clone;
	}

	public function withoutAttribute($name)
	{
		$clone = clone $this;

		if( isset($this->attributes[$name]) )
		{
			unset($clone->attributes[$name]);
		}

		return $clone;
	}
}
