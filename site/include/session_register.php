<div id="register-session-<?php echo $item->id ?>" class="jbd-container" style="display:none">
    <div class="jmodal-sm">
        <div class="jmodal-header">
            <p class="jmodal-header-title"><?php echo JText::_('LNG_ADD_SESSION_USER') ?></p>
            <a href="#close-modal" rel="modal:close" class="close-btn"><i class="la la-close "></i></a>
        </div>

        <div class="jmodal-body">
            <div class="row">
                <div class="col-12">
                    <div class="jinput-outline jinput-hover">
                        <h5><?php echo JText::_("LNG_CONFIRM_SESSION_REGISTER")?></h5>
                    </div>
                </div>
            </div>											
        </div>

        <div class="jmodal-footer">
            <div class="btn-group" role="group" aria-label="">
                <button type="button" class="jmodal-btn jmodal-btn-outline" onclick="jQuery.jbdModal.close()"><?php echo JText::_("LNG_CANCEL")?></button>
                <button type="button" class="jmodal-btn session-register-btn" onclick="jbdUtils.registerSessionUser(<?php echo $item->id ?>,<?php echo $user->ID ?>);" ><?php echo JText::_("LNG_CONFIRM")?></button>
            </div>
        </div>
    </div>
</div>


<div id="unregister-session-<?php echo $item->id ?>" class="jbd-container" style="display:none">
    <div class="jmodal-sm">
        <div class="jmodal-header">
            <p class="jmodal-header-title"><?php echo JText::_('LNG_REMOVE_SESSION_USER') ?></p>
            <a href="#close-modal" rel="modal:close" class="close-btn"><i class="la la-close "></i></a>
        </div>

        <div class="jmodal-body">
            <div class="row">
                <div class="col-12">
                    <div class="jinput-outline jinput-hover">
                        <h5><?php echo JText::_("LNG_CONFIRM_SESSION_UNREGISTER")?></h5>
                    </div>
                </div>
            </div>											
        </div>

        <div class="jmodal-footer">
            <div class="btn-group" role="group" aria-label="">
                <button type="button" class="jmodal-btn jmodal-btn-outline" onclick="jQuery.jbdModal.close()"><?php echo JText::_("LNG_CANCEL")?></button>
                <button type="button" class="jmodal-btn session-register-btn" onclick="jbdUtils.unregisterSessionUser(<?php echo $item->id ?>,<?php echo $user->ID ?>);" ><?php echo JText::_("LNG_CONFIRM")?></button>
            </div>
        </div>
    </div>
</div>


<div id="registered-users-dialog-<?php echo $item->id ?>" class="jbd-container" style="display:none;">
    <div class="jmodal-sm">
        <div class="jmodal-header">
            <p class="jmodal-header-title"><?php echo JText::_('LNG_STATISTICS') ?></p>
            <a href="#close-modal" rel="modal:close" class="close-btn"><i class="la la-close "></i></a>
        </div>

        <div class="jmodal-body">
            <div class="row">
                <div class="col-12">
                    <h4><?php echo JText::_("LNG_REGISTERED_USERS"); ?></h4>
                    <div class="jinput-outline jinput-hover">
                    <table style="width:100%">
                        <?php 
                        if(!empty($item->registrations)) {
                            echo '<tr>                                   
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th class="text-center">Action</th>
                                </tr>';                            
                            foreach($item->registrations as $registeredUser) {
                                $userId = $registeredUser[0];
                                $userInfo = JBusinessUtil::getUser($userId);
                                echo '<tr class="registered-user-'.$item->id.$userId.'">';                                                           
                                echo '<td>'.$userInfo->name.'</td>';                            
                                echo '<td>'. $userInfo->email.'</td>';
                                echo '<td class="text-center"><a class="pr-2" href="javascript:jbdUtils.removeUserRegistration('. $item->id.','. $userId .')" title="'.JText::_("LNG_REMOVE_USER_REGISTRATION").'"><i class="la la-trash text-danger"></i></a></td>';
                                echo '</tr>';                                
                            }
                        } else {
                            echo JText::_("LNG_NO_REGISTERED_USERS");
                        }
                        ?>
                </table>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                <h4><?php echo JText::_("LNG_JOIN_LIST"); ?></h4>
                    <div class="jinput-outline jinput-hover">
                    <table style="width:100%">
                        <?php 
                        if(!empty($item->joins)) {
                            echo '<tr>                                   
                                    <th>Name</th>
                                    <th>Email</th>
                                </tr>';                            
                            foreach($item->joins as $joinedUser) {
                                $userId = $joinedUser[0];
                                $userInfo = JBusinessUtil::getUser($userId);
                                echo '<tr class="joined-user-'.$item->id.$userId.'">';                                                           
                                echo '<td>'.$userInfo->name.'</td>';                            
                                echo '<td>'. $userInfo->email.'</td>';
                                echo '</tr>';                                
                            }
                        } else {
                            echo JText::_("LNG_NO_JOINED_USERS");
                        }
                        ?>
                </table>
                    </div>
                </div>
            </div>													
        </div>

        <div class="jmodal-footer">
            <div class="btn-group" role="group" aria-label="">
                <button type="button" class="jmodal-btn jmodal-btn-outline" onclick="jQuery.jbdModal.close()"><?php echo JText::_("LNG_CLOSE")?></button>
            </div>
        </div>
    </div>
</div>


<div id="session-update-dialog-<?php echo $item->id ?>" class="jbd-container" style="display:none;">
    <div class="jmodal-sm">
        <div class="jmodal-header">
            <p class="jmodal-header-title"><?php echo JText::_('LNG_ADD_SESSION_USER') ?></p>
            <a href="#close-modal" rel="modal:close" class="close-btn"><i class="la la-close "></i></a>
        </div>

        <div class="jmodal-body">
            <div class="row">
                <div class="col-12">
                    <div class="jinput-outline jinput-hover">
                     <p><?php echo JText::_("LNG_SESSION_TIME_OVERLAP")?></p>
                    </div>
                </div>
            </div>											
        </div>

        <div class="jmodal-footer">
            <div class="btn-group" role="group" aria-label="">
                <button type="button" class="jmodal-btn jmodal-btn-outline" onclick="jQuery.jbdModal.close()"><?php echo JText::_("LNG_CLOSE")?></button>
                <button type="button" class="jmodal-btn session-update-btn" onclick="jbdUtils.updateUserSession(<?php echo $item->id ?>, <?php echo !empty($overlap)?$overlap[0]:0 ?>, <?php echo $user->ID ?>);" ><?php echo JText::_("LNG_CONFIRM")?></button>
            </div>
        </div>
    </div>
</div>