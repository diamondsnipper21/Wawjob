<?php namespace iJobDesk\Models;

use Auth;
use Illuminate\Database\Eloquent\SoftDeletes;

use iJobDesk\Models\Reason;
use iJobDesk\Models\Settings;

class ContractMilestone extends Model {

    use SoftDeletes;

    /**
    * The table associated with the model.
    *
    * @var string
    */
    protected $table = 'contract_milestones';

    const HOURS_WAITING_REQUEST_PAYMENT = 12;

    const NOT_FUNDED = 0;
    const FUNDED = 1;
    const FUND_PAID = 3;
    const FUND_REFUNDED = 4;
    const FUND_PENDING = 10; // Not used in database, but used for status dropdowns while transaction is pending status.

	public static $str_fund_status;

	const CHANGED_STATUS_NO = 0;
	const CHANGED_STATUS_ADDED = 1;
	const CHANGED_STATUS_DELETED = 2;
	const CHANGED_STATUS_CHANGED = 3;

    const PERFORMED_BY_BUYER = 0;
    const PERFORMED_BY_SUPER_ADMIN = 1;

    /**
    * The attributes that should be mutated to dates.
    *
    * @var array
    */
    protected $dates = ['created_at', 'requested_at', 'funded_at', 'updated_at', 'deleted_at'];

    /**
    * Indicates if the model should be timestamped.
    *
    * @var bool
    */
    public $timestamps = true;

	function __construct() {
		parent::__construct();

		self::$str_fund_status = [
			self::NOT_FUNDED      	=> '-',
			self::FUNDED       		=> trans('common.funded'),
			self::FUND_PAID     	=> trans('common.released'), 
		    self::FUND_REFUNDED     => trans('common.refunded'), 
		];
	}

    public function fund_status_string() {
		if ( isset(self::$str_fund_status[$this->fund_status]) ) {
			return self::$str_fund_status[$this->fund_status];
		}

		return '-';
    }

    public static function getOptions($cat) {
    	if ($cat == 'fund_status') {
    		return [
                self::FUNDED       		=> 'In Escrow',
                self::FUND_PENDING      => 'Pending',
                self::FUND_PAID         => 'Released', 
                self::FUND_REFUNDED     => 'Refunded', 
    		];
    	}

    	return [];
    }

    public function name_string() {
        return strlen($this->name) > 500 ? substr($this->name, 0, 500) : $this->name;
    }

    public function new_name_string() {
        return strlen($this->name) > 500 ? substr($this->name, 0, 500) : $this->name;
    }

    public function getPrice() {
        $price = $this->price;

        if ( $this->changed_status != self::CHANGED_STATUS_NO ) {
            $price = $this->new_price;
        }

        return $price;
    }

    public function status_date_string() {
    	$return = '';
        if ( $this->isReleased() || $this->isPending() ) {
			$return = '<span class="date">(' . format_date('M d, Y', $this->contractor_transaction ? $this->contractor_transaction->created_at : $this->transaction->created_at) . ')</span>';
        } else if ( $this->isRefunded() ) {
        	$return = '<span class="date">(' . format_date('M d, Y', $this->refund_transaction->done_at) . ')</span>';
        } else {
			if ( $this->isRequested() ) {
				$return = '<span class="date">(' . trans('common.fund_requested') . ' - ' . format_date('M d, Y', $this->requested_at) . ')</span>';
			}
		}

		return $return;
    }

    /**
    * Get the contract
    */
    public function contract() {
    	return $this->hasOne('iJobDesk\Models\Contract', 'id', 'contract_id');
    }

    /**
    * Get the transaction
    */
    public function transaction() {
        return $this->hasOne('iJobDesk\Models\TransactionLocal', 'id', 'transaction_id');
    }

	public function short_name() {
		if ( mb_strlen($this->name) <= 50 ) {
			return $this->name;
		}

		return mb_substr($this->name, 0, 47, 'UTF-8') . '...';
	}

	/**
	* Get the transaction
	*/
	public function contractor_transaction() {
		return $this->hasOne('iJobDesk\Models\TransactionLocal', 'id', 'contractor_transaction_id');
	}

	/**
	* Get the refunded transaction
	*/
	public function refund_transaction() {
		return $this->hasOne('iJobDesk\Models\TransactionLocal', 'old_id', 'transaction_id');
	}

	public function isRequested() {
		return $this->payment_requested == 1;
	}

