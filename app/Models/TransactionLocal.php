<?php namespace iJobDesk\Models;

use DB;
use Config;
use Auth;
use Log;

use Illuminate\Database\Eloquent\SoftDeletes;

use iJobDesk\Models\Country;
use iJobDesk\Models\ContractMilestone;
use iJobDesk\Models\User;
use iJobDesk\Models\HourlyLogMap; 
use iJobDesk\Models\HourlyReview;
use iJobDesk\Models\NotifyInsufficientFund;
use iJobDesk\Models\PaymentGateway;
use iJobDesk\Models\UserPaymentGateway;
use iJobDesk\Models\UserDeposit;
use iJobDesk\Models\EmailTemplate;
use iJobDesk\Models\Wallet;
use iJobDesk\Models\WalletHistory;
use iJobDesk\Models\SiteWallet;
use iJobDesk\Models\SiteWalletHistory;
use iJobDesk\Models\Settings;
use iJobDesk\Models\Reason;
use iJobDesk\Models\ActionHistory;

class TransactionLocal extends Model {

	use SoftDeletes;

	protected $table = 'transactions';

	/**
	* Transaction types
	*/
	const TYPE_FIXED 			= 1;
	const TYPE_HOURLY 			= 2;
	const TYPE_BONUS 			= 3;
	const TYPE_CHARGE 			= 4;
	const TYPE_WITHDRAWAL 		= 5;
	const TYPE_REFUND 			= 6;
	const TYPE_SITE_WITHDRAWAL 	= 7;
	const TYPE_FEATURED_JOB		= 8; // Featured job fee
	const TYPE_AFFILIATE		= 9; // Affiliate fee
	const TYPE_AFFILIATE_CHILD	= 10; // Child affiliate fee
	const TYPE_IJOBDESK_EARNING	= 99; // Not used in database

	/**
	* Who is this action performed for?
	*/
	const FOR_BUYER = 1;
	const FOR_FREELANCER = 2;
	const FOR_IJOBDESK = 3;

	/**
	* Transaction status
	*/
	const STATUS_PENDING = 0;
	const STATUS_AVAILABLE = 1;
	const STATUS_DONE = 2;
	const STATUS_CANCELLED = 3;
	const STATUS_REVIEW = 4;
	const STATUS_PROCEEDING = 5; // In progress status by super admin. Need to be done in pending hours by cron job.
	const STATUS_SUSPENDED = 6; // In suspended status by super admin.
	const STATUS_NOTIFIED = 7; // In notified status by super admin.

	protected static $str_transaction_type = [
		self::TYPE_FIXED      => 'Fixed Price', 
		self::TYPE_HOURLY     => 'Hourly', 
		self::TYPE_BONUS      => 'Bonus', 
		self::TYPE_CHARGE     => 'Deposit', 
		self::TYPE_WITHDRAWAL => 'Withdrawal', 
		self::TYPE_REFUND     => 'Refund', 
		self::TYPE_SITE_WITHDRAWAL => 'Site Withdrawal', 
		self::TYPE_FEATURED_JOB => 'Featured Job Fee', 
		self::TYPE_AFFILIATE => 'Affiliate', 
		self::TYPE_AFFILIATE_CHILD => 'Affiliate Fee (Child)', 
	];

	protected static $str_status = [
		self::STATUS_PENDING => 'Pending',
		self::STATUS_AVAILABLE => 'Available',
		self::STATUS_DONE => 'Done',
		self::STATUS_CANCELLED => 'Cancelled',
		self::STATUS_REVIEW => 'Review',
		self::STATUS_PROCEEDING => 'Proceeding', // Used for deposits
		self::STATUS_SUSPENDED => 'Suspended',
	];

	const REFUND_REASON_BY_FREELANCER = 0;
	const REFUND_REASON_END_CONTRACT = 1;
	const REFUND_REASON_OFFER_DECLINED = 2;
	const REFUND_REASON_OFFER_WITHDRAWN = 3;
	const REFUND_REASON_DISPUTE_PUNISHED = 4;
	const REFUND_REASON_BY_SUPER_ADMIN = 5;

	protected static $str_refund_reason = [
		0 => 'By Freelancer',
		1 => 'End Contract',
		2 => 'Offer Declined',
		3 => 'Offer Withdrawn',
		4 => 'Dispute Punished',
		5 => 'By Super Admin',
	];

	// Overdue days how long transaction has not been performed.
	const DAYS_OVERDUE = 3;

	/**
    * The attributes that should be mutated to dates.
    *
    * @var array
    */
    protected $dates = ['created_at', 'done_at', 'updated_at', 'deleted_at'];

    /**
    * Indicates if the model should be timestamped.
    *
    * @var bool
    */
    public $timestamps = true;

    function __construct() {
        parent::__construct();

        self::$str_transaction_type = [
	        self::TYPE_FIXED      		=> trans('common.fixed_price'), 
			self::TYPE_HOURLY     		=> trans('common.hourly'), 
			self::TYPE_BONUS      		=> trans('common.bonus'), 
			self::TYPE_CHARGE     		=> trans('common.deposit'), 
			self::TYPE_WITHDRAWAL 		=> trans('common.withdrawal'), 
			self::TYPE_REFUND     		=> trans('common.refund'), 
			self::TYPE_SITE_WITHDRAWAL 	=> trans('common.site_withdrawal'), 
			self::TYPE_FEATURED_JOB 	=> trans('common.featured_job'), 
			self::TYPE_AFFILIATE 		=> trans('common.commission'), 
			self::TYPE_AFFILIATE_CHILD 	=> trans('common.commission'),
        ];
    }

	public function type_string() {
		$suf = '';

		if ( $this->for == self::FOR_IJOBDESK ) {
			if ( $this->isSiteWithdraw() ) {
				return trans('common.site_withdrawal');
			} else if ( $this->isWithdraw() ) {
				if ( $this->old_id ) {
					$suf = ' ' . trans('common.cancelled');
				}

				return trans('common.withdrawal_fee') . $suf;
			} else if ( $this->isAffiliate() ) {
				return self::$str_transaction_type[$this->type];
			} else if ( $this->isRefund() ) {
				return trans('common.fee_refund');
			} else {
				return trans('common.service_fee');
			}
		}

		if ( $this->milestone_id && $this->milestone ) {
			$suf = '';
			if ( $this->amount > 0 && $this->milestone->isRefunded() ) {
				$suf = ' ' . trans('common.refund');
			} else if ( $this->amount < 0 && $this->milestone->isRefunded() ) {
				$suf = ' ' . trans('common.deposit');
			} else if ( $this->milestone->isPending() || $this->milestone->isReleased() ) {
				$suf = ' ' . trans('common.release');
			} else if ( $this->milestone->isFunded() ) {
				$suf = ' ' . trans('common.deposit');
			}

			return trans('common.escrow') . $suf;
		}

		if ( isset(self::$str_transaction_type[$this->type]) ) {
			if ( $this->isAffiliate() ) {
				if ( $this->amount < 0 ) {
					$suf = ' ' . trans('common.refund');
				}
			} else {
				if ( $this->old_id ) {
					$suf = ' ' . trans('common.cancelled');
				}

				if ( $this->isHourly() && $this->for == self::FOR_BUYER && $this->amount > 0) {
					$suf = ' ' . trans('common.refund');
				}
			}

			return self::$str_transaction_type[$this->type] . $suf;
		}

		return '';
	}

	public function description_string($admin = false) {
		if ( $this->isAffiliate() ) {
			if ( $this->old_id ) {
				if ( $this->for == self::FOR_IJOBDESK ) {
					if ( $this->amount < 0 ) {
						$str = trans('report.affiliate_commission_by_user', [
							'user' => $this->ref_user->fullname(),
							'contract' => $this->old->contract->title,
						]);
					} else {
						$str = trans('report.affiliate_commission_refund', [
							'user' => $this->ref_user->fullname(),
						]);
					}
				} else {
					if ( $this->amount > 0 ) {
						$str = trans('report.affiliate_commission_by_user', [
							'user' => $this->ref_user->fullname(),
							'contract' => $this->old->contract->title,
						]);
					} else {
						$str = trans('report.affiliate_commission_refund', [
							'user' => $this->ref_user->fullname(),
						]);
					}
				}
			} else {
				$str = trans('report.affiliate_commission_by_buyer', [
					'buyer' => $this->ref_user->fullname(),
				]);
			}

			return $str;
		} else if ( $this->isSiteWithdraw() ) {
			return '';
		} else {
			if ( $this->for == self::FOR_IJOBDESK ) {
				if ( $this->isWithdraw() ) {
					return trans('common.withdrawal_fee') . ($this->ref_id ? ' #' . $this->ref_id : '');
				} else if ( $this->isRefund() ) {
					return trans('common.service_fee_refund_for', ['id' => $this->reference->ref_id]);
				} else {
					$str = trans('common.service_fee');

					if ( $this->ref_id ) {
						$str .= ' - ' . trans('common.ref_id');
					}

					$str .= $this->ref_id ? ' #' . $this->ref_id : '';

					return $str;
				}
			} else {
				if ( $this->isDeposit() ) {
					return trans('report.deposit_of_funds_through_payment_method', [
						'payment_method' => self::gateway_string()
					]);
				} else if ( $this->isWithdraw() ) {
					if ( $this->isCancelled() && $this->old_id ) {
						return self::$str_transaction_type[$this->type] . ' ' . trans('common.cancelled') . ' #' . $this->old_id;
					} else {
						return trans('report.withdrawal_of_funds_to_payment_method', [
							'payment_method' => self::gateway_string()
						]);
					}
				} else if ( $this->isFeaturedJob() ) {
					return trans('report.featured_job_posting_fee_for_project', [
						'project' => $this->project ? $this->project->subject : $this->id 
					]);
				} else if ( $this->isBonus() ) {
					$str = trans('report.bonus_payment_for_contract', [
						'contract' => $this->contract->title
					]);

					if ( $this->note ) {
						$str .= '<div class="info">' . $this->note . '</div>';
					}

					return $str;
				} else if ( $this->isHourly() ) {
					if ( $this->for == self::FOR_BUYER && $this->amount > 0 ) {
						$str = trans('report.auto_refunded_by_weekly_billing_dispute_for_contract', [
							'contract' => $this->contract->title
						]);
					} else {
						$str = trans('report.weekly_billing_for_contract', [
							'contract' => $this->contract->title
						]);
					}

					return $str;
				} else if ( $this->isFixed() ) {
					if ( $this->milestone ) {
						if ( $this->milestone->isReleased() || $this->milestone->isPending() ) {
							$langKey = 'report.funds_released_for_milestone_of_contract';
						} else if ( $this->milestone->isRefunded() && $this->amount > 0 ) {
							$langKey = 'report.funds_refunded_for_milestone_of_contract';
						} else {
							$langKey = 'report.funds_deposited_for_milestone_of_contract';
						}

						return trans($langKey, [
							'milestone' => $this->milestone->name,
							'contract' => $this->contract->title
						]);
					}
				} else if ( $this->isRefund() ) {
					$str = trans('report.refund_for_contract', [
						'contract' => $this->contract->title
					]);

					if ( $this->note ) {
						$str .= '<div class="info">' . $this->note . '</div>';
					}

					return $str;
				}
			}
		}

		return '';
	}

	public function affiliate_description_string() {
		$primary = trans('user.affiliate.primary_affiliate');
		$secondary = trans('user.affiliate.secondary_affiliate');

        if ( $this->ref_user->isFreelancer() ) {
            if ( $this->isAffiliatePrimary() ) {
            	$str = $primary;
            } else {
            	$str = $secondary;
            }

            $str .= '<div>';
            if ( $this->amount > 0 ) {
            	$str .= trans('user.affiliate.affiliate_paid_desc');
            } else {
            	$str .= trans('user.affiliate.affiliate_refund_desc');
            }

            $str .= '</div>';
        } else {
            if ( $this->isAffiliatePrimary() ) {
            	$str = $primary;
            } else {
            	$str = $secondary;
            }

            $str .= '<div>' . trans('user.affiliate.affiliate_buyer_desc') . '</div>';
        }

        return $str;
	}

	public function client_string() {
		$site_name = Config::get('app.name');

		if ( $this->for == self::FOR_IJOBDESK ) {
			return $site_name;
		} else {
			return $this->user->fullname();
		}
	}

	public function payer_string($admin = false) {
		$site_name = Config::get('app.name');

		if ( $this->for == self::FOR_IJOBDESK ) {
			if ( $this->isAffiliate() ) {
				if ( $this->amount > 0 ) {
					return '<a href="' . route('admin.super.user.overview', ['user_id' => $this->ref_user->id]) . '">' . $this->ref_user->fullname() . '</a>';
				} else {
					return $site_name;
				}
			} else if ( $this->isFeaturedJob() ) {
				return '<a href="' . route('admin.super.user.overview', ['user_id' => $this->reference->user->id]) . '">' . $this->reference->user->fullname() . '</a>';
			} else if ( $this->contract ) {
				// Fee
				if ( $this->isRefund() ) {
					return $site_name;
				} else {
					return '<a href="' . route('admin.super.user.overview', ['user_id' => $this->contract->contractor->id]) . '">' . $this->contract->contractor->fullname() . '</a>';
				}
			} else if ( $this->isSiteWithdraw() ) {
				return $site_name;
			} else if ( $this->isWithdraw() ) {
				if ( $this->user_id == SUPERADMIN_ID ) {
					if ( $this->amount < 0 ) {
						return $site_name;
					} else {
						return '<a href="' . route('admin.super.user.overview', ['user_id' => $this->ref_user->id]) . '">' . $this->ref_user->fullname() . '</a>';
					}
				} else {
					if ( $this->amount < 0 ) {
						return '<a href="' . route('admin.super.user.overview', ['user_id' => $this->user->id]) . '">' . $this->user->fullname() . '</a>';
					} else {
						return $site_name;
					}
				}
			}

			return $site_name;
		} else {
			if ( $this->isWithdraw() ) {
				return $site_name;
			} else if ( $this->isAffiliate() ) {
				if ( $this->amount > 0 ) {
					return $site_name;
				} else {
					return '<a href="' . route('admin.super.user.overview', ['user_id' => $this->user->id]) . '">' . $this->user->fullname() . '</a>';
				}
			} else if ( $this->contract ) {
				if ( $this->isRefund() ) {
					if ( $this->amount > 0 ) {
						return '<a href="' . route('admin.super.user.overview', ['user_id' => $this->contract->contractor->id]) . '">' . $this->contract->contractor->fullname() . '</a>';
					} else {
						return '<a href="' . route('admin.super.user.overview', ['user_id' => $this->user->id]) . '">' . $this->user->fullname() . '</a>';
					}
				} else {
					if ( $this->user->isFreelancer() ) {
						return '<a href="' . route('admin.super.user.overview', ['user_id' => $this->reference->user->id]) . '">' . $this->reference->user->fullname() . '</a>';
					} else {
						if ( $this->isHourly() && $this->amount > 0 ) {
							return $site_name;
						}
					}

					if ( $this->milestone_id && $this->old_id ) {
						// Milestone refunded
						return '<a href="' . route('admin.super.user.overview', ['user_id' => $this->contract->contractor->id]) . '">' . $this->contract->contractor->fullname() . '</a>';
					}
				}
			}

			return '<a href="' . route('admin.super.user.overview', ['user_id' => $this->user->id]) . '">' . $this->user->fullname() . '</a>';
		}

		return '';
	}

	public function receiver_string($admin = false) {
		$site_name = Config::get('app.name');
		
		if ( $this->for == self::FOR_IJOBDESK ) {
			if ( $this->isSiteWithdraw() ) {
				return '-';
			} else if ( $this->isAffiliate() ) {
				if ( $this->amount > 0 ) {
					return $site_name;
				} else {
					return '<a href="' . route('admin.super.user.overview', ['user_id' => $this->ref_user->id]) . '">' . $this->ref_user->fullname() . '</a>';
				}
			} else if ( $this->isWithdraw() ) {
				if ( $this->user_id == SUPERADMIN_ID ) {
					if ( $this->amount < 0 ) {
						return '<a href="' . route('admin.super.user.overview', ['user_id' => $this->ref_user->id]) . '">' . $this->ref_user->fullname() . '</a>';
					} else {
						return $site_name;
					}
				} else {
					if ( $this->amount < 0 ) {
						return $site_name;
					} else {
						return '<a href="' . route('admin.super.user.overview', ['user_id' => $this->user->id]) . '">' . $this->user->fullname() . '</a>';
					}
				}			
			} else if ( $this->isRefund() ) {
				return '<a href="' . route('admin.super.user.overview', ['user_id' => $this->ref_user->id]) . '">' . $this->ref_user->fullname() . '</a>';
			}

			return $site_name;
		} else {
			if ( $this->isWithdraw() ) {
				return '<a href="' . route('admin.super.user.overview', ['user_id' => $this->user->id]) . '">' . $this->user->fullname() . '</a>';
			} else if ( $this->isAffiliate() ) {
				if ( $this->amount > 0 ) {
					return '<a href="' . route('admin.super.user.overview', ['user_id' => $this->user->id]) . '">' . $this->user->fullname() . '</a>';
				} else {
					return $site_name;
				}
			} else if ( $this->contract ) {
				if ( $this->isRefund() ) {
					if ( $this->for == self::FOR_BUYER ) {
						return '<a href="' . route('admin.super.user.overview', ['user_id' => $this->user->id]) . '">' . $this->user->fullname() . '</a>';
					} else {
						return '<a href="' . route('admin.super.user.overview', ['user_id' => $this->ref_user->id]) . '">' . $this->ref_user->fullname() . '</a>';
					}
				} else {
					if ( $this->milestone_id && $this->old_id ) {
						// Milestone refunded
						return '<a href="' . route('admin.super.user.overview', ['user_id' => $this->contract->buyer->id]) . '">' . $this->contract->buyer->fullname() . '</a>';
					}

					if ( $this->isHourly() && $this->user->isBuyer() && $this->amount > 0 ) {
						return '<a href="' . route('admin.super.user.overview', ['user_id' => $this->user->id]) . '">' . $this->user->fullname() . '</a>';
					}
					
					if ( $this->isFeaturedJob() ) {
						return $site_name;
					} else {
						return '<a href="' . route('admin.super.user.overview', ['user_id' => $this->contract->contractor->id]) . '">' . $this->contract->contractor->fullname() . '</a>';
					}
				}
			} else {
				return $site_name;
			}
		}

		return '';
	}

