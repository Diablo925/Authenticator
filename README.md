# Authenticator
this it not easy to do so no module install script

this module allow you to add TOTP (Time-based One-time Password)

What do you need to do 

download the file login.ztml to the default style in "sentora\panel\etc\style\Sentora_Default\"
And download the file init.inc.php and GoogleAuthenticator.php to the folder "sentora\panel\inc\"

and install the module in the folder module. 

and open you mysql and run this 

INSERT INTO `x_modules` (`mo_category_fk`, `mo_name_vc`, `mo_version_in`, `mo_folder_vc`, `mo_type_en`, `mo_desc_tx`, `mo_installed_ts`, `mo_enabled_en`, `mo_updatever_vc`, `mo_updateurl_tx`) VALUES (1, 'TOTP Authenticator', 100, 'Authenticator', 'user', 'Allow you to add TOTP Authenticator', 1424706277, 'true', NULL, NULL);

now type 
ALTER TABLE x_accounts ADD ac_otp_vc varchar(2), ac_otpkey_vc varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER ac_deleted_ts


Remember to set the the modules right ind "Module Admin"
