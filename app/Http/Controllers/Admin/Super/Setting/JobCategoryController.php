<?php namespace iJobDesk\Http\Controllers\Admin\Super\Setting;
/**
 * @author KCG
 * @since July 28, 2017
 * Escrow Page
 */
use iJobDesk\Http\Requests;
use iJobDesk\Http\Controllers\Admin\AdminController as BaseController;
use Illuminate\Http\Request;

use App;
use DB;
use Auth;
use Config;

use iJobDesk\Models\User;
use iJobDesk\Models\Views\ViewUser;
use iJobDesk\Models\Category;

class JobCategoryController extends BaseController {

    public function __construct() {
        parent::__construct();

        $this->page_title = 'Job Categories';

        view()->share([
            'page_title' => $this->page_title
        ]);
    }

    public function index(Request $request) {
        add_breadcrumb('Job Categories');

        $action = $request->input('_action');
        
        if ($action == 'DELETE') {
            $id = $request->input('_id');
            Category::find($id)->delete();
            Category::where('parent_id', $id)->delete();

            add_message('The category has been deleted.', 'success');
        }

        $job_categories = Category::projectCategories();
        $jtree_categories = $this->getFormatJtree($job_categories);

        // create root category
        $jtree_categories = [
            'id'    => Category::ROOT_ID . '_' . '0',
            'text'  => Category::ROOT_NAME,
            'icon'  => 'fa fa-folder icon-state-info',
            'state' => ['opened' => false],
            'children' => $jtree_categories 
        ];

        return view('pages.admin.super.settings.job_categories', [
            'page' => 'super.settings.job_categories',
            'job_categories'    => $job_categories,
            'jtree_categories'  => $jtree_categories
        ]);
    }

    private function getFormatJtree($categories) {
        $jtree_categories = [];
        foreach ($categories as $cat) {
            $jtree_category = $cat;
            $jtree_category['id']  =  $cat['id'] . '_' . $cat['parent_id'];
            $jtree_category['text'] = parse_multilang($cat['name']);
            $jtree_category['icon'] = 'fa fa-folder icon-state-info';
            $jtree_category['state'] = ['opened' => false];

            if (!empty($cat['children'])) {
                $jtree_category['children'] = $this->getFormatJtree($cat['children']);
            }

            $jtree_categories[] = $jtree_category;
        }

        return $jtree_categories;
    }

    public function re_order(Request $request) {
        $data = $request->input('data');
        $tree_categories = $data[0]['children'];

        Category::re_order($tree_categories);

        return $this->index($request);
    }

    public function edit(Request $request, $id = null) {
        $job_category = new Category();
        $action = $request->input('action');

        // edit or add new
        if ($action == 'save') {
            $names      = $request->input('name');
            $name_en    = $names['EN'];
            $name_kp    = $names['KP']?$names['KP']:$names['EN'];
            $name_ch    = $names['CH']?$names['CH']:$names['EN'];
            $name_string= "<en>$name_en</en><KP>$name_kp</KP><CH>$name_ch</CH>";
            // edit
            if (!empty($id)) {
                $job_category = Category::find($id);
            } else {
                $job_category->parent_id = $request->input('parent_id');
                $job_category->order = 10000;
            }

            $job_category->type = 0; // job category
            $job_category->name = $name_string;
            $job_category->desc = $request->input('desc');

            $job_category->save();

            if (!$id)
                add_message('Successfully added new category.', 'success');
            else
                add_message('Successfully updated category.', 'success');

            if (empty($id)) {
                $id = $job_category->parent_id;
                $action = 'add';
            } else {
                $action = 'edit';
            }
        }

        if (!empty($id) && $id != Category::ROOT_ID) {
            $job_category = Category::find($id);

            if (!$job_category)
                abort(404);
        }

        $parent_category = null;
        if ($action == 'add') { // if "add" mode, current id is one for parent
            $parent_category = $job_category;
            $job_category = new Category();
        }

        return view('pages.admin.super.settings.job_category.modal', [
            'id' => $id,
            'action' => $action,
            'job_category' => $job_category,
            'parent_category' => $parent_category
        ]);
    }
}