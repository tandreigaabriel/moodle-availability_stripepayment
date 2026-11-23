@availability @availability_stripepayment
Feature: Restrict an activity until a student has paid
  As a teacher
  I need to configure a payment restriction on an activity

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "users" exist:
      | username |
      | teacher1 |
      | student1 |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "activities" exist:
      | activity | course | name   |
      | page     | C1     | Page 1 |

  @javascript
  Scenario: An unpaid student sees the payment restriction
    Given I am on the "Page 1" "page activity editing" page logged in as "teacher1"
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Stripe payment" "button" in the "Add restriction..." "dialogue"
    And I set the field "Currency" to "GBP"
    And I set the field "Amount" to "10"
    And I set the field "Item name" to "Course access"
    And I press "Save and return to course"
    When I am on the "Course 1" "course" page logged in as "student1"
    Then I should see "Course access" in the "region-main" "region"
    And I should see "Pay now" in the "region-main" "region"
