<?php
/**
   * Show common data.
   *
   * @author Jin
   * @since Jan 14, 2016
   * @version 1.0 show common data
   * @return Response
   */
return [

  /*
  |--------------------------------------------------------------------------
  | Page Language Lines
  |--------------------------------------------------------------------------
  |
  | The following language lines are used in common.
  |
  */

  'language' => [
    'en' => '영어', 
    'ch' => '중어', 
    'kp' => '조선어', 
  ], 

  'user' => [
    'status' => [
      '0' => 'Inactive',
      '1' => 'Active',
      '2' => 'Block',
      '3' => 'Financial Suspend',
      '4' => 'Account Suspend',
      '9' => 'Account Closed',
    ],
    'status-do' => [
      '0' => 'Inactive',
      '1' => 'Active',
      '2' => 'Block',
      '3' => 'Financial Suspend',
      '4' => 'Suspend',
      '9' => 'Close',
    ],
    'status-do-icon' => [
      '0' => '',
      '1' => 'fa fa-link',
      '2' => 'fa fa-lock',
      '3' => 'fa fa-unlink',
      '4' => 'fa fa-bolt',
      '9' => 'fa fa-times',
    ],
    'types' => [
      'user_guest' => '손님',
      'user_freelancer' => '개발자',
      'user_buyer' => '고객',
      'user_admin' => '관리자',
      'user_sadmin' => '책임관리자',
    ],
    'gender' => [
      '0' => '남',
      '1' => '녀',
    ],
  ],

  'contract' => [
    'status' => [
      '0' => 'Offer',
      '1' => 'Started',
      '2' => 'Paused',
      '3' => 'Suspend',
      '4' => 'Finished(wait for feedback)',
      '5' => 'Completed',
      '6' => 'Offer Declined',
    ],
    'status-icon' => [
      '0' => 'fa fa-chain',
      '1' => 'fa fa-play-circle-o',
      '2' => 'fa fa-warning',
      '3' => 'fa fa-bolt',
      '4' => 'fa fa-check',
      '5' => 'fa fa-check-square',
      '6' => 'fa fa-chain-broken',
    ],
    'status-do' => [
      '4' => 'Finish',
      '1' => 'Resume',
      '2' => 'Pause',
      '3' => 'Suspend',
    ],
    'status-do-icon' => [
      '4' => 'fa fa-check',
      '1' => 'fa fa-play-circle-o',
      '2' => 'fa fa-warning',
      '3' => 'fa fa-bolt',
    ],
    'is_hourly' => [
      '0' => 'Hourly',
      '1' => 'Fixed',
      '2' => 'Weekly',
      '3' => 'Annual',
    ],
    'is_hourly-icon' => [
      '0' => 'fa fa-clock-o',
      '1' => 'fa fa-thumb-tack',
      '2' => 'fa fa-calendar',
      '3' => 'fa fa-calendar',
    ],
  ],

  'social' => [
    'facebook' => 'Facebook',
    'linkedin' => 'LinkedIn',
    'google' => 'Google'
  ],

  // Words & phrases
  'posted' => '창조된 시간:', 
  'started' => 'Started', 
  'ago' => '전',
  'balance' => '잔고',
  'budget' => '예산',  
  'cancel' => '취소',
  'repost' => '다시 띄우기',
  'save' => '보관',
  'add' => '추가',
  'delete' => 'Delete',
  'edit' => 'Edit',
  'change' => 'Change',
  'on' => 'on',
  'to' => 'to',  
  'cover_letter' => '소개',
  'faq' => '질의문답',
  'job' => '과제',
  'now' => '현재',
  'ok'     => '예', 
  'cancel' => 'Cancel',
  'present' => '현재',
  'reports' => '보고',
  'search' => '검색',
  'skills' => '능력',
  'submit' => '전송',
  'decline'=> '거절',
  'ticket' => '표',
  'timesheet' => '취업시간기록표',
  'timezone' => '시간대',
  'today' => '오늘',
  'work_diary' => '작업업리력',
  'invited' => '만남', 
  'offer' => '제의', 
  'hired' => '채용됨',
  'limit' => '한계', 
  'learn_more' => 'Learn More',
  'please_select' => 'Please select...',

  // due to conflicts with 'common.user', which is an Array above
  'word' => [
    'contract' => '계약',
    'user' => '사용자',
  ],

  // Hourly or Fixed
  'hourly' => '시간제가격',
  'fixed'  => '고정가격', 
  'fixed_price' => '고정가격',
  'hourly_job' => '시간제과제',
  'fixed_price_job' => '고정가격 과제',
  'bonus' => '상금',
  'charge' => '입금',
  'withdrawal' => '회수',
  'refund' => '반환',

  // Duration
  'mt6m' => '6개월 이상',
  '3t6m' => '3 ~ 6 개월',
  '1t3m' => '1 ~ 3 개월',
  'lt1m' => '1개월이하',
  'lt1w' => '1주이하',

  // Workload
  'full_time' => '전임 - 주당 30시간이상',
  'part_time' => '부업 - 주당 10 ~ 30 시간',
  'as_needed' => '요구대로 - 주당 10시간이하',

  // Availability
  'av_more_than_30' => '주당 30시간이상',
  'av_10_to_30' => '주당 10 ~ 30 시간',
  'av_less_than_10' => '주당 10시간이하',
  'av_not_available' => '불가능',

  'no_items' => '결과가 없습니다.',

  'hr' => '시',
  'hour' => '시간',
  'n_hrs' => ':n 시',
  'n_hours' => ':n 시간',
  'n_max_hours_per_week' => '주당 최대 :n 시간',

  'unit_year' => '년',
  'unit_month' => '개월', 
  'unit_day'   => '일', 
  'unit_hour'  => '시간', 
  'unit_minute'=> '분', 
  'unit_plural'=> '', 
  'unit_timespace'=> '',

  'at_time' => ':time에', 
  'drafts' => 'Showing :from - :to of :total Drafts',
  'archived' => 'Showing :from - :to of :total Archived',
];