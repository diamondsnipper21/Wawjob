<?php namespace iJobDesk\Models;


class ProjectSkill extends Model {

  /**
   * The table associated with the model.
   *
   * @var string
   */
  protected $table = 'project_skills';

  /**
   * Indicates if the model should be timestamped.
   *
   * @var bool
   */
  public $timestamps = false;

  /**
   * Get the skill.
   */
  public function skill()
  {
    return $this->hasOne('iJobDesk\Models\Skill', 'skill_id');
  }
}