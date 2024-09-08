<?php
function mdw_html_loadmore_button($button_ID)
{
  $html = "<div class='mdw__content_button-loadmore'>
            <button type='button' id='mdw__button_$button_ID' class='mdw__button_loadmore'>Load More</button>
          </div>";
  return $html;
}