	public function buyer_string($admin = false) {
		if ( $this->isAffiliate() ) {
			return '';
		} else {
			if ( $this->for != self::FOR_IJOBDESK ) {
				if ( $this->contract ) {
					if ( $admin ) {
						return '<a href="' . route('admin.super.user.overview', ['user_id' => $this->contract->buyer->id]) . '">' . $this->contract->buyer->fullname() . '</a>';
					} else {
						return $this->contract->buyer->fullname();
					}
				}
			}
		}

		return '';
	}

	public function freelancer_string($admin = false) {
		if ( $this->isAffiliate() || $this->isFeaturedJob() ) {
			return '';
		} else {
			if ( $this->for != self::FOR_IJOBDESK ) {
				if ( $this->contract ) {
					if ( $admin ) {
						return '<a href="' . route('admin.super.user.overview', ['user_id' => $this->contract->contractor->id]) . '">' . $this->contract->contractor->fullname() . '</a>';
					} else {
						return $this->contract->contractor->fullname();
					}
				}
			}
		}

		return '';
	}

	public function amount_string($is_admin = false, $is_site_earning = false) {
		$currency = Settings::get('CURRENCY_SIGN');

		$negative = false;

		if ( $is_admin ) {
			if ( $this->amount < 0 ) {
				$negative = true;
				if ( $this->isRefund() ) {
					if ( $this->for == self::FOR_FREELANCER ) {
						if ( $this->reference ) {
							$amount = $this->reference->amount;
						} else {
							$amount = $this->amount;
						}
					} else if ( $this->for == self::FOR_IJOBDESK ) {
						$amount = abs($this->amount);

						if ( !$is_site_earning ) {
							$negative = false;
						}
					}
				} else {
					if ( $this->ref_amount != 0 ) {
						$amount = abs($this->ref_amount);
					} else {
						$amount = abs($this->amount);
					}
				}
			} else {
				if ( $this->isWithdraw() ) {
					$amount = $this->amount;
				} else {
					if ( $this->user && $this->user->isFreelancer() && $this->reference ) {
						if ( $this->reference->ref_amount != 0 ) {
							$amount = abs($this->reference->ref_amount);
						} else {
							$amount = abs($this->reference->amount);
						}
					} else {
						$amount = $this->amount;
					}
				}
			}
		} else {
			if ( $this->for == self::FOR_IJOBDESK ) {
				if ( $this->isWithdraw() ) {
					if ( $this->isCancelled() ) {
						if ( $this->amount > 0 ) {
							$amount = abs($this->amount);
						} else {
							$negative = true;
							$amount = abs($this->amount);
						}
					} else {
						$negative = true;
						$amount = abs($this->amount);
					}
				} else {
					if ( $this->amount > 0 ) {
						$negative = true;
						$amount = abs($this->amount);
					} else {
						$amount = abs($this->amount);
					}
				}
			} else {
				if ( $this->amount < 0 ) {
					$negative = true;

					if ( $this->isRefund() && $this->for == self::FOR_FREELANCER ) {
						$amount = $this->reference->amount;
					} else {
						$amount = abs($this->amount);
					}
				} else {
					if ( $this->isRefund() && $this->for == self::FOR_BUYER ) {
						$amount = abs($this->amount);
					} else {
						if ( $this->reference ) {
							$amount = abs($this->reference->amount);
						} else {
							$amount = abs($this->amount);
						}
					}
				}
			}
		}

		$amount_show = formatCurrency($amount, $currency);

		if ( $negative && !$is_admin ) {
			return '(' . $amount_show . ')';
		} else {
			return $amount_show;
		}
	}

	public function gateway_string($title = true) {
		if ( $this->user_payment_gateway_data ) {
			$json = json_decode($this->user_payment_gateway_data);

			if ( isset($json->gateway) ) {
				$payment_gateway = PaymentGateway::getByType($json->gateway);

				if ( $payment_gateway ) {
					$str = '';

					if ( $title ) {
						$str .= parse_json_multilang($payment_gateway->name) . ' - ';
					}

					if ( $payment_gateway->isPaypal() || $payment_gateway->isSkrill() || $payment_gateway->isPayoneer() ) {
						$str .= $json->email;
					} else if ( $payment_gateway->isWeixin() ) {
						$str .= $json->phoneNumber;
					} else if ( $payment_gateway->isWireTransfer() ) {
						$str .= $json->bankName;
					} else if ( $payment_gateway->isCreditCard() ) {
						$str .= $json->firstName . ' ' . $json->lastName . ' (' . $json->cardType . ')';
					}

					return $str;
				}
			}
		}

		return '';
	}

	public function gateway_logo() {
		if ( $this->user_payment_gateway_data ) {
			$json = json_decode($this->user_payment_gateway_data);

			if ( isset($json->gateway) ) {
				$payment_gateway = PaymentGateway::getByType($json->gateway);

		        if ( $payment_gateway->isCreditCard() ) {
		            return '/assets/images/pages/payment/' . $json->cardType . '.png';
		        } else {
		            return $payment_gateway->logo;
		        }
		    }
		}

		return '';
	}

	public function date_string($format = 'M d, Y') {
		if ( $this->isPending() || $this->isProceeding() || $this->isAvailable() ) {
			$str = '(' . trans('common.pending');
			if ( $this->isHourly() || $this->isFixed() || $this->isBonus() ) {
				$str .= ' - <br>' . $this->proceedDate();
			}
			$str .= ')';

			return $str;
		} else if ( $this->isSuspended() ) {
			return '(' . trans('common.suspended') . ')';
		}

		return format_date($format, $this->done_at);
	}

	/**
	* Get the client.
	*/
	public function user()
	{
		return $this->hasOne('iJobDesk\Models\User', 'id', 'user_id')->withTrashed();
	}

	/**
	* Get the client.
	*/
	public function ref_user()
	{
		return $this->hasOne('iJobDesk\Models\User', 'id', 'ref_user_id')->withTrashed();
	}

	/**
	* Get the contract.
	*/
	public function contract()
	{
		return $this->hasOne('iJobDesk\Models\Contract', 'id', 'contract_id');
	}

	/**
	* Get the project for only featured job fee transaction.
	*/
	public function project()
	{
		return $this->hasOne('iJobDesk\Models\Project', 'id', 'contract_id');
	}

	public function reference() {
		return $this->hasOne('iJobDesk\Models\TransactionLocal', 'id', 'ref_id');
	}

	public function old() {
		return $this->hasOne('iJobDesk\Models\TransactionLocal', 'id', 'old_id');
	}

	public function milestone() {
		return $this->hasOne('iJobDesk\Models\ContractMilestone', 'id', 'milestone_id');
	}

	/**
	* Get the user payment gateways.
	*/
	public function userPaymentGateway()
	{
		return $this->hasOne('iJobDesk\Models\UserPaymentGateway', 'id', 'user_payment_gateway_id')->withTrashed();
	}

	public function isOverdue() {
		return $this->done_at == NULL && date('Y-m-d H:i:s', strtotime('-' . TransactionLocal::DAYS_OVERDUE . ' days')) > $this->created_at;
	}

	public function proceedDate() {
		return date('M d, Y', strtotime($this->created_at) + Settings::get('DAYS_PROCESS_PENDING_TRANSACTION') * 24 * 3600);
	}

	/**
	* Converts type in string format to Model constant
	*
	* @author paulz
	* @created Mar 30, 2016
	* @param string or integer $type: Transaction type
	*/
	public static function parseType($type)
	{
		if (is_numeric($type)) {
			return $type;
		}

		$map = array_flip(self::$str_transaction_type);
		$type = ucfirst($type);
		if (!isset($map[$type])) {
			return false;
		}

		return $map[$type];
	}

	public function status_string()
	{
		if (isset(self::$str_status[$this->status])) {
			return self::$str_status[$this->status];
		}

		return '';
	}

	public function isPending() {
		return $this->status == self::STATUS_PENDING;
	}

	public function isAvailable() {
		return $this->status == self::STATUS_AVAILABLE;
	}

	public function isDone() {
		return $this->status == self::STATUS_DONE;
	}

	public function isReview() {
		return $this->status == self::STATUS_REVIEW;
	}

	public function isProceeding() {
		return $this->status == self::STATUS_PROCEEDING;
	}

	public function isSuspended() {
		return $this->status == self::STATUS_SUSPENDED;
	}

	public function isCancelled() {
		return $this->status == self::STATUS_CANCELLED;
	}

	public function isRefund() {
		return $this->type == self::TYPE_REFUND;
	}

	public function isDeposit() {
		return $this->type == self::TYPE_CHARGE;
	}

	public function isWithdraw() {
		return $this->type == self::TYPE_WITHDRAWAL;
	}

	public function isHourly() {
		return $this->type == self::TYPE_HOURLY;
	}

	public function isFixed() {
		return $this->type == self::TYPE_FIXED;
	}

	public function isBonus() {
		return $this->type == self::TYPE_BONUS;
	}

	public function isAffiliate() {
		return in_array($this->type, [self::TYPE_AFFILIATE, self::TYPE_AFFILIATE_CHILD]);
	}

	public function isAffiliatePrimary() {
		return $this->type == self::TYPE_AFFILIATE;
	}

	public function isAffiliateSecondary() {
		return $this->type == self::TYPE_AFFILIATE_CHILD;
	}

	public function isFeaturedJob() {
		return $this->type == self::TYPE_FEATURED_JOB;
	}

	public function isSiteWithdraw() {
		return $this->type == self::TYPE_SITE_WITHDRAWAL;
	}

	/**
	* Options for the <select> of "for whom"
	*
	* @author paulz
	* @created Mar 29, 2016
	*/
	public static function getForOptions()
	{
		return [
			'Buyer' => self::FOR_BUYER,
			'Freelancer' => self::FOR_FREELANCER,
			'Fee' => self::FOR_IJOBDESK
		];
	}

	/**
	* Returns array for Transaction type <select> tag
	*
	* @author paulz
	* @created Mar 29, 2016
	*
	* @param string $for: all | contract | buyer | freelancer
	*/
	public static function getTypeOptions($for = 'all')
	{
		$options = [];

		switch ($for) {
			case "all":
			case "buyer":
			case "freelancer":
				$options = array_flip(self::$str_transaction_type);
				break;

			case "hourly_contract":
			case "fixed_contract":
				if ($for == "hourly_contract") {
					$vs = [
					self::TYPE_HOURLY, self::TYPE_BONUS, self::TYPE_REFUND
					];
				} else {
					$vs = [
					self::TYPE_FIXED, self::TYPE_BONUS, self::TYPE_REFUND
					];
				}

				foreach($vs as $v) {
					$options[self::$str_transaction_type[$v]] = $v;
				}

				break;
		}

		return $options;
	}

