<div class="row mt-5">
		<div class="col-md-4 offset-md-4">
				<div class="card">
						<div class="card-header">
								<?php echo Yii::t("user", "Please Sign In") ?>
						</div>
						<div class="card-body">
								<form role="form" method="POST">

												<?php echo CHtml::errorSummary($login_form, null, null, array(
													'class' => 'alert alert-danger',
												)); ?>
												<div class="form-group">
														<?php echo CHtml::activeTextField($login_form, 'email', array(
															'class'=> 'form-control',
															'placeholder'=>Yii::t("user", "Email"),
															'type'=>"email",
															'autofous'=>'autofocus',
														));?>
												</div>
												<div class="form-group">
														<?php echo CHtml::activePasswordField($login_form, 'password', array(
																'class' => 'form-control',
																'placeholder' => Yii::t("user", "Password"),
																'required' => true,
															));?>
												</div>

                                            <?php if(Helper::isAllowedCaptcha()): ?>
                                            <div class="form-group">
                                                <?php $this->widget("ext.recaptcha2.ReCaptcha2Widget", array(
                                                    "siteKey"=>Yii::app()->params['recaptcha.public'],
                                                    'model'=>$login_form,
                                                    'attribute'=>'verifyCode',
                                                    "widgetOpts"=>array(),
                                                )); ?>
                                            </div>
                                            <?php endif; ?>

                                            <div class="form-group">
                                                <?php echo CHtml::activeCheckBox($login_form, 'remember'); ?>
                                                <label class="form-check-label" for="LoginForm_remember"><?php echo Yii::t("user", "Remember me"); ?></label>
                                            </div>

                                            <button class="btn btn-lg btn-success btn-block" type="submit"><?php echo Yii::t("user", "Sign in") ?></button>
								</form>
						</div>
				</div>
		</div>
</div>