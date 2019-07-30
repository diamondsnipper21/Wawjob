<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Validation Language Lines
	|--------------------------------------------------------------------------
	|
	| The following language lines contain the default error messages used by
	| the validator class. Some of these rules have multiple versions such
	| as the size rules. Feel free to tweak each of these messages here.
	|
	*/

	"accepted"             => ":attribute 를 접수하여야 합니다.",
	"active_url"           => ":attribute 는 정확치않은 URL입니다.",
	"after"                => ":attribute 는 :date 다음의 날자여야 합니다.",
	"alpha"                => ":attribute 는 문자여야 합니다.",
	"alpha_dash"           => ":attribute 는 문자, 수자, 밑선만이 가능합니다.",
	"alpha_num"            => ":attribute 는 문자와 수자만이 가능합니다.",
	"array"                => ":attribute 는 배렬이여야 합니다.",
	"before"               => ":attribute 는 :date 전에 날자여야 합니다.",
	"between"              => [
		"numeric" => ":attribute 는 :min 과 :max 사이에 있어야 합니다.",
		"file"    => ":attribute 의 크기는 :min 과 :max 키로바이트 사이에 있어야 합니다 .",
		"string"  => ":attribute 의 문자개수는 :min 과 :max 사이이여야 합니다.",
		"array"   => ":attribute 는 :min 과 :max 개 사이의  항목이여야 합니다.",
	],
	"boolean"              => ":attribute 마당은  true 혹은 false 이여야 합니다.",
	"confirmed"            => ":attribute 확인마당이 일치하지 않습니다.",
	"date"                 => ":attribute 는 날자형식이 틀립니다.",
	"date_format"          => ":attribute 는 :format 과 일치하지 않습니다.",
	"different"            => ":attribute 는 :other 와 달라야 합니다.",
	"digits"               => ":attribute 는 :digits 수자이여야 합니다.",
	"digits_between"       => ":attribute 는 :min 과 :max 수자사이에 놓여야 합니다.",
	"email"                => ":attribute 는 옳은 전자우편주소여야 합니다.",
	"filled"               => ":attribute 마당은 필수입니다.",
	"exists"               => "선택한 :attribute 는 틀립니다.",
	"image"                => ":attribute 는 화상이여야 합니다.",
	"in"                   => "선택한 :attribute 는 틀립니다.",
	"integer"              => ":attribute 는 옹근수여야 합니다.",
	"ip"                   => ":attribute 는 IP address 가 틀립니다.",
	"max"                  => [
		"numeric" => ":attribute는 :max 보다 커야 합니다.",
		"file"    => ":attribute는  :max KB 보다 클수 없습니다.",
		"string"  => ":attribute는 문자개수가  :max 보다 클수 없습니다.",
		"array"   => ":attribute는  항목개수가 :max 보다 클수 없습니다.",
	],
	"mimes"                => ":attribute은 :values 형의 파일이여야 합니다.",
	"min"                  => [
		"numeric" => ":attribute 는 적어도 :min 이여야 합니다..",
		"file"    => ":attribute 는 적어도 :min KB이여야 합니다.",
		"string"  => ":attribute 는 적어도 :min 문자여야 합니다.",
		"array"   => ":attribute 는 적어도 :min 항목이여야 합니다.",
	],
	"not_in"               => "선택한 :attribute 는 틀립니다.",
	"numeric"              => ":attribute 는 수자이여야 합니다.",
	"regex"                => ":attribute 형식은 틀립니다.",
	"required"             => ":attribute 마당은 필수입니다.",
	"required_if"          => ":attribute 마당은 :other 가 :value 이라면 필수입니다.",
	"required_with"        => ":attribute 마당은 :values 가 존재한다면 필수입니다.",
	"required_with_all"    => ":attribute 마당은 :values 가 존재한다면 필수입니다.",
	"required_without"     => ":attribute 마당은 :values 가 없다면 필수입니다.",
	"required_without_all" => ":attribute 마당은 :values 들이 없다면 필수입니다.",
	"same"                 => ":attribute 와 :other 는 일치하여야 합니다.",
	"size"                 => [
		"numeric" => ":attribute 는 :size 여야 합니다.",
		"file"    => ":attribute 는 :size KB여야 합니다..",
		"string"  => ":attribute 는 :size 문자여야 합니다.",
		"array"   => ":attribute 는 :size 항목이여야 합니다.",
	],
	"unique"               => ":attribute 는 이미 선택되였습니다.",
	"url"                  => ":attribute 형식은 틀립니다.",
	"timezone"             => ":attribute 는 옳은 령역이여야 합니다.",

	/*
	|--------------------------------------------------------------------------
	| Custom Validation Language Lines
	|--------------------------------------------------------------------------
	|
	| Here you may specify custom validation messages for attributes using the
	| convention "attribute.rule" to name the lines. This makes it quick to
	| specify a specific custom language line for a given attribute rule.
	|
	*/

	'custom' => [
		'attribute-name' => [
			'rule-name' => '전용통보문',
		],
	],

	/*
	|--------------------------------------------------------------------------
	| Custom Validation Attributes
	|--------------------------------------------------------------------------
	|
	| The following language lines are used to swap attribute place-holders
	| with something more reader friendly such as E-Mail Address instead
	| of "email". This simply helps us make messages a little cleaner.
	|
	*/

	'attributes' => [],

];
