<?php namespace iJobDesk\Models;

use iJobDesk\Models\Settings;
use iJobDesk\Models\UserStat;
use iJobDesk\Models\Ticket;

class UserPoint extends Model {

	/**
	* The table associated with the model.
	*
	* @var string
	*/
	protected $table = 'user_points';

	/**
	* Indicates if the model should be timestamped.
	*
	* @var bool
	*/
	public $timestamps = true;

	function __construct() {
        parent::__construct();
    }

    public function user() {
		return $this->hasOne('iJobDesk\Models\User', 'id', 'user_id');
	}

	public function updatePortrait() {
		if ( Settings::get('POINT_PORTRAIT_ENABLED') )
			$this->portrait = Settings::get('POINT_PORTRAIT');
		else
			$this->portrait = 0;

		$this->save();
	}

	public function updatePortfolio() {
		if ( Settings::get('POINT_PORTFOLIO_ENABLED') ) {
			if ( !$this->portfolio ) {
				$this->portfolio = Settings::get('POINT_PORTFOLIO');
				$this->save();
			}
		} else {
			$this->portfolio = 0;
			$this->save();
		}
	}

	public function updateCertification() {
		if ( Settings::get('POINT_CERTIFICATION_ENABLED') ) {
			if ( !$this->certification ) {
				$this->certification = Settings::get('POINT_CERTIFICATION');
				$this->save();
			}
		} else {
			$this->certification = 0;
			$this->save();
		}
	}

	public function updateEmploymentHistory() {
		if ( Settings::get('POINT_EMPLOYMENT_HISTORY_ENABLED') ) {
			if ( !$this->employment_history ) {
				$this->employment_history = Settings::get('POINT_EMPLOYMENT_HISTORY');
				$this->save();
			}
		} else {
			$this->employment_history = 0;
			$this->save();
		}
	}

	public function updateEducation() {
		if ( Settings::get('POINT_EDUCATION_ENABLED') ) {
			if ( !$this->education ) {
				$this->education = Settings::get('POINT_EDUCATION');
				$this->save();
			}
		} else {
			$this->education = 0;
			$this->save();
		}
	}

	public function updateIDVerified() {
		if ( Settings::get('POINT_ID_VERIFIED_ENABLED') )
			$this->id_verified = Settings::get('POINT_ID_VERIFIED');
		else
			$this->id_verified = 0;

		$this->save();
	}

	public function updateNewFreelancer() {
		if ( ago_days($this->user->created_at) > 90 ) {
			if ( $this->new_freelancer > 0 ) {
				$this->new_freelancer = 0;
				$this->save();
			}
		}
	}

	public function updateJobSuccess($job_success = 0) {
		if ( Settings::get('POINT_JOB_SUCCESS_ENABLED') )
			$this->job_success = ($job_success / 100) * Settings::get('POINT_JOB_SUCCESS');
		else
			$this->job_success = 0;

		$this->save();
	}

	public function updateScore($score = 0) {
		if ( Settings::get('POINT_SCORE_ENABLED') )
			$this->score = $score * Settings::get('POINT_SCORE');
		else
			$this->score = 0;

		$this->save();
	}

	public function updateOpenJobs($total = 0) {
		if ( Settings::get('POINT_OPEN_JOBS_ENABLED') ) {
			$this->open_jobs = $total * Settings::get('POINT_OPEN_JOBS') * Settings::get('POINT_SCORE_PER_DOLLAR');
		} else {
			$this->open_jobs = 0;
		}

		$this->save();
	}

	public function updateLast12Months($total = 0) {
		if ( Settings::get('POINT_LAST_12MONTHS_ENABLED') )
			$this->last_12months = $total;
		else
			$this->last_12months = 0;

		$this->save();
	}

	public function updateLifetime($total = 0) {
		if ( Settings::get('POINT_LIFETIME_ENABLED') )
			$this->lifetime = $total;
		else
			$this->lifetime = 0;

		$this->save();
	}

	public function updateActivity() {
		if ( ago_days($this->user->stat->last_activity) > 3 ) {
			if ( $this->activity > 0 ) {
				$this->activity = 0;
				$this->save();
			}
		} else {
			if ( Settings::get('POINT_ACTIVITY_ENABLED') ) {
				if ( !$this->activity ) {
					$this->activity = Settings::get('POINT_ACTIVITY');
					$this->save();
				}
			}
		}
	}

	public function updateDispute() {
		if ( Settings::get('POINT_DISPUTE_ENABLED') ) {
			$total_disputs = Ticket::leftJoin('contracts', 'contracts.id', '=', 'contract_id')
									->where('tickets.type', Ticket::TYPE_DISPUTE)
									->where('tickets.dispute_winner_id', '<>', $this->user->id)
									->where('tickets.contract_id', '<>', 0)
									->where('contracts.contractor_id', $this->user->id)
									->whereIn('tickets.status', [
										Ticket::STATUS_SOLVED,
										Ticket::STATUS_CLOSED,
									])
									->select('t.id')
									->count();

			if ( $total_disputs > 0 ) {
				$this->dispute = $total_disputs * Settings::get('POINT_DISPUTE');
				$this->save();
			}
		} else {
			$this->dispute = 0;
			$this->save();
		}
	}

