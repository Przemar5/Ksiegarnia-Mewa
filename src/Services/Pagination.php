<?php

namespace App\Services;

class Pagination
{
	/**
	 * Number of pages in total
	 */
	private $pagesCount;

	private $currentPage = 1;

	/**
	 * Number of tabs to show at once
	 */
	private $tabsShown;

	public function __construct()
	{
		
	}

	public function render(): string
	{
		if ($this->pagesCount < 2)
			return '';

		$result = 
		'<div class="pagination">
        	<a href="?strona=1" class="pagination__first">
        		<span class="fas fa-angle-double-left"></span>
        	</a>
        	<a href="?strona='.($this->currentPage-1).'" class="pagination__previous">
        		<span class="fas fa-angle-left"></span>
        	</a>';

        $start = $this->currentPage - floor($this->tabsShown / 2);
        $end = $this->currentPage + ceil($this->tabsShown / 2);

        if ($start < 1) {
        	$start = 1;
        }

        if ($start > 1) {
        	$result .= '<span class="pagination__divider">...</span>';
        }

        if ($end > $this->pagesCount + 1) {
        	$end = $this->pagesCount + 1;
        }

        for ($i = $start; $i < $end; $i++) {
        	$result .= '<a href="?strona='.$i.'" class="pagination__page">'.$i.'</a>';
        }

        if ($end < $this->pagesCount) {
        	$result .= '<span class="pagination__divider">...</span>';
        }
        
        $result .= '<a href="?strona='.($this->currentPage+1).'" class="pagination__next">
        		<span class="fas fa-angle-right"></span>
        	</a>
        	<a href="?strona='.$this->pagesCount.'" class="pagination__last">
        		<span class="fas fa-angle-double-right"></span>
        	</a>
        </div>';

        return $result;
	}

	public function setPagesCount(int $pagesCount): void
	{
		$this->pagesCount = $pagesCount;
	}

	public function getPagesCount(): ?int
	{
		return $this->pagesCount;
	}

	public function setCurrentPage(int $page): void
	{
		$this->currentPage = $page;
	}

	public function getCurrentPage(): ?int
	{
		return $this->currentPage;
	}

	public function setTabsShown(int $tabsShown): void
	{
		$this->tabsShown = $tabsShown;
	}

	public function getTabsShown(): ?int
	{
		return $this->tabsShown;
	}
}