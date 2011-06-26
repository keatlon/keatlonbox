<?php
class slicer
{
	public $name			= 'default';
	public $mode			= 'pager';
	public $count			= 0;
	public $page			= false;
	public $maxPage			= 0;
	public $perPage			= 10;
	public $nextPage		= false;
	public $prevPage		= false;
	public $baseUrl			= false;
	public $nextUrl			= false;
	public $prevUrl			= false;
    public $enableKeys		= false;
	public $bound			= 4;
	public $showPageFrom	= false;
	public $showPageTo		= false;

	protected $list = false;

	protected static $instances = array();

	/**
	 * @param string $url
	 *
	 * @return slicer
	 */
	public function init($url = false)
	{
		if (!$url)
		{
			$this->baseUrl = $_SERVER['REQUEST_URI'];
		}
		else
		{
			$this->baseUrl = $url;
		}
		
		if (request::get('page'))
		{
			$this->page	= request::get('page');
		}
		else
		{
			$this->page		= 1;
		}

	}

	/**
	 *
	 * @param string $name
	 * @return slicer
	 */
	public static function i($url = false, $name = 'default')
	{

		if (!self::$instances[$name])
		{
			self::$instances[$name] = new slicer();
			self::$instances[$name]->init($url);
		}

        self::$instances[$name]->name = $name;
        
        if ($name == 'default')
        {
            self::$instances[$name]->enableKeys = true;
        }

		return self::$instances[$name];
	}

	/**
	 *
	 * @param string $name
	 * @return slicer
	 */
	public static function iterate()
	{
		return self::$instances;
	}

	public static function slice($list, $start, $count)
	{
		if (!$count)
		{
			return array_slice($list, $start);
		}

		return array_slice($list, $start, $count);
	}

	public function build($count = false, $perPage = false, $page = false)
	{
		if ($perPage)
		{
			$this->perPage	= $perPage;
		}

		if ($page)
		{
			$this->page	= $page;
		}

		if (!$this->count && $count)
		{
			$this->count	= $count;
		}

		$this->maxPage	= ceil($this->count / $this->perPage);

		if (!$this->page)
		{
			$this->page	= 1;
		}

		// slice from end
		// $offsetPage = ($this->maxPage - $this->page);

		if ($offsetPage < 0)
		{
			$offsetPage = 0;
		}

		$this->showPageFrom	= $this->page - $this->bound;
		$this->showPageTo	= $this->page + $this->bound;

		$halfBound = floor($this->bound / 2);

		if ($this->showPageFrom < 1)
		{
			$this->showPageFrom	= 1;
			$this->showPageTo	= $this->bound * 2 + 1;
		}

        if ($this->showPageTo > $this->maxPage)
        {
			$this->showPageTo	= $this->maxPage;
			$this->showPageFrom	= $this->maxPage - ($this->bound * 2);
		}

        $this->nextPage = $this->page + 1;
        $this->prevPage = $this->page - 1;
		$this->nextUrl	= $this->addPage($this->baseUrl, $this->nextPage);
		$this->prevUrl	= $this->addPage($this->baseUrl, $this->prevPage);

        if ($this->page <= 1)
        {
            $this->prevPage = false;
        }

        if ($this->page >= $this->maxPage)
        {
            $this->nextPage = false;
        }

        if ($this->showPageTo >= $this->maxPage)
        {
			$this->showPageTo = $this->maxPage;
        }

        if ($this->showPageFrom <= 1)
        {
			$this->showPageFrom = 1;
        }
	}

	public function fetch($list, $perPage = 20, $page = false)
	{
        if (!is_array($list))
        {
            return false;
        }

		$this->list		= $list;
		$this->perPage	= $perPage;

		if ($page)
		{
			$this->page	= $page;
		} 

		$this->count	= count($list);

		$this->build();

		return slicer::slice($list, $this->perPage * ($this->page - 1), $this->perPage);
	}

    protected function addPage($url, $page, $divider = '/', $equal = '/')
    {
		if ($url == '/')
		{
			$url = '';
		}

        $page = (int)$page;

        if (strpos($url, $divider . 'page' . $equal) === false)
        {
            return $url . $divider . 'page' . $equal . $page;
        }

        $currentPage = preg_match('|/page/([0-9]*)|', $url, $matches);
        return str_replace($divider . 'page' . $equal . $matches[1], $divider . 'page' . $equal . $page, $url);
    }

	static function render($slicer)
	{
		if (!$slicer || !$slicer->count)
		{
			return '';
		}

        if ($slicer->maxPage <= 1)
        {
            return '';
        }

        if ($slicer->nextPage)
        {
            $nextPage = '<a class="ctrl" href="' . $slicer->nextUrl . '">&rarr;</a>';
        }
		else
		{
            $nextPage = '<span class="gray">&rarr;</span>';
		}

        if ($slicer->prevPage)
        {
            $prevPage = '<a class="ctrl" href="' . $slicer->prevUrl . '">&larr;</a>';
        }
		else
		{
            $prevPage = '<span class="gray">&larr;</span>';
		}

		for($l = $slicer->showPageFrom; $l <= $slicer->showPageTo; $l++)
		{
			if ($slicer->page == $l)
			{
	            $htmlPages .= '<b>' . $l . '</b>';
			}
			else
			{
	            $htmlPages .= '<a href="' . $slicer->addPage($slicer->baseUrl, $l) . '">' . $l . '</a>';
			}
		}

		return '<p id="' . $slicer->name . 'slicer" class="slicer">' . $prevPage . $htmlPages . $nextPage . '</p>';
	}
}
