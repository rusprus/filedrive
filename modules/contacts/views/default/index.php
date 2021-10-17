<?php

use app\modules\contacts\assets\ContactsAsset;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

ContactsAsset::register($this);  // $this - представляет собой объект представления


?>    
<h1 class='text-center'>Телефонная книга</h1>

        <div class="row">
            <div class="col">
                <table id="filter-table">
                <tr class='table-filters'>  
                        <td><input class="target" type="text"></td>    
                        <td><input class="target" type="text"></td>    
                        <td><input class="target" type="text"></td>
                        <td><input class="target" type="text"></td>
                        <td><input class="target" type="text"></td>
                    </tr>
                <?php foreach ( $contacts as $item ): ?>

                    <tr class='table-data' >  
                        <td class='id'  onclick="alert('!')"><?php echo $item->id; ?></td>    
                        <td class='first_name'><?php echo $item->first_name; ?></td>    
                        <td class='last_name'><?php echo $item->last_name; ?></td>
                        <td class='add_names'><?php echo $item->add_names; ?></td>
                        <td class='tel'><?php echo $item->tel; ?></td>
                    </tr>

                <?php endforeach; ?>
                </table>
            </div>
            <div class="col">
                <?php $form = ActiveForm::begin([
                    'id' => 'write-form',
                    'action' => '/contacts/default/upload',
                    'options' => ['enctype' => 'multipart/form-data',
                                    'class' => ' row',
                                 ],
                ]) ?>
                   
                    <?= $form->field($model, 'imageFile', ['options' => ['class' => '']])->fileInput(['options' => ['class' => 'btn btn-primary']])->label('Файл cvf') ?>
                    

                    <button class="btn btn-primary " type="submit" >Отправить</button>
                <?php ActiveForm::end() ?>

            </div>
        </div>