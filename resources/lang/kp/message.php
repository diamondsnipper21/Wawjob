<?php
/**
   * Show messages.
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

  'buyer' => [
    'job' => [
      'post' => [
        'success_create' => '새 과제 ":job_title" 성과적으로 만들어졌습니다.', 
        'success_update' => '과제 ":job_title" 성과적으로 갱신되였습니다.',
        'success_repost' => '과제 ":job_title" 성과적으로 다시 띄워졌습니다.',
      ], 
      'status' => [
        'closed_unpossible' => '과제 ":job_title" 를 완료할수 없습니다.', 
        'non_exist' => '과제 (#:job_id) 가 존재하지 않습니다.', 
      ], 
      'contract' => [
        'exist' => ':contract_count개의 open 계약이 있습니다.', 
        'exist_plural' => ':contract_count 개의 open 계약이 있습니다.', 
        'already_offered' => 'You already have offered job to :sb',
      ], 
      'invite' => [
        'success' => ':contractor_name 이 ":job_title"를 보았습니다.', 
      ], 
      'make_offer'=> [
        'success' => '( :job_title )에 대한 과제제안을 :contractor_name 에 보냈습니다.', 
        'job_not_open'   => "non-open 과제 ( :job_title )에 제안을 할수 없습니다.", 
        'job_is_private' => "비공개과제 ( :job_title )에 대해서는 제공을 할수 없습니다.", 
        'user_not_freelancer' => ":contractor 개발자가 아닙니다. 개발자에게만 과제를 보낼수있습니다.", 
        'existing_offer' => "( :contractor )에 과제제안을 이미 보냈습니다.", 
      ], 
    ],

    'application' => [
      'client_decline' => ':contractor을 거절하였습니다.', 
    ], 

    'price' => [
      'lt_zero' => '가격을 정확히 입력하십시요. 0보다 작으면 안됩니다.', 
    ], 
    'payment' => [
      'contract' => [
        "success_paid" => "$:amount 를 성과적으로 지불하였습니다.", 
      ]
    ], 
  ], 

  'admin' => [
    'user' => [
      'found' => 'user(s) found',
      'notfound' => '사용자 없음',
    ],
    'contract' => [
      'found' => 'contract(s) found',
      'notfound' => '계약없음',
    ],
  ],
];