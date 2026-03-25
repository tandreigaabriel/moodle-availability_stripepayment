This is an example from Moodle documentation about the moodle plugin structure read it is for 4.5 

Availability conditions
Availability conditions allow teachers to restrict an activity or section so that only certain users can access it. These are accessed using the Availability API.

Some of the conditions included with Moodle are:

Date - users can only access activity after specified date
Grade - users can only access activity if they have a certain grade in another activity
A relatively simple example is the grouping condition which can be found in /availability/condition/grouping. It is a good basis for a new plugin when starting to implement a new condition.

To see this condition in action:

Go to a course and edit any section
Expand the Restrict access heading
Click the Add restriction button
Click Grouping
File structure
All availability condition plugin files must be located inside the /availability/condition/pluginname folder.

View an example directory layout for the availability_grouping plugin.
 availability/condition/grouping
├── classes
│   ├── condition.php
│   ├── frontend.php
├── lang
│   └── en
│       └── availability_grouping.php
├── version.php
└── yui
    ├── build
    │   └── moodle-availability_grouping-form
    │       ├── moodle-availability_grouping-form-debug.js
    │       ├── moodle-availability_grouping-form-min.js
    │       └── moodle-availability_grouping-form.js
    └── src
        └── form
            ├── build.json
            ├── js
            │   └── form.js
            └── meta
                └── form.json

Some of the important files for the format plugintype are described below. See the common plugin files documentation for details of other files which may be useful in your plugin.

lang/en/availability_name.php
Language files
Refreshed on cache purge
Required
File path: /lang/en/plugintype_pluginname.php
Each plugin must define a set of language strings with, at a minimum, an English translation. These are specified in the plugin's lang/en directory in a file named after the plugin. For example the LDAP authentication plugin:

Language strings for the plugin. Required strings:

pluginname - name of plugin.
title - text of button for adding this type of plugin.
description - explanatory text that goes alongside the button in the 'add restriction' dialog.
You will usually need to add your own strings for two main purposes:

Creating suitable form controls for users who are editing the activity settings.
Displaying information about the condition.
View example
public/availability/condition/pluginname/lang/en/plugintype_pluginname.php
<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Languages configuration for the availability_pluginname plugin.
 *
 * @package   availability_pluginname
 * @copyright Year, You Name <your@email.address>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['description'] = 'Allow only students who belong to a group within a specified grouping.';
$string['pluginname'] = 'Restriction by grouping';
$string['title'] = 'Grouping';

classes/condition.php
This PHP class implements the back-end of the condition; in other words, this class contains the code which decides whether a user is allowed to access an activity that uses this condition, or not.

Here's an outline of the code (with standard PHPdoc comments omitted to save space) for a simple example in which there is a boolean value that controls whether access is allowed or not.

// You must use the right namespace (matching your plugin component name).
namespace availability_name;

class condition extends \core_availability\condition {
    // Any data associated with the condition can be stored in member
    // variables. Here's an example variable:
    protected $allow;

    public function __construct($structure) {
        // Retrieve any necessary data from the $structure here. The
        // structure is extracted from JSON data stored in the database
        // as part of the tree structure of conditions relating to an
        // activity or section.
        // For example, you could obtain the 'allow' value:
        $this->allow = $structure->allow;

        // It is also a good idea to check for invalid values here and
        // throw a coding_exception if the structure is wrong.
    }

    public function save() {
        // Save back the data into a plain array similar to $structure above.
        return (object)array('type' => 'name', 'allow' => $this->allow);
    }

    public function is_available(
        $not,
        \core_availability\info $info,
        $grabthelot,
        $userid
    ) {
        // This function needs to check whether the condition is available
        // or not for the user specified in $userid.

        // The value $not should be used to negate the condition. Other
        // parameters provide data which can be used when evaluating the
        // condition.

        // For this trivial example, we will just use $allow to decide
        // whether it is allowed or not. In a real condition you would
        // do some calculation depending on the specified user.
        $allow = $this->allow;
        if ($not) {
            $allow = !$allow;
        }
        return $allow;
    }

