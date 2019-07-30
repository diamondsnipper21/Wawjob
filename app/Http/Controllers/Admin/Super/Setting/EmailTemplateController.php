<?php namespace iJobDesk\Http\Controllers\Admin\Super\Setting;
/**
 * @author KCG
 * @since July 28, 2017
 * Email Template
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
use iJobDesk\Models\EmailTemplate;

class EmailTemplateController extends BaseController {

    public function __construct() {
        parent::__construct();

        $this->page_title = 'Email Templates';

        view()->share([
            'page_title' => $this->page_title
        ]);
    }

    public function index(Request $request) {
        add_breadcrumb('Email Templates');

        if ($request->method('post')) {

            $action = $request->input('_action');

            if ($action == 'CHANGE_STATUS') {
                $status = $request->input('template_action');
                $template_ids = $request->input('ids');
                
                if ($status == EmailTemplate::STATUS_DISABLE) {
                    EmailTemplate::whereIn('id', $template_ids)
                                 ->update(['status' => EmailTemplate::STATUS_DISABLE]);

                    add_message(sprintf('The %d Email Templates has been disabled.', count($template_ids)), 'success');
                }
                elseif ($status == EmailTemplate::STATUS_ENABLE) {
                    EmailTemplate::whereIn('id', $template_ids)
                                 ->update(['status' => EmailTemplate::STATUS_ENABLE]);

                    add_message(sprintf('The %d Email Templates has been enabled.', count($template_ids)), 'success');
                }
                elseif ($status == EmailTemplate::STATUS_DELETE) {
                    EmailTemplate::whereIn('id', $template_ids)
                                 ->update(['status' => EmailTemplate::STATUS_DELETE]);

                    EmailTemplate::whereIn('id', $template_ids)
                                 ->delete();

                    add_message(sprintf('The %d Email Templates has been deleted.', count($template_ids)), 'success');
                }
            }
        }

        $sort     = $request->input('sort', 'updated_at');
        $sort_dir = $request->input('sort_dir', 'desc');

        $email_templates = EmailTemplate::orderByRaw('IF(status = 1, 1, 0) DESC')
                                        ->orderBy($sort, $sort_dir)
                                        ->orderBy('slug', 'asc');

        // Filtering
        $filter = $request->input('filter');

        // By Slug
        if (!empty($filter['slug'])) {
            $email_templates->where('slug', 'LIKE', '%'.trim($filter['slug']).'%');
        }

        // By Subject
        if (!empty($filter['subject'])) {
            $email_templates->where('subject', 'LIKE', '%'.trim($filter['subject']).'%');
        }

        // By For
        if ($filter['for'] != '') {
            $email_templates->where('for', '=', $filter['for']);
        }

        // By Status
        if ($filter['status'] != '') {
            if ($filter['status'] != EmailTemplate::STATUS_DELETE)
                $email_templates->where('status', '=', $filter['status']);
            else
                $email_templates->onlyTrashed();
        }

        // By Updated Date
        if (!empty($filter['updated_at'])) {
            if (!empty($filter['updated_at']['from'])) {
                $email_templates->where('t.updated_at', '>=', date('Y-m-d H:i:s', strtotime($filter['updated_at']['from'])));
            }

            if (!empty($filter['updated_at']['to'])) {
                $email_templates->where('t.updated_at', '<=', date('Y-m-d H:i:s', strtotime($filter['updated_at']['to']) + 24* 3600));
            }
        }

        $email_templates->withTrashed();

        $request->flashOnly('filter');

        return view('pages.admin.super.settings.email_templates', [
            'page' => 'super.settings.email_templates',
            'email_templates' => $email_templates->paginate($this->per_page),
            'sort'   => $sort,
            'sort_dir'   => '_' . $sort_dir,
            'config' => Config::get('settings'),
        ]);
    }

    public function edit(Request $request, $id = null) {

        $is_duplicate_slug = EmailTemplate::where('status', EmailTemplate::STATUS_ENABLE);

        if ($id) {
            $email_template = EmailTemplate::find($id);
            $is_duplicate_slug = $is_duplicate_slug->where('id', '<>', $id);
        }
        else {
            $email_template = new EmailTemplate();
        }

        $slug        = $request->input('slug');
        $for         = $request->input('select_for');
        $subject     = $request->input('subject');
        $content     = $request->input('content');

        if ($request->isMethod('post') && $slug) {
            
            $is_duplicate_slug = $is_duplicate_slug->where('slug', $slug)
                                                    ->where('for', $for)
                                                    ->exists();

            if ( $is_duplicate_slug ) {
                add_message('The same slug is already exist.', 'danger');
            } else {
                $email_template->slug       = $slug;
                $email_template->for        = $for;
                $email_template->subject    = encode_json_multilang($subject);
                $email_template->content    = encode_json_multilang($content);

                $email_template->save();

                if (empty($id))
                    add_message('The new email template has been added successfully.', 'success');
                else
                    add_message('This email template has been updated successfully.', 'success');
            }
        }

        return response()->json(['alerts' => show_messages(true)]);
    }
}