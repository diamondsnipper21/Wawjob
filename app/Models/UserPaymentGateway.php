<?php namespace iJobDesk\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

use iJobDesk\Models\Country;
use iJobDesk\Models\User;
use iJobDesk\Models\UserDeposit;
use iJobDesk\Models\Settings;

/**
* @author Ro Un Nam
*/
class UserPaymentGateway extends Model {

    use SoftDeletes;

    protected $table = 'user_payment_gateways';

    /**
    * The attributes that should be mutated to dates.
    *
    * @var array
    */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /**
    * Indicates if the model should be timestamped.
    *
    * @var bool
    */
    public $timestamps = true;

    /**
    * Primary
    */
    const IS_PRIMARY_NO = 0;
    const IS_PRIMARY_YES = 1;

    /**
    * Status
    */
    const IS_STATUS_NO = 0;
    const IS_STATUS_YES = 1;
    const IS_STATUS_EXPIRED = 2;

    /**
    * Pending
    */
    const IS_PENDING_NO = 0;
    const IS_PENDING_YES = 1;

    function __construct() {
        parent::__construct();
    }

    /**
    * Get the payment gateway.
    */
    public function paymentGateway() {
        return $this->hasOne('iJobDesk\Models\PaymentGateway', 'id', 'gateway');
    }

    public function enabledWithdrawal() {
        return $this->paymentGateway->enable_withdraw == 1;
    }

    public function enabledDeposit() {
        return $this->paymentGateway->enable_deposit == 1;
    }

    public function isPayPal() {
        return $this->paymentGateway->isPayPal();
    }

    public function isCreditCard() {
        return $this->paymentGateway->isCreditCard();
    }

    public function isWeixin() {
        return $this->paymentGateway->isWeixin();
    }

    public function isWireTransfer() {
        return $this->paymentGateway->isWireTransfer();
    }

    public function isSkrill() {
        return $this->paymentGateway->isSkrill();
    }

    public function isPayoneer() {
        return $this->paymentGateway->isPayoneer();
    }

    public function user() {
        return $this->hasOne('iJobDesk\Models\User', 'id', 'user_id');
    }

    public function isEnabledCountry() {
        $country = $this->user->contact->country;

        if ( $country ) {
            if ( $this->isPayPal() ) {
                return $country->paypal_enabled;
            } if ( $this->isPayoneer() ) {
                return $country->payoneer_enabled;
            } else if ( $this->isSkrill() ) {
                return $country->skrill_enabled;
            } else if ( $this->isWeixin() ) {
                return $country->wechat_enabled;
            } else if ( $this->isWireTransfer() ) {
                return $country->bank_enabled;
            } else if ( $this->isCreditCard() ) {
                return $country->creditcard_enabled;
            }
        }

        return true;
    }

    public function file() {
        return $this->hasOne('iJobDesk\Models\File', 'id', 'file_id');
    }

    public function depositAmount() {
        $amount = UserDeposit::getAmount($this->user_id, $this->gateway, $this->real_id);

        return number_format($amount, 2, '.', '');
    }

    public static function getUserPrimaryPaymentGateway($user_id) {
        return self::where('user_id', $user_id)
                    ->where('is_primary', self::IS_PRIMARY_YES)
                    ->where('is_pending', self::IS_PENDING_NO)
                    ->where('status', self::IS_STATUS_YES)
                    ->select(['id', 'gateway', 'data', 'is_primary'])
                    ->first();
    }

    public function isPrimary() {
        return $this->is_primary == self::IS_PRIMARY_YES;
    }

    public function isActive() {
        return $this->status == self::IS_STATUS_YES && $this->is_pending != self::IS_PENDING_YES;
    }

    public function isExpired() {
        return $this->status == self::IS_STATUS_EXPIRED;
    }

    public function isPending() {
        return $this->is_pending == self::IS_PENDING_YES;
    }

    public function depositFee() {
        if ( $this->paymentGateway->isPayPal() ) {
            return Settings::get('DEPOSIT_FEE_PAYPAL');
        } else if ( $this->paymentGateway->isSkrill() ) {
            return Settings::get('DEPOSIT_FEE_SKRILL');
        } else if ( $this->paymentGateway->isPayoneer() ) {
            return Settings::get('DEPOSIT_FEE_PAYONEER');
        } else {
            return 0;
        }
    }

    public function withdrawFee() {
        if ( $this->paymentGateway->isPayPal() ) {
            return Settings::get('WITHDRAW_PAYPAL_FEE');
        } else if ( $this->paymentGateway->isSkrill() ) {
            return Settings::get('WITHDRAW_SKRILL_FEE');
        } else if ( $this->paymentGateway->isPayoneer() ) {
            return Settings::get('WITHDRAW_PAYONEER_FEE');
        } else if ( $this->paymentGateway->isWeixin() ) {
            return Settings::get('WITHDRAW_WECHAT_FEE');
        } else if ( $this->paymentGateway->isCreditCard() ) {
            return Settings::get('WITHDRAW_CREDITCARD_FEE');
        } else {
            return 0;
        }
    }

