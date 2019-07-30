<?php namespace iJobDesk\Http\Controllers;

use iJobDesk\Http\Requests;
use iJobDesk\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use Auth;
use Storage;
use Config;
use Session;
use Exception;
use Log;

use Intervention\Image\ImageManagerStatic as Image;

// Models
use iJobDesk\Models\User;
use iJobDesk\Models\File;
use iJobDesk\Models\Project;
use iJobDesk\Models\Todo;
use iJobDesk\Models\ProjectApplication;
use iJobDesk\Models\ProjectMessageThread;
use iJobDesk\Models\ProjectMessage;
use iJobDesk\Models\Ticket;
use iJobDesk\Models\TicketComment;
use iJobDesk\Models\AdminMessage;

class FileController extends Controller {

	protected function checkIfAccessable(Request $request, $hash, $thumb = null) {
		try {
			$file = decode_file_hash($hash);

			if (!$file)
				return false;

			$id 	= $file->id;
			$hash 	= $file->hash;
			$type 	= $file->type;

			if ( !in_array($type, array_keys(File::getOptions())) ) {
				return false;
			}

			$user = Auth::user();
			if ( !$user && in_array($type, [
				File::TYPE_PROJECT,
				File::TYPE_MESSAGE,
				File::TYPE_PROJECT_APPLICATION,
				File::TYPE_CONTRACT,
				File::TYPE_TODO,
				File::TYPE_TICKET,
				File::TYPE_TICKET_COMMENT,
				File::TYPE_ADMIN_MESSAGE,
				File::TYPE_ID_VERIFICATION
			]) )
				return false;

			if (!$file->isApproved() && $user && $user->id != $file->user_id && !$user->isSuper())
				return false;

			if ( $type != File::TYPE_TEMP && intval($file['type']) !== $type ) {
				return false;
			}

			$path = $file->getPath($thumb);

			if ( !file_exists($path) ) {
				return false;
			}

			if ($file->isApproved()) {
				// For message
				if ( $type === File::TYPE_MESSAGE ) {
					$project_message = ProjectMessage::find($file->target_id);
					if ( !$project_message ) {
						return false;
					}

					$project_message_thread = ProjectMessageThread::find($project_message->thread_id);
					if ( !$project_message_thread ) {
						return false;
					}

					if ( !$user->isSuper() && !in_array($user->id, [$project_message_thread->sender_id, $project_message_thread->receiver_id]) ) {
						return false;
					}
				// For job apply
				} else if ( $type === File::TYPE_PROJECT_APPLICATION ) {

					$project_application = ProjectApplication::find($file->target_id);
					if ( !$project_application ) {
						return false;
					}

					$project = Project::find($project_application->project_id);
					if ( !$project ) {
						return false;
					}

					$project_application_users = [
						$project->client_id,
						$project_application->user_id
					];

					if ( !$user->isAdmin() && !in_array($user->id, $project_application_users) ) {
						return false;
					}

				// For project
				} else if ( $type === File::TYPE_PROJECT ) {
					$project = Project::find($file->target_id);
					if ( !$project ) {
						return false;
					}
									
					if ( $project->isPrivate() ) {
						if (!$project->canViewPrivate($user))
							return false;

					} else if ( intval($project->is_public) == Project::STATUS_PROTECTED ) {
						if ( !$user ) {
							return false;
						}
					}
				} else if ( $type === File::TYPE_TICKET_COMMENT || $type === File::TYPE_ID_VERIFICATION ) {
					$comment = TicketComment::find($file->target_id);

					if (!$comment)
						return false;

					$ticket = $comment->ticket;

					$valid = false;
					$valid = $valid || ($ticket->assigner_id == $user->id);
					$valid = $valid || ($ticket->admin_id == $user->id);
					$valid = $valid || ($comment->sender_id == $user->id);
					$valid = $valid || ($ticket->user_id == $user->id);
					$valid = $valid || $user->isAdmin();
					$valid = $valid || ($ticket->contract_id && ($ticket->contract->buyer_id == $user->id || $ticket->contract->contractor_id == $user->id));

					if (!$valid)
						return false;
					
				} else if ( $type === File::TYPE_ADMIN_MESSAGE ) {
					$message = AdminMessage::find($file->target_id);

					if (!$message)
						return false;

					$ticket = null;
					$todo   = null;

					if ($message->message_type != AdminMessage::MESSAGE_TYPE_TICKET)
						$ticket = $message->ticket;

					if ($message->message_type != AdminMessage::MESSAGE_TYPE_TODO)
						$todo = $message->todo;

					$valid = false;
					$valid = $valid || ($message->sender_id == $user->id);
					$valid = $valid || ($ticket && ($ticket->assigner_id == $user->id || $ticket->admin_id == $user->id));
					$valid = $valid || ($todo && ($todo->creator == $user->id || in_array($user->id, array_pluck($todo->assigners, 'id'))));
					$valid = $valid || $user->isAdmin();

					if (!$valid)
						return false;

				} else if ( $type === File::TYPE_TODO ) {
					$todo = Todo::find($file->target_id);

					$valid = false;
					$valid = $valid || ($todo->creator_id == $user->id || in_array($user->id, array_pluck($todo->assigners, 'id')));
					$valid = $valid || $user->isAdmin();

					if (!$valid)
						return false;
				} else if ( $type === File::TYPE_TICKET ) {
					$ticket = Ticket::find($file->target_id);

					if (!$ticket)
						return false;

					$valid = false;
					$valid = $valid || ($ticket->user_id == $user->id || $ticket->admin_id == $user->id || $ticket->assigner_id == $user->id);
					$valid = $valid || $user->isAdmin();

					if (!$valid)
						return false;
				}
			}

			$mime_type = $file['mime_type'];
			if ( empty($mime_type) ) {
				return false;
			}

			// Success
			return $file;
		} catch ( Exception $e ) {
			return false;
		}

		return false;
	}

