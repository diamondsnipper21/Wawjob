<?php namespace iJobDesk\Models;

use Illuminate\Database\Eloquent\Model as BaseModel;
use Auth;

class Model extends BaseModel {
	public static function find($id, $columns = array('*')) {
		$instance = new static();
		$user = Auth::user();

		$builder = static::query();
		
		if ( $user && $user->isAdmin() && in_array('withTrashed', get_class_methods(get_class($instance))) )
			$builder->withTrashed();

		return $builder->find($id, $columns);
	}

	public static function findOrFail($id, $columns = array('*')) {
		$instance = new static();
		$user = Auth::user();

		$builder = static::query();
		
		if ( $user && $user->isAdmin() && in_array('withTrashed', get_class_methods(get_class($instance))) )
			$builder->withTrashed();

		return $builder->findOrFail($id, $columns);
	}

	public function hasOne($related, $foreignKey = null, $localKey = null) {
		$instance = new $related();
		$user = Auth::user();

		if ( $user && $user->isAdmin() && in_array('withTrashed', get_class_methods(get_class($instance))) )
			return parent::hasOne($related, $foreignKey, $localKey)->withTrashed();

		return parent::hasOne($related, $foreignKey, $localKey);
	}

	public function hasMany($related, $foreignKey = null, $localKey = null) {
		$instance = new $related();
		$user = Auth::user();

		if ( $user && $user->isAdmin() && in_array('withTrashed', get_class_methods(get_class($instance))) )
			return parent::hasMany($related, $foreignKey, $localKey)->withTrashed();

		return parent::hasMany($related, $foreignKey, $localKey);
	}

	public function belongsTo($related, $foreignKey = null, $otherKey = null, $relation = null) {
		$instance = new $related();
		$user = Auth::user();

		if ( $user && $user->isAdmin() && in_array('withTrashed', get_class_methods(get_class($instance))) )
			return parent::belongsTo($related, $foreignKey, $otherKey, $relation)->withTrashed();

		return parent::belongsTo($related, $foreignKey, $otherKey, $relation);
	}

	/**
	 * Get user by unique id.
	 */
	public static function findByUnique($unique_id) {
		$model = self::where('unique_id', $unique_id)->first();
		if (!$model)
			abort(404);

		return $model;
	}
}