    public function withdrawFixedFee() {
        if ( $this->paymentGateway->isPayPal() ) {
            return Settings::get('WITHDRAW_PAYPAL_FIXED_FEE');
        } else if ( $this->paymentGateway->isSkrill() ) {
            return Settings::get('WITHDRAW_SKRILL_FIXED_FEE');
        } else if ( $this->paymentGateway->isPayoneer() ) {
            return Settings::get('WITHDRAW_PAYONEER_FIXED_FEE');
        } else if ( $this->paymentGateway->isWireTransfer() ) {
            return Settings::get('WITHDRAW_BANK_FEE');
        } else if ( $this->paymentGateway->isWeixin() ) {
            return Settings::get('WITHDRAW_WECHAT_FIXED_FEE');
        } else if ( $this->paymentGateway->isCreditCard() ) {
            return Settings::get('WITHDRAW_CREDITCARD_FIXED_FEE');
        } else {
            return 0;
        }
    }

    public function withdrawFeeAmount($amount = 0) {
		$withdraw_fee = 0;

		$fee = $this->withdrawFee();
		$fixed_fee = $this->withdrawFixedFee();

		if ( $fee ) {
			$withdraw_fee += $amount * $fee / 100;
		}

		if ( $fixed_fee ) {
			$withdraw_fee += $fixed_fee;
		}

		if ( $withdraw_fee > 0 ) {
			$withdraw_fee = formatCurrency($withdraw_fee);
		}

		return round($withdraw_fee, 2);
    }

    public function title() {
        $title = '';

        $data = json_decode($this->data);

        if ( $this->paymentGateway->isWireTransfer() ) {
            if ( isset($data->bankName) ) {
                $title = $data->bankName;
            }
        } else if ( $this->paymentGateway->isCreditCard() ) {
        	if ( isset($data->firstName) && isset($data->lastName) && isset($data->cardNumber) ) {
                $title = $data->firstName . ' ' . $data->lastName . ' - xxxx ' . $data->cardNumber;
            }
        } else if ( $this->paymentGateway->isWeixin() ) {
            if ( isset($data->phoneNumber) ) {
                $title = $data->phoneNumber;
            }
        } else {
			if ( isset($data->email) ) {
                $title = $data->email;
            }
        }

        return $title;
    }

    public function info() {
        $info = '';

        $data = json_decode($this->data);

        if ( $this->paymentGateway->isWireTransfer() ) {
        	if ( $data->bankCountry ) {
            	$info .= '<div class="row"><label class="col-xs-6">' . trans('user.payment_method.country_of_bank') . '</label><div class="col-xs-6">' . Country::getCountryNameByCode($data->bankCountry) . '</div></div>';
            }

            if ( $data->bankBranch ) {
            	$info .= '<div class="row"><label class="col-xs-6">' . trans('user.payment_method.bank_branch') . '</label><div class="col-xs-6">' . $data->bankBranch . '</div></div>';
            }

            if ( $data->ibanAccountNo ) {
            	$info .= '<div class="row"><label class="col-xs-6">' . trans('user.payment_method.iban_account_no') . '</label><div class="col-xs-6">' . $data->ibanAccountNo . '</div></div>';
            }

            if ( $data->accountName ) {
            	$info .= '<div class="row"><label class="col-xs-6">' . trans('user.payment_method.account_name') . '</label><div class="col-xs-6">' . $data->accountName . '</div></div>';
            }

            if ( $data->beneficiaryAddress1 ) {
            	$info .= '<div class="row"><label class="col-xs-6">' . trans('user.payment_method.beneficiary_address1') . '</label><div class="col-xs-6">' . $data->beneficiaryAddress1 . '</div></div>';
            }

            if ( $data->beneficiaryAddress2 ) {
            	$info .= '<div class="row"><label class="col-xs-6">' . trans('user.payment_method.beneficiary_address2') . '</label><div class="col-xs-6">' . $data->beneficiaryAddress2 . '</div></div>';
            }

            if ( $data->beneficiarySwiftCode ) {
            	$info .= '<div class="row"><label class="col-xs-6">' . trans('user.payment_method.beneficiary_swift_code') . '</label><div class="col-xs-6">' . $data->beneficiarySwiftCode . '</div></div>';
            }

            if ( $info ) {
            	$info = '<div class="info w-70">' . $info . '</div>';
            }
        }

        return $info;
    }