	/**
	 * Output content of file to html response
	 */
	public function get(Request $request, $hash, $thumb = null) {
		$file = $this->checkIfAccessable($request, $hash, $thumb);

		if (!$file)
			abort(404);

		$file_path = $file->getPath($thumb);

		header('Cache-Control: max-age=86400');
		header('Content-Type: '. $file->mime_type);
		header('Content-Length: ' . filesize($file_path));

		readfile($file_path);
		exit;
	}

	/**
	 * Output content of file to html response
	 */
	public function get_thumb(Request $request, $hash) {
		return $this->get($request, $hash, 'thumb');
	}

	/**
	 * Download file.
	 */
	public function download(Request $request, $hash) {
		$file = $this->checkIfAccessable($request, $hash);

		if (!$file)
			abort(404);

		return response()->download($file->getPath());
	}

	/**
	 * Upload file.
	 */
	public function upload(Request $request) {
		$me = Auth::user();

        $json['success'] = true;

		$type = $request->input('file_type');
		$file_options = File::getOptions($type);

		if (!array_key_exists($type, $file_options))
			abort(404);

		$file_option = $file_options[$type];
		$files = $request->file('attached_files');

		if (!$me && !in_array($type, [
			File::TYPE_TICKET,
			File::TYPE_TICKET_COMMENT,
			File::TYPE_ID_VERIFICATION
		]))
			abort(404);

		if (!$files)
			return $json;

		if (!is_array($files))
			$files = [$files];

		// Create upload directory
		$filesystem = Config::get("filesystems.default");
		$upload_dir = get_upload_dir($me->id, $file_option['prefix'], $filesystem);
		$full_upload_dir = getRoot() . '/' . $upload_dir;

        createDir($full_upload_dir);

        $json['success'] = false;
        foreach ($files as $file) {
        	$ext = strtolower($file->getClientOriginalExtension());
        	
            // File size is larger than the limit
            if ( $file_option['file_size'] && ($file->getClientSize() == 0 || $file->getClientSize() > $file_option['file_size']) ) {
                add_message('[' . $file->getClientOriginalName() . ']: ' . trans('job.error_file_size', ['max_upload_file_size' => get_file_size_string($file_option['file_size'])]), 'danger');

                continue;
            }

            // Check file types
        	if ( ($file_option['file_types'] && !in_array($ext, $file_option['file_types'])) ||
        		 // Want to allow to upload image files...
        		 ($file_option['image'] && substr($file->getMimeType(), 0, 5) != 'image')
        	) {
        		add_message('[' . $file->getClientOriginalName() . ']: ' . trans('job.error_file_type', ['valid_file_extensions' => implode(', ', $file_option['file_types'])]), 'danger');

                continue;
            }

            $filename = generateFileName($full_upload_dir, $file->getClientOriginalName());

            try {
	            if ( $file->move($full_upload_dir, $filename) ) {
	                $file_obj = new File;
	                $file_obj->user_id 		= $me->id;
	                $file_obj->name 		= $filename;
	                $file_obj->type 		= $type;
	                $file_obj->ext  		= $ext;
	                $file_obj->is_approved  = null;
	                $file_obj->mime_type 	= $file->getClientMimeType();
	                $file_obj->size 		= $file->getClientSize();
	                $file_obj->path 		= $upload_dir;
	                $file_obj->hash();

	                $info = null;
	                if ($type == File::TYPE_USER_AVATAR || $type == File::TYPE_USER_PORTFOLIO) {
		                $image = Image::make($file_obj->getPath());
		            	$info = ['width' => $image->width(), 'height' => $image->height()];
	                }

	                if ( $file_obj->save() ) {
	                    $json['files'][] = [
	                        'id' 			=> $file_obj->id,
	                        'name' 			=> $filename,
	                        'url'			=> file_url($file_obj),
	                        'download_url'	=> file_download_url($file_obj),
	                        'delete_url' 	=> route('files.delete', ['hash' => encode_file_hash($file_obj)]),
	                        'info'			=> $info
	                    ];

	                	$json['success'] 	= true;
	                }
	            } else {
	                continue;
	            }
            } catch (Exception $e) {
            	Log::error('FileContrller@upload: '. $e->getMessage());
            }
        }

        $json['alerts'] = show_messages(true);

        return $json;
	}

	/**
	 * Delete uploaded files.
	 */
	public function delete(Request $request, $hash) {
		$user = Auth::user();

		$file = decode_file_hash($hash);

		if (!$user)
			abort(404);

		if (!$file)
			abort(404);

		if ($file->user_id != $user->id)
			abort(404);

        return ['success' => $file->remove()];
	}
}