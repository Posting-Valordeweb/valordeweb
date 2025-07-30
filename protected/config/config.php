<?php return array(
    'app.name'=>'Website Worth Calculator',
    'app.timezone'=>'Europe/Berlin',
    'app.default_language'=>'en',
    'app.host'=>'http://sitecost.codecanyon',
    'app.base_url'=>'/',
    'app.encryption_key'=>'R7KRcQWfQsM2We88492ZLold7kNqJ5VO',
    'app.validation_key'=>'BRmCmfvaemWoz5rzx767lTkURS2LS0Ku',
    'app.command_key'=>'',
    'app.log_missing_translations'=>true,
    'app.cookie_validation'=>true,
    'app.nav_name'=>'',
    'app.nav_icon'=>'',
    'app.allow_user_auth'=>true,

    'db.host'=>'localhost',
    'db.dbname'=>'sitecost',
    'db.username'=>'homestead',
    'db.password'=>'secret',
    'db.port'=>3306,

    'mailer.host'=>'localhost',
    'mailer.port'=>25,
    'mailer.auth'=>false,
    'mailer.protocol'=>'',
    'mailer.username'=>'',
    'mailer.password'=>'',

    'url.show_script_name'=>false,
    'url.multi_language_links'=>true,

    'admin.email'=>'',
    'notification.email'=>'',
    'notification.name'=>'Website Worth Calculator',

    'site_cost.captcha'=>false,
    'recaptcha.public'=>'',
    'recaptcha.private'=>'',

    'google.server_key'=>'',
    'google.browser_key'=>'',

    'moz.brand_authority_token'=>'',

    'facebook.app_id'=>'',
    'facebook.app_secret'=>'',

    'cookie_law.show'=>true,
    'cookie_law.link'=>'', // Leave empty to refer to the internal Privacy Policy page
    'cookie_law.theme'=>'light-floating',
    'cookie_law.expiry_days'=>365,

    'share.js'=>'',
    'share.html'=>'',

    'thumbnail.proxy'=>false,
    'pagepeeker.verify'=>"",
    'pagepeeker.api_key'=>'',

    'cookie.secure'=>false,
    'cookie.same_site'=>'Lax',

    // Banner free websites that can be put on sale. See the "domain_restriction.php" file for pattern examples.
    'sales.banner_free'=>array(
    ),

    'site_cost.cache'=>60 * 60 * 24,
    'site_cost.validate_bad_words'=>true,
    'site_cost.price_pattern'=>'{currency} {price}',
    'site_cost.currency'=>'&#36;',
    'site_cost.dollar_rate'=>1,
    'site_cost.decimals'=>2,
    'site_cost.dec_point'=>'.',
    'site_cost.thousands_sep'=>',',
    'site_cost.auto_calculation'=>false,
    'site_cost.banner_top'=>'',
    'site_cost.banner_bottom'=>'',

    'site_cost.curl_cookie_cache_path'=>'application.runtime',
    'site_cost.websites_per_page'=>12,
    'site_cost.on_sale_validation_limit'=>3,
    'site_cost.websites_on_index_page'=>6,
    'site_cost.on_sale_per_page'=>15,
    'site_cost.countries_per_page'=>30,
    'site_cost.category_per_page'=>15,
    'site_cost.placeholder'=>'php8developer.com',
    'site_cost.html_head'=>'',
    'site_cost.html_footer'=>'<p class="text-muted">Developed by <strong><a href="http://php8developer.com">PHP 8 Developer</a></strong></p>',
);