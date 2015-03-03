Feature: Customer

  Scenario: Customer Login
    Given I am a registered customer
    When I go to the customer login page
    And I enter my username and password
    And I press "Submit"
    Then I should by on the My Account page
    And I should see "My Account"

  Scenario: Customer Navigation
    Given I am logged in as a customer

    When I click on "My Orders"
    Then I should be on "/sales/order/history/"
    And I should see "My Orders"

    When I click on "Account Information"
    Then I should be on "/customer/account/edit/"
    And I should see "Account Information"

    When I click on "Address Book"
    Then I should be on "/customer/address/new/"
    And I should see "Add New Address"

    When I click on "Account Dashboard"
    Then I should be on "/customer/account/"
    And I should see "My Dashboard"