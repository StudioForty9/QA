Feature: Cart

  Scenario: Empty Cart
    Given I am on "/checkout/cart"
    Then I should see "Shopping Cart is Empty"

  Scenario: Cart
    When I add a product to the cart
    Then I should see "Product Name"
    Then I should see "Subtotal"
    Then I should see "Grand Total"
    When I follow "Proceed to Checkout"
    Then I should be on "/checkout/onepage"

  Scenario: Update Qty
    When I add a product to the cart
    When I change the Qty to 10
    And I press "Updating Shopping Cart"
    Then I should see a success message

  Scenario: Clear Shopping Cart
    When I add a product to the cart
    And I press "Clear Shopping Cart"
    Then I should see "Shopping Cart is Empty"

  Scenario: Apply a Discount
    Given a discount exists with code "Test Discount"
    When I add a product to the cart
    And I apply the "Test Discount" coupon code
    Then I should see a success message
    And I should see "Discount"