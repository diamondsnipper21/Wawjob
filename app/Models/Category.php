<?php namespace iJobDesk\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model {

    use SoftDeletes;

    const ROOT_ID = 10000000;
    const ROOT_NAME = 'ROOT';

    /**
    * Category types
    */
    const TYPE_PROJECT = 0;
    const TYPE_QA = 1;
    const TYPE_MAINTANANCE = 2;
    const TYPE_EN_LEVEL = 4; // English Level

    public static $type_list = [
        self::TYPE_PROJECT,
        self::TYPE_QA,
        self::TYPE_MAINTANANCE,
        self::TYPE_EN_LEVEL
    ];

    /**
    * The table associated with the model.
    *
    * @var string
    */
    protected $table = 'categories';

    /**
    * The attributes that should be mutated to dates.
    *
    * @var array
    */
    protected $dates = ['deleted_at'];

    /**
    * Indicates if the model should be timestamped.
    *
    * @var bool
    */
    public $timestamps = false;

    /**
    * Get the parent category
    *
    * @return mixed
    */
    public function parent()
    {
        return $this->hasOne('iJobDesk\Models\Category', 'id', 'parent_id');
    }

    /**
    * Get the categories.
    *
    * @param integer $type The category type
    * @return mixed
    */
    public static function byType($type = -1)
    {
        $categories = $type < 0 ? self::where('type', '>', '-1') : self::where('type', $type);
        // Order
        $categories = $categories->orderBy('parent_id','asc')->orderBy('order', 'asc');

        return self::tree2tier($categories->get());
    }

    /**
    * Get the 2 tier tree view of categories.
    *
    * @param Collection $categories The categories
    * @return mixed
    */
    public static function tree2tier($categories)
    {
        if (!$categories) {
            return false;
        }

        $result = [];
        foreach ($categories as $category) {
            $cnt_projects = $category->projects->where('accept_term', Project::ACCEPT_TERM_YES)
            ->where('is_public', Project::STATUS_PUBLIC)
            ->where('status', Project::STATUS_OPEN)
            ->count();

            if (!$category->parent_id) {
                $result[$category->id] = $category->attributes;
                $result[$category->id]['cnt_projects'] = $cnt_projects;
                $result[$category->id]['cnt_projects_with_children'] = $cnt_projects;
                continue;
            }

            if (!isset($result[$category->parent_id]['children'])) {
                $result[$category->parent_id]['children'] = [];
            }

            $result[$category->parent_id]['children'][$category->id] = $category->attributes;
            $result[$category->parent_id]['children'][$category->id]['cnt_projects'] = $cnt_projects;

            if (isset($result[$category->parent_id]['cnt_projects_with_children']))
                $result[$category->parent_id]['cnt_projects_with_children'] += $cnt_projects;
            else {
                $result[$category->parent_id]['cnt_projects_with_children'] = 0;
            }
        }

        return $result;
    }

    /**
    * Get the project categories.
    *
    * @return mixed
    */
    public static function projectCategories()
    {
        return self::byType(self::TYPE_PROJECT);
    }

    /**
    * Get the project categories.
    *
    * @return mixed
    */
    public static function qaCategories()
    {
        return self::byType(self::TYPE_QA);
    }

    /**
    * Get the project categories.
    *
    * @return mixed
    */
    public static function maintanaceCategories()
    {
        return self::byType(self::TYPE_MAINTANANCE);
    }

    /**
    * Get the sub categories.
    *
    * @return mixed
    */
    public static function children($id)
    {
        if (!$id) {
            return false;
        }

        return self::where('parent_id', $id)->get();
    }

    /**
    * Get the projects.
    *
    * @return mixed
    */
    public function projects()
    {
        return $this->hasMany('iJobDesk\Models\Project', 'category_id');
    }

    public static function getNameById($id) {
        $category = self::find($id);

        if ( $category ) {
            return parse_multilang($category->name);
        }

        return false;
    }

    /**
     * @author KCG
     * @since 20170530
     * Get english levels
     */
    public static function getEnLevels() {
        return self::byType(self::TYPE_EN_LEVEL);
    }

    /**
     * @author KCG
     * @since 20170807
     * re-ordering
     */
    public static function re_order($job_categories, $parent_id = 0) {
        if (empty($job_categories))
            return;
        
        $order = 1;
        foreach ($job_categories as $job_category) {
            $parts = explode('_', $job_category['id']);
            $id = $parts[0];

            $category = self::find($id);
            $category->order = $order++;
            $category->parent_id = $parent_id;

            $category->save();

            if (!empty($job_category['children']))
                self::re_order($job_category['children'], $id);
        }
    }
}