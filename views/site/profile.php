<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ProfileForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Modal;

$this->title = 'Profile';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-contact">
    <div class="row">
        <div class="col-lg-6 col-md-offset-3">

            <h1><?= Html::encode($this->title) ?></h1>

            <p>
                Change your profile data or delete it.
            </p>

            <?php $form = ActiveForm::begin(['id' => 'profile-form']); ?>

                <?= $form->field($model, 'firstName')->textInput(['autofocus' => true, 'value' => Yii::$app->user->identity->firstName]) ?>
                <?= $form->field($model, 'lastName')->textInput(['value' => Yii::$app->user->identity->lastName]) ?>

                <?= $form->field($model, 'email')->textInput(['readonly' => true, 'value' => Yii::$app->user->identity->email]) ?>
                <?= $form->field($model, 'password')->passwordInput() ?>

                <div class="form-group">
                    <?= Html::submitButton('Update account', ['class' => 'btn btn-primary', 'name' => 'profile-update-button']) ?>
                    <?= Html::button('Delete account',[
                        'class'=>'delete-profile-modal-click btn btn-danger',
                        'data-toggle'=>'tooltip',
                        'data-placement'=>'bottom',
                        'title'=>'Delete profile'
                    ]); ?>
                </div>

            <?php ActiveForm::end(); ?>

            <?php
                Modal::begin([
                    'header'=>'<h4>Delete profile</h4>',
                    'id'=>'delete-profile-modal',
                    'size'=>'modal-lg'
                ]);

                echo '<div id="delete-profile-modal-content">
                        <p>Are you sure you want to delete your accoount?</p>
                        <button type="button" class="btn btn-success delete-profile-confirm-click">Delete</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal" aria-hidden="true">Cancel</button>
                    </div>';

                Modal::end();

                $this->registerJs(
                "jQuery('.delete-profile-modal-click').click(function () {
                        jQuery('#delete-profile-modal').modal('show');
                    });
                    jQuery('.delete-profile-confirm-click').click(function () {
                        window.location.href = '/?r=site/profile&action=delete';
                    });"
                );
            ?>
        </div>
    </div>

</div>
