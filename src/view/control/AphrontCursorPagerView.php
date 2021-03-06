<?php

final class AphrontCursorPagerView extends AphrontView {

  private $afterID;
  private $beforeID;

  private $pageSize = 100;

  private $nextPageID;
  private $prevPageID;
  private $moreResults;

  private $uri;

  public function setPageSize($page_size) {
    $this->pageSize = max(1, $page_size);
    return $this;
  }

  public function getPageSize() {
    return $this->pageSize;
  }

  public function setURI(PhutilURI $uri) {
    $this->uri = $uri;
    return $this;
  }

  public function readFromRequest(AphrontRequest $request) {
    $this->uri = $request->getRequestURI();
    $this->afterID = $request->getStr('after');
    $this->beforeID = $request->getStr('before');
    return $this;
  }

  public function setAfterID($after_id) {
    $this->afterID = $after_id;
    return $this;
  }

  public function getAfterID() {
    return $this->afterID;
  }

  public function setBeforeID($before_id) {
    $this->beforeID = $before_id;
    return $this;
  }

  public function getBeforeID() {
    return $this->beforeID;
  }

  public function setNextPageID($next_page_id) {
    $this->nextPageID = $next_page_id;
    return $this;
  }

  public function getNextPageID() {
    return $this->nextPageID;
  }

  public function setPrevPageID($prev_page_id) {
    $this->prevPageID = $prev_page_id;
    return $this;
  }

  public function getPrevPageID() {
    return $this->prevPageID;
  }

  public function sliceResults(array $results) {
    if (count($results) > $this->getPageSize()) {
      $offset = ($this->beforeID ? count($results) - $this->getPageSize() : 0);
      $results = array_slice($results, $offset, $this->getPageSize(), true);
      $this->moreResults = true;
    }
    return $results;
  }

  public function getHasMoreResults() {
    return $this->moreResults;
  }

  public function willShowPagingControls() {
    return $this->prevPageID ||
           $this->nextPageID ||
           $this->afterID ||
           ($this->beforeID && $this->moreResults);
  }

  public function getFirstPageURI() {
    if (!$this->uri) {
      throw new PhutilInvalidStateException('setURI');
    }

    if (!$this->afterID && !($this->beforeID && $this->moreResults)) {
      return null;
    }

    return $this->uri
      ->alter('before', null)
      ->alter('after', null);
  }

  public function getPrevPageURI() {
    if (!$this->uri) {
      throw new PhutilInvalidStateException('getPrevPageURI');
    }

    if (!$this->prevPageID) {
      return null;
    }

    return $this->uri
      ->alter('after', null)
      ->alter('before', $this->prevPageID);
  }

  public function getNextPageURI() {
    if (!$this->uri) {
      throw new PhutilInvalidStateException('setURI');
    }

    if (!$this->nextPageID) {
      return null;
    }

    return $this->uri
      ->alter('after', $this->nextPageID)
      ->alter('before', null);
  }

  public function render() {
    if (!$this->uri) {
      throw new PhutilInvalidStateException('setURI');
    }

    $links = array();

    $first_uri = $this->getFirstPageURI();
    if ($first_uri) {
      $links[] = phutil_tag(
        'a',
        array(
          'href' => $first_uri,
        ),
        "\xC2\xAB ".pht('First'));
    }

    $prev_uri = $this->getPrevPageURI();
    if ($prev_uri) {
      $links[] = phutil_tag(
        'a',
        array(
          'href' => $prev_uri,
        ),
        "\xE2\x80\xB9 ".pht('Prev'));
    }

    $next_uri = $this->getNextPageURI();
    if ($next_uri) {
      $links[] = phutil_tag(
        'a',
        array(
          'href' => $next_uri,
        ),
        pht('Next')." \xE2\x80\xBA");
    }

    return phutil_tag(
      'div',
      array('class' => 'aphront-pager-view'),
      $links);
  }

}
