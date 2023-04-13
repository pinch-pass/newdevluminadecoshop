<?php
/**
 * Copyright (C) 2017-2019 Petr Hucik <petr@getdatakick.com>
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the DataKick Regular License version 1.0
 * For more information see LICENSE.txt file
 *
 * @author    Petr Hucik <petr@getdatakick.com>
 * @copyright 2017-2019 Petr Hucik
 * @license   Licensed under the DataKick Regular License version 1.0
 */

namespace Conseqs;

class AppTranslation
{
    private $module;

    /**
     * AppTranslation constructor.
     * @param $module
     */
    public function __construct($module)
    {
        $this->module = $module;
    }

    /**
     * @return array
     */
    public function getBackTranslations()
    {
        return $this->changed(array(
            "%s records" => $this->l('%s records'),
            "(executed via CRON)" => $this->l('(executed via CRON)'),
            "--" => $this->l('--'),
            "Action" => $this->l('Action'),
            "Action: %s" => $this->l('Action: %s'),
            "Actions" => $this->l('Actions'),
            "Active" => $this->l('Active'),
            "Add condition group" => $this->l('Add condition group'),
            "Add condition" => $this->l('Add condition'),
            "Agree to terms and conditions" => $this->l('Agree to terms and conditions'),
            "All conditions in this group must be satisfied" => $this->l('All conditions in this group must be satisfied'),
            "Are you sure?" => $this->l('Are you sure?'),
            "Bind value" => $this->l('Bind value'),
            "Buy license" => $this->l('Buy license'),
            "CRON settings" => $this->l('CRON settings'),
            "Can't delete measure that is in use" => $this->l('Can\'t delete measure that is in use'),
            "Cancel" => $this->l('Cancel'),
            "Cardinality" => $this->l('Cardinality'),
            "Changes in new version:" => $this->l('Changes in new version:'),
            "Check for updates" => $this->l('Check for updates'),
            "Checking for new version of this module" => $this->l('Checking for new version of this module'),
            "Choose condition" => $this->l('Choose condition'),
            "Close" => $this->l('Close'),
            "Condition group %s" => $this->l('Condition group %s'),
            "Conditions (%s)" => $this->l('Conditions (%s)'),
            "Conditions are optional. You can use them to define specific conditions that needs to be met for action to be executed" => $this->l('Conditions are optional. You can use them to define specific conditions that needs to be met for action to be executed'),
            "Conditions" => $this->l('Conditions'),
            "Congratulations, cron is up and running. Your measures will be kept up-to-date." => $this->l('Congratulations, cron is up and running. Your measures will be kept up-to-date.'),
            "Congratulations, no error logs were found" => $this->l('Congratulations, no error logs were found'),
            "Constant value" => $this->l('Constant value'),
            "Contact permission" => $this->l('Contact permission'),
            "Contact" => $this->l('Contact'),
            "Copy of %s" => $this->l('Copy of %s'),
            "Create new measure" => $this->l('Create new measure'),
            "Create new rule" => $this->l('Create new rule'),
            "Create rule" => $this->l('Create rule'),
            "Data source" => $this->l('Data source'),
            "Date" => $this->l('Date'),
            "Define conditions" => $this->l('Define conditions'),
            "Delete all logs" => $this->l('Delete all logs'),
            "Delete measure" => $this->l('Delete measure'),
            "Delete" => $this->l('Delete'),
            "Discover and install [1]ready-to use[/1] solutions prepared by DataKick or other users of this moule. You can also [2]share[/2] your cool automations  with other users. Simply export the rules and send it to [3]conseqs@getdatakick.com[/3]. It will be added to the package repository after manual validation and verification" => $this->l('Discover and install [1]ready-to use[/1] solutions prepared by DataKick or other users of this moule. You can also [2]share[/2] your cool automations  with other users. Simply export the rules and send it to [3]conseqs@getdatakick.com[/3]. It will be added to the package repository after manual validation and verification'),
            "Do you really want to delete all error logs?" => $this->l('Do you really want to delete all error logs?'),
            "Do you really want to delete rule %s?" => $this->l('Do you really want to delete rule %s?'),
            "Do you really want to delete this measure?" => $this->l('Do you really want to delete this measure?'),
            "Done" => $this->l('Done'),
            "Download latest version" => $this->l('Download latest version'),
            "Downloading package, please wait..." => $this->l('Downloading package, please wait...'),
            "Duplicate measure" => $this->l('Duplicate measure'),
            "Duplicate rule" => $this->l('Duplicate rule'),
            "Edit measure" => $this->l('Edit measure'),
            "Edit rule #%s" => $this->l('Edit rule #%s'),
            "Edit rule #%s: %s" => $this->l('Edit rule #%s: %s'),
            "Edit sql statement" => $this->l('Edit sql statement'),
            "Email address" => $this->l('Email address'),
            "Enter constant value" => $this->l('Enter constant value'),
            "Enter rule name" => $this->l('Enter rule name'),
            "Error logs (%s)" => $this->l('Error logs (%s)'),
            "Error logs" => $this->l('Error logs'),
            "Error: %s" => $this->l('Error: %s'),
            "Export" => $this->l('Export'),
            "Exported rule" => $this->l('Exported rule'),
            "Failed to change rule active state" => $this->l('Failed to change rule active state'),
            "Failed to clear error logs" => $this->l('Failed to clear error logs'),
            "Failed to delete measure" => $this->l('Failed to delete measure'),
            "Failed to delete rule" => $this->l('Failed to delete rule'),
            "Failed to duplicate rule" => $this->l('Failed to duplicate rule'),
            "Failed to execute SQL" => $this->l('Failed to execute SQL'),
            "Failed to execute sql" => $this->l('Failed to execute sql'),
            "Failed to export rule" => $this->l('Failed to export rule'),
            "Failed to install package" => $this->l('Failed to install package'),
            "Failed to install package: %s " => $this->l('Failed to install package: %s '),
            "Failed to load error logs" => $this->l('Failed to load error logs'),
            "Failed to load measures" => $this->l('Failed to load measures'),
            "Failed to load rule" => $this->l('Failed to load rule'),
            "Failed to load rules" => $this->l('Failed to load rules'),
            "Failed to parse file content: %s" => $this->l('Failed to parse file content: %s'),
            "Failed to parse package: %s" => $this->l('Failed to parse package: %s'),
            "Failed to read file %s" => $this->l('Failed to read file %s'),
            "Failed to retrieve action input parameters" => $this->l('Failed to retrieve action input parameters'),
            "Failed to retrieve information about condition arguments" => $this->l('Failed to retrieve information about condition arguments'),
            "Failed to retrieve trigger output parameters" => $this->l('Failed to retrieve trigger output parameters'),
            "Failed to save measure: %s" => $this->l('Failed to save measure: %s'),
            "Failed to save rule: %s" => $this->l('Failed to save rule: %s'),
            "Failed to update measure values" => $this->l('Failed to update measure values'),
            "I agree" => $this->l('I agree'),
            "I also offer [1]%s support[/1] a [2]custom development[/2] services. If you are looking for an experienced %s developer then look no further. Send me an email, I would be happy to help." => $this->l('I also offer [1]%s support[/1] a [2]custom development[/2] services. If you are looking for an experienced %s developer then look no further. Send me an email, I would be happy to help.'),
            "If you find any bug or have feature request then please post it on [1]forum[/1] dedicated to this module. Or simply send me an [2]email[/2]" => $this->l('If you find any bug or have feature request then please post it on [1]forum[/1] dedicated to this module. Or simply send me an [2]email[/2]'),
            "If you want to create another %s, you will need to purchase [1]license key[/1]" => $this->l('If you want to create another %s, you will need to purchase [1]license key[/1]'),
            "Import rule" => $this->l('Import rule'),
            "In order to automatically recalculate measures, you need to set up CRON. Please insert the following line into your cron tasks manager" => $this->l('In order to automatically recalculate measures, you need to set up CRON. Please insert the following line into your cron tasks manager'),
            "Install" => $this->l('Install'),
            "Interpolate values" => $this->l('Interpolate values'),
            "Invalid number" => $this->l('Invalid number'),
            "Invalid package" => $this->l('Invalid package'),
            "Key column" => $this->l('Key column'),
            "Last executed" => $this->l('Last executed'),
            "Last updated" => $this->l('Last updated'),
            "License information" => $this->l('License information'),
            "License key" => $this->l('License key'),
            "License" => $this->l('License'),
            "Limit reached" => $this->l('Limit reached'),
            "Loading data, please wait..." => $this->l('Loading data, please wait...'),
            "Loading rule #%s" => $this->l('Loading rule #%s'),
            "Loading, please wait..." => $this->l('Loading, please wait...'),
            "Location:" => $this->l('Location:'),
            "Logs" => $this->l('Logs'),
            "Marketing offers" => $this->l('Marketing offers'),
            "Measure has been created" => $this->l('Measure has been created'),
            "Measure has been deleted" => $this->l('Measure has been deleted'),
            "Measure has been updated" => $this->l('Measure has been updated'),
            "Measure name" => $this->l('Measure name'),
            "Measure not found" => $this->l('Measure not found'),
            "Measure values has been updated" => $this->l('Measure values has been updated'),
            "Measures list" => $this->l('Measures list'),
            "Measures" => $this->l('Measures'),
            "Message" => $this->l('Message'),
            "Minimal value is 1" => $this->l('Minimal value is 1'),
            "Module %s error" => $this->l('Module %s error'),
            "Name" => $this->l('Name'),
            "Never" => $this->l('Never'),
            "New condition" => $this->l('New condition'),
            "New measure" => $this->l('New measure'),
            "New module version is available" => $this->l('New module version is available'),
            "New release notification" => $this->l('New release notification'),
            "New rule" => $this->l('New rule'),
            "New version [1]%s[/1] is available. Last check %s" => $this->l('New version [1]%s[/1] is available. Last check %s'),
            "Next Page" => $this->l('Next Page'),
            "Next step: %s" => $this->l('Next step: %s'),
            "No information available. Please click on [1]Check for updates[/1] button to check for new version of this module" => $this->l('No information available. Please click on [1]Check for updates[/1] button to check for new version of this module'),
            "No package has been found" => $this->l('No package has been found'),
            "No" => $this->l('No'),
            "Not" => $this->l('Not'),
            "Oh no, there's nothing in here" => $this->l('Oh no, there\'s nothing in here'),
            "Package search failed" => $this->l('Package search failed'),
            "Packages sucessfully installed" => $this->l('Packages sucessfully installed'),
            "Packages" => $this->l('Packages'),
            "Please enter action parameters or bind them to data provided by trigger" => $this->l('Please enter action parameters or bind them to data provided by trigger'),
            "Please enter license key" => $this->l('Please enter license key'),
            "Please enter your contact email address" => $this->l('Please enter your contact email address'),
            "Please enter your sql stamement. You can use placeholders like {id}. You will be able to bind values to these placeholders in action settings" => $this->l('Please enter your sql stamement. You can use placeholders like {id}. You will be able to bind values to these placeholders in action settings'),
            "Please provide additional information required by this action:" => $this->l('Please provide additional information required by this action:'),
            "Please provide additional information required by this trigger:" => $this->l('Please provide additional information required by this trigger:'),
            "Please read carefully and agree to our [1]terms and conditions[/1] before you start using this module." => $this->l('Please read carefully and agree to our [1]terms and conditions[/1] before you start using this module.'),
            "Please select action from the list below" => $this->l('Please select action from the list below'),
            "Please select trigger from the list below" => $this->l('Please select trigger from the list below'),
            "Please wait, loading rule..." => $this->l('Please wait, loading rule...'),
            "Please wait, resolving action parameters..." => $this->l('Please wait, resolving action parameters...'),
            "Previous Page" => $this->l('Previous Page'),
            "Purchase license" => $this->l('Purchase license'),
            "Refresh period (hours)" => $this->l('Refresh period (hours)'),
            "Report broken package" => $this->l('Report broken package'),
            "Requires external modules: " => $this->l('Requires external modules: '),
            "Requires module version: " => $this->l('Requires module version: '),
            "Rule has been created" => $this->l('Rule has been created'),
            "Rule has been deleted" => $this->l('Rule has been deleted'),
            "Rule has been disabled" => $this->l('Rule has been disabled'),
            "Rule has been enabled" => $this->l('Rule has been enabled'),
            "Rule has been updated" => $this->l('Rule has been updated'),
            "Rule list" => $this->l('Rule list'),
            "Rule" => $this->l('Rule'),
            "Rules" => $this->l('Rules'),
            "SQL is valid" => $this->l('SQL is valid'),
            "SQL statement" => $this->l('SQL statement'),
            "SQL" => $this->l('SQL'),
            "Save" => $this->l('Save'),
            "Select action" => $this->l('Select action'),
            "Select input field" => $this->l('Select input field'),
            "Select trigger" => $this->l('Select trigger'),
            "Selected action" => $this->l('Selected action'),
            "Selected trigger" => $this->l('Selected trigger'),
            "Skip this step" => $this->l('Skip this step'),
            "Stacktrace:" => $this->l('Stacktrace:'),
            "Start by adding your first measure definition" => $this->l('Start by adding your first measure definition'),
            "Start by adding your first rule or install " => $this->l('Start by adding your first rule or install '),
            "Start using module" => $this->l('Start using module'),
            "Support" => $this->l('Support'),
            "Test SQL" => $this->l('Test SQL'),
            "Thank you for installing [1]Consequences[/1] module. We are very happy to have you aboard." => $this->l('Thank you for installing [1]Consequences[/1] module. We are very happy to have you aboard.'),
            "This action does not have any parameters" => $this->l('This action does not have any parameters'),
            "This package requires conseqs version %s. Please upgrade" => $this->l('This package requires conseqs version %s. Please upgrade'),
            "Trigger" => $this->l('Trigger'),
            "Trigger: %s" => $this->l('Trigger: %s'),
            "Tutorials and suggestions" => $this->l('Tutorials and suggestions'),
            "Unknown column" => $this->l('Unknown column'),
            "Unknown" => $this->l('Unknown'),
            "Unnamed rule" => $this->l('Unnamed rule'),
            "Unused" => $this->l('Unused'),
            "Update frequency" => $this->l('Update frequency'),
            "Update measure values" => $this->l('Update measure values'),
            "Update module" => $this->l('Update module'),
            "Usage" => $this->l('Usage'),
            "Used by %s rules" => $this->l('Used by %s rules'),
            "Used by one rule" => $this->l('Used by one rule'),
            "Value column" => $this->l('Value column'),
            "We need your permission to contact you in [1]emergency[/1] situations, for example if we discover a serious [2]security[/2] bug" => $this->l('We need your permission to contact you in [1]emergency[/1] situations, for example if we discover a serious [2]security[/2] bug'),
            "We promise that we will [1]not disclose[/1] your email address to anyone or use it to [2]spam[/2] you. We will not send you automated [3]marketing[/3] emails unless you [4]opt-in[/4] for it.[5][/5]You can read our [6]privacy policy[6] here." => $this->l('We promise that we will [1]not disclose[/1] your email address to anyone or use it to [2]spam[/2] you. We will not send you automated [3]marketing[/3] emails unless you [4]opt-in[/4] for it.[5][/5]You can read our [6]privacy policy[6] here.'),
            "What emails can we send you?" => $this->l('What emails can we send you?'),
            "Yes" => $this->l('Yes'),
            "You are using [1]free version[/1] of this module. Free version comes with following restrictions:" => $this->l('You are using [1]free version[/1] of this module. Free version comes with following restrictions:'),
            "You can contact me" => $this->l('You can contact me'),
            "You can create up to [1]%s[/1] measures" => $this->l('You can create up to [1]%s[/1] measures'),
            "You can create up to [1]%s[/1] rules" => $this->l('You can create up to [1]%s[/1] rules'),
            "You can purchase license for this module on [1]datakick store[/1]" => $this->l('You can purchase license for this module on [1]datakick store[/1]'),
            "You can't edit this measure. You can, however, duplicate it and make changes to the duplicated version" => $this->l('You can\'t edit this measure. You can, however, duplicate it and make changes to the duplicated version'),
            "You have defined multiple condition groups. To execute action, at least one group must be satisfied" => $this->l('You have defined multiple condition groups. To execute action, at least one group must be satisfied'),
            "You have latest version [1]%s[/1] of this module. Last check %s" => $this->l('You have latest version [1]%s[/1] of this module. Last check %s'),
            "You need to select trigger first" => $this->l('You need to select trigger first'),
            "You need to test this sql" => $this->l('You need to test this sql'),
            "Your license is valid for domain [1]%s[/1] and all its subdomains" => $this->l('Your license is valid for domain [1]%s[/1] and all its subdomains'),
            "Your license is valid for domain [1]%s[/1] and all its subdomains, and will expire [2]%s[/2]" => $this->l('Your license is valid for domain [1]%s[/1] and all its subdomains, and will expire [2]%s[/2]'),
            "[1]Free version[/1] of this module allows you to create [2]%s %s[/2]" => $this->l('[1]Free version[/1] of this module allows you to create [2]%s %s[/2]'),
            "created by community" => $this->l('created by community'),
            "learn how to utilize this module to its full potential" => $this->l('learn how to utilize this module to its full potential'),
            "measures" => $this->l('measures'),
            "package" => $this->l('package'),
            "rules" => $this->l('rules'),
            "sql does not contains FROM cause" => $this->l('sql does not contains FROM cause'),
            "sql must start with SELECT statement" => $this->l('sql must start with SELECT statement'),
            "we can send you discount offers or deals" => $this->l('we can send you discount offers or deals'),
            "we will let you know when new version is available" => $this->l('we will let you know when new version is available')
        ));
    }

    /**
     * @param stirng $str
     * @return string
     */
    public function l($str)
    {
        return html_entity_decode($this->module->l($str, 'app-translation'));
    }

    /**
     * @param $array
     * @return array
     */
    private function changed($array)
    {
        $ret = array();
        foreach ($array as $key => $value) {
            if ($value != $key) {
                $ret[$key] = $value;
            }
        }
        return $ret;
    }
}
