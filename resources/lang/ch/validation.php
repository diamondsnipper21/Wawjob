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

	"accepted"             => "必须接受:attribute。",
	"active_url"           => ":attribute是无效的URL。",
	"after"                => ":attribute必须是:date之后的日期。",
	"alpha"                => ":attribute只能包含字母。",
	"alpha_dash"           => ":attribute只能包含字母，数字和破折号。",
	"alpha_num"            => ":attribute只能包含字母和数字。",
	"array"                => ":attribute必须是数组。",
	"before"               => ":attribute必须是:date之前的日期。",
	"between"              => [
		"numeric" => ":attribute必须是:min到:max之间。",
		"file"    => ":attribute必须是:min到:max千字节之间。",
		"string"  => ":attribute必须包含:min到:max个字符之间。",
		"array"   => ":attribute必须包含:min到:max个项目之间。",
	],
	"boolean"              => ":attribute字段必须为真或假。",
	"confirmed"            => ":attribute确认不匹配。",
	"date"                 => ":attribute是无效的日期。",
	"date_format"          => ":attribute与格式:format不匹配。",
	"different"            => ":attribute和:other必须不同。",
	"digits"               => ":attribute必须是:digits位数字。",
	"digits_between"       => ":attribute必须是:min到:max位数字之间。",
	"email"                => ":attribute必须是有效的电子邮件地址。",
	"filled"               => ":attribute字段是必须的。",
	"exists"               => "选定的:attribute无效。",
	"image"                => ":attribute必须是图像。",
	"in"                   => "选定的:attribute无效。",
	"integer"              => ":attribute必须是整数。",
	"ip"                   => ":attribute必须是有效的IP地址。",
	"max"                  => [
		"numeric" => ":attribute不得大于:max。",
		"file"    => ":attribute不得超过:max千字节。",
		"string"  => ":attribute不得超过:max个字符。",
		"array"   => ":attribute不得超过:max个项目。",
	],
	"mimes"                => ":attribute必须是以下类型的文件： :values。",
	"min"                  => [
		"numeric" => ":attribute必须是至少:min。",
		"file"    => ":attribute必须是至少:min千字节。",
		"string"  => ":attribute必须包含至少:min个字符。",
		"array"   => ":attribute必须包含至少:min个项目。",
	],
	"not_in"               => "选定的:attribute无效。",
	"numeric"              => ":attribute必须是数字。",
	"regex"                => ":attribute格式无效。",
	"required"             => ":attribute字段是必须的。",
	"required_if"          => "当:other为:value时，:attribute字段是必需的。",
	"required_with"        => "当:values存在时，:attribute字段是必需的。",
	"required_with_all"    => "当:values存在时，:attribute字段是必需的。",
	"required_without"     => "当:values不存在时，:attribute字段是必需的。",
	"required_without_all" => "当:values都不存在时，:attribute字段是必需的。",
	"same"                 => ":attribute和:other必须匹配。",
	"size"                 => [
		"numeric" => ":attribute必须是:size。",
		"file"    => ":attribute必须是:size千字节。",
		"string"  => ":attribute必须包含:size个字符。",
		"array"   => ":attribute必须包含:size个项目。",
	],
	"unique"               => ":attribute已被采用。",
	"url"                  => ":attribute格式无效。",
	"timezone"             => ":attribute必须是有效区域。",

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
			'rule-name' => '自定义消息',
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
