# Frequently Bought Together and Intelligent Related Products for Magento 2

This repository contains modules for "Magento 2 Frequently Bought Together" and "Magento 2 Intelligent Related Products Using JNearestNeighbours Algorithm".

Steps to Install:
1. Clone the repository in app/code/Codilar
2. Run Upgrade, Static-Content Deploy and permission commands.
3. Set the configurations as follows:
![afbt_configuration](https://i.imgur.com/jrhiC7d.png)
![irp_configuration](https://i.imgur.com/eFi8lwt.png)
4. For testing Frequently Bought together Module, Add multiple products to cart and checkout. Place some orders with multiple products.
5. Run reindex command.
6. Frequenlty Bought Together block will be shown below the product description on the product page for the products that have been ordered. (Only simple products in this version).
![irp_configuration](https://i.imgur.com/G3ZaWj1.png)
7. Intelligent Related Products Block will be shown below FBT block in Product page.
![irp_configuration](https://i.imgur.com/yZXyu3J.png)
