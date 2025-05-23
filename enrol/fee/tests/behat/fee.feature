@enrol @enrol_fee
Feature: Signing up for a course with a fee enrolment method

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
      | manager1 | Manager   | 1        | manager1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format | summary |
      | Course 1 | C1        | topics |         |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | manager1 | C1     | manager        |
    And the following "core_payment > payment accounts" exist:
      | name           | gateways |
      | Account1       | paypal   |
    And I log in as "admin"
    And I navigate to "Plugins > Enrolments > Manage enrol plugins" in site administration
    And I click on "Enable" "link" in the "Enrolment on payment" "table_row"
    And I log out
    And I log in as "manager1"
    And I am on the "Course 1" "enrolment methods" page
    And I select "Enrolment on payment" from the "Add method" singleselect
    And I set the following fields to these values:
      | Payment account | Account1 |
      | Enrolment fee   | 123.45   |
      | Currency        | Euro     |
    And I press "Add method"
    And I log out

  @javascript
  Scenario: Student can see the payment prompt on the course enrolment page
    When I log in as "student1"
    And I am on course index
    And I follow "Course 1"
    Then I should see "This course requires a payment for entry."
    And I should see "123.45"
    And I press "Select payment type"
    And I should see "PayPal" in the "Select payment type" "dialogue"
    And I click on "Cancel" "button" in the "Select payment type" "dialogue"

  Scenario: Guest can see the login prompt on the course enrolment page
    When I log in as "guest"
    And I am on course index
    And I follow "Course 1"
    Then I should see "This course requires a payment for entry."
    And I should see "123.45"
    And I should see "Log in to the site"

  @javascript
  Scenario: Setting a custom instance name for the fee enrolment method
    And I log in as "manager1"
    And I am on the "Course 1" "enrolment methods" page
    And I click on "Edit" "link" in the "Enrolment on payment" "table_row"
    And I set the following fields to these values:
      | Custom instance name | Lifetime access |
      | Instance description | Only for teachers  |
    And I press "Save changes"
    And I should see "Lifetime access"
    And I should see "Only for teachers"
    And I should not see "Enrolment on payment" in the "Lifetime access" "table_row"
    And I log out
    When I log in as "student1"
    And I am on course index
    And I follow "Course 1"
    Then I should not see "This course requires a payment for entry."
    Then I should see "Lifetime access"
    Then I should not see "Only for teachers"
