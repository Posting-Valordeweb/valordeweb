<!DOCTYPE html>
<html lang="<?php echo Yii::app() -> language ?>">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="author" content="php8developer.com">
<meta name="dcterms.rightsHolder" content="php8developer.com">
<link rel="shortcut icon" href="<?php echo Yii::app()->getBaseUrl(true) ?>/favicon.ico" />
<link href="<?php echo Yii::app()->baseUrl ?>/css/bootstrap.yeti.min.css" rel="stylesheet">
<link href="<?php echo Yii::app()->baseUrl ?>/css/app.css" rel="stylesheet">
<link href="<?php echo Yii::app()->baseUrl ?>/css/fontawesome.min.css" rel="stylesheet">

<link rel="apple-touch-icon" href="<?php echo Yii::app()->getBaseUrl(true) ?>/images/touch-114.png">
<link rel="apple-touch-icon" sizes="72x72" href="<?php echo Yii::app()->getBaseUrl(true) ?>/images/touch-72.png">
<link rel="apple-touch-icon" sizes="114x114" href="<?php echo Yii::app()->getBaseUrl(true)  ?>/images/touch-114.png">

<?php Yii::app()->clientScript->registerCoreScript('jquery') ?>
<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js/bootstrap.bundle.min.js') ?>
<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js/base.js?v=0.1') ?>

<title><?php echo CHtml::encode($this->title) ?></title>
<?php echo Yii::app()->params['site_cost.html_head'] ?>
</head>

<body>