	/**
	* Get Statement between two days
	* @author Ro Un Nam
	* @since Jun 12, 2017
	* @param [user, balance, from, to, type, contract_id]
	*/
	public static function getStatement($opts) {

		$user = $opts['user'];

		$local_timezone_offset = getTimezoneOffset('UTC', date_default_timezone_get());

		// Get the transactions depending to the user's timezone
		if ( isset($opts['admin']) && $opts['admin'] ) {
			$user_timezone_offset = $local_timezone_offset;
		} else {		
			if ( $user->contact && $user->contact->timezone ) {
				$user_timezone_offset = timezoneToString($user->contact->timezone->gmt_offset, false);
			}		

			if ( !isset($user_timezone_offset) ) {
				$user_timezone_offset = $local_timezone_offset;
			}
		}

		$credits = $debits = 0;

		// Get credits transactions
		$credits_transactions = self::whereIn('status', [
							self::STATUS_DONE,
							self::STATUS_CANCELLED
						])
						->where('type', '<>', self::TYPE_WITHDRAWAL)
						->where('amount', '>=', 0)
						->where('user_id', $user->id);

		if ( $user->isBuyer() ) {
			$credits_transactions = $credits_transactions->where('for', self::FOR_BUYER);
		} else if ( $user->isFreelancer() ) {
			$credits_transactions = $credits_transactions->where('for', self::FOR_FREELANCER);
		}

		if ( isset($opts['from']) && $opts['from'] ) {
			$credits_transactions = $credits_transactions->whereRaw("CONVERT_TZ(done_at, '" . $local_timezone_offset . "', '" . $user_timezone_offset . "') >= '" . $opts['from'] . "'");
		}

		if ( isset($opts['to']) && $opts['to'] ) {
			$credits_transactions = $credits_transactions->whereRaw("CONVERT_TZ(done_at, '" . $local_timezone_offset . "', '" . $user_timezone_offset . "') <= '" . $opts['to'] . "'");
		}

		if ( isset($opts['contract_id']) && $opts['contract_id'] ) {
			$credits_transactions = $credits_transactions->where('contract_id', $opts['contract_id']);
		}

		if ( isset($opts['type']) && $opts['type'] ) {
			// Affiliate
			if ( $opts['type'] == self::TYPE_AFFILIATE ) {
				$credits_transactions = $credits_transactions->whereIn('type', [
					self::TYPE_AFFILIATE,
					self::TYPE_AFFILIATE_CHILD
				]);
			} else {
				$credits_transactions = $credits_transactions->where('type', $opts['type']);
			}
		}

		$credits_transactions = $credits_transactions->get();

		$credits = 0;
		if ( $credits_transactions ) {
			foreach ( $credits_transactions as $t ) {
				if ( in_array($t->type, [
						self::TYPE_AFFILIATE,
						self::TYPE_AFFILIATE_CHILD
					]) ) {
					$credits += $t->amount;
				} else if ( $t->user->isFreelancer() ) {
					$credits += abs($t->reference->amount);
				} else {
					$credits += $t->amount;
				}
			}
		}

		// Get withdraw cancelled transactions
		if ( !isset($opts['contract_id']) || !$opts['contract_id'] ) {
			$credits_withdraw_cancelled = self::where('type', self::TYPE_WITHDRAWAL)
												->where('user_id', $user->id)
												->where('status', self::STATUS_CANCELLED)
												->where('amount', '>=', 0);

			if ( isset($opts['from']) && $opts['from'] ) {
				$credits_withdraw_cancelled = $credits_withdraw_cancelled->whereRaw("CONVERT_TZ(done_at, '" . $local_timezone_offset . "', '" . $user_timezone_offset . "') >= '" . $opts['from'] . "'");
			}

			if ( isset($opts['to']) && $opts['to'] ) {
				$credits_withdraw_cancelled = $credits_withdraw_cancelled->whereRaw("CONVERT_TZ(done_at, '" . $local_timezone_offset . "', '" . $user_timezone_offset . "') <= '" . $opts['to'] . "'");
			}

			if ( isset($opts['type']) && $opts['type'] ) {
				$credits_withdraw_cancelled = $credits_withdraw_cancelled->where('type', $opts['type']);
			}

			$credits_withdraw_cancelled = $credits_withdraw_cancelled->sum('amount');
			$credits += $credits_withdraw_cancelled;
		}

		// Get refund fee
		$credits_refund_fee = self::where('type', self::TYPE_REFUND)
									->where('for', self::FOR_IJOBDESK)
									->where('amount', '<', 0)
									->where('ref_user_id', $user->id);

		if ( isset($opts['from']) && $opts['from'] ) {
			$credits_refund_fee = $credits_refund_fee->whereRaw("CONVERT_TZ(done_at, '" . $local_timezone_offset . "', '" . $user_timezone_offset . "') >= '" . $opts['from'] . "'");
		}

		if ( isset($opts['to']) && $opts['to'] ) {
			$credits_refund_fee = $credits_refund_fee->whereRaw("CONVERT_TZ(done_at, '" . $local_timezone_offset . "', '" . $user_timezone_offset . "') <= '" . $opts['to'] . "'");
		}

		if ( isset($opts['contract_id']) && $opts['contract_id'] ) {
			$credits_refund_fee = $credits_refund_fee->where('contract_id', $opts['contract_id']);
		}

		if ( isset($opts['type']) && $opts['type'] ) {
			// Affiliate
			if ( $opts['type'] == self::TYPE_AFFILIATE ) {
				$credits_refund_fee = $credits_refund_fee->whereIn('type', [
					self::TYPE_AFFILIATE,
					self::TYPE_AFFILIATE_CHILD
				]);
			} else {
				$credits_refund_fee = $credits_refund_fee->where('type', $opts['type']);
			}
		}

		$credits_refund_fee = abs($credits_refund_fee->sum('amount'));

		$credits += $credits_refund_fee;

		// Begin for debit
		// Get debit transactions
		$debits = self::whereNotIn('type', [
							self::TYPE_WITHDRAWAL,
							self::TYPE_REFUND,
						])
						->where('amount', '<', 0)
						->where('user_id', $user->id);

		if ( $user->isBuyer() ) {
			$debits = $debits->where('for', self::FOR_BUYER)
							->whereIn('status', [
								self::STATUS_AVAILABLE,
								self::STATUS_DONE,
								self::STATUS_CANCELLED
							]);
		} else if ( $user->isFreelancer() ) {
			$debits = $debits->where('for', self::FOR_FREELANCER)
							->whereIn('status', [
								self::STATUS_AVAILABLE,
								self::STATUS_DONE,
								self::STATUS_CANCELLED
							]);
		}

		if ( isset($opts['from']) && $opts['from'] ) {
			$debits = $debits->whereRaw("CONVERT_TZ(done_at, '" . $local_timezone_offset . "', '" . $user_timezone_offset . "') >= '" . $opts['from'] . "'");
		}

		if ( isset($opts['to']) && $opts['to'] ) {
			$debits = $debits->whereRaw("CONVERT_TZ(done_at, '" . $local_timezone_offset . "', '" . $user_timezone_offset . "') <= '" . $opts['to'] . "'");
		}

		if ( isset($opts['contract_id']) && $opts['contract_id'] ) {
			$debits = $debits->where('contract_id', $opts['contract_id']);
		}

		if ( isset($opts['type']) && $opts['type'] ) {
			// Affiliate
			if ( $opts['type'] == self::TYPE_AFFILIATE ) {
				$debits = $debits->whereIn('type', [
					self::TYPE_AFFILIATE,
					self::TYPE_AFFILIATE_CHILD
				]);
			} else {
				$debits = $debits->where('type', $opts['type']);
			}
		}

		$debits = abs($debits->sum('amount'));

		// Get refund transactions
		$debits_refund = self::where('type', self::TYPE_REFUND)
						->where('amount', '>', 0)
						->where('ref_user_id', $user->id);

		if ( isset($opts['from']) && $opts['from'] ) {
			$debits_refund = $debits_refund->whereRaw("CONVERT_TZ(done_at, '" . $local_timezone_offset . "', '" . $user_timezone_offset . "') >= '" . $opts['from'] . "'");
		}

		if ( isset($opts['to']) && $opts['to'] ) {
			$debits_refund = $debits_refund->whereRaw("CONVERT_TZ(done_at, '" . $local_timezone_offset . "', '" . $user_timezone_offset . "') <= '" . $opts['to'] . "'");
		}

		if ( isset($opts['contract_id']) && $opts['contract_id'] ) {
			$debits_refund = $debits_refund->where('contract_id', $opts['contract_id']);
		}

		if ( isset($opts['type']) && $opts['type'] ) {
			// Affiliate
			if ( $opts['type'] == self::TYPE_AFFILIATE ) {
				$debits_refund = $debits_refund->whereIn('type', [
					self::TYPE_AFFILIATE,
					self::TYPE_AFFILIATE_CHILD
				]);
			} else {
				$debits_refund = $debits_refund->where('type', $opts['type']);
			}
		}

		$debits_refund = abs($debits_refund->sum('amount'));

		$debits += $debits_refund;

		// Get withdraw transactions
		if ( !isset($opts['contract_id']) || !$opts['contract_id'] ) {
			$debits_withdraw = self::where('type', self::TYPE_WITHDRAWAL)
									->where('user_id', $user->id)
									->where('amount', '<', 0);

			if ( isset($opts['from']) && $opts['from'] ) {
				$debits_withdraw = $debits_withdraw->whereRaw("CONVERT_TZ(created_at, '" . $local_timezone_offset . "', '" . $user_timezone_offset . "') >= '" . $opts['from'] . "'");
			}

			if ( isset($opts['to']) && $opts['to'] ) {
				$debits_withdraw = $debits_withdraw->whereRaw("CONVERT_TZ(created_at, '" . $local_timezone_offset . "', '" . $user_timezone_offset . "') <= '" . $opts['to'] . "'");
			}

			if ( isset($opts['type']) && $opts['type'] ) {
				$debits_withdraw = $debits_withdraw->where('type', $opts['type']);
			}

			$debits_withdraw = abs($debits_withdraw->sum('amount'));

			$debits += $debits_withdraw;
		}

		// Get fee transactions
		$debits_fee = self::where('for', self::FOR_IJOBDESK)
							->whereNotIn('type', [
								self::TYPE_REFUND,
								self::TYPE_FEATURED_JOB,
								self::TYPE_WITHDRAWAL,
								self::TYPE_AFFILIATE,
								self::TYPE_AFFILIATE_CHILD
							])
							->whereIn('status', [
								self::STATUS_DONE,
								self::STATUS_CANCELLED
							])
							->where('amount', '>=', 0);

		$debits_fee = $debits_fee->where('ref_user_id', $user->id);

		if ( isset($opts['from']) && $opts['from'] ) {
			$debits_fee = $debits_fee->whereRaw("CONVERT_TZ(done_at, '" . $local_timezone_offset . "', '" . $user_timezone_offset . "') >= '" . $opts['from'] . "'");
		}

		if ( isset($opts['to']) && $opts['to'] ) {
			$debits_fee = $debits_fee->whereRaw("CONVERT_TZ(done_at, '" . $local_timezone_offset . "', '" . $user_timezone_offset . "') <= '" . $opts['to'] . "'");
		}

		if ( isset($opts['contract_id']) && $opts['contract_id'] ) {
			$debits_fee = $debits_fee->where('contract_id', $opts['contract_id']);
		}

		if ( isset($opts['type']) && $opts['type'] ) {
			$debits_fee = $debits_fee->where('type', $opts['type']);
		}

		$debits_fee = $debits_fee->sum('amount');

		$debits += $debits_fee;

		$change = $credits - $debits;

		$statement = [
			'beginning' => 0,
			'in'       => 0, 
			'out'      => 0, 
			'change'   => 0, 
			'ending'   => 0
		];

		$wallet_history_from = WalletHistory::where('user_id', $user->id)
										->whereRaw("CONVERT_TZ(created_at, '" . $local_timezone_offset . "', '" . $user_timezone_offset . "') < '" . $opts['from'] . "'")
										->orderBy('updated_at', 'desc')
										->orderBy('id', 'desc')
										->first();

		$wallet_history_to = WalletHistory::where('user_id', $user->id)
										->whereRaw("CONVERT_TZ(created_at, '" . $local_timezone_offset . "', '" . $user_timezone_offset . "') >= '" . $opts['from'] . "'")
										->whereRaw("CONVERT_TZ(created_at, '" . $local_timezone_offset . "', '" . $user_timezone_offset . "') <= '" . $opts['to'] . "'")
										->orderBy('updated_at', 'desc')
										->orderBy('id', 'desc')
										->first();

		$statement['beginning'] = $wallet_history_from ? $wallet_history_from->balance : 0;
		$statement['in'] = $credits;
		$statement['out'] = $debits;
		$statement['change'] = $credits - $debits;
		$statement['ending'] = $statement['beginning'] + $credits - $debits;

		return $statement;
	}

	/**
	* Get iJobDesk Holding Statement between two days
	* @author Ro Un Nam
	* @since Jun 12, 2017
	* @param [balance, from, to, type]
	*/
	public static function getHoldingStatement($opts) {

		$credits = $debits = 0;

		// Get credits transactions
		$credits_transactions = self::where('status', TransactionLocal::STATUS_DONE)
									->where('type', TransactionLocal::TYPE_CHARGE);

		if ( isset($opts['from']) && $opts['from'] ) {
			$credits_transactions = $credits_transactions->where('created_at', '>=', $opts['from']);
		}

		if ( isset($opts['to']) && $opts['to'] ) {
			$credits_transactions = $credits_transactions->where('created_at', '<=', $opts['to']);
		}

		if ( isset($opts['type']) && $opts['type'] ) {
			$credits_transactions = $credits_transactions->where('type', $opts['type']);
		}

		$credits = $credits_transactions->sum('amount');

		// Get withdraw cancelled transactions
		$credits_withdraw_cancelled = self::where('type', TransactionLocal::TYPE_WITHDRAWAL)
								->where('for', '<>', TransactionLocal::FOR_IJOBDESK)
								->where('user_id', '<>', SUPERADMIN_ID)
								->where('status', TransactionLocal::STATUS_CANCELLED)
								->where('amount', '>=', 0);

		if ( isset($opts['from']) && $opts['from'] ) {
			$credits_withdraw_cancelled = $credits_withdraw_cancelled->where('created_at', '>=', $opts['from']);
		}

		if ( isset($opts['to']) && $opts['to'] ) {
			$credits_withdraw_cancelled = $credits_withdraw_cancelled->where('created_at', '<=', $opts['to']);
		}

		if ( isset($opts['type']) && $opts['type'] ) {
			$credits_withdraw_cancelled = $credits_withdraw_cancelled->where('type', $opts['type']);
		}

		$credits += $credits_withdraw_cancelled->sum('amount');

		// Get debit transactions
		$debits_withdraw = self::where('type', TransactionLocal::TYPE_WITHDRAWAL)
								->where('for', '<>', TransactionLocal::FOR_IJOBDESK)
								->where('user_id', '<>', SUPERADMIN_ID)
								->whereIn('status', [
									TransactionLocal::STATUS_DONE,
									TransactionLocal::STATUS_CANCELLED
								])
								->where('amount', '<', 0);

		if ( isset($opts['from']) && $opts['from'] ) {
			$debits_withdraw = $debits_withdraw->where('created_at', '>=', $opts['from']);
		}

		if ( isset($opts['to']) && $opts['to'] ) {
			$debits_withdraw = $debits_withdraw->where('created_at', '<=', $opts['to']);
		}

		if ( isset($opts['type']) && $opts['type'] ) {
			$debits_withdraw = $debits_withdraw->where('type', $opts['type']);
		}

		$debits += abs($debits_withdraw->sum('amount'));

		// Get iJobDesk withdraw transactions
		$debits_ijobdesk_withdraw = self::where('type', TransactionLocal::TYPE_SITE_WITHDRAWAL)
										->where('status', TransactionLocal::STATUS_DONE);

		if ( isset($opts['from']) && $opts['from'] ) {
			$debits_ijobdesk_withdraw = $debits_ijobdesk_withdraw->where('created_at', '>=', $opts['from']);
		}

		if ( isset($opts['to']) && $opts['to'] ) {
			$debits_ijobdesk_withdraw = $debits_ijobdesk_withdraw->where('created_at', '<=', $opts['to']);
		}

		if ( isset($opts['type']) && $opts['type'] ) {
			$debits_ijobdesk_withdraw = $debits_ijobdesk_withdraw->where('type', $opts['type']);
		}

		$debits += abs($debits_ijobdesk_withdraw->sum('amount'));

		$change = $credits - $debits;

		$statement = [
			'beginning' => 0,
			'in'       => 0, 
			'out'      => 0, 
			'change'   => 0, 
			'ending'   => 0
		];

		$from = date('Y-m-d', strtotime($opts['from']));
		$to = date('Y-m-d', strtotime($opts['to']));

		$wallet_history_from = SiteWalletHistory::where('type', SiteWalletHistory::TYPE_HOLDING)
													->where('date', '<', $from)
													->orderBy('updated_at', 'desc')
													->orderBy('id', 'desc')
													->first();

		$wallet_history_to = SiteWalletHistory::where('type', SiteWalletHistory::TYPE_HOLDING)
												->whereBetween('date', [$from, $to])
												->orderBy('updated_at', 'desc')
												->orderBy('id', 'desc')
												->first();

		$statement['beginning'] = $wallet_history_from ? $wallet_history_from->balance : 0;
		$statement['in'] = $credits;
		$statement['out'] = $debits;
		$statement['change'] = $credits - $debits;
		$statement['ending'] = $statement['beginning'] + $credits - $debits;

		return $statement;
	}

	/**
	* Get iJobDesk Earning Statement between two days
	* @author Ro Un Nam
	* @since Jun 12, 2017
	* @param [balance, from, to, type, user_id]
	*/
	public static function getEarningStatement($opts) {

		$credits = $debits = 0;

		// Get credits transactions
		$credits_transactions = self::where('status', TransactionLocal::STATUS_DONE)
									->where('for', TransactionLocal::FOR_IJOBDESK)
									->where('user_id', SUPERADMIN_ID)
									->where('amount', '>=', 0);

		if ( isset($opts['from']) && $opts['from'] ) {
			$credits_transactions = $credits_transactions->where('created_at', '>=', $opts['from']);
		}

		if ( isset($opts['to']) && $opts['to'] ) {
			$credits_transactions = $credits_transactions->where('created_at', '<=', $opts['to']);
		}

		if ( isset($opts['type']) && $opts['type'] ) {
			$credits_transactions = $credits_transactions->where('type', $opts['type']);
		}

		if ( isset($opts['user_id']) && $opts['user_id'] ) {
			$credits_transactions = $credits_transactions->where('ref_user_id', $opts['user_id']);
		}

		$credits = $credits_transactions->sum('amount');

		// Get debit transactions
		$debits_transactions = self::where('status', TransactionLocal::STATUS_DONE)
									->where('for', TransactionLocal::FOR_IJOBDESK)
									->where('user_id', SUPERADMIN_ID)
									->where('amount', '<', 0);

		if ( isset($opts['from']) && $opts['from'] ) {
			$debits_transactions = $debits_transactions->where('created_at', '>=', $opts['from']);
		}

		if ( isset($opts['to']) && $opts['to'] ) {
			$debits_transactions = $debits_transactions->where('created_at', '<=', $opts['to']);
		}

		if ( isset($opts['type']) && $opts['type'] ) {
			$debits_transactions = $debits_transactions->where('type', $opts['type']);
		}

		if ( isset($opts['user_id']) && $opts['user_id'] ) {
			$credits_transactions = $credits_transactions->where('ref_user_id', $opts['user_id']);
		}

		$debits = abs($debits_transactions->sum('amount'));

		$change = $credits - $debits;

		$statement = [
			'beginning' => 0,
			'in'       => 0, 
			'out'      => 0, 
			'change'   => 0, 
			'ending'   => 0
		];

		$from = date('Y-m-d', strtotime($opts['from']));
		$to = date('Y-m-d', strtotime($opts['to']));

		$wallet_history_from = SiteWalletHistory::where('type', SiteWalletHistory::TYPE_EARNING)
													->where('date', '<', $from)
													->orderBy('updated_at', 'desc')
													->orderBy('id', 'desc')
													->first();

		$wallet_history_to = SiteWalletHistory::where('type', SiteWalletHistory::TYPE_EARNING)
												->whereBetween('date', [$from, $to])
												->orderBy('updated_at', 'desc')
												->orderBy('id', 'desc')
												->first();

		$statement['beginning'] = $wallet_history_from ? $wallet_history_from->balance : 0;
		$statement['in'] = $credits;
		$statement['out'] = $debits;
		$statement['change'] = $credits - $debits;
		$statement['ending'] = $statement['beginning'] + $credits - $debits;

		return $statement;
	}

