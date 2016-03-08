<?php

namespace voku\helper;

/**
 * Paginator: PHP Pagination Class
 *
 * @author David Carr - dave@daveismyname.com - http://www.daveismyname.com
 * @author Lars Moelleken - lars@moelleken.org - http://www.moelleken.org
 *
 */
class Paginator
{

  /**
   * the current page-id from _GET
   *
   * @var int
   */
  private $_pageIdentifierFromGet;

  /**
   * pages per page in the pager
   *
   * @var int
   */
  private $_perPage;

  /**
   * @var string the get-parameter for the pager
   *
   * e.g.: "mypager=2" -> then use "mypager" here
   */
  private $_instance;

  /**
   * @var int
   */
  private $_totalRows = 0;

  /**
   * @var string
   */
  private $_paginatorStartCssClass = 'pagination--start';

  /**
   * @var string
   */
  private $_paginatorEndCssClass = 'pagination--end';

  /**
   * @var string
   */
  private $_paginatorStartChar = '&laquo;';

  /**
   * @var string
   */
  private $_paginatorEndChar = '&raquo;';

  /**
   * @var bool
   */
  private $_withLinkInCurrentLi = false;

  /**
   * @var int
   */
  private $_adjacent = 2;

  /**
   * __construct
   *
   * @param int    $perPage
   * @param string $instance
   */
  public function __construct($perPage, $instance)
  {
    $this->_instance = (string) $instance;
    $this->_perPage = (int) $perPage;
    $this->set_instance();
  }

  /**
   * set the object parameter
   */
  private function set_instance()
  {
    if (isset($_GET[$this->_instance])) {
      $this->_pageIdentifierFromGet = (int)$_GET[$this->_instance];
    }

    if (!$this->_pageIdentifierFromGet) {
      $this->_pageIdentifierFromGet = 1;
    }
  }

  /**
   * set the "pageIdentifierFromGet"
   *
   * @param $pageId
   */
  public function set_pageIdentifierFromGet($pageId)
  {
    $this->_pageIdentifierFromGet = $pageId;
  }

  /**
   * set the "adjacent"
   *
   * @param int $adjacent
   */
  public function set_adjacent($adjacent)
  {
    $this->_adjacent = (int)$adjacent;
  }

  /**
   * set the "totalRows"
   *
   * @param int $totalRows
   */
  public function set_total($totalRows)
  {
    $this->_totalRows = (int)$totalRows;
  }

  /**
   * set the "withLinkInCurrentLi"
   *
   * @param bool $bool
   */
  public function set_withLinkInCurrentLi($bool)
  {
    $this->_withLinkInCurrentLi = (bool)$bool;
  }

  /**
   * set the "paginatorStartCssClass"
   *
   * @param $string
   */
  public function set_paginatorStartCssClass($string)
  {
    $this->_paginatorStartCssClass = $string;
  }

  /**
   * set the "paginatorEndCssClass"
   *
   * @param $string
   */
  public function set_paginatorEndCssClass($string)
  {
    $this->_paginatorEndCssClass = $string;
  }
  /**
   * set the "paginatorStartChar"
   *
   * @param $string
   */
  public function set_paginatorStartChar($string)
  {
    $this->_paginatorStartChar = $string;
  }

  /**
   * set the "paginatorEndChar"
   *
   * @param $string
   */
  public function set_paginatorEndChar($string)
  {
    $this->_paginatorEndChar = $string;
  }

  /**
   * returns the limit for the data source
   *
   * @return string LIMIT-String for a SQL-Query
   */
  public function get_limit()
  {
    return ' LIMIT ' . (int)$this->get_start() . ',' . (int)$this->_perPage;
  }

  /**
   * creates the starting point for get_limit()
   *
   * @return int
   */
  public function get_start()
  {
    return ($this->_pageIdentifierFromGet * $this->_perPage) - $this->_perPage;
  }

  /**
   * get next- / prev meta-links
   *
   * @param string $path
   *
   * @return string
   */
  public function getNextPrevLinks($path = '?')
  {
    // init
    $nextLink = '';
    $prevLink = '';

    $prev = $this->_pageIdentifierFromGet - 1;
    $next = $this->_pageIdentifierFromGet + 1;

    $lastpage = ceil($this->_totalRows / $this->_perPage);

    if ($lastpage > 1) {

      if ($this->_pageIdentifierFromGet > 1) {
        $prevLink = '<link rel="prev" href="' . $path . $this->_instance . '=' . $prev . '">';
      }

      if ($this->_pageIdentifierFromGet < $lastpage) {
        $nextLink = '<link rel="next" href="' . $path . $this->_instance . '=' . $next . '">';
      }

    }

    return $nextLink . $prevLink;
  }

