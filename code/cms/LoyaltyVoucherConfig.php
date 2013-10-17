<?php
/**
 * Created by JetBrains PhpStorm.
 * User: User
 * Date: 11/10/13
 * Time: 11:14 AM
 * To change this template use File | Settings | File Templates.
 */

class LoyaltyVoucherConfig extends DataExtension {

	private static $db = array(
		'RewardThreshold' => 'Int',
		'RewardType' => "Enum('Percent,Amount','Percent')",
		'RewardAmount' => 'Currency',
		'RewardPercent' => 'Percentage',
		'RewardEmailSubject' => 'Varchar',
		'RewardEmailMessage' => 'HTMLText'
	);

	public function updateCMSFields(FieldList $fields) {
		$fields->addFieldsToTab('Root.Shop.ShopTabs.LoyaltyRewards', array(
			NumericField::create('RewardThreshold', 'Reward Threshold')->setDescription('How much a member spends before getting a reward'),
			DropdownField::create('RewardType', 'Reward Type', $this->owner->dbObject('RewardType')->enumValues())->setDescription('The type of discount for the reward'),
			CurrencyField::create('RewardAmount', 'Reward Amount')->setDescription('The amount of discount if Reward Type is Amount'),
			NumericField::create('RewardPercent', 'Reward Percent')->setDescription('The percent discount if Reward Type is Percent (eg 0.05 = 5%, 0.5 = 50%, and 5 = 500%)'),
			ToggleCompositeField::create('RewardEmail', 'Email', array(
				LabelField::create('RewardEmailHelp', 'Replacement variables, $Discount, $FirstName, $Surname, $Name, $Code'),
				TextField::create('RewardEmailSubject', 'Subject'),
				HtmlEditorField::create('RewardEmailMessage', 'Message')
			))
		));
	}

}