	/**
	* Get total amount for the params
	* @author Ro Un Nam
	* @since Dec 21, 2017
	*/
	public static function getAmount($params = []) {
		$total = 0;

		if ( isset($params['mode']) ) {
			if ( $params['mode'] == 'escrow' ) {
				$total = ContractMilestone::leftJoin('transactions', 'contract_milestones.transaction_id', '=', 'transactions.id')
								->where('fund_status', ContractMilestone::FUNDED)
								->where('transaction_id', '>', 0)
								->sum('transactions.amount');
				$total = abs($total);
			} else if ( $params['mode'] == 'payment' ) {
				$transactions = self::where(function($query) {
									$query->where('type', self::TYPE_CHARGE)
										->orWhere(function($query) {
											$query->whereIn('type', [
												self::TYPE_FIXED,
												self::TYPE_HOURLY,
												self::TYPE_BONUS,
												self::TYPE_REFUND
											])->where('for', '<>', self::FOR_BUYER);
										})
										->orWhere(function($query) {
											$query->whereIn('type', [
												self::TYPE_AFFILIATE,
												self::TYPE_AFFILIATE_CHILD
											])->where('for', '<>', self::FOR_IJOBDESK);
										});
								});

				if ( isset($params['from']) && $params['from'] ) {
					$transactions->where('created_at', '>=', $params['from']);
				}

				if ( isset($params['to']) && $params['to'] ) {
					$transactions->where('created_at', '<=', $params['to']);
				}

				if ( isset($params['type']) && $params['type'] ) {
					$transactions->where('type', $params['type']);
				}

				if ( isset($params['status']) ) {
					if ( is_array($params['status']) ) {
						$transactions->whereIn('status', $params['status']);
					} else {
						$transactions->where('status', $params['status']);
					}
				}

				$total = $transactions->sum('amount');

				if ( !isset($params['type']) || !$params['type'] ) {
					$transactions = self::where('type', self::TYPE_FEATURED_JOB);

					if ( isset($params['from']) && $params['from'] ) {
						$transactions->where('created_at', '>=', $params['from']);
					}

					if ( isset($params['to']) && $params['to'] ) {
						$transactions->where('created_at', '<=', $params['to']);
					}

					if ( isset($params['status']) ) {
						if ( is_array($params['status']) ) {
							$transactions->whereIn('status', $params['status']);
						} else {
							$transactions->where('status', $params['status']);
						}
					}

					$total += abs($transactions->sum('amount'));
				}
			} else if ( $params['mode'] == 'withdraw' ) {
				$transactions = self::where('type', self::TYPE_WITHDRAWAL)
									->where('user_id', '<>', SUPERADMIN_ID)
									->where('status', '<>', self::STATUS_DONE);

				$total += abs($transactions->sum('amount'));
			}
		}

		return doubleval($total);
	}

	/**
	* Pay for bonus
	*/
	public static function pay($opts) {
		$result = [
			'success' => false,
			'message' => '',
		];

		$defaults = [
			'cid' => 0,
	  		'amount' => 0,
	  		'status' => self::STATUS_PENDING,
	  	];

		$opts = array_merge($defaults, $opts);
		extract($opts);

		$type = self::TYPE_BONUS;

		if ( !$cid ) {
			Log::error('[TransactionLocal::pay()] Error: Contract ID is not given.');
			return $result;
		}

		if ( $amount <= 0 ) {
			Log::error('[TransactionLocal::pay()] Error: Invalid amount to pay.');
			return $result;
		}

		if ( !isset($note) ) {
			$note = '';
		}

		$c = Contract::find($cid);
		if ( !$c ) {
			Log::error('[TransactionLocal::pay()] Error: Contract #' . $cid . ' is invalid.');
			return $result;
		}

		if ( $c->buyer->isSuspended() || $c->buyer->isFinancialSuspended() ) {
			Log::error('[TransactionLocal::pay()] Error: User #' . $c->buyer_id . ' has been suspended or financial suspended.');
			$result['message'] = trans('message.freelancer.payment.failed_user_suspended');
			return $result;
		}

		if ( $c->contractor->isSuspended() || $c->contractor->isFinancialSuspended() ) {
			Log::error('[TransactionLocal::pay()] Error: User #' . $c->contractor_id . ' has been suspended or financial suspended.');
			$result['message'] = trans('message.buyer.payment.failed_user_suspended');
			return $result;
		}

		try {

			// Calculate amount to pay contractor and fee
			$fee_amount = Settings::getFee($amount, $c->isAffiliated());
			$contractor_amount = round($amount - $fee_amount, 2);
			$buyer_amount = -$amount;

			// Get wallets
			$buyerWallet = Wallet::account($c->buyer_id);
			$freelancerWallet = Wallet::account($c->contractor_id);

			// Create buyer transaction
			$bt = new TransactionLocal;
			$bt->type = $type;
			$bt->for = self::FOR_BUYER;
			$bt->user_id = $c->buyer_id;
			$bt->contract_id = $cid;
			$bt->note = $note;
			$bt->amount = $buyer_amount;
			$bt->status = self::STATUS_DONE;
			$bt->done_at = date('Y-m-d H:i:s');

			if ( $bt->save() ) {
				$result['transaction_id'] = $bt->id;

				// Update buyer wallet and history
				$newAmount = round($buyerWallet->amount - abs($buyer_amount), 2);
				$buyerWallet->amount = $newAmount;
				$buyerWallet->save();

				WalletHistory::addHistory($c->buyer_id, $newAmount, $bt->id);

				// Create contractor transaction
				$ct = new TransactionLocal;
				$ct->type = $type;
				$ct->for = self::FOR_FREELANCER;
				$ct->user_id = $c->contractor_id;
				$ct->contract_id = $cid;
				$ct->amount = $contractor_amount;
				$ct->note = $note;
				$ct->status = self::STATUS_AVAILABLE;
				$ct->ref_id = $bt->id;

				// Create fee transaction
				$ft = new TransactionLocal;
				$ft->type = $type;
				$ft->for = self::FOR_IJOBDESK;
				$ft->user_id = SUPERADMIN_ID;
				$ft->contract_id = $cid;
				$ft->amount = $fee_amount;
				// $ft->note = $note;
				$ft->status = self::STATUS_AVAILABLE;
				$ft->ref_user_id = $c->contractor_id;

				// Created freelancer transaction
				if ( $ct->save() ) {
					// Update contract_meter
					$c->meter->updateTotal(abs($amount));

					$ft->ref_id = $ct->id;
					$ft->save();
				}

				// Process affiliate for invitation buyer
				// self::process_affiliate($bt);
			} else {
				Log::error('[TransactionLocal::pay()] Error: An error occured while creating contractor transaction.');
				return $result;
			}
		} catch ( Exception $e ) {
			Log::error('[TransactionLocal::pay()] Error: ' . $e->getMessage());
			return $result;
		}

		$freelancer_name = $c->contractor->fullname();
		$buyer_name = $c->buyer->fullname();
		$amount = formatCurrency(abs($buyer_amount));

		// Send notification to buyer
		Notification::send(Notification::BUYER_PAY_BONUS, SUPERADMIN_ID, $c->buyer_id, [
			'freelancer_name' => $freelancer_name,
			'amount' => $amount
		]);

		// Send notification to freelancer
		Notification::send(Notification::PAY_BONUS, SUPERADMIN_ID, $c->contractor_id, [
			'buyer_name' => $buyer_name,
			'amount' => $amount
		]);

		// Send email to freelancer
		EmailTemplate::send($c->contractor, 'SEND_BONUS', 1, [
			'USER' => $freelancer_name,
			'BUYER' => $buyer_name,
			'CONTRACT_TITLE' => $c->title,
			'AMOUNT' => $amount,
			'COMMENT' => $note,
		]);

		// Send email to buyer
		EmailTemplate::send($c->buyer, 'SEND_BONUS', 2, [
			'USER' => $buyer_name,
			'FREELANCER' => $freelancer_name,
			'CONTRACT_TITLE' => $c->title,
			'AMOUNT' => $amount,
			'COMMENT' => $note,
		]);

		$result['success'] = true;

		return $result;
	}

	/**
	* Refund for the contract
	*/
	public static function refund($opts) {
		$result = [
			'success' => false,
			'message' => '',
		];

		$defaults = [
			'cid' => 0,
	  		'amount' => 0,
	  		'status' => self::STATUS_DONE,
	  	];

		$opts = array_merge($defaults, $opts);
		extract($opts);

		$type = self::TYPE_REFUND;

		if ( !$cid ) {
			Log::error('[TransactionLocal::refund()] Error: Contract ID is not given.');
			return $result;
		}

		if ( $amount <= 0 ) {
			Log::error('[TransactionLocal::refund()] Error: Invalid amount to pay.');
			return $result;
		}

		if ( !isset($note) ) {
			$note = '';
		}

		$c = Contract::find($cid);
		if ( !$c ) {
			Log::error('[TransactionLocal::refund()] Error: Contract #' . $cid . ' is invalid.');
			return $result;
		}

		if ( $c->buyer->isSuspended() || $c->buyer->isFinancialSuspended() ) {
			Log::error('[TransactionLocal::refund()] Error: User #' . $c->buyer_id . ' has been suspended or financial suspended.');
			$result['message'] = trans('message.freelancer.payment.failed_user_suspended');
			return $result;
		}

		if ( $c->contractor->isSuspended() || $c->contractor->isFinancialSuspended() ) {
			Log::error('[TransactionLocal::refund()] Error: User #' . $c->contractor_id . ' has been suspended or financial suspended.');
			$result['message'] = trans('message.buyer.payment.failed_user_suspended');
			return $result;
		}

		try {

			$now = date('Y-m-d H:i:s');

			// Calculate amount to pay buyer and fee
			$fee_amount = Settings::getFee($amount, $c->isAffiliated());
			$contractor_amount = round(-($amount - $fee_amount), 2);
			$buyer_amount = $amount;
			$fee_amount = -$fee_amount;

			// Get wallets
			$buyerWallet = Wallet::account($c->buyer_id);
			$freelancerWallet = Wallet::account($c->contractor_id);

			// Create contractor transaction
			$ct = new TransactionLocal;
			$ct->type = $type;
			$ct->for = self::FOR_FREELANCER;
			$ct->user_id = $c->contractor_id;
			$ct->contract_id = $cid;
			$ct->amount = $contractor_amount;
			$ct->note = $note;
			$ct->status = self::STATUS_DONE;
			$ct->ref_user_id = $c->buyer_id;
			$ct->done_at = $now;

			if ( $ct->save() ) {
			  	// Update freelancer wallet and history
			  	$newAmount = round($freelancerWallet->amount - abs($contractor_amount), 2);
				$freelancerWallet->amount = $newAmount;
				$freelancerWallet->save();

				WalletHistory::addHistory($c->contractor_id, $newAmount, $ct->id);

				// Update contract_meter
				$c->meter->updateTotal(-$amount);

				// Create buyer transaction
				$bt = new TransactionLocal;
				$bt->type = $type;
				$bt->for = self::FOR_BUYER;
				$bt->user_id = $c->buyer_id;
				$bt->contract_id = $cid;
				$bt->note = $note;
				$bt->amount = $buyer_amount;
				$bt->ref_user_id = $c->contractor_id;
				$bt->ref_id = $ct->id;
				$bt->status = self::STATUS_DONE;
				$bt->done_at = $now;
				
				if ( $bt->save() ) {
					$result['transaction_id'] = $bt->id;

					$ct->ref_id = $bt->id;
					$ct->save();

				  	// Update buyer wallet and history
				  	$newAmount = round($buyerWallet->amount + $buyer_amount, 2);
					$buyerWallet->amount = $newAmount;
					$buyerWallet->save();

					WalletHistory::addHistory($c->buyer_id, $newAmount, $bt->id);

					// Create fee transaction
					$ft = new TransactionLocal;
					$ft->type = $type;
					$ft->for = self::FOR_IJOBDESK;
					$ft->user_id = SUPERADMIN_ID;
					$ft->contract_id = $cid;
					$ft->amount = $fee_amount;
					$ft->status = self::STATUS_DONE;
					$ft->ref_user_id = $c->contractor_id;
					$ft->ref_id = $bt->id;
					$ft->done_at = $now;
					
					if ( $ft->save() ) {
						// Update iJobDesk earning wallet history
						$earning = SiteWallet::earning();
						$newAmount = round($earning->amount + $fee_amount, 2);
						$earning->amount = $newAmount;
						$earning->save();

						SiteWalletHistory::addHistory(SiteWalletHistory::TYPE_EARNING, $newAmount, $ft->id);
					}

					// Check affiliates and refund affiliates should be done by automatically
					// self::process_affiliate($bt);
					self::process_affiliate($ct);
				}
			} else {
				Log::error('[TransactionLocal::refund()] Error: An error occured while creating contractor transaction.');
				return $result;
			}
		} catch ( Exception $e ) {
			Log::error('[TransactionLocal::refund()] Error: ' . $e->getMessage());
			return $result;
		}

		$freelancer_name = $c->contractor->fullname();
		$buyer_name = $c->buyer->fullname();
		$amount = formatCurrency($buyer_amount);
		$contract_url = _route('contract.contract_view', ['id' => $cid], true, null, $c->contractor) . '#contract_transactions';

		// Send notification to buyer
		Notification::send(Notification::BUYER_REFUND, SUPERADMIN_ID, $c->buyer_id, [
			'freelancer_name' => $freelancer_name,
			'amount' => $amount
		]);

		// Send notification to freelancer
		Notification::send(Notification::REFUND, SUPERADMIN_ID, $c->contractor_id, [
			'buyer_name' => $buyer_name,
			'amount' => $amount
		]);

		// Send email to freelancer
		EmailTemplate::send($c->contractor, 'REFUND', 1, [
			'USER' => $freelancer_name,
			'BUYER' => $buyer_name,
			'CONTRACT_TITLE' => $c->title,
			'AMOUNT' => $amount,
			'COMMENT' => $note,
			'CONTRACT_TRANSACTION_URL' => $contract_url,
		]);

		// Send email to buyer
		EmailTemplate::send($c->buyer, 'REFUND', 2, [
			'USER' => $buyer_name,
			'FREELANCER' => $freelancer_name,
			'CONTRACT_TITLE' => $c->title,
			'AMOUNT' => $amount,
			'COMMENT' => $note,
			'CONTRACT_TRANSACTION_URL' => $contract_url,
		]);

		$result['success'] = true;
		$result['amount'] = $buyer_amount;

		return $result;
	}

	/**
	* Process for affiliate transactions
	*/
	public static function process_affiliate($t) {
		if ( $t->checked_affiliate || $t->amount == 0 || $t->user_id == 0 ) {
			return false;
		}

		if ( $affiliate = $t->user->userAffiliate ) {
			// Affiliated itself
			if ( $affiliate->user_id == $t->user_id ) {
				return false;
			}

			// For buyer, we just disable affiliate
			if ( $t->user->isBuyer() ) {
				return false;
			}

			// Same contract
			if ( $t->contract ) {
				if ( $t->user->isBuyer() && $affiliate->user_id == $t->contract->contractor_id ) {
					return false;
				} else if ( $t->user->isFreelancer() && $affiliate->user_id == $t->contract->buyer_id ) {
					return false;
				}
			}

			// Check buyer lifetime days
			if ( $t->user->isBuyer() ) {
				$created = new \DateTime($t->user->created_at);
				$now = new \DateTime('now');
				$diff = $created->diff($now);

				if ( $diff->days > 90 ) {
					return false;
				}
			}

			// Affiliate fee rate
			if ( $t->user->isBuyer() ) {
				$fee_rate = Settings::getAffiliateBuyerFee();
				$child_fee_rate = Settings::getAffiliateChildBuyerFee();
			} else {
				$fee_rate = Settings::getAffiliateFreelancerFeeRate();
				$child_fee_rate = Settings::getAffiliateChildFreelancerFeeRate();
			}

			$now = date('Y-m-d H:i:s');

			// Check if the affiliated user has been suspended or not
			if ( !$t->contract->contractor->isSuspended() && !$t->contract->contractor->isFinancialSuspended() ) {

				// $status = $t->isRefund() ? self::STATUS_DONE : self::STATUS_PENDING;
				$status = self::STATUS_DONE;

				if ( $fee_rate > 0 ) {
					if ( $t->user->isBuyer() ) {
						$fee = -($t->amount) * $fee_rate;
					} else {
						$fee = -($t->reference->amount) * $fee_rate;
					}

					$fee = round($fee, 2);

					$ft = new TransactionLocal;
					$ft->type = TransactionLocal::TYPE_AFFILIATE;
					$ft->for = TransactionLocal::FOR_IJOBDESK;
					$ft->user_id = SUPERADMIN_ID;
					$ft->contract_id = $t->contract->id;
					$ft->amount = -$fee;
					$ft->status = $status;
					$ft->ref_user_id = $affiliate->user_id;
					$ft->old_id = $t->id;

					if ( $status == self::STATUS_DONE ) {
						$ft->done_at = $now;
					}
							
					if ( $ft->save() ) {

						$ct = $ft->replicate();
						$ct->for = $affiliate->affiliateUser->isFreelancer() ? TransactionLocal::FOR_FREELANCER : TransactionLocal::FOR_BUYER;
						$ct->user_id = $affiliate->user_id;
						$ct->amount = $fee;
						$ct->ref_id = $ft->id;
						$ct->ref_user_id = $affiliate->affiliate_id;
						if ( $ct->save() ) {
							$ft->ref_id = $ct->id;
							$ft->save();

							if ( $status == self::STATUS_DONE ) {
								// Update user wallet and history
								$userWallet = Wallet::account($affiliate->user_id);
							  	$newAmount = round($userWallet->amount + $ct->amount, 2);
								$userWallet->amount = $newAmount;
								$userWallet->save();

								WalletHistory::addHistory($affiliate->user_id, $newAmount, $ct->id);

								// Update iJobDesk earning wallet history
								$earning = SiteWallet::earning();
								$newAmount = round($earning->amount - $ct->amount, 2);
								$earning->amount = $newAmount;
								$earning->save();

								SiteWalletHistory::addHistory(SiteWalletHistory::TYPE_EARNING, $newAmount, $ft->id);
							}

							if ( $ct->amount > 0 ) {
								EmailTemplate::send($ct->user, 'AFFILIATE_CREDIT', 0, [
						            'USER' => $ct->user->fullname(),
									'AMOUNT' => formatCurrency($fee),
									'AFFILIATE_PAY_URL' => route('user.affiliate'),
								]);
							}
						}
					}
				}

				if ( $child_fee_rate ) {
					// Check child affiliate
					$child_affiliates = UserAffiliate::where('affiliate_id', $affiliate->user_id)
					                ->get();
					if ( $child_affiliates ) {
						if ( $t->user->isBuyer() ) {
							$child_fee = -($t->amount) * $child_fee_rate;
						} else {
							$child_fee = -($t->reference->amount) * $child_fee_rate;
						}

						$child_fee = round($child_fee, 2);

            			foreach ( $child_affiliates as $c_affiliate ) {
							$c_ft = new TransactionLocal;
							$c_ft->type = TransactionLocal::TYPE_AFFILIATE_CHILD;
							$c_ft->for = TransactionLocal::FOR_IJOBDESK;
							$c_ft->user_id = SUPERADMIN_ID;
							$c_ft->contract_id = $t->contract->id;
							$c_ft->amount = -$child_fee;
							$c_ft->status = $status;
							$c_ft->ref_user_id = $c_affiliate->user_id;
							$c_ft->old_id = $ft->id;

							if ( $status == self::STATUS_DONE ) {
								$c_ft->done_at = $now;
							}

							if ( $c_ft->save() ) {

								$c_ct = $c_ft->replicate();
								$c_ct->for = $c_affiliate->affiliateUser->isFreelancer() ? TransactionLocal::FOR_FREELANCER : TransactionLocal::FOR_BUYER;
								$c_ct->user_id = $c_affiliate->user_id;
								$c_ct->amount = $child_fee;
								$c_ct->ref_id = $c_ft->id;
								$c_ct->ref_user_id = $c_affiliate->affiliate_id;
								$c_ct->old_id = $ct->id;

								if ( $c_ct->save() ) {
									$c_ft->ref_id = $c_ct->id;
									$c_ft->save();

									if ( $status == self::STATUS_DONE ) {
										// Update user wallet and history
										$userWallet = Wallet::account($c_affiliate->user_id);
									  	$newAmount = round($userWallet->amount + $c_ct->amount, 2);
										$userWallet->amount = $newAmount;
										$userWallet->save();

										WalletHistory::addHistory($c_affiliate->user_id, $newAmount, $c_ct->id);

										// Update iJobDesk earning wallet history
										$earning = SiteWallet::earning();
										$newAmount = round($earning->amount - $c_ct->amount, 2);
										$earning->amount = $newAmount;
										$earning->save();

										SiteWalletHistory::addHistory(SiteWalletHistory::TYPE_EARNING, $newAmount, $c_ft->id);
									}

									EmailTemplate::send($c_ct->user, 'AFFILIATE_CREDIT', 0, [
							            'USER' => $c_ct->user->fullname(),
										'AMOUNT' => formatCurrency($child_fee),
										'AFFILIATE_PAY_URL' => route('user.affiliate'),
									]);
								}
							}
            			}
            		}
            	}
        	}
        }

        $t->checked_affiliate = 1;
		
		if ( $t->save() ) {
			return true;
		}

		return false;
	}

