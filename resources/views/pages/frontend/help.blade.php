<?php

/**
 * @author KCG
 * @since Mar 8, 2018
 */

use iJobDesk\Models\HelpPage;

?>

@extends('layouts/frontend/index', ['fullwidth' => true])

@section('css')
<link rel="stylesheet" href="{{ url('assets/styles/frontend/help.css') }}">
@endsection

@section('content')
<div id="help_page" class="content clearfix">
    <div class="title">
        <h1>{{ trans('page.frontend.help.title') }}</h1>
        <div class="hover-line"></div>
    </div>

    <form id="form_help_search" method="get" action="{{ route('frontend.help.search') }}">
        <div class="input-group">
            <input type="text" placeholder="{{ trans('common.search') }}" class="form-control" name="q" value="{{ !empty($q)?$q:'' }}">
            <span class="input-group-btn">
                <button class="btn btn-primary" type="submit">
                    {{ trans('home.help.get_help') }}
                </button>
            </span>
        </div>
    </form>

    @if (!empty($help_page))
        <!-- Help Detail Page -->
        @include('pages.frontend.help.detail')
    @elseif (!empty($q))
        <!-- Search Page -->
        @include('pages.frontend.help.search_results')
    @else
        <div class="tabs">
            <div>
                <a href="#buyer_content" class="active">{{ trans('how_it_works.buyer.tab') }}</a>
                <a href="#freelancer_content">{{ trans('how_it_works.freelancer.tab') }}</a>
            </div>
        </div>
        
        <div id="buyer_content" class="tab-p-content active">
            <h2>{{ trans('home.help.buyer_title') }}</h2>
            @include('pages.frontend.help.pages', ['type' => HelpPage::TYPE_BUYER])
        </div>
        <div id="freelancer_content" class="tab-p-content">
            <h2>{{ trans('home.help.freelancer_title') }}</h2>
            @include('pages.frontend.help.pages', ['type' => HelpPage::TYPE_FREELANCER])
        </div>

        @include('pages.frontend.help.login')
    @endif
</div>
@endsection