<div class="d-flex flex-column min-vh-100">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-20">
        <div class="container">
            <a class="navbar-brand" href="<?php echo $this -> createUrl('site/index', array('language' => Yii::app() -> language)) ?>">
                <?php echo Helper::getNavbarBrand(); ?>
            </a>

            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarPrimary" aria-controls="navbarPrimary" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarPrimary">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item<?php echo ($this->id == "site" AND $this->action->id == "index") ? ' active' : null?>">
                        <a class="nav-link" href="<?php echo $this -> createUrl('site/index', array('language' => Yii::app() -> language)) ?>">
                            <?php echo Yii::t("misc", "Home") ?>
                        </a>
                    </li>
                    <li class="nav-item<?php echo ($this->id == "website" AND in_array(strtolower($this->action->id), array("toplist", "countrylist", "country", "pageranklist", "pagerank"))) ? ' active' : null?>">
                        <a class="nav-link" href="<?php echo $this->createUrl("website/toplist") ?>">
                            <?php echo Yii::t("misc", "Top") ?>
                        </a>
                    </li>
                    <li class="nav-item<?php echo ($this->id == "website" AND $this->action->id == "upcominglist") ? ' active' : null?>">
                        <a class="nav-link" href="<?php echo $this->createUrl("website/upcominglist") ?>">
                            <?php echo Yii::t("misc", "Upcoming") ?>
                        </a>
                    </li>
                    <?php if(Yii::app()->params['app.allow_user_auth']): ?>
                    <li class="nav-item<?php echo ($this->id == "website" AND $this->action->id == "sell") ? ' active' : null?>">
                        <a class="nav-link" href="<?php echo $this->createUrl("website/sell") ?>">
                            <?php echo Yii::t("misc", "Sell Websites") ?>
                        </a>
                    </li>
                    <li class="nav-item<?php echo ($this->id == "category") ? ' active' : null?>">
                        <a class="nav-link" href="<?php echo $this->createUrl("category/index") ?>">
                            <?php echo Yii::t("misc", "Buy Websites") ?>
                        </a>
                    </li>
                    <?php endif; ?>
                    <li class="nav-item<?php echo ($this->id == "site" AND $this->action->id == "contact") ? ' active' : null?>">
                        <a class="nav-link" href="<?php echo $this->createUrl("site/contact") ?>">
                            <?php echo Yii::t("contact", "Contact us") ?>
                        </a>
                    </li>

                    <?php $this -> widget('application.widgets.LanguageSelector'); ?>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-rss fa-lg"></i><b class="caret"></b>
                        </a>
                        <div class="dropdown-menu">
                            <a href="<?php echo $this->createUrl("feed/rss") ?>"
                               class="dropdown-item"
                               target="_blank" type="application/rss+xml"
                               rel="alternate"
                               title="<?php echo Yii::t("misc", "{BrandName} | RSS feed", array("{BrandName}"=>Yii::app()->name)) ?>">
                                <?php echo Yii::t("misc", "RSS") ?>
                            </a>

                            <a href="<?php echo $this->createUrl("feed/atom") ?>"
                               target="_blank"
                               class="dropdown-item"
                               type="application/atom+xml"
                               rel="alternate"
                               title="<?php echo Yii::t("misc", "{BrandName} | Atom feed", array("{BrandName}"=>Yii::app()->name)) ?>">
                                <?php echo Yii::t("misc", "Atom") ?>
                            </a>
                        </div>
                    </li>
                </ul>
                <?php if(Yii::app()->params['app.allow_user_auth']): ?>
                <ul class="navbar-nav ml-auto">
                    <?php if(Yii::app()->user->isGuest): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $this->createUrl("user/sign-in") ?>"><?php echo Yii::t("misc", "Sign in") ?></a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-user fa-fw"></i> <?php echo Yii::t("misc", "Hello, {Username}", array(
                                    "{Username}"=>"<strong>". CHtml::encode(Yii::app()->user->name) ."</strong>",
                                )) ?>
                                <?php if($this->newMessages > 0): ?>
                                    &nbsp;<span class="badge badge-light"><?php echo $this->newMessages ?></span>
                                <?php endif; ?>
                            </a>

                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="<?php echo $this->createUrl('post/index') ?>">
                                    <i class="fas fa-inbox"></i> <?php echo Yii::t("misc", "Inbox") ?>
                                    <?php if($this->newMessages > 0): ?>
                                        &nbsp;<span class="badge badge-light"><?php echo $this->newMessages ?></span>
                                    <?php endif; ?>
                                </a>
                                <a class="dropdown-item" href="<?php echo $this->createUrl('sale/index') ?>"><i class="fas fa-dollar-sign"></i>&nbsp;&nbsp;<?php echo Yii::t("misc", "On sale") ?></a>
                                <a class="dropdown-item" href="<?php echo $this->createUrl('profile/settings') ?>"><i class="fas fa-cogs"></i>&nbsp;&nbsp;<?php echo Yii::t("misc", "Settings") ?></a>
                                <a class="dropdown-item" href="<?php echo $this->createUrl('user/logout') ?>"><i class="fas fa-sign-out-alt"></i>&nbsp;&nbsp;<?php echo Yii::t("misc", "Logout") ?></a>
                            </div>
                        </li>
                    <?php endif; ?>
                </ul>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <main class="flex-fill">
        <?php if(Helper::can_show_banner("top")): ?>
        <div class="container">
            <div class="row mb-20">
                <div class="col">
                    <?php echo Yii::app()->params['site_cost.banner_top'] ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if($flashes=Yii::app()->user->getFlashes()): ?>
            <div class="container">
                <div class="row mb-20">
                    <div class="col">
                        <?php echo $this -> renderPartial("//". $this->_end. "/site/flash", array("flashes"=>$flashes)) ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="container">
            <div class="row">
                <div class="col">
                    <?php echo $content ?>
                </div>
            </div>
        </div>

        <?php if(Helper::can_show_banner("bottom")): ?>
            <div class="container">
                <div class="row mb-20">
                    <div class="col">
                        <?php echo Yii::app()->params['site_cost.banner_bottom'] ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <?php echo Yii::app()->params['site_cost.html_footer'] ?>
                    <?php echo Yii::app()->params['pagepeeker.verify'] ?>
                </div>
                <div class="col-md-4">
                    <ul>
                        <li><a href="<?php echo Helper::url_privacy() ?>"><?php echo Yii::t("misc", "page_privacy_link") ?></a></li>
                        <li><a href="<?php echo $this->createUrl('site/terms') ?>"><?php echo Yii::t("misc", "page_terms_link") ?></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>
</div>

</body>
</html>