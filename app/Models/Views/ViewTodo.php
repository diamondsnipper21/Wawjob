<?php namespace iJobDesk\Models\Views;


use iJobDesk\Models\Todo;
use iJobDesk\Models\User;

class ViewTodo extends Todo {

	/**
	* The table associated with the model.
	*
	* @var string
	*/
	protected $table = 'view_todos';

	/**
	* Indicates if the model should be timestamped.
	*
	* @var bool
	*/
	public $timestamps = false;

	public function getAssignerNames() {
		$assigners = $this->assigners;
		$assigner_names = [];

		$index = 0;
		foreach (explode(',', $this->assigner_names) as $name) {
			if (empty($name))
				continue;

			if ( isset($assigners[$index]) ) {
				$assigner = $assigners[$index];
				$assigner_names[] = $assigner->getUserNameWithIcon();

				$index++;
			}
		}

		return implode('<br />', $assigner_names);
	}
}