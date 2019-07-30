<?php namespace iJobDesk\Http\Controllers\Admin\Super\Setting;

use iJobDesk\Http\Requests;
use iJobDesk\Http\Controllers\Admin\AdminController as BaseController;
use Illuminate\Http\Request;

use App;
use DB;
use Auth;
use Config;

use iJobDesk\Models\HelpPage;

class HelpPageController extends BaseController {

    public function __construct() {
        parent::__construct();

        $this->page_title = 'Help Pages';

        view()->share([
            'page_title' => $this->page_title
        ]);
    }

    public function index(Request $request) {
        add_breadcrumb('Help Pages');

        if ($request->method('post')) {

            $action = $request->input('_action');

            if ($action == 'CHANGE_STATUS') {
                $status = $request->input('page_action');
                $page_ids = $request->input('ids');
                
                if ($status == HelpPage::STATUS_NO_PUBLISH) {
                    foreach ($page_ids as $page_id) {
                        HelpPage::where('id', $page_id)
                                  ->update(['status' => HelpPage::STATUS_NO_PUBLISH]);
                    }

                    add_message(sprintf('The %d Help Pages has not been published.', count($page_ids)), 'success');
                }
                elseif ($status == HelpPage::STATUS_PUBLISH) {
                    foreach ($page_ids as $page_id) {
                        HelpPage::where('id', $page_id)
                                  ->update(['status' => HelpPage::STATUS_PUBLISH]);
                    }

                    add_message(sprintf('The %d Help Pages has been published.', count($page_ids)), 'success');
                }
                elseif ($status == HelpPage::STATUS_DELETE) {
                    foreach ($page_ids as $page_id) {
                        HelpPage::where('id', $page_id)
                                ->delete();
                    }

                    add_message(sprintf('The %d Help Pages has been deleted.', count($page_ids)), 'success');
                }
            }
        }

        $sort     = $request->input('sort', 'updated_at');
        $sort_dir = $request->input('sort_dir', 'desc');

        $help_pages = HelpPage::orderBy($sort, $sort_dir)
                              ->orderBy('title', 'asc');                    

        // Filtering
        $filter = $request->input('filter');

        // By title
        if (!empty($filter['title'])) {
            $help_pages->where('title', 'LIKE', '%'.trim($filter['title']).'%');
        }

        // By content
        if (!empty($filter['content'])) {
            $help_pages->where('content', '=', $filter['content']);
        }

        // By parent
        if (!empty($filter['parent_id'])) {
            $help_pages->where('parent_id', intval($filter['parent_id']));
        }

        // By parent
        if (!empty($filter['second_parent_id'])) {
            $help_pages->where('second_parent_id', intval($filter['second_parent_id']));
        }

        // By type
        if ($filter['type'] != '' && $filter['type'] != 0) {
            $help_pages->where('type', '=', $filter['type']);
        }

        // By status
        if ($filter['status'] != '') {
            $help_pages->where('status', '=', $filter['status']);
        }        

        // Get parent pages
        $parent_pages = HelpPage::where('parent_id', 0)
                                ->orderBy('order')
                                ->get();

        if ( count($parent_pages) ) {
            foreach ( $parent_pages as $k => $page ) {
                $sub_pages = HelpPage::where('parent_id', $page->id)
                                     ->orderBy('order')
                                     ->where('content', '{"en":null,"ch":null}')
                                     ->get();

                $parent_pages[$k]->child = $sub_pages;
            }
        }

        $request->flashOnly('filter');

        return view('pages.admin.super.settings.help_pages', [
            'page' => 'super.settings.help_pages',
            'help_pages' => $help_pages->paginate($this->per_page),
            'parent_pages' => $parent_pages,
            'sort'   => $sort,
            'sort_dir'   => '_' . $sort_dir,
            'config' => Config::get('settings'),
        ]);
    }

    public function edit(Request $request, $id = null) {

        if ($id) {
            $help_page = HelpPage::find($id);
        }
        else {
            $help_page = new HelpPage();
        }

        $title     = $request->input('title');
        $content   = $request->input('content');

        if ($request->method('post') && $title) {
            $help_page->title     = encode_json_multilang($title);
            $help_page->parent_id        = $request->input('parent_id');
            $help_page->second_parent_id = $request->input('second_parent_id');
            $help_page->order            = $request->input('order');
            $help_page->second_order     = $request->input('second_order');
            $help_page->slug             = $request->input('slug');
            $help_page->type             = $request->input('type');
            $help_page->content          = encode_json_multilang($content);

            if ($help_page->save()) {
                if ($id)
                    add_message('Ths help page has been updated successfully.', 'success');
                else
                    add_message('Ths help page has been created successfully.', 'success');

                return redirect()->route('admin.super.settings.help_pages');
            } else {
                add_message('There are some errors.', 'danger');
            }
        }

        $parent_pages = HelpPage::where('parent_id', 0)
                                ->orderBy('order')
                                ->get();

        if ( count($parent_pages) ) {
            foreach ( $parent_pages as $k => $page ) {
                $sub_pages = HelpPage::where('parent_id', $page->id)
                                     ->where('content', '{"en":null,"ch":null}')
                                     ->orderBy('order')
                                     ->get();

                $parent_pages[$k]->child = $sub_pages;
            }
        }

        // dd($help_page, $parent_pages);

        $request->flash();

        return view('pages.admin.super.settings.help_page.edit', [
            'page'          => 'super.settings.help_page.edit',
            'help_page'     => $help_page,
            'parent_pages'  => $parent_pages,
        ]);
    }
}