<?php namespace iJobDesk\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class StaticPage extends Model {

    use SoftDeletes;
    
    /**
    * The table associated with the model.
    *
    * @var string
    */
    protected $table = 'static_pages';

    /**
    * The attributes that should be mutated to dates.
    *
    * @var array
    */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /**
    * Indicates if the model should be timestamped.
    *
    * @var bool
    */
    public $timestamps = true;

    const STATUS_NO_PUBLISH  = 0;
    const STATUS_PUBLISH     = 1;
    const STATUS_DELETE      = 2;

    function __construct() {
        parent::__construct();
    }

    public static function enableStatusChanged($static_page) {
        $attributes = '';

        if ($static_page->is_publish == self::STATUS_NO_PUBLISH) {
            $attributes .= ' data-status-' . self::STATUS_PUBLISH . '=true';
            $attributes .= ' data-status-' . self::STATUS_DELETE . '=true';
        } elseif ($static_page->is_publish == self::STATUS_PUBLISH) {
            $attributes .= ' data-status-' . self::STATUS_NO_PUBLISH . '=true';
            $attributes .= ' data-status-' . self::STATUS_DELETE . '=true';
        } else {
            $attributes .= '';
        }

        return $attributes;
    }

}