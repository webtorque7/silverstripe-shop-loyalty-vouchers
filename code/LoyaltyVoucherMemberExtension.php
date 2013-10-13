<?php
/**
 * Created by JetBrains PhpStorm.
 * User: User
 * Date: 11/10/13
 * Time: 11:20 AM
 * To change this template use File | Settings | File Templates.
 */

class LoyaltyVoucherMemberExtension extends DataExtension
{
	private static $db = array(
		'RewardAmount' => 'Decimal(10,2)',
		'NoRewards' => 'Int',

	);


	/**
	 * Update the Member's awards from a fulfilled order
	 * @param $order Order
	 */
	public function updateRewards($order) {

		$this->owner->RewardAmount = $this->owner->RewardAmount + $order->SubTotal();
		if ($this->checkRewardAmount()) {
			$order->RewardsGiven = true;
			$order->write();
		}

		$this->owner->write();
	}

	/**
	 * Check if rewards should be given, and call giveReward if so, returns true if rewards given
	 * @return bool
	 */
	public function checkRewardAmount() {

		$config = SiteConfig::current_site_config();

		if ($this->owner->RewardAmount >= $config->RewardThreshold) {
			//work out how many rewards they got for this order
			$noRewards = floor($this->owner->RewardAmount / $config->RewardThreshold);
			//remove the amount which got used for the reward
			$this->owner->RewardAmount = $this->owner->RewardAmount - ($config->RewardThreshold * $noRewards);

			$this->giveReward($noRewards);

			return true;
		}

		return false;
	}

	public function giveReward($noRewards) {

		$config = SiteConfig::current_site_config();

		$this->owner->NoRewards += $noRewards;

		//new coupon for each rewards
		for ($i = 0; $i <= $noRewards; $i++) {
			$reward = OrderCoupon::create();
			$reward->MemberID = $this->owner->ID;
			$reward->Type = $config->RewardType;
			$reward->Amount = $config->RewardAmount;
			$reward->Percentage = $config->RewardPercentage;
			$reward->Active = true;
			$reward->write();

			$this->sendRewardEmail($reward);
		}
	}

	public function sendRewardEmail(OrderCoupon $voucher) {

		$email = new Generic_Email();

		$email->setTo($this->owner->Email);

		$email->populateFromConfig('RewardEmailSubject', 'RewardEmailMessage', array(
			'$Discount' => $voucher->getDiscountNice(),
			'$FirstName' => $this->owner->FirstName,
			'$Surname' => $this->owner->Surname,
			'$Name' => $this->owner->Name,
			'$Code' => $voucher->getCode
		));

		$email->send();
	}
}