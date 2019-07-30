<?php
/**
 * @author KCG
 * @since Jan 22, 2018
 */
namespace iJobDesk\Observers;

use Auth;
use Log;


use iJobDesk\Models\File;
use iJobDesk\Models\User;
use iJobDesk\Models\Ticket;
use iJobDesk\Models\UserPortfolio;
use Intervention\Image\ImageManagerStatic as Image;

class FileObserver {
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct() {
        //
    }

    /**
     * Handle the event.
     *
     * @param  $model
     * @return void
     */
    public function saving($model) {
        $class_name      = get_class($model);
        $base_class_name = class_basename($class_name);
        $type = File::getTypeByClass($base_class_name);

        if ($base_class_name == 'File')
            return true;

        $me = Auth::user();
        
        // Upload files
        if (!empty($_POST['file_ids'])) {
            $file_ids = $_POST['file_ids'];

            foreach (explode_bracket($file_ids) as $file_id) {
                $file = File::find($file_id);

                if (!empty($file) && $file->user_id == $me->id && ($file->type == $type || ($file->type == File::TYPE_ID_VERIFICATION && $type == File::TYPE_TICKET_COMMENT))) {
                    if (!empty($model->id) || (empty($model->id) && !$file->isApproved()))
                        continue;
                }

                Log::info("FileObserver@saving: This user have tried to upload wrong files. ID: {$me->id}, file_ids: {$file_ids}");
            }
        }

        return true;
    }

    /**
     * Handle the event.
     *
     * @param  $model
     * @return void
     */
    public function saved($model) {
        $class_name      = get_class($model);
        $base_class_name = class_basename($class_name);
        $type = File::getTypeByClass($base_class_name);

        if ($base_class_name == 'File')
            return true;

        $me = Auth::user();
        
        // Upload files
        if (!empty($_POST['file_ids'])) {
            $file_ids = $_POST['file_ids'];

            foreach (explode_bracket($file_ids) as $file_id) {
                $file = File::find($file_id);

                if (!empty($file) && !$file->isApproved() && $file->user_id == $me->id && ($file->type == $type || ($file->type == File::TYPE_ID_VERIFICATION && $type == File::TYPE_TICKET_COMMENT))) {
                    $file->is_approved  = 1;
                    $file->target_id    = $model->id;
                    $file->save();

                    $file_path = $file->getPath();

                    $file_options = File::getOptions($type);

                    if (!array_key_exists($type, $file_options))
                        return false;

                    $file_option = $file_options[$type];

                    // case of portfolio, it needs to make thumbnail for portfolio
                    if ($file_option['image']) {
                        $image = Image::make($file_path);

                        if (empty($_POST['x1']))
                            $_POST['x1'] = 0;
                        
                        if (empty($_POST['y1']))
                            $_POST['y1'] = 0;

                        if (empty($_POST['width']))
                            $_POST['width'] = $file->type == File::TYPE_USER_AVATAR?User::AVATAR_WIDTH:UserPortfolio::THUMB_WIDTH;
                        
                        if (empty($_POST['height']))
                            $_POST['height'] = $file->type == File::TYPE_USER_AVATAR?User::AVATAR_HEIGHT:UserPortfolio::THUMB_HEIGHT;

                        $image->crop(
                            $_POST['width'],
                            $_POST['height'],
                            $_POST['x1'],
                            $_POST['y1']
                        );

                        if ($file->type == File::TYPE_USER_AVATAR)
                            $image->resize(User::AVATAR_WIDTH, User::AVATAR_HEIGHT);
                        elseif ($file->type == File::TYPE_USER_PORTFOLIO) {
                            $image->resize(UserPortfolio::THUMB_WIDTH, UserPortfolio::THUMB_HEIGHT);
                            $file_path = $file->getPath('thumb', false);
                        }

                        $image->save($file_path);

                        // Remove old images.
                        $old_images = File::where('target_id', $model->id)
                                          ->where('type', $file->type)
                                          ->where('id', '<>', $file->id)
                                          ->get();

                        foreach ($old_images as $old_image)
                            $old_image->delete();
                    }

                    if ($file->type == File::TYPE_USER_AVATAR) {
                        if ( $me->isFreelancer() ) {
                            $me->point->updatePortrait();
                        }
                    }
                }
            }
        }
	}

    public function deleted($model) {
        $class_name      = get_class($model);
        $base_class_name = class_basename($class_name);

        $me = Auth::user();

        if (in_array('withTrashed', get_class_methods(get_class($model)))) // Do not remove files if model is "Soft Delete" mode
            return;

        if ($base_class_name == 'File') {
            $file_path = $model->getPath();

            if (file_exists($file_path)) {
                unlink($file_path);
            }

            if ($model->type == File::TYPE_USER_AVATAR) {
                if ( $me ) {
                    $me->updateRatings();
                }
            }
        } else {
            $files = $model->files();

            foreach ($files as $file) {
                $file->delete();
            }
        }
    }
}