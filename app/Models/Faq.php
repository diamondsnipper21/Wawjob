<?php namespace iJobDesk\Models;

use DB;

class Faq extends Model {

    /**
    * The table associated with the model.
    *
    * @var string
    */
    protected $table = 'faqs';

    /**
    * Indicates if the model should be timestamped.
    *
    * @var bool
    */
    public $timestamps = false;


    /**
    * Get Faqs with category id or faq id.
    * @return mixed
    */
    public static function get($cat_id, $type = [0, 1, 2], $faq_id = "")
    {
        if ($faq_id != "") {
            return self::where('id', $faq_id)->get();
        } else {
            return self::where('cat_id', $cat_id)->whereIn('type', $type)->orderBy('order')->get();
        }
    }

    /**
    * Get category count.
    * @return mixed
    */
    public static function getCatCount($type = [0, 1, 2])
    {
        $result = [];
        $count = self::whereIn('type', $type)->groupBy('cat_id')->select('cat_id', DB::raw('count(cat_id) as cnt'))->get();
        foreach ($count as $cat) {
            $result[$cat['cat_id']] = $cat['cnt'];
        }
        return $result;
    }

    /**
    * Add the FAQ List.
    * @return mixed
    */
    public static function saveModified($changes)
    {
        $result = [];
        $order = [];
        try {
            if (isset($changes["order"])) {
                $order = $changes["order"];
            }
            foreach ($changes as $key => $faq) {
                if ($key != "order" && isset($order[$faq['cat_id']][$faq["id"]])) {
                    $order_ind = $order[$faq['cat_id']][$faq["id"]];
                    if (is_numeric($faq["id"])) {
                        if (isset($faq['remove']) && $faq['remove'] = true) {
                            self::where('id', $faq["id"])
                            ->delete();

                        } else {
                            self::where('id', $faq["id"])
                            ->update(['title'   => $faq['title'], 
                                'content' => $faq['content'], 
                                'type'    => $faq['type'],
                                'visible' => 1,//$faq['visible'],
                                'cat_id'  => $faq['cat_id'],
                                'order'   => $order_ind,
                                ]);  
                        }

                    } else {
                        $result[$faq["id"]] = self::insertGetId(['title'    => $faq['title'], 
                            'content' => $faq['content'], 
                            'type'    => $faq['type'],
                            'visible' => 1,//$faq['visible'],
                            'cat_id'  => $faq['cat_id'],
                            'order'   => $order_ind,
                            ]);
                    }
                }
            }
            foreach ($order as $categories) {
                foreach ($categories as $k => $c) {
                    if (is_numeric($k)) {
                        self::where('id', $k)
                        ->update(['order' => $c]);
                    } else {
                        self::where('id', $result[$k])
                        ->update(['order' => $c]);
                    }
                }
            }
            return $result;
        }
        catch(Exception $e) {
            return false;
        }
        return $result;
    }

    public function category()
    {
        return $this->hasOne('iJobDesk\Models\Category', 'id', 'cat_id');
    }
}
