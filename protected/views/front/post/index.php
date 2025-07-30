<script type="text/javascript">
    $(document).ready(function(){
        $("#checkAll").on("click", function(){
            if ($(this).is(':checked')) {
                $(".headers").prop("checked", true);
            } else {
                $(".headers").prop("checked", false);
            }
        });
        $(".inbox-operation").on("click", function() {
            var $href=$(this).attr("href");
            var form=document.getElementById("header_form");
            form.action=$href;
            form.submit();
            return false;
        });
    })
</script>
<h1 class="mb-20"><i class="fa fa-inbox"></i>&nbsp;<?php echo Yii::t("post", "My messages") ?></h1>

<div class="row email">
    <div class="col-md-3">
        <ul class="nav nav-pills flex-column mb-20">
            <li class="nav-item inbox">
                <a class="nav-link<?php echo $folder==UserInnerMailBox::FOLDER_INBOX ? " active" : null ?>" href="<?php echo $this->createUrl("post/index", array("f"=>UserInnerMailBox::FOLDER_INBOX)) ?>">
                    <i class="fas fa-inbox"></i>&nbsp;&nbsp;<?php echo Yii::t("post", "folder_inbox") ?>
                <?php echo $this->newMessages > 0 ? "(". $this->newMessages .")" : null; ?>
                </a>
            </li>
            <li class="nav-item sent">
                <a class="nav-link<?php echo $folder==UserInnerMailBox::FOLDER_SENT ? ' active' : null ?>" href="<?php echo $this->createUrl("post/index", array("f"=>UserInnerMailBox::FOLDER_SENT)) ?>">
                    <i class="fas fa-share"></i>&nbsp;&nbsp;<?php echo Yii::t("post", "folder_sent") ?>
                </a>
            </li>
            <li class="nav-item starred">
            <a class="nav-link<?php echo $folder==UserInnerMailBox::FOLDER_STARRED ? ' active' : null ?>" href="<?php echo $this->createUrl("post/index", array("f"=>UserInnerMailBox::FOLDER_STARRED)) ?>">
                <i class="fas fa-star"></i>&nbsp;&nbsp;<?php echo Yii::t("post", "folder_starred") ?>
            </a>
            </li>
            <li class="nav-item important">
            <a class="nav-link<?php echo $folder==UserInnerMailBox::FOLDER_IMPORTANT ? ' active' : null ?>" href="<?php echo $this->createUrl("post/index", array("f"=>UserInnerMailBox::FOLDER_IMPORTANT)) ?>">
                <i class="fas fa-bookmark"></i>&nbsp;&nbsp;<?php echo Yii::t("post", "folder_important") ?>
            </a>
            </li>
            <li class="nav-item trash">
                <a class="nav-link<?php echo $folder==UserInnerMailBox::FOLDER_TRASH ? ' active' : null ?>" href="<?php echo $this->createUrl("post/index", array("f"=>UserInnerMailBox::FOLDER_TRASH)) ?>">
                    <i class="fas fa-trash"></i>&nbsp;&nbsp;<?php echo Yii::t("post", "folder_trash") ?>
                </a>
            </li>
            <li class="nav-item spam">
            <a class="nav-link" href="<?php echo $this->createUrl("post/blocked-users") ?>">
                <i class="fas fa-ban"></i>&nbsp;&nbsp;<?php echo Yii::t("post", "Blocked users") ?>
            </a>
            </li>
        </ul>
    </div>
    <div class="col-md-9">
          <table class="table table-hover">
             <thead class="thead-light">
                <tr>
                    <th>
                        <span><input type="checkbox" id="checkAll">
                    </th>
                   <th colspan="3">
                       <div class="dropdown inbox-action">
                           <button class="btn btn-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                               <?php echo Yii::t("post", "Action") ?>
                           </button>
                           <div class="dropdown-menu">
                               <?php echo $dropDownItems ?>
                           </div>
                       </div>
                   </th>
                    <th colspan="3">
                        <a href="<?php echo $this->createUrl("post/index") ?>" class="btn btn-default pull-right">
                            <i class="fa fa-refresh"></i>&nbsp;<?php echo Yii::t("post", "Refresh") ?>
                        </a>
                    </th>
                </tr>
             </thead>
             <tbody>
                <form method="POST" id="header_form">
                <?php foreach($headers as $header): ?>
                    <?php
                        $new=$box->isNew($header['status']);
                        $username=CHtml::encode(Helper::mb_ucfirst($senders[$header['companion_id']]->username));
                        $subject=CHtml::encode(Helper::mb_ucfirst($header['subject']));
                        $url=$this->createUrl("post/chain", array("id"=>$header['id']));
                    ?>
                    <tr<?php echo $box->isNew($header['status']) ? ' class="active"' : null ?>>
                        <td width="15px">
                            <input type="checkbox" class="headers" name="header[]" value="<?php echo $header['id'] ?>">
                        </td>
                        <td width="15px"><i class="fa<?php echo !$box->isInStarredFolder($header['id'], $state) ? "r" : "s" ?> fa-star"></i></td>
                        <td width="15px"><i class="fa<?php echo !$box->isInImportantFolder($header['id'], $state) ? "r" : "s" ?> fa-bookmark"></i></td>
                        <td>
                            <a class="mail-link" href="<?php echo $url; ?>">
                            <?php if($new): ?>
                                    <strong><?php echo $username ?></strong>
                            <?php else: ?>
                                    <?php echo $username ?>
                            <?php endif; ?>
                            </a>
                        </td>
                        <td>
                            <a class="mail-link" href="<?php echo $url; ?>">
                            <?php if($new): ?>
                                <span class="badge badge-success"><?php echo Yii::t("post", "New") ?></span>
                                <strong><?php echo $subject ?></strong>
                            <?php else: ?>
                                <?php echo $subject ?>
                            <?php endif; ?>
                            </a>
                        </td>
                        <td class="text-right">
                            <a class="mail-link" href="<?php echo $url; ?>">
                                <?php echo Helper::time_elapsed_string($header['appeared_date']) ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </form>
             </tbody>

             <thead>
                <tr>
                   <th colspan="6" class="text-center">
                        <a href="<?php echo $this->createUrl("post/index", array("f"=>$folder, "page"=>$pgNr-1)) ?>" class="btn btn-sm btn-primary<?php echo ($pgNr<=1) ? " disabled" : null?>">
                            <i class="fa fa-angle-left"></i>
                        </a>
                        <a href="<?php echo $this->createUrl("post/index", array("f"=>$folder, "page"=>$pgNr+1)) ?>" class="btn btn-sm btn-primary<?php echo ($pgNr>=$pgCnt) ? " disabled" : null?>">
                            <i class="fa fa-angle-right"></i>
                        </a>
                       <span class="pull-right"><?php echo $summaryText ?></span>
                   </th>
                </tr>
             </thead>
          </table>
    </div>
</div>