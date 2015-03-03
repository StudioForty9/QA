Feature: Checkout

  Scenario: Checkout
    When I add a product to the cart
    And I go to the checkout
    And I fill in my Billing Address
    And I chooose a Shipping Method
    And I choose Payment Method
    And I press "Place Order"
    Then I should be on "/checkout/onepage/success"

  #checkout as guest
  #register as part of checkout
  #login to checkout
  #different shipping address