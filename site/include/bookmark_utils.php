<?php
    $base_url = ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on' ? 'https' : 'http' ) . '://' .  $_SERVER['HTTP_HOST'];
    $url = $base_url . $_SERVER["REQUEST_URI"];
?>

<?php if($user->ID>0){?>
    <div id="add-bookmark" class="jbd-container" style="display:none">
        <div class="jmodal-sm">
            <div class="jmodal-header">
                <p class="jmodal-header-title"><?php echo JText::_('LNG_ADD_BOOKMARK') ?></p>
                <a href="#close-modal" rel="modal:close" class="close-btn"><i class="la la-close "></i></a>
            </div>

            <div class="jmodal-body">
                <div class="row">
                    <div class="col-12">
                        <div class="jinput-outline jinput-hover">
                            <textarea rows="5" name="note" id="note" cols="50" class="form-control validate[required]"></textarea>
                            <label><?php echo JText::_('LNG_NOTE')?>:</label>
                        </div>
                    </div>
                </div>

                <?php echo JHTML::_( 'form.token' ); ?>
                <input type='hidden' name='user_id' value='<?php echo $user->ID?>'/>
                <input type='hidden' name='item_type' id="item_type" value='<?php echo BOOKMARK_TYPE_BUSINESS ?>'/>
                <input type="hidden" name="item_id" id="item_id" value="" />
            </div>

            <div class="jmodal-footer">
                <div class="btn-group" role="group" aria-label="">
                    <button type="button" class="jmodal-btn jmodal-btn-outline" onclick="jQuery.jbdModal.close()"><?php echo JText::_("LNG_CANCEL")?></button>
                    <button type="buttom" class="jmodal-btn" onclick="jbdUtils.addBookmark()"><?php echo JText::_("LNG_ADD")?></button>
                </div>
            </div>
        </div>
    </div>
<?php } ?>


<?php if($user->ID>0){?>
    <div id="update-bookmark" class="jbd-container" style="display:none">
        <div class="jmodal-sm">
            <div class="jmodal-header">
                <p class="jmodal-header-title"><?php echo JText::_('LNG_UPDATE_BOOKMARK') ?></p>
                <a href="#close-modal" rel="modal:close" class="close-btn"><i class="la la-close "></i></a>
            </div>

            <div class="jmodal-body">
                <div class="row">
                    <div class="col-12">
                        <a href="javascript:jbdUtils.removeBookmark()" class="red"> <?php echo JText::_("LNG_REMOVE_BOOKMARK")?></a>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="jinput-outline jinput-hover">
                            <textarea rows="5" name="note" id="note" cols="50" class="form-control validate[required]"></textarea>
                            <label><?php echo JText::_('LNG_NOTE')?>:</label>
                        </div>
                    </div>
                </div>

                <?php echo JHTML::_( 'form.token' ); ?>
                <input type='hidden' name='option' id="option" value='com_jbusinessdirectory'/>
                <input type='hidden' id="task" name='task' value='companies.updateBookmark'/>
                <input type='hidden' name='user_id' id="user_id" value='<?php echo $user->ID?>'/>
                <input type='hidden' name='item_type' id="item_type" value='<?php echo BOOKMARK_TYPE_BUSINESS ?>'/>
                <input type="hidden" name="item_id" id="item_id" value="" />
            </div>

            <div class="jmodal-footer">
                <div class="btn-group" role="group" aria-label="">
                    <button type="button" class="jmodal-btn jmodal-btn-outline" onclick="jQuery.jbdModal.close()"><?php echo JText::_("LNG_CANCEL")?></button>
                    <button type="button" class="jmodal-btn" onclick="jbdUtils.updateBookmark()"><?php echo JText::_("LNG_UPDATE")?></button>
                </div>
            </div>
        </div>
    </div>
<?php } ?>

<div id="login-notice" class="jbd-container" style="display:none">
    <div class="jmodal-sm">
        <div class="jmodal-header">
            <p class="jmodal-header-title"><?php echo JText::_('LNG_INFO') ?></p>
            <a href="#close-modal" rel="modal:close" class="close-btn"><i class="la la-close "></i></a>
        </div>
        <div class="jmodal-body">
            <p>
                <?php echo JText::_('LNG_YOU_HAVE_TO_BE_LOGGED_IN') ?>
            </p>
            <p>
                <a href="<?php echo JBusinessUtil::getLoginUrl($url); ?>"><?php echo JText::_('LNG_CLICK_LOGIN') ?></a>
            </p>
        </div>
    </div>
</div>
