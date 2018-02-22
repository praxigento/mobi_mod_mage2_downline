<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Service\Customer\Request;

/**
 * @method string getCountryCode() ISO-2 code for country of the customer registration.
 * @method void  setCountryCode(string $data)
 * @method int getCustomerId() Magento ID for customer itself.
 * @method void  setCustomerId(int $data)
 * @method string getDate() UTC date ('2015-11-23 12:23:34').
 * @method void  setDate(string $data)
 * @method int getParentId() Magento ID parent customer.
 * @method void  setParentId(int $data)
 * @method string getMlmId() MLM ID or something like that.
 * @method void  setMlmId(string $data)
 * @method string getReferralCode() unique code to compose referral link.
 * @method void  setReferralCode(string $data)
 */
class Add extends \Praxigento\Core\App\Service\Base\Request {
}