  /**
   * create links for the paginator
   *
   * @param string $path
   *
   * @return string
   */
  public function page_links($path = '?')
  {
    // init
    $counter = 0;
    $pagination = '';

    $prev = $this->_pageIdentifierFromGet - 1;
    $next = $this->_pageIdentifierFromGet + 1;

    $lastpage = ceil($this->_totalRows / $this->_perPage);
    $tmpSave = $lastpage - 1;

    if ($lastpage > 1) {
      $pagination .= '<ul class="pagination">';

      if ($this->_pageIdentifierFromGet > 1) {
        $pagination .= '<li class="' . $this->_paginatorStartCssClass . '"><a href="' . $path . $this->_instance . '=' . $prev . '">' . $this->_paginatorStartChar . '</a></li>';
      } else {
        $pagination .= '<li class="' . $this->_paginatorStartCssClass . '">' . $this->_paginatorStartChar . '</li>';
      }

      if ($lastpage < 7 + ($this->_adjacent * 2)) {

        for ($counter = 1; $counter <= $lastpage; $counter++) {
          $pagination .= $this->createLiCurrentOrNot($path, $counter);
        }

      } elseif ($this->_pageIdentifierFromGet < 5 && ($lastpage > 5 + ($this->_adjacent * 2))) {

        if ($this->_pageIdentifierFromGet < 1 + ($this->_adjacent * 2)) {
          for ($counter = 1; $counter < 4 + ($this->_adjacent * 2); $counter++) {
            $pagination .= $this->createLiCurrentOrNot($path, $counter);
          }
        }

        $pagination .= '<li>&hellip;</li>';
        $pagination .= '<li><a href="' . $path . $this->_instance . '=' . $tmpSave . '">' . $tmpSave . '</a></li>';
        $pagination .= '<li><a href="' . $path . $this->_instance . '=' . $lastpage . '">' . $lastpage . '</a></li>';

      } elseif ($lastpage - ($this->_adjacent * 2) > $this->_pageIdentifierFromGet && $this->_pageIdentifierFromGet > ($this->_adjacent * 2)) {

        $pagination .= $this->createLiFirstAndSecond($path);

        if ($this->_pageIdentifierFromGet != 5) {
          $pagination .= '<li>&hellip;</li>';
        }

        for ($counter = $this->_pageIdentifierFromGet - $this->_adjacent; $counter <= $this->_pageIdentifierFromGet + $this->_adjacent; $counter++) {
          $pagination .= $this->createLiCurrentOrNot($path, $counter);
        }

        $pagination .= '<li>&hellip;</li>';
        $pagination .= '<li><a href="' . $path . $this->_instance . '=' . $tmpSave . '">' . $tmpSave . '</a></li>';
        $pagination .= '<li><a href="' . $path . $this->_instance . '=' . $lastpage . '">' . $lastpage . '</a></li>';

      } else {

        $pagination .= $this->createLiFirstAndSecond($path);

        $pagination .= '<li>&hellip;</li>';

        for ($counter = $lastpage - (2 + ($this->_adjacent * 2)); $counter <= $lastpage; $counter++) {
          $pagination .= $this->createLiCurrentOrNot($path, $counter);
        }

      }

      if ($this->_pageIdentifierFromGet < $counter - 1) {
        $pagination .= '<li class="' . $this->_paginatorEndCssClass . '"><a href="' . $path . $this->_instance . '=' . $next . '">' . $this->_paginatorEndChar . '</a></li>';
      } else {
        $pagination .= '<li class="' . $this->_paginatorEndCssClass . '">' . $this->_paginatorEndChar . '</li>';
      }

      $pagination .= '</ul>';
    }

    return $pagination;
  }

  /**
   * @param string $path
   *
   * @return string
   */
  private function createLiFirstAndSecond($path)
  {
    $html = '';

    $html .= '<li><a href="' . $path . $this->_instance . '=1">1</a></li>';
    $html .= '<li><a href="' . $path . $this->_instance . '=2">2</a></li>';

    return $html;
  }

  /**
   * @param string $path
   * @param int    $counter
   *
   * @return string
   */
  private function createLiCurrentOrNot($path, $counter)
  {
    $html = '';

    $textAndOrLink = '<a href="' . $path . $this->_instance . '=' . $counter . '">' . $counter . '</a>';
    if ($this->_withLinkInCurrentLi === false) {
      $currentTextAndOrLink = $counter;
    } else {
      $currentTextAndOrLink = $textAndOrLink;
    }

    if ($counter == $this->_pageIdentifierFromGet) {
      $html .= '<li class="current">' . $currentTextAndOrLink . '</li>';
    } else {
      $html .= '<li>' . $textAndOrLink . '</li>';
    }

    return $html;
  }
}
