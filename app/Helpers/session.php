<?php
/* Mar 6, 2016 - Sunlight */
if ( !function_exists('add_message') )
{
	function add_message($msg, $type, $show_toastr = true, $tastr_options = [])
	{
		$msgs = session('msgs');

		$msgs[] = [
			'msg' 			=> $msg,
			'type' 			=> $type,
			'show_toastr' 		=> $show_toastr,
			'tastr_options' => $tastr_options
		];

		session(['msgs' => $msgs]);
	}
}

if ( !function_exists('show_messages') )
{
	function show_messages($return = false, $show_toastr = true, $fade = true) {
		$alert_classes = [
			'error' 	=> 'g-bg-red-opacity-0_1 g-color-lightred rounded-0',
			'danger' 	=> 'g-bg-red-opacity-0_1 g-color-lightred rounded-0',
			'success' 	=> 'g-bg-primary-opacity-0_1 g-color-primary rounded-0',
			'warning' 	=> 'g-bg-orange-opacity-0_1 g-color-orange rounded-0',
			'info' 		=> 'g-bg-orange-opacity-0_1 g-color-orange rounded-0',
		];

		$groups = [
			'success' => [],
			'info' => [],
			'warning' => [],
			'danger' => [],
		];

		$msgs = session('msgs');

		if (empty($msgs)) {
			echo "";
			return;
		}

		if ( empty($msgs) ) {
			return '';
		}

		$html = '';
		foreach ($msgs as $msg) {
			$type = $msg['type'];

			if (!in_array($type, array_keys($alert_classes)))
				continue;

			$html .= '<div class="messages ' . ($show_toastr && $msg['show_toastr']?'toastr-messages':'') . '">';
			$html .= '<div class="alert alert-' . $type . ' show g-font-size-13' . ($fade ? ' fade' : ' ') . $alert_classes[$type] . '">';
			$html .= '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
			$html .= '<ul>';
			
			$html_options = [];
			$options = $msg['tastr_options'];

			// show_toastr is false, this alert will be static, not floating.
			if (!$msg['show_toastr'])
				$html_options[] = 'data-ignore-toastr="1"';

			foreach ($options as $key => $value) {
				$html_options[] = "data-toastr_$key=\"$value\"";
			}
			$html .= '<li ' . implode(' ', $html_options) . '>' . $msg['msg'] . '</li>';

			$html .= '</ul></div>';
			$html .= '</div>';
		}

		session()->forget('msgs');

		if ($return)
			return $html;
		else
			echo $html;
	}
}

use iJobDesk\Models\UserIgnoredWarning;

/**
 * @author KCG
 * @since July 11, 2017
 */
if ( !function_exists('add_breadcrumb') ) {
	function add_breadcrumb($title, $url = null) {
		$breadcrumbs = session('breadcrumbs');
		
		if (!$breadcrumbs)
			$breadcrumbs = [];

		if (!empty($url))
			$breadcrumbs[] = [$title, $url];
		else
			$breadcrumbs[] = [$title];

		session(['breadcrumbs' => $breadcrumbs]);
	}
}

if ( !function_exists('show_breadcrumb') ) {
	function show_breadcrumb() {
		$html = '';
		
		$breadcrumbs = session('breadcrumbs');
		foreach ($breadcrumbs as $breadcrumb) {
			if (count($breadcrumb) == 2)
				$html .= '<li><a href="'.$breadcrumb[1].'">'.$breadcrumb[0].'</a><i class="fa fa-circle"></i></li>';
			else
				$html .= '<li class="active">'.$breadcrumb[0].'</li>';
		}

		session()->forget('breadcrumbs');

		echo $html;
	}
}

if ( !function_exists('reset_breadcrumb') ) {
	function reset_breadcrumb() {
		session()->forget('breadcrumbs');
	}
}

/**
 * Add system notifications, warnings.
 * @author KCG
 * @since Jan 28, 2018
 */
if ( !function_exists('add_warning') ) {
	function add_warning($message, $type, $target_id = null) {
		$warnings = session('warnings');

		if (!$warnings)
			$warnings = [];

		$warnings[] = [
			'message' 		=> $message,
			'type' 			=> $type,
			'target_id' 	=> $target_id,
		];

		session(['warnings' => $warnings]);
	}
}

if ( !function_exists('show_warnings') )
{
	function show_warnings() {
		// First, show system notifications
		$html = view('layouts.section.notification', ['system_notifications' => session('system_notifications')]);

		$auth = Auth::user();
		if ($auth && $auth->isAdmin())
			$html = '';

		// Then show warnings.
		$warnings = session('warnings');
		if (empty($warnings)) {
			echo $html;
			return;
		}

		foreach ($warnings as $warning) {
			$msg_type = UserIgnoredWarning::msgTypes()[$warning['type']];

			$html .= '<div class="alert alert-'.$msg_type.' warning type-' . $warning['type'] . '" role="alert">';

			$html .= '<button type="button" class="close" data-dismiss="alert" aria-hidden="true"';

			// For financial suspended warning
			if ( $warning['type'] != 2 ) {
				$html .= ' data-url="'.route('user.ignore_warning', ['type' => $warning['type'], 'target_id' => $warning['target_id']]).'"';
			}

			$html .= '>&times;</button>';

			$html .= $warning['message'];

			$html .= '</div>';
		}

		session()->forget('warnings');

		echo $html;
	}
}