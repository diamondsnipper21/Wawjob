<div class="content">
    <div class="title text-center">
        <h1>{{ trans('how_it_works.buyer.title') }}</h1>
        <div class="hover-line"></div>
    </div>
    <div class="desc">{{ trans('how_it_works.buyer.sub_title') }}</div>
</div>
<div class="steps">
    <div id="find" class="step row gray">
        <div class="content">
            <div class="col-md-2 col-sm-2 col-xs-3">
                <div class="num"><span>1</span></div>
            </div>
            <div class="col-md-7 col-sm-6 col-xs-9">
                <div class="step-content">
                    <div class="sub-title">{{ trans('how_it_works.buyer.find.title') }}</div>
                    <div class="sub-desc">
                        <p>{{ trans('how_it_works.buyer.find.desc') }}</p>
                        {!! trans('how_it_works.buyer.find.items') !!}
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-4 col-xs-12 text-right">
                <img class="img-responsive" src="/assets/images/pages/home/how_it_works_find.png" />
            </div>
        </div>
    </div>
    <div id="hire" class="step row">
        <div class="content">
            <div class="col-md-3 col-sm-4 col-xs-8 text-right">
                <img class="img-responsive" src="/assets/images/pages/home/how_it_works_hire.png" />
            </div>
            <div class="col-md-2 col-sm-2 col-xs-4">
                <div class="num pull-right"><span>2</span></div>
            </div>
            <div class="col-md-7 col-sm-6 col-xs-12">
                <div class="step-content">
                    <div class="sub-title">{{ trans('how_it_works.buyer.hire.title') }}</div>
                    <div class="sub-desc">
                        <p>{{ trans('how_it_works.buyer.hire.desc') }}</p>
                        {!! trans('how_it_works.buyer.hire.items') !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="work" class="step row gray">
        <div class="content">
            <div class="col-md-2 col-sm-2 col-xs-3">
                <div class="num"><span>3</span></div>
            </div>
            <div class="col-md-7 col-sm-6 col-xs-9">
                <div class="step-content">
                    <div class="sub-title">{{ trans('how_it_works.buyer.work.title') }}</div>
                    <div class="sub-desc">
                        <p>{{ trans('how_it_works.buyer.work.desc') }}</p>
                        {!! trans('how_it_works.buyer.work.items') !!}
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-4 col-xs-12 text-right">
                <img class="img-responsive" src="/assets/images/pages/home/how_it_works_work.png" />
            </div>
        </div>
    </div>
    <div id="pay" class="step row">
        <div class="content">
            <div class="col-md-3 col-sm-4 col-xs-8 text-right">
                <img class="img-responsive" src="/assets/images/pages/home/how_it_works_pay.png" />
            </div>
            <div class="col-md-2 col-sm-2 col-xs-4">
                <div class="num pull-right"><span>4</span></div>
            </div>
            <div class="col-md-7 col-sm-6 col-xs-12">
                <div class="step-content">
                    <div class="sub-title">{{ trans('how_it_works.buyer.pay.title') }}</div>
                    <div class="sub-desc">
                        <p>{{ trans('how_it_works.buyer.pay.desc') }}</p>
                        {!! trans('how_it_works.buyer.pay.items') !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="action-bar row">
    <div class="content">
        <div class="title">{{ trans('how_it_works.buyer.action.title') }}</div>
        <div class="hover-line"></div>
        <p>{{ trans('how_it_works.buyer.action.desc') }}</p>
        @if (!$current_user || $current_user->isFreelancer())
        <a href="{{ route('user.signup.user', ['role' => 'buyer']) }}">{{ trans('how_it_works.buyer.action.button') }}</a>
        @else
        <a href="{{ route('job.create') }}">{{ trans('how_it_works.buyer.action.button') }}</a>
        @endif
    </div>
</div>