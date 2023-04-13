<?php
/**
* Copyright (C) 2017-2019 Petr Hucik <petr@getdatakick.com>
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@getdatakick.com so we can send you a copy immediately.
*
* @author    Petr Hucik <petr@getdatakick.com>
* @copyright 2017-2019 Petr Hucik
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

namespace Revws;

class AppTranslation {
  private $module;

  public function __construct($module) {
    $this->module = $module;
  }

  public function getFrontTranslations() {
    return $this->changed(array(
       "Are you sure you want to delete this review?" => $this->l('Are you sure you want to delete this review?'),
       "Attach images" => $this->l('Attach images'),
       "Be the first to write a review!" => $this->l('Be the first to write a review!'),
       "By submitting this review you agree to use of your data as outlined in our privacy policy" => $this->l('By submitting this review you agree to use of your data as outlined in our privacy policy'),
       "Cancel" => $this->l('Cancel'),
       "Click here to reply" => $this->l('Click here to reply'),
       "Close" => $this->l('Close'),
       "Could you review these products?" => $this->l('Could you review these products?'),
       "Create review" => $this->l('Create review'),
       "Customer didn't write any details" => $this->l('Customer didn\'t write any details'),
       "Delete review" => $this->l('Delete review'),
       "Edit review" => $this->l('Edit review'),
       "Failed to create review" => $this->l('Failed to create review'),
       "Failed to delete review" => $this->l('Failed to delete review'),
       "Failed to load reviews" => $this->l('Failed to load reviews'),
       "Failed to update review" => $this->l('Failed to update review'),
       "Failed to upload file: %s" => $this->l('Failed to upload file: %s'),
       "Invalid number" => $this->l('Invalid number'),
       "No customer reviews for the moment." => $this->l('No customer reviews for the moment.'),
       "No" => $this->l('No'),
       "Please enter review details" => $this->l('Please enter review details'),
       "Please enter review title" => $this->l('Please enter review title'),
       "Please enter your email address" => $this->l('Please enter your email address'),
       "Please enter your name" => $this->l('Please enter your name'),
       "Please provide valid email address" => $this->l('Please provide valid email address'),
       "Please provide your name" => $this->l('Please provide your name'),
       "Please review %s" => $this->l('Please review %s'),
       "Reply from %s:" => $this->l('Reply from %s:'),
       "Report abuse" => $this->l('Report abuse'),
       "Review content must be set" => $this->l('Review content must be set'),
       "Review deleted" => $this->l('Review deleted'),
       "Review details" => $this->l('Review details'),
       "Review has been created" => $this->l('Review has been created'),
       "Review has been updated" => $this->l('Review has been updated'),
       "Review title must be set" => $this->l('Review title must be set'),
       "Review title" => $this->l('Review title'),
       "Save" => $this->l('Save'),
       "Sign in to write a review" => $this->l('Sign in to write a review'),
       "Thank you for reporting this review" => $this->l('Thank you for reporting this review'),
       "Thank you for your vote!" => $this->l('Thank you for your vote!'),
       "This review hasn't been approved yet" => $this->l('This review hasn\'t been approved yet'),
       "Update review" => $this->l('Update review'),
       "Verified purchase" => $this->l('Verified purchase'),
       "Was this comment useful to you?" => $this->l('Was this comment useful to you?'),
       "Write your answer" => $this->l('Write your answer'),
       "Write your review!" => $this->l('Write your review!'),
       "Yes" => $this->l('Yes'),
       "You haven't written any review yet" => $this->l('You haven\'t written any review yet'),
       "Your answer" => $this->l('Your answer'),
       "Your email address" => $this->l('Your email address'),
       "Your name" => $this->l('Your name'),
       "Your reviews" => $this->l('Your reviews'),
       "by" => $this->l('by'),
       "Advantages" => $this->l('Advantages'),
       "Disadvantages" => $this->l('Disadvantages'),
       "Please enter advantages" => $this->l('Please enter advantages'),
       "Please enter disadvantages" => $this->l('Please enter disadvantages'),
       "Review advantage must be set" => $this->l('Review advantage must be set'),
       "Review disadvantages must be set" => $this->l('Review disadvantages must be set')
    ));
  }

  public function getBackTranslations() {
    return $this->changed(array(
      "%s reviews has been imported" => $this->l('%s reviews has been imported'),
      "%s reviews out of %s has been imported. See javascript console for more info!" => $this->l('%s reviews out of %s has been imported. See javascript console for more info!'),
      "Actions" => $this->l('Actions'),
      "Active" => $this->l('Active'),
      "Admin notifications" => $this->l('Admin notifications'),
      "All languages" => $this->l('All languages'),
      "All reviews must be approved" => $this->l('All reviews must be approved'),
      "All reviews page" => $this->l('All reviews page'),
      "All reviews" => $this->l('All reviews'),
      "All" => $this->l('All'),
      "Allow reviews by annonymous visitors" => $this->l('Allow reviews by annonymous visitors'),
      "Allow reviews for products without review criteria" => $this->l('Allow reviews for products without review criteria'),
      "Allow reviews with images" => $this->l('Allow reviews with images'),
      "Allow reviews without details" => $this->l('Allow reviews without details'),
      "Allow reviews without title" => $this->l('Allow reviews without title'),
      "Applies to %s categories and %s products" => $this->l('Applies to %s categories and %s products'),
      "Applies to %s products" => $this->l('Applies to %s products'),
      "Applies to entire catalog" => $this->l('Applies to entire catalog'),
      "Applies to product from %s categories" => $this->l('Applies to product from %s categories'),
      "Applies to your entire catalog" => $this->l('Applies to your entire catalog'),
      "Approval status" => $this->l('Approval status'),
      "Approve" => $this->l('Approve'),
      "Approved" => $this->l('Approved'),
      "Are you sure you want to delete this criterion?" => $this->l('Are you sure you want to delete this criterion?'),
      "Are you sure?" => $this->l('Are you sure?'),
      "Author" => $this->l('Author'),
      "Basic colors" => $this->l('Basic colors'),
      "Border color - empty shape" => $this->l('Border color - empty shape'),
      "Border color - filled shape" => $this->l('Border color - filled shape'),
      "Bug reporting" => $this->l('Bug reporting'),
      "Cancel" => $this->l('Cancel'),
      "Categories" => $this->l('Categories'),
      "Changes in new version:" => $this->l('Changes in new version:'),
      "Check for updates" => $this->l('Check for updates'),
      "Checking for new version of this module" => $this->l('Checking for new version of this module'),
      "Choose rating style" => $this->l('Choose rating style'),
      "Click here to reply" => $this->l('Click here to reply'),
      "Close" => $this->l('Close'),
      "Contact permission" => $this->l('Contact permission'),
      "Contact" => $this->l('Contact'),
      "Create new review criterion" => $this->l('Create new review criterion'),
      "Create review" => $this->l('Create review'),
      "Criteria" => $this->l('Criteria'),
      "Criterion deleted" => $this->l('Criterion deleted'),
      "Criterion saved" => $this->l('Criterion saved'),
      "Custom placement" => $this->l('Custom placement'),
      "Custom" => $this->l('Custom'),
      "Customer account page" => $this->l('Customer account page'),
      "Customer didn't write any details" => $this->l('Customer didn\'t write any details'),
      "Customer notifications" => $this->l('Customer notifications'),
      "Customer" => $this->l('Customer'),
      "Customers can upload new images" => $this->l('Customers can upload new images'),
      "Data has been imported" => $this->l('Data has been imported'),
      "Date" => $this->l('Date'),
      "Default customer name format" => $this->l('Default customer name format'),
      "Delete criterion" => $this->l('Delete criterion'),
      "Delete permanently" => $this->l('Delete permanently'),
      "Delete review" => $this->l('Delete review'),
      "Delete status" => $this->l('Delete status'),
      "Delete" => $this->l('Delete'),
      "Deleted" => $this->l('Deleted'),
      "Detected problems" => $this->l('Detected problems'),
      "Disabled criterion" => $this->l('Disabled criterion'),
      "Display multiple criteria" => $this->l('Display multiple criteria'),
      "Display review average in" => $this->l('Display review average in'),
      "Display reviews in" => $this->l('Display reviews in'),
      "Does not apply to any product" => $this->l('Does not apply to any product'),
      "Don't render ratings" => $this->l('Don\'t render ratings'),
      "Don't show criteria" => $this->l('Don\'t show criteria'),
      "Don't show review average" => $this->l('Don\'t show review average'),
      "Download all your reviews data as XML file" => $this->l('Download all your reviews data as XML file'),
      "Download latest version" => $this->l('Download latest version'),
      "Edit criterion" => $this->l('Edit criterion'),
      "Edit review" => $this->l('Edit review'),
      "Edit" => $this->l('Edit'),
      "Email address for notifications" => $this->l('Email address for notifications'),
      "Email address" => $this->l('Email address'),
      "Email language" => $this->l('Email language'),
      "Emit microdata / rich snippets" => $this->l('Emit microdata / rich snippets'),
      "Empty stars will be rendered if product hasn't been reviewed yet" => $this->l('Empty stars will be rendered if product hasn\'t been reviewed yet'),
      "Export data" => $this->l('Export data'),
      "Export reviews" => $this->l('Export reviews'),
      "Failed to approve review" => $this->l('Failed to approve review'),
      "Failed to delete criterion" => $this->l('Failed to delete criterion'),
      "Failed to delete review" => $this->l('Failed to delete review'),
      "Failed to export reviews" => $this->l('Failed to export reviews'),
      "Failed to import reviews: %s" => $this->l('Failed to import reviews: %s'),
      "Failed to load data" => $this->l('Failed to load data'),
      "Failed to migrate data" => $this->l('Failed to migrate data'),
      "Failed to save criterion" => $this->l('Failed to save criterion'),
      "Failed to save review" => $this->l('Failed to save review'),
      "Failed to undelete review" => $this->l('Failed to undelete review'),
      "Failed to update settings" => $this->l('Failed to update settings'),
      "Fill color - empty shape" => $this->l('Fill color - empty shape'),
      "Fill color - filled shape" => $this->l('Fill color - filled shape'),
      "Filter list" => $this->l('Filter list'),
      "Filter reviews by current language" => $this->l('Filter reviews by current language'),
      "GDPR" => $this->l('GDPR'),
      "General settings" => $this->l('General settings'),
      "Guest visitor" => $this->l('Guest visitor'),
      "Help and support" => $this->l('Help and support'),
      "Hide review section when is empty" => $this->l('Hide review section when is empty'),
      "How do you like this module?" => $this->l('How do you like this module?'),
      "I understand" => $this->l('I understand'),
      "ID" => $this->l('ID'),
      "Icon css class" => $this->l('Icon css class'),
      "Images" => $this->l('Images'),
      "Import and export reviews" => $this->l('Import and export reviews'),
      "Import data" => $this->l('Import data'),
      "Import review data and criteria settings from other modules" => $this->l('Import review data and criteria settings from other modules'),
      "Integrations" => $this->l('Integrations'),
      "Invalid number" => $this->l('Invalid number'),
      "Label" => $this->l('Label'),
      "Licensing information" => $this->l('Licensing information'),
      "Loading..." => $this->l('Loading...'),
      "Manage review criteria" => $this->l('Manage review criteria'),
      "Marketing offers" => $this->l('Marketing offers'),
      "Max image file size [MB]" => $this->l('Max image file size [MB]'),
      "Max review requests" => $this->l('Max review requests'),
      "Migrate data" => $this->l('Migrate data'),
      "Migrate reviews and criteria from product comments module" => $this->l('Migrate reviews and criteria from product comments module'),
      "Migrate reviews from CSV file generated by yotpo" => $this->l('Migrate reviews from CSV file generated by yotpo'),
      "Moderation" => $this->l('Moderation'),
      "New module version is available" => $this->l('New module version is available'),
      "New release notification" => $this->l('New release notification'),
      "New version [1]%s[/1] is available. Last check %s" => $this->l('New version [1]%s[/1] is available. Last check %s'),
      "No information available. Please click on [1]Check for updates[/1] button to check for new version of this module" => $this->l('No information available. Please click on [1]Check for updates[/1] button to check for new version of this module'),
      "No ratings markup will be rendered. Product blocks with and without ratings can have different height!" => $this->l('No ratings markup will be rendered. Product blocks with and without ratings can have different height!'),
      "No reviews" => $this->l('No reviews'),
      "No" => $this->l('No'),
      "Not deleted" => $this->l('Not deleted'),
      "Nothing found" => $this->l('Nothing found'),
      "Nothing to approve" => $this->l('Nothing to approve'),
      "Notify customer when" => $this->l('Notify customer when'),
      "Official prestashop GDPR module" => $this->l('Official prestashop GDPR module'),
      "Order reviews by" => $this->l('Order reviews by'),
      "Please choose what type of [1]emails[/1] you would like to receive from us. You can always [2]change[/2] your preferences later" => $this->l('Please choose what type of [1]emails[/1] you would like to receive from us. You can always [2]change[/2] your preferences later'),
      "Please enter customer name" => $this->l('Please enter customer name'),
      "Please enter review details" => $this->l('Please enter review details'),
      "Please enter review title" => $this->l('Please enter review title'),
      "Please enter reviewer email" => $this->l('Please enter reviewer email'),
      "Please enter your contact email address" => $this->l('Please enter your contact email address'),
      "Please provide valid email address" => $this->l('Please provide valid email address'),
      "Please provide your name" => $this->l('Please provide your name'),
      "Preview" => $this->l('Preview'),
      "Product buttons" => $this->l('Product buttons'),
      "Product comparison page" => $this->l('Product comparison page'),
      "Product detail page" => $this->l('Product detail page'),
      "Product listing page" => $this->l('Product listing page'),
      "Products criteria" => $this->l('Products criteria'),
      "Products" => $this->l('Products'),
      "Quality" => $this->l('Quality'),
      "Rating shape size" => $this->l('Rating shape size'),
      "Ratings markup will be rendered, but it will be transparent. This is useful to align height of your product blocks" => $this->l('Ratings markup will be rendered, but it will be transparent. This is useful to align height of your product blocks'),
      "Ratings" => $this->l('Ratings'),
      "Render empty ratings" => $this->l('Render empty ratings'),
      "Render ratings, but make it invisible" => $this->l('Render ratings, but make it invisible'),
      "Reply from %s:" => $this->l('Reply from %s:'),
      "Report abuse" => $this->l('Report abuse'),
      "Resize image - max height" => $this->l('Resize image - max height'),
      "Resize image - max width" => $this->l('Resize image - max width'),
      "Review content must be set" => $this->l('Review content must be set'),
      "Review content" => $this->l('Review content'),
      "Review details" => $this->l('Review details'),
      "Review has been activated" => $this->l('Review has been activated'),
      "Review has been approved" => $this->l('Review has been approved'),
      "Review has been created" => $this->l('Review has been created'),
      "Review has been deleted" => $this->l('Review has been deleted'),
      "Review has been marked as deleted" => $this->l('Review has been marked as deleted'),
      "Review language" => $this->l('Review language'),
      "Review moderation" => $this->l('Review moderation'),
      "Review saved" => $this->l('Review saved'),
      "Review title must be set" => $this->l('Review title must be set'),
      "Review title" => $this->l('Review title'),
      "Review type" => $this->l('Review type'),
      "Reviewer email" => $this->l('Reviewer email'),
      "Reviewer name" => $this->l('Reviewer name'),
      "Reviews has been exported" => $this->l('Reviews has been exported'),
      "Reviews per page" => $this->l('Reviews per page'),
      "Reviews" => $this->l('Reviews'),
      "Right column" => $this->l('Right column'),
      "Save changes" => $this->l('Save changes'),
      "Save" => $this->l('Save'),
      "Search %s" => $this->l('Search %s'),
      "Search customers" => $this->l('Search customers'),
      "Search" => $this->l('Search'),
      "Select %s" => $this->l('Select %s'),
      "Select customer" => $this->l('Select customer'),
      "Select review type" => $this->l('Select review type'),
      "Send email when" => $this->l('Send email when'),
      "Send thank you email" => $this->l('Send thank you email'),
      "Separate block" => $this->l('Separate block'),
      "Settings successfully saved" => $this->l('Settings successfully saved'),
      "Settings" => $this->l('Settings'),
      "Shipping" => $this->l('Shipping'),
      "Shortcuts" => $this->l('Shortcuts'),
      "Show average ratings on product comparison page" => $this->l('Show average ratings on product comparison page'),
      "Show average ratings on product listing page" => $this->l('Show average ratings on product listing page'),
      "Show criteria on side" => $this->l('Show criteria on side'),
      "Show criteria on top" => $this->l('Show criteria on top'),
      "Show review list" => $this->l('Show review list'),
      "Show review section in customer account" => $this->l('Show review section in customer account'),
      "Show sign in button for annonymous visitors" => $this->l('Show sign in button for annonymous visitors'),
      "Simple consent" => $this->l('Simple consent'),
      "Sort" => $this->l('Sort'),
      "Start using module" => $this->l('Start using module'),
      "Structured Data / Rich Snippets" => $this->l('Structured Data / Rich Snippets'),
      "Submit reviews without consent" => $this->l('Submit reviews without consent'),
      "Support" => $this->l('Support'),
      "Tab" => $this->l('Tab'),
      "Thank you for installing free version of [1]Revws[/1] module. We are very happy to have you aboard." => $this->l('Thank you for installing free version of [1]Revws[/1] module. We are very happy to have you aboard.'),
      "Thank you for using this [1]free[/1] module. We really hope it helped you increase trust, and sell more products." => $this->l('Thank you for using this [1]free[/1] module. We really hope it helped you increase trust, and sell more products.'),
      "Thank you for your review" => $this->l('Thank you for your review'),
      "Theme and Appearance" => $this->l('Theme and Appearance'),
      "This action will delete all your curent reviews and criteria settings!" => $this->l('This action will delete all your curent reviews and criteria settings!'),
      "This is an [1]open source[/1] project released under the [2]AFL 3.0[/2] license. That means you are free to use, modify, and copy this software in any way you wish." => $this->l('This is an [1]open source[/1] project released under the [2]AFL 3.0[/2] license. That means you are free to use, modify, and copy this software in any way you wish.'),
      "This review hasn't been approved yet" => $this->l('This review hasn\'t been approved yet'),
      "Thubmnail width" => $this->l('Thubmnail width'),
      "Thumbnail height" => $this->l('Thumbnail height'),
      "Transparent" => $this->l('Transparent'),
      "Tutorials and suggestions" => $this->l('Tutorials and suggestions'),
      "Type" => $this->l('Type'),
      "Unapproved reviews" => $this->l('Unapproved reviews'),
      "Unapproved" => $this->l('Unapproved'),
      "Undelete" => $this->l('Undelete'),
      "Update module" => $this->l('Update module'),
      "Upgrade now" => $this->l('Upgrade now'),
      "Upgrade to premium" => $this->l('Upgrade to premium'),
      "Use transparent" => $this->l('Use transparent'),
      "Verified buyer" => $this->l('Verified buyer'),
      "Verified purchase" => $this->l('Verified purchase'),
      "Visitor can review the same product more then once" => $this->l('Visitor can review the same product more then once'),
      "Visitors can delete their reviews" => $this->l('Visitors can delete their reviews'),
      "Visitors can edit their reviews" => $this->l('Visitors can edit their reviews'),
      "Visitors can mark reviews as useful" => $this->l('Visitors can mark reviews as useful'),
      "Visitors can report abusive, fake, or incorrect reviews" => $this->l('Visitors can report abusive, fake, or incorrect reviews'),
      "Votes" => $this->l('Votes'),
      "Was this comment useful to you?" => $this->l('Was this comment useful to you?'),
      "We have detected following problems. This module might not work correctly unless they are fixed" => $this->l('We have detected following problems. This module might not work correctly unless they are fixed'),
      "We need your permission to contact you in [1]emergency[/1] situations, for example if we discover a serious [2]security[/2] bug" => $this->l('We need your permission to contact you in [1]emergency[/1] situations, for example if we discover a serious [2]security[/2] bug'),
      "We promise that we will [1]not disclose[/1] your email address to anyone or use it to [2]spam[/2] you. We will not send you automated [3]marketing[/3] emails unless you [4]opt-in[/4] for it.[5][/5]You can read our [6]privacy policy[/6] here." => $this->l('We promise that we will [1]not disclose[/1] your email address to anyone or use it to [2]spam[/2] you. We will not send you automated [3]marketing[/3] emails unless you [4]opt-in[/4] for it.[5][/5]You can read our [6]privacy policy[/6] here.'),
      "We would like to ask you a [1]favor[/1]. If you like this module, could you please write [2]short review[/2] on our store? Thank you in advance!" => $this->l('We would like to ask you a [1]favor[/1]. If you like this module, could you please write [2]short review[/2] on our store? Thank you in advance!'),
      "What emails can we send you?" => $this->l('What emails can we send you?'),
      "What to approve" => $this->l('What to approve'),
      "When no review exists for product" => $this->l('When no review exists for product'),
      "Write review" => $this->l('Write review'),
      "Write your answer" => $this->l('Write your answer'),
      "Yes" => $this->l('Yes'),
      "You can contact me" => $this->l('You can contact me'),
      "You have latest version [1]%s[/1] of this module. Last check %s" => $this->l('You have latest version [1]%s[/1] of this module. Last check %s'),
      "Your answer" => $this->l('Your answer'),
      "consent is required even for logged-in customers" => $this->l('consent is required even for logged-in customers'),
      "edit your theme's product template and insert this code anywhere you want to display review average" => $this->l('edit your theme\'s product template and insert this code anywhere you want to display review average'),
      "edits of already approved review must be validated" => $this->l('edits of already approved review must be validated'),
      "employee approves review" => $this->l('employee approves review'),
      "employee deletes review" => $this->l('employee deletes review'),
      "employee rejects review" => $this->l('employee rejects review'),
      "employee replies to review" => $this->l('employee replies to review'),
      "learn how to utilize this module to its full potential" => $this->l('learn how to utilize this module to its full potential'),
      "new reviews must be approved" => $this->l('new reviews must be approved'),
      "please create javascript function named 'revwsFormatName' and include it to product page. For example" => $this->l('please create javascript function named \'revwsFormatName\' and include it to product page. For example'),
      "review author deletes review" => $this->l('review author deletes review'),
      "review author updates review" => $this->l('review author updates review'),
      "review needs approval" => $this->l('review needs approval'),
      "reviews reported as abusive must be re-approved" => $this->l('reviews reported as abusive must be re-approved'),
      "visitor creates new review" => $this->l('visitor creates new review'),
      "we can send you discount offers or deals" => $this->l('we can send you discount offers or deals'),
      "we will let you know when new version is available" => $this->l('we will let you know when new version is available'),
      "Advantages" => $this->l('Advantages'),
      "Disadvantages" => $this->l('Disadvantages'),
      "Please enter advantages" => $this->l('Please enter advantages'),
      "Please enter disadvantages" => $this->l('Please enter disadvantages'),
      "Review advantage must be set" => $this->l('Review advantage must be set'),
      "Review disadvantages must be set" => $this->l('Review disadvantages must be set')
    ));
  }

  private function l($str) {
    return html_entity_decode($this->module->l($str, 'app-translation'));
  }

  private function changed($array) {
    $ret = array();
    foreach ($array as $key => $value) {
      if ($value != $key) {
        $ret[$key] = $value;
      }
    }
    return $ret;
  }
}