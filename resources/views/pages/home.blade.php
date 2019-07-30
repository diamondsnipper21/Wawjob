@extends('layouts/home/index')

@section('css')
<link rel="stylesheet" href="{{ url('assets/styles/frontend/home.css') }}">
@endsection

@section('content')

<div class="intro">
	<div class="container">
		<!--<img src="/assets/images/pages/home/intro.jpg" />-->
		<div class="intro-content">
			<h2 class="on-bg">{{ trans('home.hire_top_talents_to_get_job_done') }}</h2>
			<div class="desc">{!! trans('home.hire_top_talents_to_get_job_done_desc') !!}</div>
			<div class="buttons">
				<a href="{{ route('user.signup.user', ['role' => 'buyer']) }}" class="btn btn-primary hire">{{ trans('common.hire') }}</a>&nbsp;&nbsp;&nbsp;
				<a href="{{ route('user.signup.user', ['role' => 'freelancer']) }}" class="btn work">{{ trans('common.work') }}</a>
			</div>
		</div>
	</div>
</div>
<div class="how-it-works text-center">
	<div class="container">
		<div class="row">
			<div class="title">{{ trans('home.how_it_works') }}</div>
			<div class="hover-line"></div>
			<div class="desc">{{ trans('how_it_works.buyer.find.desc') }}</div>
			<div class="col-sm-3 item find">
				<img src="/assets/images/pages/home/how_it_works_find_small.png" />
				<div class="sub-title">{{ trans('common.find') }}</div>
				<div class="sub-desc">{{ trans('home.how_it_works_desc.find') }}</div>
				<a href="{{ route('frontend.how_it_works') }}#find" class="learn-more">{{ trans('common.learn_more') }}</a>
			</div>
			<div class="col-sm-3 item hire">
				<img src="/assets/images/pages/home/how_it_works_hire_small.png" />
				<div class="sub-title">{{ trans('common.hire') }}</div>
				<div class="sub-desc">{{ trans('home.how_it_works_desc.hire') }}</div>
				<a href="{{ route('frontend.how_it_works') }}#hire" class="learn-more">{{ trans('common.learn_more') }}</a>
			</div>
			<div class="col-sm-3 item work">
				<img src="/assets/images/pages/home/how_it_works_work_small.png" />
				<div class="sub-title">{{ trans('common.work') }}</div>
				<div class="sub-desc">{{ trans('home.how_it_works_desc.work') }}</div>
				<a href="{{ route('frontend.how_it_works') }}#work" class="learn-more">{{ trans('common.learn_more') }}</a>
			</div>
			<div class="col-sm-3 item pay">
				<img src="/assets/images/pages/home/how_it_works_pay_small.png" />
				<div class="sub-title">{{ trans('common.pay') }}</div>
				<div class="sub-desc">{{ trans('home.how_it_works_desc.pay') }}</div>
				<a href="{{ route('frontend.how_it_works') }}#pay" class="learn-more">{{ trans('common.learn_more') }}</a>
			</div>
		</div>
	</div>
</div>

<div class="testimonials text-center">
	<div class="container">
		<div class="row">
			<div class="title">{!! trans('home.our_clients_say') !!}</div>
			<div class="hover-line"></div>
			<div id="testimonials_carousel" class="carousel slide">
				<div class="carousel-inner">
		            <div id="testimonial1" class="active item">
		            	<img src="/assets/images/pages/home/people/Men1.png" class="img-responsive" width="120" height="120">
		            	<div class="text">
		                	<p>{{ trans('home.clients.men1.desc') }}</p>
		                	<div class="author">{{ trans('home.clients.men1.name') }}</div>
		                	<div class="role">{{ trans('home.clients.men1.country') }}</div>
		                </div>			
		            </div>
		            <div id="testimonial2" class="item">
		            	<img src="/assets/images/pages/home/people/Men2.png" class="img-responsive" width="120" height="120">
		            	<div class="text">
		                	<p>{{ trans('home.clients.men2.desc') }}</p>
		                	<div class="author">{{ trans('home.clients.men2.name') }}</div>
		                	<div class="role">{{ trans('home.clients.men2.country') }}</div>
		                </div>	
		            </div>
		            <div id="testimonial3" class="item">
		            	<img src="/assets/images/pages/home/people/Men3.png	" class="img-responsive" width="120" height="120">
		            	<div class="text">
		                	<p>{{ trans('home.clients.men3.desc') }}</p>
		                	<div class="author">{{ trans('home.clients.men3.name') }}</div>
		                	<div class="role">{{ trans('home.clients.men3.country') }}</div>
		                </div>
		            </div>
		        </div>
		        <!-- Indicators -->
				<ol class="carousel-indicators">
					<li data-target="#testimonials_carousel" data-slide-to="0" class="active"></li>
					<li data-target="#testimonials_carousel" data-slide-to="1"></li>
					<li data-target="#testimonials_carousel" data-slide-to="2"></li>
				</ol>
		        <a class="left-btn" href="#testimonials_carousel" data-slide="prev"><i class="fa fa-angle-left"></i></a>
		        <a class="right-btn" href="#testimonials_carousel" data-slide="next"><i class="fa fa-angle-right"></i></a>
			</div>
		</div>
	</div>
</div>
@endsection