    public function get_description(
        $full,
        $not,
        \core_availability\info $info
    ) {
        // This function returns the information shown about the
        // condition on editing screens.
        // Usually it is similar to the information shown if the
        // user doesn't meet the condition.
        // Note: it does not depend on the current user.
        $allow = $not ? !$this->allow : $this->allow;
        return $allow ? 'Users are allowed' : 'Users not allowed';
    }

    protected function get_debug_string() {
        // This function is only normally used for unit testing and
        // stuff like that. Just make a short string representation
        // of the values of the condition, suitable for developers.
        return $this->allow ? 'YES' : 'NO';
    }
}

There are other functions you might also want to implement. For example, if your condition should apply to lists of users (in general, conditions which are 'permanent' such as group conditions apply to lists, whereas those which are 'temporary' such as date or grade conditions do not) then you should also implement is_applied_to_user_lists and filter_user_list functions. To see the full list, look at the PHPdoc for the condition and tree_node classes inside availability/classes.

classes/frontend.php
You will also need to write a frontend.php class which defines the behaviour of your plugin within the editing form (when a teacher is editing the activity settings).

The class is required, but all the functions are theoretically optional; you can leave them out if you don't need any special behaviour for that function. In practice it's likely you will need at least one of them.

namespace availability_name;

class frontend extends \core_availability\frontend {

    protected function get_javascript_strings() {
        // You can return a list of names within your language file and the
        // system will include them here.
        // Should you need strings from another language file, you can also
        // call $PAGE->requires->strings_for_js manually from here.)
        return [];
    }

    protected function get_javascript_init_params(
        $course,
        \cm_info $cm = null,
        \section_info $section = null
    ) {
        // If you want, you can add some parameters here which will be
        // passed into your JavaScript init method. If you don't include
        // this function, there will be no parameters.
        return ['frog'];
    }

    protected function allow_add(
        $course,
        \cm_info $cm = null,
        \section_info $section = null
    ) {
        // This function lets you control whether the 'add' button for your
        // plugin appears. For example, the grouping plugin does not appear
        // if there are no groupings on the course. This helps to simplify
        // the user interface. If you don't include this function, it will
        // appear.
        return true;
    }
}

YUI
The Availability API generates a dialogue to allow teachers to configure the availability conditions. Each availability plugin can add to this form by writing a JavaScript module in the YUI format which generates its form fields, errors, and configuration.

note
Although JavaScript standards in Moodle have moved on, the core availability system is implemented in YUI, so for now, the plugins need to use YUI too. (Please, someone, do MDL-69566!)

YUI does require more boilerplate configuration that AMD modules, but the same build toolset is used as for AMD modules and you can still make use of the grunt watch command.

yui/src/form/meta/form.json
The metadata file lists any dependencies that your YUI module has on other code.

Typically this will include the moodle-core_availability-form dependency, and possibly some other YUI dependencies.

availability/condition/example/yui/src/form/meta/form.json
{
  "moodle-availability_name-form": {
    "requires": [
        "base",
        "node",
        "event",
        "moodle-core_availability-form"
    ]
  }
}

yui/src/form/build.json
The build.json file describes how the YUI compiler will build your YUI module.

YUI modules can be broken down into smaller, succint, pieces of code. This is very useful for larger modules, but rarely necessary in smaller code.

Typically you should only need to set the name of your plugin in this file>

availability/condition/example/yui/src/form/build.json
{
  "name": "moodle-availability_name-form",
  "builds": {
    "moodle-availability_name-form": {
      "jsfiles": [
        "form.js"
      ]
    }
  }
}

yui/src/js/form.js
This file contains the actual JavaScript code for your plugin. It should follow the below format in order to integrate with the core JavaScript. Additional feautres are available and you can add any extra functions you like to break your code down too.

availability/condition/example/yui/src/js/form.js
M.availability_name = M.availability_name || {};

M.availability_name.form = Y.Object(M.core_availability.plugin);