    public function isEditable() {
        if ( $this->isReleased() || $this->isPending() || $this->isFunded() || $this->isRefunded() ) {
            return false;
        }

        return true;
    }

	public function isFunded() {
		return $this->fund_status == self::FUNDED; 
	}

    public function isPending() {
    	if ( !$this->contractor_transaction ) {
    		return false;
    	}

        return $this->fund_status == self::FUND_PAID && $this->contractor_transaction->isAvailable(); 
    }

	public function isReleased() {
		if ( !$this->contractor_transaction ) {
			return false;
		}

		return $this->fund_status == self::FUND_PAID && $this->contractor_transaction->isDone(); 
	}

	public function isRefunded() {
		return $this->fund_status == self::FUND_REFUNDED; 
	}

    public function isAvailableFund() {
    	if ( $this->isFunded() ) {
    		return false;
    	}

    	return $this->isAvailableRelease();
    }

    public function isAvailableRelease() {
    	if ( $this->isReleased() || $this->isRefunded() || $this->isPending() ) {
    		return false;
    	}

        if ( $this->isFunded() ) {
            return true;
        }

        // Check the balance
        if ( $this->contract->buyer->myBalance() < $this->getPrice() ) {
            return false;
        }

    	return true;
    }

    public function isPerformedByBuyer() {
        return $this->performed_by == self::PERFORMED_BY_BUYER; 
    }

    public function isPerformedByAdmin() {
        return $this->performed_by == self::PERFORMED_BY_SUPER_ADMIN; 
    }

    public function getJson() {
        $user = Auth::user();

        $array = [
            'id' => $this->id,
            'name' => $this->name,
            'contract_title' => $this->contract->title,
            'amount' => formatCurrency($this->getPrice()),
            'buyer_name' => $this->contract->buyer->fullname(),
            'freelancer_name' => $this->contract->contractor->fullname(),
            'funded_at' => format_date('Y-m-d H:i', $this->funded_at),
            'updated_at' => format_date('Y-m-d H:i', $this->updated_at),
            'status' => $this->isPending() ? ContractMilestone::FUND_PENDING : $this->fund_status,
            'status_string' => $this->isPending() ? $this->getOptions('fund_status')[ContractMilestone::FUND_PENDING] : $this->getOptions('fund_status')[$this->fund_status],
            'performed_by' => $this->isPerformedByBuyer() ? 'Buyer' : 'Super Admin',
        ];

        // Reason
        $reason = Reason::where('type', Reason::TYPE_CONTRACT_MILESTONE)
						->where('affected_id', $this->id)
						->first();

		if ( $reason ) {
			$array['reason'] = $reason->reason_string();
			$array['reason_message'] = $reason->message;
		}

        return json_encode($array);
    }
    
	/**
	* Check if the "Request Payment" button should be disabled
	* @author Ro Un Name 
	* @since Jun 05, 2017
	*/
	public function checkRequestPaymentButton() {
        if ( in_array($this->changed_status, [
            self::CHANGED_STATUS_ADDED,
            self::CHANGED_STATUS_DELETED,
            self::CHANGED_STATUS_CHANGED
        ]) ) {
            return false;
        }

		if ( self::isReleased() || self::isPending() || self::isRefunded() ) {
			return false;
		}

		if ( !self::isRequested() ) {
			return true;
		} else {
			$diff = date_diff(date_create(), date_create($this->requested_at));

			$hours = ($diff->y * 365 + $diff->m * 30 + $diff->d) * 24 + $diff->h;

			if ( $hours > self::HOURS_WAITING_REQUEST_PAYMENT )
				return true;
			else
				return false;
		}
	}

    public static function availableActionsByStatus($fund_status) {
        $actions = [];

        if ( $fund_status == ContractMilestone::NOT_FUNDED ) {
            $actions[] = ContractMilestone::FUNDED;
        } else if ( $fund_status == ContractMilestone::FUNDED ) {
        	$actions[] = ContractMilestone::NOT_FUNDED;
            $actions[] = ContractMilestone::FUND_PAID;
            $actions[] = ContractMilestone::FUND_REFUNDED;
        } else if ( $fund_status == ContractMilestone::FUND_PAID ) {
            $actions[] = ContractMilestone::NOT_FUNDED;
        }

        $attributes = '';
        foreach ($actions as $action) {
            $attributes .= ' data-status-'.$action.'=true';
        }

        return $attributes;
    }

}