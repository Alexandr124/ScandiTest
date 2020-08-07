# ScandiTest

Tasks which were made: 
Task #1 - Only BE - Adding meta tag to head (Getting store lang)
Task #2 - BE + FE - Changing buttons color with a command, depending on the store
Task #3 - Only FE - making mess on checkout :) 

FYI
All task were made within only one module to make it easier to install & check. Like a test mode. 
For installing put 'Test' folder under /app/code/Vendor/, and rut setup:upgrade command

Brief tasks info: 
Task 1 was made, Adding meta tag to cms pages via Vendor\Test\Plugin\App]\Response.php;

Task 2 was made, via Vendor\Test\Console\ChangeColor. Command example: bin/magento scandiweb:color-change -c color -s store_id

Task 3 was made using LayoutProcessor(Test\Plugin\Checkout\LayoutProcessor.php), overriding checkout_index_index.xml and adding js mixin (for button).  