M.availability_name.form.initInner = function(param) {
    // The 'param' variable is the parameter passed through from PHP (you
    // can have more than one if required).

    // Using the PHP code above it'll show 'The param was: frog'.
    console.log('The param was: ' + param);
};

M.availability_name.form.getNode = function(json) {
    // This function does the main work. It gets called after the user
    // chooses to add an availability restriction of this type. You have
    // to return a YUI node representing the HTML for the plugin controls.

    // Example controls contain only one tickbox.
    var html = '<label>'
        + M.get_string('title', 'availability_name')
        + ' <input type="checkbox"/></label>';
    var node = Y.Node.create('<span>' + html + '</span>');

    // Set initial values based on the value from the JSON data in Moodle
    // database. This will have values undefined if creating a new one.
    if (json.allow) {
        node.one('input').set('checked', true);
    }

    // Add event handlers (first time only). You can do this any way you
    // like, but this pattern is used by the existing code.
    if (!M.availability_name.form.addedEvents) {
        M.availability_name.form.addedEvents = true;
        var root = Y.one('#fitem_id_availabilityconditionsjson');
        root.delegate('click', function() {
            // The key point is this update call. This call will update
            // the JSON data in the hidden field in the form, so that it
            // includes the new value of the checkbox.
            M.core_availability.form.update();
        }, '.availability_name input');
    }

    return node;
};

M.availability_name.form.fillValue = function(value, node) {
    // This function gets passed the node (from above) and a value
    // object. Within that object, it must set up the correct values
    // to use within the JSON data in the form. Should be compatible
    // with the structure used in the __construct and save functions
    // within condition.php.
    var checkbox = node.one('input');
    value.allow = checkbox.get('checked') ? true : false;
};

M.availability_name.form.fillErrors = function(errors, node) {
    // If the user has selected something invalid, this optional
    // function can be included to report an error in the form. The
    // error will show immediately as a 'Please set' tag, and if the
    // user saves the form with an error still in place, they'll see
    // the actual error text.

    // In this example an error is not possible...
    if (false) {
        // ...but this is how you would add one if required. This is
        // passing your component name (availability_name) and the
        // name of a string within your lang file (error_message)
        // which will be shown if they submit the form.
        errors.push('availability_name:error_message');
    }
};
Availability API
The availability API controls access to activities and sections. For example, a teacher could restrict access so that an activity cannot be accessed until a certain date, or so that a section cannot be accessed unless users have a certain grade in a quiz.

note
In older versions of Moodle, the conditional availability system defaulted to off; and users could enable it from the advanced features page in site administration. It is enabled by default for new installs of Moodle since Moodle 3.1, but sites which have been upgraded may still have to manually enable it.

You can still call the API functions even if the system is turned off.

Using the API
In most cases you do not need to use the API directly because the course and activity API already handles it for you. For example, if you are writing a module:

Moodle will automatically prevent users from accessing your module if they do not meet the availability conditions (unless they have the ability to access hidden activities). This is calculated when you call require_login and pass your activity's information.
Your activity's form will automatically include controls for setting availability restriction, as part of the standard form controls.
There are two special cases in which you may need to use the API directly.

Checking whether the current user can access an activity
Displaying a list of users who may be able to access the current activity
Check access for a user
Activities
Some availability information can be accessed from an instance of the cm_info class, specifically in the uservisible property.

This property considers a range of factors to indicate whether the activity should be visible to that user, including:

whether the activity was hidden by a teacher
whether the activity is available based on the availability API
The cm_info object also includes an availableinfo property which provides HTML-formatted information to explain to the user why they cannot access the activity.

Checking and displaying availability information
$modinfo = get_fast_modinfo($course);
$cm = $modinfo->get_cm($cmid);
if ($cm->uservisible) {
    // User can access the activity.
} else if ($cm->availableinfo) {
    // User cannot access the activity, but on the course page they will
    // see a link to it, greyed-out, with information (HTML format) from
    // $cm->availableinfo about why they can't access it.
} else {
    // User cannot access the activity and they will not see it at all.
}