    public function logo() {
        if ( $this->paymentGateway->isCreditCard() ) {
            $gateway_data = json_decode($this->data, true);

            return '/assets/images/pages/payment/' . $gateway_data['cardType'] . '.png';
        } else {
            return $this->paymentGateway->logo;
        }
    }

    public function dataArray() {
        return json_decode($this->data, true);
    }

    public function dataJson() {
    	return $this->data;
    }

    public function isEnabledWithdraw() {
        if ( !$this->isActive() ) {
            return false;
        }

        if ( !$this->enabledWithdrawal() ) {
            return false;
        }

        if ( $this->user->isBuyer() && $this->depositAmount() <= 0 ) {
            return false;
        }

    	$diff = date_diff(date_create(), date_create($this->created_at));

    	$days = $diff->d;
		if ( $diff->y ) {
			$days += $diff->y * 365;
		}

		if ( $diff->m ) {
			$days += $diff->m * 30;
		}

		if ( $days >= Settings::get('DAYS_AVAILABLE_PAYMENT_METHOD') ) {
			return true;
		}

		return false;
    }

    public static function get($id) {
        return self::where('id', $id)->first();
    }

    public static function isDuplicatedGateway($request = null, $user = null) { 

        $gateway = $request->input('_gateway');

        $gateway_same_user = self::where('gateway', $gateway)
                                ->where('user_id', $user->id);

        $gateway_another_user = self::where('gateway', $gateway)
                                    ->where('user_id', '<>', $user->id);  

        if ( $request->input('_id') ) {
            $gateway_another_user = $gateway_another_user->where('id', '<>', $request->input('_id'));
        }

        if ( $gateway == PaymentGateway::GATEWAY_PAYPAL ) {
            $field = 'data';
            $data = [
                'email' => trim($request->input('paypalEmail'))
            ];

            $value = json_encode($data);
        } else if ( $gateway == PaymentGateway::GATEWAY_SKRILL ) {
            $field = 'data';
            $data = [
                'email' => trim($request->input('skrillEmail'))
            ];

            $value = json_encode($data);
        } else if ( $gateway == PaymentGateway::GATEWAY_PAYONEER ) {
            $field = 'data';
            $data = [
                'email' => trim($request->input('payoneerEmail'))
            ];

            $value = json_encode($data);
        } else if ( $gateway == PaymentGateway::GATEWAY_WEIXIN ) {
            $field = 'data';
            $data = [
                'phoneNumber' => trim($request->input('weixinNumber'))
            ];

            $value = json_encode($data);
        } else if ( $gateway == PaymentGateway::GATEWAY_WIRETRANSFER ) {
            $field = 'real_id';
            $value = implode('_', [
                trim($request->input('bankCountry')),
                trim($request->input('ibanAccountNo')),
                trim($request->input('beneficiarySwiftCode')),
            ]);
        } else {
            $field = 'real_id';
            $value = encrypt_string(trim($request->input('cardNumber')));
        }

        // Just for only new
        if ( !$request->input('_id') || ($request->input('_id') && $gateway == PaymentGateway::GATEWAY_WIRETRANSFER) ) {
	        $gateway_same_user_count = $gateway_same_user->where($field, $value)
                                                        ->where('gateway', $gateway)
	                                                    ->where('status', self::IS_STATUS_YES)
	                                                    ->count();

	        $gateway_another_user_count = $gateway_another_user->where($field, $value)
                                                                ->where('gateway', $gateway)
	                                                            ->where('status', self::IS_STATUS_YES)
	                                                            ->count();

	        $deleted_another_user_count = UserPaymentGateway::withTrashed()
						        							->where($field, $value)
                                                            ->where('gateway', $gateway)
															->where('status', self::IS_STATUS_YES)
															->where('user_id', '<>', $user->id)
															->count();

			$deleted_user_count = UserPaymentGateway::withTrashed()
		        									->where($field, $value)
                                                    ->where('gateway', $gateway)
													->where('status', self::IS_STATUS_YES)
													->where('user_id', $user->id)
													->count();
		} else {
			return 'none';
		}

        if ( $gateway_same_user_count > 0 ) {
            return 'same_duplicated';
        } elseif ( $gateway_another_user_count > 0 ) {
            return 'another_duplicated';
        } else if ( $deleted_another_user_count > 0 ) {
        	return 'deleted_duplicated';
        } else if ( $deleted_user_count > 0 ) {
        	// Deleted dupulicated gateway would be deleted permanentely.
        // 	UserPaymentGateway::withTrashed()
        // 						->where($field, $value)
								// ->where('status', self::IS_STATUS_YES)
								// ->where('user_id', $user->id)
								// ->forceDelete();

        	return 'none';
        } else {
           	return 'none';
        }
    }
}