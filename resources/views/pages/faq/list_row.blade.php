<li class="dd-item">
  <div class="dd3-content">
    <div class="dd-item-header">
      <h5 class="title">{{parse_multilang($faq->title, App::getLocale())}}</h5>
    </div>
    <div class="dd-item-body">
      <div class="dd-item-content collapse"><p>{!!nl2br(parse_multilang($faq->content, App::getLocale()))!!}</p></div>
    </div>
  </div>
</li>