Course sections
Some availability information can be accessed from an instance of the section_info class, specifically in the uservisible property.

This property considers a range of factors to indicate whether the activity should be visible to that user, including:

whether the activity was hidden by a teacher
whether the activity is available based on the availability API
The section_info object also includes an availableinfo property which provides HTML-formatted information to explain to the user why they cannot access the activity.

Checking sections and activities
The uservisible check for an activity automatically includes all relevant checks for the section that the activity is placed in.

You do not need to check visibility and availability for both the section and the activity.

Accessing information for a different user
The availability information in both the cm_info and section_info classes is calculated for the current user. You can also obtain them for a different user by passing a user ID to get_fast_modinfo, although be aware that doing this repeatedly for different users will be slow.

Display a list of users who may be able to access the current activity
Sometimes you need to display a list of users who may be able to access the current activity.

While you could use the above approach for each user, this would be slow and also is generally not what you require. For example, if you have an activity such as an assignment which is set to be available to students until a certain date, and if you want to display a list of potential users within that activity, you probably don't want to make the list blank immediately the date occurs.

The system divides availability conditions into two types:

Applied to user lists, including:
User group
User grouping
User profile conditions
Not applied to user lists, including:
The current date
completion
grade
In general, the conditions which we expect are likely to change over time (such as dates) or as a result of user actions (such as grades) are not applied to user lists.

If you have a list of users (for example you could obtain this using one of the 'get enrolled users' functions), you can filter it to include only those users who are allowed to see an activity with this code:

$info = new \core_availability\info_module($cm);
$filtered = $info->filter_user_list($users);

note
The above example does not include the $cm->visible setting, nor does it take into account the viewhiddenactivities setting.

Using availability conditions in other areas
The availability API is provided for activities (course-modules) and sections. It is also possible to use it in other areas such as within a module. See Availability API for items within a module.

Programmatically setting availability conditions
In some situations you may need to programmatically configure the availability conditions for an activity - for example you may have a custom enrolment plugin which creates assessable activities according to a student information system.

To configure the availability, you can generate a JSON structure using an instance of the core_availability\tree class, and setting it against the activity or section record in the database, for example:

$restriction = \core_availability\tree::get_root_json([
    \availability_group\condition::get_json($group->id),
]);
$DB->set_field(
    'course_modules',
    'availability',
    json_encode($restriction),
    [
        'id' => $cmid,
    ]
);
rebuild_course_cache($course->id, true);

The following code can be used to programmatically set start and end date restrictions.

use \core_availability\tree;

$dates = [];
$dates[] = \availability_date\condition::get_json(">=", $availability['start']);
$dates[] = \availability_date\condition::get_json("<", $availability['end']);

$showc = [true, true];
$restrictions = tree::get_root_json($dates, tree::OP_AND, $showc);

$DB->set_field(
    'course_modules',
    'availability',
    json_encode($restrictions),
    [
        'id' => $cmid,
    ]
);
rebuild_course_cache($course->id, true);

The $showc array determines if the course modules will be shown or invisible when not available.
PS C:\Users\web2\ci-fix\ci> php vendor/bin/phpcs --standard=moodle C:\laragon\www\moodle\availability\condition\stripepayment\webhook.php

FILE: C:\laragon\www\moodle\availability\condition\stripepayment\webhook.php
----------------------------------------------------------------------------------------------
FOUND 0 ERRORS AND 12 WARNINGS AFFECTING 12 LINES
----------------------------------------------------------------------------------------------
  25 | WARNING | Inline comments must end in full-stops, exclamation marks, or question marks
  58 | WARNING | Inline comments must end in full-stops, exclamation marks, or question marks
  69 | WARNING | Inline comments must end in full-stops, exclamation marks, or question marks
 102 | WARNING | Inline comments must end in full-stops, exclamation marks, or question marks
 108 | WARNING | Inline comments must end in full-stops, exclamation marks, or question marks
 114 | WARNING | Inline comments must end in full-stops, exclamation marks, or question marks
 124 | WARNING | Inline comments must end in full-stops, exclamation marks, or question marks
 143 | WARNING | Inline comments must end in full-stops, exclamation marks, or question marks
 153 | WARNING | Inline comments must end in full-stops, exclamation marks, or question marks
 163 | WARNING | Inline comments must end in full-stops, exclamation marks, or question marks
 174 | WARNING | Inline comments must end in full-stops, exclamation marks, or question marks
 188 | WARNING | Inline comments must end in full-stops, exclamation marks, or question marks
