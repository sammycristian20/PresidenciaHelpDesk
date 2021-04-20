This file contains comments for composer.json to store information about the package dependencies available in composer.json file. Developers are requested to mention any special usage of a package version or dependencies used in the project to make it easy for maintainers of the project to handle these special dependencies and update the project in future.

### Maatwebsite/Excel

"maatwebsite/excel": "dev-2.1-laravel6 from Manish's Gihtub account which has been updated for Laravel 6 support.

**Package name:**         maatwebsite/excel<br />
**Github link:**         <https://github.com/Maatwebsite/Laravel-Excel><br />
**Packagist:**            <https://packagist.org/packages/maatwebsite/excel><br />
**Date**                 5-01-2020<br />
**Dependent packages:**   yajra/laravel-datatables-buttons<br />
**Developer:**            Manish Verma<br />
**Description:**          While updating laravel version to 5.7 forced composer to install 2.1.30 as 3.0. Dependent packages were updated
                       which required v3.0 or higher of this package. But as the updated version of the package was backward incompatible and many methods were removed from the package resulting in many missing functionalities. As the package is being used in the code to read or maybe in exporting the excel sheets I just updated the composer.json file to use the older version as the new version. Updating the package fails this test group **batchTicket**<br />
**Reason:**               The package is being used in BatchTicket creation which reads an excel file to get all requesters details.
                       Package method which is used in the code has been removed in the latest code as the author has rewritten the package and currently it only exports the data however the author is going to work on import features soon as mentioned in the article <https://medium.com/maatwebsite/laravel-excel-lessons-learned-7fee2812551>. As v3.1 removes old methods so created a fork form v2.1 and added support for Laravel 6 in the forked branch.<br />
**Expected impact:**      This package might break the functionality of dependent package but the dependent package.<br />
**Todo:**                 v3.1 of the package is backward incompatible and many methods have been removed from the latest version which are being used in Faveo code. Check new methods in the package to achieve existing functionalities or find the replacement of methods or this package.

### Davibennun/Laravel-push-notification

Using forked branch "dev-laravel6" from Manish's Gihtub account which has been updated for laravel 6 support.

**Package name:**         davibennun/laravel-push-notification<br />
**Github link:**          <https://github.com/davibennun/laravel-push-notification><br />
**Packagist:**            <https://packagist.org/packages/davibennun/laravel-push-notification><br />
**Fork link:**            <https://github.com/mverma16/laravel-push-notification><br />
**Date**                  18-12-2019<br />
**Dependent packages:**   NONE<br />
**Developer:**            Manish Verma<br />
**Description:**          While updating laravel version to 6.0 forced composer to install the forked
					   branch from Github. As the package is not updated anymore now this was preventing
					   composer from updating Laravel so created a fork and added support for Laravel 6 in the froked branch.<br />
**Reason:**               The package is being used for sending push notifications to mobile apps
						using FCM. As the package is no longer supported we cloned the updated fork.<br />
**Expected impact:**      NONE<br />
**To-do:**                 There is a new package available <https://github.com/Edujugon/PushNotification>
						need to implement the functionality using this new package.

### Vsmoraes/Laravel-pdf

Using forked branch "dev-laravel6" from Manish's Gihtub account which has been updated for laravel 6 support.

**Package name:**         vsmoraes/laravel-pdf<br />
**Github link:**          <https://github.com/vsmoraes/pdf-laravel5><br />
**Packagist:**            <https://packagist.org/packages/vsmoraes/laravel-pdf><br />
**Fork link:**            <https://github.com/mverma16/pdf-laravel5.git><br />
**Date**                  18-12-2019<br />
**Dependent packages:**   NONE<br />
**Developer:**            Manish Verma<br />
**Description:**          While updating laravel version to 6.0 forced composer to install the forked
					   branch from GitHub. As the package is not updated anymore now this was preventing
					   composer from updating Laravel so created a fork and added support for Laravel 6 in the forked branch.<br />
**Reason:**               To able to update Laravel without breaking the system.<br />
**Expected impact:**      NONE<br />
**To-do:**                Created the issue <https://github.com/vsmoraes/pdf-laravel5/issues/35> to get 						   an update for laravel. Seems the package is no longer updated so need to find 						   replacement of the package or if not being used then we need to remove the 
                       package.

### madnest/madzipper

Using forked branch "dev-laravel6" from Manish's Gihtub account which has been updated for laravel 6 support.

**Package name:**         madnest/madzipper<br />
**Github link:**          <https://github.com/madnest/madzipper><br />
**Packagist:**            <https://packagist.org/packages/madnest/madzipper><br />
**Fork link:**            <https://github.com/mverma16/madzipper/.git><br />
**Date**                  30-12-2019<br />
**Dependent packages:**   NONE<br />
**Developer:**            Manish Verma<br />
**Description:**          While updating laravel version to 6.0 forced composer to install the forked
					   branch from Github. As the package is not updated anymore now this was preventing
					   composer from updating Laravel so created a fork and added support for Laravel 6 in the forked branch.<br />
**Reason:**               To able to update Laravel without breaking the system.<br />
**Expected impact:**      NONE<br />
**To-do:**                Created the issue <https://github.com/vsmoraes/pdf-laravel5/issues/35> to get 						   an update for Laravel. Seems the package is no longer updated so need to find 						   replacement of the package or if not being used then we need to remove the 
                       package. 

### laravel/framework
Locked laravel framework update to v6.18.26 as there are breaking changes in the framework which breaks auto-update functionality of the application.
**Package name:**         laravel/framework<br />
**Github link:**          <https://github.com/laravel/framework><br />
**Packagist:**            <https://packagist.org/packages/laravel/laravel><br />
**Date**                  12-09-2020<br />
**Dependent packages:**   NONE<br />
**Developer:**            Manish Verma<br />
**Description:**          If we update to v6.18.27 and higher it breaks the auto-update module causing the failiure of the system update and crashing the application.<br />
**Reason:**               To able to update allow auto-update module work without any issue.<br />
**Expected impact:**      NONE<br />
**To-do:**                Release security patch for the application by updating the laravel framework without database changes so laravel update cycle can run without problems. 
