

<div class="dashboard-stat {{ $_inn_blade_sb_color or '' }}">
  <div class="visual">
    <i class="{{ $_inn_blade_sb_icon or '' }}"></i>
  </div>
  <div class="details">
    <div class="number">
       {!! $_inn_blade_sb_content_num or '' !!}
    </div>
    <div class="desc">
       {!! $_inn_blade_sb_content_desc or '' !!}
    </div>
  </div>
  <a class="more" href="{{ $_inn_blade_sb_link or '#' }}">
  {!! $_inn_blade_sb_link_label or 'View More' !!} <i class="icon-sf icon-white icon-swapright"></i>
  </a>
</div>