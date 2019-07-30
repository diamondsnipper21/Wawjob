<?php namespace iJobDesk\Http\Controllers\Admin\Super\Setting;
/**
 * @author PYH
 * @author KCG
 * @since Aug 9, 2017
 * @since Dec 13, 2017
 * Static Page
 */
use iJobDesk\Http\Requests;
use iJobDesk\Http\Controllers\Admin\AdminController as BaseController;
use Illuminate\Http\Request;

use App;
use DB;
use Auth;
use Config;
use Mail;

use iJobDesk\Mail\EmailSend;
use iJobDesk\Models\StaticPage;
use iJobDesk\Models\User;
use iJobDesk\Models\EmailTemplate;

class StaticPageController extends BaseController {

    public function __construct() {
        parent::__construct();

        $this->page_title = 'Static Pages';

        view()->share([
            'page_title' => $this->page_title
        ]);
    }

    public function index(Request $request) {
        add_breadcrumb('Static Pages');

        if ($request->method('post')) {

            $action = $request->input('_action');

            if ($action == 'CHANGE_STATUS') {
                $status = $request->input('page_action');
                $page_ids = $request->input('ids');
                
                if ($status == StaticPage::STATUS_NO_PUBLISH) {
                    foreach ($page_ids as $page_id) {
                        StaticPage::where('id', $page_id)
                                  ->update(['is_publish' => StaticPage::STATUS_NO_PUBLISH]);
                    }

                    add_message(sprintf('The %d Static Pages has not been published.', count($page_ids)), 'success');
                }
                elseif ($status == StaticPage::STATUS_PUBLISH) {
                    foreach ($page_ids as $page_id) {
                        StaticPage::where('id', $page_id)
                                  ->update(['is_publish' => StaticPage::STATUS_PUBLISH]);
                    }

                    add_message(sprintf('The %d Static Pages has been published.', count($page_ids)), 'success');
                }
                elseif ($status == StaticPage::STATUS_DELETE) {
                    foreach ($page_ids as $page_id) {
                        StaticPage::where('id', $page_id)
                                  ->delete();
                    }

                    add_message(sprintf('The %d Static Pages has been deleted.', count($page_ids)), 'success');
                }
            }
        }

        $sort     = $request->input('sort', 'updated_at');
        $sort_dir = $request->input('sort_dir', 'desc');

        $static_pages = StaticPage::orderBy($sort, $sort_dir)
                                  ->orderBy('title', 'asc');

        // Filtering
        $filter = $request->input('filter');

        // By title
        if (!empty($filter['title'])) {
            $static_pages->where('title', 'LIKE', '%'.trim($filter['title']).'%');
        }

        // By slug
        if (!empty($filter['slug'])) {
            $static_pages->where('slug', 'LIKE', '%'.trim($filter['slug']).'%');
        }

        // By keyword
        if (!empty($filter['keyword'])) {
            $static_pages->where('keyword', '=', $filter['keyword']);
        }

        // By description
        if (!empty($filter['desc'])) {
            $static_pages->where('desc', '=', $filter['desc']);
        }

        // By content
        if (!empty($filter['content'])) {
            $static_pages->where('content', '=', $filter['content']);
        }

        // By Status
        if ($filter['status'] != '') {
            $static_pages->where('is_publish', '=', $filter['status']);
        }

        $request->flashOnly('filter');

        return view('pages.admin.super.settings.static_pages', [
            'page' => 'super.settings.static_pages',
            'static_pages' => $static_pages->paginate($this->per_page),
            'sort'   => $sort,
            'sort_dir'   => '_' . $sort_dir,
            'config' => Config::get('settings'),
        ]);
    }

    public function edit(Request $request, $id = null) {

        if ($id) {
            $static_page = StaticPage::find($id);
        }
        else {
            $static_page = new StaticPage();
        }

        $title     = $request->input('title');
        $keyword   = $request->input('keyword');
        $slug      = $request->input('slug');
        $desc      = $request->input('desc');
        $content   = $request->input('content');

        if ($request->method('post') && $title && $slug) {
            $is_duplicate_slug = StaticPage::where('is_publish', 1)
                                           ->where(function($query) use ($id) {
                                                if (!empty($id))
                                                    $query->where('id', '<>', $id);
                                           })
                                           ->where('slug', $slug)
                                           ->exists();

            if ( $is_duplicate_slug ) {
                add_message('This slug is in use.', 'danger');
            } else {

                $static_page->title     = encode_json_multilang($title);
                $static_page->keyword   = encode_json_multilang($keyword);
                $static_page->slug      = $slug;
                $static_page->desc      = encode_json_multilang($desc);
                $static_page->content   = encode_json_multilang($content);

                if ($static_page->save()) {
                    if ($id)
                        add_message('Ths static page has been updated successfully.', 'success');
                    else
                        add_message('Ths static page has been created successfully.', 'success');
                } else {
                    add_message('There are some errors.', 'danger');
                }

                if ( $request->input('notify_users') ) {
					$users = User::whereIn('role', [
								User::ROLE_USER_FREELANCER,
								User::ROLE_USER_BUYER,
							])
							->where('status', User::STATUS_AVAILABLE)
							->get();

					foreach ( $users as $u ) {
						EmailTemplate::send($u, 'TERM_UPDATED', 0, []);
					}
                }
            }
        }

        $request->flash();

        return view('pages.admin.super.settings.static_page.edit', [
            'page'              => 'super.settings.static_page.edit',
            'static_page'       => $static_page
        ]);
    }
}