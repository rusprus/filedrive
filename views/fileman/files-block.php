<?php 
use yii\helpers\Url;
use Yii;

// $this->params['breadcrumbs'] = Yii::$app->session->get('breadcrumbs');

?>

<div class="_files-list row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4">

    <?php foreach($files as $file):  ?>
                      <?php if($file->type == 'dir'):?>
                        <div class="dir  col border border-primary px-4 py-4" data-contextmenu-folder data-id="<?php echo $file->id;?>" style="width:250px; height: 100px;">

                            <img src="<?php echo Url::to('@web/img/folder.png'); ?>" alt="folder" class="h-100 w-25" style="width:250px; height: 100px; ">
                            <label data-id="<?php echo $file->id;?>" data-type="<?php echo $file->type;?>"  data-click class="d-inline-block"><?php echo $file->name ?></label>
                         
                        </div>
                     
                        <?php endif; ?>

                      <?php if($file->type == 'file'):?>
                        <div class="dir col border border-primary px-4 py-4" data-contextmenu-file data-id="<?php echo $file->id;?>" style="width:250px; height: 100px;">

                            <img src="<?php echo Url::to('@web/img/document.png'); ?>" alt="file" class="h-100 w-25">
                            <label data-id="<?php echo $file->id;?>"  data-type="<?php echo $file->type;?>" data-click data-contextmenufile  class="d-inline-block " ><?php echo $file->name ?></label>
                      
                        </div>
                        <?php endif; ?>
    <?php endforeach; ?>

</div>