	public function updateAll() {

		if ( !$this->user || !$this->user->isFreelancer() ) {
			return false;
		}
		
		// Portrait
		$this->portrait = 0;
		if ( Settings::get('POINT_PORTRAIT_ENABLED') ) {
			if ( $this->user->existAvatar() ) {
				$this->portrait = Settings::get('POINT_PORTRAIT');
			}
		}

		// Portfolio
		$this->portfolio = 0;
		if ( Settings::get('POINT_PORTFOLIO_ENABLED') ) {
			if ( count($this->user->portfolios) ) {
				$this->portfolio = Settings::get('POINT_PORTFOLIO');
			}
		}

		// Certification
		$this->certification = 0;
		if ( Settings::get('POINT_CERTIFICATION_ENABLED') ) {
			if ( count($this->user->certifications) ) {
				$this->certification = Settings::get('POINT_CERTIFICATION');
			}
		}

		// Employment History
		$this->employment_history = 0;
		if ( Settings::get('POINT_EMPLOYMENT_HISTORY_ENABLED') ) {
			if ( count($this->user->employments) ) {
				$this->employment_history = Settings::get('POINT_EMPLOYMENT_HISTORY');
			}
		}

		// Education
		$this->education = 0;
		if ( Settings::get('POINT_EDUCATION_ENABLED') ) {
			if ( count($this->user->educations) ) {
				$this->education = Settings::get('POINT_EDUCATION');
			}
		}

		// ID Verified
		$this->id_verified = 0;
		if ( Settings::get('POINT_ID_VERIFIED_ENABLED') ) {
			if ( $this->user->isIDVerified() ) {
				$this->id_verified = Settings::get('POINT_ID_VERIFIED');
			}
		}

		// New Freelancer
		$this->new_freelancer = 0;
		if ( Settings::get('POINT_NEW_FREELANCER_ENABLED') ) {
			if ( ago_days($this->user->created_at) <= 90 ) {
				$this->new_freelancer = Settings::get('POINT_NEW_FREELANCER');
			}
		}

		// Job Success
		/*$this->job_success = 0;
		if ( Settings::get('POINT_JOB_SUCCESS_ENABLED') ) {
			$this->job_success = ($this->user->stat->job_success / 100) * Settings::get('POINT_JOB_SUCCESS');
		}
		
		// Score
		$this->score = 0;
		if ( Settings::get('POINT_SCORE_ENABLED') ) {
			$this->score = $this->user->stat->score * Settings::get('POINT_SCORE');
		}*/

		// Open Jobs Earning
		$this->open_jobs = 0;
		if ( Settings::get('POINT_OPEN_JOBS_ENABLED') ) {
			$totalEarnings = $this->user->totalEarnedOpenContracts();
			if ( $totalEarnings ) {
				if ( $totalEarnings ) {
					$this->open_jobs = ($totalEarnings * Settings::get('POINT_OPEN_JOBS')) * Settings::get('POINT_SCORE_PER_DOLLAR');
				}
			}
		}

		// Last 12 Months Earning
		$this->last_12months = 0;
		if ( Settings::get('POINT_LAST_12MONTHS_ENABLED') ) {
			$this->last_12months = $this->user->totalContractPoints();
		}

		// Lifetime Earning
		$this->lifetime = 0;
		if ( Settings::get('POINT_LIFETIME_ENABLED') ) {
			$this->lifetime = $this->user->totalContractPoints(false);
		}

		// Activity
		$this->activity = 0;
		if ( Settings::get('POINT_ACTIVITY_ENABLED') ) {
			if ( ago_days($this->user->stat->last_activity) <= 3 ) {
				$this->activity = Settings::get('POINT_ACTIVITY');
			}
		}

		// Dispute
		$this->dispute = 0;
		if ( Settings::get('POINT_DISPUTE_ENABLED') ) {
			$total_disputs = Ticket::leftJoin('contracts', 'contracts.id', '=', 'contract_id')
									->where('tickets.type', Ticket::TYPE_DISPUTE)
									->where('tickets.dispute_winner_id', '<>', $this->user->id)
									->where('tickets.contract_id', '<>', 0)
									->where('contracts.contractor_id', $this->user->id)
									->whereIn('tickets.status', [
										Ticket::STATUS_SOLVED,
										Ticket::STATUS_CLOSED,
									])
									->select('t.id')
									->count();

			if ( $total_disputs > 0 ) {
				$this->dispute = $total_disputs * Settings::get('POINT_DISPUTE');
			}
		}

		$this->save();
	}
}