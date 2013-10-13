<?php
/**
 * Created by JetBrains PhpStorm.
 * User: User
 * Date: 11/10/13
 * Time: 12:57 PM
 * To change this template use File | Settings | File Templates.
 */

class LoyaltyVoucherOrderExtension extends DataExtension
{
	private static $db = array(
		'RewardsGiven' => 'Boolean'
	);
}