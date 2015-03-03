Feature: Product Page

  Scenario: Product Page
    Given I am on a product page
    Then I should see the product name
    And I should see the breadcrumbs
    And I should see a valid product image
    When I click the add to cart button
    Then I should be on the cart page
    And I should see a success message
