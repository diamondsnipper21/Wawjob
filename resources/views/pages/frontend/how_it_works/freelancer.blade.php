<div class="content">
    <div class="title text-center">
        <h1>{{ trans('how_it_works.freelancer.title') }}</h1>
        <div class="hover-line"></div>
    </div>
    <div class="desc">{{ trans('how_it_works.freelancer.sub_title') }}</div>
</div>
</style>
<div class="steps">
    <div class="step row gray">
        <div class="content flex-row vcenter-dad">
            <div class="col-md-2 col-sm-2 col-xs-3">
                <div class="num"><span>1</span></div>
            </div>
            <div class="col-md-7 col-sm-6 col-xs-9">
                <div class="step-content">
                    <div class="sub-title">{{ trans('how_it_works.freelancer.find.title') }}</div>
                    <div class="sub-desc">
                        <p>{{ trans('how_it_works.freelancer.find.desc') }}</p>
                        {!! trans('how_it_works.freelancer.find.items') !!}
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-4 col-xs-12 text-right vcenter-child">
                <img class="img-responsive" src="/assets/images/pages/home/how_it_works_freelancer_find.png" />
            </div>
        </div>
    </div>
    <div class="step row">
        <div class="content vcenter-dad">
            <div class="col-md-3 col-sm-4 col-xs-8 text-right vcenter-child">
                <img class="img-responsive" src="/assets/images/pages/home/how_it_works_freelancer_hire.png" />
            </div>
            <div class="col-md-2 col-sm-2 col-xs-4">
                <div class="num pull-right"><span>2</span></div>
            </div>
            <div class="col-md-7 col-sm-6 col-xs-12">
                <div class="step-content">
                    <div class="sub-title">{{ trans('how_it_works.freelancer.hire.title') }}</div>
                    <div class="sub-desc">
                        <p>{{ trans('how_it_works.freelancer.hire.desc') }}</p>
                        {!! trans('how_it_works.freelancer.hire.items') !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="step row gray">
        <div class="content vcenter-dad">
            <div class="col-md-2 col-sm-2 col-xs-3">
                <div class="num"><span>3</span></div>
            </div>
            <div class="col-md-7 col-sm-6 col-xs-9">
                <div class="step-content">
                    <div class="sub-title">{{ trans('how_it_works.freelancer.work.title') }}</div>
                    <div class="sub-desc">
                        <p>{{ trans('how_it_works.freelancer.work.desc') }}</p>
                        {!! trans('how_it_works.freelancer.work.items') !!}
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-4 col-xs-12 text-right vcenter-child">
                <img class="img-responsive" src="/assets/images/pages/home/how_it_works_freelancer_work.png" />
            </div>
        </div>
    </div>
    <div class="step row">
        <div class="content vcenter-dad">
            <div class="col-md-3 col-sm-4 col-xs-8 text-right vcenter-child">
				<img class="img-responsive" src="/assets/images/pages/home/how_it_works_freelancer_paid.png" />				
            </div>
            <div class="col-md-2 col-sm-2 col-xs-4">
                <div class="num pull-right"><span>4</span></div>
            </div>
            <div class="col-md-7 col-sm-6 col-xs-12">
                <div class="step-content">
                    <div class="sub-title">{{ trans('how_it_works.freelancer.pay.title') }}</div>
                    <div class="sub-desc">
                        <p>{{ trans('how_it_works.freelancer.pay.desc') }}</p>
                        {!! trans('how_it_works.freelancer.pay.items') !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="action-bar row">
    <div class="content">
        <div class="title">{{ trans('how_it_works.freelancer.action.title') }}</div>
        <div class="hover-line"></div>
        <p>{{ trans('how_it_works.freelancer.action.desc') }}</p>
        <a href="{{ route('user.signup.user', ['role' => 'freelancer']) }}">{{ trans('how_it_works.freelancer.action.button') }}</a>
    </div>
</div>