	/**
	* Pay for hourly contract
	*/
	public static function pay_hourly($opts) {
		$result = [
			'success' => false,
			'message' => '',
		];

		$defaults = [
			'cid' => 0,
			'tid' => 0,
	  		'amount' => 0,
	  		'type' => self::TYPE_HOURLY,
	  		'hourly_from' => '',
	  		'hourly_to' => '',
	  		'hourly_mins' => 0,
	  		'note' => '',
	  		'status' => self::STATUS_AVAILABLE,
	  	];

		$opts = array_merge($defaults, $opts);
		extract($opts);

		$type = self::parseType($type);

		if ( !$type ) {
			Log::error('[TransactionLocal::pay_hourly()] Error: Invalid transaction type is given.');
			return $result;      
		}

		if ( !$cid ) {
			Log::error('[TransactionLocal::pay_hourly()] Error: Contract ID is not given.');
			return $result;
		}

		if ( $amount <= 0 ) {
			Log::error('[TransactionLocal::pay_hourly()] Error: Invalid amount to pay.');
			return $result;
		}

		if ( !$hourly_from || !$hourly_to ) {
			Log::error('[TransactionLocal::pay_hourly()] Error: Hourly log range is not given.');
			return $result;
		}

		if ( !$hourly_mins ) {
			Log::error('[TransactionLocal::pay_hourly()] Error: Invalid weekly minutes is given.');
			return $result;
		}

		$c = Contract::find($cid);
		if ( !$c ) {
			Log::error('[TransactionLocal::pay_hourly()] Error: Contract #' . $cid . ' is invalid.');
			return $result;
		}

		if ( $c->buyer->isSuspended() || $c->buyer->isFinancialSuspended() ) {
			Log::error('[TransactionLocal::pay_hourly()] Error: User #' . $c->buyer_id . ' has been suspended or financial suspended.');
			$result['message'] = trans('message.freelancer.payment.failed_user_suspended');
			return $result;
		}

		if ( $c->contractor->isSuspended() || $c->contractor->isFinancialSuspended() ) {
			Log::error('[TransactionLocal::pay_hourly()] Error: User #' . $c->contractor_id . ' has been suspended or financial suspended.');
			$result['message'] = trans('message.buyer.payment.failed_user_suspended');
			return $result;
		}

		try {

			// Create freelancer transaction
			if ( $tid ) {
				// Calculate amount to pay contractor and fee
				$fee_amount = Settings::getFee($amount, $c->isAffiliated());
				$contractor_amount = round($amount - $fee_amount, 2);

				$ct = new TransactionLocal;
				$ct->type = $type;
				$ct->contract_id = $cid;
				$ct->hourly_from = $hourly_from;
				$ct->hourly_to = $hourly_to;
				$ct->hourly_mins = $hourly_mins;
				$ct->for = self::FOR_FREELANCER;
				$ct->user_id = $c->contractor_id;
				$ct->amount = $contractor_amount;
				$ct->status = self::STATUS_AVAILABLE;
				$ct->done_at = NULL;
				$ct->ref_id = $tid;
				$ct->save();

				// Create fee transaction
				$ft = new TransactionLocal;
				$ft->type = $type;
				$ft->contract_id = $cid;
				$ft->hourly_from = $hourly_from;
				$ft->hourly_to = $hourly_to;
				$ft->hourly_mins = $hourly_mins;
				$ft->for = self::FOR_IJOBDESK;
				$ft->user_id = SUPERADMIN_ID;
				$ft->amount = $fee_amount;
				$ft->status = self::STATUS_AVAILABLE;
				$ft->done_at = NULL;
				$ft->ref_user_id = $c->contractor_id;
				$ft->ref_id = $ct->id;
				$ft->save();

				// Send notification to freelancer
				/*
				Notification::send(Notification::PAY_HOURLY, SUPERADMIN_ID, $c->contractor_id, [
					'buyer_name' => $buyer_name,
					'amount' => formatCurrency($amount)
				]);
				*/

				// Process affiliate for invitation buyer
				// $bt = self::find($tid);
				// self::process_affiliate($bt);

			// Create buyer transaction
			} else {
				// Buyer wallet history
				$buyerWallet = Wallet::account($c->buyer_id);

				// If buyer balance is 0
				if ( $buyerWallet->amount < $amount ) {
					Log::error('[TransactionLocal::pay_hourly()] Client #' . $c->buyer_id . ' balance is not enough for $' . $amount . ' for contract #' . $c->id . '.');

					$amount = $buyerWallet->amount;
				}

				if ( $amount > 0 ) {
					$bt = new TransactionLocal;
					$bt->type = $type;
					$bt->contract_id = $cid;
					$bt->hourly_from = $hourly_from;
					$bt->hourly_to = $hourly_to;
					$bt->hourly_mins = $hourly_mins;
					$bt->for = self::FOR_BUYER;
					$bt->user_id = $c->buyer_id;
					$bt->amount = (-$amount);
					$bt->status = self::STATUS_DONE;
					$bt->done_at = date('Y-m-d H:i:s');
					$bt->save();

					// Update buyer wallet history				
					$newAmount = round($buyerWallet->amount - $amount, 2);
					$buyerWallet->amount = $newAmount;
					$buyerWallet->save();

					WalletHistory::addHistory($c->buyer_id, $newAmount, $bt->id);

					// Send notification to buyer
					/*
					Notification::send(Notification::BUYER_PAY_HOURLY, SUPERADMIN_ID, $c->buyer_id, [
						'freelancer_name' => $freelancer_name,
						'amount' => formatCurrency($amount)
					]);
					*/

					$result['id'] = $bt->id;
					$result['amount'] = $amount;
				} else {
					Log::error('[TransactionLocal::pay_hourly()] Client balance is not enough.');

					return $result;
				}
			}

		} catch ( Exception $e ) {
			Log::error('[TransactionLocal::pay_hourly()] Error: ' . $e->getMessage());
			return $result;
		}

		$result['success'] = true;

		return $result;
	}

	public static function pay_hourly_refund($opts) {
		$result = [
			'success' => false,
			'message' => '',
		];

		extract($opts);

		$c = Contract::find($cid);
		if ( !$c ) {
			Log::error('[TransactionLocal::pay_hourly_refund()] Error: Contract #' . $cid . ' is invalid.');
			return $result;
		}

		if ( $c->buyer->isSuspended() || $c->buyer->isFinancialSuspended() ) {
			Log::error('[TransactionLocal::pay_hourly_refund()] Error: User #' . $c->buyer_id . ' has been suspended or financial suspended.');
			$result['message'] = trans('message.freelancer.payment.failed_user_suspended');
			return $result;
		}

		try {
			// Buyer wallet history
			$buyerWallet = Wallet::account($c->buyer_id);

			$bt = new TransactionLocal;
			$bt->type = self::TYPE_HOURLY;
			$bt->contract_id = $cid;
			$bt->hourly_from = $hourly_from;
			$bt->hourly_to = $hourly_to;
			$bt->hourly_mins = $hourly_mins;
			$bt->for = self::FOR_BUYER;
			$bt->user_id = $c->buyer_id;
			$bt->amount = $amount;
			$bt->status = self::STATUS_DONE;
			$bt->done_at = date('Y-m-d H:i:s');
			$bt->save();

			// Update buyer wallet history
			$newAmount = round($buyerWallet->amount + $amount, 2);
			$buyerWallet->amount = $newAmount;
			$buyerWallet->save();

			WalletHistory::addHistory($c->buyer_id, $newAmount, $bt->id);

			$buyer_name = $c->buyer->fullname();
			$freelancer_name = $c->contractor->fullname();
			$date_from = date('M d, Y', strtotime($hourly_from));
			$date_to = date('M d, Y', strtotime($hourly_to));
			$hours = formatMinuteInterval($hourly_mins);

			// Notification to buyer
			Notification::send(Notification::DISPUTE_LASTWEEK_BUYER, SUPERADMIN_ID, $c->buyer_id, [
				'HOURS' => $hours,
				'CONTRACT_NAME' => $c->title,
				'AMOUNT' => formatCurrency($amount),
				'DATE_FROM' => $date_from,
				'DATE_TO' => $date_to,
			]);

			// Notification to freelancer
			Notification::send(Notification::DISPUTE_LASTWEEK_FREELANCER, SUPERADMIN_ID, $c->contractor_id, [
				'BUYER_NAME' => $buyer_name,
				'CONTRACT_NAME' => $c->title,
				'HOURS' => $hours,
				'AMOUNT' => formatCurrency($amount),
				'DATE_FROM' => $date_from,
				'DATE_TO' => $date_to,
			]);

			// Send email to buyer
			EmailTemplate::send($c->buyer, 'DISPUTE_LASTWEEK', 2, [
				'USER' => $buyer_name,
				'CONTRACT_NAME' => $c->title,
				'HOURS' => $hours,
				'AMOUNT' => formatCurrency($amount),
				'DATE_FROM' => $date_from,
				'DATE_TO' => $date_to,
			]);

			// Send email to freelancer
			EmailTemplate::send($c->contractor, 'DISPUTE_LASTWEEK', 1, [
				'USER' => $freelancer_name,
				'CONTRACT_NAME' => $c->title,
				'BUYER_NAME' => $buyer_name,
				'HOURS' => $hours,
				'AMOUNT' => formatCurrency($amount),
				'DATE_FROM' => $date_from,
				'DATE_TO' => $date_to,
			]);

		} catch ( Exception $e ) {
			Log::error('[TransactionLocal::pay_hourly_refund()] Error: ' . $e->getMessage());
			return $result;
		}

		$result['success'] = true;

		return $result;
	}

	/**
	* Pay one transaction like affiliate
	*/
	public static function payOne($t) {
		if ( $t->amount == 0 ) {
			return false;
		}

		$now = date('Y-m-d H:i:s');

		$t->status = self::STATUS_DONE;
		$t->done_at = $now;
		
		if ( $t->save() ) {

	  		// Update user wallet history
	  		$wallet = Wallet::account($t->user_id);
	  		$newAmount = round($wallet->amount + $t->amount, 2);
			$wallet->amount = $newAmount;
			$wallet->save();

			WalletHistory::addHistory($t->user_id, $newAmount, $t->id);

			if ( $t->reference ) {
				$t->reference->status = self::STATUS_DONE;
				$t->reference->done_at = $now;
				$t->reference->save();
			
				// Update iJobDesk earning wallet history
				$earning = SiteWallet::earning();
				$newAmount = round($earning->amount + $t->reference->amount, 2);
				$earning->amount = $newAmount;
				$earning->save();

				SiteWalletHistory::addHistory(SiteWalletHistory::TYPE_EARNING, $newAmount, $t->reference->id);
			}

			$transaction_url = route('report.transactions');

			// Send notification to user
			if ( $t->amount > 0 ) {
				Notification::send(Notification::PAY_AFFILIATE, SUPERADMIN_ID, $t->user_id, [
					'amount' => formatCurrency($t->amount)
				]);

				// Send email to user
				EmailTemplate::send($t->user, 'PAY_AFFILIATE', 0, [
					'USER' => $t->user->fullname(),
					'AMOUNT' => formatCurrency($t->amount),
					'AFFILIATE_PAY_URL' => $transaction_url,
				]);
			} else {
				/*
				Notification::send(Notification::REFUND_IJOBDESK, SUPERADMIN_ID, $t->user_id, [
					'amount' => formatCurrency(abs($t->amount))
				]);

				// Send email to user
				EmailTemplate::send($t->user, 'REFUND_IJOBDESK', 0, [
					'USER' => $t->user->fullname(),
					'AMOUNT' => formatCurrency(abs($t->amount)),
					'AFFILIATE_PAY_URL' => $transaction_url,
				]);
				*/
			}

			return true;
		}

		return false;
	}

	/**
	* Pay featured job fee
	*/
	public static function payFee($uid, $type, $amount, $project_id) {
		if ( !$uid || $amount <= 0 ) {
			return false;
		}

  		// Add transaction
		$t = new TransactionLocal;
		$t->type = $type;
		$t->for = self::FOR_BUYER;
		$t->user_id = $uid;
		$t->amount = -$amount;
		$t->contract_id = $project_id;
		$t->status = self::STATUS_DONE;
		$t->done_at = date('Y-m-d H:i:s');
		
		if ( $t->save() ) {

	  		// Update user wallet history
	  		$wallet = Wallet::account($uid);
	  		$newAmount = round($wallet->amount - $amount, 2);
			$wallet->amount = $newAmount;
			$wallet->save();

			WalletHistory::addHistory($uid, $newAmount, $t->id);

			// Fee transaction for user
			$ft = new TransactionLocal;
			$ft->type = $type;
			$ft->for = self::FOR_IJOBDESK;
			$ft->user_id = SUPERADMIN_ID;
			$ft->amount = $amount;
			$ft->contract_id = $project_id;
			$ft->status = self::STATUS_DONE;
			$ft->done_at = date('Y-m-d H:i:s');
			$ft->ref_id = $t->id;
			$ft->ref_user_id = $uid;
			
			if ( $ft->save() ) {

				// Update iJobDesk earning wallet history
				$earning = SiteWallet::earning();
				$newAmount = round($earning->amount + $amount, 2);
				$earning->amount = $newAmount;
				$earning->save();

				SiteWalletHistory::addHistory(SiteWalletHistory::TYPE_EARNING, $newAmount, $ft->id);

			}

			return true;

		}

		return false;
	}

