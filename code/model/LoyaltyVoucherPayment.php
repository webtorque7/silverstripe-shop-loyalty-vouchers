<?php
/**
 * Created by JetBrains PhpStorm.
 * User: User
 * Date: 11/10/13
 * Time: 12:54 PM
 * To change this template use File | Settings | File Templates.
 */

class LoyaltyVoucherPayment extends DataExtension
{
	/**
	 * Update order status when payment is sucessful.
	 * This is called when payment status is updated in Payment.
	 */
	public function onAfterWrite() {
		if($this->owner->Status == 'Success' && ($order = $this->owner->Order()) && !$order->RewardsGiven) {
			if ($member = Member::currentUser()) {
				$member->updateRewards($order);
			}
		}
	}
}