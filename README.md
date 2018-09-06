
# Nth Order Discount for WooCommerce

Nth Order Discount for WooCommerce is a WooCommerce extension that automatically creates discount for customer's order after every nth successful order by that customer. Discount will be in form of coupon that can be used only once by that customer. Customer must be registered and logged in for each order.

## Usage

If you clone this repository, run `composer install`. Otherwise, require [`dimadin/nth-order-discount-for-woocommerce` package](https://packagist.org/packages/dimadin/nth-order-discount-for-woocommerce) in your project.

After activation, set preferences on *WooCommerce* > *Settings* > *General* page.

### Settings

#### Completed orders before discount

With this setting, you select how many completed orders user has to made before it gets automatic discount. For example, if you select *4*, that means that on its fifth order (and tenth, and fifteenth and so on), user will get automatic discount.

#### Discount amount

With this setting, you select what discount user will get. This setting depends on setting *Discount type*, so if you select *10* for *Discount amount* and *Percentage discount* for *Discount type*, user would get discount of 10%. If it is *Fixed discount* under *Discount type*, it means that discount is 10 units of selected currency (USD, EUR, RSD, etc).

#### Discount type

See *Discount amount* for details.