	/**
	* Escrow fund milestone
	*
	* @author Ro Un Nam
	* @since May 14, 2017
	*/
	public static function fund($contract_id, $milestone_id) {
		$status = self::STATUS_DONE;
		$type = TransactionLocal::TYPE_FIXED;

		if ( !$contract_id ) {
			Log::error('[TransactionLocal::fund()] Error: Contract ID is not given.');
			return false;
		}

		$c = Contract::find($contract_id);
		if ( !$c ) {
			Log::error('[TransactionLocal::fund()] Error: Contract #' . $contract_id . ' is invalid.');
			return false;
		}
		
		$m = ContractMilestone::find($milestone_id);
		if ( !$m ) {
			Log::error('[TransactionLocal::fund()] Error: Milestone #' . $milestone_id . ' is invalid.');
			return false;
		}

		if ( $c->buyer->isSuspended() || $c->buyer->isFinancialSuspended() ) {
			Log::error('[TransactionLocal::fund()] Error: User #' . $c->buyer_id . ' has been suspended or financial suspended.');
			return false;
		}

		if ( $c->contractor->isSuspended() || $c->contractor->isFinancialSuspended() ) {
			Log::error('[TransactionLocal::fund()] Error: User #' . $c->contractor_id . ' has been suspended or financial suspended.');
			return false;
		}

		$amount = $m->getPrice();

		$transaction = $m->transaction;
		if ( $transaction ) {
			return true;
		}
		
		$amount = -$amount;

		$now = date('Y-m-d H:i:s');
		
		// Create buyer transaction
		$bt = new TransactionLocal;
		$bt->type = $type;
		$bt->for = self::FOR_BUYER;
		$bt->user_id = $c->buyer_id;
		$bt->contract_id = $contract_id;
		$bt->milestone_id = $m->id;
		$bt->note = $m->name;
		$bt->amount = $amount;
		$bt->status = $status;
		$bt->done_at = $now;
		
		if ( $bt->save() ) {

			$m->fund_status = ContractMilestone::FUNDED;
			$m->transaction_id = $bt->id;
			$m->funded_at = $now;
			$m->save();

			// Update buyer wallet history
	  		$wallet = Wallet::account($c->buyer_id);
	  		$newAmount = round($wallet->amount + $amount, 2);
			$wallet->amount = $newAmount;
			$wallet->save();

			WalletHistory::addHistory($c->buyer_id, $newAmount, $bt->id);

			$amount = formatCurrency(abs($amount));
			
			// Send notification to buyer
			Notification::send(Notification::BUYER_FUND, SUPERADMIN_ID, $c->buyer_id, [
					'amount' => $amount,
					'milestone_title' => sprintf('%s', $m->name)
				]
			);

			// Send notification to freelancer
			Notification::send(Notification::FUND, SUPERADMIN_ID, $c->contractor_id, [
					'buyer_name' => $c->buyer->fullname(),
					'amount' => $amount,
					'milestone_title' => sprintf('%s', $m->name)
				]
			);

			$contract_url = _route('contract.contract_view', ['id' => $c->id], true, null, $c->buyer);

			// Send email to freelancer
			EmailTemplate::send($c->contractor, 'FUND', 1, [
				'USER' => $c->contractor->fullname(),
				'BUYER' => $c->buyer->fullname(),
				'AMOUNT' => $amount,
				'CONTRACT_TITLE' => $c->title,
				'CONTRACT_URL' => $contract_url,
				'MILESTONE' => $m->name,
			]);

			// Send email to buyer
			EmailTemplate::send($c->buyer, 'FUND', 2, [
				'USER' => $c->buyer->fullname(),
				'AMOUNT' => $amount,
				'CONTRACT_TITLE' => $c->title,
				'CONTRACT_URL' => $contract_url,
				'MILESTONE' => $m->name,
			]);

			return true;
		}

		return false;
	}

	/**
	* Release fund milestone
	*
	* @author Ro Un Nam
	* @since May 18, 2017
	*/
	public static function release($contract_id, $milestone_id, $force = false) {
		$result = [
			'success' 	=> false,
			'error' 	=> 0,
			'amount' 	=> 0
		];

		$status = self::STATUS_AVAILABLE;
		$type = TransactionLocal::TYPE_FIXED;

		if ( !$contract_id ) {
			Log::error('[TransactionLocal::release()] Error: Contract ID is not given.');

			$result['error'] = 1;
			return $result;
		}

		$c = Contract::find($contract_id);
		if ( !$c ) {
			Log::error('[TransactionLocal::release()] Error: Contract #' . $contract_id . ' is invalid.');

			$result['error'] = 2;
			return $result;
		}

		$m = ContractMilestone::find($milestone_id);
		if ( !$m ) {
			Log::error('[TransactionLocal::release()] Error: Milestone #' . $milestone_id . ' is invalid.');

			$result['error'] = 3;
			return $result;
		}

		if ( !$force && ($c->buyer->isSuspended() || $c->buyer->isFinancialSuspended()) ) {
			Log::error('[TransactionLocal::release()] Error: User #' . $c->buyer_id . ' has been suspended or financial suspended.');
			
			$result['error'] = 5;
			return $result;
		}

		if ( !$force && ($c->contractor->isSuspended() || $c->contractor->isFinancialSuspended()) ) {
			Log::error('[TransactionLocal::release()] Error: User #' . $c->contractor_id . ' has been suspended or financial suspended.');
			
			$result['error'] = 6;
			return $result;
		}

		try {
			// Check if not funded before
			if ( !$m->transaction_id ) {
				$amount = $m->getPrice();

				// Check the balance
				$balance = $c->buyer->myBalance();
				if ( $amount > $balance ) {
					Log::error('[TransactionLocal::release()] Error: Not enough balance to release milestone #' . $milestone_id . '.');

					$result['error'] = 8;
					$result['amount'] = abs($amount);
					$result['message'] = trans('message.not_enough_balance', ['milestone' => $m->name]);

					return $result;
				}

				$amount = -$amount;

				$now = date('Y-m-d H:i:s');
				
				// Create buyer transaction
				$bt = new TransactionLocal;
				$bt->type = $type;
				$bt->for = self::FOR_BUYER;
				$bt->user_id = $c->buyer_id;
				$bt->contract_id = $contract_id;
				$bt->milestone_id = $m->id;
				$bt->note = $m->name;
				$bt->amount = $amount;
				$bt->status = self::STATUS_DONE;
				$bt->done_at = $now;
				$bt->save();

				$m->fund_status = ContractMilestone::FUNDED;
				$m->transaction_id = $bt->id;
				$m->funded_at = $now;
				$m->save();

				// Update buyer wallet history
		  		$wallet = Wallet::account($c->buyer_id);
		  		$newAmount = round($wallet->amount + $amount, 2);
				$wallet->amount = $newAmount;
				$wallet->save();

				WalletHistory::addHistory($c->buyer_id, $newAmount, $bt->id);
			}

			// Calculate amount to pay contractor and fee
			$amount = -$m->transaction->amount;

			// Check iJobDesk holding wallet
			$holding = SiteWallet::holding();
			if ( $holding->amount < $amount ) {
				// Send email to super admin
				EmailTemplate::sendToSuperAdmin('SUPER_ADMIN_WALLET_ISSUE', User::ROLE_USER_SUPER_ADMIN, [
					'holding_wallet' => $holding->amount,
					'amount' => $amount,
				]);

				Log::error('[TransactionLocal::release()] Error: Not enough ijobdesk holding wallet to release milestone #' . $milestone_id . '. iJobDesk holding wallet: $' . $holding->amount . ', Milestone amount: $' . $amount);
				
				$result['error'] = 9;

				return $result;
			}

			$fee_amount = Settings::getFee($amount, $c->isAffiliated());
			$contractor_amount = round($amount - $fee_amount, 2);

			$bt = $m->transaction;

			// Create contractor transaction
			$ct = $bt->replicate();

			if ( $ct ) {

				// Create fee transaction
				$ft = $bt->replicate();

				$ct->for = self::FOR_FREELANCER;
				$ct->user_id = $c->contractor_id;
				$ct->amount = $contractor_amount;
				$ct->ref_id = $bt->id; // buyer transaction id
				$ct->status = $status;
				$ct->done_at = NULL;
				$ct->save();

				$ft->for = self::FOR_IJOBDESK;
				$ft->user_id = SUPERADMIN_ID; // means iJobDesk
				$ft->amount = $fee_amount;
				$ft->ref_id = $ct->id;
				$ft->ref_user_id = $c->contractor_id;
				$ft->status = $status;
				$ft->done_at = NULL;
				$ft->save();

				$m->fund_status = ContractMilestone::FUND_PAID;
				$m->transaction_id = $bt->id;
				$m->contractor_transaction_id = $ct->id;
				$m->save();

				// Update contract_meter
				$m->contract->meter->updateTotal($amount);

				// Process affiliate for invitation buyer
				// self::process_affiliate($bt);

				$amount = formatCurrency($amount);

				$milestone_name = $m->short_name();

				// Send notification to buyer
				Notification::send(Notification::BUYER_RELEASE, SUPERADMIN_ID, $c->buyer_id, [
					'amount' => $amount,
					'milestone_title' => sprintf('%s', $milestone_name)
					]
				);

				// Send notification to freelancer
				Notification::send(Notification::RELEASE, SUPERADMIN_ID, $c->contractor_id, [
					'buyer_name' => $c->buyer->fullname(),
					'amount' => $amount,
					'milestone_title' => sprintf('%s', $milestone_name)
					]
				);

				$freelancer_name = $c->contractor->fullname();
				$buyer_name = $c->buyer->fullname();
				$contract_title = $c->title;
				$contract_url = _route('contract.contract_view', ['id' => $c->id], true, null, $c->buyer) . '#contract_transactions';

				// Send email to freelancer
				EmailTemplate::send($c->contractor, 'RELEASE_FUND', User::ROLE_USER_FREELANCER, [
					'USER' => $freelancer_name,
					'BUYER' => $buyer_name,
					'AMOUNT' => $amount,
					'MILESTONE' => $milestone_name,
					'CONTRACT_TITLE' => $contract_title,
					'CONTRACT_TRANSACTION_URL' => $contract_url,
				]);

				// Send email to buyer
				EmailTemplate::send($c->buyer, 'RELEASE_FUND', User::ROLE_USER_BUYER, [
					'USER' => $buyer_name,
					'FREELANCER' => $freelancer_name,
					'AMOUNT' => $amount,
					'MILESTONE' => $milestone_name,
					'CONTRACT_TITLE' => $contract_title,
					'CONTRACT_TRANSACTION_URL' => $contract_url,
				]);

				$result['success'] = true;
				$result['code'] = 0;
				$result['amount'] = $amount;
			}
		} catch ( Exception $e ) {
			Log::error('[TransactionLocal::release()] Error: ' . $e->getMessage());

			return $result;
		}

		return $result;
	}

	/**
	* Refund fund milestone
	* @param @force Although contractor was suspended, refund.(Punishment to Freelancer on Dispute)
	* @author Ro Un Nam
	* @since Dec 16, 2017
	*/
	public static function refund_fund($contract_id, $milestone_id, $force = false, $reason = 0) {
		$result = [
			'success' => false,
			'error' => 0,
			'amount' => 0
		];

		$status = self::STATUS_AVAILABLE;
		$type = TransactionLocal::TYPE_FIXED;

		if ( !$contract_id ) {
			Log::error('[TransactionLocal::refund_fund()] Error: Contract ID is not given.');
			
			$result['error'] = 1;
			return $result;
		}

		$c = Contract::find($contract_id);
		if ( !$c ) {
			Log::error('[TransactionLocal::refund_fund()] Error: Contract #' . $contract_id . ' is invalid.');
			
			$result['error'] = 2;
			return $result;
		}
		
		$m = ContractMilestone::find($milestone_id);
		if ( !$m ) {
			Log::error('[TransactionLocal::refund_fund()] Error: Milestone #' . $milestone_id . ' is invalid.');
			
			$result['error'] = 3;
			return $result;
		}

		$transaction = $m->transaction;
		if ( !$transaction ) {
			Log::error('[TransactionLocal::refund_fund()] Error: No found transaction for #' . $milestone_id . ' milestone.');
			
			$result['error'] = 4;
			return $result;
		}
		
		if ( !$force && ($c->buyer->isSuspended() || $c->buyer->isFinancialSuspended()) ) {
			Log::error('[TransactionLocal::refund_fund()] Error: User #' . $c->buyer_id . ' has been suspended or financial suspended.');
			
			$result['error'] = 5;
			return $result;
		}

		if ( !$force && ($c->contractor->isSuspended() || $c->contractor->isFinancialSuspended()) ) {
			Log::error('[TransactionLocal::refund_fund()] Error: User #' . $c->contractor_id . ' has been suspended or financial suspended.');
			
			$result['error'] = 6;
			return $result;
		}
		
		try {
			$transaction->status = TransactionLocal::STATUS_DONE;
			$transaction->done_at = $transaction->created_at;
			$transaction->save();

			$amount = abs($transaction->amount);

			$refund_transaction = $transaction->replicate();
			$refund_transaction->done_at = date('Y-m-d H:i:s');
			$refund_transaction->old_id = $transaction->id;
			$refund_transaction->amount = $amount;
			$refund_transaction->save();
			
			// Update buyer wallet history
	  		$wallet = Wallet::account($refund_transaction->user_id);
	  		$newAmount = round($wallet->amount + $amount, 2);
			$wallet->amount = $newAmount;
			$wallet->save();

			WalletHistory::addHistory($refund_transaction->user_id, $newAmount, $refund_transaction->id);

			$m->fund_status = ContractMilestone::FUND_REFUNDED;
			$m->save();

			$amount = formatCurrency($amount);
			
			// Send notification to buyer
			Notification::send(Notification::REFUNDED_FUND, SUPERADMIN_ID, $refund_transaction->contract->buyer_id, [
				'amount' => $amount,
				'milestone_title' => sprintf('%s', $m->name)
				]
			);

			// Send notification to freelancer
			Notification::send(Notification::REFUNDED_FUND, SUPERADMIN_ID, $refund_transaction->contract->contractor_id, [
				'amount' => $amount,
				'milestone_title' => sprintf('%s', $m->name)
				]
			);

			$freelancer_name = $refund_transaction->contract->contractor->fullname();
			$buyer_name = $refund_transaction->contract->buyer->fullname();
			$contract_title = $refund_transaction->contract->title;
			$contract_url = _route('contract.contract_view', ['id' => $refund_transaction->contract->id], true, null, $refund_transaction->contract->buyer) . '#contract_transactions';

			$by_freelancer = in_array($reason, [
				TransactionLocal::REFUND_REASON_BY_FREELANCER,
				TransactionLocal::REFUND_REASON_END_CONTRACT,
				TransactionLocal::REFUND_REASON_OFFER_DECLINED
			]);

			if ( $by_freelancer ) {
				// Send email to freelancer
				EmailTemplate::send($refund_transaction->contract->contractor, 'REFUNDED_FUND', 1, [
					'USER' => $freelancer_name,
					'AMOUNT' => $amount,
					'MILESTONE' => $m->name,
					'CONTRACT_TITLE' => $contract_title,
					'COMMENT' => self::$str_refund_reason[$reason],
					'CONTRACT_TRANSACTION_URL' => $contract_url,
				]);
			}

			// Send email to buyer
			EmailTemplate::send($refund_transaction->contract->buyer, 'REFUNDED_FUND', 2, [
				'USER' => $buyer_name,
				'AMOUNT' => $amount,
				'MILESTONE' => $m->name,
				'FREELANCER' => $by_freelancer ? $freelancer_name : 'Admin',
				'CONTRACT_TITLE' => $contract_title,
				'COMMENT' => self::$str_refund_reason[$reason],
				'CONTRACT_TRANSACTION_URL' => $contract_url,
			]);
		} catch ( Exception $e ) {
			Log::error('[TransactionLocal::refund_fund()] Error: ' . $e->getMessage());

			return $result;
		}

		$result['success'] = true;
		$result['code'] = 0;
		$result['amount'] = $amount;

		return $result;
	}

