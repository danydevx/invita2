
# Restaurant Menu Module

## Objective

Create a dedicated Restaurant Menu module for businesses such as:

* Restaurants
* Cafes
* Food Trucks
* Taquerías
* Pizzerías
* Bars
* Fast Food Businesses

This module is independent from the generic Business Products module because restaurant products require categories, variants, sizes, presentations, and multiple pricing structures.

---

## Requirements

### Hierarchical Categories

Categories must support unlimited nesting levels using a self-referencing parent_id.

Examples:

### Structure A

* Mexican Food

  * Breakfast
  * Lunch
  * Dinner

* Italian Food

  * Breakfast
  * Lunch
  * Dinner

### Structure B

* Tacos

  * Pastor
  * Suadero
  * Asada

* Tortas

  * Ham
  * Milanesa
  * Cubana

### Structure C

* Drinks

  * Soft Drinks
  * Juices
  * Coffee
  * Beer

The system must allow any hierarchy depth.

---

## Database Tables

### menu_categories

Fields:

* id
* business_id
* parent_id nullable
* title
* description nullable
* image nullable
* slug
* active
* sort_order
* created_at
* updated_at

Rules:

* Category belongs to a Business.
* Category can have a parent category.
* Category can have many child categories.
* Category can contain products.
* Categories must be sortable.

---

### menu_products

Fields:

* id
* business_id
* category_id
* image nullable
* title
* description nullable
* base_price nullable
* show_price
* featured
* active
* sort_order
* created_at
* updated_at

Rules:

* Product belongs to a category.
* Product may have a single price.
* Product may have multiple variants.
* Product may have an image.
* Product may be featured.

---

### menu_product_variants

Fields:

* id
* product_id
* title
* description nullable
* price
* active
* sort_order
* created_at
* updated_at

Examples:

Pizza Hawaiian

* Small → $120
* Medium → $180
* Large → $250

Taco Suadero

* Individual → $25
* Order of 5 → $110
* Order of 10 → $200

Rules:

* Product can have unlimited variants.
* Variant price overrides product base_price.

---

### menu_product_images

Fields:

* id
* product_id
* image
* sort_order
* created_at
* updated_at

Rules:

* Product may contain multiple images.
* First image acts as cover image.

---

## Relationships

Business

* hasMany(MenuCategory)
* hasMany(MenuProduct)

MenuCategory

* belongsTo(Business)
* belongsTo(MenuCategory parent)
* hasMany(MenuCategory children)
* hasMany(MenuProduct)

MenuProduct

* belongsTo(Business)
* belongsTo(MenuCategory)
* hasMany(MenuProductVariant)
* hasMany(MenuProductImage)

MenuProductVariant

* belongsTo(MenuProduct)

MenuProductImage

* belongsTo(MenuProduct)

---

## Public Menu Features

The public menu page must support:

* Mobile-first design.
* Category navigation.
* Product search.
* Featured products.
* Product gallery.
* Product variants.
* Optional price visibility.
* QR code access.
* Public shareable URL.

Example:

business.com/menu

or

business.com/{business_slug}/menu

---

## Admin Features

Business owners must be able to:

* Create categories.
* Create nested categories.
* Reorder categories.
* Upload category images.
* Create products.
* Upload product images.
* Create product variants.
* Activate/deactivate categories.
* Activate/deactivate products.
* Mark products as featured.
* Change display order.

---

## Future Features (Not Required Yet)

* Product extras.
* Ingredients.
* Nutritional information.
* Product tags.
* Allergens.
* Spicy indicators.
* Vegan indicators.
* Availability schedules.
* Online ordering.
* WhatsApp ordering.
* Multi-language menus.
* Coupons and promotions.

Do not implement future features at this stage.
Implement only the core menu functionality described above.


Implementation Notes

Create this module inside:

Modules/RestaurantMenu

Use:

- Entities/Models
- Database/Migrations
- Http/Controllers
- Resources/Views
- Routes/web.php
