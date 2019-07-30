<?php namespace iJobDesk\Http\Controllers\Admin\Super\Setting;
/**
 * @author KCG
 * @since July 7, 2017
 * Faq Management for Super Manager
 */
use iJobDesk\Http\Requests;
use iJobDesk\Http\Controllers\Admin\AdminController as BaseController;
use Illuminate\Http\Request;

use App;
use DB;
use iJobDesk\Models\Faq;
use iJobDesk\Models\Category;
use iJobDesk\Models\Views\ViewUser;

class FaqController extends BaseController {

    public function __construct() {
        $this->page_title = 'Faqs';
        parent::__construct();
    }

    public function listing(Request $request) {
        add_breadcrumb('Faqs');

        $action = $request->input('_action');

        if ($action == 'DELETE') {
            $ids = $request->input('ids');

            Faq::whereIn('id', $ids)->delete();
            add_message(sprintf('The %d Faq(s) has been deleted.', count($ids)), 'success');
        }

        $sort     = $request->input('sort', 'id');
        $sort_dir = $request->input('sort_dir', 'desc');

        $faqs = Faq::orderBy($sort, $sort_dir);

        // Filtering
        $filter = $request->input('filter');

        // By Title
        if (!empty($filter['title'])) {
            $faqs->where('title', 'LIKE', '%'.trim($filter['title']).'%');
        }

        // By Content
        if (!empty($filter['content'])) {
            $faqs->where('content', 'LIKE', '%'.trim($filter['content']).'%');
        }

        // By Type
        if (isset($filter) && $filter['type'] != '') {
            $faqs->where('type', $filter['type']);
        }

        // By Visible
        if (isset($filter) && $filter['visible'] != '') {
            $faqs->where('visible', $filter['visible']);
        }

        // By Category
        if (isset($filter) && $filter['cat_id'] != '') {
            $faqs->where('cat_id', $filter['cat_id']);
        }

        // By Order
        if (isset($filter) && $filter['order'] != '') {
            $faqs->where('order', $filter['order']);
        }

        $categories = Category::where('type', Category::TYPE_FAQ)->get();
        $request->flashOnly('filter');

        return view('pages.admin.super.settings.faqs', [
            'page' => 'super.settings.faqs',
            'faqs' => $faqs->paginate($this->per_page),
            'categories' => $categories,
            'sort'   => $sort,
            'sort_dir'   => '_' . $sort_dir
        ]);     
    }

    public function edit(Request $request, $faq_id = null) {
        $faq = new Faq();
        $action = $request->input('action');

        if (!empty($faq_id)) {
            $faq = Faq::find($faq_id);
        } else {
            $faq->order = 0;
        }

        // edit or add new
        if ($action == 'save') {
            $names      = $request->input('name');
            $name_en    = $names['EN'];
            $name_kp    = $names['KP']?$names['KP']:$names['EN'];
            $name_ch    = $names['CH']?$names['CH']:$names['EN'];
            $name_string= "<en>$name_en</en><KP>$name_kp</KP><CH>$name_ch</CH>";

            $descs      = $request->input('desc');
            $desc_en    = $descs['EN'];
            $desc_kp    = $descs['KP']?$descs['KP']:$descs['EN'];
            $desc_ch    = $descs['CH']?$descs['CH']:$descs['EN'];
            $desc_string= "<en>$desc_en</en><KP>$desc_kp</KP><CH>$desc_ch</CH>";
            
            $faq->title = $name_string;
            $faq->content = $desc_string;
            $faq->type = $request->input('type');
            $faq->visible = $request->input('visible');
            $faq->cat_id = $request->input('cat_id');
            $faq->order = $request->input('order');

            $faq->save();

            if (!$faq_id)
                add_message('Successfully added new category.', 'success');
            else
                add_message('Successfully updated category.', 'success');
        }

        if (empty($faq_id)) {
            $action = 'add';
        } else {
            $action = 'edit';
        }


        $categories = Category::where('type', Category::TYPE_FAQ)->get();

        return view('pages.admin.super.settings.faq.modal', [
            'faq_id' => $faq_id,
            'action' => $action,
            'categories' => $categories,
            'faq' => $faq,
        ]);
    }
}