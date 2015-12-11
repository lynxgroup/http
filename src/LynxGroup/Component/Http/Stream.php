<?php namespace LynxGroup\Component\Http;

use Psr\Http\Message\StreamInterface;

use InvalidArgumentException;

use RuntimeException;

class Stream implements StreamInterface
{
	protected $stream;

	protected $resource;

	public function __construct($stream, $mode = 'x+')
	{
		$this->stream = $stream;

		if( is_resource($stream) )
		{
			$this->resource = $stream;
		}
		elseif( is_string($stream) )
		{
			$this->resource = fopen($stream, $mode);
		}
		else
		{
			throw new InvalidArgumentException();
		}
	}

	public function __toString()
	{
		try {
			$this->seek(0);

			return (string)stream_get_contents($this->resource);
		} catch (\Exception $e) {
			return '';
		}
	}

	public function close()
	{
		fclose($stream);
	}

	public function detach()
	{
		$resource = $this->resource;

		$this->resource = null;

		return $resource;
	}

	public function getSize()
	{
		if( null === $this->resource )
		{
			return null;
		}

		$stats = fstat($this->resource);

		return $stats['size'];
	}

	public function tell()
	{
		if( !$this->resource )
		{
			throw new RuntimeException();
		}

		$result = ftell($this->resource);

		if( !is_int($result) )
		{
			throw new RuntimeException();
		}

		return $result;
	}

	public function eof()
	{
		if( !$this->resource )
		{
			return true;
		}

		return feof($this->resource);
	}

	public function isSeekable()
	{
		if( !$this->resource )
		{
			return false;
		}

		$meta = stream_get_meta_data($this->resource);

		return $meta['seekable'];
	}

	public function seek($offset, $whence = SEEK_SET)
	{
		if( !$this->resource)
		{
			throw new RuntimeException();
		}
		
		if( !$this->isSeekable() )
		{
			throw new RuntimeException();
		}

		$result = fseek($this->resource, $offset, $whence);

		if( 0 !== $result )
		{
			throw new RuntimeException('Error seeking within stream');
		}

		return true;
	}

	public function rewind()
	{
		return $this->seek(0);
	}

	public function isWritable()
	{
		if( !$this->resource )
		{
			return false;
		}

		$meta = stream_get_meta_data($this->resource);

		return is_writable($meta['uri']);
	}

	public function write($string)
	{
		if( !$this->resource )
		{
			throw new RuntimeException('No resource available; cannot write');
		}

		$result = fwrite($this->resource, $string);

		if( false === $result )
		{
			throw new RuntimeException('Error writing to stream');
		}

		return $result;
	}

	public function isReadable()
	{
		if( !$this->resource )
		{
			return false;
		}

		$meta = stream_get_meta_data($this->resource);

		$mode = $meta['mode'];

		return (strstr($mode, 'r') || strstr($mode, '+'));
	}

	public function read($length)
	{
		if( !$this->resource )
		{
			throw new RuntimeException();
		}

		if( !$this->isReadable() )
		{
			throw new RuntimeException();
		}

		$result = fread($this->resource, $length);

		if( false === $result )
		{
			throw new RuntimeException();
		}

		return $result;
	}

	public function getContents()
	{
		if( !$this->isReadable() )
		{
			return '';
		}

		$this->seek(0);

		$result = stream_get_contents($this->resource);

		if( false === $result)
		{
			throw new RuntimeException();
		}

		return $result;
	}

	public function getMetadata($key = null)
	{
		if( null === $key )
		{
			return stream_get_meta_data($this->resource);
		}

		$metadata = stream_get_meta_data($this->resource);

		if( !array_key_exists($key, $metadata) )
		{
			return null;
		}

		return $metadata[$key];
	}
}
