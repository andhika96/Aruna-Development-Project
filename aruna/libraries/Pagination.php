<?php

	/*
	 *	Aruna Development Project
	 *	IS NOT FREE SOFTWARE
	 *	Codename: Aruna Personal Site
	 *	Source: Based on Sosiaku Social Networking Software
	 *	Website: https://www.sosiaku.gq
	 *	Website: https://www.aruna-dev.id
	 *	Created and developed by Andhika Adhitia N
	 */

defined('MODULEPATH') OR exit('No direct script access allowed');

class ARUNA_Pagination {

	public $total;
	public $onepage;
	public $num;
	public $pagecount;
	public $total_page;
	public $offset;
	public $linkhead;
	public $type_id;
	public $first = '';
	public $paras = '';
	public $prefix = "322_";

	public $next_page = '&raquo;';
	public $last_page = '&laquo;';
	public $first_page = 'First';
	public $end_page = 'End';

	public function __construct($total_rows) 
	{
		// if ( ! is_array($params))
		// {
		// 	log_message('error', 'Data must be an array');
		// 	show_error('Data must be an array');
		// }

		$this->total = $total_rows;
		$this->onepage = num_per_page();
		$this->total_page = ceil($total_rows/num_per_page());

		$pagecount = page();

		if (empty($pagecount)) 
		{
			$this->pagecount = 1;
			$this->offset = 0;
		}
		else 
		{
			$this->pagecount = $pagecount;
			$this->offset = ($pagecount-1)*num_per_page();
		}
		
		$linkarr = explode("pagecount=", $_SERVER['QUERY_STRING']);
		$linkft = $linkarr[0];
		$formlink = NULL;

		if (empty($linkft)) 
		{
			$this->linkhead = $_SERVER['PHP_SELF']."?".$formlink;
		}
		else 
		{
			$linkft = (substr($linkft, -1)=="&") ? $linkft : $linkft."&";
			$this->linkhead = $_SERVER['PHP_SELF']."?".$linkft.$formlink;
		}
	}

	public function offset() 
	{
		return $this->offset;
	}

	public function pre_page($char = '') 
	{
		$linkhead = $this->linkhead;
		$pagecount = $this->pagecount;

		if (empty($char)) 
		{
			$char = $this->last_page;
		}

		if ($pagecount > 1) 
		{
			$pre_page = $pagecount-1;

			if ($pre_page == 1) 
			{
				return "<li class=\"page-item\"><a class=\"page-link\" href=\"".$this->paras."\" aria-label=\"Previous\"><span aria-hidden=\"true\">$char</span></a></li>";
			}
			else 
			{
				return "<li class=\"page-item\"><a class=\"page-link\" href=\"".$this->paras."page=".$pre_page."\" aria-label=\"Previous\"><span aria-hidden=\"true\">$char</span></a></li>";
			}
		}
		else 
		{
			return '';
		}
	}

	public function next_page($char = '') 
	{
		$linkhead = $this->linkhead;
		$total_page = $this->total_page;
		$pagecount = $this->pagecount;

		if (empty($char)) 
		{
			$char = $this->next_page;
		}

		if ($pagecount < $total_page) 
		{
			$next_page = $pagecount+1;
			return "<li class=\"page-item\"><a class=\"page-link\" href=\"".$this->paras."page=".$next_page."\" aria-label=\"Next\"><span aria-hidden=\"true\">$char</span></a></li>";
		}
		else 
		{
			return '';
		}
	}

	public function num_bar($num = '', $color = '', $left = '', $right = '') 
	{
		$num = (empty($num)) ? 9 : $num;
		$this->num = $num;
		$mid = floor($num/2);
		$last = $num - 1;
		$pagecount = $this->pagecount;
		$totalpage = $this->total_page;
		$linkhead  = $this->linkhead;
		$color = (empty($color)) ? "#ff0000" : $color;
		$minpage = (($pagecount-$mid) < 1) ? 1 : ($pagecount-$mid);
		$maxpage = $minpage + $last;
		$linkbar = NULL;

		if ($maxpage > $totalpage) 
		{
			$maxpage = $totalpage;
			$minpage = $maxpage - $last;
			$minpage = ($minpage < 1) ? 1 : $minpage;
		}

		for ($i = $minpage; $i <= $maxpage; $i++) 
		{
			$char = $left.$i.$right;

			if ($i == $pagecount) 
			{
				$linkchar = "<li class=\"page-item active\"><a class=\"page-link\">".$char."</a></li>";
			}
			elseif ($i == 1) 
			{
				$linkchar = "<li class=\"page-item\"><a class=\"page-link\" href=\"".$this->paras."\">".$char."</a></li>";
			}
			else 
			{
				$linkchar = "<li class=\"page-item\"><a class=\"page-link\" href=\"".$this->paras."page=$i\">".$char."</a></li>";
			}
		
			$linkbar = $linkbar.$linkchar;
		}

		return $linkbar;
	}

	public function pre_group($char = '') 
	{
		$pagecount = $this->pagecount;

		if ($pagecount > 2) 
		{
			if ($this->first) 
			{
				$content = "<li class=\"page-item\"><a class=\"page-link\" href=\"".$this->first."\" aria-label=\"Previous\"><span aria-hidden=\"true\">".$this->first_page."</span></a></li>";
			}
			else 
			{
				$content = "<li class=\"page-item\"><a class=\"page-link\" href=\"".$this->paras."\" aria-label=\"Previous\"><span aria-hidden=\"true\">".$this->first_page."</span></a></li>";
			}
		}
		else 
		{
			$content = "";
		}

		return $content;
	}

	public function next_group($char = '') 
	{
		$pagecount = $this->pagecount;
		$linkhead = $this->linkhead;
		$totalpage = $this->total_page;

		if ($pagecount < ($totalpage-1)) 
		{
			$content = "<li class=\"page-item\"><a class=\"page-link\" href=\"".$this->paras."page=".$totalpage."\" aria-label=\"Next\"><span aria-hidden=\"true\">".$this->end_page."</span></a></li>";
		}
		else 
		{
			$content = "";
		}

		return $content;
	}

	public function whole_num_bar($position = 'justify-content-start', $num = '', $color = '') 
	{
		if ($this->total <= $this->onepage) 
		{
			return '';
		}
		
		if (preg_match("/\?/i", $this->paras)) 
		{
			$this->paras = $this->paras.'&';
		}
		else {
			$this->paras = $this->paras.'?';
		}

		$num_bar = $this->num_bar($num, $color);
		$pre_group = $this->pre_group();
		$pre_page = $this->pre_page();
		$next_page = $this->next_page();
		$next_group = $this->next_group();

		$pagebar = $pre_group.$pre_page.$num_bar.$next_page.$next_group;

		if ($pagebar == 1) 
		{
			return '';
		}
		else 
		{
			return '<div class="w-100 mt-3"><ul class="pagination '.$position.'">'.$pagebar.'</ul></div>';
		}
	}
}

?>