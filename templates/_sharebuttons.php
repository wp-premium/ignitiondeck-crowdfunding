<form name="sharebuttons" action="" method="post">
    <table>
        <tr>
            <td><?php echo $GLOBALS['tr_Facebook']?></td>
            <td>
                <select name="facebook">
                    <option value="yes"  <?php if ($facebook=='yes'){?> selected="selected" <?php }?>><?php echo $GLOBALS['tr_Yes']?></option>
                    <option value="no"   <?php if ($facebook=='no'){?> selected="selected" <?php }?>><?php echo $GLOBALS['tr_No']?></option>
                </select>
            </td>
        </tr>
        <tr>
            <td><?php echo $GLOBALS['tr_Twitter']?></td>
            <td>
                <select name="twitter">
                    <option value="yes"  <?php if ($twitter=='yes'){?> selected="selected" <?php }?>><?php echo $GLOBALS['tr_Yes']?></option>
                    <option value="no"   <?php if ($twitter=='no'){?> selected="selected" <?php }?>><?php echo $GLOBALS['tr_No']?></option>
                </select>
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>
                <?php if($facebook==""): ?>
                    <input type="submit" name="submit" value="<?php echo $GLOBALS['tr_Save']; ?>"/>
                <?php else: ?>
                    <input type="submit" name="submit" value="<?php echo $GLOBALS['tr_Update']; ?>"/>
                <?php endif;?>
            </td>
        </tr>
    </table>
</form>