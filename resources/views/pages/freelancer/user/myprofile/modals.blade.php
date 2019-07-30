<?php

/**
 * @author KCG
 * @since Feb 3, 2018
 */

?>

@if (!$current_user->isSuspended())

	<div id="modal_container"></div>

	@include ('pages.freelancer.user.myprofile.modal.portfolio')
	@include ('pages.freelancer.user.myprofile.modal.certification')
	@include ('pages.freelancer.user.myprofile.modal.education')
	@include ('pages.freelancer.user.myprofile.modal.employment')
	@include ('pages.freelancer.user.myprofile.modal.experience')

@endif