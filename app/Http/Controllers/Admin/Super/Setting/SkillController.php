<?php namespace iJobDesk\Http\Controllers\Admin\Super\Setting;
/**
 * @author KCG
 * @since July 28, 2017
 * Skill Page
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
use iJobDesk\Models\Skill;

class SkillController extends BaseController {

    public function __construct() {
        parent::__construct();

        $this->page_title = 'Skills';

        view()->share([
            'page_title' => $this->page_title
        ]);
    }

    public function index(Request $request) {
        add_breadcrumb('Skills');

        $action = $request->input('_action');

        if ($action == 'DELETE') {
            $ids = $request->input('ids');

            Skill::whereIn('id', $ids)->delete();
            add_message(sprintf('The %d Skill(s) has been deleted.', count($ids)), 'success');
        }

        $sort     = $request->input('sort', 'id');
        $sort_dir = $request->input('sort_dir', 'desc');

        $skills = Skill::orderBy($sort, $sort_dir);

        // Filtering
        $filter = $request->input('filter');

        // By Name
        if (!empty($filter['name'])) {
            $skills->where('name', 'LIKE', '%'.trim($filter['name']).'%');
        }

        // By Desc
        if (!empty($filter['desc'])) {
            $skills->where('desc', 'LIKE', '%'.trim($filter['desc']).'%');
        }

        $request->flashOnly('filter');

        return view('pages.admin.super.settings.skills', [
            'page' => 'super.settings.skills',
            'skills' => $skills->paginate($this->per_page),

            'sort'   => $sort,
            'sort_dir'   => '_' . $sort_dir
        ]);
    }


    public function edit(Request $request, $id = null) {
        $skill = new Skill();
        if (!empty($id)) {
            $skill = Skill::find($id);

            if (empty($skill))
                abort(404);
        }

        $action = $request->input('_action');
        if ($action == 'SAVE') {
            $skill->name = $request->input('name');
            $skill->desc = $request->input('desc');

            $skill->save();

            if (!$id)
                add_message('Successfully added new skill.', 'success');
            else
                add_message('Successfully updated skill.', 'success');
        }

        return view('pages.admin.super.settings.skill.modal', [
            'id' => $id,
            'skill' => $skill
        ]);
    }

    public function validate_name(Request $request, $id = null) {
        $name = $request->input('name');

        return Skill::where(function($query) use ($id, $name) {
                            $query->where('name', $name);
                            if (!empty($id))
                                $query->where('id', '<>', $id);
                      })
                    ->exists()?"false":"true";
    }
}