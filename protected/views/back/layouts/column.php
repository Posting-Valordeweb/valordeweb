<?php $this->beginContent('/'.$this->_end.'/layouts/main'); ?>

<div class="d-flex flex-column min-vh-100">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-3">
        <a class="navbar-brand" href="<?php echo $this -> createUrl('admin/site/index') ?>">
            <?php echo Helper::getNavbarBrand(); ?>
        </a>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">

                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $this->createUrl("admin/site/index") ?>"><?php echo Yii::t("admin", "Dashboard") ?></a>
                </li>
                <?php if($this->user->isSuperUser()): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $this->createUrl("admin/user/index") ?>"><?php echo Yii::t("user", "Manage Users") ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $this->createUrl("admin/scam/index") ?>"><?php echo Yii::t("scam", "Scam reports") ?></a>
                </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $this->createUrl("admin/website/index") ?>"><?php echo Yii::t("website", "Manage Websites") ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $this->createUrl("admin/category/index") ?>"><?php echo Yii::t("category", "Manage Categories") ?></a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?php echo Yii::t("admin", "Localization") ?>&nbsp;&nbsp;<b class="caret"></b>
                    </a>

                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="<?php echo $this->createUrl("admin/language/index") ?>"><?php echo Yii::t("language", "Manage existing languages") ?></a>
                        <a class="dropdown-item" href="<?php echo $this->createUrl("admin/language/category") ?>"><?php echo Yii::t("language", "Manage Translations") ?></a>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?php echo Yii::t("admin", "Tools") ?>&nbsp;&nbsp;<b class="caret"></b>
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="<?php echo $this->createUrl("admin/tools/sitemap") ?>"><?php echo Yii::t("admin", "Sitemap generation") ?></a>
                        <a class="dropdown-item" href="<?php echo $this->createUrl("admin/tools/bulkimport") ?>"><?php echo Yii::t("admin", "Bulk Import") ?></a>
                        <a class="dropdown-item" href="<?php echo $this->createUrl("admin/tools/clearcache") ?>"><?php echo Yii::t("admin", "Flush cache") ?></a>
                        <a class="dropdown-item" href="<?php echo $this->createUrl("admin/tools/checkonsale") ?>"><?php echo Yii::t("admin", "Run widget checker") ?></a>
                        <a class="dropdown-item" href="<?php echo $this->createUrl("admin/tools/garbagecollector") ?>"><?php echo Yii::t("admin", "Run garbage collector") ?></a>
                    </div>
                </li>
                <?php $this -> widget('application.widgets.LanguageSelector'); ?>
            </ul>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?php echo Yii::t("misc", "Hello, {Username}", array(
                            "{Username}"=>"<strong>". CHtml::encode(Yii::app()->user->name) ."</strong>",
                        )) ?><b class="caret"></b>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <?php if($this->user->isSuperUser()): ?>
                        <a class="dropdown-item" href="<?php echo $this->createUrl("admin/user/update", array("id"=>$this->user->id)) ?>">
                            <?php echo Yii::t("user", "Profile Settings") ?>
                        </a>
                        <a class="dropdown-item" href="<?php echo $this->createUrl("admin/user/reset-password", array("id"=>$this->user->id)) ?>">
                            <?php echo Yii::t("user", "Reset password") ?>
                        </a>
                        <?php endif; ?>
                        <a class="dropdown-item" href="<?php echo $this->createUrl("admin/user/logout") ?>"><i class="fa fa-sign-out fa-fw"></i>
                            <?php echo Yii::t("misc", "Logout") ?>
                        </a>
                    </div>
                </li>
            </ul>
        </div>
    </nav>

    <main class="flex-fill mb-3">
        <div class="container mb-3">
            <div class="row">
                <div class="col-lg-12 mb-3">
                    <?php echo $this -> renderPartial("//". $this->_end. "/site/flash", array(
                        "messages"=>Yii::app() -> user -> getFlashes(),
                    )) ?>
                </div>
                <div class="col-lg-12">
                    <h1><?php echo CHtml::encode($this->title) ?></h1>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="row">
                <div class="col">
                    <?php echo $content ?>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <div class="row">
                <div class="col">
                    <p class="text-muted">Developed by <strong><a href="http://php8developer.com">PHP 8 Developer</a></strong></p>
                </div>
            </div>
        </div>
    </footer>
</div>

<?php $this->endContent('/'.$this->_end.'/layouts/main'); ?>