----------------------------------------------------------------------------------------------

Time: 3.36 secs; Memory: 12MB

PS C:\Users\web2\ci-fix\ci> php vendor/bin/phpcs --standard=moodle C:\laragon\www\moodle\availability\condition\stripepayment\activity_report.php

FILE: C:\laragon\www\moodle\availability\condition\stripepayment\activity_report.php
----------------------------------------------------------------------------------------------
FOUND 0 ERRORS AND 7 WARNINGS AFFECTING 6 LINES
----------------------------------------------------------------------------------------------
  42 | WARNING | Inline comments must start with a capital letter, digit or 3-dots sequence
  54 | WARNING | Inline comments must start with a capital letter, digit or 3-dots sequence
  78 | WARNING | Inline comments must start with a capital letter, digit or 3-dots sequence
  83 | WARNING | Line exceeds 132 characters; contains 134 characters
 114 | WARNING | Inline comments must start with a capital letter, digit or 3-dots sequence
 114 | WARNING | Inline comments must end in full-stops, exclamation marks, or question marks
 116 | WARNING | Inline comments must start with a capital letter, digit or 3-dots sequence
----------------------------------------------------------------------------------------------

Time: 3.51 secs; Memory: 12MB

PS C:\Users\web2\ci-fix\ci> php vendor/bin/phpcs --standard=moodle C:\laragon\www\moodle\availability\condition\stripepayment\db\upgrade.php
PS C:\Users\web2\ci-fix\ci> php vendor/bin/phpcs --standard=moodle C:\laragon\www\moodle\availability\condition\stripepayment\settings.php
PS C:\Users\web2\ci-fix\ci> php vendor/bin/phpcs --standard=moodle C:\laragon\www\moodle\availability\condition\stripepayment\payment.php
PS C:\Users\web2\ci-fix\ci> php vendor/bin/phpcs --standard=moodle C:\laragon\www\moodle\availability\condition\stripepayment\transactions.php

FILE: C:\laragon\www\moodle\availability\condition\stripepayment\transactions.php
---------------------------------------------------------------------------------
FOUND 0 ERRORS AND 1 WARNING AFFECTING 1 LINE
---------------------------------------------------------------------------------
 176 | WARNING | Line exceeds 132 characters; contains 139 characters
---------------------------------------------------------------------------------

Time: 3.59 secs; Memory: 12MB

PS C:\Users\web2\ci-fix\ci> php vendor/bin/phpcs --standard=moodle C:\laragon\www\moodle\availability\condition\stripepayment\version.php
PS C:\Users\web2\ci-fix\ci> php vendor/bin/phpcs --standard=moodle C:\laragon\www\moodle\availability\condition\stripepayment\db\access.php
PS C:\Users\web2\ci-fix\ci> php vendor/bin/phpcs --standard=moodle C:\laragon\www\moodle\availability\condition\stripepayment\classes\transactions_table.php


