<?php
/**
   * Show messages.
   *
   * @author nada
   * @since June 14, 2016
   * @version 1.0 show common data
   * @return Response
   */
return [

  /*
  |--------------------------------------------------------------------------
  | Javascript - Message
  |--------------------------------------------------------------------------
  |
  | The following language lines are used in common.
  |
  */
  'btn_ok' => '확인', 
  'btn_cancel' => '취소', 
  'btn_yes' => '예', 
  'btn_no' => '아니', 
  'btn_delete' => '삭제', 

  'buyer' => [  
    'job' => [
      'status' => [
        'close_job' => '이 과제를 끝내시겠습니까?',
        'change_public' => '이 과제상태를 :status로 변화시키겠습니까?', 
        'app_declined' => '이 신청을 거절하겠습니까?', 
      ]
    ], 
    'user' => [
      'charge' => ':amount딸라를 입금하겠습니까?'
    ]
  ], 

  'freelancer' => [
    'job' => [
      'reject_offer' => '이 제의를 거절하시겠습니까?', 
      'accept_offer' => '이 제의를 접수하시겠습니까?', 
    ], 
    'user' => [
      'withdraw' => ':amount딸라를 뽑겠습니까?', 
    ], 
    'workdiary' => [
      'delete_screenshot' => '선택된 화면리력을 모두 삭제하시겠습니까?', 
      'select_screenshot' => '화면리력을 선택하여야 합니다.', 
    ], 
  ],

  'admin' => [
    'category' => [
      'remove_category' => '이 부류를 삭제하시겠습니까?', 
    ], 
    'faq' => [
      'remove_faq' => '이 FAQ를 삭제하시겠습니까?', 
    ], 
    'affiliate' => [
      'update' => 'Affiliate값을 변화시키겠습니까?', 
      'saved'  => 'Affiliate값을 성과적으로 보관하였습니다.',
    ],
    'notification' => [
      'remove_notification' => '이 알림을 삭제하시겠습니까?', 
      'title_send_notification' => '알림보내기', 
      'title_add_cronjob' => '예약과제목록에 추가',
      'send_notification' => '알림을 보내시겠습니까?', 
      'add_cronjob' => '알림을 예약과제목록에 추가하겠습니까?', 
    ], 
    'skill' => [
      'remove_skill' => '이 능력항목을 삭제하시겠습니까?', 
      'deactivate_skill' => '이 능력항목을 비활성화시키겠습니까?', 
    ], 
    'ticket' => [
      'remove_comment' => '이 설명을 삭제하시겠습니까?', 
    ], 
  ]
];