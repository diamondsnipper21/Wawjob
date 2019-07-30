<?php namespace iJobDesk\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class HelpPage extends Model {

    use SoftDeletes;
    
    /**
    * The table associated with the model.
    *
    * @var string
    */
    protected $table = 'help_pages';

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

    const TYPE_FREELANCER   = 1;
    const TYPE_BUYER        = 2;

    function __construct() {
        parent::__construct();
    }

    public function parent() {
        return $this->hasOne('iJobDesk\Models\HelpPage', 'id', 'parent_id');
    }

    public function second_parent() {
        return $this->hasOne('iJobDesk\Models\HelpPage', 'id', 'second_parent_id');
    }

    public static function enableStatusChanged($page) {
        $attributes = '';

        if ($page->is_publish == self::STATUS_NO_PUBLISH) {
            $attributes .= ' data-status-' . self::STATUS_PUBLISH . '=true';
            $attributes .= ' data-status-' . self::STATUS_DELETE . '=true';
        } elseif ($page->is_publish == self::STATUS_PUBLISH) {
            $attributes .= ' data-status-' . self::STATUS_NO_PUBLISH . '=true';
            $attributes .= ' data-status-' . self::STATUS_DELETE . '=true';
        } else {
            $attributes .= '';
        }

        return $attributes;
    }

    public static function pages($type, $parent = 0) {
        return self::where(function($query) use ($type) {
                        $query->where('type', $type)
                              ->orWhere('type', 0);
                   })
                   ->where(function($query) use ($parent) {
                        $query->where('parent_id', $parent);

                        if ($parent != 0)
                            $query->orWhere('second_parent_id', $parent);
                   })
                   ->orderBy('order')
                   ->orderBy('second_order')
                   ->get();
    }

    public function hasChildren() {
        return count(self::pages($this->type, $this->id)) != 0;
    }

    public function isOutUrl() {
        return strpos($this->slug, 'http') === 0;
    }

    public function url() {
        $slug = $this->slug;

        if (!$slug)
            return 'javascript:void(0)';

        if ($this->isOutUrl())
            return $slug;

        return route('frontend.help.detail', ['slug' => $slug]);
    }
}