	/**
	* Charges (typically buyer's) credit to his/her account
	*
	* @author paulz
	* @created Mar 30, 2016
	* @author Ro Un Nam
	* @modifed Mar 4, 2017  
	*/
	public static function charge($uid, $amount, $user_payment_gateway_id, $order_id = '', $meta = [], $status = self::STATUS_AVAILABLE) {
		if ( !$uid || $amount <= 0 ) {
			return false;
		}

  		// Add transaction
		$t = new TransactionLocal;
		$t->type = self::TYPE_CHARGE;
		$t->for = self::FOR_BUYER;
		$t->user_id = $uid;
		$t->amount = $amount;
		$t->status = $status;

		if ( $status == self::STATUS_DONE ) {
			$t->status = $status;
			$t->done_at = date('Y-m-d H:i:s');
		}

		$t->user_payment_gateway_id = $user_payment_gateway_id;

		$user_payment_gateway = UserPaymentGateway::get($user_payment_gateway_id);
		if ( $user_payment_gateway ) {
			$user_payment_gateway_data = $user_payment_gateway->dataArray();
			$user_payment_gateway_data['gateway'] = $user_payment_gateway->gateway;
			$t->user_payment_gateway_data = json_encode($user_payment_gateway_data);
		}
		
		if ( $order_id ) {
			$t->order_id = $order_id;
		}

		if ( $meta ) {
			$t->meta = json_encode($meta);
		}
		
		if ( $t->save() ) {

            EmailTemplate::sendToSuperAdmin('ADMIN_DEPOSIT', User::ROLE_USER_SUPER_ADMIN, [
            	'CUSTOMER' => $t->user->fullname(),
            	'ID' => $t->id,
            	'AMOUNT' => $t->amount,
            	'PAYMENT_GATEWAY' => $t->gateway_string(),
            ]);

			if ( $status == self::STATUS_DONE ) {
				// Update amount in user_deposits table
				if ( $t->userPaymentGateway && $t->userPaymentGateway->real_id ) {
					UserDeposit::updateAmount($t->user_id, $t->userPaymentGateway->gateway, $t->userPaymentGateway->real_id, $t->amount);
				}

				// Update user wallet history
				$wallet = Wallet::account($t->user_id);
                $wallet->amount = round($wallet->amount + $t->amount, 2);
                $wallet->save();

				WalletHistory::addHistory($t->user_id, $wallet->amount, $t->id);

				// Update notify_insufficient_fund table
				NotifyInsufficientFund::updateClient($t->user_id);

				// Update iJobDesk holding wallet history
                $holding = SiteWallet::holding();
                $newAmount = round($holding->amount + $t->amount, 2);
                $holding->amount = $newAmount;
                $holding->save();

                SiteWalletHistory::addHistory(SiteWalletHistory::TYPE_HOLDING, $newAmount, $t->id);

				// Check affiliate
				// TransactionLocal::addDepositAffiliates($t->user_id);

		  		// Send notification and email
		  		Notification::send(Notification::BUYER_DEPOSIT, SUPERADMIN_ID, $t->user_id, [
				 	'amount' => formatCurrency($t->amount)
				]);

				EmailTemplate::send($t->user, 'DEPOSIT', User::ROLE_USER_BUYER, [
		            'USER' => $t->user->fullname(),
					'AMOUNT' => formatCurrency($t->amount),
				]);
			}

			return true;
		} else {
			return false;
		}
	}

	/**
	* Add affiliates from first deposit
	* @author Ro Un Nam
	* @since Dec 18, 2017
	*/
	public static function addDepositAffiliates($user_id = 0) {
        // Process affiliate transactions
		$total_deposits = TransactionLocal::where('type', TransactionLocal::TYPE_CHARGE)
						                    ->where('for', TransactionLocal::FOR_BUYER)
						                    ->where('user_id', $user_id)
						                    ->where('status', TransactionLocal::STATUS_DONE)
						                    ->count();

		// If it is first deposit
		if ( $total_deposits == 1 ) {

			$fee = Settings::getAffiliateBuyerFee();
			$child_fee = Settings::getAffiliateChildBuyerFee();

            $affiliates = UserAffiliate::where('affiliate_id', $user_id)
                                        ->get();
            
            if ( $affiliates ) {
                foreach ( $affiliates as $affiliate ) {
					$ft = new TransactionLocal;
					$ft->type = TransactionLocal::TYPE_AFFILIATE;
					$ft->for = TransactionLocal::FOR_IJOBDESK;
					$ft->user_id = SUPERADMIN_ID;
					$ft->amount = -$fee;
					$ft->status = TransactionLocal::STATUS_PENDING;
					$ft->ref_user_id = $affiliate->user_id;
					$ft->save();

					$ct = $ft->replicate();
					$ct->for = $affiliate->affiliateUser->isFreelancer() ? TransactionLocal::FOR_FREELANCER : TransactionLocal::FOR_BUYER;
					$ct->user_id = $affiliate->user_id;
					$ct->amount = $fee;
					$ct->ref_id = $ft->id;
					$ct->ref_user_id = $affiliate->affiliate_id;
					if ( $ct->save() ) {
						$ft->ref_id = $ct->id;
						$ft->save();
					}

					// Check child affiliate
					$child_affiliates = UserAffiliate::where('affiliate_id', $affiliate->user_id)
					                ->get();
					if ( $child_affiliates ) {

            			foreach ( $child_affiliates as $c_affiliate ) {
							$c_ft = new TransactionLocal;
							$c_ft->type = TransactionLocal::TYPE_AFFILIATE_CHILD;
							$c_ft->for = TransactionLocal::FOR_IJOBDESK;
							$c_ft->user_id = SUPERADMIN_ID;
							$c_ft->amount = -$child_fee;
							$c_ft->status = TransactionLocal::STATUS_PENDING;
							$c_ft->ref_user_id = $c_affiliate->user_id;
							$c_ft->old_id = $ft->id;
							$c_ft->save();

							$c_ct = $c_ft->replicate();
							$c_ct->for = $c_affiliate->affiliateUser->isFreelancer() ? TransactionLocal::FOR_FREELANCER : TransactionLocal::FOR_BUYER;
							$c_ct->user_id = $c_affiliate->user_id;
							$c_ct->amount = $child_fee;
							$c_ct->ref_id = $c_ft->id;
							$c_ct->ref_user_id = $c_affiliate->affiliate_id;
							$c_ct->old_id = $ct->id;
							if ( $c_ct->save() ) {
								$c_ft->ref_id = $c_ct->id;
								$c_ft->save();
							}
            			}
            		}
                }
            }
        }
	}

	/**
	* Withdraw from user wallet
	*
	* @author Ro Un Nam
	* @modifed Mar 4, 2017
	*/
	public static function withdraw($uid, $amount, $user_payment_gateway_id, $status = self::STATUS_AVAILABLE, $order_id = '')
	{
		$user_payment_gateway = UserPaymentGateway::get($user_payment_gateway_id);

		$withdraw_fee = $user_payment_gateway->withdrawFeeAmount($amount);
		
		$withdraw_min_amount = doubleval(Settings::get('WITHDRAW_MIN_AMOUNT'));
		$withdraw_max_amount = doubleval(Settings::get('WITHDRAW_MAX_AMOUNT'));

		// two transactions - fee, withdraw
		if ( !$uid || $amount < $withdraw_min_amount || $amount > $withdraw_max_amount ) {
			return false;
		}

		$user = User::find($uid);

		if ( $user->isSuspended() || $user->isFinancialSuspended() ) {
			Log::error('[TransactionLocal::withdraw()] Failed: User #' . $uid . ' has been suspended or financial suspended.');
			return false;
		}

		// Check whether the wallet of this user has enough amount to withdraw
		if ( $user->myBalance() < $amount ) {
			Log::error('[TransactionLocal::withdraw()] Failed: User #' . $uid . ' has not enough balance to withdraw $' . $amount);
			return false;
		}

		$amount = round($amount - $withdraw_fee, 2);

  		$now = date('Y-m-d H:i:s');

  		try {

	  		// Withdraw transaction
			$t = new TransactionLocal;
			$t->type = self::TYPE_WITHDRAWAL;
			$t->for = $user->isFreelancer() ? self::FOR_FREELANCER : self::FOR_BUYER;
			$t->user_id = $uid;
			$t->amount = -$amount;
			$t->status = $status;
			$t->user_payment_gateway_id = $user_payment_gateway_id;

			// For wechat(CNY) or payoneer(EUR)
			if ( $user_payment_gateway->isWeixin() ) {
				$cny_exchange_rate = Settings::get('CNY_EXCHANGE_RATE_SELL');
				$t->ref_amount = number_format(-$amount * $cny_exchange_rate, 2, '.', '');
			} else if ( $user_payment_gateway->isPayoneer() ) {
				$eur_exchange_rate = Settings::get('EUR_EXCHANGE_RATE_SELL');
				$t->ref_amount = number_format(-$amount * $eur_exchange_rate, 2, '.', '');
			}

			if ( $user_payment_gateway ) {
				$user_payment_gateway_data = $user_payment_gateway->dataArray();
				$user_payment_gateway_data['gateway'] = $user_payment_gateway->gateway;
				$t->user_payment_gateway_data = json_encode($user_payment_gateway_data);
			}

			if ( $order_id ) {
				$t->order_id = $order_id;
			}

			if ( $status == self::STATUS_DONE ) {
				$t->done_at = $now;
			}
			
			if ( $t->save() ) {
		  		// Update user wallet history
		  		$wallet = Wallet::account($uid);
		  		$newAmount = round($wallet->amount - $amount - $withdraw_fee, 2);
				$wallet->amount = $newAmount;
				$wallet->save();

				WalletHistory::addHistory($uid, $newAmount, $t->id);

		  		if ( $withdraw_fee > 0 ) {
			  		// Fee transaction for user
					$tf = new TransactionLocal;
					$tf->type = self::TYPE_WITHDRAWAL;
					$tf->for = self::FOR_IJOBDESK;
					$tf->user_id = $uid;
					$tf->amount = -$withdraw_fee;
					$tf->status = $status;
					
					if ( $status == self::STATUS_DONE ) {
						$tf->done_at = $now;
					}

					$tf->ref_id = $t->id;
					$tf->save();

			  		// Fee transaction for iJobDesk
					$wf = new TransactionLocal;
					$wf->type = self::TYPE_WITHDRAWAL;
					$wf->for = self::FOR_IJOBDESK;
					$wf->user_id = SUPERADMIN_ID;
					$wf->amount = $withdraw_fee;
					$wf->status = $status;

					if ( $status == self::STATUS_DONE ) {
						$wf->done_at = $now;
					}
					
					$wf->ref_id = $t->id;
					$wf->ref_user_id = $uid;
					$wf->save();
				}

				// Direct withdraw using like PayPal
				if ( $status == self::STATUS_DONE ) {
					// Update iJobDesk holding wallet history
					$holding = SiteWallet::holding();
					$newAmount = round($holding->amount - $amount, 2);
					$holding->amount = $newAmount;
					$holding->save();

					SiteWalletHistory::addHistory(SiteWalletHistory::TYPE_HOLDING, $newAmount, $t->id);

					if ( $withdraw_fee > 0 ) {
						// Update iJobDesk earning wallet history
						$earning = SiteWallet::earning();
						$newAmount = $earning->amount + $withdraw_fee;
						$earning->amount = $newAmount;
						$earning->save();

						SiteWalletHistory::addHistory(SiteWalletHistory::TYPE_EARNING, $newAmount, $wf->id);
					}
				}

                // Update user_deposits
                if ( $t->user->isBuyer() ) {
					if ( $t->userPaymentGateway->real_id ) {
						UserDeposit::updateAmount($t->user_id, $t->userPaymentGateway->gateway, $t->userPaymentGateway->real_id, -($amount + $withdraw_fee));
					}
                }

				// Send email to super admin or financial manager
            	$admin_users = User::getAdminUsers([
	                User::ROLE_USER_SUPER_ADMIN,
	                User::ROLE_USER_FINANCIAL_MANAGER
	            ]);

	            if ( $admin_users ) {
	            	foreach ( $admin_users as $admin_user ) {
						EmailTemplate::send($admin_user, 'SUPER_ADMIN_WITHDRAWAL_REQUEST', 0, [
							'USER' => $admin_user->fullname(),
							'CUSTOMER' => $t->user->fullname(),
							'AMOUNT' => formatCurrency($amount),
							'PAYMENT_METHOD' => $t->gateway_string(),
							'DATE' => date('Y/m/d H:i:s'),
						]);
					}
				}

				return $t->id;
			}
		} catch ( Exception $e ) {
			Log::error('[TransactionLocal::withdraw()] Error: ' . $e->getMessage());
		}

		return false;
	}	

	/**
	* Get the last transaction for withdraw
	* @author Ro Un Nam
	*/
	public static function getLastWithdraw($user_id)
	{
		$row = self::where('user_id', $user_id)
					->where('type', self::TYPE_WITHDRAWAL)
					->whereIn('status', [
						self::STATUS_PENDING,
						self::STATUS_AVAILABLE,
						self::STATUS_DONE
					])
					->where('for', '<>', self::FOR_IJOBDESK)
					->orderBy('created_at', 'desc')
					->first();

		if ( !$row ) {
			return false;
		}

		if ( $row ) {
			$fee = self::where('user_id', $user_id)
						->where('type', self::TYPE_WITHDRAWAL)
						->where('ref_id', $row->id)
						->where('for', self::FOR_IJOBDESK)
						->first();

			if ( $fee ) {
				$row->reference = $fee;
			}
		}

		return $row;
	}

	/**
	* Get the last transaction for deposit
	* @author Ro Un Nam
	*/
	public static function getLastCharge($user_id)
	{
		$row = self::where('user_id', $user_id)
					->where('type', self::TYPE_CHARGE)
					->where('for', self::FOR_BUYER)
					->orderBy('created_at', 'desc')
					->first();

		if ( !$row ) {
			return false;
		}

		return $row;
	}

	public static function lastWithdrawalAmount($user_id) {
		$row = self::where('user_id', $user_id)
					->where('type', self::TYPE_WITHDRAWAL)
					->where('status', self::STATUS_DONE)
					->where('for', '<>', self::FOR_IJOBDESK)
					->orderBy('created_at', 'desc')
					->select(['user_id', 'amount'])
					->first();

		if ( !$row ) {
			return 0;
		}

		return -$row->amount;
	}

	/**
	* Get all transactions
	* @param $params [user, server_timezone_offset, user_timezone_offset, from, to, contract_id, type]
	*/
	public static function getTransactions($params = []) {
		$transactions = self::where(function($query) use ($params) {
			$query->where(function($query2) use ($params) {
				$query2->where(function($query3) use ($params) {
					$query3->where('status', self::STATUS_DONE)
							->whereRaw("CONVERT_TZ(done_at, '" . $params['server_timezone_offset'] . "', '" . $params['user_timezone_offset'] . "') >= '" . $params['from'] . "'")
							->whereRaw("CONVERT_TZ(done_at, '" . $params['server_timezone_offset'] . "', '" . $params['user_timezone_offset'] . "') <= '" . $params['to'] . "'");
				})
				->orWhere(function($query3) use ($params) {
					$query3->whereIn('status', [
								self::STATUS_AVAILABLE, 
								self::STATUS_PROCEEDING,
								self::STATUS_SUSPENDED,
								self::STATUS_REVIEW
							])
							->whereRaw("CONVERT_TZ(created_at, '" . $params['server_timezone_offset'] . "', '" . $params['user_timezone_offset'] . "') >= '" . $params['from'] . "'")
							->whereRaw("CONVERT_TZ(created_at, '" . $params['server_timezone_offset'] . "', '" . $params['user_timezone_offset'] . "') <= '" . $params['to'] . "'");
				});
			})
			->where('type', '<>', self::TYPE_WITHDRAWAL);
			
			if ( isset($params['user']) ) {
				$query->where('user_id', $params['user']->id)
					  ->where('for', ($params['user']->isBuyer() ? self::FOR_BUYER : self::FOR_FREELANCER));
			} else {
				$query->whereIn('for', [
					self::FOR_BUYER, 
					self::FOR_FREELANCER
				]);
			}

			if ( isset($params['contract_id']) ) {
				$query->where('contract_id', $params['contract_id']);
			}

			if ( isset($params['type']) ) {
				// Affiliate
				if ( $params['type'] == self::TYPE_AFFILIATE ) {
					$query->whereIn('type', [
						self::TYPE_AFFILIATE,
						self::TYPE_AFFILIATE_CHILD
					]);
				} else {
					$query->where('type', $params['type']);
				}
			}
		})
		->orWhere(function($query) use ($params) {
			$query->where(function($query2) use ($params) {
				$query2->where(function($query3) use ($params) {
					$query3->whereIn('status', [
							self::STATUS_DONE,
							self::STATUS_CANCELLED
						])
						->whereRaw("CONVERT_TZ(done_at, '" . $params['server_timezone_offset'] . "', '" . $params['user_timezone_offset'] . "') >= '" . $params['from'] . "'")
						->whereRaw("CONVERT_TZ(done_at, '" . $params['server_timezone_offset'] . "', '" . $params['user_timezone_offset'] . "') <= '" . $params['to'] . "'");
				})
				->orWhere(function($query3) use ($params) {
					$query3->whereIn('status', [
								self::STATUS_AVAILABLE,
								self::STATUS_PROCEEDING,
								self::STATUS_SUSPENDED,
								self::STATUS_REVIEW
							])
							->whereRaw("CONVERT_TZ(created_at, '" . $params['server_timezone_offset'] . "', '" . $params['user_timezone_offset'] . "') >= '" . $params['from'] . "'")
							->whereRaw("CONVERT_TZ(created_at, '" . $params['server_timezone_offset'] . "', '" . $params['user_timezone_offset'] . "') <= '" . $params['to'] . "'");
				});
			})
			->where('type', self::TYPE_WITHDRAWAL)
			->where('user_id', '<>', SUPERADMIN_ID);

			if ( isset($params['user']) ) {
				$query->where('user_id', $params['user']->id);
			}

			if ( isset($params['contract_id']) ) {
				$query->where('contract_id', $params['contract_id']);
			}

			if ( isset($params['type']) ) {
				// Affiliate
				if ( $params['type'] == self::TYPE_AFFILIATE ) {
					$query->whereIn('type', [
						self::TYPE_AFFILIATE,
						self::TYPE_AFFILIATE_CHILD
					]);
				} else {
					$query->where('type', $params['type']);
				}
			}
		});

		if ( $params['user'] && $params['user']->isFreelancer() ) {
			$transactions->orWhere(function($query) use ($params) {
				$query->where(function($query2) use ($params) {
					$query2->where(function($query3) use ($params) {
						$query3->where('status', self::STATUS_DONE)
								->whereRaw("CONVERT_TZ(done_at, '" . $params['server_timezone_offset'] . "', '" . $params['user_timezone_offset'] . "') >= '" . $params['from'] . "'")
								->whereRaw("CONVERT_TZ(done_at, '" . $params['server_timezone_offset'] . "', '" . $params['user_timezone_offset'] . "') <= '" . $params['to'] . "'");
					})
					->orWhere(function($query3) use ($params) {
						$query3->where('status', self::STATUS_AVAILABLE)
								->whereRaw("CONVERT_TZ(created_at, '" . $params['server_timezone_offset'] . "', '" . $params['user_timezone_offset'] . "') >= '" . $params['from'] . "'")
								->whereRaw("CONVERT_TZ(created_at, '" . $params['server_timezone_offset'] . "', '" . $params['user_timezone_offset'] . "') <= '" . $params['to'] . "'");
					});
				})
				->where('for', self::FOR_IJOBDESK)
				->where('ref_user_id', $params['user']->id)
				->whereNotIn('type', [
					self::TYPE_WITHDRAWAL,
					self::TYPE_AFFILIATE,
					self::TYPE_AFFILIATE_CHILD,
				]);

				if ( isset($params['contract_id']) ) {
					$query->where('contract_id', $params['contract_id']);
				}

				if ( isset($params['type']) ) {
					$query->where('type', $params['type']);
				}
			});
		}

		return $transactions->orderBy('id', 'desc')->get();
	}

