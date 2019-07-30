<?php namespace iJobDesk\Models;

use DB;
use Illuminate\Validation\Rule;

use iJobDesk\Models\Category;

class UserPortfolio extends Model {

    const THUMB_WIDTH   = 170;
    const THUMB_HEIGHT  = 123;

    //
    /**
    * The table associated with the model.
    *
    * @var string
    */
    protected $table = 'user_portfolios';

    /**
    * Indicates if the model should be timestamped.
    *
    * @var bool
    */
    public $timestamps = false;

    protected $appends = array('image_url', 'thumb_url', 'imploded_files');

    /**
    * Get the user's portfolios.
    * @return array
    */
    public static function getPortfolio($user_id)
    {
        try {
            $portfolios = self::where('user_id', $user_id)->get();
            return $portfolios;
        } catch(Exception $e) {
            return [];
        }
    }
    /**
    * Get the user portfolio's categories.
    * @return array
    */
    public static function getCategories($user_id)
    {
        try {
            $categories = DB::select("SELECT id, name FROM categories WHERE id IN (SELECT cat_id FROM user_portfolios WHERE user_id=?)", [$user_id]);
            return $categories;
        }
        catch(Exception $e) {
            return [];
        }
    }

    public function files() {
        return $this->hasMany('iJobDesk\Models\File', 'target_id', 'id')
                    ->where('files.type', File::TYPE_USER_PORTFOLIO);
    }

    public function getImageUrlAttribute() {
        return portfolio_url($this);
    }

    public function getThumbUrlAttribute() {
        return portfolio_thumb_url($this);
    }

    public function getImplodedFilesAttribute() {
        $file = File::getPortfolio($this->id);

        if (!$file)
            return '';
        return implode_bracket([$file->id]);
    }

    public function category() {
        return $this->hasOne('iJobDesk\Models\Category', 'id', 'cat_id');
    }

    public static function getValidator() {
        $validator = [
            'profile.portfolio.cat_id' => [
                'required',
                function($attribute, $value, $fail) {
                    $valid = Category::where('id', $value)
                                     ->where('type', Category::TYPE_PROJECT)
                                     ->exists();
                    if (!$valid) {
                        return $fail('The category is invalid.');
                    }
                }
            ],
            'profile.portfolio.description' => 'required|max:255'
        ];

        return $validator;
    }
}