FILE: C:\laragon\www\moodle\availability\condition\stripepayment\classes\transactions_table.php
-----------------------------------------------------------------------------------------------
FOUND 0 ERRORS AND 16 WARNINGS AFFECTING 11 LINES
-----------------------------------------------------------------------------------------------
 170 | WARNING | Line exceeds 132 characters; contains 136 characters
 233 | WARNING | Line exceeds 132 characters; contains 161 characters
 306 | WARNING | Line exceeds 132 characters; contains 136 characters
 314 | WARNING | Line exceeds 132 characters; contains 139 characters
 324 | WARNING | Line exceeds 132 characters; contains 135 characters
 341 | WARNING | Inline comments must start with a capital letter, digit or 3-dots sequence
 341 | WARNING | Inline comments must end in full-stops, exclamation marks, or question marks
 347 | WARNING | Line exceeds 132 characters; contains 137 characters
 367 | WARNING | Inline comments must start with a capital letter, digit or 3-dots sequence
 367 | WARNING | Inline comments must end in full-stops, exclamation marks, or question marks
 368 | WARNING | Inline comments must start with a capital letter, digit or 3-dots sequence
 368 | WARNING | Inline comments must end in full-stops, exclamation marks, or question marks
 369 | WARNING | Inline comments must start with a capital letter, digit or 3-dots sequence
 369 | WARNING | Inline comments must end in full-stops, exclamation marks, or question marks
 370 | WARNING | Inline comments must start with a capital letter, digit or 3-dots sequence
 370 | WARNING | Inline comments must end in full-stops, exclamation marks, or question marks
-----------------------------------------------------------------------------------------------

Time: 3.28 secs; Memory: 14MB

PS C:\Users\web2\ci-fix\ci> php vendor/bin/phpcs --standard=moodle C:\laragon\www\moodle\availability\condition\stripepayment\classes\task\cleanup_pending_payments.php
PS C:\Users\web2\ci-fix\ci> php vendor/bin/phpcs --standard=moodle C:\laragon\www\moodle\availability\condition\stripepayment\classes\condition.php

FILE: C:\laragon\www\moodle\availability\condition\stripepayment\classes\condition.php
----------------------------------------------------------------------------------------------
FOUND 4 ERRORS AND 2 WARNINGS AFFECTING 5 LINES
----------------------------------------------------------------------------------------------
  93 | ERROR   | Missing docblock for function is_available
 110 | ERROR   | Missing docblock for function get_description
 114 | ERROR   | Missing docblock for function get_either_description
 185 | WARNING | Inline comments must start with a capital letter, digit or 3-dots sequence
 185 | WARNING | Inline comments must end in full-stops, exclamation marks, or question marks
 201 | ERROR   | Missing docblock for function get_debug_string
----------------------------------------------------------------------------------------------

Time: 3.22 secs; Memory: 12MB

PS C:\Users\web2\ci-fix\ci> php vendor/bin/phpcs --standard=moodle C:\laragon\www\moodle\availability\condition\stripepayment\classes\privacy\provider.php
PS C:\Users\web2\ci-fix\ci> php vendor/bin/phpcs --standard=moodle C:\laragon\www\moodle\availability\condition\stripepayment\activity_report.php

FILE: C:\laragon\www\moodle\availability\condition\stripepayment\activity_report.php
----------------------------------------------------------------------------------------------
FOUND 0 ERRORS AND 7 WARNINGS AFFECTING 6 LINES
----------------------------------------------------------------------------------------------
  42 | WARNING | Inline comments must start with a capital letter, digit or 3-dots sequence
  54 | WARNING | Inline comments must start with a capital letter, digit or 3-dots sequence
  78 | WARNING | Inline comments must start with a capital letter, digit or 3-dots sequence
  83 | WARNING | Line exceeds 132 characters; contains 134 characters
 114 | WARNING | Inline comments must start with a capital letter, digit or 3-dots sequence
 114 | WARNING | Inline comments must end in full-stops, exclamation marks, or question marks
 116 | WARNING | Inline comments must start with a capital letter, digit or 3-dots sequence
----------------------------------------------------------------------------------------------

Time: 3.44 secs; Memory: 12MB

PS C:\Users\web2\ci-fix\ci> php vendor/bin/phpcs --standard=moodle C:\laragon\www\moodle\availability\condition\stripepayment\webhook.php

