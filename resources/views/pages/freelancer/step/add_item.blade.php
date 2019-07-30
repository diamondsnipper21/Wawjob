@extends('layouts/default/index')

@section('content')

<script type="text/javascript">
    var delete_item_url = "{{ route('profile.delete') }}";
</script>
<div id="profile_setup_page" class="page-content-section no-padding">

    @include('pages.freelancer.step.header')
    
    <div class="view-section job-content-section">
        <div class="row">
            <div class="col-md-9">
                <div class="profile-page freelancer-step-content {{ $current_user->isSuspended()?'disable-edit-mode':'' }}">
                    <div id="{{ $collection_var_name }}" class="page-content" data-var="{{ $var_name }}">
                        @include('pages.freelancer.user.myprofile.form')

                        <div class="job-top-section mb-4">
                            <div class="title-section">
                                <span class="title">{{ $step }}. {{ trans('profile.add_'.$var_name) }}</span>

                                @if (!$current_user->isSuspended())
                                <button class="btn btn-primary pull-right add-item-action"><i class="icon-plus"></i>&nbsp;{{ trans('common.add') }}</button>
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <script type="text/javascript">
                                var {{ $collection_var_name }} = @json($current_user->$collection_var_name);
                            </script>
                            <div class="col-md-12">
                                <div class="row">
                                    @forelse ($current_user->$collection_var_name as $i => $item)
                                        @include ('pages.freelancer.user.myprofile.' . $var_name, [$var_name => $item])
                                    @empty
                                        <div class="not-found-result text-center col-md-12">
                                            {{ trans('profile.message.No_' . ucfirst($collection_var_name)) }}
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </div>

                        <div class="form-group row pt-4">
                            <div class="col-sm-6">
                                @if (!$current_user->isSuspended())
                                <button type="submit" class="btn btn-link btn-primary btn-back">&lt;&nbsp;{{ trans('common.back') }}</button>
                                @endif
                            </div>
                            <div class="col-sm-6">
                                @if (!$current_user->isSuspended() && $var_name != 'experience')
                                <button type="submit" class="btn btn-link btn-primary btn-next pull-right">{{ trans('common.skip') }}</button>
                                @endif
                                
                                <button type="submit" class="btn btn-primary btn-next pull-right" 
                                    @if ($current_user->isSuspended())
                                    disabled
                                    @elseif ($var_name != 'experience' && count($current_user->$collection_var_name) == 0)
                                    disabled
                                    @endif
                                    >
                                    @if ($var_name == 'experience')
                                        {{ trans('common.complete') }}
                                    @else
                                        {{ trans('common.next') }}
                                    @endif
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 page-content">
                @include('pages.freelancer.step.right_block')
            </div>
        </div><!-- .roww -->
    </div><!-- .view-section -->
</div><!-- .page-content-section -->

@include ('pages.freelancer.user.myprofile.modals', ['modal_form_action' => route('profile.add')])

@endsection