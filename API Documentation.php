
   User Operations Controller [ */src/Controller/UserController.php* ]
   ==============================================
   1. Login => Method => login();
   2. Email validation => Method => checkEmailValidity();
   3. Forgot Password => Method => forgetPassword();
   4. Change/Reset Password => Method => changePassword();
   5. Logout => Method => logout();

   
   Custom Component [ */src/Controller/Component/* ]
   ==============================================
   1. Email Templates => Component => SmartEmailComponent.php
   
   
   Email Template Loader [ */vendor/EmailTemplates/class.templates.php* ]
   ==============================================
   1. Loading Forgot Password, forgetPasswordTemplate();
   2. Loading Password Reset, passwordChangedTemplate();
   
   
   Email Templates [ */vendor/EmailTemplates/* ]
   ==============================================
   1. forgetPassword.php
   2. passwordChanged.php
   
   
   Data Controller [ */src/Controller/DataController.php* ]
   ==============================================  
   1. get Countries List: Method => countries();
   2. get States List: Method => states();
   3. get Cities List: Method => cities();
   4. get States by Country Id: Method => statesByCountryId();
   5. get Cities by State Id: Method => citiesByStateId();
   
   
   Location Controller [ */src/Controller/LocationController.php*  ]
   ===============================================
   1. Create Location: Method => create();
   2. Edit Location: Method => edit();
   3. Get All Agency Locations: Method => getUserLocations();
   4. Delete Location: Method => delete();
   
   
   FILES:
   --------------------------------
   Common Functions defined in "/src/Controller/AppController.php"
   
   Constants Parameter defined in "bootstrap.php" file within " /config/bootstrap.php ";