FILE: C:\laragon\www\moodle\availability\condition\stripepayment\webhook.php
----------------------------------------------------------------------------------------------
FOUND 0 ERRORS AND 12 WARNINGS AFFECTING 12 LINES
----------------------------------------------------------------------------------------------
  25 | WARNING | Inline comments must end in full-stops, exclamation marks, or question marks
  58 | WARNING | Inline comments must end in full-stops, exclamation marks, or question marks
  69 | WARNING | Inline comments must end in full-stops, exclamation marks, or question marks
 102 | WARNING | Inline comments must end in full-stops, exclamation marks, or question marks
 108 | WARNING | Inline comments must end in full-stops, exclamation marks, or question marks
 114 | WARNING | Inline comments must end in full-stops, exclamation marks, or question marks
 124 | WARNING | Inline comments must end in full-stops, exclamation marks, or question marks
 143 | WARNING | Inline comments must end in full-stops, exclamation marks, or question marks
 153 | WARNING | Inline comments must end in full-stops, exclamation marks, or question marks
 163 | WARNING | Inline comments must end in full-stops, exclamation marks, or question marks
 174 | WARNING | Inline comments must end in full-stops, exclamation marks, or question marks
 188 | WARNING | Inline comments must end in full-stops, exclamation marks, or question marks
----------------------------------------------------------------------------------------------

Time: 3.42 secs; Memory: 12MB

PS C:\Users\web2\ci-fix\ci> php vendor/bin/phpcs --standard=moodle C:\laragon\www\moodle\availability\condition\stripepayment\lib.php
PS C:\Users\web2\ci-fix\ci> php vendor/bin/phpcs --standard=moodle C:\laragon\www\moodle\availability\condition\stripepayment\lang\en\availability_stripepayment.php