	public static function getThisWeekTransactions() {
		$transactions_amount = 0;
		$range = weekRange();
		$start_date = $range['0'];
		$end_date = $range['1'];

		$transactions = self::whereBetween('created_at', [$start_date, $end_date])
        					->whereIn('type', [
        						self::TYPE_FIXED,
        						self::TYPE_HOURLY,
        						self::TYPE_BONUS
        					])
        					->where('for', self::FOR_BUYER)
        					->get();

        foreach ($transactions as $transaction) {
        	$transactions_amount += abs($transaction->amount);
	    }

		return $transactions_amount;
	}

	public static function getWithdrawRequests() {
		return TransactionLocal::where('type', self::TYPE_WITHDRAWAL)
								->where('user_id', '<>', SUPERADMIN_ID)
								->where('for', '<>', self::FOR_IJOBDESK)
                                ->where('status', self::STATUS_AVAILABLE)
                                ->get();
	}

	public static function getTotalWithdrawRequests() {
		return TransactionLocal::where('type', self::TYPE_WITHDRAWAL)
								->where('user_id', '<>', SUPERADMIN_ID)
								->where('for', '<>', self::FOR_IJOBDESK)
                                ->where('status', self::STATUS_AVAILABLE)
                                ->count();
	}

	/**
	* Get all overdue withdraws
	* @author Ro Un Nam
	* @since Jan 16, 2018
	*/
	public static function getOverdueWithdraws() {
		$requests = self::where('type', self::TYPE_WITHDRAWAL)
						->where('for', '<>', self::FOR_IJOBDESK)
						->where('user_id', '<>', SUPERADMIN_ID)
						->whereNull('done_at')
						->whereRaw('DATEDIFF(CURDATE(), created_at) > ' . self::DAYS_OVERDUE)
						->orderBy('created_at', 'asc')
						->get();

		return $requests;
	}

	/**
	* Get all overdue affiliate transactions
	* @author Ro Un Nam
	* @since Feb 01, 2018
	*/
	public static function getOverdueAffiliateTransactions() {
		$transactions = self::whereIn('type', [
							self::TYPE_AFFILIATE,
							self::TYPE_AFFILIATE_CHILD,
						])
						->where('for', self::FOR_IJOBDESK)
						->where('user_id', SUPERADMIN_ID)
						->where('status', self::STATUS_PENDING)
						->whereNull('done_at')
						->whereRaw('DATEDIFF(CURDATE(), created_at) >= 30')
						->orderBy('created_at', 'asc')
						->get();

		return $transactions;
	}	

	/**
	* Get other transactions except for hourly transactions
	* @param $params [user, server_timezone_offset, user_timezone_offset, from, to, created_from, created_to]
	*/
	public static function getUserOtherTransactions($params = []) {
		$transactions = self::whereIn('type', [
								self::TYPE_FIXED, 
								self::TYPE_BONUS, 
								self::TYPE_REFUND,
								self::TYPE_AFFILIATE,
								self::TYPE_AFFILIATE_CHILD
							]);

		if ( isset($params['buyer_id']) ) {
			$transactions = $transactions->where('for', self::FOR_BUYER)
										->where('user_id', $params['buyer_id']);
		} else if ( isset($params['contractor_id']) ) {
			$transactions = $transactions->where('for', self::FOR_FREELANCER)
										->where('user_id', $params['contractor_id']);;
		}

		if ( isset($params['from']) && isset($params['to']) ) {
			$transactions = $transactions->whereRaw("CONVERT_TZ(done_at, '" . $params['server_timezone_offset'] . "', '" . $params['user_timezone_offset'] . "') >= '" . $params['from'] . "'")
										->whereRaw("CONVERT_TZ(done_at, '" . $params['server_timezone_offset'] . "', '" . $params['user_timezone_offset'] . "') <= '" . $params['to'] . "'");

		}

		if ( isset($params['created_from']) && isset($params['created_to']) ) {
			$transactions = $transactions->whereRaw("CONVERT_TZ(created_at, '" . $params['server_timezone_offset'] . "', '" . $params['user_timezone_offset'] . "') >= '" . $params['created_from'] . "'")
										->whereRaw("CONVERT_TZ(created_at, '" . $params['server_timezone_offset'] . "', '" . $params['user_timezone_offset'] . "') <= '" . $params['created_to'] . "'");

		}

		$transactions = $transactions->orderBy('created_at', 'asc');

		return $transactions->get();
	}

	/**
	* Get total deposits amount
	*/
	public static function totalAmountDeposits() {
		return self::where('type', self::TYPE_CHARGE)
					->where('for', self::FOR_BUYER)
					->where('status', self::STATUS_DONE)
					->sum('amount');
	}

	/**
	* Get total user withdraws and site withdraws
	*/
	public static function totalAmountWithdraws() {
		$total = self::where(function($query1) {
					$query1->where('type', self::TYPE_WITHDRAWAL)
							->where('for', '<>', self::FOR_IJOBDESK)
							->where('status', self::STATUS_DONE);
				})->orWhere(function($query2) {
					$query2->where('type', self::TYPE_SITE_WITHDRAWAL)
							->where('for', self::FOR_IJOBDESK)
							->where('status', self::STATUS_DONE);
				})->sum('amount');

		return abs($total);
	}

	/**
	* Get total escrow amount
	*/
	public static function totalAmountEscrows() {
		$total = self::leftJoin('contract_milestones', 'transactions.milestone_id', '=', 'contract_milestones.id')
					->where('transactions.type', self::TYPE_FIXED)
					->where('transactions.status', self::STATUS_DONE)
					->where('transactions.milestone_id', '<>', 0)
					->where('contract_milestones.fund_status', ContractMilestone::FUNDED)
					->sum('amount');

		return abs($total);
	}

	/**
	* Get total hourly & fixed amount under pending
	*/
	public static function totalAmountPending() {
		$fixed_pending = self::whereIn('type', [
						self::TYPE_FIXED,
						self::TYPE_BONUS,
						self::TYPE_HOURLY,
					])
					->where('status', self::STATUS_AVAILABLE)
					->sum('amount');

		$hourly_pending = HourlyReview::leftJoin('transactions', 'transactions.id', '=', 'hourly_reviews.transaction_id')
										->where('hourly_reviews.status', HourlyReview::STATUS_PENDING)
										->where('transactions.status', self::STATUS_DONE)
										->whereNotNull('transactions.id')
										->sum('hourly_reviews.amount');

		return $fixed_pending + $hourly_pending;
	}

	/**
	* Get total withdrawal amount under request
	*/
	public static function totalAmountWithdrawRequests() {
		$total = self::where('type', self::TYPE_WITHDRAWAL)
					->where('status', self::STATUS_AVAILABLE)
					->sum('amount');

		return abs($total);
	}

	/**
	* Get total withdrawal amount under pending
	*/
	public static function totalAmountPendingWithdraws() {
		$total = self::whereIn('type', [
						self::TYPE_WITHDRAWAL,
						self::TYPE_SITE_WITHDRAWAL,
					])
					->where('user_id', '<>', SUPERADMIN_ID)
					->whereIn('status', [
						self::STATUS_AVAILABLE,
						self::STATUS_REVIEW,
						self::STATUS_PROCEEDING,
						self::STATUS_SUSPENDED
					])->sum('amount');

		return abs($total);
	}

	/**
	* Get iJobDesk holding amount
	*/
	public static function totalAmountHolding() {
		return self::totalAmountDeposits() - self::totalAmountWithdraws();
	}

	/**
	* Get user pending balance(total pending under withdrawal or paid)
	*/
	public static function getUserPendingBalance($user_id) {
		$total = self::where('user_id', $user_id)
					->whereIn('status', [
						self::STATUS_AVAILABLE,
						self::STATUS_REVIEW,
						self::STATUS_PROCEEDING,
						self::STATUS_SUSPENDED,
					])
					->sum('amount');

		return abs($total);
	}

	/**
	* Get user total balance
	*/
	public static function getUserAmount($user_id) {
		$total = self::where('user_id', $user_id)
					->where(function($query) {
						$query->where(function($query2) {
							$query2->where('status', self::STATUS_DONE)
									->where('type', '<>', self::TYPE_WITHDRAWAL);
						})
						->orWhere(function($query2) {
							$query2->where('type', self::TYPE_WITHDRAWAL)
									->whereIn('status', [
										self::STATUS_AVAILABLE,
										self::STATUS_REVIEW,
										self::STATUS_PROCEEDING,
										self::STATUS_SUSPENDED,
										self::STATUS_DONE,
									]);
						});
					})
					->sum('amount');

		return $total;
	}

	/**
	* Get iJobDesk earning amount
	* @author Ro Un Nam
	* @since Dec 22, 2017
	*/
	public static function getEarningAmount($params = []) {
		return self::where(function($query1) use ($params) {
								$query1->where('status', self::STATUS_DONE)
										->where('user_id', SUPERADMIN_ID)
										->where('for', self::FOR_IJOBDESK)
										->where('type', '<>', self::TYPE_SITE_WITHDRAWAL);

								if ( isset($params['from']) && $params['from'] ) {
									$query1->where('done_at', '>=', $params['from']);
								}

								if ( isset($params['to']) && $params['to'] ) {
									$query1->where('done_at', '<=', $params['to']);
								}
							})
							->orWhere(function($query2) use ($params) {
								$query2->where('type', self::TYPE_SITE_WITHDRAWAL)
										->where('for', self::FOR_IJOBDESK);

								if ( isset($params['from']) && $params['from'] ) {
									$query2->where('done_at', '>=', $params['from']);
								}

								if ( isset($params['to']) && $params['to'] ) {
									$query2->where('done_at', '<=', $params['to']);
								}
							})->sum('amount');
	}

	/**
	* For withdraws
	*/
	public static function enableStatusChanged($t) {
		$attributes = '';

		if ( $t->status == self::STATUS_AVAILABLE ) {
			$attributes .= ' data-status-' . self::STATUS_CANCELLED . '=true';
			$attributes .= ' data-status-' . self::STATUS_SUSPENDED . '=true';
			$attributes .= ' data-status-' . self::STATUS_PROCEEDING . '=true';
			if ( $t->isSiteWithdraw() ) {
				$attributes .= ' data-status-DELETE=true';
			}

			if ( !$t->notify_sent ) {
				$attributes .= ' data-status-' . self::STATUS_NOTIFIED . '=true';	
			}
		} else if ( $t->status == self::STATUS_PROCEEDING ) {
			$attributes .= ' data-status-' . self::STATUS_SUSPENDED . '=true';
			$attributes .= ' data-status-' . self::STATUS_CANCELLED . '=true';
		} else if ( $t->status == self::STATUS_SUSPENDED ) {
			$attributes .= ' data-status-' . self::STATUS_AVAILABLE . '=true';
			$attributes .= ' data-status-' . self::STATUS_PROCEEDING . '=true';
			$attributes .= ' data-status-' . self::STATUS_CANCELLED . '=true';
		}

		return $attributes;
	}

	/**
	* For deposits
	*/
	public static function enableDepositStatusChanged($t) {
		$attributes = '';

		if ( $t->status == self::STATUS_AVAILABLE ) {
			$attributes .= ' data-status-' . self::STATUS_PROCEEDING . '=true';
			$attributes .= ' data-status-' . self::STATUS_SUSPENDED . '=true';
			$attributes .= ' data-status-' . self::STATUS_DONE . '=true';
			$attributes .= ' data-status-EDIT=true';
			$attributes .= ' data-status-DELETE=true';
		} else if ( $t->status == self::STATUS_PROCEEDING ) {
			$attributes .= ' data-status-' . self::STATUS_SUSPENDED . '=true';
			$attributes .= ' data-status-' . self::STATUS_DONE . '=true';
			$attributes .= ' data-status-EDIT=true';
			$attributes .= ' data-status-DELETE=true';
		} else if ( $t->status == self::STATUS_SUSPENDED ) {
			$attributes .= ' data-status-' . self::STATUS_PROCEEDING . '=true';
			$attributes .= ' data-status-' . self::STATUS_DONE . '=true';
			$attributes .= ' data-status-EDIT=true';
			$attributes .= ' data-status-DELETE=true';
		}

		return $attributes;
	}

	public function getArray() {
		$user = Auth::user();

		$gateway_type = 0;
		$gateway_data = [];
		if ( $this->user_payment_gateway_data ) {
			$gateway_data = json_decode($this->user_payment_gateway_data, true);
			if ( isset($gateway_data['gateway']) ) {
				$gateway_type = $gateway_data['gateway'];

				if ( $gateway_type == PaymentGateway::GATEWAY_WEIXIN ) {
					if ( $this->userPaymentGateway && $file = $this->userPaymentGateway->file ) {
						$gateway_data['file_url'] = file_url($file);
					}
				} else if ( $gateway_type == PaymentGateway::GATEWAY_WIRETRANSFER ) {
					$gateway_data['bankCountryName'] = Country::getCountryNameByCode($gateway_data['bankCountry']);
				}
			}			
		}

		$array = [
			'id' => $this->id,
			'user_id' => $this->user_id,
			'user_name' => $this->user->fullname(),
			'amount' => formatCurrency(abs($this->amount)),
			'comment' => $this->note ? $this->note : '',
			'user_payment_gateway' => $this->gateway_string(),
			'user_payment_gateway_type' => $gateway_type,
			'user_payment_gateway_data' => $gateway_data,
			'created_at' => format_date('Y-m-d H:i', $this->created_at),
			'updated_at' => format_date('Y-m-d H:i', $this->updated_at),
			'status' => $this->status,
			'status_string' => $this->status_string(),
		];

		if ( $this->isDeposit() ) {
			$array['order_id'] = $this->order_id;

			if ( $this->meta ) {
				$array['meta'] = json_decode($this->meta, true);
			}
		}

		if ( $this->userPaymentGateway->paymentGateway->isWeixin() ) {
			$array['exchange_rate'] = Settings::get('CNY_EXCHANGE_RATE_SELL');
			if ( $this->ref_amount < 0 ) {
				$array['exchange_amount'] = abs($this->ref_amount);
			} else {
				$array['exchange_amount'] = number_format(abs($array['exchange_rate'] * $this->amount), 2, '.', '');
			}
		} else if ( $this->userPaymentGateway->paymentGateway->isPayoneer() ) {
			$array['exchange_rate'] = Settings::get('EUR_EXCHANGE_RATE_SELL');
			if ( $this->ref_amount < 0 ) {
				$array['exchange_amount'] = abs($this->ref_amount);
			} else {
				$array['exchange_amount'] = number_format(abs($array['exchange_rate'] * $this->amount), 2, '.', '');
			}
		}

		// Reason
        $reason = Reason::where('type', Reason::TYPE_TRANSACTION)
						->where('affected_id', $this->id)
						->first();

		if ( $reason ) {
			$array['reason_message'] = $reason->message;
		}

		// Action history
        $history = ActionHistory::where('type', ActionHistory::TYPE_TRANSACTION)
								->where('target_id', $this->id)
								->orderBy('id', 'desc')
								->first();

		if ( $history ) {
			$array['modified_by'] = $history->doer->fullname();
			$array['action_history'] = $history->description;
		}

		return $array;
	}

	public function getJson() {
		return json_encode($this->getArray());
	}

}