FILE: C:\laragon\www\moodle\availability\condition\stripepayment\lang\en\availability_stripepayment.php
----------------------------------------------------------------------------------------------------------------------------------------------------------
FOUND 0 ERRORS AND 61 WARNINGS AFFECTING 60 LINES
----------------------------------------------------------------------------------------------------------------------------------------------------------
  32 | WARNING | Unexpected comment found. Auto-fixing will not work after this comment
  33 | WARNING | The string key "enable" is not in the correct order, it should be before "title"
  41 | WARNING | The string key "accounts_email" is not in the correct order, it should be before "webhook_secret_desc"
  45 | WARNING | The string key "settings_transactions_admin" is not in the correct order, it should be before "settings_transactions_link"
  47 | WARNING | The string key "dot" is not in the correct order, it should be before "status_ok"
  48 | WARNING | Unexpected comment found. Auto-fixing will not work after this comment
  49 | WARNING | The string key "amount" is not in the correct order, it should be before "dot"
  54 | WARNING | Unexpected comment found. Auto-fixing will not work after this comment
  57 | WARNING | The string key "payment_required" is not in the correct order, it should be before "payment_required_desc"
  58 | WARNING | The string key "payment_completed" is not in the correct order, it should be before "payment_required"
  59 | WARNING | The string key "make_payment" is not in the correct order, it should be before "payment_completed"
  62 | WARNING | The string key "payment_successful" is not in the correct order, it should be before "processing"
  63 | WARNING | The string key "payment_cancelled" is not in the correct order, it should be before "payment_successful"
  64 | WARNING | The string key "already_paid" is not in the correct order, it should be before "payment_cancelled"
  66 | WARNING | Unexpected comment found. Auto-fixing will not work after this comment
  68 | WARNING | The string key "payment_success_notification" is not in the correct order, it should be before "payment_successful_title"
  69 | WARNING | The string key "payment_details" is not in the correct order, it should be before "payment_success_notification"
  70 | WARNING | The string key "payment_detail_item" is not in the correct order, it should be before "payment_details"
  71 | WARNING | The string key "payment_detail_amount" is not in the correct order, it should be before "payment_detail_item"
  73 | WARNING | The string key "continue_to_activity" is not in the correct order, it should be before "payment_detail_id"
  75 | WARNING | Unexpected comment found. Auto-fixing will not work after this comment
  77 | WARNING | The string key "payment_failed" is not in the correct order, it should be before "stripe_not_configured"
  78 | WARNING | The string key "error_not_configured" is not in the correct order, it should be before "payment_failed"
  79 | WARNING | The string key "error_amount_required" is not in the correct order, it should be before "error_not_configured"
  83 | WARNING | The string key "activity_not_found" is not in the correct order, it should be before "payment_not_found"
  85 | WARNING | The string key "invalid_amount_admin" is not in the correct order, it should be before "no_condition_found"
  88 | WARNING | The string key "payment_config_error" is not in the correct order, it should be before "payment_failed_declined"
  90 | WARNING | The string key "webhook_empty_payload" is not in the correct order, it should be before "webhook_method_not_allowed"
  93 | WARNING | The string key "webhook_invalid_payload" is not in the correct order, it should be before "webhook_secret_not_configured"
  95 | WARNING | The string key "webhook_error" is not in the correct order, it should be before "webhook_invalid_signature"
  96 | WARNING | The string key "webhook_already_processed" is not in the correct order, it should be before "webhook_error"
  99 | WARNING | Unexpected comment found. Auto-fixing will not work after this comment
 100 | WARNING | The string key "transactionsreport" is not in the correct order, it should be before "webhook_payment_not_completed"
 101 | WARNING | The string key "managetransactions" is not in the correct order, it should be before "transactionsreport"
 102 | WARNING | The string key "id" is not in the correct order, it should be before "managetransactions"
 104 | WARNING | The string key "totalpayments" is not in the correct order, it should be before "transactionid"
 106 | WARNING | The string key "copytransactionid" is not in the correct order, it should be before "viewinstripe"
 108 | WARNING | The string key "downloadcsv" is not in the correct order, it should be before "filteractive"
 109 | WARNING | The string key "allcourses" is not in the correct order, it should be before "downloadcsv"
 111 | WARNING | Unexpected comment found. Auto-fixing will not work after this comment
 111 | WARNING | Inline comments must end in full-stops, exclamation marks, or question marks
 115 | WARNING | Unexpected comment found. Auto-fixing will not work after this comment
 117 | WARNING | The string key "tablenotexist" is not in the correct order, it should be before "unknownactivity"
 118 | WARNING | The string key "payments" is not in the correct order, it should be before "tablenotexist"
 120 | WARNING | Unexpected comment found. Auto-fixing will not work after this comment
 121 | WARNING | The string key "paymentreport" is not in the correct order, it should be before "payments"
 122 | WARNING | The string key "backtocourse" is not in the correct order, it should be before "paymentreport"
 125 | WARNING | The string key "completed" is not in the correct order, it should be before "pending"
 127 | WARNING | The string key "cancelled" is not in the correct order, it should be before "failed"
 129 | WARNING | The string key "clearfilter" is not in the correct order, it should be before "expired"
 131 | WARNING | The string key "payingstudents" is not in the correct order, it should be before "totalrevenue"
 132 | WARNING | The string key "nopayments" is not in the correct order, it should be before "payingstudents"
 134 | WARNING | The string key "activitypaymentreport" is not in the correct order, it should be before "student"
 136 | WARNING | Unexpected comment found. Auto-fixing will not work after this comment
 139 | WARNING | The string key "privacy:metadata:payments:courseid" is not in the correct order, it should be before "privacy:metadata:payments:userid"
 140 | WARNING | The string key "privacy:metadata:payments:cmid" is not in the correct order, it should be before "privacy:metadata:payments:courseid"
 142 | WARNING | The string key "privacy:metadata:payments:amount" is not in the correct order, it should be before "privacy:metadata:payments:sessionid"
 147 | WARNING | Unexpected comment found. Auto-fixing will not work after this comment
 150 | WARNING | Unexpected comment found. Auto-fixing will not work after this comment
 151 | WARNING | The string key "payment_in_progress" is not in the correct order, it should be before "task_cleanup_pending"
 153 | WARNING | Unexpected comment found. Auto-fixing will not work after this comment
----------------------------------------------------------------------------------------------------------------------------------------------------------

Time: 3.36 secs; Memory: 12MB

PS C:\Users\web2\ci-fix\ci>
